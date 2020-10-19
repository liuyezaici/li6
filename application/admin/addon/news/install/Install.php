<?php

namespace app\admin\addon\news\install;

use app\common\controller\Addoninstall;
use think\Addons;

/**
 * 新闻插件
 */
class Install extends Addons
{
	private $menu = [
						[
							'name'    => 'news',
							'title'   => '新闻管理',
							'icon'    => 'fa fa-indent',
							'weigh'   => 1,
							'sublist' => [
								[
									'name'    => 'addon/news/index/index',
									'title'   => '新闻列表',
									'icon'    => 'fa fa-credit-card',
									'sublist' => [
										['name' => 'addon/news/index/add', 'title' => '添加'],
										['name' => 'addon/news/index/edit', 'title' => '修改'],
										['name' => 'addon/news/index/del', 'title' => '删除']
									]
								],
								['name' => 'addon/news/config/edit', 'title' => '配置', 'ismenu' => 0]
							]
						]
					];



    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        Menu::create($this->menu, 0, $this->ver);
		$this->importsql('install.sql');
		$this->field($this->addon);
        return true;
    }

}
