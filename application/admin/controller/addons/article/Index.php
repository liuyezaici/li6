<?php

namespace app\admin\controller\addons\pic;

use app\admin\controller\addons\pic\model\Article;
use app\common\controller\Backend;
use fast\Date;
use fast\DiyAddon;
/**
 *  首页
 * @internal
 */
class Index extends Backend
{
    protected static $thisAddonName = 'pic';
    public function _initialize()
    {
        parent::_initialize();
    }


    //删除文章
    public function del($id=NULL){
        $info = Article::get($id);
        if(!$info) $this->error('id no exist');
        Article::where('id', $id)->delete();
        $this->success('success');
    }

    //编辑文章
    public function edit($id=NULL){
        $infoModel = Article::get($id);
        if(!$infoModel) $this->error('id no exist');
        $info = $infoModel->toArray();
        if($this->request->isPost()) {
            $editData = input('row/a');
            $name = $editData['name'];
            if(!$name) $this->error('empty name!');
            $editData['id'] = $id;
//            print_r($editData);exit;
            $infoModel->save($editData);
            $this->success('success');
        }
        $this->view->engine->layout(false);
        print_r($this->fetch(DiyAddon::getViewPath(self::$thisAddonName, 'edit'), $info)) ;
    }


    // 列表
    public function index(){
//        print_r($allTypesTitle);exit;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isPost()) {
            $where  = [];
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 20, 'int');
            if($title = input('title/s', '', 'trim'))  $where['title'] = ['like', "%{$title}%"];
            $total = Article::where($where)->count();
            $list = Article::field('id,name')->where($where)
                ->order('id', 'desc')
                ->page($page, $pageSize)
                ->select();
            unset($v);
            return json_output($total, $list, $pageSize, 1);
        }
        $this->view->engine->layout(false);
        print_r($this->fetch(DiyAddon::getViewPath(self::$thisAddonName, 'index'), [])) ;
    }
}
