<?php

namespace app\admin\addon\makeapi\model;

use think\Model;

class Makeapi extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';



}
