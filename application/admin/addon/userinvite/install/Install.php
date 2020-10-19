<?php

namespace app\admin\addon\userinvite\install;

use think\Addons;

/**
 * 用户邀请插件
 */
class Install extends Addons
{
	public $menu = [
						[
							'name'    => 'userinvite',
							'title'   => '用户邀请',
							'icon'    => 'menu-icon glyphicon glyphicon-list-alt',
                            'weigh'   => 1,
                            'ismenu'  => 1,
							'sublist' => [
                                [
                                    'name'    => 'addon/userinvite/index/index',
                                    'title'   => '邀请发起记录',
                                    'icon'    => 'glyphicon glyphicon-indent-left',
                                    'sublist' => [
                                        ['name' => 'addon/userinvite/index/add', 'title' => '添加'],
                                        ['name' => 'addon/userinvite/index/edit', 'title' => '修改'],
                                        ['name' => 'addon/userinvite/index/del', 'title' => '删除'],
                                    ]
                                ],
//                                [
//                                    'name'    => 'addon/advert/index/successlist',
//                                    'title'   => '邀请成功记录',
//                                    'icon'    => 'fa fa-reply-all',
//                                    'sublist' => [
//                                        ['name' => 'addon/advert/index/successlist/add', 'title' => '添加'],
//                                    ]
//                                ],
							]
						]
					];


    
	
    

}
