<?php

namespace app\admin\model;

use think\Model;

class AuthGroup extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    public function getNameAttr($value, $data)
    {
        return __($value);
    }

    //定义安装商的权限组id
    //用于业务功能限制 比如绑定用户时填写的memo 只能查看自己的
    public static $installerGroupId = 3;

}
