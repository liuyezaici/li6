<?php

namespace app\admin\addon\message\install;

use think\Addons;

/**
 * 站内信插件
 */
class Install extends Addons
{
	public $menu = [
                        [
                            'name'    => 'message',
                            'title'   => '站内信',
                            'icon'    => 'menu-icon glyphicon glyphicon-list-alt',
                            'weigh'   => 1,
                            'ismenu'  => 0
                        ]
                    ];


    
	
    

}
