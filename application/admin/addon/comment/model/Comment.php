<?php

namespace app\admin\addon\comment\model;

use think\Model;

class Comment extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;

}
