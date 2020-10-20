<?php

namespace app\juzi\controller;

use app\common\controller\Frontend;
use app\common\model\Users;
use think\Config;
use think\Request;
use fast\Addon;
use app\admin\addon\juzi\model\Juzi_fromarticle as juziArticleModel;
use app\admin\addon\juzi\model\Juzi_author as juziAuthorModel;
use app\admin\addon\juzi\model\Juzi_from as juziFromModel;

class Dianji extends Frontend
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


    //获取作者的
    public function author($name='') {
        print_r($name);
    }

    //文章阅读
    public function article() {
        if ($this->request->isPost()){
            print_r('forbid_post');
            exit;
        }
        $request = Request::instance();
        $path_ = $request->path();
        if(!strstr($path_, '/dianji/article/')) {
            print_r('路径不正确,必须是/dianji/article/,当前:'.$path_);
            exit;
        }
        $array_ = explode('/dianji/article/', $path_);
        $endStr = $array_[1];
        $endStr = trim($endStr);
        $articleId = trim($endStr, '/');
        $articleId = intval($articleId);
        if(!$articleId) {
            print_r('id不能为空');
            exit;
        } 
        $articleInfo = juziArticleModel::get($articleId);
        if(!$articleInfo) {
            print_r('文章不存在');
            exit;
        }
        $articleTitle = $articleInfo['title'];
        $authorid = $articleInfo['authorid'];
        $fromId = $articleInfo['fromid'];
        $content = $articleInfo['content'];
        $content = str_replace("\n", '<br />', $content);
        $rq = $articleInfo['rq'];
        $fromInfo = juziFromModel::get($fromId);
        if(!$fromInfo) {
            print_r('文章来源不存在');
            exit;
        }
        $fromName = $fromInfo['title'];
        $authorName = $authorid ? juziAuthorModel::getfieldbyid($authorid, 'title') : '';
        juziArticleModel::updateRq($articleId);
        $articleInfo['authorName'] = $articleInfo['authorid'] ? juziAuthorModel::getfieldbyid($articleInfo['authorid'], 'title') : '';
        $content = str_replace("\n", '<br/>', $articleInfo['content']);
        $content = preg_replace("/^(\s|　)+/",'', $content); //去掉前面的空格
        $this->view->assign('webTitle', $articleTitle.'古典文集'. $fromName .'的文章['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('articleInfo', $articleInfo);
        $this->view->assign('content', $content);
        $this->view->assign('fromName', $fromName);
        $this->view->assign('rq', $rq);
        $this->view->assign('authorStr', $authorid ? "<a href=\"/juzi/author/id/{$authorid}\" target='_blank'>{$authorName}</a>":'');
        $this->view->assign('authorid', $authorid);
        $this->view->assign('authorName', $authorName);
        $this->view->assign('content', $content);
        $this->view->assign('authorId', $fromId); 
        print_r($this->view->fetch());
    }

    //某典籍的文章
    public function main() {
        if ($this->request->isPost()){
            print_r('forbid_post');
            exit;
        }
        $page = input('page', 1, 'int');
        $page = (int)$page;
        $request = Request::instance();
        $path_ = $request->path();
        if(!strstr($path_, '/dianji/main/')) {
            print_r('路径不正确,必须是/dianji/main/,当前:'.$path_);
            exit;
        }
        $array_ = explode('/dianji/main/', $path_);
        $endStr = $array_[1];
        $endStr = trim($endStr);
        $fromId = trim($endStr, '/');
        $fromId = intval($fromId);
        if(!$fromId) {
            print_r('authorId不能为空');
            exit;
        }

        $pageSize = 50;
        $path = "/juzi/dianji/main/{$fromId}/";
        $articleInfo = juziFromModel::get($fromId);
        if(!$articleInfo) {
            print_r('authorId不能为空');
            exit;
        }
        $fromName = $articleInfo['title'];
        $authorid = $articleInfo['authorid'];
        $content = $articleInfo['content'];
        $content = str_replace("\n", '<br />', $content);
        $rq = $articleInfo['rq'];
        $authorName = $authorid ? juziAuthorModel::getfieldbyid($authorid, 'title') : '';
        $where = [
            'fromid' => $fromId
        ];
        $list = juziArticleModel::where($where)->order('id', 'asc')
            ->paginate($pageSize, false,
                [
                    'page'=> $page,
                    'path'=> $path,
                ]
            );
        $nullStr = '';
        if(count($list)==0) {
            $nullStr = '没有录入';
        }
        juziFromModel::updateRq($fromId);
        // 获取分页显示
        $pageMenu = $list->render();
        $this->view->assign('webTitle', '古典文集:'.$fromName.'的文章['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('fromName', $fromName);
        $this->view->assign('rq', $rq);
        $this->view->assign('authorStr', $authorid ? "<a href=\"/juzi/author/id/{$authorid}\" target='_blank'>{$authorName}</a>":'');
        $this->view->assign('authorid', $authorid);
        $this->view->assign('authorName', $authorName);
        $this->view->assign('content', $content);
        $this->view->assign('authorId', $fromId);
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }

    //所有典籍
    public function index() {
        if ($this->request->isPost()){
            print_r('forbid_post');
            exit;
        }
        $filterTitle = '';
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
            'fromtype'=> ['in', [juziFromModel::$fromtypeGushi,juziFromModel::$fromtypeDianji]]
        ];
        if($authorid) {
            $query['authorid'] = $authorid;
            $whereSql['authorid'] = $authorid;
            $filterTitle .= " &raquo; {$author}";
        }
        if($keyword) {
            $query['keyword'] = $keyword;
            $whereSql['title'] = ['like', "%{$keyword}%"];
            $filterTitle .= " &raquo; {$keyword}";
        }
        $pageSize = 10;
        $path = "/juzi/dianji/index/";
        $list = juziFromModel::where($whereSql)->order('id', 'desc')
            ->paginate($pageSize, false,
                [
                    'page'=> $page,
                    'path'=> $path,
                    'query'=> $query,
                ]
            );
//        print_r(juziFromModel::getlastsql());exit;
//        print_r($list);exit;
        foreach ($list as &$v) {
            $v['authorLink'] = $v['authorid']>0 ? "/juzi/author/id/{$v['authorid']}/1" : "#";
            $v['authorName'] = $v['authorid'] ? juziAuthorModel::getfieldbyid($v['authorid'], 'title') : Users::getfieldbyid($v['cuid'], 'nickname');
        }
        $nullStr = '';
        if(count($list)==0) {
            $nullStr = '没有来源';
        }
        //所有朝代
//      $allYear = yearModel::select();
        // 获取分页显示
        $pageMenu = $list->render();
        $this->view->assign('webTitle', $filterTitle.'古典文集['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('keyword', $keyword);
//        $this->view->assign('allYear', $allYear);
        $this->view->assign('author', $author);
        $this->view->assign('filterTitle', $filterTitle);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }
}
