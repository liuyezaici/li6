<?php

namespace app\admin\controller\auth;

use app\common\controller\Backend;
use fast\Tree;
use think\Cache;

/**
 * 规则管理
 *
 * @icon fa fa-list
 * @remark 规则通常对应一个控制器的方法,同时左侧的菜单栏数据也从规则中体现,通常建议通过控制台进行生成规则节点
 */
class Rule extends Backend
{

    protected $model = null;
    protected $rulelist = [];
    protected $multiFields = 'ismenu,status';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('AuthRule');
    }


    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a", [], 'strip_tags');
            if ($params)
            {
                $result = $this->model->validate()->save($params);
                if ($result === FALSE)
                {
                    $this->error($this->model->getError());
                }
                $this->success();
            }
            $this->error();
        }
       print_r($this->view->fetch());
    }

    /**
     * 编辑
     */
    public function edit($id = NULL)
    {
        $row = $this->model->get(['id' => $id]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a", [], 'strip_tags');
            if ($params)
            {
                //这里需要针对 auth_path 做唯一验证
                if(isset($params['auth_path'])) {
                    $ruleValidate = \think\Loader::validate('AuthRule');
                    $ruleValidate->rule([
                        'auth_path' => 'require|format|unique:AuthRule,auth_path,' . $row->id,
                    ]);
                    $row->validate();
                }
                $result = $row->save($params);
                if ($result === FALSE)
                {
                    $this->error($row->getError());
                }
                $this->success();
            }
            $this->error();
        }
        $this->view->assign("row", $row);
       print_r($this->view->fetch());
    }

    /**
     * 删除
     */
    public function del($id = "")
    {
        if ($id)
        {
            $count = $this->model->where('id', 'in', $id)->delete();
            if ($count)
            {
                $this->success();
            } else {
                $this->error('no_count');
            }
        }
        $this->error('no_id');
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isPost())
        {
            list($whereMore, $sort, $order) = $this->buildparams();
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 20, 'int');
            $where  = [];
            $where['ismenu'] = 1;
            if($userName = input('title'))  $where['title'] = ['like', "%". trim($userName) ."%"];
            if($whereMore) $where = array_merge($where, $whereMore);
            $total = $this->model->where($where)->count();
            $listResult = $this->model
                ->where($where)
                ->order('order', 'desc')
                ->page($page, $pageSize)
                ->select();
//            print_r($this->model->getlastsql());exit;
//            print_r(json_encode($listResult));exit;

            $result = array("total" => $total, "page" => $page, "rows" => $listResult);

            return json($result);
        }
       print_r($this->view->fetch());
    }
}
