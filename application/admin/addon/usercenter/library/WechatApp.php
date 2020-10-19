<?php

namespace app\admin\addon\usercenter\library;

use fast\Http;
use think\Config;

/**
 * 微信
 */
class WechatApp
{

    const GET_USERINFO_URL = "https://api.weixin.qq.com/sns/jscode2session";

    /**
     * 配置信息
     * @var array
     */
    private $config = [];

    public function __construct($options = [])
    {
        if ($config = Config::get('usercenter.wechatapp'))
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
        if (isset($params['code']))
        {
			//获取openid
			$queryarr = [
				"appid" => $this->config['app_id'],
				"secret"       => $this->config['app_secret'],
				"js_code"         => $params['code'],
				"grant_type" => 'authorization_code',
			];
			$ret = Http::post(self::GET_USERINFO_URL, $queryarr);
			\think\Log::write($ret);
			$userinfo = json_decode($ret, TRUE);
			if (!$userinfo || isset($userinfo['errcode']))
				return [];
			$userinfo = $userinfo ? $userinfo : [];
			$userinfo['avatar'] = isset($params['avatar']) ? $params['avatar'] : '';
			$userinfo['nickname'] = isset($params['nickname']) ? $params['nickname'] : '';
			$data = [
				'access_token'  => '',
				'refresh_token' => '',
				'expires_in'    => '',
				'openid'        => isset($userinfo['openid']) ? $userinfo['openid'] : '',
				'unionid'       => isset($userinfo['unionid']) ? $userinfo['unionid'] : '',
				'userinfo'      => $userinfo
			];
			return $data;
        }
        return [];
    }
}
