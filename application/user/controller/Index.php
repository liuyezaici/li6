<?php

namespace app\user\controller;

use app\common\controller\Frontend;

use think\Cache;

class Index extends Frontend
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
    //保存背景图的x坐标
    public function  save_bg_pos() {
        $bg_x = input('bg_x', 0, 'trim');
        Cache::set('bgPosition', $bg_x);
        $this->success('');
    }

    //文章首页
    public function index(){
        $uInfo = $this->auth->getToken();
        $bg_x = Cache::get('bgPosition', 0);
        $this->view->assign('webTitle',   '个人中心');
        $this->view->assign('avatar',   $uInfo['token']);
        $this->view->assign('nickname',   $uInfo['nickname']);
        $this->view->assign('avatar',   $uInfo['avatar']);
        $this->view->assign('bg_x',   $bg_x);
        print_r($this->view->fetch());
    }
}
