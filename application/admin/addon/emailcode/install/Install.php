<?php

namespace app\admin\addon\emailcode\install;

use think\Addons;

/**
 * 资金插件
 */
class Install extends Addons
{
	public $menu = [
						[
							'name'    => 'emailcode',
							'title'   => '邮箱验证码',
							'icon'    => 'fa fa-wrench',
							'sublist' => [
								[
									'name'    => 'addon/emailcode/index/index',
									'title'   => '邮箱验记录',
									'icon'    => 'fa fa-chain',
									'sublist' => [
										['name' => 'addon/emailcode/index/add', 'title' => '添加'],
										['name' => 'addon/emailcode/index/edit', 'title' => '修改'],
										['name' => 'addon/emailcode/index/del', 'title' => '删除'],
									]
								],
							]
						]
					];

    
	
    

}
