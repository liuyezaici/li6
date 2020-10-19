<?php

namespace app\admin\controller\general;

use app\common\model\Users as userModel;
use app\common\controller\Backend;
use fast\Random;
use think\Session;

/**
 * 个人配置
 *
 * @icon fa fa-user
 */
class Profile extends Backend
{


    /**
     * 更新个人信息
     */
    public function update()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            $params = array_filter(array_intersect_key($params, array_flip(array('email', 'nickname', 'password', 'avatar'))));
            unset($v);
            if (isset($params['password']))
            {
                $params['salt'] = Random::alnum();
                $params['password'] = userModel::encryptPassword($params['password'], $params['salt']);
            }
            if ($params)
            {
                $admin = userModel::get($this->auth->id);
                $admin->save($params);
                //因为个人资料面板读取的Session显示，修改自己资料后同时更新Session
                Session::set("user_token", $admin->toArray());
                $this->success();
            }
            $this->error();
        }
        return;
    }

}
