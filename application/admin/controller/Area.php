<?php

namespace app\admin\controller;

use app\admin\model\User;
use app\admin\model\Admin;
use app\common\controller\Backend;
use fast\Random;
use think\Db;
use think\Exception;

/**
 * 国家地区
 * @internal
 */
class Area extends Backend
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
     * 通过国家 获取 省 州 区
     */
    public function getStateByCountry()
    {
        $countryCode = input('post.parentVal', '', 'trim');
        if(!$countryCode) {
            $stateList = [];
        } else {
            $stateList = \app\admin\model\Area::getStateByCountry($countryCode, 0);
        }
        print_r($this->success('success', '', $stateList));

    }
    /**
     * 通过 省 州 区 获取 城市
     */
    public function getCityByState()
    {
        $stateId = input('post.parentVal', '', 'intval');
        if(!$stateId) {
            $cityList = [];
        } else {
            $cityList = \app\admin\model\Area::getCitiesByState($stateId, 0);
        }
        print_r($this->success('success', '', $cityList));

    }



}
