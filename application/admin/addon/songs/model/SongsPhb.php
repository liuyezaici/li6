<?php
namespace app\admin\addon\songs\model;

use think\Model;

class SongsPhb extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = '';
    protected $updateTime = '';
   //今天是否加载过
    public static function hasGetToday($title='', $url='') {
        if($info = self::where([
            'url' => $url
        ])->find()) {
            if($info['day']==\fast\Date::toYMD()) {
                return $info['mp3ids'];
            } else {
                self::where([
                    'id' => $info['id'],
                ])->update([
                    'title' => $title,
                    'day' => \fast\Date::toYMD(),
                ]);
                return false;
            }
        } else {
            self::insert([
                'title' => $title,
                'url' => $url,
                'day' => \fast\Date::toYMD(),
            ]);
            return false;
        }
    }

    //更新今天的歌曲
    public static function setTodaySong($url='', $songIds=[]) {
        self::where([
            'url' => $url,
        ])->update([
            'mp3ids' => json_encode($songIds),
        ]);
    }
}
