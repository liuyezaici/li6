<?php
//系统Ip通用方法
namespace fast;

use think\Db;
use fast\Str;
use fast\File;
class Ip {
	public static function getIp(){
        $ip=false;
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
            for ($i = 0; $i < count($ips); $i++) {
                if (!preg_match("/^(10|172.16|192.168)./i", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	}
    //获取IP所在城市
    public static function getIpCity() {
        $IP = self::getIp();
        if($IP == '127.0.0.1') {
            /*return array(
                '本地环境',
                0,
                0
            );*/
            $IP  = '120.84.204.58';
        }
        //获取前面三位ip 如果数据库里没有 则从淘宝获取
        $ipArray = explode('.', $IP);
        $ipFront3 = $ipArray[0] .".". $ipArray[1] . "." . $ipArray[2];
        $IpInfo = Db::query('SELECT s_region,s_regionid,s_city,s_cityid FROM `s_ip_area` WHERE s_ip=?', [$ipFront3]);
        if($IpInfo) {
            $IpInfo = $IpInfo[0];
            return array(
                $IpInfo['s_region'] . '-'. $IpInfo['s_city'],
                $IpInfo['s_regionid'],
                $IpInfo['s_cityid']
            );
        } else {
            //淘宝IP接口 不稳定 有点卡
            $addressInfo = File::get_nr("http://ip.taobao.com/service/getIpInfo.php?ip=".$IP);
            //$addressInfo = Func::get_nr("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=".$IP);
            if($addressInfo) {
                $addressInfo = json_decode($addressInfo, true);
                $addressInfo = $addressInfo['data'];
                $country = isset($addressInfo['country']) ? $addressInfo['country'] : '';
                $country_id = isset($addressInfo['country_id']) ? $addressInfo['country_id'] : 0;
                $area = isset($addressInfo['area']) ? $addressInfo['area'] : '';
                $area_id = isset($addressInfo['area_id']) ? $addressInfo['area_id'] : '';
                $province = isset($addressInfo['region']) ? $addressInfo['region'] : '';
                $provinceId = isset($addressInfo['region_id']) ? $addressInfo['region_id'] : '';
                $city = isset($addressInfo['city']) ? $addressInfo['city'] : '';
                $city_id = isset($addressInfo['city_id']) ? $addressInfo['city_id'] : '';
                $isp = isset($addressInfo['isp']) ? $addressInfo['isp'] : '';
                //采集入库
                $newIpData = array(
                    's_ip' => $ipFront3,
                    's_country' => $country,
                    's_countryid' => $country_id,
                    's_area' => $area,
                    's_areaid' => $area_id,
                    's_city' => $city,
                    's_cityid' => $city_id,
                    's_region' => $province,
                    's_regionid' => $provinceId,
                    's_isp' => $isp
                );
                $db->InsertRecord('s_ip_area', $newIpData);
                return array(
                    $province . '-'. $city,
                    $provinceId,
                    $city_id
                );
            }
            return array();
        }
    }

    /*
     * 屏ip类
     * */
    public static function stopBadIp($iplist){
        $ip=self::getIp();
        $is_ban = false;
        if(!empty($iplist)){
            foreach( $iplist as $v ){
                if(strpos($v,'~')){
                    $ips=explode('~',$v);
                    $s1=substr(strrchr($ips[0],'.'),1);
                    $s2=substr(strrchr($ips[1],'.'),1);
                    $s3=substr(strrchr($ip,'.'),1);
                    $ss1=substr($ips[0],0,strrpos($ips[0],'.'));
                    $ss2=substr($ips[1],0,strrpos($ips[0],'.'));
                    $ss3=substr($ip,0,strrpos($ips[0],'.'));
                    if(strcmp($ss1,$ss2)==0 && strcmp($ss2,$ss3)==0 && $s1<=$s3 && $s3<=$s2 ){
                        $is_ban = true;
                    }
                }else{
                    $v=str_replace(array('*','.'),array('\\d+','\.'),$v);
                    if(preg_match('/^'.$v.'$/',$ip)){
                        $is_ban = true;
                    }
                }
            }
        }
        return $is_ban;
    }
    //判断是否本地
    public static function isLocal() {
        $ip3 = substr(self::getIp(), 0, 7);
        return($ip3 == '127.0.0' || $ip3 == '192.168');
    }
}
