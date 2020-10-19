<?php

namespace app\admin\controller\auth;

use app\admin\model\AuthGroup;
use app\common\model\Users as userModel;
use app\common\controller\Backend;
use fast\Random;
use fast\Addon;
use think\Config;
use fast\Tree;
use think\Db;
use think\Exception;

/**
 * 管理员管理
 *
 * @icon fa fa-users
 * @remark 一个管理员可以有多个角色组,左侧的菜单根据管理员所拥有的权限进行生成
 */
class Admin extends Backend
{

    protected $model = null;
    protected $childrenGroupIds = [];
    protected $noNeedRight = ['get_my_info', 'edit_my_info']; //自己的信息不需要分配权限

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new userModel();

        $this->childrenGroupIds = $this->auth->getChildrenGroupIds();
//
//        print_r('userModel');
//        print_r($this->model);
//        exit;
        $groupList = collection(AuthGroup::where('id', 'in', $this->childrenGroupIds)->select())->toArray();
//        print_r('$groupList');
//        print_r($groupList);
//        exit;
        Tree::instance()->init($groupList);
        $groupdata = [];
        $result = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');
//        print_r('$result');
//        print_r($result);
//        exit;
        foreach ($result as $k => $v)
        {
            $groupdata[$v['id']] = $v['title'];
        }

        $this->view->assign('groupdata', $groupdata);
        $this->assignconfig("admin", ['id' => $this->auth->id]);
    }

    //搜索管理/用户 只能输出3个字段 id,username,nickname
    public function searchusername() {
        $username = input('post.username');
        if(!$username) $this->error('$username不能为空');
        $userList = userModel::where([
            'username' => ['like', '%'. $username .'%']
        ])->whereOr([
            'nickname' => ['like', '%'. $username .'%']
        ])->field('id,username,nickname')->limit(10)->select();
        return json(['code'=>1, 'rows' => $userList]);
    }

    //获取个人信息
    public function get_my_info() {
        $row = $this->model->get($this->auth->id);
        unset($row['password']);
        unset($row['salt']);
        $this->result($row);
    }
    //修改个人信息
    public function edit_my_info() {
        $id = $this->auth->id;
        $row = userModel::get(['id' => $id]);
        if (!$row) $this->error('找不到记录:'. $id);
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            $avatar = $params['avatar'];//头像单独获取
            if ($params)
            {
                $params['avatar'] = $avatar;
                if ($params['password'])
                {
                    $params['salt'] = Random::alnum();
                    $params['password'] = userModel::encryptPassword($params['password'], $params['salt']);
                }
                else
                {
                    unset($params['password'], $params['salt']);
                }
                Db::startTrans();
                try {
                    //这里需要针对username和email做唯一验证
                    $adminValidate = \think\Loader::validate('Users');
                    $adminValidate->rule([
                        'username' => 'max:50|unique:Users,username,' . $row->id,
                        'email'    => 'email|unique:Users,email,' . $row->id
                    ]);
                    $result = $row->validate('Users.edit')->save($params);
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    return $this->error($e->getMessage());
                }
                if ($result === false)
                {
                    $this->error($row->getError());
                }
                //附件更新sid
                if($avatar) {
                    $fujianModel = Addon::getModel('fujian');
                    if(!$fujianModel) $this->error('未安装fujian组件');
                    $fujianModel->updateSid($id, [$avatar]);
                }
                $this->success();
            }
            $this->error('未提交参数');
        }
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                if(!$params['username']) $this->error('username不能为空');
                if(!$params['password']) $this->error('密码不能为空');
                if(!$params['groupid']) $this->error('分组不能为空');
                //获取分组所在的utype
                $utype = AuthGroup::getfieldbyid($params['groupid'], 'utype');
                $params['utype'] = $utype;
                $newUid = userModel::createAdmin($params);
                if(!is_numeric($newUid))  $this->error('创建失败：'. $newUid);
                $this->success();
            }
            $this->error('no params');
        }
    }

    /**
     * 编辑
     */
    public function edit($id = NULL)
    {
        $row = userModel::get(['id' => $id]);
        if (!$row) $this->error('找不到记录:'. $id);
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                if ($params['password'])
                {
                    $params['salt'] = Random::alnum();
                    $params['password'] = userModel::encryptPassword($params['password'], $params['salt']);
                }
                else
                {
                    unset($params['password'], $params['salt']);
                }

                if(!$params['groupid']) $this->error('分组不能为空');
                if(!$params['status'] || userModel::isWrongStatus($params['status'])) {
                    $params['status'] = userModel::getAdminDefaultStatus();
                };
                Db::startTrans();
                try {
                    //这里需要针对username和email做唯一验证
                    $adminValidate = \think\Loader::validate('Users');
                    $adminValidate->rule([
                        'username' => 'require|max:50|unique:admin,username,' . $row->id,
                        'email'    => 'email|unique:admin,email,' . $row->id
                    ]);
                    $result = $row->validate('Admin.edit')->save($params);
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    return $this->error($e->getMessage());
                }
                if ($result === false)
                {
                    $this->error($row->getError());
                }
                $this->success();
            }
            $this->error('未提交参数');
        }
    }

    /**
     * 删除
     */
    public function del($id = "")
    {
        if ($id)
        {
            // 避免越权删除管理员
            $childrenGroupIds = $this->childrenGroupIds;
            $adminList = $this->model
                ->where('id', 'in', $id)
                ->where('groupid', 'in', $childrenGroupIds)
                ->field('id')->select();

            if ($adminList)
            {
                $deleteIds = [];
                foreach ($adminList as $k => $v)
                {
                    if(Config::get('adminid') == $v->id) {
                        $this->error('不能删除系统管理员');
                    }
                    $deleteIds[] = $v->id;
                }
                $deleteIds = array_diff($deleteIds, [$this->auth->id]);
                if ($deleteIds)
                {
                    $this->model->destroy($deleteIds);
                    $this->success();
                }
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
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('get_select'))
            {
                $this->dataLimit = 'auth';
                $this->dataLimitField = 'id';
                return parent::selectpage();
            }
            list($whereMore, $sort, $order) = $this->buildparams();
            $where  = [];
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');
            if($id = input('id/d'))  $where['id'] = $id;
            if($username = input('username/s'))  $where['username'] = trim($username);
            if($usernick = input('nickname/s'))  $where['nickname'] = trim($usernick);

            //除非超级管理员 否则只能查看自己分组下的管理员
            if(!$this->auth->isSuperAdmin()) {
                $where['groupid'] = ['in', $this->childrenGroupIds];
            }
//            print_r(json_encode($where));exit;
//            if($whereMore) $where = array_merge($where, $whereMore);
            $total = $this->model
                ->where($where)
                ->where('utype', $this->auth->getIdentNormalAdmin())
                ->count();
//            print_r($this->model->getlastsql());

            $list = $this->model
                ->where($where)
                ->where('utype', $this->auth->getIdentNormalAdmin())
                ->field(['password', 'salt', 'token'], true)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();
            foreach ($list as $k => &$v)
            {
                if($v['groupid']) {
                    $v['group_name'] = AuthGroup::getfieldbyid($v['groupid'], 'title');
                } else {
                    $v['group_name'] = $v['groupid'];
                }
                $v['status_name'] = userModel::getAdminStatusName($v['status']);

            }
            unset($v);
            $result = array("total" => $total, "page_size" => $pageSize, "page" => $page, "rows" => $list);

            return json($result);
        }
        $this->view->assign('allStatus', json_encode(userModel::getAdminAllStatusForRadio()));
       print_r($this->view->fetch());
    }
}
