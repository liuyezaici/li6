<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class Area extends Model
{


   /*
   使用新表 fa_admin
   */

    /**
     * 获取国家列表
     */
    public static function getAllCountry($countryCode='')
    {
        $list = Db::table('world_countries')->field('iso2 as code,name')->select();
        foreach ($list as &$v) {
            $v['selected'] = $countryCode == $v['code'] ? ' selected' : '';
        }
        return $list;
    }
    /**
     * 通过国家获取州/省列表
     */
    public static function getStateByCountry($countryCode='', $stateId=0)
    {
        $countryId = Db::table('world_countries')->where('iso2', $countryCode)->value('id');
        $list = Db::table('world_states')->field('id,name')->where(['country_id' => $countryId]) ->select();
        foreach ($list as &$v) {
            $v['selected'] = $stateId == $v['id'] ? ' selected' : '';
        }
        return $list;
    }
    /**
     * 通过州/省获取城市列表
     */
    public static function getCitiesByState($stateId=0, $cityid='')
    {
        $list = Db::table('world_cities')->field('id,name')->where(['state_id' => $stateId]) ->select();
        foreach ($list as &$v) {
            $v['selected'] = $cityid == $v['id'] ? ' selected' : '';
        }
        return $list;
    }


}
