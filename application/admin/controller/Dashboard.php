<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use \fast\Addon;
use app\common\model\Users;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{
    protected $noNeedRight = '*';

    /**
     * 查看
     */
    public function index()
    {
        $todayInt = \fast\Date::todayInt();
        //获取我的身份【管理/代理/商家】
        $isAdmin = $this->auth->identIsNormalAdmin($this->auth->identity);
        $isAgent = $this->auth->identIsAgent($this->auth->identity);
        $isSeller = $this->auth->identIsSeller($this->auth->identity);
		$orderModel = Addon::getModel('order');
        $mySellerIds = 0;
        if($isAgent || $isSeller) {
            $mySellerIds = $this->auth->getChildrenAdminIds(true, 'seller');
        }
        //统计用户数
        $userNumCacheName = 'user_num_'.$this->auth->id;//不同的身份看到的数据是不一样的
        $userNums = \think\cache::get($userNumCacheName);
        if(!$userNums) {
            $userNums = 0;
            if($isAdmin) {
            $userNums = Users::countAllUser();
            } else {
//                echo $mySellerIds;exit;
                if($orderModel) {
                    //TP去重统计查询
                    $mapCountUser = [
                        'sellerid' => ['in', $mySellerIds]
                    ];
                    $userNums = $orderModel->where($mapCountUser)->count('distinct(cuid)');
                }
            }
            \think\cache::set($userNumCacheName, $userNums, 300);
        }
        //今日注册
        $todayReg = 0;
        if($orderModel) {
            if($isAdmin) {
                $todayReg = db('users')->where([
                    'createtime' => ['>', $todayInt]
                ])->count();
            }  else {
                if($isAgent) {//我是代理
                    $todayReg = db('users')->where([
                        'createtime' => ['>', $todayInt],
                        'pid' => ['in', $mySellerIds]
                    ])->count();
                } elseif($isSeller) {//我是商家
                    $todayReg = db('users')->where([
                        'createtime' => ['>', $todayInt],
                        'pid' => $this->auth->id
                    ])->count();
                }
            }
        }

        $this->view->assign([
            'totaluser'        => $userNums,
            'todayReg'       => $todayReg,
        ]);

       print_r($this->view->fetch());
    }

}
