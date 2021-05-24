<?php
namespace app\index\controller;

use app\common\controller\Backend;
use app\admin\controller\addons\pic\model\Article ;
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



    //
    public function index() {
        print_r($this->fetch());
    }

}
