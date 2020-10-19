<?php
namespace app\admin\addon\songs\model;

use think\Model;
use think\Cookie;
use fast\File;
use fast\Str;

class SongsSingeralbum extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = '';
    protected $updateTime = '';

    //修改人气+1
    public static function updateRq($code='') {
        $cacheName = 'readAlbum:'.$code;
        $lastTime = Cookie::get($cacheName);
        if(!$lastTime || $lastTime < time()-60) {
            Cookie::set($cacheName, time(), 60);
            self::where([
                'uri' => $code,
            ])->setInc('rq');
        }
    }
    //采集专辑的歌曲
    public static function caijiAlbumsSongs($wyAlbumid) {
        $url = "https://music.163.com/album?id={$wyAlbumid}";
        //拼接加密 params 用到的第一个参数
        $htmlInfo = File::get_https($url, 'music.163.com');
        $albumDesc = Str::sp_('"description": "','",', $htmlInfo);
        $htmlInfo = Str::sp_('<body>','</body>', $htmlInfo);
        $listInfo = Str::sp_('<textarea id="song-list-pre-data" style="display:none;">','</textarea>', $htmlInfo);
        if(!$listInfo) {
            return '获取不到textarea';
        }
        if(substr($listInfo, 0, 2) =='[{') {
            $array_ = json_decode($listInfo, true);
            foreach ($array_ as $item) {
                if(!$item) continue;
                $artists = $item['artists'];
                $songName = $item['name'];
                $wySongid = $item['id'];
                $singerList = [];
                foreach ($artists as $v2) {
                    $singerList[] = [
                        'id'=> $v2['id'],
                        'name'=> $v2['name'],
                    ];
                }
                $singerIdArray = SongsSinger::insertSingers($singerList);
                if($songInfo = Songs::field('uri,songid,title,singer')->where('songid', $wySongid)->find()) {
                    $songList[] = $songInfo;
                } else {
                    $songList[] = Songs::insertSongs($songName, $wySongid, $singerIdArray);
                }
            }
        } else {
            $listInfo = Str::sp_('<ul class="f-hide">','</ul>', $htmlInfo);
            $arrayLi = explode('</li>', $listInfo);
            foreach ($arrayLi as $li) {
                if(!$li) continue;
                $wySongid = Str::sp_('song?id=','"', $li);
                if(!$wySongid) {
                    print_r('没有歌曲ID');
                    print_r($arrayLi);
                    exit;
                }
                if($songInfo = Songs::field('uri,songid,title,singer')->where('songid', $wySongid)->find()) {
                    $songList[] = $songInfo;
                } else {
                    $singerIdArray = Songs::caijiSongSinger($wySongid);//[uri,uri]
                    if(!is_array($singerIdArray)) {
                        return $singerIdArray;
                    }
                    $songName = strip_tags($li);
                    $songList[] = Songs::insertSongs($songName, $wySongid, $singerIdArray);
                }
            }
        }
        $songNum =  count($songList);
//        print_r(json_encode($songList));exit;
        self::where('wyalbumidid', $wyAlbumid)->update(['songs'=>$songNum, 'songsids'=> json_encode($songList), 'desc'=>$albumDesc]);
        return [
            'songList' => $songList,
            'desc' => $albumDesc,
            'songNum' => $songNum,
        ];
    }
}
