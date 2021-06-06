<?php
namespace app\my\controller;

use app\common\controller\Backend;
use app\admin\controller\addons\article\model\Article AS articleModel;
use app\admin\controller\addons\article\model\Types;
use app\admin\controller\addons\fujian\model\Fujian;
use fast\Date;
use fast\Str;
use think\Db;

class Article extends Backend
{
    protected $layout = true;
    protected $noNeedLogin = [''];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
    }

    //编辑详情
    public function edit($id=NULL){
        $info = ArticleModel::getbyid($id);
        $myUid = $this->auth->id;
        if($myUid != $info['cuid']) $this->error('身份已经切换:'.$myUid . '!='. $info['cuid']);
        if($this->request->isPost()) {
            $title = input('post.title','', 'trim');
            $editorType = input('post.editorType','', 'trim');
            $content = input('post.content','', 'trim');
            $typeId = input('post.typeid',0, 'intval');
            if(!$typeId) $this->error('请选择分类');
            if(!$title) $this->error('请输入标题');
            if(!$editorType) $this->error('未选择编辑器类型');
            if(!$content) $this->error('请输入内容');
            $rows = [
                'title' => $title,
                'content' => $content,
                'typeid' => $typeId,
                'editorType' => $editorType,
            ];
            ArticleModel::where('id', $id)->update($rows);
            $this->success('编辑成功');
        }
        $allTypeList = Types::field('id,title')->select();
        $mainHtml = $this->view->fetch('modify', [
            'topTitle' => '编辑文章:'.$id,
            'id' =>  $id,
            'row' =>  $info,
            'allTypeList' =>  $allTypeList,
            'submitUrl' =>  '/my/article/edit/id/'.$id,
        ]);
        print_r($mainHtml);
    }

    //写一篇
    public function add() {
        $myUid = $this->auth->id;
        if($this->request->isPost()) {
            $title = input('post.title','', 'trim');
            $editorType = input('post.editorType','', 'trim');
            $content = input('post.content','', 'trim');
            $typeId = input('post.typeid',0, 'intval');
            if(!$typeId) $this->error('请选择分类');
            if(!$title) $this->error('请输入标题');
            if(!$editorType) $this->error('未选择编辑器类型');
            if(!$content) $this->error('请输入内容');
            $rows = [
                'title' => $title,
                'content' => $content,
                'typeid' => $typeId,
                'editorType' => $editorType,
            ];
            $rows['cuid'] = $myUid;
            $rows['ctime'] = Date::toYMDS();
            $sid = articleModel::insertGetId($rows);
            $this->success('发布成功');
        }
        $rows = [
            'topTitle' => '写一篇',
            'id' =>  0,
            'row' =>  [
                'typeid' => 0,
                'title' => '',
                'editorType' => '',
                'content' => '',
            ],
            'allTypeList' =>  [],
            'submitUrl' =>  '/my/article/add/'
        ];
        print_r($this->view->fetch('modify', $rows));
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



    //获取详情
    public function get($id=NULL) {
        $info = ArticleModel::getbyid($id);
        $info['content'] = html_entity_decode($info['content']);
        $this->success('获取成功', '', $info);
    }





    //列表
    public function index() {
        $this->request->filter(['strip_tags', 'trim']);
        $where  = [
        ];
        $page = input('page', 1, 'int');
        $typeid = input('typeid', 0, 'intval');
        $pageSize = input('page_size', 20, 'int');
        $pathArray = [
            'page' => $page,
            'page_size' => $pageSize,
            'typeid' => $typeid,
        ];
        if($typeid) {
            $where['typeid'] = $typeid;
        }
       // id,typeid,title,cuid,ctime,rq,fileids,content,status
        if($title = input('title/s', '', 'trim'))  $where['title'] = ['like', "%{$title}%"];
        $total = articleModel::where($where)->count();
        $result = articleModel::field('id,title,typeid,ctime')->where($where)
            ->order('id', 'desc')
            ->paginate($pageSize,false, [
                'page' => $page,
                'type'      => 'page\Pagetyle1',
                'query' => $pathArray,
            ]);
        $total = $result->total();
        $menu = $result->render();
        $list = $result->items();
        foreach ($list as &$v) {
            $v['typeName'] = types::getTypeTitle($v['typeid'], '-');
        }
        unset($v);
        $this->view->engine->layout(false);
        print_r($this->fetch('', [
            'total' => $total,
            'list' => $list,
            'menu' => $menu,
            'typeid' => $typeid,
            'pageSize' => $pageSize,
            'title' => $title,
        ]));
    }
}
