<?php

namespace app\txtTool\controller;

use app\common\controller\Frontend;
use fast\Date;
use app\txtTool\model\txtTool as Model;

class Index extends Frontend
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';
    protected $layout = '';
    protected $keyword = '';
    protected $allTypes = [];

    public function _initialize()
    {
        parent::_initialize();
    }

    public function login() {
        $key = input('post.key', '', 'trim');
        print_r($key);
    }
}
