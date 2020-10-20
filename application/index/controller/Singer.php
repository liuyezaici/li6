<?php

namespace app\juzi\controller;

use app\admin\addon\songs\model\Songs;
use app\admin\addon\songs\model\SongsSinger;
use app\admin\addon\songs\model\SongsSingeralbumcache;
use app\common\controller\Frontend;
use think\Config;
use think\Request;
use fast\Addon;
use fast\Str;
use app\admin\addon\songs\model\SongsSingeralbum as albumModel;

class Singer extends Frontend
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
        $this->view->assign('webTitle', self::$webTitle);//站点名
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('tongjiCode', self::$tongjiCode);//统计代码
        $this->view->assign('footContent', self::$footContent);//页脚内容
    }


    //歌手的专辑 id
    public function albumid()
    {
        $request = Request::instance();
        $path_ = $request->path();
        $uriArray = explode('singer/albumid/', $path_);
        $uriStr = trim(end($uriArray));
        if(strstr($uriStr, '/')) {
            $array_ = explode('/', $uriStr);
            $id = intval($array_[0]);
            $page = intval($array_[1]);
        } else {
            $page = 1;
        }
        if(!$id) {
            echo('no id');
            exit;
        }
        $sInfo = albumModel::field('uri,title,wyavatar,wyalbumidid,wysingerid,songsids,songs,desc,rq')->where('id', $id)->find();
        if(!$sInfo) {
            echo('no_record:'.$id);
            exit;
        }
        $uriStr = $sInfo['uri'];
        albumModel::updateRq($uriStr);
        if($songList = $sInfo['songsids']) {
            $songList = json_decode($songList, true);
            $songNum = $sInfo['songs'];
            $albumDesc = $sInfo['desc'];
        } else {
            $songInfo = albumModel::caijiAlbumsSongs($sInfo['wyalbumidid']);
            if(!is_array($songInfo)) {
                print_r($songInfo);
                exit;
            }
            $songNum = $songInfo['songNum'];
            $songList = $songInfo['songList'];
            $albumDesc = $songInfo['desc'];
        }
        $albumDesc = str_replace("\n", '<br />', $albumDesc);
        $albumDesc = str_replace("\\n", '<br />', $albumDesc);
        $albumDesc = str_replace("网易云音乐", '互联网', $albumDesc);
        foreach ($songList as &$v) {
            $v['singer'] = SongsSinger::getSingerLink($v['singer'], '/');
        }
        unset($v);
        $singerLinks = SongsSinger::getWySingerByWyId($sInfo['wysingerid'],'/');
        $singerName = SongsSinger::getSingerName($sInfo['wysingerid'],'/');
        $this->view->assign('webTitle', "专辑:{$sInfo['title']}-歌手:{$singerName} ".self::$webTitle);//站点名
        $this->view->assign('singerLinks', $singerLinks);
        $this->view->assign('albumDesc', $albumDesc);
        $this->view->assign('sInfo', $sInfo);
        $this->view->assign('songList', $songList);
        $this->view->assign('songNum', $songNum);
        $this->view->assign('albumDesc', $albumDesc);
        print_r($this->view->fetch());
    }

    //网易歌手的专辑 uri
    public function wyuri()
    {
        $request = Request::instance();
        $path_ = $request->path();
        $path_ = strtolower($path_);
        $uriArray = explode('singer/wyuri/', $path_);
        $uriStr = trim(end($uriArray));
        if(strstr($uriStr, '/')) {
            $array_ = explode('/', $uriStr);
            $uriStr = trim($array_[0]);
            $page = intval($array_[1]);
        } else {
            $page = 1;
        }
//        print_r($uriStr);
//        exit;
        $sInfo = SongsSinger::field('id,title,singerid,avatar,rq')->where('singerid', $uriStr)->find();
        if(!$sInfo) {
            echo('no_record:'.$uriStr);
            exit;
        }
        $id = $sInfo['id'];
        header('location: /juzi/singer/id/'.$id.'/'.$page);
    }

    //歌手的专辑 uri
    public function album()
    {
        $request = Request::instance();
        $path_ = $request->path();
        $uriArray = explode('singer/album/', $path_);
        $uriStr = trim(end($uriArray));
        if(strstr($uriStr, '/')) {
            $array_ = explode('/', $uriStr);
            $uriStr = trim($array_[0]);
            $page = intval($array_[1]);
        } else {
            $page = 1;
        }
        $id = albumModel::getfieldbyuri($uriStr, 'id');
        if(!$id) {
            echo('no_record_uri:'.$uriStr);
            exit;
        }
        header('location: /juzi/singer/albumid/'.$id.'/'.$page);
    }

    //歌手的歌曲 所有专辑uri
    public function id()
    {
        $request = Request::instance();
        $path_ = $request->path();
        $uriArray = explode('singer/id/', $path_);
        $id = trim(end($uriArray));
        if(strstr($id, '/')) {
            $array_ = explode('/', $id);
            $id = intval($array_[0]);
            $page = intval($array_[1]);
        } else {
            $id = intval($id);
            $page = 1;
        }
        $songpage = input('songpage', 1, 'int');
        if(!$id) {
            echo('no_id');
            exit;
        }
        $sInfo = SongsSinger::field('uri,title,singerid,avatar,rq')->where('id', $id)->find();
        if(!$sInfo) {
            echo('no_record:'.$id);
            exit;
        }
        $uriStr = $sInfo['uri'];
        SongsSinger::updateRq($uriStr);
        //专辑会失效 比如： https://music.163.com/#/album?id=95270958  所以还是实时获取比较好
//        if($albumInfo = SongsSingeralbumcache::hasGetPage($sInfo['singerid'], $page)) {
//        } else {
//            $albumInfo = SongsSinger::caijiAlbums($sInfo['singerid'], $page);
//        }
        $albumInfo = SongsSinger::caijiAlbums($sInfo['singerid'], $page);
        $total = $albumInfo['pagenum'];
        $url = "/juzi/singer/id/{$id}/{page}";
        $albumPageSize = 12;
        $albumPageHtml = Str::makeNumPage($url, $page, intval($total/$albumPageSize)+1, 12, 'active');
        $albumPageHtml = str_replace('class="pagination"', 'class="pagination pagination-xs"', $albumPageHtml);
        //查询歌手所有歌曲
        $path = "/juzi/singer/id/{$id}";
        $songsPageSize = 20;
        $songList = Songs::where([
            'singer'=>  $uriStr
        ])->field('uri,title')
        ->paginate($songsPageSize, false,
            [
                'var_page'=> 'songpage',
                'page'=> $songpage,
                'path'=> $path,
            ]
        );
        $songPageHtml = $songList->render();
        $songPageHtml = str_replace('class="pagination"', 'class="pagination pagination-md"', $songPageHtml);

        $this->view->assign('webTitle', "歌手{$sInfo['title']}的歌曲和音乐专辑-- ".self::$webTitle);//站点名
        $this->view->assign('keywords', "歌手{$sInfo['title']}的歌曲,歌手{$sInfo['title']}的音乐专辑");//页面关键词
        $this->view->assign('description', "歌手{$sInfo['title']}的所有歌曲、音乐专辑");//页面介绍
        $this->view->assign('albumInfo', $albumInfo);
        $this->view->assign('sInfo', $sInfo);
        $this->view->assign('albumPageHtml', $albumPageHtml);
        $this->view->assign('songList', $songList);
        $this->view->assign('songPageHtml', $songPageHtml);
        print_r($this->view->fetch());
    }

    //歌手的歌曲 所有专辑uri
    public function uri()
    {
        $request = Request::instance();
        $path_ = $request->path();
        $uriArray = explode('singer/uri/', $path_);
        $uriStr = trim(end($uriArray));
        if(strstr($uriStr, '/')) {
            $array_ = explode('/', $uriStr);
            $uriStr = trim($array_[0]);
            $page = intval($array_[1]);
        } else {
            $page = 1;
        }
        $songpage = input('songpage', 1, 'int');
        if(!$uriStr) {
            echo('no_uri');
            exit;
        }
        SongsSinger::updateRq($uriStr);
        $sInfo = SongsSinger::field('id,title,singerid,avatar,rq')->where('uri', $uriStr)->find();
        if(!$sInfo) {
            echo('no_record:'.$uriStr);
            exit;
        }
        $id = $sInfo['id'];
        header('location: /juzi/singer/id/'.$id.'/1');
    }


}
