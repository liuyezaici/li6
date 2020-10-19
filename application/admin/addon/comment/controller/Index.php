<?php

namespace app\admin\addon\comment\controller;

use app\common\controller\Backend;
use think\Config;
use think\Hook;
use think\Validate;
use fast\Addon;

/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->addonName ='comment';
        $this->model = Addon::getModel($this->addonName);
    }

    public function index()
    {
        if ($this->request->isPost()){
            //前置操作
            //.......................
            $buildparams = $this->buildparams();
//            $buildparams['where']['user_id'] = $this->auth->id;
            $result = parent::index($this->model, $buildparams, true);//调用父类
            //后继操作
            foreach($result['rows'] as &$v){
                $v['user_name'] = db('users')->where(['id'=>$v['userid']])->value('username');
                $v['text'] = mb_substr($v['text'],'0','15','UTF-8');
            }
            unset($v);
            return json($result);
        }
        return parent::index();
    }

    //评价详情
    public function details($ids =null)
    {
        if(!$ids) $this->error('id不存在');
        $commentInfo = $this->model->where(['id'=>$ids])->find();
        if(!$commentInfo) $this->error('评价记录不存在');
        $commentInfo['user_name'] = db('users')->where(['id'=>$commentInfo['userid']])->value('username');
        $commentInfo['status_name'] = $commentInfo['status'] ==0 ? '隐藏' : '显示';
        $commentInfo['createtime'] = date('Y-m-d H:i:s',$commentInfo['createtime']);
        $this->assign('row',$commentInfo);
       print_r($this->view->fetch());
    }
}
