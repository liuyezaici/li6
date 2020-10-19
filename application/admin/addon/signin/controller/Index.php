<?php
namespace app\admin\addon\signin\controller;
use app\common\controller\Backend;
use fast\Addon;
use think\Db;


/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->addonName = 'signin';
        $this->model = Addon::getModel($this->addonName);
    }
    //签到记录
    public function index()
    {
        if ($this->request->isPost()){
            list($whereMore, $sort, $order) = $this->buildparams();
            $map = [];
            $filter = json_decode($this->request->param('filter'));
            if(isset($filter->uid)) $map['user_id'] = $filter->uid;
            if(isset($filter->username)) {
                $uid = model('users')->getfieldbyusername($filter->username, 'id');
                if($uid) $map['user_id'] = $uid;
            }
            //不能加where条件，因为where里的多余条件无法剔除 如：username=>xxx。
            $total = $this->model->where($map)->count();
            $list = $this->model->where($map)
                ->limit($offset,$limit)
                ->order($sort,$order)
                ->select();
            $userClass = model('users');
            foreach ($list as $k => $v)
            {
                $list[$k]['username'] = $userClass->getUserName($v['cuid']);
            }

            return json_output($total, $list);

        }
       print_r($this->view->fetch());
    }

}
