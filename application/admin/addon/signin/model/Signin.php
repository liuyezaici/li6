<?php
/**
 *  积分模块
 *  功能：积分余额 充值 收入提现
 *  作者：LR  2018.5.11
 */
namespace app\admin\addon\signin\model;

use fast\Random;
use think\Model;
use think\Db;
use fast\Addon;
use fast\Date;

class signin extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    protected $addonName = 'signin'; //插件名字
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';


    //获取签到赠送的积分 返回数组
    public function getSigninScore() {
        $configResult = Addon::getAddonConfig($this->addonName);
        if(!$configResult)  return '未写入config配置信息';
        $cfgInfo = $configResult['config'] ;
        if(empty($cfgInfo['score'])) return '未配置 score';
        return [$cfgInfo['score']];
    }


    //今日是否签到
    public function checkTodaySign($uid=0) {
        $todayInt = Date::todayInt();
        return $this->where([
            'cuid'=> $uid,
            'cday'=> $todayInt
        ])->find();
    }

    //获取我的连续签到天数
    public function continueSigninDays($uid=0) {
        $lastSignin = $this->where([
            'cuid'=> $uid
        ])->field('gift,cday,continue_days')->order('id desc')->limit(1)->find();
        if(!$lastSignin) return 0;
        //最后签到必须是昨天或今天 否则视为非连续签到
        if($lastSignin['cday'] != \fast\Date::subdayInt(1) && $lastSignin['cday'] != \fast\Date::todayInt()) {
            return 0;
        }
        return $lastSignin['continue_days'];
    }

    //是否满足连续签到天数
    public function continueSignin($uid=0,$day=10) {
        $lastSignin = $this->where([
            'cuid'=> $uid
        ])->field('gift,cday,continue_days_score')->order('id desc')->limit(1)->find();
        if(!$lastSignin){ //连续签到少一天 再加一天则满足条件
            return false;
        }
        //最后签到必须是昨天
        if($lastSignin['cday'] != \fast\Date::subdayInt(1)) {
            return false;
        }
        //最后签到必须是未获得积分
        if($lastSignin['gift']) {
            return false;
        }
        //送过积分连续签到如果未满 不算连续签到
        if($lastSignin['continue_days_score'] != $day-1 ) {
            return false;
        }
        return true;
    }


    //提交签到
    public function submitSignin($uid=0, $giveNum=0) {
        $scoreAddon = Addon::getModel('score');
        $adminid = $scoreAddon->getAdminId();
        $scoreInfo = $this->getSigninScore();
        if(!is_array($scoreInfo)) throw new \Exception($scoreInfo,-1);
        $score = $scoreInfo[0];
        $scoreAddon->tradeUserScore($adminid, $uid, $score, 'score', $scoreAddon->tradeTypeSign);
        $continue_days = 0;
        $continue_days_score = 0;
        //获取最后签到记录
        $lastSignin = $this->where([
            'cuid'=> $uid
        ])->field('gift,cday,continue_days,continue_days_score')->order('id desc')->limit(1)->find();
        if($giveNum) {
            $scoreAddon->tradeUserScore($adminid, $uid, $giveNum, 'score', $scoreAddon->tradeTypeSignGift);
            if(!$lastSignin) throw new \Exception('没有最后签到记录，怎么会送积分？',-1);
            $continue_days = $lastSignin['continue_days'] +1;
            $continue_days_score = 0; //每次赠送积分 连续签到的日期归零
        } else {
            if(!$lastSignin) { //首次签到
                $continue_days = 1;
                $continue_days_score = 1;
            } else {
                //最后签到是否昨天
                if($lastSignin['cday'] == \fast\Date::subdayInt(1)) {
                    $continue_days = $lastSignin['continue_days'] + 1;
                    $continue_days_score = $lastSignin['continue_days_score'] + 1;
                } else {
                    $continue_days = 1;
                    $continue_days_score = 1;
                }
            }
        }

        //写入签到记录
        $this->insert([
           'cuid' => $uid,
           'ctime' => time(),
           'cday' => Date::todayInt(),
           'score' => $score,
           'gift' => $giveNum > 0 ? 1 : 0,
           'gift_num' => $giveNum,
           'continue_days' => $continue_days,
           'continue_days_score' => $continue_days_score,
           'memo' => '手动签到',
        ]);
        return $score + $giveNum;
    }

}
