<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/16
 * Time: 16:50
 */

namespace Pub\Tools;


class F
{

    /**
     * 是否一個不爲空的字符串
     * @param $data
     * @return bool
     */
    public static function isString($data):bool
    {
        return $data && $data != '' ? true : false;
    }

    /**
     * 是否一個有單元的數組
     * @param $data
     * @return bool
     */
    public static function isArray($data):bool
    {
        return is_array($data) && count($data) ? true : false;
    }

    /**
     * @param $data
     * @return bool
     */
    public static function isTrue($data):bool
    {
        return isset($data) && $data ? true : false;
    }

}