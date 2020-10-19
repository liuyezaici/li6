<?php
/**
 *  短信模块
 *  作者：LR  2018.12.18
 */
namespace app\admin\addon\sms\model;

use think\Model;
use think\Hook;

class Sms extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';

    protected $updateTime = false;
    protected static $expire = 300; //短信有效期
    // 追加属性
    protected $append = [];

    //短信状态
    protected static $smsStatusDefault = 0;
    protected static $smsStatusUsed = 1;
    protected static $smsStatusTimeOut = -1;
    protected static function getAllSmsStatus() {
        $allStatus = [];
        $allStatus[self::$smsStatusDefault] ='未使用';
        $allStatus[self::$smsStatusUsed] ='已使用';
        $allStatus[self::$smsStatusTimeOut] ='作废';
        return $allStatus;
    }
    //获取状态名字
    public static function getStatusName($status = 0){
        $allStatus = self::getAllSmsStatus();
        return isset($allStatus[$status]) ? $allStatus[$status] : $status;
    }

    //短信使用场景
    protected static $eventTypes = [
        'register',
        'login',
        'changemobile',
    ];
    //检测场景是否正确
    public static function checkEvent($event_='') {
        return in_array($event_, self::$eventTypes);
    }
    //检测场景是否是想要登录
    public static function wannaLogin($event_='') {
        return $event_ == 'login';
    }
    //检测场景是否是想要注册
    public static function wannaReg($event_='') {
        return $event_ == 'register';
    }
    //检测场景是否是想要变更手机
    public static function wannaChangeMobile($event_='') {
        return $event_ == 'changemobile';
    }
    //检测场景是否是想要变更密码
    public static function wannaChangePwd($event_='') {
        return in_array($event_, ['changepwd', 'resetpwd']);
    }
    /**
     * 获取最后一次手机发送的数据
     *
     * @param   int       $mobile   手机号
     * @param   string    $event    事件
     * @return  Sms
     */
    public static function getLastSms($mobile, $event = 'default')
    {
        $sms = self::where(['mobile' => $mobile, 'event' => $event])
            ->order('id', 'DESC')
            ->find();
        Hook::listen('sms_get', $sms, null, true);
        return $sms ? $sms : NULL;
    }

    /**
     *  保存短信验证码
     *
     * @param   int       $mobile   手机号
     * @param   int       $code     验证码,为空时将自动生成4位数字
     * @param   string    $event    事件
     * @return  boolean
     */
    public static function saveSms($mobile, $code = '', $event = '', $platform = '', $content = '')
    {
        $code = is_null($code) ? mt_rand(1000, 9999) : $code;
        $time = time();
        $ip = request()->ip();
        $sms = self::create([
            'platform' => $platform,
            'event' => $event,
            'mobile' => $mobile,
            'code' => $code,
            'content' => $content,
            'ip' => $ip,
            'createtime' => $time
        ]);
        return $sms;
    }

    /**
     * 校验验证码
     *
     * @param   int       $mobile     手机号
     * @param   int       $code       验证码
     * @param   string    $event      事件
     * @return  boolean
     */
    public static function checkSms($mobile, $code, $event = 'default')
    {
        $time = time() - self::$expire;
        $sms = self::where(['event' => $event, 'mobile' => $mobile ])
            ->order('id', 'DESC')
            ->find();
        if ($sms)
        {
            if ($time < $sms['createtime'])
            {
                if ($code != $sms['code']) {
                    return '验证码错误';
                } else if ($sms['status']!=0) {
                    return '验证码已经被使用';
                }
            } else {
                self::setTimeOut($sms['id']);
                return '验证码超时';
            }
        } else {
            return '未获取验证码';
        }
        return true;
    }

    /**
     * 作废空指定手机号验证码
     *
     * @param   int       $mobile     手机号
     * @param   string    $event      事件
     * @return  boolean
     */
    public static function setTimeOut($id)
    {
        return self::where(['id' => $id])
            ->update([
                'status' => self::$smsStatusTimeOut
            ]);
    }
    /**
     * 设为已使用 验证码
     *
     * @param   int       $mobile     手机号
     * @param   string    $event      事件
     * @return  boolean
     */
    public static function setSmsUsed($id)
    {
        return self::where(['id' => $id])
            ->update([
                'status' => self::$smsStatusUsed
            ]);
    }
    /**
     * 设为已使用 验证码
     *
     * @param   int       $mobile     手机号
     * @param   string    $event      事件
     * @return  boolean
     */
    public static function setSmsUsedByMobile($mobile, $event = 'default')
    {
        return self::where(['event' => $event, 'mobile' => $mobile ])
            ->update([
                'status' => self::$smsStatusUsed
            ]);
    }
    /**
     * 发送通知
     *
     * @param   mixed     $mobile   手机号,多个以,分隔
     * @param   string    $msg      消息内容
     * @param   string    $template 消息模板
     * @return  boolean
     */
    public static function notice($mobile, $msg = '', $template = NULL)
    {
        $params = [
            'mobile'   => $mobile,
            'msg'      => $msg,
            'template' => $template
        ];
        $result = Hook::listen('sms_notice', $params, null, true);
        return $result ? TRUE : FALSE;
    }
}
