<?php

namespace app\admin\addon\songs\install;

use think\Addons;

/**
 * 插件
 */
class Install extends Addons
{
	public $menu = [
						[
							'name'    => 'songs',
							'title'   => '网易歌曲',
							'icon'    => 'fa fa-file-word-o',
                            'weigh'   => 1,
                            'ismenu'  => 1,
							'sublist' => [
                                [
                                    'name'    => 'addon/songs/index/index',
                                    'title'   => '歌曲列表',
                                    'icon'    => 'fa fa-clone',
                                    'sublist' => [
                                        ['name' => 'addon/songs/index/add', 'title' => '添加'],
                                        ['name' => 'addon/songs/index/edit', 'title' => '修改'],
                                        ['name' => 'addon/songs/index/del', 'title' => '删除'],
                                    ]
                                ],
                                [
                                    'name'    => 'addon/songs/singer/index',
                                    'title'   => '歌手列表',
                                    'icon'    => 'fa fa-clone',
                                    'sublist' => [
                                        ['name' => 'addon/songs/singer/add', 'title' => '添加'],
                                        ['name' => 'addon/songs/singer/edit', 'title' => '修改'],
                                        ['name' => 'addon/songs/singer/del', 'title' => '删除'],
                                    ]
                                ],
                                [
                                    'name'    => 'addon/songs/caiji/index',
                                    'title'   => '采集',
                                    'icon'    => 'fa fa-files-o',
                                    'ismenu'  => 1,
                                    'sublist' => [
                                    ]
                                ],
							]
						]
					];

}
