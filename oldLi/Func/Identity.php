<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/23
 * Time: 16:45
 * Desc: 用户身份层基类
 */

namespace Func;


class Identity extends Api
{

    public static $user;
    public static $userId; //当前身份uid
    public static $userAesKey; //当前身份加密
    public static $token; #必须提交的参数

    /**
     *  core初始化身份
     * @param $_POST   {token:token, encrypted:AES encode}
     */
    public function checkIdentity()
    {
        $token = isset($_POST['token']) ? $_POST['token'] : '';
        $encrypted = isset($_POST['encrypted']) ? $_POST['encrypted'] : ''; //加密的数据
        $webtoken = isset($_POST['webToken']) ? $_POST['webToken'] : ''; //web端token 不是必须的
        if (!$token) {
            return self::error(self::getLangText('noParam') . 'token');
        }
        if (!$userInfo = \PubToken::operatAppSession('get', $token)) {
            return self::error(self::getLangText('pleaseLoginApp'));
        }
        if (!is_array($userInfo)) return self::error(self::getLangText('noLogin'));
        $cacheWebtoken = \PubToken::optUserToken('getToken',  'web', $userInfo['uid']);
        if($webtoken && $webtoken != $cacheWebtoken) {
            return self::error(self::getLangText('webHasLogOut').':user_has_login_out');
        }
        self::$user = $userInfo;
        self::$userId = $userInfo['uid'];
        self::$userAesKey = $userInfo['aesKey'];
        self::$token = $token;
        //允许提交密钥获取用户信息
        if ($encrypted) {
            if (!$userInfoText = \Aes::aesDecode($encrypted, self::$userAesKey)) {
                return self::error(self::getLangText('encryptedError'));
            }
            if (!is_array($postData = json_decode($userInfoText, true))) {
                return self::error(self::getLangText('encryptedError'));
            }
            foreach ($postData as $keyname=>$val) {
                $_POST[$keyname] = $val;
            }
        }
        return true;
    }


    //统一解密data简化加密写法
    public static function AesDecodeUserData($encrypted='') {
        return \Aes::aesDecode($encrypted, self::$userAesKey);
    }
    //成功 用户aes加密
    public static function successAes($msg='success', $data=[]) {
        if($msg)   if(is_array($msg)) $msg = json_encode($msg, true);
        if($data)   if(is_array($data)) $data = json_encode($data, true);
        $msg = \Aes::aesHash($msg, self::$userAesKey);
        $data = \Aes::aesHash($data, self::$userAesKey);
        return self::success($msg, $data);
    }
    //成功 用户aes加密data 不加密msg
    public static function successDataAes($msg='success', $data=[]) {
        if($msg)   if(is_array($msg)) $msg = json_encode($msg, true);
        if($data)   if(is_array($data)) $data = json_encode($data, true);
        $data = \Aes::aesHash($data, self::$userAesKey);
        return self::success($msg, $data);
    }
}