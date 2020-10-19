<?php
/**
 * 发送短信的接口
 */
class sendsms{
    public $wsdl_url; /* 短信接口地址 */
    public $acount; /* 短信接口的登录用户名 */
    public $password; /* 短信接口的登录密码 */

    /*
     * 查询用户是否愿意接收短信
     * $moban_id 当前短信模版ID
     * $user_diy_moban_id 用户定制的接收ID
     * 如果是 all 那么直接return true
     * 如果是0 表示全部不接收
     * */
    public function  check_send_sms($moban_id,$user_diy_moban_id){
        //all 表示接收全部
        if($user_diy_moban_id=='all'){
            return true;
        }
        //表示不接收任意
        else if($user_diy_moban_id=='0' || $user_diy_moban_id==''){
            return false;
        }
        //分割成数组
        $user_diy_moban_id_array = explode(',',$user_diy_moban_id);
        //检查当前的短信模版是否存在用户定制的模版数组
        if(in_array($moban_id,$user_diy_moban_id_array)){
            return true;
        }
        return false;
    }

    // 手机号码验证
    public function checkMobileValidity($mobile_phone){
        $exp = "/^13[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$|15[0123456789]{1}[0-9]{8}$|17[0123456789]{1}[0-9]{8}$|18[0123456789]{1}[0-9]{8}$/";
        if(preg_match($exp,$mobile_phone)){
            return true;
        }else{
            return false;
        }
    }
    // 手机号码归属地
    public function checkMobilePlace($mobile_phone){
        $url = "http://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel=".$mobile_phone."&t=".time();
        $content = file_get_contents($url);
        $p = substr($content, 56, 4);
        $mo = substr($content, 81, 4);
        return $str = conv2utf8($p).conv2utf8($mo);
    }
    // 短信的发送间隔时间 防止重复发送
    public static function sms_send_interval(){
        global $cfg;
        @session_start();
        $sms_interval = $cfg['sms']['interval'];
        $now_time = time();
        //如果没有发送过验证信息
        if(!isset($_SESSION['sms_send_expires'])){
            $_SESSION['sms_send_expires'] = $now_time;//时间戳
            return true;
        } else if( $now_time - $_SESSION['sms_send_expires']  > ($sms_interval*60) ){ //如果当前时间 减去 上一次记录的发送时间 大于 系统定义的 时间间隔
            $_SESSION['sms_send_expires'] = $now_time;//时间戳
            return true;
        }
        return false;
    }

    //添加手机号码到 c_phone_list 表
    public static function add_phone($phone,$user_id){
        $db = mysql::getInstance();
        $time = Func::ntime();
        $data = array(
            'p_number'=>$phone,
            'p_userid'=>$user_id,
            'p_add_time'=>$time,
            'p_is_validate' => 1
        );
        if( DbBase::insertRows('c_phone_list',$data)!=1  ) {
            return false;//添加失败
        } else {
            return true;//添加成功
        }
    }

    /*
     * 从 c_phone_list 表 获取信息
     * 获取单条
     * */
    public static function get_info_from_c_phone_list($user_id, $fields='*'){
        $db = mysql::getInstance();
        $sql = "SELECT ". $fields ." FROM c_phone_list WHERE p_userid = '". $user_id ."'";
        $db->Query($sql);
        return $db->getCurRecode(\PDO::FETCH_ASSOC);
    }

    /*
     * 从 c_phone_list 表 获取信息
     * 获取组
     * */
    public static function get_list_from_c_phone_list($user_ids, $fields='*'){
        $db = mysql::getInstance();
        $sql = "SELECT ". $fields ." FROM c_phone_list WHERE p_userid in (". $user_ids .")";
        $db->Query($sql);
        return $db->getAllRecodes(\PDO::FETCH_ASSOC);
    }

    /*
     * 从 c_phone_list 表 移除
     * 移除单条
     * */
    public static function delete_c_phone_list_by_user_id($user_id){

        $db = mysql::getInstance();

        //移除
        $sql = "DELETE FROM c_phone_list WHERE p_userid = '". $user_id ."'";
        $db->Query($sql);
        //减金币
        $sql = "update c_account set ac_score=ac_score-1 WHERE ac_id = '". $user_id ."'";
        $db->Query($sql);

    }


    /*
     * 获取短信模版,单条
     * sms_moban_id 短信ID
     * sms_content  短信模版内容 ，需要替换的 [user_real_name] => 用户名 ,[code]=>验证码，[time]=>有效时间
     * sms_mark     短信标识
     * sms_is_open  短信开关，0关，1开
     * */
    public static function get_sms_moban_one($moban_id,$fields='*'){
        $db = mysql::getInstance();
        $sql = "SELECT ". $fields ." FROM s_sms_moban WHERE sms_moban_id = '". $moban_id ."'";
        $db->Query($sql);
        return $db->getCurRecode(\PDO::FETCH_ASSOC);
    }

    /*
     * 从 s_sms_list 表 获取信息
     * 获取单条
     * */
    public static function get_sms_one_by_sms_id($sms_id, $fields='*'){
        $db = mysql::getInstance();
        $sql = "SELECT ". $fields .",c_user_1.* FROM s_sms_list
        LEFT JOIN
        (
            SELECT u_name,u_id,u_type from c_user
        ) c_user_1 on c_user_1.u_id=s_sms_list.sms_userid
        WHERE sms_id = '". $sms_id ."'";
        $db->Query($sql);
        return $db->getCurRecode(\PDO::FETCH_ASSOC);
    }

    /*
     * 保存短信记录 到 s_sms_list表
     * 字段如下
     * sms_phone
     * sms_content
     * sms_userid
     * sms_type
     * sms_code
     * sms_status
     * sms_send_userid
     * sms_wsdl
     * sms_expires
     * sms_add_time
     * */
    public static function add_to_sms_list( $data=array()){
        $db = mysql::getInstance();
        $time = Func::ntime();
        $sms_wsdl = isset($data['sms_wsdl']) ? intval($data['sms_wsdl']):1;
        $add_data = array(
            'sms_phone'=>$data['sms_phone'],
            'sms_content'=>$data['sms_content'],
            'sms_userid'=>$data['sms_userid'],
            'sms_type'=>$data['sms_type'],
            'sms_code'=>$data['sms_code'],
            'sms_status'=>$data['sms_status'],//发送状态：0，发送中，1接口返回已成功，2发送失败
            'sms_send_userid'=>$data['sms_send_userid'],
            'sms_wsdl'=>$sms_wsdl,
            'sms_expires'=>$data['sms_expires'],
            'sms_add_time'=>$time,
        );

        if( DbBase::insertRows('s_sms_list',$add_data)!=1  ) {
            return false;//添加失败
        }
        else{
            return true;//添加成功
        }
    }
    /*
     * 获取单条短信的记录 从 s_sms_list表
     * 获取会员最新一条短信记录
     */
    public static function get_sms_list_lastOne( $user_id, $fields='*'){
        $db = mysql::getInstance();
        $sql = "SELECT ". $fields ." FROM s_sms_list WHERE sms_userid = '". $user_id ."' order by sms_id desc limit 1";
        $db->Query($sql);
        return $db->getCurRecode(\PDO::FETCH_ASSOC);
    }

    //修改单条短信
    public static function edit_sms_one($sms_id, $vartab){
        $db = mysql::getInstance();
        return DbBase::updateByData('s_sms_list', $sms_id, $vartab, $flag='sms_id');
    }

    //短信验证成功
    public static function validate_reg_success($user_id,$sms_id){
        $db = mysql::getInstance();
        $time = Func::ntime();
        $vartab1 = array(
            'p_is_validate'=>1,
            'p_validate_time'=>$time
        );
        DbBase::updateByData('c_phone_list', $user_id, $vartab1, $flag='p_userid','p_receive_count');

        $vartab2 = array(
            'sms_use'=>1,
        );
        DbBase::updateByData('s_sms_list', $sms_id, $vartab2, $flag='sms_id');

        return true;
    }

    //将用户的所有短信息状态改为失效 过期
    public static function validate_expires($sms_uid){
        $db = mysql::getInstance();
        $vartab2 = array(
            'sms_use'=> 2,
        );
        return DbBase::updateByData('s_sms_list', $sms_uid, $vartab2, $flag='sms_userid');
    }


    /*
     * 新的短信群发，目前用于任务计划
     * */
    public static function send_one($phone,$content,$sms_userid,$sms_send_userid=1,$rand_code,$sms_moban_id,$customMsgID='',$vipFlag='false'){

        $send_data = array(
            0=>array(
                'phone'=>$phone,
                'sms_content'=>$content,
                'user_id'=>$sms_userid,
                'send_user_id'=>$sms_send_userid,
                'rand_code'=>$rand_code,
                'sms_moban_id'=>$sms_moban_id,
                'customMsgID'=>$customMsgID,
                'vipFlag'=>$vipFlag,
            )
        );
        //添加到任务表
        self::add_to_task($send_data);
    }

    /*
     * 发送单条短信
     * sms_userid   接收者ID
     * sms_send_userid 发送者id 一般默认是管理员 1
     */
    public static function send_one_new($phone,$content,$sms_userid,$sms_send_userid=1,$rand_code,$sms_moban_id,$customMsgID='',$vipFlag='false'){
        global $cfg;
        $sms_wsdl = 1;
        $smsInfo = $cfg['sms'][$sms_wsdl];
        $sms_expires = $cfg['sms']['expires'];
        $wsdl = $smsInfo['url'];
        try{
            $client = new SoapClient($wsdl);
            $points = $client->PostGroup(
                array(
                    'account'=>$smsInfo['verify']['account'],
                    'password'=>$smsInfo['verify']['password'],
                    'subid'=>'',
                    'mssages'=>array(
                        'MessageData'=>array(
                            0 =>array(
                                'Phone'=>$phone,
                                'Content'=>$content,
                                'customMsgID'=>$customMsgID,
                                'vipFlag'=>$vipFlag,
                            )
                        )
                    )
                )
            );
            if($sms_wsdl==1){
                /*
                 * $points->PostGroupResult
                  0	成功
                -1	账号无效
                -2	参数：无效
                -3	连接不上服务器
                -5	无效的短信数据，号码格式不对
                -6	用户名密码错误
                -7	旧密码不正确
                -9	资金账户不存在
                -11	包号码数量超过最大限制
                -12	余额不足
                -13	账号没有发送权限
                -99	系统内部错误
                -100	其它错误
                */

                $status = $points->PostGroupResult==0 ? 1:2;
            }
            $time = Func::ntime();
            $add_data = array(
                'sms_phone'=>$phone,
                'sms_content'=>$content,
                'sms_userid'=>$sms_userid,
                'sms_type'=>$sms_moban_id,
                'sms_code'=>$rand_code,
                'sms_status'=>$status,//发送状态：0，发送中，1 api接口返回已发送成功，2 api接口返回发送失败，3 连接api接口失败，发送不成功
                'sms_send_userid'=>$sms_send_userid,
                'sms_wsdl'=>$sms_wsdl,
                'sms_expires'=>$sms_expires,
                'sms_add_time'=>$time,
            );
            self::add_to_sms_list($add_data);
            return true;
        }
        catch(Exception $e){

            if($sms_wsdl==1){
                $status = 3;
            }
            $time = Func::ntime();
            $add_data = array(
                'sms_phone'=>$phone,
                'sms_content'=>$content,
                'sms_userid'=>$sms_userid,
                'sms_type'=>$sms_moban_id,
                'sms_code'=>$rand_code,
                'sms_status'=>$status,//发送状态：0，发送中，1 api接口返回已发送成功，2 api接口返回发送失败，3 连接api接口失败，发送不成功
                'sms_send_userid'=>$sms_send_userid,
                'sms_wsdl'=>$sms_wsdl,
                'sms_expires'=>$smsInfo['expires'],
                'sms_add_time'=>$time,
            );
            self::add_to_sms_list($add_data);
            return false;/* 连接api接口失败，发送不成功 */
        }

    }

    //把短信添加到任务表
    public static function add_to_task($send_data){
        $send_data = addslashes(json_encode($send_data));

        $db = mysql::getInstance();

        $add_data = array(
            'send_data'=>$send_data,
            'send_status'=>0,
        );

        if( DbBase::insertRows('s_sms_task',$add_data)!=1 ) {
            return false;//添加失败
        }
        else{
            return true;//添加成功
        }

    }

    /*
     * 群发短信
     * 数量上限：未知
     * $data 的结构必须是如下
     *
     * $data => array(
     *          1 =>array(
                    'Phone'=>'号码1',
                    'Content'=>'测试短信1。',
                    'customMsgID'=>'',
                    'vipFlag'=>'false',
                ),
                2=>array(
                    'Phone'=>'号码2',
                    'Content'=>'测试短信试试2',
                    'customMsgID'=>'',
                    'vipFlag'=>'false',
                )
        )
     *
     * */
    public static function send_all($data){

        //添加到任务表
        self::add_to_task($data);
    }

    /*
     * 新的短信群发，目前用于任务计划
     * */
    public static function send_all_new($data){
        global $cfg;
        $sms_wsdl = 1;
        $smsInfo = $cfg['sms'][$sms_wsdl];
        $sms_expires = $cfg['sms']['expires'];
        $wsdl = $smsInfo['url'];

        $send_data = array();
        foreach($data as $value){
            $send_data_one = array(
                'Phone'=>$value['phone'],
                'Content'=>$value['sms_content'],
                'customMsgID'=>'',
                'vipFlag'=>'false',
            );
            array_push($send_data,$send_data_one);
        }

        try{
            $client = new SoapClient($wsdl);
            $points = $client->PostGroup(
                array(
                    'account'=>$smsInfo['verify']['account'],
                    'password'=>$smsInfo['verify']['password'],
                    'subid'=>'',
                    'mssages'=>array(
                        'MessageData'=> $send_data
                    )
                )
            );

            if($sms_wsdl==1){
                /*
                 * $points->PostGroupResult
                  0	成功
                -1	账号无效
                -2	参数：无效
                -3	连接不上服务器
                -5	无效的短信数据，号码格式不对
                -6	用户名密码错误
                -7	旧密码不正确
                -9	资金账户不存在
                -11	包号码数量超过最大限制
                -12	余额不足
                -13	账号没有发送权限
                -99	系统内部错误
                -100	其它错误
                */

                $status = $points->PostGroupResult==0 ? 1:2;
            }
            $time = Func::ntime();
            foreach($data as $value){
                $add_data = array(
                    'sms_phone'=>$value['phone'],
                    'sms_content'=>$value['sms_content'],
                    'sms_userid'=>$value['user_id'],
                    'sms_type'=>$value['sms_moban_id'],
                    'sms_code'=>$value['rand_code'],
                    'sms_status'=>$status,//发送状态：0，发送中，1 api接口返回已发送成功，2 api接口返回发送失败，3 连接api接口失败，发送不成功
                    'sms_send_userid'=>$value['send_user_id'],
                    'sms_wsdl'=>$sms_wsdl,
                    'sms_expires'=>$sms_expires,
                    'sms_add_time'=>$time,
                );
                self::add_to_sms_list($add_data);
            }
            return true;
        }
        catch(Exception $e){
            if($sms_wsdl==1){
                $status = 3;
            }
            $time = Func::ntime();
            foreach($data as $value){
                $add_data = array(
                    'sms_phone'=>$value['phone'],
                    'sms_content'=>$value['sms_content'],
                    'sms_userid'=>$value['user_id'],
                    'sms_type'=>$value['sms_moban_id'],
                    'sms_code'=>$value['rand_code'],
                    'sms_status'=>$status,//发送状态：0，发送中，1 api接口返回已发送成功，2 api接口返回发送失败，3 连接api接口失败，发送不成功
                    'sms_send_userid'=>$value['send_user_id'],
                    'sms_wsdl'=>$sms_wsdl,
                    'sms_expires'=>$sms_expires,
                    'sms_add_time'=>$time,
                );
                self::add_to_sms_list($add_data);
            }
            return false;/* 连接api接口失败，发送不成功 */
        }

    }

}