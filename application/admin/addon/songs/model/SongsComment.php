<?php
namespace app\admin\addon\songs\model;

use think\Model;

class SongsComment extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = '';
    protected $updateTime = '';
   //写入歌曲评论
    public static function addComments($wycommentid, $content='', $songuri='', $wysongid=0, $useruri=0, $wyuid=0, $likecount=0, $writetime=0, $beReplied=[]) {
        if($uri = self::where([
            'wycommentid' => $wycommentid,
        ])->value('uri')) {
            return $uri;
        } else {
            $uri = \fast\Str::getRadomTime(20);
            self::insert([
                'content' => $content,
                'uri' => $uri,
                'songuri' => $songuri,
                'wysongid' => $wysongid,
                'useruri' => $useruri,
                'wyuid' => $wyuid,
                'wycommentid' => $wycommentid,
                'writetime' => $writetime,
                'likecount' => $likecount,
                'bereplied' => json_encode($beReplied),
            ]);
            return $uri;
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
    //获取最新的评论
    public static function getLastCommit() {
        return self::field('content,uri,songuri,wysongid,useruri,wyuid,wycommentid,writetime,likecount,bereplied')->group('wysongid')->order('id', 'desc')->limit(5)->select();
    }
    //获取模糊相似的评论
    public static function getLikeCommits($tagArray=[]) {
        if(!$tagArray) return [];
        $result = [];
        foreach ($tagArray as $tag_) {
            $result = array_merge($result, self::field('content,uri,songuri,wysongid,useruri,wyuid,wycommentid,writetime,likecount')->where('content', 'like', "%{$tag_}%")->limit(3)->select());
        }
        return $result;
    }


}
