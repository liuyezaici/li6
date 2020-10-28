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
