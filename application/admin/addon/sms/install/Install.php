<?php

namespace app\admin\addon\sms\install;

use think\Addons;

/**
 * sms
 */
class Install extends Addons
{
	public $menu = [
						[
							'name'    => 'sms',
							'title'   => '短信组件',
							'icon'    => 'fa fa-sms',
							'weigh'   => 1,
							'ismenu'  => 1,
							'sublist' => [
                                [
                                    'name'    => 'addon/sms/index/index',
                                    'title'   => '短信记录',
                                    'icon'    => 'fa fa-taxi',
                                    'sublist' => [
                                        ['name' => 'addon/sms/index/edit', 'title' => '修改'],
                                    ]
                                ]
							]
						]
					];

}
