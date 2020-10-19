<?php

namespace app\admin\addon\signin\controller\api;

use app\api\controller\Common;
use fast\Addon;
use think\Db;

/**
 * 签到接口
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

        $this->addonName = 'signin';
//      //初始化获取版本
        $this->model = Addon::getModel($this->addonName);

    }

    //获取连续签到天数
    public function getMySigninDays() {
        $continueDays =  $this->model->continueSigninDays($this->auth->id);//连续签到次数
        $this->success('获取成功', ['day'=>$continueDays]);
    }
    //签到接口
    public function signin() {
        $signinScore = $this->model->getSigninScore(); //签到送积分
        if(!is_array($signinScore)) {
            $this->error($signinScore);
        }
        $hasSiginToday = $this->model->checkTodaySign($this->auth->id); //检测今天是否签到送积分
        if($hasSiginToday) {
            $this->error('今日已签到');
        }
        $configinfo = Addon::getAddonConfig($this->addonName);
        $continueGive = $configinfo['continue_sign'];

        //赠送额外积分
        //生成充值记录
        Db::startTrans();
        try {
            //检测是否有设置赠送额外积分
            $gift = 0;
            if($continueGive['days'] && $continueGive['days'] >1){
                //检查是否达到
                $continueSignin =  $this->model->continueSignin($this->auth->id, $continueGive['days']);//连续签到次数
                if($continueSignin) $gift = $continueGive['score']; //连续签到 赠送积分
            }
            $getScore = $this->model->submitSignin($this->auth->id, $gift); //提交签到 返回获得的积分
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('success', $getScore);
    }

}
