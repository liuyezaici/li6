<?php
namespace app\admin\addon\userinvite\model;

use think\Model;

class Userinvite extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

}

