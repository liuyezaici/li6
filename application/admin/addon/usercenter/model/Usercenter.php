<?php

namespace app\admin\addon\usercenter\model;

use fast\Addon;
use think\Db;
use think\Model;

/**
 * 会员模型
 */
class Usercenter Extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
    ];

    //检测用户是否需要绑定手机
    public function checkUserNeedBangding($user_id=0){
        $config = Addon::getAddonConfig('usercenter');
        //检查是否需要绑定手机
        if(!$config) return false;

        $bangding = $config['bangding'];
        if($bangding ==0) return false; //不需要绑定
        //检查用户是否绑定手机
        $mobile = db('users')->where(['id'=>$user_id])->value('mobile');
        if(!$mobile){
            return ['mobile'=>$mobile];
        }
        return ['mobile'=>$mobile];

    }
}
