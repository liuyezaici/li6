<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/5
 * Time: 16:32
 */

namespace Pub;

class Config
{

    protected static $config;

    function __construct()
    {
        self::$config = [
            //common的socket配置
            'commonIps' => [
                'tcp://192.168.0.171:33233',
            ],
            //stream消费者命名前缀 用于支持多机器运行
            'streamRules' => [
                'customerName' => 'any'
            ],
            //最大可用内存，超过自动重启服务，单位：M
            'maxCanUseMemory' => 10,
            //单任务消费者数量
            'cumstomerNumGroup' => 3,
            'cumstomerNumSingle' => 2,
            'cumstomerNumRecenter' => 2,
        ];
    }


    /**
     * @author LR
     * @Date:date 2019.7.22
     * @param string $namePath
     * @return mixed
     * 支持多级配置
     */
    public static function get($namePath = 'a.b.c')
    {
        $digui = function ($sour = [], $pathName = 'a.b.c') use (&$digui) {
            $pathArray = explode('.', $pathName);
            if (count($pathArray) == 1) {
                return $sour[$pathName];
            } else {
                $firstPath = $pathArray[0];
                unset($pathArray[0]);
                return $digui($sour[$firstPath], join('.', $pathArray));
            }
        };
        return $digui(self::$config, $namePath);
    }

}

new Config;
