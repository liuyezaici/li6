<?php

namespace app\user\controller;

use app\admin\addon\fujian\model\Fujian;
use app\common\controller\Backend;
use fast\Date;
use \app\admin\addon\article\model\Article as ArticleModel;
use \app\admin\addon\article\model\ArticleTypes as ArticleTypesModel;

class Article extends Backend
{

    protected $noNeedLogin = [];
    protected $noNeedRight = '*';
    protected $layout = '';
    protected $keyword = '';
    protected $allTypes = [];

    public function _initialize()
    {
        parent::_initialize();
    }

    //写一篇
    public function add() {
        $myUid = $this->auth->id;
        if($this->request->isPost()) {
            $rows = input('post.rows/a');
            if(!$rows) {
                $this->error('no rows');
            }
            $title = isset($rows['title']) ? trim($rows['title']) : '';
            $content = isset($rows['content']) ? trim($rows['content']) : '';
            $typeId = isset($rows['typeid']) ? intval($rows['typeid']) : 0;
            if(!$typeId) $this->error('请选择分类');
            if(!$title) $this->error('请输入标题');
            if(!$content) $this->error('请输入内容');
            //过滤内容的附件
            $rows['cuid'] = $myUid;
            $rows['ctime'] = Date::toYMDS();
            $sid = ArticleModel::insertGetId($rows);
            $this->success('发布成功');
        }
        print_r($this->view->fetch());
    }



    //删除
    public function del($id=NULL){
        $id = intval($id);
        if(!$id) {
            $this->error('缺少参数id');
        }
        $info = ArticleModel::get($id);
        if(!$info) {
            $this->error('数据不存在');
        }
        $uid = ArticleModel::getfieldbyid($id, 'uid');
        $myUid = $this->auth->id;
        if($myUid != $uid) $this->error('身份已经切换');
        //删除附件
        Fujian::removeAddonFile('article', $id);
        ArticleModel::where('id', $id)->delete();
        $this->success('删除成功');
    }
    //添加分类
    final function add_type() {
        $myUid = $this->auth->id;
        if($this->request->isPost()) {
            $title = input('title', '', 'trim');
            if(!$title) $this->error('no title');
            if(ArticleTypesModel::where(['title' => $title])->find()) $this->error('分类已经存在');
            $newData = [
                'title' => $title,
                'cuid' => $myUid,
                'addtime' => time(),
            ];
            ArticleTypesModel::insert($newData);
            $this->success('添加成功');
        }
        $arr = array(
            'id' => 0,
            'title' => '',
            'modify' => 'add'
        );
        $mainHtml = $this->view->fetch('', $arr);
        print_r($mainHtml);
    }

    //编辑文章分类
    final function edit_type() {
        $id = input('id', 0, 'int');
        //id参数是否为空
        if( !$id ){
            $this->error('no id');
        }
        $classInfo = ArticleTypesModel::where('id', $id)->find();
        if(!$classInfo) $this->error('分类不存在');
        if($this->request->isPost()) {
            $title = input('title', '', 'trim');
            if(!$title) $this->error('no title');
            if(ArticleTypesModel::where(['title' => $title, 'id'=>['<>', $id]])->find()) $this->error('分类已经存在');
            $editData = [
                'title' => $title,
            ];
            ArticleTypesModel::where('id', $id)->update($editData);
            $this->success('添加成功');
        }
        $mainHtml = $this->view->fetch('', json_decode(json_encode($classInfo), true));
        print_r($mainHtml);
    }

    //编辑文章分类
    final function del_type() {
        $id = input('id', 0, 'int');
        //id参数是否为空
        if( !$id ){
            $this->error('no id');
        }
        ArticleTypesModel::where('id', $id)->delete();
        $this->error('删除成功');
    }

    //管理所有分类
    public function manageAllTypes(){
        $list = ArticleTypesModel::field('id,title')->order('id', 'desc')->select();
        //获取分类树形菜单
        $mainHtml = $this->view->fetch('', [
          'class_list' => json_encode($list)
        ]);
        print_r($mainHtml);
    }
    //获取所有分类
    public function getAllTypes($id=NULL){
        $list = ArticleTypesModel::field('id,title')->select();
        $this->success('获取成功', '', ['list' => $list]);
    }
    //获取详情
    public function get($id=NULL) {
        $info = ArticleModel::getbyid($id);
        $this->success('获取成功', '', $info);
    }

    //编辑详情
    public function edit($id=NULL){
        $info = ArticleModel::getbyid($id);
        $myUid = $this->auth->id;
        if($myUid != $info['cuid']) $this->error('身份已经切换:'.$myUid . '!='. $info['cuid']);
        if($this->request->isPost()) {
            $rows = input('post.rows/a');
            if(!$rows) {
                $this->error('no rows');
            }
            $title = isset($rows['title']) ? trim($rows['title']) : '';
            $content = isset($rows['content']) ? trim($rows['content']) : '';
            $typeId = isset($rows['typeid']) ? intval($rows['typeid']) : 0;
            if(!$typeId) $this->error('请选择分类');
            if(!$title) $this->error('请输入标题');
            if(!$content) $this->error('请输入内容');
            ArticleModel::where('id', $id)->update($rows);
            $this->success('编辑成功');
        }
        $mainHtml = $this->view->fetch('', [
            'id' =>  $id,
            'savePath' =>  'upload/post_files/',
            'upload_safe_code' =>  \fast\Str::makeSafeUploadCode('upload/post_files/', $myUid), //生成安全码 防止上传路径被手动篡改
        ]);
        print_r($mainHtml);
    }


    //我的文章
    public function index(){
        $typeid = input('typeid', 0, 'int');
        $page = input('page', 1, 'int');
        $keyword = input('keyword', '', 'trim');
        $topTitle = '我的文章';
        $noResultText = '还没有文章';
        $pagesize = 10;
        $where = [];
        $myUid = $this->auth->id;
//        $where['uid'] = $myUid;
        if($typeid) {
            $where['typeid'] = $typeid;
            $topTitle = '分类:'. ArticleTypesModel::getFieldById($typeid, 'title') ;
            $noResultText = '分类没有文章';
        }
        if($keyword) {
            $where['title'] = ['like', "%{$keyword}%"];
            $topTitle = '搜索文章:'. $keyword;
            $noResultText = '没有搜索结果';
        }
        $path = "/user/article/?keyword={$keyword}&typeid={$typeid}&page=[PAGE]";
        $result = ArticleModel::field('id,title,rq,typeid')->where($where)->order('id', 'Desc')->paginate($pagesize, false,
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
        $pageInfo = [
            'pagenow' => $page,
            'total' => $result->total(),
            'pagesize' => $pagesize,
        ];
//        print_r(json_encode($pageInfo));exit;
        $mainHtml = $this->view->fetch('', [
            'webTitle' =>  '我的文章',
            'allTypes' => $this->allTypes,
            'typeid' => $typeid,
            'keyword' => $keyword,
            'topTitle' =>  $topTitle,
            'articleList' =>  json_encode($articleList),
            'noResultText' =>  $noResultText,
            'page' =>  $page,
            'pageInfo' =>  $pageInfo,
        ]);
        print_r($mainHtml);
    }

}
