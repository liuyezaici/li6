<?php

namespace app\tool\controller;

use app\common\controller\Frontend;
use think\Validate;
use think\Db;
use fast\Addon;
use app\admin\library\Auth;
use app\common\model\Users;
use app\admin\addon\usercenter\model\Third;
use OSS\OssClient;
class World extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
    }

    //找市
    public function findcitys() {
        $stateId = input('post.stateId', '', 'intval');
        if(!$stateId) {
            $this->error('empty stateId');
        }
        $fields = 'id,name,latitude,longitude';
        $countryInfo = Db::table('world_cities')->field($fields)->where('state_id', $stateId)->select();
        if(!$countryInfo) {
            $this->error('noFoundCity');
        }
        $this->success('success', '', $countryInfo);
    }
    //找州/省份
    public function findstates() {
        $ctId = input('post.ctId', '', 'intval');
        if(!$ctId) {
            $this->error('empty ctId');
        }
        $fields = 'id,name,iso2,fips_code,latitude,longitude';
        $countryInfo = Db::table('world_states')->field($fields)->where('country_id', $ctId)->select();
        if(!$countryInfo) {
            $this->error('noFoundStates');
        }
        $this->success('success', '', $countryInfo);
    }

    //获取国家信息
    public function getcountryinfo() {
        $ctId = input('post.ctId', '', 'intval');
        if(!$ctId) {
            $this->error('empty ctId');
        }
        $fields = 'id,name,iso3,iso2,phonecode,capital,currency,currency_symbol,tld,native,region,subregion,timezones,longitude,latitude';
        $countryInfo = Db::table('world_countries')->field($fields)->find($ctId);
        if(!$countryInfo) {
            $this->error('noFoundCountry');
        }
        $this->success('success', '', $countryInfo);
    }

    //查找国家
    public function findcountry() {
        $ct = input('post.country', '', 'trim');
        if(!$ct) {
            $this->error('empty country');
        }
        //只允许汉字、字母和数字
        if(!Validate::is($ct,'chsAlphaNum')) {
            $this->error('invalid country');
        }
        //tld 顶级域名
        //native 当地标题
        //region 所属洲
        //subregion 所属洲-细节
        //capital 首都
        //timezones 时区
        //currency 货币缩写
        //currency_symbol 货币符号
        //longitude latitude 经度纬度
//        $fields = 'id,name,iso3,iso2,phonecode,capital,currency,currency_symbol,tld,native,region,subregion,timezones,longitude,latitude';
        $fields = 'id,name,native';
        if(is_numeric($ct)) {
            $where_ = [
                'phonecode' => ['like', "%{$ct}%"]
            ];
            $countryList = Db::table('world_countries')->field($fields)
                ->where($where_)->limit(10)->select();
        } else {
            $countryList = Db::table('world_countries')->field($fields)
                ->where([
                    'name' => ['like', "%{$ct}%"]
                ])->whereOr([
                    'iso3' => ['like', "%{$ct}%"]
                ])->whereOr([
                    'iso2' => ['like', "%{$ct}%"]
                ])->whereOr([
                    'native' => ['like', "%{$ct}%"]
                ])->limit(10)->select();
        }
        $this->success('success', '', $countryList);
    }

}
