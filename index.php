<?php
//print_r('网站暂停4天整顿，预计2020.4.20重新开方');
//exit;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// [ 应用入口文件 ]
// 定义应用目录
define('APP_DIR', 'application');
define('APP_PATH', __DIR__ . '/application/');

// 判断是否安装FastAdmin
if (!is_file(__DIR__ . '/install/install.lock'))
{
    header("location:/install.php");
    exit;
}
if(isset($_SERVER['HTTP_API']) || isset($_POST['HTTP_API'])){
	$http_api = isset($_SERVER['HTTP_API']) ? $_SERVER['HTTP_API'] : $_POST['HTTP_API'];
	if($http_api){
		$_SERVER['REQUEST_URI'] = $_SERVER['REDIRECT_URL'] = '/'.$http_api;
		$_GET['s'] = $_SERVER['argv'][1] = $_SERVER['ORIG_PATH_INFO'] = $_SERVER['REDIRECT_PATH_INFO'] = $_SERVER['PATH_INFO'] = $http_api;
		$_SERVER['QUERY_STRING'] = str_replace('=api/index', '='.$http_api, $_SERVER['QUERY_STRING']);
	}
}
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
