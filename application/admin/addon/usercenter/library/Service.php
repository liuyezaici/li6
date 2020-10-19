<?php

namespace app\admin\addon\usercenter\library;

use app\admin\addon\usercenter\model\Third;
use fast\Random;

/**
 * 第三方登录服务类
 *
 * @author Karson
 */
class Service
{

    /**
     * 第三方登录
     * @param string    $platform   平台
     * @param array     $params     参数
     * @return boolean
     */
    public static function connect($platform, $params = [])
    {
        $time = time();
        $nickname = isset($params['userinfo']['nickname']) ? $params['userinfo']['nickname'] : '';
        $avatar = isset($params['userinfo']['avatar']) ? $params['userinfo']['avatar'] : '';
        $values = [
            'platform'      => $platform,
            'openid'        => $params['openid'],
            'unionid'        => $params['unionid'] ? : '',
            'openname'      => $nickname,
            'access_token'  => $params['access_token'],
            'refresh_token' => $params['refresh_token'],
            'expires_in'    => $params['expires_in'],
            'logintime'     => $time,
            'expiretime'    => $time + $params['expires_in'],
        ];
        $auth = \app\admin\library\Auth::instance();


		$unionidThird = null;
		if($params['unionid']) {
		    $unionidThird = Third::get(['platform' => $platform, 'unionid' => $params['unionid']]);
        }
		$third = Third::get(['platform' => $platform, 'openid' => $params['openid']]);
        if ($third)
        {
            $user = \app\common\model\Users::get($third['user_id']);
            if (!$user)
            {
                return '找不到$third[\'user_id\']:'.$third['user_id'];
            }
			$third->save($values);
            $userToken = $auth->loginByUidAndThirdid($user->id, $third->id);
            return [
                'userToken' => $userToken,
            ];
        }
        else
        {
			if($unionidThird){
				$user = \app\common\model\Users::get($unionidThird['user_id']);
				if (!$user)
				{
                    return '$unionidThird[\'user_id\']:'.$unionidThird['user_id'];
				}
			} else {
				// 先随机一个用户名,随后再变更为u+数字id
				// 默认注册一个会员
				$result = $auth->registerWeixinUser($nickname, $avatar, []);
				if ($result != true)
				{
					return 'registerWeixinUser注册失败：'. $result;
				}
				$user = $auth->getUser();
				$fields = ['username' => 'u' . $user->id];
				if (isset($params['userinfo']['nickname']))
					$fields['nickname'] = $params['userinfo']['nickname'];
				if (isset($params['userinfo']['avatar']))
					$fields['avatar'] = $params['userinfo']['avatar'];
	
				// 更新会员资料
				$user->save($fields);
			}

            // 保存第三方信息
            $values['user_id'] = $user->id;
            $third = Third::create($values);
            // 写入登录Cookies和Token
            $userToken = $auth->loginByUidAndThirdid($user->id, $third->id);
            return [
                'userToken' => $userToken,
            ];
        }
    }

}
