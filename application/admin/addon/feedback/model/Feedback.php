<?php

namespace app\admin\addon\feedback\model;

use think\Model;

class Feedback extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    //问题的状态
    public static $statusDefault =0;    //未处理
    public static $statusOk =1;    //已处理

    //获取vip套餐所有状态名称
    public static function getAllStatusName(){
        $allStatus = [];
        $allStatus[self::$statusDefault] ='未处理';
        $allStatus[self::$statusOk] ='已处理';
        return $allStatus;
    }
    //获取vip套餐的状态名
    public static function getStatusName($status=0) {
        $allStatus = self::getAllStatusName();
        return isset($allStatus[$status]) ? $allStatus[$status] : $status;
    }
}
