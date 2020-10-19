<?php
//组织架构类
class organization
{
    public static $organizationTable= 's_power_organization';//部门表
    public static $positionTable= 's_power_organization_position';//岗位表
    public static $positionEmployeeTable= 's_power_position_employees';//岗位的员工表

	function __construct() {

	}

    //获取所有组织架构
    public static function getAllOrganization( $parentGid=0) {
        $db = mysql::getInstance();
        $where_ = "g_parentid = {$parentGid}";
        return $db->getAll(self::$organizationTable, "g_id,g_title", $where_);
    }

    //部门操作日志
    public static function addOrganizationLog($oId = 0, $operator, $ntime, $memo='')
    {
        $db = mysql::getInstance();
        if(!$ntime) $ntime = Timer::now();
        $vartab = array(
            'log_organization_id' => $oId,
            'log_addtime' => $ntime,
            'log_uid' => $operator,
            'log_desc' => $memo
        );
        return DbBase::insertRows('s_power_organization_log',$vartab);
    }
    //岗位操作日志
    public static function addPositionLog($oId = 0, $operator, $ntime, $memo='')
    {
        $db = mysql::getInstance();
        if(!$ntime) $ntime = Timer::now();
        $vartab = array(
            'log_position_id' => $oId,
            'log_addtime' => $ntime,
            'log_uid' => $operator,
            'log_desc' => $memo
        );
        return DbBase::insertRows('s_power_position_log',$vartab);
    }
    //获取所有岗位
    public static function getAllPosition( $parentPid=0) {
        $db = mysql::getInstance();
        $where_ = "p_parentid = {$parentPid}";
        return $db->getAll(self::$positionTable, "p_id,p_title", $where_);
    }
    //获取部门信息
    public static function getOrganizationInfo($organizationId= 0, $fields='*') {
        $db = mysql::getInstance();
        if(!$organizationId) return [];
        return DbBase::getRowBy(self::$organizationTable, $fields, "g_id={$organizationId}");
    }
    //获取部门标题
    public static function getOrganizationTitle($organizationId= 0) {
        $db = mysql::getInstance();
        if(!$organizationId) return '-';
        $orgInfo = self::getOrganizationInfo($organizationId, 'g_title');
        return $orgInfo ? $orgInfo['g_title']: '-';
    }
    //获取岗位信息
    public static function getPositionInfo($positionId=0, $fields='*') {
        $db = mysql::getInstance();
        if(!$positionId) return [];
        return DbBase::getRowBy(self::$positionTable, $fields, "p_id={$positionId}");
    }
    //获取岗位名字
    public static function getPositionTitle($positionId=0) {
        $db = mysql::getInstance();
        if(!$positionId) return'-';
        $positionInfo = DbBase::getRowBy(self::$positionTable, 'p_title', "p_id={$positionId}");
        return $positionInfo ? $positionInfo['p_title'] : '';
    }
    //获取岗位的部门名字
    public static function getPositionOrganizationTitle($positionId=0) {
        $db = mysql::getInstance();
        if(!$positionId) return [];
        $positionInfo = DbBase::getRowBy(self::$positionTable, 'p_organizationid', "p_id={$positionId}");
        if(!$positionInfo) return '-';
        $organizationid = $positionInfo['p_organizationid'];
        if(!$organizationid) return '-';
        return self::getOrganizationTitle($organizationid);
    }

    //获取岗位权限
    public static function getPositionPower($positionId=0) {
        $db = mysql::getInstance();
        $positionInfo = self::getPositionInfo($positionId, 'p_power_ids');
        return isset($positionInfo['p_power_ids']) ? $positionInfo['p_power_ids'] : '';
    }

    //给岗位分配权限时，所有权限和岗位生成一条索引
    public static function refreshPositionPowerIndex($positionId=0, $powerIds='', $userId=0, $myTime=NULL) {
        $db = mysql::getInstance();
        $myTime = is_null($myTime)? Timer::now():$myTime;
        $powerLogIds = [];
        $powerIdArray = explode(',', $powerIds);
        foreach ($powerIdArray as $tmpPowerid) {
            $logData = DbBase::getRowBy('s_power_position_power_id', 'l_id,l_positionid,l_powerid,l_adduid,l_addtime', "l_positionid={$positionId} AND l_powerid={$tmpPowerid}");
            if($logData) {
                $logid = $logData['l_id'];
            } else {
                $newData = [
                    'l_positionid' => $positionId,
                    'l_powerid' => $tmpPowerid,
                    'l_adduid' => $userId,
                    'l_addtime' => $myTime
                ];
                DbBase::insertRows('s_power_position_power_id', $newData);
                $logid = DbBase::lastInsertId();;
            }
            $powerLogIds[] = $logid;
        }
        $powerLogIds = join(',', $powerLogIds);
        DbBase::deleteBy('s_power_position_power_id', "l_positionid={$positionId} AND l_id NOT IN({$powerLogIds})");//移除废弃的索引
    }

}




