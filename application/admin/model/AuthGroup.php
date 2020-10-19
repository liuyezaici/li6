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

    //定义 admin 所有状态
    protected static $statusNormal = 1;
    protected static $statusLock = -1;
    //获取 admin 所有状态
    public static function getAdminGroupAllStatus() {
        return [
            self::$statusNormal => '正常',
            self::$statusLock => '锁定',
        ];
    }
    //获取 admin 所有状态 给前端radio用
    public function getAdminGroupAllStatusForRadio() {
        $allStatus = self::getAdminGroupAllStatus();
        $newData = [];
        foreach ($allStatus as $k =>$v) {
            $newData[] = [
                'value' => $k,
                'text' => $v,
            ];
        }
        return $newData;
    }
    //获取 admin 正常状态
    public static function getAdminGroupNormalStatus() {
        return self::$statusNormal;
    }
    //获取 admin 默认状态
    public static function getAdminGroupDefaultStatus() {
        return self::$statusNormal;
    }
    //判断状态是否正确
    public static function isWrongStatus($status='') {
        $allStatus = array_keys(self::getAdminGroupAllStatus());
        return !in_array($status, $allStatus);
    }
    //获取 admin 状态名字
    public static function getAdminGroupStatusName($status=0) {
        $allStatus = self::getAdminGroupAllStatus();
        return isset($allStatus[$status]) ? $allStatus[$status] : $status;
    }
}
