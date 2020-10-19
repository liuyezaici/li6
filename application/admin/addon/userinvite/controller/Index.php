<?php
namespace app\admin\addon\userinvite\controller;
use app\common\controller\Backend;

use think\Db;
use fast\Addon;
/**
 * 用户邀请记录
 * @internal
 */
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->addonName = 'userinvite'; //model名
		$this->model =  Addon::getModel('userinvite');
     
    }
    //添加
    public function add(){
        if ($this->request->isPost()){
            //前置操作
            $params = $this->request->post("row/a");
            if(!$params['uid']) $this->error('必须填写uid');
            $result = $this->model->createInviteLog($params['uid']);//调用父类
            if(!is_array($result)){
                //后续操作
                $this->success();
            }else{
                $this->error($result[0]);
            }
        }
        return parent::add();
    }

    public function index()
    {
        if ($this->request->isPost()){
            list($whereMore, $sort, $order) = $this->buildparams();
            $map = [];
            if(input('title'))$map['title'] = ['like', input('title')];
            $total = $this->model->where($map)->where($where)->count();
            $list = $this->model->where($where)
                ->where($map)
                ->limit($offset,$limit)
                ->order($sort,$order)
                ->select();
            foreach ($list as $n =>&$v) {
                $v['main_username'] =  db('users')->getfieldbyid($v['main_uid'], 'username');
            }
            return json_output($total, $list);

        }
       print_r($this->view->fetch());
    }

   
}


