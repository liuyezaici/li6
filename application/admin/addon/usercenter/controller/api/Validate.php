<?php

namespace app\admin\addon\usercenter\api\controller;

use app\api\controller\Common;
use app\common\model\Users;
use app\admin\addon\sms\model\Sms;
use think\Hook;

/**
 * 验证接口
 */
class Validate extends Common
{

    protected $noNeedLogin = '*';
    protected $layout = '';
    protected $error = null;

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 检测邮箱
     * 
     * @param string $email 邮箱
     * @param string $id 排除会员ID
     */
    public function check_email_available()
    {
        $email = $this->request->request('email');
        $id = (int) $this->request->request('id');
        $count = Users::where('email', '=', $email)->where('id', '<>', $id)->count();
        if ($count > 0)
        {
            $this->error(__('邮箱已经被占用'));
        }
        $this->success();
    }

    /**
     * 检测用户名
     * 
     * @param string $username 用户名
     * @param string $id 排除会员ID
     */
    public function check_username_available()
    {
        $email = $this->request->request('username');
        $id = (int) $this->request->request('id');
        $count = Users::where('username', '=', $email)->where('id', '<>', $id)->count();
        if ($count > 0)
        {
            $this->error(__('用户名已经被占用'));
        }
        $this->success();
    }

    /**
     * 检测手机
     * 
     * @param string $mobile 手机号
     * @param string $id 排除会员ID
     */
    public function check_mobile_available()
    {
        $mobile = $this->request->request('mobile');
        $id = (int) $this->request->request('id');
        $count = Users::where('mobile', '=', $mobile)->where('id', '<>', $id)->count();
        if ($count > 0)
        {
            $this->error(__('该手机号已经占用'));
        }
        $this->success();
    }

    /**
     * 检测手机
     * 
     * @param string $mobile 手机号
     */
    public function check_mobile_exist()
    {
        $mobile = $this->request->request('mobile');
        $count = Users::where('mobile', '=', $mobile)->count();
        if (!$count)
        {
            $this->error(__('手机号不存在'));
        }
        $this->success();
    }

    /**
     * 检测邮箱
     * 
     * @param string $mobile 邮箱
     */
    public function check_email_exist()
    {
        $email = $this->request->request('email');
        $count = Users::where('email', '=', $email)->count();
        if (!$count)
        {
            $this->error(__('邮箱不存在'));
        }
        $this->success();
    }

    /**
     * 检测手机验证码
     * 
     * @param string $mobile    手机号
     * @param string $captcha   验证码
     * @param string $event     事件
     */
    public function check_sms_correct()
    {
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        $event = $this->request->request('event');
		Hook::add('sms_check', function() {
            return true;
        });
        if (!Sms::checkSms($mobile, $captcha, $event))
        {
            $this->error(__('验证码不正确'));
        }
        $this->success();
    }

    /**
     * 检测邮箱验证码
     * 
     * @param string $email     邮箱
     * @param string $captcha   验证码
     * @param string $event     事件
     */
    public function check_ems_correct()
    {
        $email = $this->request->request('email');
        $captcha = $this->request->request('captcha');
        $event = $this->request->request('event');
        if (!\app\common\library\Ems::check($email, $captcha, $event))
        {
            $this->error(__('验证码不正确'));
        }
        $this->success();
    }

}
