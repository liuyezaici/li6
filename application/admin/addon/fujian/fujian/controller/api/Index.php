<?php

namespace app\admin\addon\fujian\controller\api;

use app\api\controller\Common;
use fast\Random;
use think\Validate;
use fast\Addon;

/**
 * 对外接口
 * @internal
 */
class Index extends Common
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['login'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();

        $this->addonName ='fujian';
        $this->model = Addon::getModel($this->addonName);

    }
	
	public function index(){
		$this->success(__('success'), []);
	}

}
