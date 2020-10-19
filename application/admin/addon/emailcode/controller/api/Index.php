<?php

namespace app\admin\addon\emailcode\controller\api;

use app\api\controller\Common;
use fast\Addon;
use think\Db;

/**
 * 参数接口
 * @internal
 */
class Index extends Common
{

    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['index','setlist'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];


    protected $admin_id = '';
    public function _initialize()
    {
        parent::_initialize();

        $this->addonName = 'emailcode';
        $this->model = Addon::getModel($this->addonName);
    }

    //查询系统配置
    public function index(){
        $keyname = input('keyname');
        if(!$keyname)  $this->error('keyname 不能空');
        $result = $this->model->getEmailcode($keyname);
        $this->success('success', $result);
    }

    //系统参数列表
    public function setlist(){
        $this->success('success', $this->model->select());
    }

}
