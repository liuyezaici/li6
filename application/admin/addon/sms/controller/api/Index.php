<?php

namespace app\admin\addon\sms\api\controller;

use app\api\controller\Common;
use app\common\model\Users;
use app\admin\addon\sms\model\Sms;
use fast\Addon;

/**
 * 二维码生成
 *
 */
class Index extends Common
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 发送验证码
     *
     * @param string    $platform     平台
     * @param string    $mobile     手机号
     * @param string    $event      事件名称
     */
    public function send()
    {
        $mobile = $this->request->request("mobile");
        $event = $this->request->request("event");
        $code = \fast\Str::getRam(4);
        if(!$event) $this->error(__('缺少参数event'));
        if(!Sms::checkEvent($event)) $this->error(__('event不支持'));

        $last = Sms::getLastSms($mobile, $event);
        if ($last && time() - $last['createtime'] < 60)
        {
            //$this->error(__('发送频繁'));
        }
        if ($event)
        {
            $userinfo = Users::getByMobile($mobile);
            if ($event == 'register' && $userinfo)
            {
                //已被注册
                $this->error(__('手机已被注册'));
            }
            else if (in_array($event, ['changemobile']) && $userinfo)
            {
                //被占用
                $this->error(__('手机已被占用'));
            }
            else if (in_array($event, ['login','changepwd', 'resetpwd']) && !$userinfo)
            {
                //未注册
                $this->error(__('未注册'));
            }
//            else if($event == 'pickupcode'){
//                //获取取件码即为校验码
//                $cupboardModel = Addon::getModel('cupboard','CupboardCode');
//                if(!$cupboardModel)  $this->error(__('未安装柜子设备组件'));
//                $code = $cupboardModel->getNewCode();
//                $cupboardModel->updateCode($mobile,$code);
//            }
        }
        $config = \fast\Addon::getAddonConfig('sms');
        if(!$config) $this->error(__('未配置短信'));
        $platform = $config['sms_type'];
        if(!$platform) $this->error(__('未配置短信类型'));
        $SmsLib = \fast\Addon::getLibrary('sms', $platform);
        $SmsLib->setConfig($config);
        $res = $SmsLib->smsSend($event, $mobile, $code);
        if ($res !== true) {
            $this->error($res);
        } else {
            $this->success(__('发送成功'));
        }
    }

    /**
     * 检测验证码
     *
     * @param string    $platform     平台
     * @param string    $mobile     手机号
     * @param string    $event      事件名称
     * @param string    $captcha    验证码
     */
    public function check()
    {
        $mobile = $this->request->request("mobile");
        $event = $this->request->request("event");
        $event = $event ? $event : 'register';
        $captcha = $this->request->request("captcha");

        if ($event)
        {
            $userinfo = Users::getByMobile($mobile);
            if ($event == 'register' && $userinfo)
            {
                //已被注册
                $this->error(__('已被注册'));
            }
            else if (in_array($event, ['changemobile']) && $userinfo)
            {
                //被占用
                $this->error(__('已被占用'));
            }
            else if (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo)
            {
                //未注册
                $this->error(__('未注册'));
            }
        }
		\think\Hook::add('sms_check', function() {
            return true;
        });
        $ret = Sms::checkSms($mobile, $captcha, $event);
        if ($ret)
        {
            $this->success(__('成功'));
        }
        else
        {
            $this->error(__('验证码不正确'));
        }
    }
}
