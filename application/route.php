<?php

//use  think\Route;
//
//Route::alias('icannetzeroplus','index/product/item/icannetzeroplus');
//Route::alias('iCANNetZeroPlus','index/product/item/icannetzeroplus');

//Route::get('/icannetzeroplusspecs','index/product/item/icannetzeroplusspecs');
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
//
return [
    'share/:code' => 'index/share/page',
    'share/:code/' => 'index/share/page',

    //别名配置,别名只能是映射到控制器且访问时必须加上请求的方法
    '__alias__'   => [
        'icannetzeroplus' => 'index/product/item/icannetzeroplus',
        'icannetzeroplusspecs' => 'index/product/item/icannetzeroplusspecs',
        'icannetzeroplusexplore' => 'index/product/item/icannetzeroplusexplore',
        'icannetzeroplusdownload' => 'index/product/item/icannetzeroplusdownload',

        'icanminispecs' => 'index/product/item/icanminispecs',
        'icanminiexplore' => 'index/product/item/icanminiexplore',
        'icanminidownloads' => 'index/product/item/icanminidownloads',

        'icannetzero' => 'index/product/item/icannetzero',
        'icannetzerospecs' => 'index/product/item/icannetzerospecs',
        'icannetzeroexplore' => 'index/product/item/icannetzeroexplore',
        'icannetzerodownloads' => 'index/product/item/icannetzerodownloads',

        'ican' => 'index/product/item/ican',
        'dchome' => 'index/product/item/dchome',

        'company' => 'index/company',

    ],
    //变量规则
    '__pattern__' => [
    ],
//        域名绑定到模块
//        '__domain__'  => [
//            'admin' => 'admin',
//            'api'   => 'api',
//        ],
];
