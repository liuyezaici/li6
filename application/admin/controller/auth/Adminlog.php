<?php

namespace app\admin\controller\auth;

use app\admin\model\AuthGroup;
use app\common\controller\Backend;

/**
 * 管理员日志
 *
 * @icon fa fa-users
 * @remark 管理员可以查看自己所拥有的权限的管理员日志
 */
class Adminlog extends Backend
{

    protected $model = null;
    protected $childrenAdminIds = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('AdminLog');

        $this->childrenAdminIds = $this->auth->getChildrenAdminIds(true);

    }


    /**
     * 详情
     */
    public function detail($ids)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        $this->view->assign("row", $row->toArray());
       print_r($this->view->fetch());
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids)
        {
            $childrenGroupIds = $this->childrenGroupIds;
            $adminList = $this->model->where('id', 'in', $ids)->where('admin_id', 'in', $childrenGroupIds)->select();
            if ($adminList)
            {
                $deleteIds = [];
                foreach ($adminList as $k => $v)
                {
                    $deleteIds[] = $v->id;
                }
                if ($deleteIds)
                {
                    $this->model->destroy($deleteIds);
                    $this->success();
                }
            }
        }
        $this->error();
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isPost())
        {
            list($whereMore, $sort, $order) = $this->buildparams();
            $where  = [];
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');
            if($id = input('id/d'))  $where['id'] = $id;
            if($username = input('username/s'))  $where['username'] = $username;
//            print_r(json_encode($whereMore));exit;
            if($whereMore) $where = array_merge($where, $whereMore);
            $total = $this->model
                ->where($where)
                ->where('admin_id', 'in', $this->childrenAdminIds)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where('admin_id', 'in', $this->childrenAdminIds)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();
            $result = array("total" => $total, "page_size" => $pageSize, "page" => $page, "rows" => $list);
            return json($result);
        }
       print_r($this->view->fetch());
    }
}
