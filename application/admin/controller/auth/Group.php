<?php

namespace app\admin\controller\auth;

use app\common\model\Users;
use app\common\controller\Backend;
use fast\Str;

/**
 * 角色组
 *
 * @icon fa fa-group
 * @remark 角色组可以有多个,角色有上下级层级关系,如果子角色有角色组和管理员的权限则可以派生属于自己组别下级的角色组或管理员
 */
class Group extends Backend
{

    protected $model = null;
    //无需要权限判断的方法
    protected $noNeedRight = ['roletree'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('AuthGroup');
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds(true);

    }


    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a", [], 'strip_tags');
            if($params['pid']) {
                if (!in_array($params['pid'], $this->childrenGroupIds))
                {
//                    print_r($this->childrenGroupIds);
                    $this->error('所选父级不在您的权限范围');
                }
                $parentmodel = $this->model->get($params['pid']);
                if (!$parentmodel)
                {
                    $this->error(__('The parent group can not found'));
                }
                // 父级别的规则节点
                $parentrules = explode(',', $parentmodel->rules);
                // 当前组别的规则节点
                $currentrules = $this->auth->getRuleIds();
                if($params['rules']) {
                    $rules = $params['rules'];
                    // 如果父组不是超级管理员则需要过滤规则节点,不能超过父组别的权限
                    $rules = in_array('*', $parentrules) ? $rules : array_intersect($parentrules, $rules);
                    // 如果当前组别不是超级管理员则需要过滤规则节点,不能超当前组别的权限
                    $rules = in_array('*', $currentrules) ? $rules : array_intersect($currentrules, $rules);
                    $params['rules'] = implode(',', $rules);
                }
            }

            if ($params)
            {
                $this->model->create($params);
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
            if(isset($params['rules'])) {
                $rules = $params['rules'];
                if(!is_array($rules)) $rules = explode(',', $rules);
                $params['rules'] = $rules;
            }
//            print_r($rules);exit;
            if(isset($params['rules'])) {
                // 当前组别的规则节点
                $currentrules = $this->auth->getRuleIds();

                // 如果当前组别不是超级管理员则需要过滤规则节点,不能超当前组别的权限
                $rules = $currentrules == '*' ? $rules : array_intersect($currentrules, $rules);
//                print_r($rules);exit;
                $params['rules'] = implode(',', $rules);
            }
            if ($params)
            {
                $row->save($params);
                $this->success();
            }
            $this->error();
            return;
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
            $idArray = explode(',', $id);
            $grouplist = $this->auth->getMyGroupId();
            $group_ids = array_map(function($group) {
                return $group['id'];
            }, $grouplist);
            // 移除掉当前管理员所在组别
            $idArray = array_diff($idArray, $group_ids);
            if (!$idArray)
            {
                $this->error('你不能删除含有子组和管理员的组');
            }
            //判断当前组还有没有管理员
            if(Users::getbygroupid(join(',', $idArray))) {
                $this->error('当前组下还有管理员，无法删除');
            }
            $count = $this->model->where('id', 'in', $idArray)->delete();
            if ($count)
            {
                $this->success();
            }
        }
        $this->error('no id');
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
            $pageSize = input('page_size', 10, 'int');
            $where  = [];
            if($userId = input('id/s'))  $where['id'] = trim($userId);
            if($userName = input('title'))  $where['title'] = ['like', "%". trim($userName) ."%"];
            if(!$this->auth->isSuperAdmin()) {
                $where['id'] = ['in', join(',', $this->childrenGroupIds)];
            }
            if($whereMore) $where = array_merge($where, $whereMore);
//            print_r($wherePost);exit;
            $total = $this->model->where($where)->count();
            $listResult = $this->model
                ->where($where)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();
//            print_r($this->model->getlastsql());exit;
//            print_r(json_encode($listResult));exit;
            foreach ($listResult as $k => &$v)
            {
                $v['title_parent'] = ($v['pid'] ? $this->model->getfieldbyid($v['pid'], 'title') : '-');
                $v['status_name'] = $this->model->getAdminGroupStatusName($v['status']);
                $v['type_name'] = $this->auth->getAdminTypeName($v['utype']);
                unset($v);
            }
            return json_output($total, $listResult);
        }
        $allPower = $this->auth->getMyRules();
        $allPower = str::diguiArray($allPower, 0, 'child', 'pid', 'id');
//      print_r(json_encode($allPower));exit;
        $this->view->assign('allPower', json_encode($allPower));
        $this->view->assign('allStatus', json_encode($this->model->getAdminGroupAllStatusForRadio()));
        $this->view->assign('allTypes', json_encode($this->auth->getAdminAllTypesForRadio()));
        print_r($this->view->fetch());
    }

}
