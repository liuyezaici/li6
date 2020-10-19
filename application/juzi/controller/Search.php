<?php

namespace app\juzi\controller;

use app\common\controller\Frontend;
use app\common\model\Users;
use think\Config;
use think\Request;
use fast\Addon;
use app\admin\addon\juzi\model\Juzi as juziModel;
use app\admin\addon\juzi\model\Juzi_author as juziAuthorModel;

class Search extends Frontend
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



    //   搜索句子
    public function index() {
        if ($this->request->isPost()){
            print_r('forbid_post');
            exit;
        }
        $page = input('page', 1, 'int');
        $page = (int)$page;
        $keyword = input('keyword', '', 'trim');
        $t_ = input('t_', 'lr', 'trim');
        if(!in_array($t_, ['lr', 'l', 'r'])) $t_ = 'lr';
        if($t_ == 'lr') {
            $likeSql = "%{$keyword}%";
        } elseif($t_ == 'l') {
            $likeSql = "{$keyword}%";
        } elseif($t_ == 'r') {
            $likeSql = "%{$keyword}";
        }
        $pageSize = 10;
        $path = "/juzi/search/index/";
        $list = JuziModel::field('uri,content,cuid,author')->where('content', 'like', $likeSql)->paginate($pageSize, false,
            [
                'page'=> $page,
                'path'=> $path,
                'query'=> [
                    'keyword' => $keyword,
                    't_' => $t_,
                ],
            ]
        );
        foreach ($list as &$v) {
            $v['authorLink'] = $v['author']>0 ? "/juzi/author/id/{$v['author']}/1" : "#";
            $v['authorName'] = $v['author'] ? juziAuthorModel::getfieldbyid($v['author'], 'title') : Users::getfieldbyid($v['cuid'], 'nickname');
        }
        $nullStr = '';
        if(count($list)==0) {
            $nullStr = '没有搜索结果';
        }
        // 获取分页显示
        $pageMenu = $list->render();
        $this->view->assign('webTitle', '搜索包含'.$keyword.'的句子['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('t_', $t_);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }


}
