<?php
namespace app\admin\addon\message\controller;
use app\common\controller\Backend;


use fast\Addon;
/**
 * 版本控制
 * @internal
 */
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->addonName = 'message';
        $this->model = Addon::getModel('message');
    }
}
