<?php

namespace fast;


/**
 * 权限认证类
 自定义功能
 */
class DiyAddon
{

    //获取某个功能的模板
    //向后退路径，直到在当前控制器的view目录
    public static function getViewPath($addonName='', $controller)
    {
        return "../controller/addons/{$addonName}/view/{$controller}";
    }
}