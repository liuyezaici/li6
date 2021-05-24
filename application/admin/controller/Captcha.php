<?php

namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\AdminLog;
use app\common\controller\Frontend;
use app\common\library\Ems;
use app\common\library\Sms;
use think\Config;
use think\Hook;
use think\Validate;

/**
 * 验证码
 * @internal
 */
class Captcha extends Frontend
{

    protected $noNeedLogin = ['index'];
    protected $noNeedRight = ['index'];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }
    /**
     * 后台首页
     */
    public function index()
    {
        return captcha('', Config::get('captcha'));
    }

}
