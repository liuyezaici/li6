<?php

namespace app\admin\controller\auth;

use app\admin\model\AuthRule;
use app\common\controller\Backend;
use fast\Tree;
use think\Cache;

/**
 * 规则管理
 *
 * @icon   fa fa-list
 * @remark 规则通常对应一个控制器的方法,同时左侧的菜单栏数据也从规则中体现,通常建议通过控制台进行生成规则节点
 */
class Rule extends Backend
{

    /**
     * @var \app\admin\model\AuthRule
     */
    protected $model = null;
    protected $rulelist = [];
    protected $multiFields = 'ismenu,status';

    public function _initialize()
    {
        parent::_initialize();
        if (!$this->auth->isSuperAdmin()) {
            $this->error(__('Access is allowed only to the super management group'));
        }
        $this->model = model('AuthRule');
        // 必须将结果集转换为数组
        $ruleList = collection($this->model->field('condition,remark,createtime,updatetime', true)->order('weigh DESC,id ASC')->select())->toArray();
        //后台不需要多语言化
//        foreach ($ruleList as $k => &$v) {
//            $v['title'] = __($v['title']);
//        }
        unset($v);
        Tree::instance()->init($ruleList);
        $this->rulelist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');
        $ruledata = [0 => __('None')];
        foreach ($this->rulelist as $k => &$v) {
            if (!$v['ismenu']) {
                continue;
            }
            $ruledata[$v['id']] = $v['title'];
        }
        unset($v);
        $this->view->assign('ruledata', $ruledata);
    }

    /**
     * 旧版列表 无分页
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $list = $this->rulelist;
            $total = count($this->rulelist);

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        $this->view->engine->layout(false);
        print_r($this->view->fetch());
    }


    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a", [], 'strip_tags');
            if ($params) {
                if (!$params['ismenu'] && !$params['pid']) {
                    $this->error(__('The non-menu rule must have parent'));
                }
                $result = $this->model->validate()->save($params);
                if ($result === false) {
                    $this->error($this->model->getError());
                }
                Cache::rm('__menu__');
                $this->success('success');
            }
            $this->error();
        }
        return $this->view->fetch();
    }
    /**
     * 新版弹窗添加
     */
    public function addNew()
    {
        $this->view->engine->layout(false);
        print_r($this->view->fetch('addNew'));
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a", [], 'strip_tags');
            if ($params) {
                if ($params['pid'] && $params['pid'] != $row['pid']) {
                    $childrenIds = Tree::instance()->init(collection(AuthRule::select())->toArray())->getChildrenIds($row['id']);
                    if (in_array($params['pid'], $childrenIds)) {
                        $this->error(__('Can not change the parent to child'));
                    }
                }
                //这里需要针对name做唯一验证
                $ruleValidate = \think\Loader::validate('AuthRule');
                $ruleValidate->rule([
                    'name' => 'require|format|unique:AuthRule,name,' . $row->id,
                ]);
                $result = $row->validate()->save($params);
                if ($result === false) {
                    $this->error($row->getError());
                }
                Cache::rm('__menu__');
                $this->success();
            }
            $this->error();
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 新版全部图标
     */
    public function allIcon() {

        $this->view->engine->layout(false);
        print_r($this->view->fetch('allIcon'));
    }

    /**
     * 新版编辑
     */
    public function editNew($id = null)
    {
        $row = $this->model->get(['id' => $id]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a", [], 'strip_tags');
            if ($params) {
                if ($params['pid'] && $params['pid'] != $row['pid']) {
                    $childrenIds = Tree::instance()->init(collection(AuthRule::select())->toArray())->getChildrenIds($row['id']);
                    if (in_array($params['pid'], $childrenIds)) {
                        $this->error(__('Can not change the parent to child'));
                    }
                }
                //这里需要针对name做唯一验证
                $ruleValidate = \think\Loader::validate('AuthRule');
                $ruleValidate->rule([
                    'name' => 'require|format|unique:AuthRule,name,' . $row->id,
                ]);
                $result = $row->validate()->save($params);
                if ($result === false) {
                    $this->error($row->getError());
                }
                Cache::rm('__menu__');
                $this->success('success');
            }
            $this->error();
        }
        $this->view->engine->layout(false);
        $this->view->assign("row", $row);
        print_r($this->view->fetch('editNew'));
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ? $ids : $this->request->post("ids");
        if ($ids) {
            $delIds = [];
            foreach (explode(',', $ids) as $k => $v) {
                $delIds = array_merge($delIds, Tree::instance()->getChildrenIds($v, true));
            }
            $delIds = array_unique($delIds);
            $count = $this->model->where('id', 'in', $delIds)->delete();
            if ($count) {
                Cache::rm('__menu__');
                $this->success('success');
            }
        }
        $this->error();
    }


    //新版列表
    public function newIndex(){

        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isPost()) {
            $where  = [
                'pid' => 0
            ];
            $pid = input('pid', '', 'int');
            if($pid)  $where['pid'] = $pid;
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 20, 'int');
            if($title = input('title/s', '', 'trim'))  $where['title'] = ['like', "%{$title}%"];
            if($url = input('url/s', '', 'trim'))  $where['name'] = ['like', "%{$url}%"];

            $total = AuthRule::where($where)->count();
            $list = AuthRule::field('id,pid,name,title,icon,ismenu,weigh,status')->where($where)
                ->page($page, $pageSize)
                ->select();
            foreach ($list as $n =>&$v) {
                $v['total'] = AuthRule::where('pid', $v['id'])->count();
                $v['parentTitle'] = '';
                if($v['pid']) {
                    $v['parentTitle'] = AuthRule::getfieldbyid($v['pid'], 'title') .'->';
                }
            }
            unset($v);
            return json_output($total, $list, $pageSize, $page);
        }
        $this->view->engine->layout(false);
        print_r($this->fetch()) ;
    }
}
