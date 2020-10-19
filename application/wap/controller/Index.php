<?php

namespace app\wap\controller;

use app\common\controller\Frontend;
use think\Config;
use think\Db;
use fast\Addon;
use app\admin\library\Auth;
use app\common\model\Users;
use app\admin\addon\usercenter\model\Third;

class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function test() {
        print_r(new Db());exit;
        echo 666;
    }
    /**
     * 管理员登录
     */
    public function login()
    {
        $url = $this->request->get('url', 'index/index');
        if ($this->auth->isLogin())
        {
            $this->success(__("You've logged in, do not login again"), $url);
        }
        if ($this->request->isPost())
        {
            $username = $this->request->post('username');
            $password = $this->request->post('password');
            $keeplogin = $this->request->post('keeplogin');
            $token = $this->request->post('__token__');
            $rule = [
                'username'  => 'require|length:3,30',
                'password'  => 'require|length:3,30',
                '__token__' => 'token',
            ];
            $data = [
                'username'  => $username,
                'password'  => $password,
                '__token__' => $token,
            ];
            if (Config::get('fastadmin.login_captcha'))
            {
                $rule['captcha'] = 'require|captcha';
                $data['captcha'] = $this->request->post('captcha');
            }
            $validate = new Validate($rule, [], ['username' => __('Username'), 'password' => __('Password'), 'captcha' => __('Captcha')]);
            $result = $validate->check($data);
            if (!$result)
            {
                $this->error($validate->getError(), $url, ['token' => $this->request->token()]);
            }
            $result = $this->auth->login($username, $password, $keeplogin ? 86400 : 0);
            if ($result === true)
            {
                $this->success(__('Login successful'), $url, ['url' => $url, 'id' => $this->auth->id, 'username' => $username, 'avatar' => $this->auth->avatar]);
            }
            else
            {
                $msg = $this->auth->getError();
                $msg = $msg ? $msg : __('Username or password is incorrect');
                $this->error($msg, $url, ['token' => $this->request->token()]);
            }
        }

        // 根据客户端的cookie,判断是否可以自动登录
        if ($this->auth->autologin())
        {
            $this->redirect($url);
        }
        $this->view->assign('title', '登录');
       print_r($this->view->fetch());
    }

    public function index()
    {
        //实例化配置组件
        $settingModel = Addon::getModel('setting');
        if(!$settingModel) {
            $webTitle = '未安装setting组件';
            $webdesc = '未安装setting组件';
            $webLogo = Config::get('default_img');
            $indexBg = '';
            $tongjiCode = '';
            $footBg = '';
            $footContent = '';
        } else {
            $webTitle = $settingModel->getSetting('web_title');//站点名字设置
            $webdesc = $settingModel->getSetting('web_desc');//站点描述
            $webLogo = $settingModel->getSetting('web_logo');//站点logo
            $indexBg = $settingModel->getSetting('index_bg');//首页背景图
            $tongjiCode = $settingModel->getSetting('tongji_code');//统计代码
            $footBg = $settingModel->getSetting('foot_bg');//页脚背景图
            $footContent = $settingModel->getSetting('foot_content');//页脚内容
        }
        $this->auth = Auth::instance();
        $uid = $this->auth->id;
        $userToken = '';
        $userAppid = '';
        //测试阶段默认uid
        $testToken = Config::get('test_token');
        if(!$uid && $testToken) {
            $userToken = $testToken;
            $uid = Users::getfieldbytoken($userToken, 'id');
        }
        if($uid) {
            $userToken = Users::getfieldbyid($uid, 'token');
            $userAppid = Third::getfieldbyuserId($uid, 'openid');
        }

        $socket_url = Addon::getAddonConfigAttr('socket', 'socket_url');
        if(!$socket_url) {
            $this->error('未配置socket_url');
        }
        $cfg = Addon::getAddonConfig('weixinpay');
        if(!$cfg) {
            $this->error('未配置微信支付信息');
        }
        if(!isset($cfg['JSAPI'])) {
            $this->error('未配置微信支付JSAPI信息');
        }
        $appid = $cfg['JSAPI']['appid'];
        if(!$appid) {
            $this->error('未配置微信支付的 appid');
        }

        $this->assign('weixin_appid', $appid);
        $this->assign('uip', request()->ip());
        $this->assign('user_openid', $userAppid);
        $this->assign('user_token', $userToken);
        $this->assign('socketUrl', $socket_url);
        $this->assign('userToken', $userToken);//userToken
        $this->assign('webTitle', $webTitle);//站点名
        $this->assign('webdesc', $webdesc);//站点名
        $this->assign('webLogo', $webLogo);//站点名
        $this->assign('indexBg', $indexBg);//首页背景图
        $this->assign('tongjiCode', $tongjiCode);//统计代码
        $this->assign('footBg', $footBg);//页脚背景图
        $this->assign('footContent', $footContent);//页脚内容

       print_r($this->view->fetch());
    }

}
