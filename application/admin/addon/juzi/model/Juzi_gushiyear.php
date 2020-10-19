<?php
namespace app\admin\addon\juzi\model;

use think\Model;

class Juzi_gushiyear extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'ctime';
    protected $updateTime = '';
    protected $name = 'gushiyear';

}
