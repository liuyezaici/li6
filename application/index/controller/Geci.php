<?php

namespace app\juzi\controller;

use app\admin\addon\juzi\model\Juzi_from;
use app\common\controller\Frontend;
use app\common\model\Users;
use think\Config;
use think\Request;
use fast\Addon;
use app\admin\addon\juzi\model\Juzi_author as juziAuthorModel;
use app\admin\addon\juzi\model\Juzi_fromarticle as juziArticleModel;
use app\admin\addon\juzi\model\Juzi_from as juziFromModel;
use app\admin\addon\juzi\model\Juzi_gushiyear as yearModel;

class Geci extends Frontend
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


    //某 歌词
    public function main() {
        if ($this->request->isPost()){
            print_r('forbid_post');
            exit;
        }
        $request = Request::instance();
        $path_ = $request->path();
        if(!strstr($path_, '/geci/main/')) {
            print_r('路径不正确,必须是/geci/main/,当前:'.$path_);
            exit;
        }
        $array_ = explode('/geci/main/', $path_);
        $endStr = $array_[1];
        $endStr = trim($endStr);
        $fromId = trim($endStr, '/');
        $fromId = intval($fromId);
        if(!$fromId) {
            print_r('authorId不能为空');
            exit;
        }
        $articleInfo = juziFromModel::get($fromId);
        if(!$articleInfo) {
            print_r('authorId不能为空');
            exit;
        }
        $fromName = $articleInfo['title'];
        $authorid = $articleInfo['authorid'];
        $content = $articleInfo['content'];
        $fromtype = $articleInfo['fromtype'];
        if($fromtype !== Juzi_from::$fromtypeGeci) {
            print_r('此内容不是小说');
            exit;
        }
        $content = str_replace("\n", '<br />', $content);
        $rq = $articleInfo['rq'];
        $authorName = $authorid ? juziAuthorModel::getfieldbyid($authorid, 'title') : '';
        juziFromModel::updateRq($fromId);
        // 获取分页显示
        $this->view->assign('webTitle', '歌词《'.$fromName.'》['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('fromName', $fromName);
        $this->view->assign('rq', $rq);
        $this->view->assign('authorStr', $authorid ? "<a href=\"/juzi/author/id/{$authorid}\" target='_blank'>{$authorName}</a>":'');
        $this->view->assign('authorid', $authorid);
        $this->view->assign('authorName', $authorName);
        $this->view->assign('content', $content);
        $this->view->assign('authorId', $fromId);
        print_r($this->view->fetch());
    }


    //所有歌词
    public function index() {
        if ($this->request->isPost()){
            print_r('forbid_post');
            exit;
        }
        $keyword = input('keyword', '', 'trim');
        $author = input('author', '', 'trim');
        $authorid = input('authorid', 0, 'int');
        $page = input('page', 1, 'int');
        $page = (int)$page;
        if($author) {
            $authorid = juziAuthorModel::getfieldbytitle($author, 'id');
            if(!$authorid) {
                echo '作者不存在';
                exit;
            }
        }
        if($authorid) {
            $author = juziAuthorModel::getfieldbyid($authorid, 'title');
            if(!$author) {
                echo '作者不存在';
                exit;
            }
        }
        $query= [];
        $whereSql = [
            'fromtype'=>juziFromModel::$fromtypeGeci
        ];
        if($authorid || $keyword) {
            if($authorid) {
                $query['authorid'] = $authorid;
                $whereSql['authorid'] = $authorid;
                $filterTitle = " 作者[{$author}]的歌词";
            }
            if($keyword) {
                $query['keyword'] = $keyword;
                $whereSql['title'] = ['like', "%{$keyword}%"];
                $filterTitle = " 查找[{$keyword}]的歌词";
            }
        } else {
            $filterTitle = "最新歌词";
        }
        $pageSize = 10;
        $path = "/juzi/geci/index/";
        $list = juziFromModel::where($whereSql)->order('id', 'desc')
            ->paginate($pageSize, false,
                [
                    'page'=> $page,
                    'path'=> $path,
                    'query'=> $query,
                ]
            );
        foreach ($list as &$v) {
            $v['authorLink'] = $v['authorid']>0 ? "/juzi/author/id/{$v['authorid']}/1" : "#";
            $v['authorName'] = $v['authorid'] ? juziAuthorModel::getfieldbyid($v['authorid'], 'title') : Users::getfieldbyid($v['cuid'], 'nickname');
        }
        $nullStr = '';
        if(count($list)==0) {
            $nullStr = '没有来源';
        }
        //所有朝代
//        $allYear = yearModel::select();
        // 获取分页显示
        $pageMenu = $list->render();
        $this->view->assign('webTitle', $filterTitle.'['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('author', $author);
        $this->view->assign('filterTitle', $filterTitle);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }
}
