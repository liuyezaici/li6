<?php
namespace app\admin\addon\juzi\model;

use think\Model;

class Juzi_type extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    //默认分类名字
    public static $defaultTypeName = '默认分类';
    //获取分类名字
    public static function getUserDefaultType($uid=0) {
        $hasTypeId = self::getfieldbytitle(self::$defaultTypeName, 'id');
        if($hasTypeId) return $hasTypeId;
        $hasTypeId = self::insertGetId([
            'title' => self::$defaultTypeName,
            'ctime' => time(),
            'cuid' => $uid,
            'opened' => 1,
        ]);
        return $hasTypeId;
    }

}
