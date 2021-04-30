<?php

namespace app\txtTool\controller;

use app\common\controller\Frontend;
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

    //登录
    public function login() {
        $key = input('post.key', '', 'trim');
        Model::checkKey($key);
        Model::saveKeyCookies($key);
        $this->success('success');
    }

    //获取是否有设置过提问
    public function hasAsk() {
        $key = Model::getKeyCookies();
        if(!$key) $this->error('身份超时，请输入钥匙');
        $ask = Model::getKeyAsk($key);
        $this->success('success', '', ['ask'=> $ask]);
    }
}
