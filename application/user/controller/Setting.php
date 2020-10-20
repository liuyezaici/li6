<?php

namespace app\user\controller;

use app\common\controller\Frontend;

class Setting extends Frontend
{

    protected $noNeedLogin = [];
    protected $noNeedRight = '*';
    protected $layout = '';
    protected $keyword = '';
    protected $allTypes = [];

    public function _initialize()
    {
        parent::_initialize();
    }

    //我的文章
    public function index(){
        $uInfo = $this->auth->getToken();
        $this->view->assign('webTitle',   '控制面板');
        $this->view->assign('uInfo',   $uInfo);
        print_r($this->view->fetch());
    }

}
