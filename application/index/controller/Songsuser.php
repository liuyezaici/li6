<?php
//用户的评论记录
namespace app\juzi\controller;

use app\admin\addon\songs\model\SongsGedan;
use app\common\controller\Frontend;
use think\Config;
use fast\Addon;
use fast\Date;
use think\Request;
use app\admin\addon\songs\model\SongsSinger as singerModel;
use app\admin\addon\songs\model\Songs as songsModel;
use app\admin\addon\songs\model\SongsUser as userModel;

class Songsuser extends Frontend
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
        $this->view->assign('webTitle', '歌曲-'.self::$webTitle);//站点名
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('tongjiCode', self::$tongjiCode);//统计代码
        $this->view->assign('footContent', self::$footContent);//页脚内容
    }

    //用户最近听歌
    public function recentListen() {
        $ourUid = input('get.u', '', 0);
        $sInfo = userModel::field('uri,title,avatar,wyuid,rq')->where('uri', $ourUid)->find();
        if(!$sInfo) {
            echo('no_record.recent:'.$ourUid);
            exit;
        }
        $wyUid = $sInfo['wyuid'];
        $songList = songsModel::caijiUserRecent($wyUid, 1); //$type1: 最近一周, 0: 所有时间
        $this->view->assign('webTitle', "{$sInfo['title']}的歌曲评论 - ".self::$webTitle);//站点名
        $this->view->assign('keywords', "{$sInfo['title']}的歌曲赏析,网易歌曲评论");//页面关键词
        $this->view->assign('description', "{$sInfo['title']}的歌曲赏析,网易歌曲评论");//页面介绍
        $this->view->assign('uriStr', $ourUid);
        $this->view->assign('sInfo', $sInfo);
        $this->view->assign('gedanList', $gedanList);
        print_r($this->view->fetch());
    }
    //用户的歌单按id
    public function albumid() {
        $request = Request::instance();
        $path_ = $request->path();
        $uriArray = explode('songsuser/albumid/', $path_);
        $id = trim(end($uriArray));
        if(strstr($id, '/')) {
            $id = explode('/', $id)[0];
        }
        if(!$id) {
            echo('no id');
            exit;
        }
        $sInfo = SongsGedan::where('id', $id)->find();
        if(!$sInfo) {
            echo('no_record.album:'.$id);
            exit;
        }
        $uri = $sInfo['uri'];
        $wyUid = $sInfo['wyuid'];
        $songList = songsModel::caijiGedan($sInfo['wyid'], $uri)['songList'];
//        print_r($songList);exit;
        foreach ($songList as &$v) {
            $v['singerLink'] = $v['singer'] ? singerModel::getWySingerLinkByArray($v['singer'], '/', '/') : '';
        }
        unset($v);
        $uInfo = userModel::field('id,uri,title,avatar,wyuid,rq')->where('wyuid', $wyUid)->find();
        if(!$uInfo) {
            echo('no_record.wyuser:'.$wyUid);
            exit;
        }
        $this->view->assign('webTitle', "歌单《{$sInfo['title']}》的歌曲列表 - ".self::$webTitle);//站点名
        $this->view->assign('keywords', "{$sInfo['title']}的歌曲赏析,网易歌曲评论");//页面关键词
        $this->view->assign('description', "{$sInfo['title']}的歌曲赏析,网易歌曲评论");//页面介绍
        $this->view->assign('uriStr', $uri);
        $this->view->assign('uInfo', $uInfo);
        $this->view->assign('sInfo', $sInfo);
        $this->view->assign('songList', $songList);
        print_r($this->view->fetch());
    }
    //用户的歌单
    public function wyGedan($id=null) {
        if(!$id) {
            print_r('no id');
            exit;
        }
        $gedanInfo = songsModel::caijiGedan($id);
        $songList = $gedanInfo['songList'];
        $userId = $gedanInfo['userId'];
        $coverImgUrl = $gedanInfo['coverImgUrl'];
        $description = $gedanInfo['description'];
        $title = $gedanInfo['title'];
        $sInfo = [
            'title' => $title,
            'avatar' => $coverImgUrl,
            'description' => $description,
        ];
//        print_r($songList);exit;
        foreach ($songList as &$v) {
            $v['singerLink'] = $v['singer'] ? singerModel::getWySingerLinkByArray($v['singer'], '/', '/') : '';
        }
        unset($v);
        $uInfo = userModel::field('id,uri,title,avatar,wyuid,rq')->where('wyuid', $userId)->find();
        if(!$uInfo) {
            $uInfo = songsModel::caijiUserInfo($userId);
        }
        $this->view->assign('webTitle', "歌单《{$title}》的歌曲列表 - ".self::$webTitle);//站点名
        $this->view->assign('keywords', "{$title}的歌曲赏析,网易歌曲评论");//页面关键词
        $this->view->assign('description', "{$title}的歌曲赏析,网易歌曲评论");//页面介绍
        $this->view->assign('uInfo', $uInfo);
        $this->view->assign('sInfo', $sInfo);
        $this->view->assign('songList', $songList);
        print_r($this->view->fetch('albumid'));
    }
    //用户的歌单
    public function album($uri=null) {
        if(!$uri) {
            print_r('no uri');
            exit;
        }
        $sInfo = SongsGedan::where('uri', $uri)->find();
        if(!$sInfo) {
            echo('no_record.uri:'.$uri);
            exit;
        }
        $id = $sInfo['id'];
        header('location: /juzi/songsuser/albumid/'.$id);
    }
    //用户的歌单
    public function myAlbum() {
        $ourUri = input('get.u', '', 0);
        $ourUid = input('get.uid', '', 0);
        if($ourUri) {
            $where = [
                'uri' => $ourUri
            ];
        } elseif($ourUid) {
            $where = [
                'id' => $ourUid
            ];
        }
        $sInfo = userModel::field('uri,title,avatar,wyuid,rq')->where($where)->find();
        if(!$sInfo) {
            echo('no_user:'.$ourUid);
            exit;
        }
        $wyUid = $sInfo['wyuid'];
        $gedanList = songsModel::caijiUserGedan($wyUid, 1);
        $this->view->assign('webTitle', "{$sInfo['title']}的歌单 - ".self::$webTitle);//站点名
        $this->view->assign('keywords', "{$sInfo['title']}的歌曲赏析,网易歌曲评论");//页面关键词
        $this->view->assign('description', "{$sInfo['title']}的歌曲赏析,网易歌曲评论");//页面介绍
        $this->view->assign('uriStr', $ourUid);
        $this->view->assign('sInfo', $sInfo);
        $this->view->assign('gedanList', $gedanList);
        print_r($this->view->fetch());
    }


    //最新歌单
    public function newGedan()
    {
        $page = input('page', 1, 'intval');
        $pageSize = 100;
        $list = SongsGedan::order('id', 'desc')
            ->paginate($pageSize, false,
                [
                    'page'=> $page,
                ]
            );
//        print_r(json_encode($list));exit;
        // 获取分页显示
        $pageMenu = $list->render();
        $this->view->assign('webTitle', "最新歌单 - ".self::$webTitle);//站点名
        $this->view->assign('keywords', "最新歌单,网易歌曲评论");//页面关键词
        $this->view->assign('description', "最新歌单,网易歌曲评论");//页面介绍
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }

    //最新用户
    public function index()
    {
        $keyword = input('keyword', '', 'trim');
        $page = input('page', 1, 'intval');
        $pageSize = 10;
        $where = [];
        $topTitle = '最新用户';
        $path = "/juzi/songsuser/index?keyword={$keyword}&page=[PAGE]";
        if($keyword) {
            $keyword = htmlspecialchars($keyword);
            $topTitle = '检索用户:'.$keyword;
            $where['title'] = ['like', "%{$keyword}%"];
        }
        $list = userModel::order('id', 'desc')->where($where)
            ->paginate($pageSize, false,
                [
                    'page'=> $page,
                    'path'=> $path,
                ]
            );
//      print_r(json_encode($list));exit;
        // 获取分页显示
        $pageMenu = $list->render();
        $this->view->assign('webTitle', "{$topTitle} - ".self::$webTitle);//站点名
        $this->view->assign('keywords', "最新用户的歌曲赏析,网易歌曲评论");//页面关键词
        $this->view->assign('description', "最新用户的歌曲赏析,网易歌曲评论");//页面介绍
        $this->view->assign('topTitle', $topTitle);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }

}
