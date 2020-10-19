<?php

namespace app\admin\addon\score\controller\api;

use app\api\controller\Common;
use fast\Addon;
use think\Db;

/**
 * 资金接口
 * @internal
 */
class Index extends Common
{

    // 无需登录的接口,*表示全部
    protected $noNeedLogin = [];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];


    protected $admin_id = '';
    public function _initialize()
    {
        parent::_initialize();

        $this->addonName = 'score';
//      //初始化获取版本
        $this->model = Addon::getModel($this->addonName);
//        print_r($this->model);exit;
        //读取配置信息
        $configResult = Addon::getAddonConfig($this->addonName);
        if(!$configResult)  $this->error('未写入config配置信息');
//      print_r($configResult);exit;
        $cfgInfo = $configResult['config'] ;
        if(empty($cfgInfo['admin_id']))  $this->error('未配置 admin_id');
//      print_r($cfgInfo);
//      exit;
        $this->admin_id = $cfgInfo['admin_id'];
        $this->cfgInfo = $cfgInfo;
    }

    //查询用户积分
    public function getMyScore(){
        //获取用户资金
        $result = $this->model->getUserScoreInfo($this->auth->id);
        $this->success('success', $result);
    }
    //积分记录
    public function getUserScoreLog() {
        $page = input('page/d', '1');
        $pagesize = input('pagesize') ?: 20;
        $logModel =  Db::name('score_log');
        $map1['from_uid'] = $this->auth->id;
        $map2['to_uid'] = $this->auth->id;
        $list = $logModel->where($map1)->whereOr($map2)
            ->page($page,10)
            ->select();
        $count = $logModel->where($map1)->whereOr($map2)->count();
        foreach ($list as $n=>&$v) {
            $v['operate_desc'] = $this->model->getScoreTypeName($v['operate_type']);
            unset($v['id']);
            unset($v['memo']);
            unset($v['from_uid']);
            unset($v['to_uid']);
        }
        $result = array("total" => $count, "totalpage" => ceil($count / $pagesize), "list" => $list);
        $this->success('success', $result);
    }
}
