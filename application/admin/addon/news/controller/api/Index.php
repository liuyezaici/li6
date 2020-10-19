<?php

namespace app\admin\addon\news\controller\api;

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
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = Addon::getModel('news');
    }
	
	public function index(){
		$this->success(__('success'), []);
	}
}
