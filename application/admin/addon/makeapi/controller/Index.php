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
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = Addon::getModel('makeapi');
    }

    // 首页列表
    public function index()
    {
        if ($this->request->isPost())
        {
            $result = parent::index(false, false, true);
            return json($result);
        }
       print_r($this->view->fetch());
    }

    // 添加
    public function add()
    {
        if($this->request->post())
        {
            $data = input('post.row/a');

            $data['apis'] = array_col2row($data['apis']);

            $res = $this->model->save($data);

            if($res)
            {
                $this->success('添加成功');
            }
            else
            {
                $this->error('添加失败');
            }

        }
       print_r($this->view->fetch());
    }

    // 编辑
    public function edit($ids = NULL)
    {
        if(!$ids) $this->error('缺少ids');

        if($ids)
        {
            $row = $this->model->where('id', $ids)->find();

            $apis = $row['apis'];
            foreach ($apis as $key => $api)
            {
                $apis[$key]['params'] = htmlspecialchars($api['params']);
            }
            $row['apis'] = $apis;

            $this->assign('row', $row);
        }

        if($this->request->post())
        {
            $post = input('post.row/a');

            if ($this->model->where(['keyname' => $post['keyname'], 'id' => ['neq', $ids]])->find()) $this->error('索引名已存在');

            // 更新数组
            $newData = [
                'keyname' => $post['keyname'],
                'remark'  => $post['remark'],
                'apis'    => array_col2row($post['apis']),
            ];

            return parent::edit($ids, $newData);
        }

       print_r($this->view->fetch());
    }

    // 删除成功
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