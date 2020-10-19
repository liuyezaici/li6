<?php
namespace app\admin\addon\juzi\model;

use think\Model;
use think\Cookie;

class Juzi_fromarticle extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'ctime';
    protected $updateTime = '';

    //外部插入来源
    public static function addArticle($title='', $author=0, $fromid=0, $content='', $uid=0) {
//        $hasId = self::where([
//            'title' => $title,
//            'fromid' => $fromid,
//        ])->value('id');
//        if($hasId) return $hasId;
        return self::insertGetId([
            'title' => $title,
            'authorid' => $author,
            'ctime' => time(),
            'cuid' => $uid,
            'fromid' => $fromid,
            'content' => $content,
            'rq' => 0,
        ]);
    }

    //修改人气+1
    public static function updateRq($id='') {
        $cacheName = 'readFromArticle:'.$id;
        $lastTime = Cookie::get($cacheName);
        if(!$lastTime || $lastTime < time()-60) {
            Cookie::set($cacheName, time(), 60);
            self::where([
                'id' => $id,
            ])->setInc('rq');
        }
    }
}
