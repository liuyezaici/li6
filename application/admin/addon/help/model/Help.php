<?php
namespace app\admin\addon\help\model;

use think\Model;

class Help extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';


    protected $helpStatusPause = 0;
    protected $helpStatusNoraml = 1;

}
