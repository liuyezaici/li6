<?php

namespace fast;
use think\Db;

/**
 * 和后台的log插件功能一样 只是方便给其他外部站点使用而复制过来的
 */
class Log
{
    public static function addlog($text='') {
        if(is_array($text)) $text = json_encode($text, true);
        $data = [
            'ctime' => time(),
            'text' => $text
        ];
        Db::name('log')->insert($data);
    }
}
