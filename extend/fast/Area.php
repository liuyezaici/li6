<?php

/* ClassName: 全国城市地区街道
 * Memo:lr
 * Version:1.0.0
 * EditTime:2015-09-24

 * */

namespace fast;

use think\Db;
class Area
{

    //获取所有国家
    public function getAllCountries($codes='')
    {
        $db = new Db;
        $where_ = "";
        if($codes) {
            if(strstr($codes, ",")) {
                $where_ = "c_country_code in (". $db::quo($codes) .")";
            } else {
                $where_ = "c_country_code = '". $codes ."'";
            }
        }
        return $db->getAll("s_world_country", "c_country_code,c_country_name", $where_." order by c_country_code asc");
    }
    //获取国家的所有区域 国际化
    public function getCountryAllAreas($countryCodes='')
    {
        if(strstr($countryCodes, ",")) {
            $where_ = "country_code in (". $db::quo($countryCodes) .")";
        } else {
            $where_ = "country_code = '". $countryCodes ."'";
        }
        return $db->getAll("s_world_region", "region_code,country_code,region_name,upper_region", $where_." AND level = 1");
    }

    //获取省份的市区 国际化
    public function getRegionSonAreas($codes='0')
    {
        if(strstr($codes, ",")) {
            $where_ = "upper_region in (". $db::quo($codes) .")";
        } else {
            $where_ = "upper_region = '". $codes ."'";
        }
        return $db->getAll("s_world_region", "region_code,country_code,region_name,upper_region", $where_."");
    }
    //获取所有区域 国际化
    public function getAllAreas($codes='')
    {
        $where_ = "";
        if($codes) {
            if(strstr($codes, ",")) {
                $where_ = "region_code in (". $db::quo($codes) .")";
            } else {
                $where_ = "region_code = '". $codes ."'";
            }
        }
        return $db->getAll("s_world_region", "region_code,country_code,region_name,upper_region", $where_."");
    }

    //获取所有省份
    public static function getAllProvince()
    {
        return mysql::getInstance()->getAll("s_prov_city_area_street", "s_id,s_name,s_parent_id,s_needopen", "s_level='1' order by s_sort asc");
    }
    //获取省份的市/区
    public static function getAllCityByArea($areaId=0)
    {
        $allCity = $db->getAll("s_prov_city_area_street", "s_id,s_name,s_needopen", "s_parent_id = '{$areaId}'  order by s_sort asc");
        $newCity = array();
        foreach($allCity as $n =>&$v) {
            if($v['s_needopen'] == 1) {
                $newCity1 = $db->getAll("s_prov_city_area_street", "s_id,s_name,s_needopen", "s_parent_id = '{$v['s_id']}'  order by s_sort asc");
                $newCity = array_merge($newCity, $newCity1);
            }
        }
        if($newCity) $allCity = $newCity;
        return  $allCity;
    }
    //获取所有省份的市/区
    public static function getAllArea()
    {
        $allCity = $db->getAll("s_prov_city_area_street_no_open", "s_id,s_name,s_parent_id", "s_level in(1,2,3) order by s_sort asc");
        return  $allCity;
    }
    //获取省份/地区名称
    public static function getAreaName($id_ )
    {
        $provinceInfo = $db->getOne("s_prov_city_area_street", "s_name", "s_id = '". $id_ ."' limit 0,1");
        return isset($provinceInfo['s_name']) ? $provinceInfo['s_name']: '';
    }
    //搜索省份地区名称
    public static function searchAreaByName($areaName='', $num =10)
    {
        if(!$areaName) return [];
        return $db->getAll("s_prov_city_area_street", "s_id,s_name", "s_name LIKE '%{$areaName}%' limit 0,{$num}");
    }
    //获取省份/地区名称
    public static function getCountryName($code_ )
    {
        $countryInfo = cache::getCacheData('s_world_country', $code_, 'c_country_code', 'c_country_name')[0];
        return $countryInfo['c_country_name'];
    }
    //获取街道，区
    public static function getTownName($code_ )
    {
        $countryInfo = cache::getCacheData('s_world_region', $code_, 'region_code', 'region_name')[0];
        return $countryInfo['region_name'];
    }
    //获取完整的地址
    public static function getFullAddress($provId , $cityId , $m_town = '' , $address = '') {
        return self::getAreaName($provId) . self::getAreaName($cityId) . self::getAreaName($m_town) . $address;
    }

    //获取多层级的地区
    public static function getAllAreaWithSons2($limit=0) {
        $limitWh_ = 'pid=0';
        if($limit) $limitWh_ = 'pid=0 limit 0,'.$limit;
        $allArea = $db->getAll('location', 'id,name,type,pid,redirect', $limitWh_);
        foreach ($allArea as &$v) {
            $child = $db->getAll('location', 'id,name,type,pid,redirect', "pid={$v['id']}");
            foreach ($child as $n=>&$childVal) {
                if($childVal['redirect']==1) {
                    $child = $db->getAll('location', 'id,name,type,pid,redirect', "pid={$childVal['id']}");
                    break;
                }
            }
            foreach ($child as $n2=>&$childVal2) {
                unset($childVal2['type']);
                unset($childVal2['pid']);
                unset($childVal2['redirect']);
                $childVal2Child = $db->getAll('location', 'id,name,type,pid,redirect', "pid={$childVal2['id']}");
                foreach ($childVal2Child as $n3=>&$childVal3) {
                    unset($childVal3['type']);
                    unset($childVal3['pid']);
                    unset($childVal3['redirect']);
                }
                $childVal2['child'] = $childVal2Child;
            }
            $v['child'] = $child;
            unset($v['type']);
            unset($v['pid']);
            unset($v['redirect']);
        }
//        print_r($allArea);exit;
        return $allArea;
    }
}