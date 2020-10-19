<?php

namespace app\admin\addon\usercenter\library;

use fast\Http;
use think\Config;
use think\Session;

/**
 * 微信
 */
class Wechat
{

    const GET_AUTH_CODE_URL = "https://open.weixin.qq.com/connect/oauth2/authorize";
    const GET_ACCESS_TOKEN_URL = "https://api.weixin.qq.com/sns/oauth2/access_token";
    const GET_USERINFO_URL = "https://api.weixin.qq.com/sns/userinfo";
    const GET_TOKEN_URL = "https://api.weixin.qq.com/cgi-bin/token";
	const GET_TICKET_URL = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket';

    /**
     * 配置信息
     * @var array
     */
    private $config = [];

    public function __construct($options = [])
    {
        $this->config = $options;
    }

    /**
     * 登陆
     */
    public function login()
    {
        header("Location:" . $this->getAuthorizeUrl());
    }

    /**
     * 获取authorize_url
     */
    public function getAuthorizeUrl()
    {
        $state = md5(uniqid(rand(), TRUE));
        Session::set('state', $state);
        $queryarr = array(
            "appid"         => $this->config['app_id'],
            "redirect_uri"  => $this->config['callback'],
            "response_type" => "code",
            "scope"         => $this->config['scope'],
            "state"         => $state,
        );
        request()->isMobile() && $queryarr['display'] = 'mobile';
        $url = self::GET_AUTH_CODE_URL . '?' . http_build_query($queryarr) . '#wechat_redirect';
        return $url;
    }

    /**
     * 获取用户信息
     * @param array $params
     * @return array
     */
    public function getUserInfo($params = [])
    {
        $params = $params + $_POST + $_GET;
        if (isset($params['access_token']) || (isset($params['state']) && $params['state'] == Session::get('state')) || isset($params['code']))
        {
//            \fast\Log::addlog('getUserInfo---->pass_params');
            //获取access_token
            $data = isset($params['code']) ? $this->getAccessToken($params['code']) : $params;
//            \fast\Log::addlog('getAccessToken.data');
//            \fast\Log::addlog($data);
            $access_token = isset($data['access_token']) ? $data['access_token'] : '';
            $refresh_token = isset($data['refresh_token']) ? $data['refresh_token'] : '';
            $expires_in = isset($data['expires_in']) ? $data['expires_in'] : 0;
            if ($access_token)
            {
                $openid = isset($data['openid']) ? $data['openid'] : '';
                $unionid = isset($data['unionid']) ? $data['unionid'] : '';
                //获取用户信息
                $queryarr = [
                    "access_token" => $access_token,
                    "openid"       => $openid,
                    "lang"         => 'zh_CN'
                ];
                $ret = Http::post(self::GET_USERINFO_URL, $queryarr);
                $userinfo = json_decode($ret, TRUE);
                if (!$userinfo || isset($userinfo['errcode'])) {
                    \fast\Log::addlog('!$userinfo');
                    \fast\Log::addlog($userinfo);
                    return [];
                }
                $userinfo = $userinfo ? $userinfo : [];
                $userinfo['avatar'] = isset($userinfo['headimgurl']) ? $userinfo['headimgurl'] : '';
                $data = [
                    'access_token'  => $access_token,
                    'refresh_token' => $refresh_token,
                    'expires_in'    => $expires_in,
                    'openid'        => $openid,
                    'unionid'       => $unionid,
                    'userinfo'      => $userinfo
                ];
                return $data;
            }
        } else {
//            \fast\Log::addlog('not_in---->');
        }
        return [];
    }
	
	public function getTicket(){
		$ret = \think\Cache::get('usercenter.third.wechat.ticket');
		if($ret)return $ret;
		//
		$queryarr = array(
			"type" => "jsapi",
			"access_token" => $this->getAccessToken()['access_token'],
		);	
		$response = Http::post(self::GET_TICKET_URL, $queryarr);
        $ret = json_decode($response, TRUE);
		\think\Cache::set('usercenter.third.wechat.ticket', $ret, 7200);
        return $ret ? $ret : [];
	}

    /**
     * 获取access_token
     * @param string code
     * @return array
     */
    private function getAccessToken($code = '')
    {
//        print_r('$this->config');
//        print_r($this->config);
//        \fast\Log::addlog('$code:'. $code );
		if($code){
			$queryarr = array(
				"appid"      => $this->config['app_id'],
				"secret"     => $this->config['app_secret'],
				"code"       => $code,
				"grant_type" => "authorization_code",
			);
			$response = Http::post(self::GET_ACCESS_TOKEN_URL, $queryarr);
//            \fast\Log::addlog('$response');
//            \fast\Log::addlog($response);
			$ret = json_decode($response, TRUE);
		}else{
			$ret = \think\Cache::get('usercenter.third.wechat.access_token');
			if($ret) return $ret;
			$queryarr = array(
				"grant_type" => "client_credential",
				"appid"      => $this->config['app_id'],
				"secret"     => $this->config['app_secret'],
			);
			$response = Http::post(self::GET_TOKEN_URL, $queryarr);
			$ret = json_decode($response, TRUE);
			\think\Cache::set('usercenter.third.wechat.access_token', $ret, 7200);
		}
        return $ret ? $ret : [];
    }

}
