<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/5
 * Time: 16:13
 */

class Loader
{
    /* 路径映射 */
    public static $vendorMap = array(
        'Func' => __DIR__ . DIRECTORY_SEPARATOR ,
        'App' => __DIR__ .'/../Application' . DIRECTORY_SEPARATOR ,
    );
    /**
     * 自动加载器
     */
    public static function autoload($class)
    {
        $file = self::findFile($class);
        if (file_exists($file)) {
            self::includeFile($file);
        } else {
            print_r('文件不存在:'. $file);
        }
    }

    /**
     * 解析文件路径
     */
    private static function findFile($class)
    {
        $vendor = substr($class, 0, strpos($class, '\\')); // 顶级命名空间
        if(!isset(self::$vendorMap[$vendor])) {
            return '';
        }
        $vendorDir = self::$vendorMap[$vendor]; // 文件基目录
        $filePath = substr($class, strlen($vendor)) . '.php'; // 文件相对路径
        return strtr($vendorDir . $filePath, '\\', DIRECTORY_SEPARATOR); // 文件标准路径
    }

    /**
     * 引入文件
     */
    private static function includeFile($file)
    {
        if (is_file($file)) {
            include_once $file;
        }
    }
}

spl_autoload_register('Loader::autoload');