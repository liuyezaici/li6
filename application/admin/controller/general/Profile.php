<?php

namespace app\admin\controller\general;

use app\admin\model\Admin;
use app\common\controller\Backend;
use fast\Random;
use think\Session;
use think\Validate;

/**
 * 个人配置
 *
 * @icon fa fa-user
 */
class Profile extends Backend
{

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isPost()) {
            $this->model = model('AdminLog');
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->where($where)
                ->where('admin_id', $this->auth->id)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        $this->view->engine->layout(false);
        $isAdmin = $this->auth->id==1;
        print_r($this->view->fetch('', ['isAdmin' => $isAdmin]));
    }

    /**
     * 更新个人信息
     */
    public function update()
    {
        $isAdmin = $this->auth->id==1;
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a");
            $params = array_filter(array_intersect_key(
                $params,
                array_flip(array('email', 'nickname', 'password', 'avatar'))
            ));
            unset($v);

            $editPwd = false;
            if (isset($params['password'])) {
                if (!Validate::is($params['password'], "/^[\S]{6,16}$/")) {
                    $this->error(__("Please input correct password"));
                }
                $params['salt'] = Random::alnum();
                $editPwd = base64_encode(hash("sha256", $params['password'],true));
                $params['password'] = $editPwd;
//                $params['password'] = md5(md5($params['password']) . $params['salt']);
            }
            if($isAdmin) {
                if (!Validate::is($params['email'], "email")) {
                    $this->error(__("Please input correct email"));
                }
                $exist = Admin::where('email', $params['email'])->where('id', '<>', $this->auth->id)->find();
                if ($exist) {
                    $this->error(__("Email already exists"));
                }
            } else {
                unset($params['email']);
            }
            if ($params) {
                $admin = Admin::get($this->auth->id);
                $admin->save($params);
                //因为个人资料面板读取的Session显示，修改自己资料后同时更新Session
                Session::set("admin", $admin->toArray());
                //修改旧库user的密码
                if($editPwd) {
                    \think\Db::table('user')->where('id', $this->auth->id)->update([
                        'userPwd' => $editPwd
                    ]);
                }
                $this->success();
            }
            $this->error();
        }
        return;
    }
}
