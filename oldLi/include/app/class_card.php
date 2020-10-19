<?php
/* ClassName: 银行卡
 * Memo:lr
 * Version:1.0.0
 * EditTime:2014-09-30

 * */
class card
{

	public function __construct( )
	{
	}

	//获取所有省份
	public function getAllProvince()
	{
        $db = mysql::getInstance();
        $sql = "SELECT a_id,a_name FROM t_area WHERE a_level = '1'";
        $db->Query($sql);
        return $db->getAllRecodes( \PDO::FETCH_ASSOC );
	}
    //获取省份的省会
    public function getprovinceShenghui($province)
    {
        $db = mysql::getInstance();
        $sql = "SELECT a_shenghui_id FROM t_area WHERE a_id = '". $province ."'";
        $db->Query($sql);
        $shenghuiId = $db->getCurRecode( \PDO::FETCH_ASSOC );
        if(isset($shenghuiId['a_shenghui_id'])) {
            $shenghuiId = $shenghuiId['a_shenghui_id'];
        } else {
            $shenghuiId = 0;
        }
        return $shenghuiId;
    }
    //获取省份的市/区
    public function getAllCityByArea($areaId=0)
    {
        $db = mysql::getInstance();
        $sql = "SELECT a_id,a_name,a_path FROM t_area WHERE a_parentid = '". $areaId ."'";
        $db->Query($sql);
        return $db->getAllRecodes( \PDO::FETCH_ASSOC );
    }
    //获取所有支持的卡
    public function getAllCard()
    {
        $db = mysql::getInstance();
        $sql = "SELECT bank_id,bank_name from t_bank where is_support = 'supported';";
        $db->Query($sql);
        return $db->getAllRecodes( \PDO::FETCH_ASSOC );
    }
    //获取系统银行卡信息
    public function getSysCardInfo($card_id=0, $fields='*')
    {
        $db = mysql::getInstance();
        $sql = "SELECT ". $fields ." from t_bank where bank_id = '". $card_id ."'";
        $db->Query($sql);
        return $db->getCurRecode( \PDO::FETCH_ASSOC );
    }
    //获取会员银行卡信息
    public function getUserCardInfo($card_id=0, $fields='*', $wh_="1=1")
    {
        $db = mysql::getInstance();
        $sql = "SELECT ". $fields ." from t_usercard where c_id = '". $card_id ."' AND ".$wh_;
        $db->Query($sql);
        return $db->getCurRecode( \PDO::FETCH_ASSOC );
    }

    //银行卡状态

    public static function get_bank_status($array_style='text'){

        $bank_card_validate_status = array(
            0=>'未验证',
            1=>'验证中',
            2=>'验证通过',
            -2=>'验证不通过',
        );
        $bank_card_validate_status_html =array(
            0=>'未验证',
            1=>'<span style="color:#960">验证中</span>',
            2=>'<span style="color:green">验证通过</span>',
            -2=>'<span style="color:red">验证不通过</span>',
        );

        //银行卡验证状态
        if($array_style=='text') {
            return $bank_card_validate_status;
        }
        else if($array_style=='html') {
            return $bank_card_validate_status_html;
        }

        return $bank_card_validate_status;

    }


}