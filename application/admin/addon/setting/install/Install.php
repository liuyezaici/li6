<?php

namespace app\admin\addon\setting\install;

use think\Addons;

/**
 * 资金插件
 */
class Install extends Addons
{
	public $menu = [
						[
							'name'    => 'setting',
							'title'   => '系统参数设置',
							'icon'    => 'fa fa-wrench',
							'sublist' => [
								[
									'name'    => 'addon/setting/index/index',
									'title'   => '系统参数设置',
									'icon'    => 'fa fa-chain',
									'sublist' => [
										['name' => 'addon/setting/index/add', 'title' => '添加'],
										['name' => 'addon/setting/index/edit', 'title' => '修改'],
										['name' => 'addon/setting/index/del', 'title' => '删除'],
									]
								],
							]
						]
					];


}
