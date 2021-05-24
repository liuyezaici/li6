<?php
namespace app\index\controller;

use app\common\controller\Frontend;

class Index extends Frontend
{
    protected $layout = true;

    public function _initialize()
    {
        parent::_initialize();
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
    }


    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';
    protected $keyword = '';






    //首页
    public function index() {

        print_r($this->fetch());
    }
}
