<?php
//die('<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">机房升级，网站维护中，请多包涵。 ');
ob_start(); //必须在最顶 防止setcookies 失败
error_reporting(E_ALL);
ini_set('display_errors','On');


define('ROOT_PATH', __DIR__);
require_once("Config.php");//系统参数配置
require_once 'Func/Loader.php';
//页面初始化
\Func\DbBase::init();
print_r(\Func\Core::invoke());
