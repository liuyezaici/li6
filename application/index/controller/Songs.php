<?php

namespace app\juzi\controller;

use app\admin\addon\songs\model\SongsSinger;
use app\common\controller\Frontend;
use think\Config;
use think\Cookie;
use fast\Addon;
use fast\Date;
use fast\Str;
use think\Request;
use app\admin\addon\songs\model\Songs as songsModel;
use app\admin\addon\songs\model\SongsSinger as singerModel;
use app\admin\addon\songs\model\SongsSingeralbum as albumModel;
use app\admin\addon\songs\model\SongsPhb as songsPhbModel;
use app\admin\addon\songs\model\SongsUser;

class Songs extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected static $webTitle = '';
    protected static $webdesc = '';
    protected static $webLogo = '';
    protected static $tongjiCode = '';
    protected static $footContent = '';

    public function _initialize()
    {
        parent::_initialize();
        //实例化配置组件
        $settingModel = Addon::getModel('setting');
        if(!$settingModel) {
            self::$webTitle = '未安装setting组件';
            self::$webdesc = '未安装setting组件';
            self::$webLogo = Config::get('default_img');
            self::$tongjiCode = '';
            self::$footContent = '';
        } else {
            self::$webTitle = $settingModel->getSetting('web_title');//站点名字设置
            self::$webdesc = $settingModel->getSetting('web_desc');//站点描述
            self::$webLogo = $settingModel->getSetting('web_logo');//站点logo
            self::$tongjiCode = $settingModel->getSetting('tongji_code');//统计代码
            self::$footContent = $settingModel->getSetting('foot_content');//页脚内容
        }
        $this->view->assign('webLogo', self::$webLogo);//站点名
        $this->view->assign('front_header', $this->view->fetch('common/header'));
        $this->view->assign('webTitle', '歌曲赏析,'.self::$webTitle);//站点名
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('keywords', "句子林,歌曲赏析");//页面关键词
        $this->view->assign('description', "歌曲赏析,全网最齐全的歌曲赏析网站");//页面介绍
        $this->view->assign('tongjiCode', self::$tongjiCode);//统计代码
        $this->view->assign('footContent', self::$footContent);//页脚内容
    }


    /**
     * curl 发送 get 请求
     * @param $url
     * @param $header
     * @return mixed
     */
    private function __httpGet($url,$header)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);                // true获取响应头的信息
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);        // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);        // 使用自动跳转
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);           // 自动设置Referer
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);        // 设置等待时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);              // 设置cURL允许执行的最长秒数
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    //插入歌曲和歌手
    private function __insertSongs($songName='', $songId=0, $singerIdList=[]) {
        sort($singerIdList);
        $songInfo = songsModel::field('uri,title,singer')->where('songid', $songId)->find();
        if(!$songInfo) {
            $songInfo = songsModel::field('uri,title,singer')->where([
                'title' => $songName,
                'singer' => join(',', $singerIdList),
            ])->find();
            if(!$songInfo) {
                return songsModel::insertSongs($songName, $songId, $singerIdList);
            } else {
                return $songInfo;
            }
        } else {
            return $songInfo;
        }
    }

    //格式化歌手
    protected function __formatSinger($songIds) {
        foreach ($songIds as &$v) {
            $singerIds = $v['singer'];
            $v['singer'] = singerModel::getSingerLink($singerIds, '/');
        }
        unset($v);
        return $songIds;
    }
    //采集歌手
    protected function __caijiSinger($url='') {
        $cacheName = ROOT_PATH . 'runtime/cached/readSingerList_'.md5($url).'.txt';
        $cacheTime = ROOT_PATH . 'runtime/cached/readSingerList_'.md5($url).'_time.txt';
        if(!is_dir($cacheTime))
        {
            @mkdir(dirname($cacheTime), 0777);
        }
        $lastCache = file_exists($cacheTime) ? file_get_contents($cacheName): '';
        $lastTime = file_exists($cacheTime) ? file_get_contents($cacheTime): '';
//        print_r($lastCache);exit;
        if($lastCache && $lastTime > time()-2*86400) {
            return json_decode($lastCache, true);
        }
        // 设置请求头
        $headers = array(
            'Host:music.163.com',
            'Refere:http://music.163.com/',
            // 模拟浏览器设置 User-Agent ，否则取到的数据不完整
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36'
        );
        $htmlInfo = $this->__httpGet($url, $headers);
        //$htmlInfo = \fast\File::get_https($url, 'http://music.163.com/');
        $htmlInfo = explode('body>', $htmlInfo)[1];
        $htmlInfo = explode('</body>', $htmlInfo)[0];
        if(!strstr($htmlInfo, 'id="m-artist-box">')) {
            echo '歌手类型无法获取id';
            exit;
        }
        $htmlInfo = explode('id="m-artist-box">', $htmlInfo)[1];
        $htmlInfo = explode('</ul>', $htmlInfo)[0];
//        print_r($arrays);exit;
        $arrays = explode('</li>', $htmlInfo);
//        print_r($arrays);exit;
        $singerList = [];
        foreach ($arrays as $tmpStr){
            $singerId_ = Str::sp_('artist?id=','"',$tmpStr);
            $singerName_ = Str::sp_('title="','"',$tmpStr);
            $singerName_ = str_replace('的音乐', '', $singerName_);
            if(is_numeric($singerId_)) {
                $singerList[] = [
                    'id'=> $singerId_,
                    'name'=> $singerName_,
                ];
            }
        }
        if($singerList) {
            $singerList = singerModel::insertSingerGetFullData($singerList);
            file_put_contents($cacheName, json_encode($singerList));
            file_put_contents($cacheTime, time());
        }
        return $singerList;
    }
    //采集歌曲榜
    protected function __caijiPhb($title='', $url='') {
        if($songIds = songsPhbModel::hasGetToday($title, $url)) {
            $songIds = json_decode($songIds, true);
            return $songIds;
        }
        // 设置请求头
        $headers = array(
            'Host:music.163.com',
            'Refere:http://music.163.com/',
            // 模拟浏览器设置 User-Agent ，否则取到的数据不完整
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36'
        );
        $htmlInfo = $this->__httpGet($url, $headers);
        //$htmlInfo = \fast\File::get_https($url, 'http://music.163.com/');
        $htmlInfo = explode('body>', $htmlInfo)[1];
        $htmlInfo = explode('</body>', $htmlInfo)[0];
        $htmlInfo = explode('id="song-list-pre-data" style="display:none;">', $htmlInfo)[1];
        $htmlInfo = explode('</textarea>', $htmlInfo)[0];
        $data = json_decode($htmlInfo, true);
        $songIds = [];
        foreach ($data as $k=>$v){
            $singerList = [];
            foreach ($v['artists'] as $v2) {
                $singerList[] = [
                    'id'=> $v2['id'],
                    'name'=> $v2['name'],
                ];
            }
            $singerIdArray = singerModel::insertSingers($singerList);
            $songIds[] = $this->__insertSongs($v['name'], $v['id'], $singerIdArray);
//            if($k==1) {
//                exit;
//            }
        }
        $songIds = $this->__formatSinger($songIds);
        songsPhbModel::setTodaySong($url, $songIds);
        return $songIds;
    }
    //江小白YOLO云音乐说唱榜
    public function singerList($type) {
        $zm = input('zm', '-1', 'trim');
        $typeId = SongsSinger::getZimuTypeid($zm);
        switch ($type) {
            case 'huayunangeshou':
                $topTitle = '华语男歌手';
                $url = 'https://music.163.com/discover/artist/cat?id=1001&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            case 'huayunvgeshou':
                $topTitle = '华语女歌手';
                $url = 'https://music.163.com/discover/artist/cat?id=1002&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            case 'huayuteam':
                $topTitle = '华语乐队、组合';
                $url = 'https://music.163.com/discover/artist/cat?id=1003&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            case 'oumeinan':
                $topTitle = '欧美男歌手';
                $url = 'https://music.163.com/discover/artist/cat?id=2001&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            case 'oumeinv':
                $topTitle = '欧美女歌手';
                $url = 'https://music.163.com/discover/artist/cat?id=2002&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            case 'oumeiteam':
                $topTitle = '欧美乐队、组合';
                $url = 'https://music.163.com/discover/artist/cat?id=2003&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            case 'japannan':
                $topTitle = '日本男歌手';
                $url = 'https://music.163.com/discover/artist/cat?id=6001&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            case 'japannv':
                $topTitle = '日本女歌手';
                $url = 'https://music.163.com/discover/artist/cat?id=6002&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            case 'japanteam':
                $topTitle = '日本乐队、组合';
                $url = 'https://music.163.com/discover/artist/cat?id=6003&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            case 'hanguonan':
                $topTitle = '韩国男歌手';
                $url = 'https://music.163.com/discover/artist/cat?id=7001&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            case 'hanguonv':
                $topTitle = '韩国女歌手';
                $url = 'https://music.163.com/discover/artist/cat?id=7002&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            case 'hanguoteam':
                $topTitle = '韩国乐队、组合';
                $url = 'https://music.163.com/discover/artist/cat?id=7003&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            case 'qitanan':
                $topTitle = '其他男歌手';
                $url = 'https://music.163.com/discover/artist/cat?id=4001&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            case 'qitanv':
                $topTitle = '其他女歌手';
                $url = 'https://music.163.com/discover/artist/cat?id=4002&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            case 'qitateam':
                $topTitle = '其他乐队、组合';
                $url = 'https://music.163.com/discover/artist/cat?id=4003&initial='.$typeId;
                $singerData = $this->__caijiSinger($url);
                break;
            default:
                echo 'no support type';
                exit;
                break;
        }
        $this->view->assign('type', $type);
        $this->view->assign('zm', $zm);
        $this->view->assign('topTitle', $topTitle);
        $this->view->assign('singerData', $singerData);
        $this->view->assign('keywords', "{$topTitle}的音乐,歌曲赏析");//页面关键词
        $this->view->assign('description', "{$topTitle}的音乐以及歌曲赏析");//页面介绍
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/singerList.html'));
    }
    //江小白YOLO云音乐说唱榜
    public function jiangxiaobai() {
        $url = 'http://music.163.com/discover/toplist?id=991319590';
        $songIds = $this->__caijiPhb('江小白YOLO云音乐说唱榜', $url);
        $this->view->assign('topTitle', '江小白YOLO云音乐说唱榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'jiangxiaobai');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //法国 NRJ Vos Hits 周榜
    public function faguoNRJ() {
        $url = 'http://music.163.com/discover/toplist?id=27135204';
        $songIds = $this->__caijiPhb('法国 NRJ Vos Hits 周榜', $url);
        $this->view->assign('topTitle', '法国 NRJ Vos Hits 周榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'faguoNRJ');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //欧美新歌榜
    public function oumeiNew() {
        $url = 'http://music.163.com/discover/toplist?id=2809577409';
        $songIds = $this->__caijiPhb('欧美新歌榜', $url);
        $this->view->assign('topTitle', '欧美新歌榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'oumeiNew');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //欧美热歌榜
    public function oumeiHot() {
        $url = 'http://music.163.com/discover/toplist?id=2809513713';
        $songIds = $this->__caijiPhb('欧美热歌榜', $url);
        $this->view->assign('topTitle', '欧美热歌榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'oumeiHot');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //日本Oricon周榜
    public function ribenoricon() {
        $url = 'http://music.163.com/discover/toplist?id=60131';
        $songIds = $this->__caijiPhb('日本Oricon周榜', $url);
        $this->view->assign('topTitle', '日本Oricon周榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'ribenoricon');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //美国Billboard周榜
    public function billboard() {
        $url = 'http://music.163.com/discover/toplist?id=60198';
        $songIds = $this->__caijiPhb('美国Billboard周榜', $url);
        $this->view->assign('topTitle', '美国Billboard周榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'billboard');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //电竞音乐榜
    public function dianjing() {
        $url = 'http://music.163.com/discover/toplist?id=2006508653';
        $songIds = $this->__caijiPhb('电竞音乐榜', $url);
        $this->view->assign('topTitle', '电竞音乐榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'dianjing');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //UK排行榜周榜
    public function uk() {
        $url = 'http://music.163.com/discover/toplist?id=180106';
        $songIds = $this->__caijiPhb('UK排行榜周榜', $url);
        $this->view->assign('topTitle', 'UK排行榜周榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'uk');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //韩语榜
    public function hanyu() {
        $url = 'http://music.163.com/discover/toplist?id=745956260';
        $songIds = $this->__caijiPhb('韩语榜', $url);
        $this->view->assign('topTitle', '韩语榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'hanyu');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //ACG音乐榜
    public function acg() {
        $url = 'http://music.163.com/discover/toplist?id=71385702';
        $songIds = $this->__caijiPhb('ACG音乐榜', $url);
        $this->view->assign('topTitle', 'ACG音乐榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'acg');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //新声榜
    public function xinsheng() {
        $url = 'http://music.163.com/discover/toplist?id=2617766278';
        $songIds = $this->__caijiPhb('新声榜', $url);
        $this->view->assign('topTitle', '新声榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'xinsheng');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //云音乐电音榜
    public function dianyin() {
        $url = 'http://music.163.com/discover/toplist?id=1978921795';
        $songIds = $this->__caijiPhb('云音乐电音榜', $url);
        $this->view->assign('topTitle', '云音乐电音榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'dianyin');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //iTunes榜
    public function iTunes() {
        $url = 'http://music.163.com/discover/toplist?id=11641012';
        $songIds = $this->__caijiPhb('iTunes榜', $url);
        $this->view->assign('topTitle', 'iTunes榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'iTunes');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //抖音排行榜
    public function douyin() {
        $url = 'http://music.163.com/discover/toplist?id=2250011882';
        $songIds = $this->__caijiPhb('抖音排行榜', $url);
        $this->view->assign('topTitle', '抖音排行榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'douyin');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //古典音乐榜
    public function gudian() {
        $url = 'http://music.163.com/discover/toplist?id=71384707';
        $songIds = $this->__caijiPhb('古典音乐榜', $url);
        $this->view->assign('topTitle', '古典音乐榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'gudian');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //原创歌曲榜
    public function original() {
        $url = 'http://music.163.com/discover/toplist?id=2884035';
        $songIds = $this->__caijiPhb('原创歌曲榜', $url);
        $this->view->assign('topTitle', '原创歌曲榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'original');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //最新歌曲
    public function newSongs() {
        $url = 'http://music.163.com/discover/toplist?id=3779629';
        $songIds = $this->__caijiPhb('最新歌曲', $url);
        $this->view->assign('topTitle', '最新歌曲');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'newSongs');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //飙升榜
    public function speedHot() {
        $url = 'http://music.163.com/discover/toplist?id=19723756';
        $songIds = $this->__caijiPhb('飙升榜', $url);
        $this->view->assign('topTitle', '飙升榜');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'speedHot');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }
    //热门歌曲
    public function hotSongs()
    {
        $url = 'http://music.163.com/discover/toplist?id=3778678';
        $songIds = $this->__caijiPhb('热门歌曲', $url);
        $this->view->assign('topTitle', '热门歌曲');
        $this->view->assign('songIds', $songIds);
        $this->view->assign('typeName', 'hotSongs');
        print_r($this->view->fetch(APP_PATH .request()->module().'/view/songs/phb.php'));
    }

    // 搜索歌曲
    public function search() {
        if ($this->request->isPost()){
            print_r('forbid_post');
            exit;
        }
        $page = input('page', 1, 'int');
        $page = (int)$page;
        $keyword = input('keyword', '', 'trim');
        $type = input('t', '', 'int');
        if(!$keyword) return $this->success('获取成功', []);
        $likeSql = "%{$keyword}%";
        $pageSize = 10;
        $path = "/juzi/songs/search/";
        //本站歌曲
        if($type==1) {
            $list = SongsModel::field('id,uri,title,singer')->where('title', 'like', $likeSql)->paginate($pageSize, false,
                [
                    'page'=> $page,
                    'path'=> $path,
                    'query'=> [
                        'keyword' => $keyword,
                        't' => $type,
                    ],
                ]
            );
            foreach ($list as &$v) {
                $v['text_li'] = SingerModel::getSingerLink($v['singer'], '/') . ':'. "<a href='/juzi/songs/details/{$v['id']}' target='_blank'>{$v['title']}</a>";
            }
            $pageMenu = $list->render();
        } elseif($type==2) { //全网歌曲
            $data = songsModel::searchCloudMusic($keyword, $page, true);
            if(!isset($data['list'])) {
                print_r($data);
                exit;
            }
            $list = $data['list'];
            $total = $data['total'];
            $totalPage = intval($total /10) +1;
            if(!is_array($list)) {
                $list = [];
                $totalPage = 0;
            }
            foreach ($list as &$v) {
                $v['text_li'] = SingerModel::getSingerLink($v['singer'], '/') . ':'
                    . "<a href='/juzi/songs/uri/{$v['uri']}' target='_blank'>{$v['title']}</a> 
                - 专辑《<a href='/juzi/singer/album/{$v['album']['albumUri']}' target='_blank'>{$v['album']['albumTitle']}</a>》";
            }
            $pageMenu = Str::makeNumPage($path ."?keyword={$keyword}&t={$type}&page={page}", $page,$totalPage, 10, 'active');
        } elseif($type==3) { //本站歌手
            $list = SingerModel::field('uri,title')->where('title', 'like', $likeSql)->limit(10)->select();
            foreach ($list as &$v) {
                $v['text_li'] = "<a href='/juzi/singer/uri/{$v['uri']}' target='_blank'>{$v['title']}</a>";
            }
        } elseif($type==4) { //全网歌手
            $list = songsModel::field('uri,title,singer')->where('title', 'like', $likeSql)->limit(10)->select();
            foreach ($list as &$v) {
                $v['text_li'] = SingerModel::getSingerLink($v['singer'], '/');
            }
        } else {
            print_r('type不支持');
            exit;
        }
        $nullStr = '';
        if(count($list)==0) {
            $nullStr = '没有搜索结果';
        }

        //获取分页显示
        $this->view->assign('webTitle', '包含'.$keyword.'的歌曲['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('t_', $type);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }

    //歌曲详情 按id
    public function details()
    {
        $myUid = $this->auth->id;
        $request = Request::instance();
        $path_ = $request->path();
        $uriArray = explode('songs/details/', $path_);
        $id = trim(end($uriArray));
        if(strstr($id, '/')) {
            $array_ = explode('/', $id);
            $id = trim($array_[0]);
            $page = intval($array_[1]);
        } else {
            $page = 1;
        }
        if(!$id) {
            echo('no_uri');
            exit;
        }
        $albumUri = isset($_GET['album']) ? trim($_GET['album']) : '';
        $sInfo = songsModel::field('uri,title,singer,songid,geci,rq,playurl')->where('id', $id)->find();
        if(!$sInfo) {
            echo('no_record.song:'.$id);
            exit;
        }
        $uriStr = $sInfo['uri'];
        songsModel::updateRq($uriStr);
        //album
        $albumSongs = [];
        if($albumUri) {
            $albumSongsId = albumModel::getfieldbyuri($albumUri, 'songsids');
            if($albumSongsId) {
                $albumSongs = json_decode($albumSongsId, true);
            }
        }
        //获取评论
        //commids,total,more

            $ip = request()->ip();
            $commentInfo = songsModel::caijiComments($sInfo['songid'], $page, $uriStr);
//            if($ip == '127.0.0.1') {
//                $commentInfo = songsModel::caijiComments($sInfo['songid'], $page, $uriStr);
//            } else {
//                $commentInfo = songsModel::caijiLocalComments($sInfo['songid'], $page, $uriStr);
//            }
//            print_r('$commentInfo:');
//            print_r($commentInfo);
//            exit;
        if(isset($commentInfo['commids'])) {
            $commentCache = $commentInfo['commids'];
            $commentList = json_decode($commentCache, true);
            $commentTotal = $commentInfo['total'];
        } else {
            print_r('!isset $commentInfo.commids');
            exit;
            $commentList = [];
            $commentTotal = 0;
        }

        foreach($commentList as &$item) {
            $item['time'] = Date::toYMDS(substr($item['time'], 0, -3));
        }
        unset($item);
        $pageSize = 20;
        $url = "/juzi/songs/details/{$id}/{page}";
        $pageHtml = Str::makeNumPage($url, $page, intval($commentTotal/$pageSize)+1, 10, 'active');
        //获取歌词
        if(!$sInfo['geci']) {
            if(isset($commentInfo['geci']) && $commentInfo['geci']) {
                $sInfo['geci'] = $commentInfo['geci'];
            } else {
                $sInfo['geci'] = songsModel::caijiGeci($sInfo['songid']);
            }
        }
        //获取播放地址
        $sInfo['playurl'] = songsModel::caijiSongUrl($sInfo['songid']);
        $sInfo['geci'] = songsModel::formatGeci($sInfo['geci']);
        $sInfo['singerLinks'] = $sInfo['singer'] ? singerModel::getSingerLink($sInfo['singer'], '/', $uriStr) : '';
        $sInfo['singerName'] = $sInfo['singer'] ? singerModel::getSingerName($sInfo['singer'], '/') : '';
        $this->view->assign('webTitle', "{$sInfo['title']}歌曲赏析,{$sInfo['title']}音乐赏析,{$sInfo['singerName']}的音乐 - ".self::$webTitle);//站点名
        $this->view->assign('keywords', "{$sInfo['title']}歌曲赏析,{$sInfo['title']}音乐赏析,{$sInfo['singerName']}的音乐");//页面关键词
        $this->view->assign('description', "{$sInfo['title']}歌曲赏析,{$sInfo['title']}音乐赏析-{$sInfo['singerName']}的音乐");//页面介绍
        $this->view->assign('albumUri', $albumUri);
        $this->view->assign('albumSongs', $albumSongs);
        $this->view->assign('commentList', $commentList);
        $this->view->assign('commentTotal', $commentTotal);
        $this->view->assign('pageHtml', $pageHtml);
        $this->view->assign('sInfo', $sInfo);
        $this->view->assign('myUid', $myUid);
        $this->view->assign('page', $page);
        print_r($this->view->fetch());
    }
    //网易歌曲
    public function wySongId() {
        $request = Request::instance();
        $path_ = $request->path();
        $uriArray = explode('songs/wySongId/', $path_);
        $uriStr = trim(end($uriArray));
        if(strstr($uriStr, '/')) {
            $array_ = explode('/', $uriStr);
            $uriStr = trim($array_[0]);
            $page = intval($array_[1]);
        } else {
            $page = 1;
        }
        if(!$uriStr) {
            echo('no_uri');
            exit;
        }
        songsModel::caijiSong($uriStr);
        $sInfo = songsModel::field('id,uri,title,singer,songid,geci,rq,playurl')->where('songid', $uriStr)->find();
        if(!$sInfo) {
            echo('no_record:'.$uriStr);
            exit;
        }
//        print_r($sInfo);exit;
        $id = $sInfo['id'];
        header('location: /juzi/songs/details/'.$id.'/'.$page);
        exit;
    }
    //系统歌曲详情
    public function uri()
    {
        $myUid = $this->auth->id;
        $request = Request::instance();
        $path_ = $request->path();
        $uriArray = explode('songs/uri/', $path_);
        $uriStr = trim(end($uriArray));
        if(strstr($uriStr, '/')) {
            $array_ = explode('/', $uriStr);
            $uriStr = trim($array_[0]);
            $page = intval($array_[1]);
        } else {
            $page = 1;
        }
        if(!$uriStr) {
            echo('no_uri');
            exit;
        }
        $albumUri = isset($_GET['album']) ? trim($_GET['album']) : '';
        songsModel::updateRq($uriStr);
        $sInfo = songsModel::field('id,uri,title,singer,songid,geci,rq,playurl')->where('uri', $uriStr)->find();
        if(!$sInfo) {
            echo('no_record:'.$uriStr);
            exit;
        }
        $id = $sInfo['id'];
        header('location: /juzi/songs/details/'.$id.'/'.$page);
    }
    
    //首页
    public function index()
    {
        //搜索记录
        $pageSize = 20;
        $page = input('page', 1, 'int');
        $path = "/juzi/songs/index/";
        $searchList = Db('songsSearch')->field('title')->where('page', 1)->order('id', 'desc')->limit(80)->select();
//        $songList = songsModel::field('uri,title,singer')->order('id', 'desc')
//            ->paginate($pageSize, false,
//                [
//                    'page'=> $page,
//                    'path'=> $path,
//                ]
//            );
//        foreach ($songList as &$v) {
//            $v['li_text'] = "<a href='/juzi/songs/uri/{$v['uri']}' target='_blank'>{$v['title']}</a> - 歌手:".SingerModel::getSingerLink($v['singer'], '/');
//        }
        unset($v);
        $this->view->assign('searchList', $searchList);
//        $this->view->assign('songList', $songList);
        print_r($this->view->fetch());
    }

}
