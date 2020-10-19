<?php

namespace app\juzi\controller;

use app\common\controller\Frontend; 
use app\admin\library\Auth;
use app\common\model\Users;
use think\Config;
use think\Db;
use fast\Str;
use fast\Addon;
use fast\Email;
use app\admin\addon\sms\model\Sms;
use think\Request;
use think\Session;

class System extends Frontend
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected static $webTitle = '';
    protected static $webdesc = '';
    protected static $webLogo = '';
    protected static $tongjiCode = '';
    protected static $footContent = '';

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        //实例化配置组件
        $settingModel = Addon::getModel('setting');
        if(!$settingModel) {
            self::$webTitle = '未安装setting组件';
            self::$webdesc = '未安装setting组件';
            self::$webLogo = Config::get('default_img');
            self::$tongjiCode = '';
            self::$footContent = '';
        } else {
            self::$webTitle = $settingModel->getSetting('web_title');//站点名字设置
            self::$webdesc = $settingModel->getSetting('web_desc');//站点描述
            self::$webLogo = $settingModel->getSetting('web_logo');//站点logo
            self::$tongjiCode = $settingModel->getSetting('tongji_code');//统计代码
            self::$footContent = $settingModel->getSetting('foot_content');//页脚内容
        }
        $this->view->assign('webLogo', self::$webLogo);//站点名
        $this->view->assign('front_header', $this->view->fetch('common/header'));
        $this->view->assign('webTitle', self::$webTitle);//站点名
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('tongjiCode', self::$tongjiCode);//统计代码
        $this->view->assign('footContent', self::$footContent);//页脚内容
    }

    public function _initialize()
    {
        parent::_initialize();

    }
    //邮箱重置密码
    public function emailresetpassword() {
        if ($this->request->isPost())
        {
            $myEmail = $this->request->post('my_email');
            $emailCode = $this->request->post('email_code');
            if(!$myEmail) $this->error('请输入邮箱');
            $new_pwd = $this->request->post('new_pwd');
            if(!Str::isEmail($myEmail)) {
                return  $this->error('邮箱格式错误');
            }
            $emailAddon = Addon::getModel('emailcode');
            if(!$emailAddon) {
                return  $this->error('系统未安装邮箱验证码组件');
            }
            $myInfo = Users::where([
                'email'  => $myEmail,
            ])->find();
            if(!$myInfo) {
                $this->error('邮箱未注册');
            } else {
                $userId = $myInfo['id'];
                $emailRight = $emailAddon::checkEmailCode($myEmail, $emailCode, $emailAddon::$codeTypeChangePwd);
                if($emailRight !== true) {
                    $this->error($emailRight); //邮箱验证码错误
                }
                (new Users())->resetPassword($userId, $new_pwd);
            }
            $this->auth->logout();
            $this->success('密码修改成功,请重新登录');
        } else {
            exit('please post me');
        }
    }
    //邮箱获取重置密码的验证码
    public function sendemailtochangepwd() {
        if ($this->request->isPost())
        {
            $myEmail = $this->request->post('my_email');
            if(!$myEmail) $this->error('请输入邮箱');
            if(!Str::isEmail($myEmail)) {
                return  $this->error('邮箱格式错误');
            }
            $emailAddon = Addon::getModel('emailcode');
//            print_r($emailAddon);exit;
            if(!$emailAddon) {
                return  $this->error('系统未安装邮箱验证码组件');
            }
            $myInfo = Users::where([
                'email'  => $myEmail,
            ])->find();
            if(!$myInfo) {
                $this->error('邮箱未注册');
            } else {
                $status = $emailAddon::checkNoSendEmailCode($myEmail, $emailAddon::$codeTypeChangePwd);
                if($status !== true) {
                    return  $this->error('生成验证码失败：'. $status);
                }
                $emailCode = $emailAddon::getEmailCode($myEmail, $emailAddon::$codeTypeChangePwd);
                $mailsubject = "您申请了邮箱修改密码";//邮件主题
                $mailbody = "您申请了邮箱验证码以修改密码，验证码：". $emailCode." 如果不是您申请的请求，请无需理会且勿将验证码告诉他人。";//邮件内容
                $status = Email::mailto($myEmail, $mailsubject, $mailbody);
                if($status !== true) {
                    return  $this->error('邮件发送失败：'. $status);
                }
                //生成提现记录 扣除收入余额
                Db::startTrans();
                try {
                    $emailAddon::insert([
                        'email'=> $myEmail,
                        'code'=> $emailCode,
                        'ctime'=> time(),
                        'typeid'=> $emailAddon::$codeTypeChangePwd,
                    ]);
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                $this->success('发送成功');
            }
        } else {
            exit('please post me');
        }
    }

    //手机验证码登录
    public function mobilesmslogin() {
        if ($this->request->isPost())
        {
            $mobile = $this->request->post('login_mobile');
            $event = $this->request->request("event");
            $event = $event ? $event : 'register';
            $captcha = $this->request->request("code");
            if ($event)
            {
                \think\Hook::add('sms_check', function() {
                    return true;
                });
                $smsTestIgnore = Config::get('smsTestIgnore');
                if(!$smsTestIgnore) {
                    if(!$captcha) $this->error(__('缺少验证码'));
                    $ret = Sms::checkSms($mobile, $captcha, $event);
                    if (!$ret)
                    {
                        $this->error(__('验证码不正确'));
                    }
                }
                $buyerGroup = Addon::getAddonConfigAttr('buyer', 'groupid');
                if(!$buyerGroup) {
                    $this->error('未配置买家所在组');
                    exit;
                }
                $userinfo = Users::getByMobile($mobile);
                if (Sms::wannaReg($event) && $userinfo) {
                    //已被注册
                    $this->error(__('已被注册'));
                } else if (Sms::wannaLogin($event) && !$userinfo) {
                    //未注册
                    //自动注册用户
                    Users::createAdmin([
                        'username' => $mobile .'_loginreg',
                        'mobile' => $mobile,
                        'utype' => $buyerGroup,
                        'password' => \fast\Random::alnum(),
                        'groupid' => Addon::getAddonConfigAttr('buyer', 'groupid'),
                    ]);
                } else if (Sms::wannaChangeMobile($event) && $userinfo)
                {
                    //被占用
                    $this->error(__('已被占用'));
                } else if (Sms::wannaChangePwd($event) && !$userinfo)
                {
                    //未注册
                    $this->error(__('未注册'));

                }
            }
            $keeplogin = 1;
            $status = Users::phoneLogin([
                'mobile'  => $mobile,
                'keeplogin'  => $keeplogin,
            ]);
            if($status === true) {
                $this->success('登录成功');
            } else {
                $this->error($status);
            }
        } else {
            exit('please post me');
        }
    }
    //退出
    public function logout() {
        $this->auth->logout();
        $this->success('退出成功');
    }
    //登录
    public function idLogin() {
        if ($this->request->isPost())
        {
            $username = $this->request->post('u_nick');
            $password = $this->request->post('u_pwd');
            $keeplogin = 1;
            $captcha = $this->request->post('captcha');
            $status = Users::adminLogin([
                'username'  => $username,
                'password'  => $password,
                'keeplogin'  => $keeplogin,
                'captcha'  => $captcha,
            ]);
            if($status === true) {
                $this->success('登录成功');
            } else {
                $this->error($status);
            }
        } else {
            exit('please post me');
        }
    }

    //检测是否登录
    public function checklogin() {
        if ($this->request->isPost()) {
//            $openid = input('openid', '', 'trim');
//            if(!$openid) exit('请先授权微信登录');
            $this->auth = Auth::instance();
            //读取用户的id
            $uid = $this->auth->id;
            if(!$uid){
                $outputData = [
                    'username' => '-',
                    'nickname' => '-',
                    'avatar' => '-',
                    'token' => '-',
                    'local_uid' => 0,
                ];
                $this->error('nologin', '', $outputData);
            } else {
                //读取用户的nick
                $uInfo = Users::get($uid);
                $outputData = [
                    'username' => $uInfo['username'],
                    'nickname' => $uInfo['nickname'],
                    'avatar' => $uInfo['avatar'],
                    'token' => $uInfo['token'],
                    'local_uid' => $uid,
                ];
                $this->success('login', '', $outputData);
            }
        } else {
            exit('please post me');
        }
    }
    //ajax退出登录
    public function ajaxloginout() {
        if ($this->request->isPost()) {
            $this->auth = Auth::instance();
            //读取用户的id
            $uid = $this->auth->id;
            if(!$uid){
                $this->success('您已经退出成功');
            }else{
                $this->auth->logout();
                $this->success('退出成功');
            }
        } else {
            exit('please post me');
        }
    }

    /**
     * 登录
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

    /**
     * 注册
     */
    public function fast_reg()
    {
        if ($this->request->isPost())
        {
            $account = $this->request->post('account');
            $pwd1 = $this->request->post('pwd1');
            $inviter = $this->request->post('inviter', 0);
            if(!$account) $this->error('请输入帐号');
            if(!$pwd1)  $this->error('请输入密码');
            if(strlen($pwd1) > 25) $this->error('密码最多20位');
            $u_nick = strtolower($account);
            if ($this->request->isPost())
            {
                $params = [
                    'username' => $account,
                    'password' => $pwd1,
                    'pid' => $inviter,
                    'utype' => Auth::getIdentBuyer(),
                ];

                if(!$params['username']) $this->error('username不能为空');
                if(!$params['password']) $this->error('密码不能为空');

                $buyerGroup = Addon::getAddonConfigAttr('buyer', 'groupid');
                if(!$buyerGroup) {
                    $this->error('未配置买家所在组');
                }
                $params['groupid'] = $buyerGroup;
                $newUid = Users::createAdmin($params);
                if(!is_numeric($newUid))  $this->error('创建失败：'. $newUid);

                //直接登录
                $data = [
                    'username'  => $u_nick,
                    'password'  => $pwd1,
                    'keeplogin'  => 1,
                ];
                $status = Users::adminLogin($data);
                if($status == true) {
                    $this->success('注册成功，您已经成功登录');
                } else {
                    $this->error($status);
                }
            } else {
                $this->error('please post me');
            }
        }
  
    }

}
