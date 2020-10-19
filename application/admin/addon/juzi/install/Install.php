<?php

namespace app\admin\addon\juzi\install;

use think\Addons;

/**
 * 商品插件
 */
class Install extends Addons
{
	public $menu = [
						[
							'name'    => 'juzi',
							'title'   => '句子',
							'icon'    => 'fa fa-file-word-o',
                            'weigh'   => 1,
                            'ismenu'  => 1,
							'sublist' => [
                                [
                                    'name'    => 'addon/juzi/index/index',
                                    'title'   => '句子列表',
                                    'icon'    => 'fa fa-clone',
                                    'sublist' => [
                                        ['name' => 'addon/juzi/index/add', 'title' => '添加'],
                                        ['name' => 'addon/juzi/index/edit', 'title' => '修改'],
                                        ['name' => 'addon/juzi/index/del', 'title' => '删除'],
                                    ]
                                ],
                                [
                                    'name'    => 'addon/juzi/caiji/index',
                                    'title'   => '翻页采集',
                                    'icon'    => 'fa fa-files-o',
                                    'ismenu'  => 1,
                                    'sublist' => [
                                    ]
                                ],
                                [
                                    'name'    => 'addon/juzi/caijiauthor/index',
                                    'title'   => '采集作者',
                                    'icon'    => 'fa fa-files-o',
                                    'ismenu'  => 1,
                                    'sublist' => [
                                    ]
                                ],
                                [
                                    'name'    => 'addon/juzi/caijigushi/index',
                                    'title'   => '采集古诗',
                                    'icon'    => 'fa fa-files-o',
                                    'ismenu'  => 1,
                                    'sublist' => [
                                    ]
                                ],
                                [
                                    'name'    => 'addon/juzi/caijidianji/index',
                                    'title'   => '采集典籍',
                                    'icon'    => 'fa fa-files-o',
                                    'ismenu'  => 1,
                                    'sublist' => [
                                    ]
                                ],
							]
						]
					];

}
