<?php
/**
 *  积分模块
 *  功能：积分余额 充值 收入提现
 *  作者：LR  2018.5.11
 */
namespace app\admin\addon\score\model;

use fast\Random;
use think\Model;
use think\Db;
use fast\Addon;

class score extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    protected $addonName = 'score'; //插件名字
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    //定义积分交易类型
    public $tradeTypeShare = 100;  //分享获得积分
    public $tradeTypeSign = 120;  //签到得积分
    public $tradeTypePay = 140; //支付消费积分
    public $tradeTypeChangeGoods= 150; //积分兑换商品
    public $tradeTypeChangeCoupon= 160; //积分兑换优惠券
    public $tradeTypeByhand= 200; //后台手动充值
    public $tradeTypeRubbish= 170; //投递垃圾得积分

    //获取积分类型名称
    public function getAllScoreTypeName(){
        return [
            $this->tradeTypeShare => '分享获得积分',
            $this->tradeTypeSign => '签到得积分',
            $this->tradeTypePay => '支付消费积分',
            $this->tradeTypeChangeGoods => '积分兑换商品',
            $this->tradeTypeChangeCoupon => '积分兑换优惠券',
            $this->tradeTypeByhand => '后台手动充值',
            $this->tradeTypeRubbish => '投递垃圾得积分',

        ];
    }

    //获取积分操作类型名字
    public function getScoreTypeName($typeName){
        $AllTypescore = $this->getAllScoreTypeName();
        $TypeName = isset($AllTypescore[$typeName]) ? $AllTypescore[$typeName] : '';
        return $TypeName;
    }

    /**
     *  积分加密算法  一但写入用户积分 将不能再改变算法，否则现有用户的积分会变异常
     * @param int $uid
     * @param int $score
     * @param int $gift
     * @return string
     */
    protected function hashScore($uid=0, $score=0) {
        $score = bcadd($score, 0, 0);
        $hash1 = MD5($uid. '[l]'. $score. '[r]');
        return MD5($hash1. '|once_safe_code_set_never_change|');
    }

    ////检查用户积分 没有则写入 外部已经有事务
    public function checkAddUserRecord($uid=0) {
        if(!$uid) return false;
        if(!$this->where('user_id', $uid)->find()) {
            $score = 0;
            $score = bcadd($score, 0, 0);
            if(!$this->insert([
                'user_id' => $uid,
                'score' => $score,
                'hash' => $this->hashScore($uid, $score)
            ])) {
                return '积分记录放入失败';
            }
            return true;
        }
        return true;
    }

    /**
     *  用户积分校验
     */
    protected function checkUserScoreHash($uid=0) {
        $status = $this->checkAddUserRecord($uid);
        if($status !== true) {
            return $status;
        }
        $result = $this->where('user_id', $uid)->field('score,hash')->find();
        return $result['hash'] === $this->hashScore($uid, $result['score']);
    }


    //获取用户积分，没有记录则写入积分记录
    public function getUserScoreInfo($uid=0) {
        //检查用户积分 没有则写入
        $status = $this->checkAddUserRecord($uid);
        if($status !== true) {
            return $status;
        }
        $result = $this->where('user_id', $uid)->field('score')->find();
        //校验积分异常
        $status = $this->checkUserScoreHash($uid);
        if($status !== true) return $status;
        return $result;
    }


    //修改用户积分
    protected function addSubUserScore($uScoreInfo=[], $uid, $lastScore=0, $tradeScore=0, $tradeKey='score', $addFlag='add') {
        $editData = [];
        if($addFlag == 'add' ) {
            $editData[$tradeKey] = bcadd($lastScore, $tradeScore, 2);
        } else {
            $editData[$tradeKey] = bcsub($lastScore, $tradeScore, 2);
        }
        $editData['hash'] = $this->hashScore($uid, $editData['score']);
        if(!$this->where('user_id', $uid)->update($editData)) {
            throw new \Exception('修改积分失败',-1);
        }
        return true;
    }
    /**
     *  积分交易
     *   交易积分类型 $tradeKey ( score/score_gift/income/deposit)，只允许同种积分进行交易，充值积分->充值积分; 赠送积分->赠送积分; 收入积分->收入积分
     *  注意：外部调取时要加事务包裹
     * @param int $fromUid
     * @param int $toUid
     * @param int $tradeScore
     * @param string $tradeKey
     * @param int $operate_type
     * @param string $memo
     * @return bool|string
     */
    public function tradeUserScore($fromUid=0, $toUid=0, $tradeScore=0, $tradeKey='score', $operate_type=0, $memo='') {
        if(bccomp($tradeScore, 0, 2) == 0) throw new \Exception('交易积分不能为0',-1);
        $fromUScoreInfo = $this->getUserScoreInfo($fromUid);
        if(is_string($fromUScoreInfo)) throw new \Exception('来源用户积分异常:'. $fromUScoreInfo,-1);
        $toUScoreInfo = $this->getUserScoreInfo($toUid);
        if(is_string($toUScoreInfo)) throw new \Exception('目标用户积分异常:'. $toUScoreInfo,-1);
        $status1 = $this->addSubUserScore($fromUScoreInfo, $fromUid, $fromUScoreInfo[$tradeKey], $tradeScore, $tradeKey,  'sub'); //-score
        if($status1 !== true) throw new \Exception('来源用户积分修改失败:'. $status1,-1);
        $status2 = $this->addSubUserScore($toUScoreInfo, $toUid, $toUScoreInfo[$tradeKey], $tradeScore, $tradeKey,  'add'); //+score
        if($status2 !== true) throw new \Exception('目标用户积分修改失败:'. $status2,-1);
        //写入积分日志
        $logData = [
            'from_uid' => $fromUid,
            'to_uid' => $toUid,
            'ctime' => time(),
            'score' => $tradeScore,
            'operate_type' => $operate_type,
            'memo' => $memo,
        ];
        $newLog = Db::name('ScoreLog')->insertGetId($logData);
        if(!$newLog) throw new \Exception('用户积分日志写入失败',-1);
        return  $newLog;
    }

    //获取管理员id
    public function getAdminId() {
        $configResult = Addon::getAddonConfig($this->addonName);
        if(!$configResult)  return '未写入config配置信息';
        $cfgInfo = $configResult['config'] ;
        if(empty($cfgInfo['admin_id'])) return '未配置 admin_id';
        return $cfgInfo['admin_id'];
    }



    //积分兑换商品
    public function xxxxxxxxxxxxxxx($uid=0, $tixianScore=0) {
        //获取管理员id
        $admin_id = $this->getAdminId();
        if(!is_numeric($admin_id)) return $admin_id;
        $this->tradeUserScore($admin_id, $uid, $tixianScore, 'income', $this->tradeTypeBackTixian);
        return true;
    }
}
