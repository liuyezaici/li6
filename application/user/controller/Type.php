<?php

namespace app\user\controller;

use app\common\controller\Backend;
use fast\Date;
use \app\admin\addon\article\model\ArticleTypes;

class Type extends Backend
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




    //编辑文章分类
    final function del_type() {
        $id = input('id', 0, 'int');
        //id参数是否为空
        if( !$id ){
            $this->error('no id');
        }
        ArticleTypes::where('id', $id)->delete();
        $this->error('删除成功');
    }


    //编辑文章分类
    final function edit_type() {
        $id = input('id', 0, 'int');
        //id参数是否为空
        if( !$id ){
            $this->error('no id');
        }
        $classInfo = ArticleTypes::where('id', $id)->find();
        if(!$classInfo) $this->error('分类不存在');
        if($this->request->isPost()) {
            $title = input('title', '', 'trim');
            if(!$title) $this->error('no title');
            if(ArticleTypes::where(['title' => $title, 'id'=>['<>', $id]])->find()) $this->error('分类已经存在');
            $editData = [
                'title' => $title,
            ];
            ArticleTypes::where('id', $id)->update($editData);
            $this->success('添加成功');
        }
        $mainHtml = $this->view->fetch('', json_decode(json_encode($classInfo), true));
        print_r($mainHtml);
    }

    //添加分类
    public function addApi() {
        $title = input('post.title', '', 'trim');
        if(!$title) {
            $this->error('请输入分类名');
        }
        $title = input('title', '', 'trim');
        if(!$title) $this->error('no title');
        if(ArticleTypes::where(['title' => $title])->find()) $this->error('分类已经存在');
        $newData = [
            'title' => $title,
            'addtime' => Date::toYMDS(time()),
        ];
        ArticleTypes::insert($newData);
        $this->success('添加成功');
    }
    //添加分类
    public function add() {
        $list = ArticleTypes::field('id,title')->select();
        print_r($this->fetch('', ['allTypes' => $list]));
    }
    //添加分类
    public function getMyTypes() {
        $id1 = input('id', 0, 'intval');
        $list = ArticleTypes::field('id,title')->select();
        foreach ($list as $n => &$v) {
            if($id1 && $v['id'] == $id1) {
                $v['selected'] = 'selected';
            } else {
                $v['selected'] = '';
            }
        }
        unset($v);
        print_r($this->fetch('get_my_types', ['allTypes' => $list, 'id1' => $id1]));
    }

}
