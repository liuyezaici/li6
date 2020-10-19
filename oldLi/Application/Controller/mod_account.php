<?php
//买卖家资金功能
class mod_account extends pageuser
{	
	function __construct( $options = '', $checkuser = true )
	{
		parent::__construct( $options,$checkuser );
	
	}
	
	function doAction(){
        $db = mysql::getInstance();
        $userid  = $this->userClass->getUserAttrib('userId');
        $userType = $this->userClass->getUserAttrib('userType'); //1买家 2商家 3雇员[在此不使用]
		switch ($this->options['do']) {
            //快速修改手机，只要输入原来的手机即可
            case 'edit_phone':
                $mypwd = !empty($this->options['mypwd']) ? trim($this->options['mypwd']) : '';
                $old_phone = !empty($this->options['old_phone']) ? trim($this->options['old_phone']) : '';
                $phone = !empty($this->options['phone_number']) ? trim($this->options['phone_number']) : '';
                // 参数是否为空
                if(!$phone){
                    return Message::getMsgJson('0399');//请输入手机号码
                }
                if(!$mypwd){
                    return Message::getMsgJson('0401');//请输入您在平台的登录密码
                }
                $smsModel = new sendsms();
                //手机号码格式不对
                if(!$smsModel->checkMobileValidity($phone)){
                    return Message::getMsgJson('0378');//您提交的手机号码格式不正确
                }
                $user = new Users();
                $usernick = $this->userClass->getUserAttrib('userNick');
                //校验密码
                $res = $user->checkUser($usernick, $mypwd,false, false);
                if($res != '0001') {
                    return Message::getMsgJson($res); //返回登录错误详细信息[0097 账号不存在, 0003 冻结, 0002 密码错误]
                }
                //判断原来的手机号码是否正确
                if($old_phone) {
                    if (!DbBase::ifExist('c_user',"u_id = '".$userid."' AND u_tel = '".$old_phone."' ")){
                        return $this->error('旧手机号码错误');
                    }
                }
                //判断手机号码是否被他人使用
                if (DbBase::ifExist('c_phone_list',"p_number = '".$phone."' and p_userid <> '".$userid."' ") > 0){
                    return Message::getMsgJson('0400'); //手机已经被他人使用
                }
                /* 判断手机库是否有此会员 如果有就修改，没有就添加  */
                if (DbBase::ifExist('c_phone_list',"p_userid = '".$userid."' ") > 0){
                    //修改手机号码
                    $vartab1 = array(
                        'p_number'=> $phone,
                        'p_add_time'=> Func::ntime()
                    );
                    if(!DbBase::updateByData('c_phone_list', $userid, $vartab1, $flag='p_userid')) {
                        return Message::getMsgJson('0044');//修改失败
                    }
                } else {
                    if(!$smsModel->add_phone($phone,$userid)){
                        return Message::getMsgJson('0385');//添加失败
                    }
                }
                $newDate = array(
                    'u_tel' => $phone
                );
                if(!$user ->edit($userid, $newDate)){
                    return Message::getMsgJson('0044');//修改成功
                } else{
                    return Message::getMsgJson('0043');//修改失败
                }
                break;

            //上传实名认证图片 手持照 正面，背面，身份证号码
            case 'upload_sfz_shiming':
                $type= !empty($this->options['type']) ? trim($this->options['type']) : 0;
                $id_card_number = !empty($this->options['id_card_number']) ? trim($this->options['id_card_number']) : 0;//身份证
                //判断参数
                if(!$type){
                    return Message::getMsgJson('0023');//缺少必填的信息，请重试
                }
                if( $type!='by_hand' && $type!='front' && $type!='back' && $type!='id_card_number' ){
                    return Message::getMsgJson('0023');//缺少必填的信息，请重试
                }
                if($type=='id_card_number' && !$id_card_number){//空的参数
                    return Message::getMsgJson('0023');//缺少必填的信息，请重试
                }

                //查询是否已实名
                if(DbBase::ifExist('c_user_idcard'," s_uid='".$userid."' and s_status='1' " )){
                    return Message::getMsgJson('0502','您实名认证已通过审核，不能修改');//缺少必填的信息，请重试
                }
                $user = new Users();
                $useraccount = $user->getUserByID($userid, "u_name");
                $username = trim($useraccount['u_name']);
                if(!$username) {
                    return $this->error('您没有填写姓名不能实名认证');//您没有填写姓名不能实名认证
                }
                //如果未验证通过，要判断是否同名，同名的会员只能通过客服来实名验证
                $sql = "SELECT count(*) as total FROM c_user_idcard card LEFT JOIN c_user u ON card.s_uid=u.u_id WHERE u.u_name = '". $useraccount['u_name'] ."' AND u_id<>'". $userid ."'";
                $db->Query($sql);
                $countInfo = $db->getCurRecode(\PDO::FETCH_ASSOC);
                if($countInfo['total'] > 0) {
                    return $this->error('您的姓名已经被实名认证，请联系客服进行人工审核');
                }

                //定义上传图片
                $upload_file = false;
                if( $type=='by_hand' ){//手持照
                    $update_field = 's_hand_url';
                    $update_input = 'shiming_by_hand';
                    $upload_file = true;
                }
                else if( $type=='front' ){//正面
                    $update_field = 's_front_url';
                    $update_input = 'upload_sfz_shiming_front';
                    $upload_file = true;
                }
                else if( $type=='back' ){//背面
                    $update_field = 's_back_url';
                    $update_input = 'upload_sfz_shiming_back';
                    $upload_file = true;
                }
                else if( $type=='id_card_number' ){//身份证号码
                    $update_field = 's_idCard_number';
                    $upload_file = false;
                }

                //如果上传图片
                if($upload_file){
                    $savePath = $GLOBALS['cfg_sfz_shiming'];
                    $savePath .= '/'.date("Ymd");
                    file::creatdir(trim($savePath, "/"));
                    $fileFormat = Array('jpeg','jpg','png','gif','pjpeg','x-png');//pjpeg,x-png ie6,ie7,ie8
                    $md5_rand = md5(time()).'_'.uniqid();//随机名
                    //设定每个 $maxSize = 2048 2MB大小
                    $up = new uploadfile($this->options['Files'],$savePath,$fileFormat,$maxSize = 2048, $overwrite = 1,$autocreatedir = 1);
                    $up->setSavename(1, $userid.'_'.$md5_rand.".jpg"); //1 指定为右边名字
                    if( !$up->run($update_input) ){
                        return  Message::getMsgJson('0068',"，".$up->errmsg('zh'));//返回‘上传失败’
                    }
                    $imginfo = $up->getInfo();
                    $save_url = $savePath."/".$imginfo[0]['saveName'];
                    $new_data[$update_field] = $save_url;
                }
                else{
                    $new_data[$update_field] = $id_card_number;
                }
                $new_data['s_status'] = '0';//新的提交和重新提交就改成未审核状态
                if(DbBase::ifExist('c_user_idcard'," s_uid='".$userid."' " )){
                    //数据存在 更新
                    $status = DbBase::updateByData('c_user_idcard',$userid,$new_data,'s_uid');
                }
                else{
                    //不存在，新增
                    $new_data['s_uid'] = $userid;
                    $new_data['s_utype'] = $userType;
                    $new_data['s_addtime'] = strtotime(Func::ntime());
                    $status = DbBase::insertRows('c_user_idcard',$new_data);
                }

                if(!$status){
                    $info = '';
                    if( $type=='id_card_number' ){//身份证号码
                        $info = ",身份证号码已被使用";
                    }
                    return  Message::getMsgJson('0044',$info); //修改失败
                } else {
                    return  Message::getMsgJson('0364'); //修改成功 头像上传成功
                }

                break;
            //查询我某张的银行卡是否有成功,
            //如果有数据，提现过，就收1元
            //如果没有，当做是首笔
            case 'search_bank_card_fee':
                //银行卡号ID
                $card_id = !empty($this->options['card_id']) ? trim($this->options['card_id']) : '';
                if(!DbBase::ifExist('c_tixian_log', "t_cardid='". $card_id ."' and t_uid='".$userid."'")) {
                     return Message::getMsgJson('0487',2);
                }
                else{
                    return Message::getMsgJson('0487',1);
                }

            break;
            //添加银行卡
            case 'add_card':
                $area_id1 = !empty($this->options['area_id1']) ? trim($this->options['area_id1']) : '';
                $area_id2 = !empty($this->options['area_id2']) ? trim($this->options['area_id2']) : '';
                $my_card_type = !empty($this->options['my_card_type']) ? trim($this->options['my_card_type']) : '';
                $card_num = !empty($this->options['card_num']) ? trim($this->options['card_num']) : '';
                //$card_uname = !empty($this->options['card_uname']) ? trim($this->options['card_uname']) : '';
                //id参数是否为空
                if( !$area_id1 || !$area_id2 || !$my_card_type|| !$card_num ){
                    return Message::getMsgJson('0023');//缺少必填的信息，请重试
                }

                /* 获取用户姓名 */
                $user_info= cachemysqltable::get($userid,'c_user');
                $card_uname = $user_info['u_name'];

                if($card_uname==''){
                    return Message::getMsgJson('0023','您可能需要完善您的平台网站姓名');//缺少必填的信息，请重试
                }

                //判断卡号是否被添加过
                $cardClass = new card();
                if(DbBase::ifExist('t_usercard',"c_card_num = '". $card_num ."' && c_uid='".$userid."'") > 0) {
                    return (Message::getMsgJson('0459')); //抱歉,银行卡已经被绑定使用
                }
                //获取地区id和 卡id对应行号，如果不存在 说明不支持此卡
                //1.判断是否在市区
                $bankInfo = DbBase::getRowBy('t_bankno',"bank_no", "bank_id = '". $my_card_type ."' AND region_id = '". $area_id2 ."'");
                if(!$bankInfo ) {
                    //判断是否在市省份
                    $bankInfo1 = DbBase::getRowBy('t_bankno',"bank_no", "bank_id = '". $my_card_type ."' AND region_id = '". $area_id1 ."'");
                    if(!$bankInfo1 ) {
                        //判断是否支持省会
                        $a_shenghui_id = $cardClass->getprovinceShenghui($area_id1);
                        $bankInfo2 = DbBase::getRowBy('t_bankno',"bank_no", "bank_id = '". $my_card_type ."' AND region_id = '". $a_shenghui_id ."'");
                        if(!$bankInfo2 ) {
                            return (Message::getMsgJson('0460')); //抱歉,您的地区和银行卡类型不支持绑定,请联系客服
                        } else {//存在省会则直接获取行号
                            $c_bankno = $bankInfo2['bank_no'];
                            $c_area_id = $a_shenghui_id;
                        }
                    } else {//存在省份则直接获取行号
                        $c_bankno = $bankInfo1['bank_no'];
                        $c_area_id = $area_id1;
                    }
                } else {//存在则直接获取行号
                    $c_bankno = $bankInfo['bank_no'];
                    $c_area_id = $area_id2;
                }
                $mytime = Func::ntime();
                $cardInfo = $cardClass->getSysCardInfo($my_card_type, "bank_name");
                if(count($cardInfo) == 0) {
                    return (Message::getMsgJson('0049')); //数据不存在
                }
                $bank_name = $cardInfo['bank_name'];
                //添加卡
                $newData = array(
                    'c_uid' => $userid,
                    'c_card_num' => $card_num,
                    'c_addtime' => $mytime,
                    'c_bank_id' => $my_card_type,
                    'c_card_uname' => $card_uname,
                    'c_bank_name' => $bank_name,
                    'c_bankno' => $c_bankno,
                    'c_area_id' => $c_area_id
                );
                if(DbBase::insertRows('t_usercard', $newData)) {
                    return (Message::getMsgJson('0113')); //添加成功
                } else {
                    return (Message::getMsgJson('0114')); //添加失败
                }
                break;
            //编辑银行卡
            case 'edit_card':
                $card_id = !empty($this->options['card_id']) ? intval($this->options['card_id']) : 0;
                $card_num = !empty($this->options['card_num']) ? trim($this->options['card_num']) : '';
                $money = !empty($this->options['money']) ? floatval($this->options['money']) : '';//验证金额

                //id参数是否为空
                if( !$card_id || !$card_num ){
                    return Message::getMsgJson('0023');//缺少必填的信息，请重试
                }

                //银行卡如果非数字
                if(!is_numeric($card_num)){
                    return Message::getMsgJson('0492','：您的银行卡号有误');//缺少必填的信息，请重试
                }

                //如果有提交金额，那么验证当前状态是否是 1
                // c_is_validate = 0 只能修改未验证的用户
                // c_is_validate = 1 只能修改验证中的用户
                if($money!=''){
                    //获取数据
                    $bankInfo =  DbBase::getRowBy("c_user_bankcard", "c_validate_count,c_validate_money", "c_id = '". $card_id ."' AND c_uid='". $userid ."' and c_is_validate='1'");
                    if(!$bankInfo){
                        return (Message::getMsgJson('0049')); //数据不存在
                    }
                    $c_validate_count = $bankInfo['c_validate_count']+1;
                    $c_validate_money = $bankInfo['c_validate_money'];

                    //验证错误超过三次，提醒需要联系客服
                    if($c_validate_count>3){
                        return (Message::getMsgJson('0484',$c_validate_count-1)); //提醒需要联系客服
                    }

                    //每次验证时都给验证次数+1
                    DbBase::updateByData('c_user_bankcard', $card_id, array('c_validate_count'=>$c_validate_count), 'c_id');

                    //验证金额比对
                    if($c_validate_money!=$money){
                        return (Message::getMsgJson('0485',$c_validate_count)); //您提交的金额验证不正确
                    }

                    //编辑信息:验证通过
                    $newData = array(
                        'c_is_validate' => 2,
                        'c_validate_count' => $c_validate_count
                    );

                }
                else{
                    if(!DbBase::ifExist('c_user_bankcard',"c_id = '". $card_id ."' AND c_uid='". $userid ."' and c_is_validate='0' ") ) {
                        return (Message::getMsgJson('0049')); //数据不存在
                    }

                    //编辑信息
                    $newData = array(
                        'c_card_num' => $card_num,
                    );
                }


                if(DbBase::updateByData('c_user_bankcard', $card_id, $newData, 'c_id')) {
                    return (Message::getMsgJson('0043')); //修改成功
                } else {
                    return (Message::getMsgJson('0044')); //修改失败
                }

                break;
            //绑定支付宝
            case 'post_zfb':
                $zfb = !empty($this->options['zfb']) ? trim($this->options['zfb']) : ''; //支付宝
                if(!$zfb) {
                    return Message::getMsgJson('0023');//缺少必填的信息，请重试
                }
                //判断是否能更新（账户状态为1才能更新）
                if (!DbBase::ifExist('v_user',"u_id = '".$userid."' and ac_state = 1 ")){
                    return Message::getMsgJson('0005'); //您支付宝已经绑定，请与管理员联系。
                }
                if (DbBase::ifExist('c_account',"ac_zhifubao = '".$zfb."'  and ac_id <> '".$userid."' ") > 0 ){
                    return Message::getMsgJson('0006'); //此支付宝已经被他人绑定
                }
                $ac = new account();
                if ($ac->bindZFB($userid, $zfb,1, $this->userClass)){
                    return Message::getMsgJson('0004');//支付宝绑定成功
                }else{
                    return Message::getMsgJson('0007');//支付宝绑定修改失败
                }
                break;
            //买、卖家提现
            case 'tixian':
                $tixian_type = !empty($this->options['tixian_type']) ? intval($this->options['tixian_type']) : 0; //提现类型:1银行卡 2支付宝
                $tixian_card = !empty($this->options['tixian_card']) ? intval($this->options['tixian_card']) : 0; //提现到哪张卡的id
                $zfb = !empty($this->options['zfb']) ? trim($this->options['zfb']) : ''; //支付宝
                $money = !empty($this->options['money']) ? trim($this->options['money']) : ''; //流动资金 需要计算手续费
                $code = !empty($this->options['code']) ? trim($this->options['code']) : ''; //验证码
                $pwd = !empty($this->options['pwd']) ? trim($this->options['pwd']) : ''; //登录密码
                //$memo = !empty($this->options['memo']) ? trim($this->options['memo']) : ''; //描述
                $week = date("w");
                if($week == 6 || $week == 0 || date("y-m-d",time()) == '15-04-06') {
                    return Message::getMsgJson('0502','温馨提示：节假日关闭提现通道，请在工作日进行提现操作');//周六和周末禁止提现
                }
                if(!$tixian_type || $tixian_type==0) {
                    return Message::getMsgJson('0462');//请选择提现方式
                }
                if($tixian_type == 1 && !$tixian_card) {
                    return Message::getMsgJson('0463');//请选择提现的银行卡
                }
                $week = date("w");
                if($week == 6 || $week == 0 || date("y-m-d",time()) == '15-04-06') {
                    return Message::getMsgJson('0502','温馨提示：节假日关闭提现通道，请在工作日进行提现操作');//请选择提现的银行卡
                }
                $date_ = strtotime(Timer::today());
                if($date_ == 1430409600 || $date_ == 1430496000|| $date_ == 1430582400 ) {
                    return Message::getMsgJson('0502','节假日关闭提现通道');//请选择提现的银行卡
                }

                if($tixian_type == 2 && !$zfb) {
                    return Message::getMsgJson('0278');//您没有绑定支付宝
                }

                if( !$money || !$code || $money < 1) {
                    return Message::getMsgJson('0023');//缺少必填的信息，请重试
                }
                //登录密码
                if (Func::getMD5($pwd,32) != $this->userClass->getUserAttrib('userPwdMd5')){
                    return (Message::getMsgJson('0002')); //密码错误
                }
                if($money > 50000) {
                    return (Message::getMsgJson('0467')); //单笔提现最多5万元
                }

                //核对校验码核对
                $codeClass = new validate();
                if( !$codeClass->getValidate(strtolower($code)) ){
                    return Message::getMsgJson('0022');
                }
                //判断用户是否被锁定
                if(DbBase::ifExist("c_user", "u_id ='". $userid ."' AND u_state ='0'") > 0) {
                    return Message::getMsgJson('0502','您帐号已经被锁定，无法提现');
                }
                $shouxufei = 1;
                //提现到银行卡
                if($tixian_type == 1) {
                    //判断银行卡是否验证通过
                    if(!DbBase::ifExist('t_usercard', "c_id='". $tixian_card ."' and c_uid='".$userid."' and c_is_validate='2'")) {
                        return Message::getMsgJson('0491'); //您提现的银行卡没有通过验证
                    }

                    //设置银行卡首次提现收取的手续费为2元，以后每次为1元
                    if(!DbBase::ifExist('c_tixian_log', "t_cardid='". $tixian_card ."' and t_uid='".$userid."'")) {
                        $shouxufei = 2;
                    }
                    else{
                        $shouxufei = 1;
                    }

                } else if($tixian_type == 2) { //提现到支付宝
                    //判断是否能更新（账户状态为2才能更新）
                    if (!DbBase::ifExist('c_account',"ac_id = '".$userid."' and ac_zhifubao = '".$zfb."'")){
                        return Message::getMsgJson('0279'); //您提交的支付宝与绑定的不一致
                    }
                    $shouxufei  = $money * 0.005;
                    $shouxufei = round($shouxufei,2);
                    if($shouxufei < 1 )  $shouxufei = 1;
                    if($shouxufei > 25 )  $shouxufei = 25;
                }

                $accountClass = new account();

                if (DbBase::ifExist('c_account',"ac_id='". $userid ."' AND ac_ldzj  >= '".$money."'") == 0){
                    return Message::getMsgJson('0059'); //提现金额超过账户上流动资金
                }

                $mytime = Func::ntime();
                $errId = '0058';

                if(bcsub($money , $shouxufei, 2)<=0){
                    return Message::getMsgJson('0490'); //提现金额必须大于手续费
                }

                //添加事务
                $db->BeginTRAN();
                try {
                    $memo = "会员申请提现".$money."元";
                    $memo2 = "提现".$money."元,扣除手续费".$shouxufei."元";
                    //提现到银行卡
                    if($tixian_type == 1) {
                        $memo .= "[提现到银行卡,处理编号:". $tixian_card ."]";
                    } else {
                        $memo .= "[提现到支付宝:". $zfb ."]";
                    }
                    //提现手续费最新插入ID
                    $new_account_cash_fee_insert_id = $accountClass->operatAccount('cash_fee', $shouxufei,array($userid,'ac_ldzj'),array($GLOBALS['cfg_admin_uid'],'ac_ldzj'), $userid, $memo2, $mytime);
                    if (!$new_account_cash_fee_insert_id){
                        $errId = '0489';
                        throw new Exception('提现扣除手续费失败',-1);
                    }
                    $tixian_money = bcsub($money , $shouxufei, 2);
                    //提现最新插入ID
                    $new_account_cash_insert_id =$accountClass->operatAccount('cash', $tixian_money, array($userid,'ac_ldzj'), array($GLOBALS['cfg_admin_uid'],'ac_ldzj'), $userid, $memo, $mytime,0);
                    if (!$new_account_cash_insert_id){
                        $errId = '0060';
                        throw new Exception('提现失败:资金转移失败',-1);
                    }
                    //单独插入提现记录
                    $tixianLogData = array(
                        't_addtime' => $mytime,
                        't_uid' => $userid,
                        't_tixiantype' => $tixian_type,
                        't_cardid' => $tixian_card,
                        't_zhifubao' => $zfb,
                        't_accountlogid' => $new_account_cash_insert_id,
                        't_settle' => 0,
                        't_money' => $tixian_money,
                        't_utype' => $userType,
                    );
                    if(!DbBase::insertRows('c_tixian_log', $tixianLogData)) {
                        $errId = '0060';
                        throw new Exception(',插入提现记录失败',-1);
                    }
                    //新增提现手续费日志表
                    $data_fee_log = array(
                        'log_ch_id'=> $new_account_cash_insert_id,
                        'log_ch_fee_id' => $new_account_cash_fee_insert_id,//手续费最新插入ID
                        'log_ch_add_time'=>strtotime($mytime),
                        'log_ch_fee_money'=>$shouxufei,//提现手续费
                        'log_ch_uid'=>$userid,//资源来源ID
                        'log_ch_utype'=>$userType,//提现人身份类型，1试客，2商家
                        'log_ch_tianxian_type'=>$tixian_type,//提现类型 1是银联卡 2是支付宝
                    );
                    if(!DbBase::insertRows('c_accashchg_fee_log', $data_fee_log)){
                        $errId = '0060';
                        throw new Exception(',插入提现手续费日志表记录失败',-1);
                    }
                    $db->CommitTRAN();
                    $dayWordsId = $userType == 1 ? '0358':'0357';
                    return json_encode(array('id' => '0061','msg' => Message::getMessage('0061').",".Message::getMessage($dayWordsId)));
                } catch ( Exception $e ) {
                    $db->RollBackTRAN();
                    //提现失败
                    return json_encode(array('id' => $errId,'msg' => $e->getMessage(),'info'=>''));
                }
                break;
            default:
                print_r(Message::getMessage('0135'));//页面不存在
        }
    }

    function getData()
    {
        $userid = $this->userClass->getUserAttrib('userId');
        $db = mysql::getInstance();
        switch ($this->options['show']){
            //金币异动记录
            case 'get_score_change':
                $page = !empty($this->options['page']) ? intval($this->options['page']):1;
                $fields = 'sch_type,sch_score,sch_ctime,sch_memo ';
                $sql = "SELECT ". $fields ." FROM c_acscorechg WHERE sch_id='".$userid."' order by sch_ctime desc";
                $div = new Divpage($sql,"",$fields,$page, 10, 9,'','view_my_cashlist');
                $div -> getDivPage();
                $listResult = $div->getPage();
                foreach($listResult as $n=>$v) {
                    if($v['sch_type']== 6){
                        $listResult[$n]['state'] = '获取金币';
                        $listResult[$n]['str'] = "+";
                    } else {
                        $listResult[$n]['state'] = '消费金币';
                        $listResult[$n]['str'] = "-";
                    }
                }
                $arr = array(
                    'acchangelist' => $listResult,
                    'divmenu' => $div->getMenu()
                );
                $htmlname = 'manage/user/score_yidong';
                break;
            //金币每日任务
            case 'score':
                $htmlname = 'manage/score';
                break;
            //实名认证
            case 'renzheng':
                $user = new Users();
                $userInfo = $user->getUserByID($userid, 'u_name');
                $userName = $userInfo['u_name'];
                $cardInfo =  DbBase::getRowBy("c_user_idcard", " s_id,s_uid,s_utype,s_hand_url,s_front_url,s_back_url,s_addtime,s_status,s_validate_time,s_memo,s_idCard_number", "s_uid = '". $userid ."' limit 1");
                $validate_reason = "";
                if($cardInfo) {
                    if($cardInfo['s_status'] == 0) {
                        $validate_reason = "<span style='color: #333;'>您提交的信息尚未审核</span>";
                    } else if($cardInfo['s_status'] == -1) {
                        $validate_reason = "<span class='red'>" .$cardInfo['s_memo']."</span>";
                    } else if($cardInfo['s_status'] == 1) {
                        $validate_reason = "<span class='blue'>您提交的信息已经审核通过</span>";
                    }
                }
                $arr = array(
                    'username' => $userName,
                    'c_user_idcard_info' => $cardInfo,
                    'validate_reason' => $validate_reason,
                );
                $htmlname = 'manage/account/renzheng';
                break;
            //查看我的身份证实名验证
            case 'view_shiming_idCard':
                $type = !empty($this->options['type']) ? trim($this->options['type']):1;
                //判断参数
                if(!$type){
                    echo Message::getMsgJson('0023');//缺少必填的信息，请重试
                }
                if( $type!='by_hand' && $type!='front' && $type!='back' ){
                    echo Message::getMsgJson('0023');//缺少必填的信息，请重试
                }
                if( $type=='by_hand' ){
                    $update_field = 's_hand_url';
                }
                else if( $type=='front' ){
                    $update_field = 's_front_url';
                }

                $c_user_idcard_info = DbBase::getRowBy("c_user_idcard", $update_field, "s_uid = '".$userid."'");
                if(!empty($c_user_idcard_info)){
                    //定义上传图片
                    header("Location: ".$c_user_idcard_info[$update_field]);
                } else {
                    //跳转到默认图片
                    header("Location: /resource/manage/images/user/no_pic.png");
                }

            break;
            case 'form':
                switch ($this->options['form']){
                    //修改手机
                    case 'edit_phone':
                        $user = new Users();
                        $myInfo = $user -> getUserByID($userid, "u_tel");
                        $old_phone = $myInfo['u_tel'];
                        if(!$old_phone || strlen($old_phone) <1) {
                            $old_phone = "未绑定过手机,无须输入";
                        } else {
                            $old_phone = substr($old_phone, 0, 3)."****". substr($old_phone, 7, 6);
                        }

                        $arr = array(
                            'old_phone' => $old_phone
                        );
                        $htmlname = 'manage/account/f_edit_phone';
                        break;
                    //获取我的银行卡
                    case 'get_my_card':
                        $sql = "SELECT c_id,c_card_num,c_card_uname,c_bank_name,c_is_validate from t_usercard where c_uid = '". $userid ."'";
                        $db->Query($sql);
                        $arr['mycards'] = $db->getAllRecodes( \PDO::FETCH_ASSOC );
                        $htmlname = 'manage/account/f_all_my_card.php';
                        break;
                    //编辑银行卡信息
                    case 'edit_card':
                        $card_id = !empty($this->options['card_id']) ? intval($this->options['card_id']): 0;
                        //id参数是否为空
                        if( !$card_id ){
                            print_r(Message::getMsgJson('0023'));//缺少必填的信息，请重试
                            exit;
                        }
                        $cardClass = new card();
                        $cardInfo = $cardClass->getUserCardInfo($card_id,"c_id,c_card_num,c_bank_name,c_card_uname,c_area_id,
                        c_is_validate,c_validate_money,c_err_validate_bank_reason","c_uid='". $userid ."'");
                        if(!count($cardInfo)) {
                            print_r(Message::getMsgJson('0049'));//数据不存在
                            exit;
                        }
                        $arr = $cardInfo;
                        //获取银行卡地区
                        $areaInfo = DbBase::getRowBy("t_area", "a_name,a_level,a_parentid", "a_id = '". $cardInfo['c_area_id'] ."'");
                        $card_area = $areaInfo['a_name'];
                        //如果仍有上级
                        if($areaInfo['a_level'] != 1) { //获取上级地区
                            $previousInfo = DbBase::getRowBy("t_area", "a_name,a_parentid,a_level", "a_id = '". $areaInfo['a_parentid'] ."'");
                            $card_area .= " ". $previousInfo['a_name'];
                            //如果当前级别是2(表示仍有上级)
                            if($previousInfo['a_level'] != 1) {
                                //获取市/区
                                $provinceInfo = DbBase::getRowBy("t_area", "a_name", "a_id = '". $previousInfo['a_parentid'] ."'");
                                $card_area .= " ". $provinceInfo['a_name'];
                            }
                        }
                        $arr['card_area'] = $card_area;
                        $htmlname = 'manage/account/f_edit_card.php';
                        break;
                    //绑定支付宝
                    case 'bind_zfb':
                        $userinfo = $this->userClass->getUserByID($userid,'u_name, u_IDCard, u_tel, u_email,u_qq, ac_zhifubao, u_address'); //|| Func::none($userinfo['u_tel'])
                        if( Func::none($userinfo['u_name']) || Func::none($userinfo['u_email']) || Func::none($userinfo['u_qq']) ){
                            //必须输出然后停止,不能return
                            print_r(Message::getMessage('0232')); //<a href="/?s=user" target="_blank">请先完善个人信息</a>
                            exit;
                        }

                        $arr = array(
                            'u_name' => $userinfo['u_name'], //姓名
                            'zfb' => $userinfo['ac_zhifubao'] //支付宝
                        );
                        if(strlen($userinfo['ac_zhifubao']) > 0 ) {
                            $arr['button'] = '<span class="red">您已经绑定支付宝,如需修改请联系客服.</span>';
                        } else {
                            $arr['button'] = '<a class="button" style="float: left;display: inline-block;" onclick="submitEidt();" href="javascript: void(0);" target="_self">提交绑定</a>';
                        }
                        $htmlname = 'manage/account/f_bind_zfb';
                        break;
                        //绑定银行卡
                    case 'bind_card':
                        $userinfo = $this->userClass->getUserByID($userid,'u_name, u_IDCard, u_tel, u_email,u_qq, ac_zhifubao'); //|| Func::none($userinfo['u_tel'])
                        if( Func::none($userinfo['u_name']) || Func::none($userinfo['u_email']) || Func::none($userinfo['u_qq']) ){
                            //必须输出然后停止,不能return
                            print_r(Message::getMessage('0232')); //<a href="/?s=user" target="_blank">请先完善个人信息</a>
                            exit;
                        }
                        $cardClass = new card();
                        $allProvince = $cardClass->getAllProvince();
                        $all_card = $cardClass->getAllCard();
                        if(!DbBase::ifExist('t_usercard',"c_uid = '". $userid ."'") ) {
                            $mycards = array();
                        } else {
                            $sql = "select c_id,c_card_num,c_addtime,c_card_uname,c_bank_name,c_is_validate,c_err_validate_bank_reason FROM t_usercard WHERE c_uid='". $userid ."' order by c_id desc";
                            $db->Query($sql); //执行sql语句
                            $mycards =  $db->getAllRecodesEx(\PDO::FETCH_ASSOC);
                        }
                        $arr = array(
                            'all_province' => $allProvince, //所有市区
                            'all_card' => $all_card, //所有支持的银行卡
                            'mycards' => $mycards, //我绑定的银行卡
                            'userinfo' => $userinfo,//用户信息
                        );
                        $htmlname = 'manage/account/f_bind_card.php';
                        break;
                    //提现
                    case 'tixian':
                        $week = date("w");
                        if($week == 6 || $week == 0 || date("y-m-d",time()) == '15-04-06') {
                            print_r(Message::Show('温馨提示：节假日关闭提现通道，请在工作日进行提现操作') );//周六和周末禁止提现
                            exit;
                        }
                        $date_ = strtotime(Timer::today());
                        if($date_ == 1430409600 || $date_ == 1430496000|| $date_ == 1430582400 ) {
                            print_r(Message::Show('温馨提示：节假日关闭提现通道') );
                            exit;
                        }
                        $userinfo = $this->userClass->getUserByID($userid,'u_name,ac_ldzj,ac_djzj,ac_zhifubao');

                        $notices = '';
                        $validate_bank = DbBase::ifExist('t_usercard',"  c_uid='". $userid ."' and c_is_validate='2' "  );
                        //如果没有添加银行卡，或者银行没有通过
                        if($validate_bank==0){
                            $notices .= '<a href="#" target="_self" onclick="javascript:hideNewBox();openMenu(4);">您还没有添加的银行卡，或者银行卡没有通过验证；</a><br />';
                        }

                        if(empty($userinfo['ac_zhifubao'])){
                            $notices .= '<a href="#" target="_self" onclick="javascript:hideNewBox();openMenu(3);">您还没有绑定支付宝；</a>';
                        }
                        //不强制绑定支付宝，只要绑定其中任意一项就可以提现
                        if($validate_bank==0 && empty($userinfo['ac_zhifubao'])){
                            Message::Show($notices," ",0,2000,false,2) ;
                            die();
                        }

                        //获取用户类型
                        $u_type = $this->userClass->getUserAttrib('userType');

                        $arr = array(
                            'u_type' => $u_type,
                            'username' => $userinfo['u_name'],
                            'all_money' => $userinfo['ac_ldzj'] + $userinfo['ac_djzj'],
                            'liudongzijin' => $userinfo['ac_ldzj']+0,
                            'dongjiezijin' => $userinfo['ac_djzj']+0,
                            'zfb' => $userinfo['ac_zhifubao'],
                        );
                        $htmlname = 'manage/account/f_tixian';
                        break;
                    //充值
                    case 'chongzhi':
                        $todayLimit = Timer::today()." 23:50:00";
                        $nowtime = Func::ntime();
                        /*if(strtotime($nowtime) > strtotime($todayLimit)) {
                            echo(Message::getMessage('0409')) ;
                            exit;
                        }*/
                        /*if($userid != 6134) {
                            print_r('充值接口正在升级，暂止只能手动打款到支付宝') ;
                            exit;
                        }*/
                        $yongtu = !empty($this->options['yongtu']) ? trim($this->options['yongtu']) : '即时到账充值'; //用途
                        $money = !empty($this->options['money']) ? trim($this->options['money']) : ''; //金额
                        $username = $this->userClass->getUserAttrib('userNick');
                        $arr = array(
                                'yongtu' => $yongtu,
                                'money' => $money,
                                'userid' => $userid,
                                'username' => $username
                        );
                        $htmlname = 'manage/account/f_chongzhi';
                        break;
                    default:
                        print_r(Message::getMessage('0135'));//页面不存在
                        exit;
                }
                break;
    		//默认显示 会员的资金
    		default:
                $page = !empty($this->options['page']) ? intval($this->options['page']):1;
                $from_month = !empty($this->options['fromtime']) ? trim($this->options['fromtime']) : '';
                $end_month = !empty($this->options['endtime']) ? trim($this->options['endtime']) : '';
                $reason = !empty($this->options['reason']) ? intval($this->options['reason']):0;
                $lastMonth =  Func::getmonth(Func::lastDay(time(), 30));
                if(!$from_month)  $from_month = $lastMonth;
                if(!$end_month) $end_month =  Func::getmonth(Timer::GetDateTimeMk(Timer::addDay(strtotime($from_month),35))); //日期加35 确保能跳过本月
                //最多查询1年月的数据
                if(strtotime($end_month) - strtotime($from_month) > 31104000) {
                    $end_month = Timer::GetDateTimeMk(Timer::addDay(strtotime($from_month), 360));
                    $end_month = Func::getmonth($end_month);
                }
                //去掉-0 ,如2015-01 换成2015-1 便于替换选择状态
                $from_month = str_replace("-0", "-", $from_month);
                $end_month = str_replace("-0", "-", $end_month);
                $from_month_sql = strtotime($from_month);
                $end_month_sql = strtotime($end_month);

                $whTime_ = " WHERE l_uid='". $userid ."' AND l_month_int between '". $from_month_sql ."' and  '". $end_month_sql ."'";
                $wh_ = $whTime_;
                if($reason) {
                    $wh_  .= " and l_chreason = '". $reason ."'";
                }
                if($u_type == 1) {//试客资金记录
                    $top_menu = '
                    <a href="javascript: void(0);" target="_self" onclick="filterLogList(0);" data="0">全部</a>
                    <a href="javascript: void(0);" target="_self" onclick="filterLogList(3);" data="3">转账</a>
                    <a href="javascript: void(0);" target="_self" onclick="filterLogList(5);" data="5">提现</a>
                    <a href="javascript: void(0);" target="_self" onclick="filterLogList(18);" data="18">红包</a>';
                } else {
                    $top_menu = '
                    <a href="javascript: void(0);" target="_self" onclick="filterLogList(0);" data="0">全部</a>
                    <a href="javascript: void(0);" target="_self" onclick="filterLogList(1);" data="1">发布活动</a>
                    <a href="javascript: void(0);" target="_self" onclick="filterLogList(2);" data="2">活动核算</a>
                    <a href="javascript: void(0);" target="_self" onclick="filterLogList(3);" data="3">会员转账</a>
                    <a href="javascript: void(0);" target="_self" onclick="filterLogList(4);" data="4">在线充值</a>
                    <a href="javascript: void(0);" target="_self" onclick="filterLogList(5);" data="5">提现</a>
                    <a href="javascript: void(0);" target="_self" onclick="filterLogList(6);" data="6">被充值</a>
                    <a href="javascript: void(0);" target="_self" onclick="filterLogList(7);" data="7">手续费</a>
                    <a href="javascript: void(0);" target="_self" onclick="filterLogList(8);" data="8">开通VIP</a> ';
                }
                $top_menu = str_replace('data="'. $reason .'"','data="'. $reason .'" class="active"',$top_menu);
                //生成年月选择器
                $month_from_html = '';
                for($year = 2014; $year<2018;$year++) {
                    for($month = 1; $month<13;$month++) {
                        $month_from_html .= "<option value='". $year."-".$month ."'>". $year."-".$month ."</option>".chr(10);
                    }
                }
                $month_from_html = str_replace("value='". $from_month ."'", "value='". $from_month ."' selected",$month_from_html);
                $month_to_html = '';
                for($year = 2014; $year<2018;$year++) {
                    for($month = 1; $month<13;$month++) {
                        $month_to_html .= "<option value='". $year."-".$month ."'>". $year."-".$month ."</option>".chr(10);
                    }
                }
                $month_to_html = str_replace("value='". $end_month ."'", "value='". $end_month ."' selected",$month_to_html);
                //获取指定月份期间的chid
                $sql = " SELECT l_chids FROM `c_accashchg_user_month` ". $wh_ ." limit 1000" ;
                $db->Query($sql); //执行sql语句
                $accResult =  $db->getAllRecodes(\PDO::FETCH_ASSOC);
                $chids = "";
                foreach($accResult as $n=> $v) {
                    if($v['l_chids']) $chids .= ",".trim($v['l_chids'], ",");
                }
                if($chids) {
                    $chids = trim($chids, ",");
                }
                //分页输出查询结果
                $pagesize = 10;
                $page_ids = Func::getPage($chids, $pagesize, $page, 2);
                $idsArray = explode(",",$chids);
                $countTotal = count($idsArray);
                $page_ids = Func::quo($page_ids);
                $sql = "SELECT ch_id,ch_money,ch_ctime,ch_type,ch_fromflag,ch_toflag,ch_reason,ch_settle, ch_memo,ch_from,ch_to FROM c_accashchg WHERE ch_id in(". $page_ids .") ORDER BY ch_id desc";
                $divClass = new Divpage( $sql, '', '*', $page, $pagesize, $menustyle = 9 ,'','money_log_page');
                $divClass -> getDivPage(3, $countTotal);
                $listResult = $divClass->getPage();
                $page_menu = $divClass->getMenu();
                if(count($listResult) == 0) $page_menu = '';

                foreach ($listResult as $n => &$v) {
                    //结算状态
                    if($v['ch_settle'] == 1) {
                        $v['jszt'] = '已到账';
                    } else if($v['ch_settle'] == 0) {
                        $v['jszt'] = '未到账';
                    } else {
                        $v['jszt'] = '拒绝';
                    }
                    if($v['ch_fromflag'] == 'ac_ldzj') {
                        $zjlx1 = '→ ';
                        $zj_fromname = "流动 ";
                    } else {
                        $zjlx1 = '← ';
                        $zj_fromname = "冻结 ";
                    }
                    if($v['ch_toflag'] == 'ac_ldzj') {
                        $zj_toname = "流动 ";
                    } else {
                        $zj_toname = "冻结 ";
                    }
                    $v['zj_lx'] = $zj_fromname . $zjlx1 . $zj_toname;
                    if($v['ch_to'] == $userid && $v['ch_from'] != $v['ch_to']) {
                        $v['mod'] = "+";
                    } else if ($v['ch_from'] == $userid && $v['ch_from'] != $v['ch_to']){
                        $v['mod'] = "-";
                    } else if ( $v['ch_from'] == $v['ch_to'] && $v['ch_fromflag'] ==  'ac_ldzj'){
                        $v['mod'] = "→";
                    } else if ( $v['ch_from'] == $v['ch_to'] && $v['ch_fromflag'] ==  'ac_djzj'){
                        $v['mod'] = "←";
                    } else  {
                        $v['mod'] = "";
                    }
                }
                $userAccount = $this->userClass->getUserByID($userid);
                $arr = array(
                    'myldzj' => $userAccount['ac_ldzj'],
                    'mydjzj' => $userAccount['ac_djzj'],
                    'mymoney' => bcadd($userAccount['ac_ldzj'] , $userAccount['ac_djzj'],2),
                    'page' => $page,
                    'fromtime' => $from_month."-01",
                    'endtime' => $end_month."-01",
                    'acchangelist' => $listResult,
                    'divmenu' => $page_menu,
                    'reason' => $reason,
                    'month_from_html' => $month_from_html,
                    'month_to_html' => $month_to_html,
                    'top_menu' => $top_menu,
                );
                $htmlname = 'manage/account/account_index';
		}
        $arr['userLogo'] = $GLOBALS['cfg_photos']."/".$userid.".jpg?r=".Timer::getMtime();
        $this->setTempData ($arr);
		$this->setTempPath($htmlname);//设置模板
	}
}