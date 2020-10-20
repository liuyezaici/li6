<?php

namespace app\admin\addon\article\model;

use think\Cookie;
use think\Model;

class Article extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    //修改浏览+1
    public static function updateRq($id='') {
        $cacheName = 'readArticle:'.$id;
        $lastTime = Cookie::get($cacheName);
        if(!$lastTime || $lastTime < time()-60) {
            Cookie::set($cacheName, time(), 60);
            self::where([
                'id' => $id,
            ])->setInc('rq');
        }
    }
    //获取上一篇
    public static function getPrevNextArticle($typeId=0, $id=0, $flag='</>') {
        return self::field('id,title')->where([
            'typeId' => $typeId,
            'id' => [$flag, $id],
        ])->find();
    }


    //检测是否可以收藏
    public function checkCanCollect($sid= 0, $myUid) {
        return self::where('id', $sid)->value('uid') == $myUid;
    }

    //获取作者uid
    public static function getAuthorId($id=0) {
        return self::field('uid')->where([
            'id' => $id,
        ])->value('uid');
    }
}
