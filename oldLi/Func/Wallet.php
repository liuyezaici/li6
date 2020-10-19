<?php

namespace Func;

class Wallet
{

    protected static $instance;
    //初始化配置
    public static function init($cfg) {
        self::$instance = new static;
        return RpcClientWallet::config($cfg);
    }


    //每次获取的时候 执行
    public static function instance(){
        return self::$instance;
    }
    //获取远程公共的方法、参数、缓存 等
    public static function call($classFuncStr, $params) {
        $array_ = explode('.', $classFuncStr);
        $className = $array_[0];
        $funcName = $array_[1];
        $result = RpcClientWallet::instance($className)->$funcName($params);
        return $result['data'];
    }

    //魔术方法
    // 支持 class_func($arguments)这样的方式调取远程代码
    public static function __callStatic($name, $arguments)
    {
        $array_ = explode('_', $name);
        $funcName = end($array_);
        array_pop($array_);
        $className = join('_', $array_);
        $result = call_user_func_array([RpcClientWallet::instance($className), $funcName], $arguments);
        if(!isset($result['code']) || $result['code'] != 0){
            print_r("访问{$className}.{$funcName}失败:{$result['msg']}");
        }
        return $result['data'];
    }

}

