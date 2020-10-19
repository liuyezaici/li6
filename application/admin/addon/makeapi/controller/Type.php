<?php
namespace app\admin\addon\makeapi\controller;

use app\common\controller\Backend;


use think\Config;
use think\Hook;
use think\Validate;
use fast\Addon;

/**
 * 加盟
 * Class Type
 * @package app\admin\addon\makeapi\controller
 */
class Type extends Backend{


    public function _initialize()
    {
        parent::_initialize();
        $this->addonName = 'makeapi';
        $this->model = Addon::getModel('makeapi','Makeapitype');
    }

    //首页列表
    public function index()
    {

        if ($this->request->isPost()) {
            $result = parent::index(false, false, true);
            return json($result);
        }
       print_r($this->view->fetch());
    }

    //添加
    public function add()
    {

        //需要过滤的字段
        $filter = ['id','user_id','join_type','name','mobile','content'];

        if($this->request->post()){

            $post = input('post.row/a');

            $newData = array_col2row($post['config']);
            $data['ext_field'] = $newData;
            $data['title'] = $post['title'];
            $data['ctime'] = time();
            $data['keyname']  = $post['keyname'];

            $res = $this->model->save($data);
            if($res){
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }

        }
       print_r($this->view->fetch());
    }

    //编辑
    public function edit($ids = NULL)
    {
        if(!$ids) $this->error('缺少ids');

        if ($ids) {
            $row = $this->model->where('id', $ids)->find();
            $this->assign('row',$row);
        }

        if ($this->request->post()) {
            $post = input('post.row/a');

            if ($this->model->where(['title' => $post['title'], 'id' => ['neq', $ids]])->find()) $this->error('名称已存在');
            $newConfig = array_col2row($post['config']);


            //更新数组
            $newData = [
                'title' =>$post['title'],
                'ext_field' =>$newConfig,
                'keyname'   =>$post['keyname'],
            ];

            return parent::edit($ids, $newData);
        }

       print_r($this->view->fetch());
    }


    //删除成功
    public function del($ids = "")
    {
        if(!$ids) $this->error('缺少ids');
        $row = $this->model->where('id',$ids)->find();
        if(!$row){
            $this->success('记录不存在');
        }else{
            $res =$this->model->where('id',$ids)->delete();
            $res ? $this->success('删除成功') : $this->error('删除失败');
        }
    }
}