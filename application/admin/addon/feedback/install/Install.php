<?php

namespace app\admin\addon\feedback\install;

use think\Addons;

/**
 * 新闻插件
 */
class Install extends Addons
{
	public $menu = [
						[
							'name'    => 'feedback',
							'title'   => '问题反馈管理',
							'icon'    => 'fa fa-file-text',
							'weigh'   => 1,
							'sublist' => [
								[
									'name'    => 'addon/feedback/index/index',
									'title'   => '问题反馈列表',
									'icon'    => 'fa fa-sticky-note',
									'sublist' => [
//										['name' => 'addon/feedback/index/add', 'title' => '添加'],
//										['name' => 'addon/feedback/index/edit', 'title' => '修改'],
										['name' => 'addon/feedback/index/del', 'title' => '删除']
									]
								],
								['name' => 'addon/feedback/config/edit', 'title' => '配置', 'ismenu' => 0]
							]
						]
					];

    
	
    

}
