<?php

namespace app\admin\addon\message\controller\api;

use app\common\controller\Api;
use fast\Random;
use think\Validate;
use fast\Addon;

/**
 * 后台首页
 * @internal
 */
class Index extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = [''];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $this->addonName = 'message';
		$this->model = Addon::getModel('message');
        //读取配置信息
        $configResult = Addon::getAddonConfig($this->addonName);
        if(!$configResult)  $this->error('未写入config配置信息');
        $cfgInfo = $configResult['config'] ;
        if(empty($cfgInfo['admin_id']))  $this->error('未配置 admin_id');
        $this->admin_id = $cfgInfo['admin_id'];
        $this->cfgInfo = $cfgInfo;
    }
	
	public function index(){
		$this->success(__('success'), []);
	}


    //读取站内信
    public function getMsgList(){
        $page = input('page') ?: 1;
        $pagesize = input('pagesize') ?: 20;
        $isRead = input('is_read',0);
        $list_page = list_page($this->model,['to_uid'=>$this->auth->id,'is_read'=>$isRead],$page,$pagesize,'id desc');
        foreach ($list_page['list'] as &$v){
            $v['source_info'] = [];
            if($v['source']){
                $sourceModel = Addon::getModel($v['source']);
                if(!$sourceModel)  $this->error($v['source'].'未安装');
                if(!$v['source_id'])  $this->error('source_id不存在');

                $v['source_info'] = $sourceModel->where(['id'=>$v['source_id']])->find();
                if(!empty($v['source_info']['station_id'])){
                    $v['source_info']['address'] =  Addon::getModel('station')->getfieldbyid($v['source_info']['station_id'],'address');
                }else{
                    $v['source_info']['address'] = '';
                }
                $boxModel = Addon::getModel('rubbish','RubbishBox');
                if($v['source_id'] && $boxModel){
                    $v['source_info']['capacity'] = $boxModel->where(['device_id'=>$v['source_id']])->sum('capacity');
                }else{
                    $v['source_info']['capacity'] = 0;
                }
            }
        }
        $this->success('success', $list_page);
    }

    //获取站内信详情
    public function getMsgDetail(){
        $id = input('id',0);
        if(!$id)  $this->error('缺少id');
        if(!$this->auth->id) $this->error('请先登陆');
        $detail = $this->model->where(['id'=>$id])->find();
        if($detail){
            $this->model->where(['id'=>$id])->update(['is_read'=>1]);
            $this->success('success', $detail);
        }else{
            $this->error('数据不存在');
        }
    }

}
