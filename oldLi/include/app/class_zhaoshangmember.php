<?php
/**
 * 招商成员
 */
class zhaoshangmember
{
    //获取所有雇员组 29
    function getMemberListAll($fields='*',$order='desc'){
        $db = mysql::getInstance();
        $sql = "SELECT {$fields} FROM v_member_list order by g_id {$order}";
        $db->Query($sql);
        return $db->getAllRecodes(\PDO::FETCH_ASSOC);
    }
    //获取某组的所有雇员（按组id） 29
    //fields:g_id	g_user_id	g_is_leader	g_addtime	g_fid	g_desc	g_path	user_name	user_real_name
    function getMemberListBYGroupID($groupID, $fields='*'){
        $db = mysql::getInstance();
        $sql = "SELECT {$fields} FROM v_member_list where `g_fid` = '{$groupID}' order by g_order asc";
        $db->Query($sql);
        return $db->getAllRecodes(\PDO::FETCH_ASSOC);
    }
    //从招商组获取单个雇员（按组id） 29
    //fields:g_id	g_user_id	g_is_leader	g_addtime	g_fid	g_desc	g_path	user_name	user_real_name
    function getMemberInfoBYID($id,$fields='*'){
        $db = mysql::getInstance();
        $sql = "SELECT {$fields} FROM v_member_list where `g_id`='{$id}'";
        $db->Query($sql);
        return $db->getCurRecode(\PDO::FETCH_ASSOC);
    }
    //从招商组获取单个雇员（按会员_id） 29
    function getMemberInfoBYUserID($user_id,$fields='*'){
        $db = mysql::getInstance();
        $sql = "SELECT {$fields} FROM v_member_list where `g_user_id`='{$user_id}'";
        $db->Query($sql);
        return $db->getCurRecode(\PDO::FETCH_ASSOC);
    }

    // 批量添加雇员到招生组[c_member]  username 29
    function bacthAddMembers($data,$operator_id){
        $db = mysql::getInstance();

        $ids = explode(',',$data['user_id']);

        foreach($ids as $value){
            $num = DbBase::ifExist('c_user',"u_id = '".$value."' ");
            if ($num == 0){
                return (message::getMsgJson('0097'));//用户不存在
            }
            /*判断新加的会员是否存在c_power_group*/
            $num = DbBase::ifExist('c_member',"g_user_id = '".$value."' ");
            if ($num > 0){
                return (message::getMsgJson('0100'));//用户id是否存在c_member表
            }
        }
        $time_ = Func::ntime();
        foreach($ids as $value){
            $newData = array();
            $newData['g_user_id'] = $value;
            $newData['g_addtime'] = $time_;
            $newData['g_desc'] = urldecode($data['desc']);
            $newData['g_is_leader'] = $data['is_leader'];
            /* 如果添加的是组员，那么要加上组长的 ID */
            if($data['is_leader']==0){
                if($data['fid2']!=0){
                    $newData['g_fid'] = $data['fid2'];
                    $newData['g_path'] = 3;
                }
                else{
                    $newData['g_fid'] = $data['fid'];
                    $newData['g_path'] = 2;
                }
            }
            DbBase::insertRows('c_member', $newData);
            $log_data= array(
                'log_operator_uid' => $operator_id,
                'log_desc' => '添加雇员',
                'log_target_uid'=>  $newData['g_user_id'],
                'log_type'=> 'member',
                'log_addtime'=> $time_,
            );
            DbBase::insertRows('c_userlog', $log_data);//日志
        }
        return (message::getMsgJson('0113')); //添加成功
    }
    //检测雇员是否在某个部门里
    public function getPartmentId($employid=0) {
        $db = mysql::getInstance();
        if(!$employid) return 0;
        //判断雇员是组员还是组长
        $employInfo = $this->getMemberInfoBYUserID($employid, "g_id,g_fid,g_path");
        if(count($employInfo) == 0) return 0;
        $g_id = $employInfo['g_id'];
        $g_fid = $employInfo['g_fid'];
        $g_path = $employInfo['g_path'];
        //如果业务员是组员 ,获取其组长的上级id
        if($g_path == 3) {
            $employInfo2 = $this->getMemberInfoBYID($g_fid, "g_fid");
            if(count($employInfo2) == 0) return 0;
            $g_fid = $employInfo2['g_fid'];
            return $g_fid;
        } elseif ($g_path == 2) { //如果业务员是组长 ,获取其部门id
            return $g_fid;
        } else { //自己就是部门经历
            return $employid;
        }
    }

}