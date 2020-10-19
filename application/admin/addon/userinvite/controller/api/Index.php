<?php

namespace app\admin\addon\userinvite\controller\api;

use app\common\controller\Api;
use fast\Addon;

/**
 * 商品接口
 * @internal
 */
class Index extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $this->addonName = 'Plate';
		$this->model = Addon::getModel($this->addonName);
    }

	public function index(){
		$this->success(__('success'), []);
	}


}
