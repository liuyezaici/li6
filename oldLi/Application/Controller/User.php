<?php
use Func\Api;
use Func\Timer;
use Func\Message;
use Func\DbBase;
use Func\Str;
use Func\Users;
use Func\Account;
use Func\Cache;
use Func\Ip;

//用户后台
class User extends Api
{
    protected $user_index_bg_pox_cache_name = '';//后台背景图的位置缓存名字
    protected $time = '';//后台背景图的位置缓存名字
    function __construct()
    {
        parent::__construct();
        $this->time = Timer::now();
    }

    function doAction()
    {
        $this->userId = $this->userId;
        $this->user_index_bg_pox_cache_name = 'use_bg_posx_'. $this->userId;
		switch ($this->getOption('do'))
        {
            //获取手机验证码-以更换手机
            case 'get_phone_code_to_change_phone':
                $oldPhone = $this->getOption('old_phone');//旧的手机
                $newPhone = $this->getOption('new_phone');//新的手机
                if(!$oldPhone){
                    return $this->error('请输入当前手机');
                }
                if(!$newPhone){
                    return $this->error('请输入新的手机号码');
                }
                //判断原来的手机号码是否正确
                if (!Account::checkMyPhone($oldPhone, $this->userId)) {
                    //return $this->error('您的旧手机号码错误');
                }
                //新手机格式
                if(!Str::checkPhone($newPhone)) {
                    return $this->error('您的新手机格式不正确');
                }
                //判断手机有没有改变
                if (Account::checkMyPhone($newPhone, $this->userId)) {
                    return $this->error('手机没有改变');
                }
                //判断手机号码是否被他人使用
                if (Account::checkPhoneReg($newPhone)) {
                    return $this->error('手机已经被使用');
                }
                $curlsms = new curl_sms();
                // 操作时间如果在间隔范围内
                if(!$curlsms->sms_send_interval()) {
                    return Message::getMsgJson('0375'); //您操作太快了,歇一会
                }
                $rand_code = rand(1000,9999);
                //替换内容 #code# , #time#
                $text_ = '#code#='.$rand_code
                    .'&#time#=5';
                //发送短信
                $sendSMS = $curlsms->sendSms($newPhone, $this->userId, 'get_code', $text_, $rand_code);
                if($sendSMS){
                    return Message::getMsgJson('0376');//短信已发送成功
                } else{
                    return Message::getMsgJson('0377');//短信验证码发送失败，请重新获取或者联系客服。
                }
                break;
            //解绑QQ
            case 'unbind_qq':
                DbBase::deleteBy("c_user_qqlogin","q_uid={$this->userId}");
                return Message::getMsgJson('0043');
                break;
            //保存背景图的x坐标
            case  'save_bg_pos':
                $bg_x = $this->getOption('bg_x', 0, 'float');
                $cache = new Cache();
                $cache->Add($this->user_index_bg_pox_cache_name, $bg_x);
                break;
            //上传图片 文件名 uid_new.jpg 在未添加数据时 可作编辑，有数据时就自动改文件名
            case 'upload_pic':
                //上传文件
                $target_url = !empty($this->options['target_url']) ? trim($this->options['target_url']) : '';//旧的完整文件名，用于上传覆盖原文件
                $upload_safe_code = !empty($this->options['upload_safe_code']) ? trim($this->options['upload_safe_code']) : '';//文件夹安全校验码，防止被修改
                if($target_url) {
                    if($upload_safe_code != Func::makeSafeUploadCode($target_url, $this->userId) ) {
                        return  Message::getMsgJson('0502', '文件上传目录被手动篡改了:'.$target_url);
                    }
                    $target_url = urldecode($target_url);
                } else {
                    $fileName = $this->userId."_new.jpg";
                    $savePath = $GLOBALS['cfg_imagefiles'];
                    file::creatdir(trim($savePath, "/"));
                }
                $ftp = new file();
                $uploadResponse = $ftp -> uploadFile($target_url, 'cover_url', $this->options);
                if($uploadResponse[0] == 'success') {
                    $filebackurl = $uploadResponse[1];
                } else {
                    return  Message::getMsgJson('0502', $uploadResponse[1]);//返回上传失败的原因
                }
                return  Message::getMsgJson('0388', $filebackurl);//返回‘图片上传成功’,新url
                break;
            //上传头像
            case 'upload_face':
                //上传文件
                $target_url = Users::createUserFaceUrl($this->userId);
                $savePath = dirname($target_url);
                file::creatdir(trim($savePath, "/"));
                $uploadResponse = file::uploadFile($target_url, 'face_url', $this->options);
                if ($uploadResponse[0] == 'success') {
                    $filebackurl = $uploadResponse[1];
                } else {
                    return Message::getMsgJson('0502', $uploadResponse[1]); //返回上传失败的原因
                }
                //删除旧头像
                $uInfo = DbBase::getRowBy("c_user", 'u_logo', "u_id=".$this->userId);
                $oldUrl = $uInfo['u_logo'];
                if(strstr($oldUrl, ".com/")){
                    file::delHttpFile($oldUrl);
                } else {
                    //删除旧本地图片：除了系统头像，其他删除
                    if($oldUrl && !strstr($oldUrl,'resource') && !strstr($oldUrl,'system')) {
                        $oldUrl = trim($oldUrl,'/');
                        if (file_exists(root.$oldUrl)) {
                            unlink(root.$oldUrl);
                        }
                    }
                }
                DbBase::updateByData("c_user", array('u_logo' => $filebackurl), "u_id={$this->userId}");
                return Message::getMsgJson('0388', $filebackurl); //返回‘图片上传成功’,新url
                break;
            //从客户邮箱复制验证码 回来 验证邮箱
            case 'edit_email':
                $code = $this->getOption('email_code');
                $newEmail = $this->getOption('new_email');
                //验证码是否为空
                if( !$code  ){
                    return(Message::getMsgJson('0502', '请输入邮箱验证码'));//请输入邮箱验证码
                }
                //新的邮箱是否为空
                if( !$newEmail  ){
                    return(Message::getMsgJson('0502', '请输入新的邮箱'));//请输入新的邮箱
                }
                //判断邮箱是否存在
                if (DbBase::ifExist('c_user',"u_email = '{$newEmail}' and u_id <> {$this->userId} ") > 0){
                    return Message::getMsgJson('0115'); //邮箱已经被他人使用
                }
                $emailRight = Str::checkEmailCode($newEmail, $code);
                if($emailRight !== true) {
                    return (Message::getMsgJson('0502', $emailRight));//邮箱验证码错误
                }
                $messageId = '0043'; //修改成功
                //修改为新邮箱
                if(Users::editUserInfo($this->userId, array('u_email'=>$newEmail))!=1) {
                    $messageId = '0044'; //修改失败
                }
                return Message::getMsgJson($messageId);// 输出提示内容
                break;
            //生成验证邮箱-发送邮件
            case 'send_mail':
                $email = $this->getOption('newemail');
                // 参数是否为空
                if(!$email){
                    return Message::getMsgJson('0023');//缺少必填的信息，请重试
                }
                $user = new Users();
                $mess = new message();
                $email = urldecode($email);
                //判断邮箱是否存在
                if (DbBase::ifExist('c_user',"u_email = '".$email."' and u_id <> {$this->userId} ") > 0){
                    return Message::getMsgJson('0115'); //邮箱已经被他人使用
                }
                $oldEmail = $user->getUserInfo($this->userId, 'u_nick, u_email');
                $u_nick = $oldEmail['u_nick'];
                $oldEmail = $oldEmail['u_email'];
                if($oldEmail == $email) {
                    return Message::getMsgJson('0151');//您已经验证过此邮箱
                }
                $emailCode = Str::getEmailCode($email);
                $mailsubject = "您申请了验证邮箱";//邮件主题
                $mailbody = "您的帐号:". $u_nick ." 申请了邮箱验证，验证码：". $emailCode."";//邮件内容
                $result = $mess->mailto($email, $mailsubject, $mailbody);
                DbBase::insertRows('s_email_code', [
                    'email'=>$email,
                    'code'=>$emailCode,
                    'ctime'=>Timer::now(),
                ]);
                return $result;
                break;
            //选择系统头像
            case 'chose_face':
                $faceid = $this->getOption('faceid', 1, 'int');
                if( !$faceid ){
                    return Message::getMsgJson('0023');//缺少必填的信息，请重试
                }
                //判断系统文件是否存在
                $fileUrl = '/resource/system/images/face/' . $faceid . '.jpg';
                $target_url = Users::createUserFaceUrl($this->userId);
                if (!file_exists(RootPath. $fileUrl)) {
                    return Message::getMsgJson('0502', '本地图片不存在:' . $fileUrl); //图片文件不存在
                }
                $file_url_local = RootPath . trim($fileUrl, '/');
                $myUrl = RootPath . trim($target_url, '/');
                //检测目录是否存在，不存在则创建
                if(!file_exists(dirname($myUrl))){
                    mkdir (dirname($myUrl), 0755, true );
                };
                @copy($file_url_local, $myUrl);
                if(!Ip::isLocal()||1) {//服务器才需要远程图片
                    $uploadResponse = file::moveFile($myUrl, $target_url);
                    if ($uploadResponse[0] == 'success') {
                        $filebackurl_our = $uploadResponse[1];
                        $filebackurl = $uploadResponse[2];
                    } else {
                        return Message::getMsgJson('0502', $uploadResponse[1]); //返回上传失败的原因
                    }
                } else {
                    $filebackurl = $target_url;
                }
                if ($filebackurl) {
                    Users::editUserInfo($this->userId, array('u_logo' => $target_url)); //更新头像地址
                    return Message::getMsgJson('0267', $filebackurl); //头像设置成功
                } else {
                    return Message::getMsgJson('0268'); //头像设置失败
                }
                break;
            //用户修改密码
            case 'edit_pwd':
                $old_phone = $this->getOption('old_phone');
                $new_pwd = $this->getOption('new_pwd1');
                if(!$new_pwd || strlen($new_pwd) < 6) {
                    return $this->error('新密码最少6位');
                }
                if(!$old_phone) {
                    return $this->error('请输入当前使用的手机');
                }
                //校验手机
                if(!Account::checkMyPhone($old_phone, $this->userId)) {
                    return  Message::getMsgJson('0502', '当前手机错误');
                }
                if (DbBase::updateByData('c_user', ['u_pwd' => Str::getMD5($new_pwd)], 'u_id='.$this->userId) != 1) {
                    return $this->error('密码没有改变');
                }
                $this->userClass = new Users();
                $this->userClass->exitUser();
                return Message::getMsgJson('0043');
                break;

            //短信验证码 验证修改手机
            case 'edit_phone':
                $code = $this->getOption('code');
                $new_phone = $this->getOption('new_tel');
                // 参数是否为空
                if(!$code){
                    return $this->error('您尚未提交手机号码');//您尚未提交手机号码
                }
                if(!$new_phone){
                    return $this->error('请输入手机号码');//请输入手机号码
                }
                $sms = new curl_sms();
                /* 获取该会员最新一条短信 */
                $sms_list_info = $sms->getLastSms($this->userId, 'sms_id,sms_use,sms_code,sms_phone,sms_userid,sms_add_time', 'sms_userid');
                if(!$sms_list_info){
                    return $this->error('您尚未获取验证码');//您尚未获取验证码
                }
                $sms_id = $sms_list_info['sms_id'];
                $sms_use = $sms_list_info['sms_use'];
                $sms_code = $sms_list_info['sms_code'];
                $sms_phone = $sms_list_info['sms_phone'];
                $add_time = $sms_list_info['sms_add_time'];
                /* 如果验证码已使用 */
                if($sms_use != 0){
                    return $this->error('您获取验证码已过期');//您获取验证码已过期，请重新获取
                }
                if($sms_phone != $new_phone) {
                    return $this->error('您验证的手机和当前填写的不一致');//您验证的手机和当前填写的不一致
                }
                /* 如果验证码过期 */
                $database_add_time = strtotime($add_time);//数据库里的短信生成时间 strtotime 变成 时间戳
                $now_time = time();//当时时间戳
                $sys_define_expires = $sms->expires*60;//系统定义的短信过期时间
                //判断该信息是否过期
                if($database_add_time - $now_time > $sys_define_expires ) {
                    $sms->updataSmsStatus($this->userId, 2, 'sms_userid'); //将该用户所有的短信息状态改为失效
                    return Message::getMsgJson('0381');//您获取验证码已过期，请重新获取
                } else if($sms_code != $code){ /* 验证码不正确 */
                    return $this->error('验证码不正确');
                } else{
                    //验证成功
                    $sms->updataSmsStatus($sms_id, 2, 'sms_id');
                    //同时更新会员电话
                    Users::editUserInfo($this->userId, array('u_tel'=>$new_phone));
                    return Message::getMsgJson('0043');//修改成功
                }
                break;
            //用户修改马甲
            case 'change_u_name':
				//修改用户信息
                $newName = $this->getOption('u_name');
                $newName = strtolower($newName);
                if(!$newName) return $this->error('昵称不能为空');
                if(strstr($newName, 'admin') || strstr($newName, '管理')  || strstr($newName, '客服')) {
                    return $this->error('马甲不合法');
                }
                if(DbBase::ifExist("c_user", "u_name = '". $newName ."'", 'u_id') >= 1 ){
                    return $this->error('马甲已被注册');
                }
                //如果旧的姓名已经存在，不能再编辑
				$newData = Array(
                    'u_name' => $newName
                );
                if (Users::editUserInfo($this->userId, $newData, 'u_id') != 1) {
                    return Message::getMsgJson('0044'); //返回‘修改失败’
                }
                return Message::getMsgJson('0043'); //返回‘修改成功’
            break;
            //用户修改帐号
            case 'change_u_nick':
				//修改用户信息
                $newNick = $this->getOption('nick');
                $nick = strtolower($newNick);
                if(strstr($nick, 'admin') || $nick == 'admin') {
                    return $this->error('帐号不合法');
                }
                $us = new Users();
                if($us->checkUserId($nick) >= 1 ){
                    return $this->error('帐号已被注册');
                }
                //如果旧的姓名已经存在，不能再编辑
				$newData = Array(
                    'u_nick' => $newNick
                );
                if (Users::editUserInfo( $this->userId, $newData, 'u_id') != 1) {
                    return Message::getMsgJson('0044'); //返回‘修改失败’
                }
                $us->exitUser();
                return Message::getMsgJson('0043'); //返回‘修改成功’
            break;
            default:
                print_r(Message::getMessage('0135'));//页面不存在
        }
    }

    final function main() {
        $this->userId = $this->userId;
        $user_nick = $this->userNick;
        $userInfo = $this->userClass->getUserInfo($this->userId, "u_name,u_logo,u_tel");
        $u_logo = $userInfo['u_logo'];
        $u_tel = $userInfo['u_tel'];
        $cache = $this->memcache;
        $bg_x = $cache->Get($this->user_index_bg_pox_cache_name);
        $arr = array(
            'u_id' => $this->userId,
            'user_nick' => $userInfo['u_name'],
            'bg_x' => $bg_x,
            'u_logo' => $u_logo.'?r_='.Str::getRam(8)
        );
        return $this->readTemp('', $arr);//设置模板
    }
    //编辑个人资料
    final function edit_my_info() {
        $user_nick = $this->userNick;
        $useraccount = $this->userClass->getUserInfo($this->userId, "u_email,u_logo,u_name,u_tel");
        //最后一次获取短信的时间
        $restTime = 0;//剩余可以发送的时间为0 表示允许发送短信
        $arr = array(
            'u_id' => $this->userId,
            'user_nick' => $user_nick,
            'u_logo' => $useraccount['u_logo'],
            'user_name' => $useraccount['u_name'],
            'u_email' => $useraccount['u_email'],
            'u_tel' => '*******'.substr($useraccount['u_tel'], -4),
        );
        $arr['restTime'] = $restTime;
        return $this->readTemp('', $arr);//设置模板
    }
    //检查邮箱是否可用
    final function check_new_email() {
        $email = $this->getOption('new_email');
        // 参数是否为空
        if(!$email){
            print_r(json_encode([[
                'id'=> '',
                'result'=> '请输入邮箱'
            ]]));
            exit;
        }
        if(!Str::isEmail($email)) {
            print_r(json_encode([[
                'id'=> '',
                'result'=> '邮箱格式不正确'
            ]]));
            exit;
        }
        $user = new Users();
        $email = urldecode($email);
        //判断邮箱是否存在
        if (DbBase::ifExist('c_user',"u_email = '{$email}' and u_id <> {$this->userId} ") > 0) {
            print_r(json_encode([[
                'id'=> '',
                'result'=> '邮箱已经被他人使用'
            ]]));
            exit;
        }
        $oldInfo = $user->getUserInfo($this->userId, 'u_nick,u_email');
        $oldEmail = $oldInfo['u_email'];
        if($oldEmail == $email) {
            print_r(json_encode([[
                'id'=> '',
                'result'=> '邮箱没有改变'
            ]]));
            exit;
        }
        print_r(json_encode([[
            'id'=> '',
            'result'=> '邮箱可以使用'
        ]]));
    }
    //校验用户姓名是否可以使用
    final function check_new_name() {
        $u_name = $this->getOption('u_name');
        if(!$u_name || strlen($u_name) < 1) {
            print_r(json_encode([[
                'id'=> '',
                'result'=> '马甲至少1位数'
            ]]));
            exit;
        }
        $u_name = strtolower($u_name);
        if(strstr($u_name, 'admin') || strstr($u_name, '管理')  || strstr($u_name, '客服')) {
            print_r(json_encode([[
                'id'=> '',
                'result'=> '马甲不合法'
            ]]));
            exit;
        }
        if(DbBase::ifExist("c_user", "u_name = '". $u_name ."'", 'u_id') >= 1 ){
            print_r(json_encode([[
                'id'=> $u_name,
                'result'=> '马甲已被注册'
            ]]));
            exit;
        }
        print_r(json_encode([[
            'id'=> $u_name,
            'result'=> '马甲可以使用'
        ]]));
    }
    //校验用户名称是否可以使用
    final function check_new_nick() {
        $nick = $this->getOption('nick');
        if(!$nick || strlen($nick) < 2) {
            print_r(json_encode([[
                'id'=> '',
                'result'=> '帐号至少2位数'
            ]]));
            exit;
        }
        $nick = strtolower($nick);
        if(strstr($nick, 'admin') || $nick == 'admin') {
            print_r(json_encode([[
                'id'=> '',
                'result'=> '帐号不合法'
            ]]));
            exit;
        }
        $us = new Users();
        if($us->checkUserId($nick) >= 1 ){
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
    }
    //修改手机
    final function edit_phone() {
        $htmlname = 'manage/account/f_edit_phone';
        
    }
    //修改邮箱
    final function edit_email() {
        $htmlname = 'manage/account/f_edit_email';
        
    }
    //修改头像
    final function edit_my_face() {
        $myInfo = DbBase::getRowBy('c_user', "u_logo", "u_id='". $this->userId ."'");
        $oldFaceUrl = $myInfo['u_logo'];
        if(!$oldFaceUrl) {
            $oldFaceUrl = Users::createUserFaceUrl($this->userId);
        }
        $arr['userLogo'] = $oldFaceUrl;
        $arr['upload_safe_code'] = Func::makeSafeUploadCode($oldFaceUrl, $this->userId); //生成安全码 防止上传路径被手动篡改
        $htmlname = 'manage/user/f_edit_my_face.php';
    }

}