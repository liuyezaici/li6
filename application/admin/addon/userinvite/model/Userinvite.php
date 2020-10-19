<?php
namespace app\admin\addon\userinvite\model;

use app\admin\command\Addon;
use think\Model;

class Userinvite extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';


    //创建邀请码
    public function createInviteKey($uid=0) {
        $radomKey = substr(md5($uid.'_'. date_rand_no()), 0, 10);
        if($this->getbykeyname($radomKey)) { //检查是否存在
            return $this->createInviteKey($uid);
        }
        return $radomKey;
    }
    //写入邀请记录
    public function createInviteLog($key='',$uid=0, $regSuccessFunc=[], $paySuccessFunc=[]) {
        $params['main_uid'] = $uid;
        $params['reg_success_func'] = serialize($regSuccessFunc);
        $params['pay_success_func'] = serialize($paySuccessFunc);
        $params['ctime'] = time();
        $params['keyname'] = $key;
        if(!$this->insert($params)) return ['插入邀请记录失败'];
        return $key;
    }

    //邀请数量加1
    public function addInviteNum($log_id){
        $status = $this->where(['id'=>$log_id])->setInc('successnum',1); //库存减少1
        return $status ? true:false;
    }

    //根据key执行包里的数据
    //执行字段：$flag  'reg','pay'
    public function runInviteKeyFunc($key='', $newUid=0, $flag='reg') {
        if($flag == 'reg') {
            $fieldName = 'reg_success_func';
        } elseif($flag == 'pay') {
            $fieldName = 'pay_success_func';
        }
        $getFieldContent = $this->getfieldbykeyname($key, $fieldName);
        $getFieldContent = unserialize($getFieldContent);
        /*
        $regSuccessFunc = [
            [
                'source_method' => 'newUserGetOnceCard',
                'source_data' => [
                    'main_uid'=> $this->auth->id,
                ]
            ]
        ]
        */
        foreach ($getFieldContent as $k=>$data_) {
            //补充新uid
            if(!isset($data_['source_data']['new_uid'])) $data_['source_data']['new_uid'] = $newUid;
            $main_uid = $data_['source_data']['main_uid'];
            db('users')->where('id',$newUid)->update(['pid'=>$main_uid]);//修改父pid
            if(isset($data_['source_model'])) {
                $model_ = \fast\Addon::getModel($data_['source_model']);
                //补充新uid
                $model_->$data_['source_method']($data_['source_data']);
            } else {
                $this->$data_['source_method']($data_['source_data']);
            }
        }
    }

}
