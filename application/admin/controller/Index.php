<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use app\common\model\Users as userModel;
use think\Config;
use think\Hook;

/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{

    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }


    /**
     * 帐号密码登录
     */
    public function login()
    {
        $url = $this->request->get('url', 'index/index');
//        if ($this->auth->isLogin())
//        {
//            $this->success(__("您已经登录过"), $url);
//        }
        if ($this->request->isPost())
        {
            $username = $this->request->post('username');
            $password = $this->request->post('password');
            $keeplogin = $this->request->post('keeplogin');
            $captcha = $this->request->post('captcha');
            $status = userModel::adminLogin([
                'username'  => $username,
                'password'  => $password,
                'keeplogin'  => $keeplogin,
                'captcha'  => $captcha,
            ]);
            if($status === true) {
                $this->success('登录成功', $url);
            } else {
                $this->error($status, $url);
            }
        }

        // 根据客户端的cookie,判断是否可以自动登录
        if ($this->auth->autologin())
        {
            $this->redirect($url);
        }
        $background = cdnurl(Config::get('fastadmin.login_background'));
        $this->view->assign('background', $background);
        $this->view->assign('title', __('Login'));
        Hook::listen("login_init", $this->request);
       print_r($this->view->fetch());
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        $this->redirect('/');
    }
    /**
     * 后台首页
     */
    public function index()
    {
        //左侧菜单
        $menulist = $this->auth->getSidebar([
            'dashboard' => 'hot',
            'addon'     => ['new', 'red', 'badge'],
            'auth/rule' => __('Menu'),
            'general'   => ['new', 'purple'],
        ]
        );
        $settingAddon = \fast\Addon::getModel('setting');
        $webInfo = [];
        $set_web_title = $set_web_admin_title =
            '未安装setting组件';
        if($settingAddon) {
            $set_web_title = $settingAddon->getSetting('web_title');
            $set_web_admin_title = $settingAddon->getSetting('web_admin_title');
        }
        $webInfo['web_title'] = $set_web_title ? : '站点名字,key:web_title';
        $webInfo['set_web_admin_title'] = $set_web_admin_title ? : '后台站点名字,key:web_admin_title';
        $this->view->assign('menulist', $menulist);
        $this->view->assign('webInfo', $webInfo);
        print_r($this->view->fetch());
    }

}
