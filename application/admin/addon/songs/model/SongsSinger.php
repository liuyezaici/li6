<?php
namespace app\admin\addon\songs\model;

use think\Model;
use think\Cookie;
use fast\File;
use fast\Str;
use app\admin\addon\songs\model\Songs;

class SongsSinger extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
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

    //外部插入作者返回uri数组
    //$singerList[] = [
    //'id'=> $v2['id'],
    //'name'=> $v2['name'],
    //];
    public static function insertSingers($singerList=[]) {
        $newIdArray = [];
        foreach ($singerList as $v) {
            $newId = self::getfieldbysingerid($v['id'], 'uri');
            if($newId) {
                $newIdArray[] = $newId;
            } else {
                $uri = Str::getRadomTime(20);
                self::insert([
                    'uri' => $uri,
                    'singerid' => $v['id'],
                    'title' => $v['name'],
                ]);
                $newIdArray[] = $uri;
            }
        }
        $newIdArray = array_unique($newIdArray);
        return $newIdArray;
    }
    //外部插入作者
    //$singerList[] = [
    //'id'=> $v2['id'],
    //'name'=> $v2['name'],
    //];
    public static function insertSingerGetFullData($singerList=[]) {
        $newIdArray = [];
        foreach ($singerList as $v) {
            $newData = self::getbysingerid($v['id'], 'uri,title');
            if($newData) {
                $newIdArray[] = $newData;
            } else {
                $uri = Str::getRadomTime(20);
                self::insert([
                    'uri' => $uri,
                    'singerid' => $v['id'],
                    'title' => $v['name'],
                ]);
                $newIdArray[] = [
                    'uri' => $uri,
                    'title' => $v['name'],
                ];
            }
        }
        return $newIdArray;
    }
    //检测歌手是否重复
    //获取歌手链接
    public static function getSingerLink($singerIds='', $join=',', $songUri='') {
        $singerStrArray=  [];
        $array_ = explode(',', $singerIds);
        $array_2 = array_unique($array_);
        if($songUri) {
            if($array_ != $array_2) {
                Songs::where('uri',$songUri)->update([
                    'singer' => join(',', $array_2)
                ]);
            }
        }
        foreach ($array_ as $singerUri_) {
            $singerName = self::getfieldbyuri($singerUri_, 'title');
            $singerStrArray[] = "<a href='/juzi/singer/uri/{$singerUri_}/1' target='_blank'>{$singerName}</a>";
        }
        return join($join, $singerStrArray);
    }
    //获取网易歌手链接
    public static function getWySingerLinkByArray($wySingerArray=[], $join=',') {
        $singerStrArray=  [];
        foreach ($wySingerArray as $v) {
            $singerStrArray[] = "<a href='/juzi/singer/wyUri/{$v['id']}/1' target='_blank'>{$v['name']}</a>";
        }
        return join($join, $singerStrArray);
    }
    //获取网易歌手链接
    public static function getWySingerLinkByUri($wySingerIds='', $join=',') {
        $singerStrArray=  [];
        $array_ = explode(',', $wySingerIds);
        foreach ($array_ as $singerUri_) {
            $singerName = self::getfieldbysingerid($singerUri_, 'title');
            $singerStrArray[] = "<a href='/juzi/singer/uri/{$singerUri_}/1' target='_blank'>{$singerName}</a>";
        }
        return join($join, $singerStrArray);
    }
    //获取网易歌手链接
    public static function getWySingerByWyId($ids='', $join=',') {
        $singerStrArray=  [];
        $array_ = explode(',', $ids);
        foreach ($array_ as $singerUri_) {
            $singerInfo = self::getbysingerid($singerUri_, 'uri,title');
            $singerName = $singerInfo['title'];
            $singerStrArray[] = "<a href='/juzi/singer/uri/". $singerInfo['uri'] ."/1' target='_blank'>{$singerName}</a>";
        }
        return join($join, $singerStrArray);
    }
    //获取歌手名字
    public static function getSingerName($singerIds='', $join=',') {
        $singerStrArray=  [];
        foreach (explode(',', $singerIds) as $singerUri_) {
            $singerName = self::getfieldbyuri($singerUri_, 'title');
            $singerStrArray[] = $singerName;
        }
        return join($join, $singerStrArray);
    }

    //修改人气+1
    public static function updateRq($code='') {
        $cacheName = 'readSinger:'.$code;
        $lastTime = Cookie::get($cacheName);
        if(!$lastTime || $lastTime < time()-60) {
            Cookie::set($cacheName, time(), 60);
            self::where([
                'uri' => $code,
            ])->setInc('rq');
        }
    }
    //采集歌手专辑
    public static function caijiAlbums($singeridWY, $page=1) {
        $limit = 12;
        $offset = ($page -1) * $limit;
        $url = "https://music.163.com/artist/album?id={$singeridWY}&limit={$limit}&offset={$offset}";
        //拼接加密 params 用到的第一个参数
        $htmlInfo = File::get_https($url, 'music.163.com');
        $htmlInfo = Str::sp_('<body>','<div class="g-sd4">', $htmlInfo);
        $mainList = Str::sp_('id="m-song-module"','</ul>', $htmlInfo);
        $pageHtml = Str::sp_('<div class="u-page">','</div>', $htmlInfo);
        if(!$pageHtml) {
            $pageNum = 0;
        } else {
            $pageArray_ = explode('"zpgi">', $pageHtml);
            $pageNum = trim(explode('</a>', end($pageArray_))[0]);
        }
        $array_ = explode('</li>', $mainList);
        $albumList = [];
        foreach ($array_ as $item) {
            if(!$item) continue;
            $albumAvatar = Str::sp_('src="','"', $item);
            $albumId = Str::sp_('/album?id=','"', $item);
            $albumTitle = Str::sp_('"tit s-fc0">','</a>', $item);
            if(!$albumAvatar) continue;
            if(!$albumUri = Db('songsSingeralbum')->field('uri')->where('wyalbumidid', $albumId)->value('uri')) {
                $albumUri = Str::getRadomTime(20);
                $albumId = Db('songsSingeralbum')->insertGetId([
                    'uri' => $albumUri,
                    'title' => $albumTitle,
                    'wyavatar' => $albumAvatar,
                    'wyalbumidid' => $albumId,
                    'wysingerid' => $singeridWY
                ]);
            }
            $albumList[] = [
                'albumAvatar' => $albumAvatar,
                'albumTitle' => $albumTitle,
                'albumUri' => $albumUri,
                'albumId' => $albumId,
            ];
        }
       // SongsSingeralbumcache::insertPageCache($singeridWY, $page, $pageNum, $albumList);
        return [
            'albumList' => $albumList,
            'pagenum' => $pageNum,
        ];
    }
    //获取字幕的typeid 网易云转换的
    public static function getZimuTypeid($zm='') {
        $allData = [
            'A' => '65',
            'B' => '66',
            'C' => '67',
            'D' => '68',
            'E' => '69',
            'F' => '70',
            'G' => '71',
            'H' => '72',
            'I' => '73',
            'J' => '74',
            'K' => '75',
            'L' => '76',
            'M' => '77',
            'N' => '78',
            'O' => '79',
            'P' => '80',
            'Q' => '81',
            'R' => '82',
            'S' => '83',
            'T' => '84',
            'U' => '85',
            'V' => '86',
            'W' => '87',
            'X' => '88',
            'Y' => '89',
            'Z' => '90',
        ];
        return isset($allData[$zm]) ? $allData[$zm] : -1;
    }
}
