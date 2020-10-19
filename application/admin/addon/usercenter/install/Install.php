<?php

namespace app\admin\addon\usercenter\install;

use think\Addons;

/**
 * 第三方登录
 */
class Install extends Addons
{
	public $menu = [
						[
							'name'    => 'usercenter',
							'title'   => '会员中心',
							'icon'    => 'fa fa-user-o',
							'weigh'   => 1,
							'ismenu'  => 0,
							'sublist' => [								
								['name' => 'addon/usercenter/config/edit', 'title' => '配置', 'ismenu' => 0]
							]
						]
					];

    

    

}
