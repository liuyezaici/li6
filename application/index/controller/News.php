<?php

namespace app\juzi\controller;

use app\common\controller\Frontend;
use think\Config;
use fast\Addon;
use app\common\model\Users;
use app\admin\addon\juzi\model\Juzi as juziModel;
use app\admin\addon\juzi\model\Juzi_author as juziAuthorModel;

class News extends Frontend
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


    public function index()
    {

        $page = input('page', 1, 'int');
        $page = (int)$page;
        $pageSize = 10;
        $path = "/juzi/news/index/";
        $list = juziModel::order('id', 'desc')
            ->paginate($pageSize, false,
                [
                    'page'=> $page,
                    'path'=> $path,
                ]
            );
        foreach ($list as &$v) {
            $v['authorLink'] = $v['author']>0 ? "/juzi/author/id/{$v['author']}/1" : "#";
            $v['authorName'] = $v['author'] ? juziAuthorModel::getfieldbyid($v['author'], 'title') : Users::getfieldbyid($v['cuid'], 'nickname');
            $v['createtime'] = \fast\Date::toYMDS($v['createtime']);
        }
        $nullStr = '';
        if(count($list)==0) {
            $nullStr = '没有句子';
        }
        // 获取分页显示
        $pageMenu = $list->render();
        $this->view->assign('webTitle', '最新句子['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }
}
