<?php
namespace app\index\controller;

use app\common\controller\Frontend;
use app\index\model\txtTool as Model;
use app\index\model\types;
use think\Db;
use think\Exception;

class Action extends Frontend
{
    protected $layout = true;
    protected $noNeedLogin = [''];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
    }




    //登录后-设置暗号-页面
    public function ask() {
        $oldAsk = Model::getKeyAsk($this->keys);
        $setAns = Model::hasKeyAns($this->keys);
        print_r($this->fetch('', [
            'ask' => $oldAsk,
            'setAns' => $setAns,
        ]));
    }

    //登录后-设置暗号
    public function saveAsk() {
        $ask = input('post.ask', '', 'trim');
        $ans = input('post.ans', '', 'trim');
        Model::editKeyAskAns($this->keys, $ask, $ans);
        $this->success('保存成功');
        return;
    }


    //首页
    public function index() {

        print_r($this->fetch());
    }
}
