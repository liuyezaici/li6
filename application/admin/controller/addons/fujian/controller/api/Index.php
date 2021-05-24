<?php

namespace app\admin\addon\fujian\controller\api;

use app\api\controller\Common;
use fast\Random;
use think\Validate;
use fast\Addon;

/**
 * 对外接口
 * @internal
 */
class Index extends Common
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['login'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();

        $this->addonName ='fujian';
        $this->model = Addon::getModel($this->addonName);

    }
	
	public function index(){
		$this->success(__('success'), []);
	}
	
    /**
     * 商家登录
     * 
     * @param string $account 账号
     * @param string $password 密码
     */
    public function login()
    {
        $account = input('account');
        $password = input('password');
        if (!$account) $this->error('未输入账号');
        if (!$password) $this->error('未输入密码');
		$FujianConfig = \fast\Addon::getModel('tester', 'FujianConfig');
		$config = $TesterConfig->get(1);
		if(!isset($config->config['auth_group_ids']) || !$config->config['auth_group_ids']) $this->error('未设置商家组');
        $ret = $this->auth->login($account, $password, ['group_id' => ['in', $config->config['auth_group_ids']]], ['fujian' => 1]);
        if ($ret)
        {
            $this->success(__('Logged in successful'), $this->auth->getUserinfo());
        }
        else
        {
            $this->error($this->auth->getError());
        }
    }
}
