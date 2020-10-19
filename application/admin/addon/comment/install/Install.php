<?php

namespace app\admin\addon\comment\install;

use think\Addons;

/**
 * 评价插件
 */
class Install extends Addons
{
	public $menu = [
						[
							'name'    => 'comment',
							'title'   => '评价管理',
							'icon'    => 'fa fa-comment',
							'weigh'   => 1,
							'sublist' => [
								[
									'name'    => 'addon/comment/index/index',
									'title'   => '评价列表',
									'icon'    => 'fa fa-square',
									'sublist' => [
										['name' => 'addon/comment/index/add', 'title' => '添加'],
										['name' => 'addon/comment/index/edit', 'title' => '修改'],
										['name' => 'addon/comment/index/del', 'title' => '删除']
									]
								],
								['name' => 'addon/comment/config/edit', 'title' => '配置', 'ismenu' => 0]
							]
						]
					];


}
