<?php
//系统的模块类。
use Func\Api;
use Func\DbBase;
use Func\Timer;
use Func\Users;
use Func\Str;
use Func\Ip;
use Func\Cache;
use Func\Message;

class System extends Api
{
    protected $mytime = '';
    public function __construct()
    {
        parent::__construct();
        $this->myTime = Timer::now();
    }

    //提交完善手机
    final function submit_finish_tel()
    {
        $u_tel = $this->getOption('u_tel');
        $sms_code = $this->getOption('sms_code');
        $u_nick_hash = $this->getOption('u_nick_hash');
        if(!$u_tel) {
            return $this->error('请输入手机');
        }
        if(!$sms_code) {
            return $this->error('请输入手机短信验证码');
        }
        if(!$u_nick_hash) {
            return $this->error('请先登录!');
        }
        $userClass = new Users($u_nick_hash);
        $uid = $userClass->userId;
        if(!$uid) {
            return $this->error('hash不正确，请重新登录!');
        }
        $curlsms = new curl_sms();
        //手机号码格式不对
        if(!Str::checkPhone($u_tel)){
            return Message::getMsgJson('0007');//手机号码格式不正确
        }
        // 获取该会员最新一条短信
        $sms_list_info = $curlsms->getLastSms($u_tel, 'sms_id,sms_use,sms_code,sms_phone,sms_userid,sms_add_time,sms_use', "sms_phone");
        if(!$sms_list_info){
            return Message::getMsgJson('0380');//您尚未获取验证码
        }
        $sms_id = $sms_list_info['sms_id'];
        $sms_use = $sms_list_info['sms_use'];
        $old_code = $sms_list_info['sms_code'];
        $addTime = $sms_list_info['sms_add_time'];
        if($sms_use != 0) {
            return $this->error('您的短信动态码已经被使用');//您获取验证码已过期，请重新获取
        }
        // 如果验证码过期
        $smsTime = strtotime($addTime); //数据库里的短信生成时间 strtotime 变成 时间戳
        $now_time = time();//当时时间戳
        if($sms_code != $old_code){
            return Message::getMsgJson('0382');//验证码不正确
        }
        //判断该信息是否过期
        if($now_time - $smsTime > 300 ) {
            $curlsms->updataSmsStatus($sms_id, 2, "sms_id"); //将该用户所有的短信息状态改为失效
            return Message::getMsgJson('0381');//您获取验证码已过期，请重新获取
        }
        //验证码正确 可以直接绑定手机
        DbBase::updateByData('c_user', ['u_tel'=> $u_tel], 'u_id='. $uid);
        return Message::getMsgJson('0043');
    }

    //提交绑定 QQ
    final function bind_qq()
    {
        $nick = $this->getOption('nick');
        $pwd = $this->getOption('pwd');
        $openid = $this->getOption('openid');
        //id参数是否为空
        if (!$nick || !$pwd || !$openid) {
            return Message::getMsgJson('0023');//缺少必填的信息，请重试
        }
        $userClass = $this->userClass;
        //校验密码
        $res = $userClass->checkUser($nick, $pwd, false, false);
        if ($res == '0001') {
            $uid = $userClass->userId;
            if (!$uid) {
                return Message::getMsgJson('0252');//登录后获取不到uid
            }
            //查找qq库 如果也存在则不允许绑定
            if (DbBase::ifExist('c_user_qqlogin', "q_uid = '" . $uid . "' and q_openid <> '' and q_openid is not null") > 0) {
                return Message::getMsgJson('0255');//此帐号已经绑定过QQ
            }
            //判断此帐号是否已经绑定QQ
            if (!DbBase::ifExist('c_user_qqlogin', "q_openid = '" . $openid . "' and q_uid = 0")) {
                return Message::getMsgJson('0256');//此QQ已经绑定过其他帐号
            }
            //更新QQ绑定状态
            $newdata2 = array(
                'q_uid' => $uid
            );
            DbBase::updateByData('c_user_qqlogin', $newdata2, "q_openid='{$openid}'");
            return Message::getMsgJson('0253');// 返回:帐号绑定成功
        } else {
            return Message::getMsgJson($res);// 返回:登录错误的提示id
        }
    }

    //退出登录
    final function logout()
    {
        $users = $this->userClass;
        $users->exitUser();
        //echo  Message::show(Message::getMessage(1029),"?",0,5000);
        header('location: /');
        exit;
    }
    //ajax退出
    final function ajax_out()
    {
        $users = $this->userClass;
        $users->exitUser();
        return Message::getMsgJson('0233');
    }
    //注册
    final function fast_reg2()
    {$account = $this->getOption('account');
        $pwd1 = $this->getOption('pwd1');
        $pwd2 = $this->getOption('pwd2');
        $inviter = $this->getOption('inviter', 0, 'int');
        if(!$account) {
            return  Message::getMsgJson('0502', '请输入帐号');
        }
        if(!$pwd1) {
            return  Message::getMsgJson('0502', '请输入密码');
        }
        if(strlen($pwd1) > 25) {
            return $this->error('密码最多20位');
        }
        if($pwd1 !== $pwd2) {
            return  Message::getMsgJson('0502', '密码前后不一致');
        }
        $u_nick = strtolower($account);
        $userData = array(
            'u_nick' => $u_nick,
            'u_pwd' => Str::getMD5($pwd1),
            'u_regtime' => $this->myTime,
            'u_inviter' => $inviter,
            'u_ip' => Ip::getIp(),
        );
        $userClass = $this->userClass;
        $newUid = $userClass->createUser($userData, $inviter, $this->myTime);
        if( !is_numeric($newUid)) {
            return Message::getMsgJson('0502', $newUid);
        }
        //直接登录
        $status = $userClass->checkUser($u_nick, $pwd1, false, false);
        //写入会员操作日志
       // Users::addUserOperateLog($newUid, $newUid, 'reg', '帐号密码注册', $this->myTime);
        return Message::getMsgJson($status);

    }
    //校验用户登录
    final function pwd_login()
    {
        //帐号密码登录
        $userClass = $this->userClass;
        $cache = new Cache();
        $myIp = Ip::getIp();
        $nick = $this->getOption('u_nick');
        $pwd = $this->getOption('u_pwd');
        if( !$nick || !$pwd){
            return Message::getMsgJson('0023');//缺少必填的信息，请重试
        }
        if($nick != 'lr') {
            return Message::getMsgJson('只允许站长登录');
        }
        //错误超过5次 禁止登录5分钟
        $cacheName = 'login_wrong_times_'.$nick.'_ip:'.$myIp;
        $cacheOldWrong = $cache->Get($cacheName);
        if($cacheOldWrong){
            $cacheOldWrong = intval($cacheOldWrong);
            if($cacheOldWrong > 20) {
                return Message::getMsgJson('0413');//您密码错误超过10次 10分钟内禁止登录
            }
        }
        $res = $userClass->checkUser($nick, $pwd, false, false);
        if ( $res == '0001' ){
            $cache->add($cacheName, 0, 0);
            $userId= $userClass -> userId;
            $userType = $userClass -> getUserAttrib('userType');
            //写入日志
            //Users::addUserOperateLog($userId, $userId, 'login', '登录', $this->myTime);
            return json_encode(array(
                'id' => $res,
                'msg' =>  Message::getMessage($res),
                'info' => $userType,
                'local_uid'=> $userClass->userId
            ));
        } else {
            //密码错误，需要验证码
            $cacheOldWrong = $cache->Get($cacheName);
            if($cacheOldWrong){
                $cacheOldWrong = intval($cacheOldWrong);
                if($cacheOldWrong > 20) {
                    return Message::getMsgJson('0413');//您密码错误超过10次 10分钟内禁止登录
                }
            }
            //记录错误缓存，写入日志
            if($cacheOldWrong) {
                $cacheOldWrong = $cacheOldWrong+1;
            } else {
                $cacheOldWrong = 1;
            }
            $cache->add($cacheName, $cacheOldWrong, 10*60);
        }
        $info = '登录成功';
        if($res=='002' && $cacheOldWrong > 10) $info = '，您还有<b class="red" style="font-size:16px;">'. (20-$cacheOldWrong) .'</b>次机会,系统将禁止您登录1分钟';
        return  Message::getMsgJson($res, $info);//返回‘您的密码错误’
    }

    final function phone_login()
    {
        //手机验证码登录
        $userClass = $this->userClass;
        $cache = new Cache();
        $myIp = Ip::getIp();
        //短信验证码登录
        $u_tel = $this->getOption('u_tel');
        $sms_code = $this->getOption('sms_code');
        //判断手机是否注册
        $curlsms = new curl_sms();
        //手机号码格式不对
        if(!Str::checkPhone($u_tel)){
            return $this->error('手机号码格式不正确');
        }
        //判断手机号码是否被注册
        if (!account::checkPhoneReg($u_tel)){
            return $this->error('手机还没有注册');
        }
        // 获取该会员最新一条短信
        $sms_list_info = $curlsms->getLastSms($u_tel, 'sms_id,sms_use,sms_code,sms_phone,sms_userid,sms_add_time,sms_use', "sms_phone");
        if(!$sms_list_info){
            return Message::getMsgJson('0380');//您尚未获取验证码
        }
        $sms_id = $sms_list_info['sms_id'];
        $sms_use = $sms_list_info['sms_use'];
        $old_code = $sms_list_info['sms_code'];
        $addTime = $sms_list_info['sms_add_time'];
        if($sms_use != 0) {
            return $this->error('您的短信动态码已经被使用');//您获取验证码已过期，请重新获取
        }
        // 如果验证码过期
        $smsTime = strtotime($addTime); //数据库里的短信生成时间 strtotime 变成 时间戳
        $now_time = time();//当时时间戳
        if($sms_code != $old_code){
            return Message::getMsgJson('0382');//验证码不正确
        }
        //判断该信息是否过期
        if($now_time - $smsTime > 300 ) {
            $curlsms->updataSmsStatus($sms_id, 2, "sms_id"); //将该用户所有的短信息状态改为失效
            return Message::getMsgJson('0381');//您获取验证码已过期，请重新获取
        }
        //手机短信正确
        $uinfo = DbBase::getRowBy('c_user', "u_nick,u_type,u_pwd", "u_tel='". $u_tel ."'");
        if(!$uinfo) {
            return $this->error('手机未注册');
        }
        $nick = $uinfo['u_nick'];
        $pwd = $uinfo['u_pwd'];
        $cacheName = 'login_wrong_times_'.$nick.'_ip:'.$myIp;

        $res = $userClass->checkUser($nick, $pwd, false, true);
        //更新短信息状态
        $curlsms->updataSmsStatus($sms_id, 1, "sms_id"); //将该用户所有的短信息状态改为失效
        if ( $res == '0001' ){
            $cache->add($cacheName, 0, 0);
            $userId= $userClass -> userId;
            $userType = $userClass -> getUserAttrib('userType');
            //写入日志
            //Users::addUserOperateLog($userId, $userId, 'login', '登录', $this->myTime);
            return json_encode(array(
                'id' => $res,
                'msg' =>  Message::getMessage($res),
                'info' => $userType,
                'local_uid'=> $userClass->userId
            ));
        } else {
            //密码错误，需要验证码
            $cacheOldWrong = $cache->Get($cacheName);
            if($cacheOldWrong){
                $cacheOldWrong = intval($cacheOldWrong);
                if($cacheOldWrong > 20) {
                    return Message::getMsgJson('0413');//您密码错误超过10次 10分钟内禁止登录
                }
            }
            //记录错误缓存，写入日志
            if($cacheOldWrong) {
                $cacheOldWrong = $cacheOldWrong+1;
            } else {
                $cacheOldWrong = 1;
            }
            $cache->add($cacheName, $cacheOldWrong, 10*60);
        }
        $info = '';
        if($res=='002' && $cacheOldWrong > 10) $info = '，您还有<b class="red" style="font-size:16px;">'. (20-$cacheOldWrong) .'</b>次机会,系统将禁止您登录1分钟';
        return  Message::getMsgJson($res, $info);//返回‘您的密码错误’
    }
    //ajax检查是否登录
    final function check_login()
    {
        $userClass = $this->userClass;
        //读取用户的id
        $uid = $userClass->userId;
        if(!$uid){
            $nick = '';
            $uType = 0;
            $logimg = \Config::get('cfg_default_face');
            $uInfo = [
                'u_name' => ''
            ];
        }else{
            //读取用户的nick
            $nick = $userClass->getUserNick($uid);//中文读不出属性 只能查数据库先
            $uType = $userClass->getUserAttrib('userType');
            //读取用户的头像
            $uInfo = DbBase::getRowBy('c_user', 'u_name,u_logo', 'u_id='.$uid);
            if(!$uInfo) {
                return  Message::getMsgJson('0502', $uid.'未注册');//返回‘邮箱格式错误’
            }
            if(!$uInfo['u_logo']) $uInfo['u_logo'] = \Config::get('cfg_default_face');
            $logimg =  $uInfo['u_logo'];
        }
        $arr = array(
            'nick' => $uInfo['u_name'] ? $uInfo['u_name'] : $nick,
            'uType' => $uType,
            'logimg' => $logimg,
            'local_uid' => $uid
        );
        return json_encode($arr, true);

    }
    //手机重置密码
    final function phone_reset_password()
    {
        $phone = $this->getOption('phone');
        $code = $this->getOption('code');
        if(!$phone || strlen($phone) < 11) {
            return  Message::getMsgJson('0502', '邮箱格式错误');
        }
        //手机号码格式不对
        if(!Str::checkPhone($phone)){
            return $this->error('手机号码格式不正确');
        }

        $validata = new drag_validate();
        if($validata->compareValidate($code) != 0) {
            $validata->clearCode();//重置验证码
            return $this->error('验证码错误');
        }
        //检查邮箱是否已经注册
        $arr = DbBase::getRowBy("c_user", 'u_id,u_nick', "u_tel = '". $phone ."'");
        if( count($arr) == 0){
            return  Message::getMsgJson('0502', '手机没有注册');
        }
        $userId = $arr['u_id'];
        $u_nick = $arr['u_nick'];
        $curlsms = new curl_sms();
        // 获取该会员最新一条短信 如果未验证 不能再发送
        $sms_list_info = $curlsms->getLastSms($phone, 'sms_id,sms_use,sms_code,sms_phone,sms_userid,sms_add_time,sms_use', "sms_phone");
        if($sms_list_info){
            $sms_use = $sms_list_info['sms_use'];
            if($sms_use == 0) {
                return $this->error('您已经发过短信了');//您获取验证码已过期，请重新获取
            }
        }
        $today = Timer::today();
        $sms_count = DbBase::ifExist("s_sms_list",
            "sms_phone = '". $phone ."' AND sms_add_day = '". $today ."' order by sms_id desc limit 1");
        if($sms_count > 3){
            return $this->error('您今天已经发过3条短信了');
        }
        $newPass = substr(md5(microtime()),0,6);
        $pwdMD5 = Str::getMD5( $newPass );
        $editData = array(
            'u_pwd' => $pwdMD5
        );
        if( DbBase::updateByData('c_user', $editData, 'u_id='. $userId) != 1){
            return  Message::getMsgJson('0048');//返回‘修改用户信息失败’
        }
        //注销会员身份，防止旧密码不更新的错误
        @session_start();
        $userClass = $this->userClass;
        $userClass->exitUser();
        //替换内容 #code# , #time#
        $text_ = "#nick#={$u_nick}&#pwd#={$newPass}";
        //发送短信
        $sendSMS = $curlsms->sendSms($phone, 0, 'reset_pwd', $text_);
        if($sendSMS =='ok') {
            $validata->clearAllCache();//重置验证码
            return Message::getMsgJson('0376');//短信已发送成功
        } else{
            return Message::getMsgJson('0502', $sendSMS);
        }

    }
    //邮箱重置密码
    final function email_reset_password()
    {
        //校验邮箱
        $email = $this->getOption('my_email');
        $email_code = $this->getOption('email_code');
        $new_pwd = $this->getOption('new_pwd');
        if(!$email || strlen($email) < 4) {
            return  Message::getMsgJson('0502', '请输入邮箱');
        }
        if(!Str::isEmail($email)) {
            return  Message::getMsgJson('0502', '邮箱格式错误');
        }
        //检查邮箱是否已经注册
        $checkInfo = DbBase::getRowBy("c_user", 'u_id,u_nick', "u_email = '". $email ."'");
        if( count($checkInfo) == 0){
            return  Message::getMsgJson('0502', '您的邮箱未注册');
        }
        $userId = $checkInfo['u_id'];
        $emailRight = Str::checkEmailCode($email, $email_code);
        if($emailRight !== true) {
            return (Message::getMsgJson('0502', $emailRight));//邮箱验证码错误
        }
        $pwdMD5 = Str::getMD5($new_pwd);
        $editData = array(
            'u_pwd' => $pwdMD5
        );
        DbBase::updateByData('c_user', $editData, 'u_id='. $userId);
        return  Message::getMsgJson('0043');
    }
    //生成验证邮箱-发送邮件
    final function send_mail()
    {
        $email = $this->getOption('my_mail');
        // 参数是否为空
        if(!$email){
            return $this->error('请输入邮箱');//缺少必填的信息，请重试
        }
        $mess = new Message();
        $email = urldecode($email);
        //判断邮箱是否存在
        $uInfo = DbBase::getRowBy('c_user','u_id',"u_email = '".$email."'");
        if (!$uInfo){
            return $this->error('邮箱未注册');
        }
        $userId = $uInfo['u_id'];
        $emailCode = Str::getEmailCode($email);
        $mailsubject = "您申请了邮箱修改密码";//邮件主题
        $mailbody = "您申请了邮箱验证码以修改密码，验证码：". $emailCode." 如果不是您申请的请求，请无需理会且勿将验证码告诉他人。";//邮件内容
        $result = $mess->mailto($email, $mailsubject, $mailbody);
        DbBase::insertRows('s_email_code', [
            'email'=>$email,
            'code'=>$emailCode,
            'ctime'=>Timer::now(),
        ]);
        return Message::getMsgJson('0038', $result);

    }
    //获取手机验证码-以登录帐号
    final function get_phone_code_to_login()
    {
        $pic_code = $this->getOption('pic_code');//图片验证码
        $phone = $this->getOption('new_phone');
        // 参数是否为空
        if(!$phone) {
            return $this->error('请输入手机号码');
        }
        // 验证码是否为空
        if(!$pic_code){
            return $this->error('请输入图片验证码');
        }
        $validata = new drag_validate();
        if($validata->compareValidate($pic_code) != 0) {
            $validata->clearCode();//重置验证码
            return $this->error('验证码错误');
        }
        $curlsms = new curl_sms();
        //手机号码格式不对
        if(!Str::checkPhone($phone)){
            return $this->error('手机号码格式不正确');
        }
        //判断手机号码是否被注册
        if (!account::checkPhoneReg($phone)){
            return $this->error('手机还没有注册');
        }
        // 操作时间如果在间隔范围内
        if(!$curlsms->sms_send_interval()) {
            return Message::getMsgJson('0375');//  '您操作太快了,歇一会'
        }
        $rand_code = rand(1000,9999);
        //替换内容 #code# , #time#
        $text_ = '#code#='.$rand_code
            .'&#time#=5';
        //发送短信
        $sendSMS = $curlsms->sendSms($phone, 0, 'get_code', $text_, $rand_code);
        if($sendSMS =='ok') {
            $validata->clearAllCache();//重置验证码
            return Message::getMsgJson('0376');//短信已发送成功
        } else{
            return Message::getMsgJson('0502', $sendSMS);
        }
    }
    //获取手机验证码-以注册帐号
    final function get_phone_code_to_reg()
    {
        $pic_code = $this->getOption('pic_code');//图片验证码
        $phone = $this->getOption('new_phone');
        // 参数是否为空
        if(!$phone) {
            return $this->error('请输入手机号码');
        }
        // 验证码是否为空
        if(!$pic_code){
            return $this->error('请输入图片验证码');
        }
        $validata = new drag_validate();
        if($validata->compareValidate($pic_code) != 0) {
            $validata->clearCode();//重置验证码
            return $this->error('验证码错误');
        }
        $curlsms = new curl_sms();
        //手机号码格式不对
        if(!Str::checkPhone($phone)){
            return $this->error('手机号码格式不正确');
        }
        //判断手机号码是否被他人使用
        if (account::checkPhoneReg($phone)){
            return $this->error('手机已经被使用');
        }
        // 操作时间如果在间隔范围内
        if(!$curlsms->sms_send_interval()){
            return Message::getMsgJson('0375');//  '您操作太快了,歇一会'
        }
        $rand_code = rand(1000,9999);
        //替换内容 #code# , #time#
        $text_ = '#code#='.$rand_code
            .'&#time#=5';
        //发送短信
        $sendSMS = $curlsms->sendSms($phone, 0, 'get_code', $text_, $rand_code);
        if($sendSMS =='ok') {
            $validata->clearAllCache();//重置验证码
            return Message::getMsgJson('0376');//短信已发送成功
        } else{
            return Message::getMsgJson('0502', $sendSMS);
        }
    }
    //检查手机是否可用
    final function check_new_tel()
    {
        $new_tel = $this->getOption('new_tel');
        // 参数是否为空
        if(!$new_tel) {
            return(json_encode([[
                'id'=> '',
                'result'=> '请输入手机'
            ]]));
        }
        if(!Str::checkPhone($new_tel)) {
            return(json_encode([[
                'id'=> '',
                'result'=> '手机格式不正确'
            ]]));
        }
        //判断手机是否存在
        if (DbBase::ifExist('c_user',"u_tel = '".$new_tel."'")) {
            return(json_encode([[
                'id'=> '',
                'result'=> '手机已经被他人使用'
            ]]));
        }
        return(json_encode([[
            'id'=> $new_tel,
            'result'=> '手机可以使用'
        ]]));
    }


	function getData()
	{
        $db = mysql::getInstance();
        $userId = $this->userId;
        $arr = [];
        $userClass = $this->userClass;
        switch( $this->getOption('show') ) {
            //校验用户名称是否已经被注册
            case 'check_nick':
                $nick = $this->getOption('nick');
                if(!$nick || strlen($nick) < 2) {
                    print_r(json_encode([[
                        'id'=> $nick,
                        'result'=> '帐号至少2位数'
                    ]]));
                    exit;
                }
                $nick = strtolower($nick);
                if(strstr($nick, 'admin') || $nick == 'admin') {
                    print_r(json_encode([[
                        'id'=> $nick,
                        'result'=> '帐号不合法'
                    ]]));
                    exit;
                }
                $userClass = $this->userClass;
                if($userClass->checkUserId($nick) >= 1 ){
                    print_r(json_encode([[
                        'id'=> $nick,
                        'result'=> '帐号已被注册'
                    ]]));
                    exit;
                }
                print_r(json_encode([[
                    'id'=> $nick,
                    'result'=> '帐号可以使用'
                ]]));
                exit;
                break;
            case 'reg':
                $router = $this->getOption('router');
                $router_inviter = 0;
                if(strstr($router, '/reg/')) {
                    $router_inviter = intval(explode('/reg/', $router)[1]);
                }
                $u_nick = Users::createUnick($db);
                $radomPwd = Str::getRandChar(10);
                $arr['radomUnick'] = $u_nick;
                $arr['radomPwd'] = $radomPwd;
                $arr['web_title'] = '会员注册';
                //最后一次获取短信的时间
                $now_time = time();
                $sms = new curl_sms();
                $sms_interval = $sms->interval;
                //如果没有发送过验证信息
                $sms_lastSessionName = $sms->last_send_session_name;
                $lastPostSmsTime = isset($_SESSION[$sms_lastSessionName]) ? $_SESSION[$sms_lastSessionName] : 0;
                if($now_time - $lastPostSmsTime  > $sms_interval*60) {
                    $leftTime = 0;//剩余可以发送的时间为0 表示允许发送短信
                } else {
                    $leftTime = $sms_interval*60 - ($now_time - $lastPostSmsTime);
                }
                $inviter = 0;
                $session_inviter = isset($_SESSION['inviter']) && $_SESSION['inviter']>0 ? intval($_SESSION['inviter']) : 0;
                if(!$session_inviter) {
                    if($router_inviter) {
                        $_SESSION['inviter'] = $router_inviter;
                        $inviter = $router_inviter;
                    }
                } else {
                    $inviter = $session_inviter;
                }
                $arr['leftTime'] = $leftTime;
                $arr['inviter'] = $inviter;
                $arr['header'] = $this -> readTemp('/front/header.php', [
                    'web_title'=> '注册帐号',
                    'web_keywords'=> ' ',
                    'web_description'=> ' '
                ]);
                $this->templatefile = '/system/reg.php';
                break;
            // 首次注册 完善手机
            case 'finish_tel':
                if(!$userId) {
                    return $this->error('未登录');
                }
                $userNick = $userClass->getUserAttrib('userNick');
                $userHash = $userClass->getUserAttrib('safe_hash');
                //最后一次获取短信的时间
                $now_time = time();
                $sms = new curl_sms();
                $sms_interval = $sms->interval;
                //如果没有发送过验证信息
                $sms_lastSessionName = $sms->last_send_session_name;
                @session_start();
                $lastPostSmsTime = isset($_SESSION[$sms_lastSessionName]) ? $_SESSION[$sms_lastSessionName] : 0;
                if($now_time - $lastPostSmsTime  > $sms_interval*60) {
                    $leftTime = 0;//剩余可以发送的时间为0 表示允许发送短信
                } else {
                    $leftTime = $sms_interval*60 - ($now_time - $lastPostSmsTime);
                } 
                $arr['leftTime'] = $leftTime;
                $arr['userNick'] = $userNick;
                $arr['userHash'] = $userHash;
                $arr2 = array(
                    'web_title' => '完善手机',
                );
                $arr = array_merge($arr, $arr2);
                $this->templatefile = '/system/finish_tel.php';
                break;
            // 弹出层登录
            case 'quick_login':
                //最后一次获取短信的时间
                $now_time = time();
                $sms = new curl_sms();
                $sms_interval = $sms->interval;
                //如果没有发送过验证信息
                $sms_lastSessionName = $sms->last_send_session_name;
                $lastPostSmsTime = isset($_SESSION[$sms_lastSessionName]) ? $_SESSION[$sms_lastSessionName] : 0;
                if($now_time - $lastPostSmsTime  > $sms_interval*60) {
                    $leftTime = 0;//剩余可以发送的时间为0 表示允许发送短信
                } else {
                    $leftTime = $sms_interval*60 - ($now_time - $lastPostSmsTime);
                }
                $tmpArr['leftTime'] = $leftTime;
                $main_content = $this -> readTemp('/system/quick_login.php', $tmpArr);
                print_r($main_content);
                exit;
                break;
            case 'forget':
                //最后一次获取短信的时间
                $now_time = time();
                $sms = new curl_sms();
                $sms_interval = $sms->interval;
                //如果没有发送过验证信息
                $sms_lastSessionName = $sms->last_send_session_name;
                $lastPostSmsTime = isset($_SESSION[$sms_lastSessionName]) ? $_SESSION[$sms_lastSessionName] : 0;
                if($now_time - $lastPostSmsTime  > $sms_interval*60) {
                    $leftTime = 0;//剩余可以发送的时间为0 表示允许发送短信
                } else {
                    $leftTime = $sms_interval*60 - ($now_time - $lastPostSmsTime);
                }
                $arr['header'] = $this -> readTemp('/front/header.php', [
                    'web_title'=> '找回帐号/密码',
                    'web_keywords'=> '撒撒碎,悬赏答题,洒洒碎/撒撒碎',
                    'web_description'=> '高效的在线问答平台，让知识得到应有的回报'
                ]);
                $arr['leftTime'] = $leftTime;
                $this->templatefile = '/system/find.php';
                break; 
        }
        //组织显示数据
        $this->setTempData ($arr);
	}
}