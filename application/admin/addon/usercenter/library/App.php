<?php

namespace app\admin\addon\usercenter\library;

use fast\Http;
use think\Config;

/**
 * 微信
 */
class App
{

    /**
     * 配置信息
     * @var array
     */
    private $config = [];

    public function __construct($options = [])
    {
        if ($config = Config::get('usercenter.app'))
        {
            $this->config = array_merge($this->config, $config);
        }
        $this->config = array_merge($this->config, is_array($options) ? $options : []);
    }

    /**
     * 获取用户信息
     * @param array $params
     * @return array
     */
    public function getUserInfo($params = [])
    {
        $params = $params + $_POST + $_GET;
        if (isset($params['openid']))
        {
			$userinfo = [];
			$userinfo['avatar'] = isset($params['avatar']) ? $params['avatar'] : '';
			$userinfo['nickname'] = isset($params['nickname']) ? $params['nickname'] : '';
			$data = [
				'access_token'  => '',
				'refresh_token' => '',
				'expires_in'    => '',
				'openid'        => $params['openid'],
				'unionid'       => isset($params['unionid']) ? $params['unionid'] : '',
				'userinfo'      => $userinfo
			];
			return $data;
        }
        return [];
    }
}
