<?php

namespace app\juzi\controller;

use app\common\controller\Frontend;
use app\common\model\Users;
use think\Config;
use think\Db;
use fast\Addon;
use think\Request;
use app\admin\addon\juzi\model\Juzi as juziModel;
use app\admin\addon\juzi\model\Juzi_author as juziAuthorModel;
use app\admin\addon\juzi\model\Juzi_from as juziFromModel;
use app\admin\addon\juzi\model\Juzi_tag as juziTagModel;

class Tag extends Frontend
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

    public function id()
    {
        $request = Request::instance();
        $path_ = $request->path();
        $uriArray = explode('tag/id/', $path_);
        $tagStr = trim(end($uriArray));
        if(!$tagStr) {
            echo('no_tagid');
            exit;
        }
        $newTagIdArray = [];
        foreach (explode(',', $tagStr) as $tmpTagId) {
            if(is_numeric($tmpTagId)) $newTagIdArray[] = $tmpTagId;
        }
        if(!$newTagIdArray) {
            print_r('tagid不能无/');
            exit;
        }
        $page = input('page', 1, 'int');
        $pageSize = 10;
        $listIndex = juziTagModel::searchIndex($newTagIdArray, $page, $pageSize);
        $sidArray= [];
        foreach ($listIndex as $v) {
            $sidArray[] = $v['juziid'];
        }
        // 获取分页显示
        $pageMenu = $listIndex->render();
        $nullStr = '';
        if(count($sidArray)==0) {
            $nullStr = '没有句子';
            $list = [];
        } else {
            $list =  juziModel::where(['id'=> ['in', join(',', $sidArray)]])->order('id', 'desc')
                ->select();
        }
        $tagNames = juziTagModel::getTagNames($newTagIdArray, ',');
        $this->view->assign('webTitle', '关于'. $tagNames .'的句子['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('newTagIdArray', $newTagIdArray);
        $this->view->assign('tagNames', $tagNames);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }

}
