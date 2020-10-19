<?php
/**
 * 用户VIP类

 * @version 1.0.0
 */

class vip
{
	protected $userID = '';//编号
    protected $userNick = '';//昵称
	protected $userState = '';//用户当前状态
	
	//构造函数
	function __construct($sessionid='')
	{
	}

	
	function add($vartab){
		$db = mysql::getInstance();
		if (DbBase::insertRows('c_vip',$vartab) > 0){
			return DbBase::lastInsertId();;//返回生成记录的ID
		}else{
			return 0;//执行出错
		}
	}
	
	function edit($ids, $vartab){
		$db = mysql::getInstance();
		return DbBase::updateByData('c_vip', $ids, $vartab, $flag='v_id');
	}
	
	function del( $ids)
	{
		$db = mysql::getInstance();
		return $db->DeleteRecord('c_vip', $ids, 'v_id');
	}
    //根据id得到vip记录信息
    function getVipByID($id,$fields='*'){
        $db = mysql::getInstance();
        $sql = "SELECT $fields FROM v_vip WHERE v_id = '$id'";
        $db->Query($sql);
        return $db->getCurRecode(\PDO::FETCH_ASSOC);
    }

    //获取会员的VIP记录 最新一条
    function getUserLastVipLog($uid,$fields='*'){
        $db = mysql::getInstance();
        $sql = "SELECT $fields FROM v_vip WHERE v_uid = '{$uid}' order by v_id desc limit 1";
        $db->Query($sql);
        return $db->getCurRecode(\PDO::FETCH_ASSOC);
    }
    //获取当前有效的vip
    public  static function getVipByUid($uid=0, $fields='*') {
        $db = mysql::getInstance();
        $mytime = Func::ntime();
        $wh_ = "v_uid='". $uid ."'";
        $wh_ = $wh_."AND v_stime < '". $mytime ."' AND v_etime > '". $mytime ."'";
        $sql = "SELECT ". $fields ." FROM c_vip WHERE ". $wh_ ." order by v_id desc limit 1";
        $db->Query($sql);
        return $db->getCurRecode(\PDO::FETCH_ASSOC);
    }
    //$utype 1试客VIP 2商家VIP
    public  static function getAllVip($utype=0, $fields='*', $wh_ = '1=1') {
        $db = mysql::getInstance();
        $mytime = Func::ntime();
        $wh_ = $wh_."AND v_stime < '". $mytime ."' AND v_etime > '". $mytime ."'";
        if($utype!=0) {
            $wh_ .= " AND v_viptype='". $utype ."'";
        }
        $sql = "SELECT ". $fields ." FROM c_vip WHERE ". $wh_ ."";
        $db->Query($sql);
        return $db->getAllRecodes(\PDO::FETCH_ASSOC);
    }

    //clear缓存
    public  static function clear_vip_cache(){
        /*更新缓存*/
        $cache = new Cache();
        $cache->Delete('index_ad_list');//首页
        $cache->Delete('vippage_ad_list');//vip页
    }

    //vip活动时间赠送

    /*活动名称：喜迎“双11”商家发布活动优惠大放送
    活动对象：所有新老客户（商家）
    时间：2014年10月10日-2014年11月11日

    开通月度会员（1个月）赠会员（15天）
    开通季度会员（3个月）赠会员（45天）
    开通年度会员（12个月）赠会员（6个月）
    开通至尊VIP会员（12个月）赠会员（10个月）
    */

    public static function  vip_time_free($type=1){
        $time = 0;

        //如果当前时间 大于 此次的活动时间，直接不赠送
        if( strtotime(Func::ntime()) > strtotime('2014-11-11 23:59:59')){
            return 0;
        }

        if( $type == 1 ){
            $time = 15 * 24 * 3600;//开通月度会员（1个月）赠会员（15天）
        }
        else if( $type == 2 ){
            $time = 45 * 24 * 3600;//开通季度会员（3个月）赠会员（45天）
        }
        else if( $type == 3 ){
            $time = 6 * 30 * 24 * 3600;//开通年度会员（12个月）赠会员（6个月）
        }
        else if( $type == 4 ){
            $time = 10 * 30 * 24 * 3600;//开通至尊VIP会员（12个月）赠会员（10个月）
        }

        return $time;
    }

}