<?php
namespace app\admin\addon\songs\model;

use fast\Str;
use think\Model;

class SongsGedan extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = '';
    protected $updateTime = '';
   //加入歌单
    public static function addGedan($wyid, $wyuid, $gedanTitle, $wycreateTime, $gedanAvatar, $description) {
        $uri = Str::getRadomTime(20);
        //写入歌单-用户唯一索引 方便查找喜欢这个歌单的用户
        if(!Db('songs_gedan_user')->where([
            'gedanwyid' => $wyid,
            'wyuid' => $wyuid,
        ])->count()) {
            Db('songs_gedan_user')->insert([
                'gedanwyid' => $wyid,
                'gedanuri' => $uri,
                'wyuid' => $wyuid,
                'createtime' => time(),

            ]);
        }
        if($info = self::where([
            'wyid' => $wyid,
        ])->field('uri')->find()) {
            return $info['uri'];
        } else {
            self::insert([
               'uri' => $uri,
               'wyid' => $wyid,
               'wyuid' => $wyuid,
               'createtime' => time(),
               'wycreateTime' => $wycreateTime,
               'title' => $gedanTitle,
               'avatar' => $gedanAvatar,
               'description' => $description,
            ]);
            return $uri;
        }
    }

}
