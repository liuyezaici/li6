<?php

namespace app\admin\addon\help\install;

use think\Addons;

/**
 * 商品插件
 */
class Install extends Addons
{
	public $menu = [
						[
							'name'    => 'help',
							'title'   => '帮助系统',
							'icon'    => 'fa fa-file-word-o',
                            'weigh'   => 1,
                            'ismenu'  => 1,
							'sublist' => [
                                [
                                    'name'    => 'addon/help/index/index',
                                    'title'   => '帮助列表',
                                    'icon'    => 'fa fa-clone',
                                    'sublist' => [
                                        ['name' => 'addon/help/index/add', 'title' => '添加'],
                                        ['name' => 'addon/help/index/edit', 'title' => '修改'],
                                        ['name' => 'addon/help/index/del', 'title' => '删除'],
                                    ]
                                ],
                                [
                                    'name'    => 'addon/help/helptype/index',
                                    'title'   => '分类列表',
                                    'icon'    => 'fa fa-files-o',
                                    'ismenu'  => 0,
                                    'sublist' => [
                                        ['name' => 'addon/help/helptype/add', 'title' => '添加'],
                                        ['name' => 'addon/help/helptype/edit', 'title' => '修改'],
                                        ['name' => 'addon/help/helptype/del', 'title' => '删除'],
                                    ]
                                ],
							]
						]
					];

}
