<?php
/**
 * 广告类
 */

class adsense
{

	public function __construct( )
	{
	}

    //获取单个广告信息
    public function getAdCate($cid, $fildes = '*')
    {
        $db = mysql::getInstance();
        $sql = "SELECT ". $fildes ." FROM s_ad_category where adc_id = '{$cid}'";
        $db->Query($sql);
        return $db->getCurRecode( \PDO::FETCH_ASSOC );
    }
	//添加广告单元分类
	public function addAdCate( $vartab )
	{
        $db = mysql::getInstance();
		return DbBase::insertRows('s_ad_category',$vartab);
	}
    //修改广告单元分类
    public function editAdCate( $id, $vartab , $flag = 'adc_id' , $dbname = 's_ad_category')
    {
        $db = mysql::getInstance();
        return DbBase::updateByData( $dbname , $id , $vartab , $flag );
    }
    //添加广告商品
    public function addAdItem( $vartab )
    {
        $db = mysql::getInstance();
        return DbBase::insertRows('s_ad_list',$vartab);
    }
    //修改广告单元分类
    public function editAdItem( $id, $vartab , $flag = 'ad_id' , $dbname = 's_ad_list')
    {
        $db = mysql::getInstance();
        return DbBase::updateByData( $dbname , $id , $vartab , $flag );
    }
    //获取单个广告商品信息
    public function getAditem($ad_id, $fildes = '*')
    {
        $db = mysql::getInstance();
        $sql = "SELECT ". $fildes ." FROM s_ad_list where ad_id = '{$ad_id}'";
        $db->Query($sql);
        return $db->getCurRecode( \PDO::FETCH_ASSOC );
    }
}