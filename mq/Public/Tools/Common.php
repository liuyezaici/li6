<?php

namespace Pub\Tools;

class Common
{
    protected static $instance;

    //初始化配置
    public static function init($cfg)
    {
        self::$instance = new static;
        return RpcClient::config($cfg);
    }

    //每次获取的时候 执行
    public static function instance()
    {
        return self::$instance;
    }

    //魔术方法
    // 支持 class_func($arguments)这样的方式调取远程代码
    public static function __callStatic($class_func, $arguments)
    {
        $array_   = explode('_', $class_func);
        $funcName = end($array_);
        array_pop($array_);
        $className = join('_', $array_);
        $result    = call_user_func_array([RpcClient::instance($className), $funcName], $arguments);
        if (isset($result['code']) && $result['code'] != 0) {
            throw new \Exception("访问{$className}.{$funcName}失败:{$result['msg']}");
        }
        return isset($result['data']) ? $result['data'] : '';
    }

    //获取类的静态属性
    public function __get($class_staticName = '')
    {
        $array_    = explode('_', $class_staticName);
        $className = current($array_);
        array_shift($array_);
        $staticName = join('_', $array_);
        $result     = call_user_func_array([RpcClient::instance('GetClassAttr'), 'get'], [$className, $staticName]);
        if ($result['code'] != 0) {
            throw new \Exception("访问{$className}.get失败:{$result['msg']}");
        }
        return $result['data'];
    }
    //加载代码
    public static function loadCodes() {
        $result = call_user_func_array([RpcClient::instance('GetClassAttr'), 'getCodes'], []);
        return $result['data'];
    }
}
