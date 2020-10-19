<?php

/**
* 客户端类
*/
NameSpace Func;

class Client
{
    static function ip(){
        if (isset($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR']))
            $ip = $HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'];
        elseif (isset($HTTP_SERVER_VARS['HTTP_CLIENT_IP']))
            $ip = $HTTP_SERVER_VARS['HTTP_CLIENT_IP'];
        elseif (isset($HTTP_SERVER_VARS['REMOTE_ADDR']))
            $ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
        elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        elseif (isset($_SERVER['REMOTE_ADDR']))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = '0.0.0.0';
        if(strrpos(',',$ip)>=0){
            $ip=explode(',',$ip,2);
            $ip=current($ip);
        }
        return $ip;
    }

    //获取客户端操作系统信息包括win10
    public static function get_os(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $os = false;
        if (preg_match('/win/i', $agent) && strpos($agent, '95'))$os = 'Windows 95';
        else if(preg_match('/win 9x/i', $agent) && strpos($agent, '4.90'))$os = 'Windows ME';
        else if(preg_match('/win/i', $agent) && preg_match('/98/i', $agent))$os = 'Windows 98';
        else if(preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent))$os = 'Windows Vista';
        else if(preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent))$os = 'Windows 7';
        else if(preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent))$os = 'Windows 8';
        else if(preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent))$os = 'Windows 10';#添加win10判断
        else if(preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent))$os = 'Windows XP';
        else if(preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent))$os = 'Windows 2000';
        else if(preg_match('/win/i', $agent) && preg_match('/nt/i', $agent))$os = 'Windows NT';
        else if(preg_match('/win/i', $agent) && preg_match('/32/i', $agent))$os = 'Windows 32';
        else if(preg_match('/linux/i', $agent))$os = 'Linux';
        else if(preg_match('/unix/i', $agent))$os = 'Unix';
        else if(preg_match('/sun/i', $agent) && preg_match('/os/i', $agent))$os = 'SunOS';
        else if(preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent))$os = 'IBM OS/2';
        else if(preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent))$os = 'Macintosh';
        else if(preg_match('/PowerPC/i', $agent))$os = 'PowerPC';
        else if(preg_match('/AIX/i', $agent))$os = 'AIX';
        else if(preg_match('/HPUX/i', $agent))$os = 'HPUX';
        else if(preg_match('/NetBSD/i', $agent))$os = 'NetBSD';
        else if(preg_match('/BSD/i', $agent))$os = 'BSD';
        else if(preg_match('/OSF1/i', $agent))$os = 'OSF1';
        else if(preg_match('/IRIX/i', $agent))$os = 'IRIX';
        else if(preg_match('/FreeBSD/i', $agent))$os = 'FreeBSD';
        else if(preg_match('/teleport/i', $agent))$os = 'teleport';
        else if(preg_match('/flashget/i', $agent))$os = 'flashget';
        else if(preg_match('/webzip/i', $agent))$os = 'webzip';
        else if(preg_match('/Android/i', $agent))$os = 'Android';
        else if(preg_match('/offline/i', $agent))$os = 'offline';
        else if(preg_match('/iPhone/i', $agent)) $os = 'iPhone';
        else if(preg_match('/iPad/i', $agent)) $os = 'iPad';
        else $os = '未知';
        return $os;
    }
    
    //获取用户客户端设备
    public static function getDeviceType() {
        $deviceType = '';
        if(php_sapi_name() != 'cli'){
            $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            if(strpos($agent, 'iphone') > -1){
                $deviceType = 'iphone';
            }elseif(strpos($agent, 'ipad') > -1){
                $deviceType = 'ipad';
            }elseif(strpos($agent, 'android') > -1){
                $deviceType = 'android';
            }else{
                $deviceType = 'PC';
            }
        }
        return $deviceType;
    }
}