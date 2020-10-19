<?php

namespace app\admin\addon\sms\controller;

use app\common\controller\Backend;
use fast\Addon;

/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->addonName = 'sms';
        $this->model = Addon::getModel('sms');
        if(!$this->model) {
            $this->error('未安装短信组件');
        }
    }

    //短信列表
    public function index()
    {
        if ($this->request->isPost())
        {
            list($whereMore, $sort, $order) = $this->buildparams();
            $where  = [];
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');
            if($mobile = input('mobile/s')) {
                $where['mobile'] = $mobile;
            }
            if($whereMore) $where = array_merge($where, $whereMore);
//            print_r(($where));exit;
            $total = $this->model
                ->where($where)
                ->count();
            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();
            foreach ($list as $n =>&$v) {
                $v['statusName'] = $this->model->getStatusName($v['status']);
                unset($v);
            }
//            echo $this->model->getlastsql();exit;
            return json_output($total, $list);
        }
        print_r($this->view->fetch());
    }
}
