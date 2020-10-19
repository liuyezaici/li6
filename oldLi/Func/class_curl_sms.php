<?php
/**
 *  php 以curl模式发送短信
 *  Create On 2015/7/27
 *  Author rui
 **/
class curl_sms {
    public $expires = 5;//短信过期时间,单位：分
    public $interval = 1; //短信发短间隔时间,单位：分。  防止恶意获取短信
    public $last_send_session_name = 'last_send_sms_time'; //最后一次发送时间的session 名字

    public function __construct() {
    }
    //短信模版设置
    protected $all_sms_data = array(
        'open' => true,
        'get_code' => '【li6】您的验证码为：#code#,有效时间#time#分钟,请妥善保管好您的信息,勿将验证码告知他人！'
    );

    //将用户的所有短信息状态改为失效 过期
    public static function updataSmsStatus($sms_sid, $newstatus = 2, $flag='sms_userid'){
        $db = mysql::getInstance();
        $vartab2 = array(
            'sms_use'=> $newstatus,
        );
        return DbBase::updateByData('s_sms_list', $sms_sid, $vartab2, $flag);
    }
    /*
     * 获取单条短信的记录 从 s_sms_list表
     * 获取会员最新一条短信记录
     */
    public static function getLastSms($sid, $fields='*', $flag="sms_userid"){
        $db = mysql::getInstance();
        return DbBase::getRowBy("s_sms_list", $fields, $flag." = '". $sid ."' order by sms_id desc limit 1");
    }
    // 短信的发送间隔时间 防止重复发送
    public function sms_send_interval(){
        @session_start();
        $sms_interval = $this->interval;
        $now_time = time();
        //如果没有发送过验证信息
        if(!isset($_SESSION[$this->last_send_session_name])){
            $_SESSION[$this->last_send_session_name] = $now_time;//时间戳
            return true;
        } else if( $now_time - $_SESSION[$this->last_send_session_name]  > ($sms_interval*60) ) { //如果当前时间 减去 上一次记录的发送时间 大于 系统定义的 时间间隔
            $_SESSION[$this->last_send_session_name] = $now_time;//时间戳
            return true;
        } else {
            return false;
        }
    }
    /*
     * 获取短信模版,单条
     * sms_moban_id 短信ID
     * sms_content  短信模版内容 ，需要替换的 [user_real_name] => 用户名 ,[code]=>验证码，[time]=>有效时间
     * sms_mark     短信标识
     * sms_is_open  短信开关，0关，1开
     * */
    public static function getSmsMoban($moban_id,$fields='*'){
        $db = mysql::getInstance();
        return DbBase::getRowBy("s_sms_moban", $fields, "sms_moban_id = '". $moban_id ."'");
    }
    /*
     * 获取单条短信
     * */
    public static function getOneSms ($sms_id, $fields='*'){
        $db = mysql::getInstance();
        return DbBase::getRowBy("s_sms_list", $fields, "sms_id = '". $sms_id ."'");
    }
    //直接发送短信
    public function sendSmsForCrm($phone='',$content) {
        $url = "http://106.3.37.50:9999/sms.aspx";//指定模版发送
        $postData = array(
            'userid' => '5114',
            'account' => 'kbwlsc',
            'password' => '666888',
            'mobile' => $phone,
            'content' => $content, //短信内容
            'action' => 'send',
        );
        $msgData = Func::post_nr_str($url, '', $postData);
        $p = xml_parser_create();
        xml_parse_into_struct($p, $msgData, $vals, $index);
        xml_parser_free($p);
        $vals = $vals[1];
        $status = isset($vals['value']) ? strtolower($vals['value']) : '';
        $errMsg = 0;//发送状态：0 发送中，1接口返回已成功，2发送失败
        if($status == 'success') {
            $errMsg = 1;
        }
        return $errMsg;

    }

    // 发送短信
    public function sendSms($phone='', $userid_=0, $moban_name, $postStr, $code='') {
        $db = mysql::getInstance();
        $smsData = $this->all_sms_data;
        $sms_moban_content = $smsData[$moban_name];
        if($postStr && strstr($postStr, '&')) {
            $postArray = explode('&', $postStr);
            foreach($postArray as $n => $v) {
                $array_ = explode('=', $v);
                $sms_moban_content = str_replace($array_[0], $array_[1], $sms_moban_content);
            }
        }
        $url = "http://106.3.37.50:9999/sms.aspx";//指定模版发送
        $postData = array(
            'userid' => '5255',
            'account' => 'rui6ye',
            'password' => 'Ygmlxomlg6',
            'mobile' => $phone,
            'content' => $sms_moban_content . ' 退订回T', //短信内容
            'action' => 'send',
        );
        $msgData = Func::post_nr_str($url, '', $postData);
        $p = xml_parser_create();
        xml_parse_into_struct($p, $msgData, $vals, $index);
        xml_parser_free($p);
        $statusData = $vals[1];
        $messageData = $vals[3];
        $status = isset($statusData['value']) ? strtolower($statusData['value']) : '';
        $message = isset($messageData['value']) ? strtolower($messageData['value']) : '';
        $mytime = Timer::now();
        $errMsg = $status == 'success' ? 1 : 0;
        $add_data = array(
            'sms_phone'=> $phone,
            'sms_content'=> $sms_moban_content,
            'sms_userid'=> $userid_,
            'sms_mobanid'=> 0,
            'sms_code'=> $code,
            'sms_status'=> $errMsg,//发送状态：0 发送中，1接口返回已成功，2发送失败
            'sms_expires'=> 5,//分钟
            'sms_add_time'=> $mytime,
        );
        DbBase::insertRows('s_sms_list',$add_data);
        if( $status == 'success'  ) {
            return 'ok'; //返回 ok 表示 发送成功
        } else {
            return '短信接口异常,'.$message;//返回：发送失败原因
        }
    }
    //通过短信验证码绑定手机
    public function CheckSmsAndBindPhone($userid = 0, $code='', $new_phone, $mytime = '') {
        $db = mysql::getInstance();
        $userClass = new Users();//不能传入 会无法调取
        if(!$mytime) $mytime = Timer::now();
        if(!$userid) return '缺少uid';
        if(!$new_phone) return '缺少新手机';
        /* 获取该会员最新一条短信 */
        $sms_list_info = $this->getLastSms($userid, 'sms_id,sms_use,sms_code,sms_phone,sms_userid,sms_add_time', "sms_userid");
        if(!$sms_list_info){
            return message::getMessage('0380');//您尚未获取验证码
        }
        $sms_id = $sms_list_info['sms_id'];
        $sms_use = $sms_list_info['sms_use'];
        $sms_code = $sms_list_info['sms_code'];
        $sms_phone = $sms_list_info['sms_phone'];
        $add_time = $sms_list_info['sms_add_time'];
        /* 如果验证码已使用 */
        if($sms_use != 0){
            return message::getMessage('0386');//您获取验证码已过期，请重新获取
        }
        if($sms_phone != $new_phone) {
            return message::getMessage('0402');//您验证的手机和当前填写的不一致
        }
        $database_add_time = strtotime($add_time);//数据库里的短信生成时间 strtotime 变成 时间戳
        $now_time = time();//当时时间戳
        $sys_define_expires = $this->expires*60;//系统定义的短信过期时间
        //判断该信息是否过期
        if($database_add_time - $now_time > $sys_define_expires ) {
            $this->updataSmsStatus($userid, 2, "sms_userid"); //将该用户所有的短信息状态改为失效
            return message::getMessage('0381');//您获取验证码已过期，请重新获取
        }
        if($sms_code != $code) { /* 验证码不正确 */
            return message::getMessage('0382');
        }
        //如果手机号码被他人使用，判断他是否有绑定微信或QQ
        $hisInfo =  DbBase::getRowBy('c_user', "u_id,u_nick,u_email,u_pwd", "u_tel = '".$new_phone."' and u_id <> '".$userid."'");
        if ($hisInfo){
            $hisUid = $hisInfo['u_id'];
            $hisUnick = $hisInfo['u_nick'];
            $hisPwd = $hisInfo['u_pwd'];
            //手机已经被他人使用
            //判断我是什么类型的用，如果我是QQ用户，对方不能已经绑定QQ，如果我是微信用户 对方不能绑定微信 如果是邮箱用户 对方不能绑定邮箱
            $myInfo = DbBase::getRowBy('c_user', "u_regfrom,u_email", "u_id='". $userid ."'");
            $my_regfrom = $myInfo['u_regfrom'];
            if($my_regfrom == 1) { //1手机 2邮箱 3QQ 4微信
                return ('您是通过手机注册的用户,您的手机已经被'.$hisInfo['u_nick'].'使用');
            } elseif($my_regfrom == 2) { //1手机 2邮箱 3QQ 4微信
                if($hisInfo['u_email'] && Func::isEmail($hisInfo['u_email']) ) {
                    return ('您是通过邮箱注册的用户,此手机已经被邮箱用户'.$hisInfo['u_email'].'绑定');
                }
                //如果对方没有邮箱，修改对方的邮箱，并且将我的邮箱清空
                DbBase::updateByData("c_user", $hisUid, array('u_email' => $myInfo['u_email']), 'u_id');
                DbBase::updateByData("c_user", $userid, array('u_email' => ''), 'u_id');
                //系统帮会员自动登录他的用户
                $userClass->checkUser($hisUnick, $hisPwd, $isAdmin = false, $systemLogin = true);
            } elseif($my_regfrom == 3) { //1手机 2邮箱 3QQ 4微信
                $hisQQInfo = DbBase::getRowBy('c_user_qqlogin', "q_usernick", "q_uid='". $hisUid ."'");
                if($hisQQInfo) {
                    return ('您是通过QQ注册的用户,此手机已经被QQ用户'.$hisQQInfo['q_usernick'].'绑定');
                }
                //如果对方没有QQ，修改我之前的QQ绑定uid为他的Uid
                DbBase::updateByData("c_user_qqlogin", $userid, array('q_uid' => $hisUid), 'q_uid');
                //系统帮会员自动登录他的用户
                $userClass->checkUser($hisUnick, $hisPwd, $isAdmin = false, $systemLogin = true);
            } elseif($my_regfrom == 4) { //1手机 2邮箱 3QQ 4微信
                $hisWXInfo = DbBase::getRowBy('c_user_weixinlogin', "w_usernick", "w_uid='". $hisUid ."'");
                if($hisWXInfo) {
                    return ('您(uid:'.$userid.')是通过微信注册的用户,此手机已经被微信用户(uid:'. $hisUid .')'.$hisWXInfo['w_usernick'].'绑定');
                }
                //如果对方没有微信，修改我之前的微信绑定uid为他的Uid
                DbBase::updateByData("c_user_weixinlogin", $userid, array('w_uid' => $hisUid), 'w_uid');
                //系统帮会员自动登录他的用户
                $userClass->checkUser($hisUnick, $hisPwd, $isAdmin = false, $systemLogin = true);
            }
        } else {
            //此电话没有绑定过任何人，可以修改或写入电话绑定记录
            $oldUinfo = DbBase::getRowBy('c_user', "u_nick", "u_tel = '".$new_phone."' ");
            if ($oldUinfo) {
                return ('手机已经被'. $oldUinfo['u_nick'] .'占用');
            }
            //判断会员是否是首次绑定手机 如果是首次绑定手机 插入首次绑定手机的时间，并送 510个金豆
            $my_tel_settime= DbBase::getRowBy('c_user', "u_tel,u_tel_settime", "u_id = '".$userid."' ");
            if( ( $my_tel_settime['u_tel_settime'] == '' &&  $my_tel_settime['u_tel'] =='') ||( $my_tel_settime['u_tel_settime'] == '0000-00-00 00:00:00' &&  $my_tel_settime['u_tel'] =='') ){
                $get_uid = $userid; //获得金豆的用户ID
                $edit_num = $GLOBALS['cfg_reg_get_jindou_money']; //首次绑定手机号码赠送510个金豆
                $reason ='首次绑定手机号码赠送510个金豆';
                $chaozuo_userid = $GLOBALS['cfg_admin_uid']; //操作用户ID
                $ac = new account();
                $db->BeginTRAN();
                try {
                    if( Users::editUserInfo( $userid, array('u_tel_settime'=> $mytime), 'u_id') != 1){
                        throw new Exception('写入会员首次绑定时间失败',-1);
                    };
                    if( Users::editUserInfo( $userid, array('u_tel'=>$new_phone), 'u_id') != 1){
                        throw new Exception('绑定会员电话失败',-1);
                    };
                    if ( !$ac->editBeanNum($get_uid , $edit_num , $reason, $mytime, 0, $chaozuo_userid)){
                        throw new Exception('充值不成功',-1);
                    }
                    $db->CommitTRAN();
                } catch ( Exception $e ) {
                    $db->RollBackTRAN();
                    return message::getMsgJson('0089');//充值不成功
                }

            }else{
                //同时更新会员电话
                Users::editUserInfo( $userid, array('u_tel'=>$new_phone), 'u_id');
            }


        }
        return 'ok';//您的手机验证已通过
    }
}