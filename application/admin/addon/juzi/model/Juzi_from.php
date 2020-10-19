<?php
namespace app\admin\addon\juzi\model;

use think\Model;
use think\Cookie;

class Juzi_from extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    public static $fromtypeGushi = 1;//古诗典籍
    public static $fromtypeSanwen = 3;//散文
    public static $fromtypeXiaoshuo = 4;//小说
    public static $fromtypeXdShige = 5;//现代诗歌
    public static $fromtypeGeci = 6;//歌词
    public static $fromtypeMovie = 7;//电影
    public static $fromtypeTonghua = 8;//童话故事
    public static $fromtypeOther = 10;//其他
    //返回所有类型给前端
    public static function getAllFromType() {
        return [
            self::$fromtypeGushi => '古诗典籍',
            self::$fromtypeSanwen => '散文',
            self::$fromtypeXiaoshuo => '小说',
            self::$fromtypeXdShige => '现代诗歌',
            self::$fromtypeGeci => '歌词',
            self::$fromtypeMovie => '电影',
            self::$fromtypeOther => '其他',
        ];
    }
    //返回所有来源链接给前端
    public static function getFromTypeLink($typeId=0, $fromId=0, $fromTitle=0) {
        if($typeId == self::$fromtypeXiaoshuo) return "<a href='/juzi/xiaoshuo/main/{$fromId}'>《{$fromTitle}》</a>";
        if($typeId == self::$fromtypeSanwen) return "<a href='/juzi/sanwen/main/{$fromId}'>《{$fromTitle}》</a>";
        if($typeId == self::$fromtypeGeci) return "<a href='/juzi/geci/main/{$fromId}'>《{$fromTitle}》</a>";
        return "<a href='/juzi/from/id/{$fromId}'>《{$fromTitle}》</a>";
    }
    //作者类型
    public static $authorTypeGuren = 1;//古人
    public static $authorTypeJindai = 2;//学者
    public static $authorTypeYiren = 3;//艺人

    //校验类型是否合法
    public static function allowFromType($typeid=0) {
        return in_array($typeid, self::getAllFromType());
    }
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

    //外部插入来源
    public static function addFrom($title='', $author=0, $uid=0, $fromtype=0) {
        $hasId = self::getfieldbytitle($title, 'id');
        if($hasId) return $hasId;
        return self::insertGetId([
            'title' => $title,
            'authorid' => $author,
            'ctime' => time(),
            'cuid' => $uid,
            'fromtype' => $fromtype,
        ]);
    }


    //来源重复性检测
    public static function hasFrom($title='', $authorId=0) {
        return self::where([
                'authorid' => $authorId,
                'title' => $title
            ])->find();

    }
    //修改人气+1
    public static function updateRq($id='') {
        $cacheName = 'readFrom:'.$id;
        $lastTime = Cookie::get($cacheName);
        if(!$lastTime || $lastTime < time()-60) {
            Cookie::set($cacheName, time(), 60);
            self::where([
                'id' => $id,
            ])->setInc('rq');
        }
    }
}
