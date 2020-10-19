<?php

namespace app\juzi\controller;

use app\common\controller\Frontend;
use think\Config;
use think\Db;
use fast\Yihuo;

class Test extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function test() {
        $boxId = '01';
        $tezhengma = 'aa';
        $dataStr = '0000';
        echo Yihuo::yihuoCupboardJiaoyanwei($boxId, $tezhengma, $dataStr);
    }

    //发送urls
    public function sendUrl()
    {
        (new \fast\Socket())->sendImgUrls(['a','b','c']);
        print_r('ok');
    }

}
