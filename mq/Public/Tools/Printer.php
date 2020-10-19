<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/16
 * Time: 16:59
 */

namespace Pub\Tools;


class Printer
{

    public static function cli($msg)
    {
        echo "[".date('Y-m-d H:i:s')."] ".$msg."\n";
    }

}