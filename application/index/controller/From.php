<?php

namespace app\juzi\controller;

use app\common\controller\Frontend;
use app\common\model\Users;
use think\Config;
use think\Request;
use fast\Addon;
use app\admin\addon\juzi\model\Juzi as juziModel;
use app\admin\addon\juzi\model\Juzi_author as juziAuthorModel;
use app\admin\addon\juzi\model\Juzi_from as juziFromModel;

class From extends Frontend
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


    //所有来源
    public function lists() {
        if ($this->request->isPost()){
            print_r('forbid_post');
            exit;
        }
        $page = input('page', 1, 'int');
        $page = (int)$page;
        $pageSize = 10;
        $path = "/juzi/from/lists/";
        $list = juziFromModel::order('id', 'desc')
            ->paginate($pageSize, false,
                [
                    'page'=> $page,
                    'path'=> $path,
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
        // 获取分页显示
        $pageMenu = $list->render();
        $this->view->assign('webTitle', '所有来源['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }


    //某来源的句子
    public function id() {
        if ($this->request->isPost()){
            print_r('forbid_post');
            exit;
        }
        $page = input('page', 1, 'int');
        $page = (int)$page;
        $request = Request::instance();
        $path_ = $request->path();
        if(!strstr($path_, '/from/id/')) {
            print_r('路径不正确,必须是/from/id/,当前:'.$path_);
            exit;
        }
        $array_ = explode('/from/id/', $path_);
        $endStr = $array_[1];
        $endStr = trim($endStr);
        $fromId = trim($endStr, '/');
        $fromId = intval($fromId); 
        if(!$fromId) {
            print_r('authorId不能为空');
            exit;
        }

        $pageSize = 10;
        $where = [
            'fromid' => $fromId
        ];
        $path = "/juzi/from/id/{$fromId}/";
        $fromInfo = juziFromModel::get($fromId);
        if(!$fromInfo) {
            print_r('authorId不能为空');
            exit;
        }
        $fromName = $fromInfo['title'];
        $authorid = $fromInfo['authorid'];
        $content = $fromInfo['content'];
        $content = str_replace("\n", '<br />', $content);
        $rq = $fromInfo['rq'];
        $authorName = $authorid ? juziAuthorModel::getfieldbyid($authorid, 'title') : '';
        $list = juziModel::where($where)->order('id', 'desc')
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
        $this->view->assign('webTitle', '来源于:'.$fromName.'的句子['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('fromName', $fromName);
        $this->view->assign('rq', $rq);
        $this->view->assign('authorStr', $authorid ? "<a href=\"/juzi/author/id/{$authorid}\">{$authorName}</a>":'');
        $this->view->assign('authorid', $authorid);
        $this->view->assign('authorName', $authorName);
        $this->view->assign('content', $content);
        $this->view->assign('authorId', $fromId);
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }

}
