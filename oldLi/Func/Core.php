<?php

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// | Shitou
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// | 入口文件
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// | 2019 07 05
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//使用Session

namespace Func;

use ReflectionClass;
class Core
{
 

    /**
     * Go constructor.
     * @throws
     */
    function __construct()
    {
        spl_autoload_register([__CLASS__,'findFiles']);
        self::invoke();
    }

    public static function findFiles($class)
    {
        $sysPathes = \Config::get('router.sysPathes');
        $classArr = explode('\\', $class);
        if(strpos($class, '\\'. $sysPathes['modelPath'] .'\\')){
            require_once __DIR__.'/'. $sysPathes['appPath'] .'/'. $sysPathes['modelPath'] .'/'.end($classArr).'.php';
        }
    }

    /**
     * @throws ReflectionException
     * @throws \ReflectionException
     */
    public static function invoke()
    {
        $sysPathes = \Config::get('router.sysPathes');
        //路由
        $s_ = isset($_GET['s']) ? $_GET['s'] : '';
        if($s_) {
            $s_ = trim($s_, '/');
            $s_ = explode('/', $s_);
            //注意 app应用名字不能带大写 因为是区分大小写的 所以要强制改小写
            $ctroller = $s_[0];
            $func = isset($s_[1]) ? $s_[1] : \Config::get('router.default.method');
            if(!$func) {
                return ('未提交method');
            }
        } else {
            $ctroller = \Config::get('router.default.ctrl');
            $func = \Config::get('router.default.method');
        }
//        print_r("get \n");
//        var_dump($_GET);
        //加載控制器或模型
        if(!file_exists($controlFile = RootPath.'/'. $sysPathes['appPath'] .'/'. $sysPathes['ctrlPath'] .'/' . ucfirst($ctroller) . '.php'))
        {
            return('类文件不存在:'. '/'. $sysPathes['appPath'] .'/'. $sysPathes['ctrlPath'] .'/' . ucfirst($ctroller) . '.php');
        }
        require_once $controlFile;

        $yepo = new ReflectionClass($ctroller);
        if(!$yepo->hasMethod($func)){
            print_r('$controlFile:'.$controlFile);
            return ('Method[' . $func . ']does not exist');
        }
        $instance = $yepo->newInstanceArgs([]);
        $yepoEc = $yepo->getmethod($func);
        if(!strpos($yepoEc, 'final')){
            print_r('接口必须用final开头');
            return '接口必须用final开头:'.$yepoEc;
        }
        //身份层控制器需要初始化拦截token
        if($yepo->hasMethod('checkIdentity')) {
            $intFunc = $yepo->getmethod('checkIdentity');
            $status = $intFunc->invoke($instance);
            if($status !== true) return $status;
        }
        return $yepoEc->invoke($instance);
    }

}
