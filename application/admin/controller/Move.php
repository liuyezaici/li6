<?php

namespace app\admin\controller;

use app\admin\model\User;
use app\admin\model\Admin;
use app\common\controller\Backend;
use fast\Random;
use think\Db;
use think\Exception;

/**
 * 旧版数据过度
 * @internal
 */
class Move extends Backend
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    protected $layout = '';
    protected static $defaultGroupId = 2;

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 用户
     */
    public function index()
    {
        print_r('<h4>过度用户：</h4>');
        print_r(' user_has_device 表复制到新库  <br>');
        print_r('新建普通用户组,默认2 修改头部代码 $defaultGroupId改为 2<br>');
        print_r('执行 <a href="/move/user" target="_blank">导入</a>用户 <br>');
        print_r(' <a href="/move/device" target="_blank">导入设备</a> <br>');
    }


    /**
     * 用户id修改
     * 因为后台权限分配时 user的group是无法共享后台权限的 所以将user导入到admin表，并且将group默认为2
     */
    public function id_()
    {
        $oldList = Db::table('user')->field('userId,userName,userPwd,userEmail,authCode,registerTime,privilege')->order('userId', 'desc')->select();
        $insertNum = 0;
        foreach ($oldList as $v) {
            //修改旧版的uid为新的id
            if($newUid = Admin::getfieldbyemail($v['userEmail'], 'id')) {
                if($newUid == 1) continue;
                Admin::where('id', $newUid)->update([
                    'id' => $v['userId']
                ]);
                $insertNum ++;
            }
        }
        $this->success('success Num:'.$insertNum);
    }

    /**
     * 导入旧用户
     * 因为后台权限分配时 user的group是无法共享后台权限的 所以将user导入到admin表，并且将group默认为2
     */
    public function inuser()
    {
        $oldList = Db::table('user')->field('userId,userName,userPwd,userEmail,Name,authCode,registerTime,privilege')->order('userId', 'desc')->select();
        $insertNum = 0;
        foreach ($oldList as $v) {
            //修改旧版的uid为新的id
            if(!Admin::getfieldbyemail($v['userEmail'], 'id')) {
                Admin::insert([
                    'id' => $v['userId'],
                    'username' => $v['userName'],
                    'nickname' => $v['Name'],
                    'password' => $v['userPwd'],
                    'email' => $v['userEmail'],
                ]);
                $insertNum ++;
            }
        }
        $this->success('success Num:'.$insertNum);
    }
    /**
     * 对比旧版密码
     *
     */
    public function comparepwd()
    {
        $oldList = Db::table('user')->field('userId,userEmail,userPwd')->order('userId', 'desc')->select();
        $insertNum = 0;
        $noRegList = [];
        $noSamePwdList = [];
        foreach ($oldList as $v) {
            //修改旧版的uid为新的id
            if(!$exist = Admin::getbyemail($v['userEmail'], 'id,password')) {
                $noRegList[] = $v['userId'];
            } else {
                if($exist['password'] != $v['userPwd']) {
                    $noSamePwdList[] = $v['userId'];
                    //更新密码
                    Admin::where('id', $exist['id'])->update([
                        'password' =>  $v['userPwd']
                    ]);
                }
            }
            $insertNum ++;
        }
        print_r("未注册的：");
        print_r($noRegList);
        print_r("密码不一致的：");
        print_r($noSamePwdList);
        $this->success('success Num:'.$insertNum);
    }

    /**
     *  导入设备
     * 因为后台权限分配时 user的group是无法共享后台权限的 所以将user导入到admin表，并且将group默认为2
     */
    public function device()
    {

        //使用旧版设备 不能影响旧的表 业务
        //导入设备索引
        $deviceList = Db::table('user_has_device')->select();
        $insertNum = 0;
        foreach ($deviceList as $userDevice) {
            if(!$userDevice['fk_userId']) continue;
            if(!$userDevice['fk_deviceId']) continue;
            $email = Db::table('user')->where(['userId'=>$userDevice['fk_userId']])->value('userEmail');
            $newUid = Admin::getfieldbyemail($email, 'id');
            if(!$newUid) $this->error('找不到Email:'. $email);
            //检测新表
            if(!Db('userHasDevice')->where([
                'fk_userId' => $newUid,
                'fk_deviceId' => $userDevice['fk_deviceId'],
            ])-> find()) {
                //写入新表
                Db('userHasDevice')->insert([
                    'fk_userId'=> $newUid,
                    'fk_deviceId' => $userDevice['fk_deviceId'],
                    'uhdOwner'=> $userDevice['uhdOwner'],
                    'uhdReady'=> $userDevice['uhdReady'],
                    'uhdAddTime'=> $userDevice['uhdAddTime'] ? : time(),
                    'uhdDevName'=> $userDevice['uhdDevName'] ? : '',
                    'uhdDevDesc'=> $userDevice['uhdDevDesc'] ? : '',
                ]);
                $insertNum++;
            }
        }
        $this->success('success Num:'.$insertNum);

    }


}
