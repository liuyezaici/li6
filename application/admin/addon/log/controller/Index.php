<?php
namespace app\admin\addon\log\controller;
use app\common\controller\Backend;

use think\Db;
use fast\Addon;
use app\admin\addon\log\model\Log as LogModel;
/**
 * 日志管理
 * @internal
 */
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->addonName = 'log';
        $this->model = Addon::getModel($this->addonName);
    }


    //预览json信息
    public function get($id='') {
        $row = LogModel::get($id);
        if (!$row) $this->error(__('No Results were found'));
        $jsonArray = json_decode($row['text'], true);
        if(is_array($jsonArray)) {
            $row['text'] = print_r($jsonArray, true);
        }
        $this->result($row, 1);
    }

    //日志列表
    public function index()
    {
        if ($this->request->isPost())
        {
//            LogModel::addLog([
//                'a' => 12133,
//                'ab' => 12133,
//                'abc' => '的件大事的',
//            ]);
            list($whereMore, $sort, $order) = $this->buildparams();
            $where  = [];
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');
            if($id = input('id/d'))  $where['id'] = $id;
//          print_r(json_encode($where));exit;
            if($whereMore) $where = array_merge($where, $whereMore);
            $total = $this->model
                ->where($where)
                ->count();
            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();
//            echo $this->model->getlastsql();exit;
            return json_output($total, $list);
        }
        print_r($this->view->fetch());
    }
}
