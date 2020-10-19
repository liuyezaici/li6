<?php
/**
 * 
 * 账户类
 * 

 * @version 1.0.0
 * 
 */
namespace Func;

class Account
{	
	public function __construct(){}
    //充值后多久可以提现
    public static $chongzhi_can_tixian_hours = 48;
    //提现后多久可以到账
    public static $tixian_can_getmoney_hours = 48;
    //资金变动类型
    protected static $allMoneyChangeTypes = array(
        '1' => '充值',
        '2' => '提现',
        '-2' => '拒绝提现',
        '3' => '加押金',
        '5' => '获得悬赏',
        '6' => '手续费',
        '8' => '提现成功',
        '10' => '回收押金',
        '16' => '付费下载资源',
    );
    public static $moneyChangeTypeChongzhi = 1; //充值
    public static $moneyChangeTypeTixian = 2; //申请提现
    public static $moneyChangeTypeTixianRefuse = -2; //拒绝提现
    public static $moneyChangeTypeJiaYajin = 3; //加押金
    public static $moneyChangeTypeGetXuanshang = 5; //获得悬赏
    public static $moneyChangeTypeFee = 6; //手续费
    public static $moneyChangeTypeTixianSuccess = 8; //提现成功
    public static $moneyChangeTypeBlance = 10; //回收押金
    public static $moneyChangeTypePayForShare = 16; //付费下载资源
    //付款账户类型
    protected  $allAccountTypes = array(
        '1' => '支付宝',
        '2' => '财付通',
        '3' => '银行卡',
        '4' => '微信帐号',
    );
    //完成任务的手续费
    public static $finishCharge = 0.1;
    //获取所有账户类型
    public function getAllAccountType() {
        return $this->allAccountTypes;
    }
    //获取所有交易类型
    public static function getAllMoneyChangeType() {
        return self::$allMoneyChangeTypes;
    }
    //获取账户名字
    public function getAccountType($typeId) {
        return $this->allAccountTypes[$typeId];
    }
    //获取交易类型
    public static function getMoneyType($typeId=0) {
        return isset(self::$allMoneyChangeTypes[$typeId]) ? self::$allMoneyChangeTypes[$typeId] : $typeId;
    }
    //判断交易类型是否支持
    public static function checkMoneyType($typeId) {
        return isset(self::$allMoneyChangeTypes[$typeId]);
    }
    //获取资金安全码
    public static function getAccountSafeCode($uid=0, $ldzj=0, $djzj=0) {
        return Str::getMD5(bcadd($ldzj, 0, 2) .'|'. $uid .'|'. bcadd($djzj, 0, 2) .'|sasa|lr|good_job');
    }
    //获取任务的押金安全码
    public static function makeTaskMoneyHash($qid=0, $money=0) {
        return Str::getMD5(bcadd($money, 0, 2) .'|'. $qid .'|'.Str::getMD5($qid.'|....|') .'|sasa|lr|good_job');
    }
    //获取用户余额
    public static function getUserMoney($uid=0) {
        if(!$uid) return ['ldzj'=>0, 'djzj'=>0];
        if(!is_numeric($uid)) {
            $uInfo = DbBase::getRowBy('c_user', 'u_id', "u_nick='{$uid}'");
            if(!$uInfo) return ['ldzj'=>0, 'djzj'=>0];
            $uid = $uInfo['u_id'];
        } else {
            $uInfo = DbBase::getRowBy('c_user', 'u_id', "u_id={$uid}");
            if(!$uInfo) return ['ldzj'=>0, 'djzj'=>0];
        }
        $uExtraInfo = DbBase::getRowBy('c_user_extra', 'c_ldzj,c_djzj,c_safe_code', "c_uid={$uid}");
        if(!$uExtraInfo) {
            $newData = array(
                'c_uid' => $uid,
                'c_createtime' => Timer::now(),
                'c_ldzj' => 0,
                'c_djzj' => 0,
                'c_safe_code' => self::getAccountSafeCode($uid, 0, 0),
            );
            DbBase::insertRows('c_user_extra', $newData);
            return ['ldzj'=>0, 'djzj'=>0];
        } else {
            return ['ldzj'=>$uExtraInfo['c_ldzj'], 'djzj'=>$uExtraInfo['c_djzj']];
        }
    }
    //校验hash算法是否合法
    public static function checkMoneyHash($uid=0) {
        $uExtraInfo = DbBase::getRowBy('c_user_extra', 'c_ldzj,c_djzj,c_safe_code', "c_uid={$uid}");
        if($uExtraInfo) {
            if($uExtraInfo['c_safe_code'] && $uExtraInfo['c_safe_code'] != self::getAccountSafeCode($uid, $uExtraInfo['c_ldzj'], $uExtraInfo['c_djzj'])) {
                return false;
            }
        }
        return true;
    }
    //账户资金异动 
    public static function changeMoney($fromUid,$fromMoneyType='ldzj', $toUid, $toMoneyType='djzj', $money=0, $changeType=0, $operator=0, $hisPayNumber='', $time_ = null, $ch_finish=1, $memo='', $ch_question_id=0, $ch_applyid=0) {
        $time_ = is_null($time_) ? Timer::now() :$time_;
        if(bccomp($money, 0, 2) ==0) throw new Exception('金额不能为0',-1);
        if(!self::checkMoneyType($changeType)) throw new Exception('交易类型不支持',-1);
        if(!DbBase::getRowBy("c_user", 'u_type', "u_id=". $fromUid ."")) throw new Exception('来源用户不存在',-1);
        if(!DbBase::getRowBy("c_user", 'u_type', "u_id=". $toUid ."")) throw new Exception('目标用户不存在',-1);
        if(!in_array($fromMoneyType, ['ldzj', 'djzj'])) throw new Exception('来源资金不支持',-1);
        if(!in_array($toMoneyType, ['ldzj', 'djzj'])) throw new Exception('目标资金不支持',-1);
        self::getUserMoney($fromUid);//用户没有资金 则写入
        self::getUserMoney($toUid);//用户没有资金 则写入
        if(!self::checkMoneyHash($fromUid))  throw new Exception('来源用户资异常！',-1);
        if(!self::checkMoneyHash($toUid))  throw new Exception('目标用户资异常！',-1);
        $fromUinfo = DbBase::getRowByLock('c_user_extra', 'c_ldzj,c_djzj', 'c_uid='.$fromUid);
        $toUinfo = DbBase::getRowByLock('c_user_extra', 'c_ldzj,c_djzj', 'c_uid='.$toUid);
        $changeLogata = array(
            'ch_type' => $changeType,
            'ch_from' => $fromUid,
            'ch_fromflag' => $fromMoneyType,
            'ch_to' => $toUid,
            'ch_toflag' => $toMoneyType,
            'ch_money' => $money,
            'ch_addtime' => $time_,
            'ch_payorder' => $hisPayNumber,
            'ch_adduid' => $operator,
            'ch_finish' => $ch_finish,
            'ch_question_id' => $ch_question_id,
            'ch_applyid' => $ch_applyid,
            'ch_memo' => $memo
        );
        if( DbBase::insertRows('c_user_money_log', $changeLogata) != 1){
            throw new Exception('写入日志失败',-1);
        }
        //来源用户
        if($fromUid == $toUid && $fromMoneyType== $toMoneyType) throw new Exception('自身金额变动时 交易类型不能一样',-1);
        if($fromUid == $toUid) {//同一个用户转账
            if($fromMoneyType == 'ldzj') {
                $fromUinfo['c_ldzj'] = bcsub($fromUinfo['c_ldzj'], $money, 2);
                $fromUinfo['c_djzj'] = bcadd($fromUinfo['c_djzj'], $money, 2);
            } else {
                $fromUinfo['c_djzj'] = bcsub($fromUinfo['c_djzj'], $money, 2);
                $fromUinfo['c_ldzj'] = bcadd($fromUinfo['c_ldzj'], $money, 2);
            }
            $fromUinfo['c_safe_code'] = self::getAccountSafeCode($fromUid, $fromUinfo['c_ldzj'], $fromUinfo['c_djzj']);
            if( DbBase::updateByData('c_user_extra', $fromUid, $fromUinfo, 'c_uid') != 1) {
                throw new Exception('用户'. $fromUid .'资金修改失败',-1);
            }
        } else {//不同用户转账
            if($fromMoneyType == 'ldzj') {
                $fromUinfo['c_ldzj'] = bcsub($fromUinfo['c_ldzj'], $money, 2);
            } else {
                $fromUinfo['c_djzj'] = bcsub($fromUinfo['c_djzj'], $money, 2);
            }
            $fromUinfo['c_safe_code'] = self::getAccountSafeCode($fromUid, $fromUinfo['c_ldzj'], $fromUinfo['c_djzj']);
            if($toMoneyType == 'ldzj') {
                $toUinfo['c_ldzj'] = bcadd($toUinfo['c_ldzj'], $money, 2);
            } else {
                $toUinfo['c_djzj'] = bcadd($toUinfo['c_djzj'], $money, 2);
            }
            $toUinfo['c_safe_code'] = self::getAccountSafeCode($toUid, $toUinfo['c_ldzj'], $toUinfo['c_djzj']);
            if( DbBase::updateByData('c_user_extra', $fromUid, $fromUinfo, 'c_uid') != 1) {
                throw new Exception('来源用户'. $fromUid .'资金修改失败',-1);
            }
            if( DbBase::updateByData('c_user_extra', $toUid, $toUinfo, 'c_uid') != 1)  {
                throw new Exception('目标用户'. $toUinfo .'资金修改失败',-1);
            }
        }
        return true;
    }
    //检测我的手机是否正确
    public static function checkMyPhone($phone='1300000000', $uid=0) {
        if (DbBase::ifExist('c_user',"u_tel = '". $phone ."' AND u_id=". $uid, 'u_id')){
            return true; //手机正确
        }
        return false;
    }
    //检测手机是否注册
    public static function checkPhoneReg($phone='1300000000') {
        if (DbBase::ifExist('c_user',"u_tel = '". $phone ."'", 'u_id')){
            return true; //手机已经被使用
        }
        return false;
    }

    //生成充值流水号
    public static function createChongzhiLogNumber() {
        $logNumber = date('YmdHis',time()). Str::getRam(12);//20150903092728 11701321
        if(DbBase::ifExist("c_user_chongzhi", "l_number='". $logNumber ."'") ) {
            $logNumber = self::createChongzhiLogNumber();
        }
        return $logNumber;
    }

    //充值到账
    public static function chongzhiLogSuccess($payType='alipay', $our_logid, $trade_no){
        $mytime = Timer::now();
        $tradeInfo = DbBase::getRowByLock('c_user_chongzhi', 'l_uid,l_pay_money,l_get_money,l_pay_fee,l_status', "l_id={$our_logid}");
        if(!$tradeInfo) {
            echo('交易流水号不存在');
            exit;
        }
        $l_uid = $tradeInfo['l_uid'];
        $l_pay_money = $tradeInfo['l_pay_money'];
        $l_get_money = $tradeInfo['l_get_money'];
        $l_pay_fee = $tradeInfo['l_pay_fee'];
        $oldStatus = $tradeInfo['l_status'];
        $successNewLogStatus = 1;
        //检查是否更新过，如果是，放弃更新
        if ($oldStatus == $successNewLogStatus) {
            return '已经更新过了';
        }
        //校验到账金额
        if(bccomp($l_pay_fee, bcmul($l_pay_money, $GLOBALS['cfg_chongzhi_fee'], 2)) !=0) {
            return '手续费不正确！';
        }
        $db->BeginTRAN();
        try {
            //转账
            if(!self::changeMoney($GLOBALS['cfg_admin_uid'], 'ldzj', $l_uid, 'ldzj', $l_get_money,
                self::$moneyChangeTypeChongzhi, $l_uid, $trade_no, $mytime, 1, "成功转账￥{$l_pay_money}，扣除手续费￥{$l_pay_fee}，实际到账￥{$l_get_money}")) {
                throw new Exception('充值转账失败',-1);
            }
            //更新日志记录
            $editLogData = array(
                'l_status' => $successNewLogStatus,
                'l_payment_number' => $trade_no,
                'l_comfirm_paytime' => $mytime
            );
            if(!DbBase::updateByData("c_user_chongzhi", $our_logid, $editLogData, "l_id")) {
                throw new Exception('修改充值状态失败',-1);
            }
            $db->CommitTRAN();
        }catch (Exception $e){
            $db->RollBackTRAN();
            $err = $e->getMessage();
            //写入报错日志
            $errData = array(
                'l_paylogid' => $our_logid,
                'l_addtime' => $mytime,
                'l_errtext' => $err,
            );
            DbBase::insertRows('pf_pay_err', $errData);
            return $err;
        }
        return 'success';
    }
}