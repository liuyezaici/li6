<?php

namespace app\admin\addon\makeapi\install;

use think\Addons;

/**
 * 新闻插件
 */
class Install extends Addons
{
	public $menu = [
						[
							'name'    => 'makeapi',
							'title'   => '制造接口',
							'icon'    => 'fa fa-list-alt',
							'weigh'   => 1,
							'sublist' => [
                                [
                                    'name'    => 'addon/makeapi/index/index',
                                    'title'   => '接口列表',
                                    'icon'    => 'glyphicon glyphicon-edit',
                                    'sublist' => [
                                        ['name' => 'addon/makeapi/index/add', 'title' => '添加'],
                                        ['name' => 'addon/makeapi/index/edit', 'title' => '修改'],
                                        ['name' => 'addon/makeapi/index/del', 'title' => '删除']
                                    ]
                                ],
							]
						]
					];

	
    

}
