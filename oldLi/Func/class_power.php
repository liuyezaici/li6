<?php
class power
{
    private static $powerTable = 's_power';
	function __construct() {
	}

	//获取权限ids 管理员专用
    public static function getAllPowerIds() {
        $db = mysql::getInstance();
        return $db->getAllIds(self::$powerTable, 'p_id', "1");
    }
	//获取所有父权限
    public static function getAllPower() {
        $db = mysql::getInstance();
	    $allPower = $db->getAll(self::$powerTable, 'p_id,p_title,p_parentid,p_model,p_do,p_show,p_show_form', "1");
        $allPower = Str::diguiArray($allPower, 0, 'sons', 'p_parentid', 'p_id');
        return $allPower;
    }
    //获取权限名字
    public static function getPowerTitle($powerId=0) {
        $db = mysql::getInstance();
        if(!$powerId) return [];
        $powerInfo = DbBase::getRowBy(self::$powerTable, 'p_title', "p_id={$powerId}");
        return $powerInfo ? $powerInfo['p_title'] : '';
    }
    //获取单个权限菜单信息
    public static function getPowerInfo($powerId=0) {
        $db = mysql::getInstance();
        if(!$powerId) $powerId = 0;
        return DbBase::getRowBy(self::$powerTable, 'p_id,p_title,p_model,p_show,p_parentid', "p_id={$powerId}");
    }
	//获取我的自定义菜单
    public static function getMyTopMenu($userId=0) {
        $db = mysql::getInstance();
	    $menuData = $db->getAll('s_employee_menu', 'm_id,m_title,m_powerid,m_order', "m_uid={$userId} ORDER BY m_order ASC");
	    foreach ($menuData as &$datum) {
	        $powerInfo = self::getPowerInfo($datum['m_powerid']);
            $datum['p_title'] = isset($powerInfo['p_title']) ? $powerInfo['p_title'] : '';
            $datum['p_model'] = isset($powerInfo['p_model']) ? $powerInfo['p_model'] : '';
            $datum['p_do'] = isset($powerInfo['p_do']) ? $powerInfo['p_do'] : '';
            $datum['p_show'] = isset($powerInfo['p_show']) ? $powerInfo['p_show'] : '';
        }
        return $menuData;
    }
    //获取我的所有权限
    public static function getMyPower($uid=0) {
        $db = mysql::getInstance();
        //管理员要读取全部权限
        if(Users::isAdmin($uid)) return self::getAllPowerIds();
        $myInfo = Users::getUserInfo($uid, 'u_positionid');
        if(!$myInfo) return '';
        if(empty($myInfo['u_positionid'])) return '';
        $myPositionid = $myInfo['u_positionid'];
        $p_powerids = $db->getAllIds('s_power_position_power_id', 'l_powerid', "l_positionid={$myPositionid}");
        return trim($p_powerids);
    }
}