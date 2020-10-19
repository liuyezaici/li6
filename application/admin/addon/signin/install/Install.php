<?php

namespace app\admin\addon\signin\install;

use think\Addons;

/**
 * 资金插件
 */
class Install extends Addons
{
	public $menu = [
						[
							'name'    => 'signin',
							'title'   => '积分签到',
							'icon'    => 'fa fa-edit',
                            'weigh'   => 1,
                            'ismenu'  => 1,
							'sublist' => [
                                [
                                    'name'    => 'addon/signin/index/index',
                                    'title'   => '签到记录',
                                    'icon'    => 'fa fa-check-square-o',
                                    'sublist' => [
                                        [],
                                    ]
                                ]
							]
						]
					];

	
    

}
