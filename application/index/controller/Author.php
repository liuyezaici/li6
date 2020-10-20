<?php

namespace app\juzi\controller;

use app\admin\addon\juzi\model\Juzi_from;
use app\common\controller\Frontend;
use app\common\model\Users;
use think\Config;
use think\Request;
use fast\Addon;
use app\admin\addon\juzi\model\Juzi as juziModel;
use app\admin\addon\juzi\model\Juzi_author as juziAuthorModel;

class Author extends Frontend
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



    //所有作者
    public function lists() {
        if ($this->request->isPost()){
            print_r('forbid_post');
            exit;
        }
        $keyword = input('keyword', '', 'trim');
        $page = input('page', 1, 'int');
        $page = (int)$page;
        $pageSize = 40;
        $path = "/juzi/author/lists/";
        $whereSql = [];
        if($keyword) {
            $whereSql['title'] = $keyword;
        }
        $list = juziAuthorModel::order('id', 'desc')
            ->where($whereSql)->paginate($pageSize, false,
                [
                    'page'=> $page,
                    'path'=> $path,
                ]
            );
        $nullStr = '';
        if(count($list)==0) {
            $nullStr = '没有作者';
        }
        // 获取分页显示
        $pageMenu = $list->render();
        $this->view->assign('webTitle', '所有作者['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }



    //作者下的句子
    public function id() {
        if ($this->request->isPost()){
            print_r('forbid_post');
            exit;
        }
        $page = input('page', 1, 'int');
        $page = (int)$page;
        $request = Request::instance();
        $path_ = $request->path();
        if(!strstr($path_, '/author/id/')) {
            print_r('路径不正确,必须是/author/id/');
            exit;
        }
        $array_ = explode('/author/id/', $path_);
        $endStr = $array_[1];
        $endStr = trim($endStr);
        $authorId = trim($endStr, '/');
        $authorId = intval($authorId);
        if(!$authorId) {
            print_r('authorId不能为空');
            exit;
        }
        $pageSize = 10;
        $where = [
            'author' => $authorId
        ];
        $path = "/juzi/author/id/{$authorId}/";
        $authorname = juziAuthorModel::getfieldbyid($authorId, 'title');
        $list = juziModel::where($where)->order('id', 'desc')
            ->paginate($pageSize, false,
                [
                    'page'=> $page,
                    'path'=> $path,
                ]
            );
        foreach ($list as &$v) {
            $v['fromStr'] = '';
            if($v['fromid']) {
                $fromInfo = Juzi_from::field('fromtype,title')->find($v['fromid']);
                if($fromInfo) {
                    $fromType = $fromInfo['fromtype'];
                    $fromTitle = $fromInfo['title'];
                    $v['fromStr'] = Juzi_from::getFromTypeLink($fromType, $v['fromid'], $fromTitle);
                }
            }
        }
        $nullStr = '';
        if(count($list)==0) {
            $nullStr = '没有句子';
        }
        // 获取分页显示
        $pageMenu = $list->render();
        $this->view->assign('webTitle', '作者:'.$authorname.'的句子['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('authorname', $authorname);
        $this->view->assign('authorId', $authorId);
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }

}
