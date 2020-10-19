<?php

namespace app\admin\addon\score\install;

use think\Addons;

/**
 * 资金插件
 */
class Install extends Addons
{
	public $menu = [
						[
							'name'    => 'score',
							'title'   => '积分功能',
							'icon'    => 'fa fa-calendar-check-o',
                            'weigh'   => 1,
                            'ismenu'  => 1,
							'sublist' => [
                                [
                                    'name'    => 'addon/score/index/scorelist',
                                    'title'   => '积分记录',
                                    'icon'    => 'fa fa-database',
                                    'sublist' => [
                                        ['name' => 'addon/score/index/agree', 'title' => '同意'],
                                    ]
                                ],
								[
									'name'    => 'addon/score/index/index',
									'title'   => '用户积分',
									'icon'    => 'fa fa-align-center',
									'sublist' => [
										['name' => 'addon/score/index/add', 'title' => '添加'],
										['name' => 'addon/score/index/edit', 'title' => '修改'],
									]
								],
							]
						]
					];

    
	
    

}
