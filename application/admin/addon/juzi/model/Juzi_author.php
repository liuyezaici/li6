<?php
namespace app\admin\addon\juzi\model;

use think\Model;

class Juzi_author extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    //默认分类名字
    public static $defaultTypeName = '原创';
    //获取分类名字
    public static function getUserDefaultType($uid=0) {
        $hasTypeId = self::getfieldbytitle(self::$defaultTypeName, 'id');
        if($hasTypeId) return $hasTypeId;
        $hasTypeId = self::insertGetId([
            'title' => self::$defaultTypeName,
            'ctime' => time(),
            'cuid' => $uid
        ]);
        return $hasTypeId;
    }

    //外部插入作者
    public static function addAuthor($title='', $uid=0) {
        $hasId = self::getfieldbytitle($title, 'id');
        if($hasId) return $hasId;
        return self::insertGetId([
            'title' => $title,
            'ctime' => time(),
            'cuid' => $uid
        ]);
    }
    //外部插入百度作者
    public static function addBaiduAuthor($title='', $uid=0) {
        $hasId = Db('juziAuthorbaidu')->where('title', $title)->value('id');
        if($hasId) return $hasId;
        return Db('juziAuthorbaidu')->insertGetId([
            'title' => $title,
            'ctime' => time(),
            'cuid' => $uid
        ]);
    }

}
