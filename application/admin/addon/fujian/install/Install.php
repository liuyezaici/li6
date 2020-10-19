<?php

namespace app\admin\addon\fujian\install;

use think\Addons;

/**
 * 商家插件
 */
class Install extends Addons
{
    public $menu = [
        [
            'name'    => 'fujian',
            'title'   => '附件组件',
            'icon'    => 'glyphicon glyphicon-bookmark',
            'weigh'   => 355,
            'ismenu'  => 1,
            'sublist' => [
                [
                    'name'    => 'addon/fujian/index/index',
                    'title'   => '附件列表',
                    'icon'    => 'fa fa-user-o',
                    'sublist' => [
                        ['name' => 'addon/fujian/index/upload', 'title' => '上传'],
                        ['name' => 'addon/fujian/index/edit', 'title' => '修改'],
                        ['name' => 'addon/fujian/index/del', 'title' => '删除'],
                    ]
                ],

            ]
        ]
    ];
    
}
