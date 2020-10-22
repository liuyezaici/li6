<?php

namespace app\index\controller;

use app\admin\addon\fujian\model\Fujian;
use app\common\controller\Frontend;
use app\common\model\Users;
use fast\File;
use fast\Date;
use fast\Addon;
use \app\admin\addon\article\model\Article as ArticleModel;
use \app\admin\addon\article\model\ArticleTypes as ArticleTypesModel;

class Index extends Frontend
{

    protected $noNeedLogin = ['details', 'index'];
    protected $noNeedRight = '*';
    protected $layout = '';
    protected $keyword = '';
    protected $allTypes = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->allTypes = ArticleTypesModel::select();
        $this->keyword = input('keyword', '', 'trim');
    }

    //文章详情
    public function details($id=NULL){
        $id = intval($id);
        if(!$id) {
            $this->error('缺少参数id');
        }
        $info = ArticleModel::get($id);
        if(!$info) {
            $this->error('数据不存在');
        }
        $myUid = $this->auth->id;
        ArticleModel::updateRq($id);
        $info['typeName'] = $info['typeid'] ? ArticleTypesModel::getFieldById($info['typeid'], 'title') : '';
        //markdown
        include_once(ROOT_PATH . 'assets/libs/markdown/Markdown.php');
        include_once(ROOT_PATH . 'assets/libs/markdown/MarkdownExtra.php');
        $info['content'] = \MarkdownExtra::defaultTransform( $info['content']);
        $info['content'] = preg_replace("/<img(.+)src=\"([^\"]+)\"(.+)>/", '<img class="lazy"$1data-original=\'$2\' src=\'/assets/img/loading2.gif\'$3>',$info['content']);
        //上一篇 下一篇
        $prevArticle = ArticleModel::getPrevNextArticle($info['typeid'], $id, '<');
        $nextArticle = ArticleModel::getPrevNextArticle($info['typeid'], $id, '>');

        $myUid = $this->auth->id;
        $showEdit = false;
        if($myUid == $info['cuid']) {
            $showEdit = true;
        }
        $info['author'] = Users::getfieldbyid($info['cuid'], 'username');
        $rightHtml = $this->view->fetch('', [
            'webTitle' =>  $info['title'],
            'info' =>  $info,
            'showEdit' =>  $showEdit,
            'prevArticle' =>  $prevArticle,
            'nextArticle' =>  $nextArticle,
        ]);
        print_r($rightHtml);
    }

    //我的文章
    public function my(){
        $tab = input('tab', 0, 'int');
        $page = input('page', 1, 'int');
        $keyword = input('keyword', '', 'trim');
        $topTitle = '最新文章';
        $noResultText = '还没有文章';
        $where = [];
        $myUid = $this->auth->id;
        $where['uid'] = $myUid;
        if($tab) {
            $where['typeid'] = $tab;
            $topTitle = '分类:'. ArticleTypesModel::getFieldById($tab, 'title') ;
            $noResultText = '分类没有文章';

        }

        $articleHeader = $this->view->fetch('topMy', [
            'allTypes' => $this->allTypes,
            'tab' => $tab,
            'keyword' => $keyword
        ]);
        if($keyword) {
            $where['title'] = ['like', "%{$keyword}%"];
            $topTitle = '搜索文章:'. $keyword;
            $noResultText = '没有搜索结果';
        }
        $path = "/uc/article/my/?keyword={$keyword}&tab={$tab}&page=[PAGE]";
        $result = ArticleModel::where($where)->order('id', 'Desc')->paginate(10, false,
            [
                'page' => $page,
                'path' => $path,
            ]
        );
        $articleList = json_decode(json_encode($result), true)['data'];
        foreach ($articleList as &$v) {
            $v['typeName'] = $v['typeid'] ? ArticleTypesModel::getFieldById($v['typeid'], 'title') : '';
        }
        unset($v);
        $pageMenu = $result->render();
        $rightHtml = $this->view->fetch('myRight', [
            'articleHeader' =>  $articleHeader,
            'topTitle' =>  $topTitle,
            'articleList' =>  $articleList,
            'pageMenu' =>  $pageMenu,
            'noResultText' =>  $noResultText,
        ]);
        $this->view->assign('webTitle',   'beyond资料、歌迷文章、周边讯息');
        $this->view->assign('right',   $this->view->fetch('common/right', ['rightHtml' =>  $rightHtml]));
        print_r($this->view->fetch());
    }

    //文章首页
    public function index(){
        $typeId = input('typeId', 0, 'int');
        $page = input('page', 1, 'int');
        $topTitle = '最新文章';
        $noResultText = '还没有文章';
        $where = [];
        if($typeId) {
            $where['typeid'] = $typeId;
            $topTitle = '分类:'. ArticleTypesModel::getFieldById($typeId, 'title') ;
            $noResultText = '分类没有文章';

        }
        if($this->keyword) {
            $where['title'] = ['like', "%{$this->keyword}%"];
            $topTitle = '搜索文章:'. $this->keyword;
            $noResultText = '没有搜索结果';
        }
        $path = "/index/?keyword={$this->keyword}&typeId={$typeId}&page=[PAGE]";
        $result = ArticleModel::where($where)->order('id', 'Desc')->paginate(10, false,
            [
                'page' => $page,
                'path' => $path,
            ]
        );
//        print_r(ArticleModel::getlastsql());exit;
        $articleList = json_decode(json_encode($result), true)['data'];
        foreach ($articleList as &$v) {
            $v['typeName'] = $v['typeid'] ? ArticleTypesModel::getFieldById($v['typeid'], 'title') : '';
        }
        unset($v);
        $pageMenu = $result->render();
        $pageMenu = str_replace('', '', $pageMenu);
        $this->view->assign('webTitle',   '文章');
        $this->view->assign('typeId',   $typeId);
        $this->view->assign('topTitle',   $topTitle);
        $this->view->assign('articleList',   $articleList);
        $this->view->assign('keyword',   $this->keyword);
        $this->view->assign('pageMenu',   $pageMenu);
        $this->view->assign('allTypes',   $this->allTypes);
        $this->view->assign('noResultText',   $noResultText);
        print_r($this->view->fetch());
    }
}
