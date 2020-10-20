<?php

namespace app\admin\addon\article\install;

use think\Addons;

/**
 * 新闻插件
 */
class Install extends Addons
{
    public $menu = [
						[
							'name'    => 'article',
							'title'   => '文章管理',
							'icon'    => 'fa fa-indent',
							'weigh'   => 1,
							'sublist' => [
								[
									'name'    => 'addon/article/index/index',
									'title'   => '文章列表',
									'icon'    => 'fa fa-credit-card',
									'sublist' => [
										['name' => 'addon/article/index/add', 'title' => '添加'],
										['name' => 'addon/article/index/edit', 'title' => '修改'],
										['name' => 'addon/article/index/del', 'title' => '删除']
									]
								],
								[
									'name'    => 'addon/article/articletype/index',
									'title'   => '文章分类',
									'icon'    => 'fa fa-credit-card',
									'sublist' => [
										['name' => 'addon/article/articletype/add', 'title' => '添加'],
										['name' => 'addon/article/articletype/edit', 'title' => '修改'],
										['name' => 'addon/article/articletype/del', 'title' => '删除']
									]
								],
							]
						]
					];


}
