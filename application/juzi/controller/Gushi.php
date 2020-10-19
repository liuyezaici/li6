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
use app\admin\addon\juzi\model\Juzi_gushiyear as yearModel;

class Gushi extends Frontend
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
        $fromName = juziFromModel::getfieldbyid($fromId, 'title');
        $list = juziModel::where($where)->order('id', 'desc')
            ->paginate($pageSize, false,
                [
                    'page'=> $page,
                    'path'=> $path,
                ]
            );
        $nullStr = '';
        if(count($list)==0) {
            $nullStr = '没有句子';
        }
        // 获取分页显示
        $pageMenu = $list->render();
        $this->view->assign('webTitle', '来源于:'.$fromName.'的句子['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('fromName', $fromName);
        $this->view->assign('authorId', $fromId);
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }


    //所有古诗
    public function index() {
        if ($this->request->isPost()){
            print_r('forbid_post');
            exit;
        }
        $filterTitle = '';
        $keyword = input('keyword', '', 'trim');
        $author = input('author', '', 'trim');
        $authorid = input('authorid', 0, 'int');
        $yearid = input('yearid', '', 'trim');
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
        $yearName = '';
        if($yearid) {
            $yearName= yearModel::getfieldbyid($yearid, 'title');
            if(!$yearName) {
                echo '朝代不存在';
                exit;
            }
        }
        $query= [];
        $whereSql = [
            'fromtype'=> juziFromModel::$fromtypeGushi
        ];
        if($authorid) {
            $query['authorid'] = $authorid;
            $whereSql['authorid'] = $authorid;
            $filterTitle .= " &raquo; {$author}";
        }
        if($yearid) {
            $query['yearid'] = $yearid;
            $whereSql['yearid'] = $yearid;
            $filterTitle .= " &raquo; {$yearName}";
        }
        if($keyword) {
            $whereSqlSame = $whereSql;
            $whereSqlSame['title'] = $keyword;
            $query['keyword'] = $keyword;
            $whereSql['title'] = ['like', "%{$keyword}%"];
            $filterTitle .= " &raquo; {$keyword}";
        }
        $pageSize = 10;
        $path = "/juzi/gushi/index/";
        //关键词全等搜索
        $listSame = [];
        $hasId = '';
        if($keyword && $page == 1) {
            $listSame = juziFromModel::where($whereSqlSame)->paginate(5)->toArray();
            $listSame = $listSame['data'];
            $hasId = array_column($listSame, 'id');
            if($hasId) {
                $whereSql['id'] = ['not in', $hasId];
            }
//            print_r($whereSql);exit;
        }
//        print_r(json_encode($listSame));exit;
        $listObj = juziFromModel::where($whereSql)->paginate($pageSize, false,
                [
                    'page'=> $page,
                    'path'=> $path,
                    'query'=> $query,
                ]
            );
//        print_r(juziFromModel::getlastsql());exit;
        $list= $listObj->toArray();
//        print_r(($list['data']));exit;
        if($listSame) $list['data'] = array_merge($listSame, $list['data']);
        $list = $list['data'];
//        print_r(($list['data']));exit;
//        print_r(juziFromModel::getlastsql());exit;
//        print_r(($list));exit;
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
        $pageMenu = $listObj->render();
        $this->view->assign('webTitle', $filterTitle.'古代诗歌全集['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('keyword', $keyword);
//        $this->view->assign('allYear', $allYear);
        $this->view->assign('yearid', $yearid);
        $this->view->assign('author', $author);
        $this->view->assign('filterTitle', $filterTitle);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }
}
