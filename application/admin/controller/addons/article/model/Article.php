<?php

namespace app\admin\controller\addons\article\model;

use fast\File;
use fast\Str;
use think\Cookie;
use think\Model;
use think\Session;

class Article extends Model
{


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
    public static function getPrevNextArticle($typeId=0, $id=0, $flag='<') {
        return self::field('id,title')->where([
            'typeId' => $typeId,
            'id' => [$flag, $id],
        ])->find();
    }


}
