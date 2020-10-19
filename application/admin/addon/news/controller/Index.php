<?php

namespace app\admin\addon\news\controller;

use app\common\controller\Backend;
use think\Config;
use think\Hook;
use think\Validate;
use fast\Addon;

/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->addonName = 'news';
        $this->model = Addon::getModel('news');
    }
}
