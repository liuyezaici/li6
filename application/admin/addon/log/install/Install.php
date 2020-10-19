<?php

namespace app\admin\addon\log\install;

use think\Addons;

/**
 * 新闻插件
 */
class Install extends Addons
{
	public $menu = [
						[
							'name'    => 'log',
							'title'   => '调试日志',
							'icon'    => 'menu-icon glyphicon glyphicon-list-alt',
							'sublist' => [
								[
									'name'    => 'addon/log/index/index',
									'title'   => '日志',
									'icon'    => 'fa fa-reply-all',
									'sublist' => [
										['name' => 'addon/log/index/edit', 'title' => '修改'],
										['name' => 'addon/log/index/del', 'title' => '删除'],
									]
								],
							]
						]
					];


    
	
    

}
