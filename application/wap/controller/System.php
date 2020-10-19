<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\common\model\Users as userModel;
use app\admin\library\Auth;
use think\Config;
use think\Db;
use fast\Str;
use fast\Addon;
use think\Session;
use fast\Email;

class System extends Frontend
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
            $myInfo = userModel::where([
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
                (new userModel())->resetPassword($userId, $new_pwd);
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
            $myInfo = userModel::where([
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

    //登录
    public function login() {
        if ($this->request->isPost())
        {
            $username = $this->request->post('u_nick');
            $password = $this->request->post('u_pwd');
            $keeplogin = 1;
            $captcha = $this->request->post('captcha');
            $status = userModel::adminLogin([
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
            $this->auth = Auth::instance();
            //读取用户的id
            $uid = $this->auth->id;
            if(!$uid){
                $outputData = [
                    'username' => '-',
                    'nickname' => '-',
                    'avatar' => '-',
                    'local_uid' => 0,
                ];
                $this->success('nologin', '', $outputData);
            }else{
                //读取用户的nick
                $uInfo = userModel::get($uid);
                $outputData = [
                    'username' => $uInfo['username'],
                    'nickname' => $uInfo['nickname'],
                    'avatar' => $uInfo['avatar'],
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
                    'groupid' => Auth::getIdentBuyer(),
                ];

                if(!$params['username']) $this->error('username不能为空');
                if(!$params['password']) $this->error('密码不能为空');

                $buyerGroup = Addon::getAddonConfigAttr('buyer', 'groupid');
                if(!$buyerGroup) {
                    $this->error('未配置买家所在组');
                }
                $params['groupid'] = $buyerGroup;
                $newUid = userModel::createAdmin($params);
                if(!is_numeric($newUid))  $this->error('创建失败：'. $newUid);

                //直接登录
                $data = [
                    'username'  => $u_nick,
                    'password'  => $pwd1,
                    'keeplogin'  => 1,
                ];
                $status = userModel::adminLogin($data);
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
