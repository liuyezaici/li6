<?php
namespace app\admin\addon\songs\model;

use fast\File;
use fast\Str;
use think\Model;
use think\Cookie;

class SongsUser extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = '';
    protected $updateTime = '';
   //写入作者
    public static function addUserGetUri($wyuid, $nickname='', $avatar='') {
        if($uri = self::where([
            'wyuid' => $wyuid,
        ])->value('uri')) {
            return $uri;
        } else {
            $uri = \fast\Str::getRadomTime(20);
            self::insertGetId([
                'title' => $nickname,
                'uri' => $uri,
                'avatar' => $avatar,
                'wyuid' => $wyuid,
                'createtime' => time(),
            ]);
            return $uri;
        }
    }

    //修改人气+1
    public static function updateRq($code='') {
        $cacheName = 'readSongUser:'.$code;
        $lastTime = Cookie::get($cacheName);
        if(!$lastTime || $lastTime < time()-60) {
            Cookie::set($cacheName, time(), 60);
            self::where([
                'uri' => $code,
            ])->setInc('rq');
        }
    }

}
