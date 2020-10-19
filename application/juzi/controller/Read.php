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

class Read extends Frontend
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

    public function uri()
    {
        $request = Request::instance();
        $path_ = $request->path();
        $uriArray = explode('read/uri/', $path_);
        $uriStr = trim(end($uriArray));
        if(!$uriStr) {
            echo('no_uri');
            exit;
        }
        juziModel::updateRq($uriStr);
        $juziInfo = juziModel::field('createtime,cuid,author,fromid,content,contenthash,tagids,rq')->where('uri', $uriStr)->find();
        if(!$juziInfo) {
            echo('no_record');
            exit;
        }
        $fromInfo = juziFromModel::getbyid($juziInfo['fromid']);
        $fromType = $fromInfo['fromtype'];
        $fromTitle = $fromInfo['title'];
        $juziInfo['fromStr'] = juziFromModel::getFromTypeLink($fromType, $juziInfo['fromid'], $fromTitle);
        $tagNames =  juziTagModel::getTagNames($juziInfo['tagids'], '/');
//        print_r($tagNames);exit;
        $juziInfo['tagList'] =  juziTagModel::getTagList($juziInfo['tagids']);
        $juziInfo['authorName'] = $juziInfo['author'] ? juziAuthorModel::getfieldbyid($juziInfo['author'], 'title') : '';
        $this->view->assign('webTitle', $juziInfo['content']." ".self::$webTitle);//站点名
        $this->view->assign('keywords', "作者{$juziInfo['authorName']}的句子". (($fromTitle)? ',来源:'.$fromTitle: '') . ',关于:'.$tagNames.'的句子' );//页面关键词
        $this->view->assign('description', $juziInfo['content'] . '-作者:'.$juziInfo['authorName']);//页面介绍
        $juziInfo['fromName'] = $juziInfo['fromid'] ? juziFromModel::getfieldbyid($juziInfo['fromid'], 'title') : '';
        $juziInfo['nickname'] = Users::getfieldbyid($juziInfo['cuid'], 'nickname');
        $juziInfo['createtime'] = \fast\Date::toYMDS($juziInfo['createtime']);
        $this->view->assign('juziInfo', $juziInfo);
        print_r($this->view->fetch());
    }

}
