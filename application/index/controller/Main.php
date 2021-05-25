<?php
namespace app\index\controller;

use app\common\controller\Backend;
use app\admin\controller\addons\pic\model\Article ;
use think\Cache;
use think\Db;
use think\Exception;

class Main extends Backend
{
    protected $layout = true;

    public function _initialize()
    {
        parent::_initialize();
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
    }


    protected $noNeedLogin = [''];
    protected $noNeedRight = '*';
    protected $keyword = '';

    //保存背景图的x坐标
    public function  save_bg_pos() {
        $bg_x = input('bg_x', 0, 'float');
        Cache::set('bgPosition', $bg_x);
        $this->success('');
    }

    //
    public function index() {
        $bg_x = Cache::get('bgPosition', 0);
        print_r($this->fetch('', [
            'bg_x' => $bg_x
        ]));
    }

}
