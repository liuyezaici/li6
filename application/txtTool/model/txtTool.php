<?php

namespace app\txtTool\model;

use think\Cookie;
use think\Db;
use think\Model;
use fast\Str;

class txtTool extends Model
{

    //定义cookies
    protected static $keyCookies = 'txtToolLoginKey';

    //是否存在key
    public static function hasKey($key='') {
        return  Db::name('txtkey')->field('id')->where('key', $key)->find();
    }
    //获取key的提问
    public static function getKeyAsk($key='') {
        return  Db::name('txtkey')->where('key', $key)->value('ask');
    }
    //修改key的答案
    public static function editKeyAskAns($key='', $ask='', $ans='') {
        return  Db::name('txtkey')->where('key', $key)->update(['ask'=> $ask, 'ans'=> $ans]);
    }
    //检测key的答案
    public static function checkKeyAns($key='', $ans='') {
        return  Db::name('txtkey')->where('key', $key)->value('ans') == $ans;
    }


    //加密key 防止直接数据库表之间可以手动关联
    protected static function keyProtectEncode($key, $aesStr, $aesIv) {

    }

    //写入aeskey
    public static function insertNewAesKey($key='') {
        $aesStr = Str::getRandChar(16);
        $aesIv = Str::getRandChar(16);
        //写入key
        Db::name('txtaeskeys')->insert([
            'keyHash' => self::keyProtectEncode($key, $aesStr, $aesIv),
            'aeskey' => $aesStr,
            'aesiv' => $aesIv,
        ]);
    }

    //检测是否存在key 无则写入
    public static function checkKey($key='') {
       if(!self::hasKey($key)) {
           Db::name('txtkey')->insert([
               'ctime' => time(),
               'key' => $key,
               'ask' => '',
               'ans' => '',
           ]);
           self::insertNewAesKey($key);
       }
    }

    //保存key的缓存
    public static function saveKeyCookies($key='') {
        Cookie::set(self::$keyCookies, $key);
    }
    //获取key的cookies
    public static function getKeyCookies() {
        Cookie::get(self::$keyCookies);
    }
    //移除key的cookies
    public static function removeKeyCookies($key='') {
        Cookie::delete(self::$keyCookies);
    }
}
