<?php

namespace app\admin\addon\feedback\controller;

use app\common\controller\Backend;
use think\Config;
use think\Hook;
use think\Validate;
use think\Db;
use fast\Addon;
use app\admin\addon\feedback\model\Feedback as FeedbackModel;
/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->addonName ='feedback';
        $this->model = Addon::getModel($this->addonName);
    }

    //查看详情
    public function get($id='') {
        $row = FeedbackModel::get($id);
        if (!$row) $this->error(__('No Results were found'));
        $this->result($row, 1);
    }
    //修改状态
    public function setStatus($id=0) {
        if ($id)
        {
            $newStatus = $this->request->post('newStatus');
            $count = $this->model->where('id', $id)->update([
                'status' => $newStatus
            ]);
            if ($count)
            {
                $this->success();
            } else {
                $this->error('no_count');
            }
        }
        $this->error('no_id');
    }
////    问题反馈列表
    public function index(){
        if ($this->request->isPost()){
            list($whereMore, $sort, $order) = $this->buildparams();
            $where  = [];
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');
            if($username = input('username/s'))  $where['username'] = $username;
            if($tel = input('tel/s'))  $where['tel'] = trim($tel);
//            print_r(json_encode($where));exit;
            if($whereMore) $where = array_merge($where, $whereMore);

            $total = FeedbackModel::where($where)->count();
            $list = FeedbackModel::where($where)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();

            foreach ($list as $n =>&$v) {
                $v['status_name'] =  FeedbackModel::getStatusName($v['status'], '<br />');
                unset($v);
            }

            return json_output($total, $list);

        }
        return parent::index();
    }

}
