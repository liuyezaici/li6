<?php
/* 试用模块: business
 * */
class business
{
	private $id = NULL;

	public function __construct( )
	{
	}
	//编辑
	public function edit($id, $vartab )
	{
		$db = mysql::getInstance();
		return DbBase::updateByData('a_business', $id, $vartab, 'bs_id');
	}

	//根据id删除信息（需要根据身份来删除）
	public function del( $id )
	{
        $db = mysql::getInstance();
		return $db->DeleteRecord('a_business', $id, 'bs_id');
	}

    //获取 a_business 信息 行锁
    public function getBusinessByIdLock( $bsid = '', $filds = '*', $and_='')
    {
        $db = mysql::getInstance();
        if ($and_){
            $wh_  =  " and $and_";
        }
        $sql = "SELECT {$filds} FROM a_business WHERE bs_id='". $bsid ."' ".$wh_." for update";
        $db->Query( $sql );
        return $db->getCurRecode( \PDO::FETCH_ASSOC );
    }


    //更新每日每时的试用记录   3旧时间到3新时间,,5旧时间移到5新时间 需要新时间
    //1.相同状态间只改变时间， 2.不同的状态表切换
    //business日志
    public function addBusinessLog($shikeUid=0, $sellerUid=0,$actionId=0,$bsId=0, $mytime, $money=0, $operator, $dotype=0,$l_wangwang = '') {
        $db = mysql::getInstance();
        if(!$mytime) $mytime = Func::ntime();
        $newDate = array(
            'l_shikeid' => $shikeUid,
            'l_sellerid' => $sellerUid,
            'l_actionid'=> $actionId,
            'l_bussid'=> $bsId,
            'l_addtime'=> $mytime,
            'l_money'=> $money,
            'l_opeator'=> $operator,
            'l_dotype'=> $dotype,
            'l_wangwang'=> $l_wangwang
        );
        return DbBase::insertRows('s_buss_log', $newDate);
    }
    //禁止申请的IP
    public  static function allowIp($ip="192.168.0.0") {
        $arr = $GLOBALS['cfg_shilke_err_shenqing_ip'];
        if(in_array($ip, $arr)) {
            return false;
        }
        return true;
    }
    //累积订单统计情况
    public  static  function addOrderCount($ww, $ntime) {
        $sql = "select s_id,s_order_day_num,s_order_day_date,s_order_week_num,s_order_week_date,s_order_month_num,s_order_month_date  FROM c_shike_wangwang WHERE s_wangwang='". $ww ."' limit 1";
        $db->Query($sql); //执行sql语句
        $wInfo =  $db->getCurRecode(\PDO::FETCH_ASSOC);
        if(count($wInfo) == 0) {
            return;
        }
        $ww_sid = $wInfo['s_id'];
        $s_order_day_date = $wInfo['s_order_day_date'];
        $s_order_day_num = intval($wInfo['s_order_day_num']);
        $s_order_week_date = $wInfo['s_order_week_date'];
        $s_order_week_num = intval($wInfo['s_order_week_num']);
        $s_order_month_date = $wInfo['s_order_month_date'];
        $s_order_month_num = intval($wInfo['s_order_month_num']);
        //每日下单数量
        $day_ = Timer::today(strtotime($ntime));
        $sql = "SELECT count(*) as total FROM
        (SELECT l_id FROM `s_buss_log` where l_dotype='5' and l_addtime >='". $day_ ."'  and l_wangwang='". $ww ."' GROUP BY l_actionid) ld";
        $db->Query($sql);
        $countInfo = $db->getCurRecode(\PDO::FETCH_ASSOC);
        $s_order_day_num = $countInfo['total'];
        $newCountDate['s_order_day_num'] = $s_order_day_num;
        if(strtotime($s_order_day_date) != strtotime($day_)) {
            $newCountDate['s_order_day_date'] = $day_;
        }
        //7天下单数量
        $day7_ = Timer::today(Func::lastDay(strtotime($ntime), 7));

        $sql = "SELECT count(*) FROM
        (SELECT l_id FROM `s_buss_log` where l_dotype='5' and l_addtime >='". $day7_ ."'  and l_wangwang='". $ww ."' GROUP BY l_actionid) ld";
        $db->Query($sql);
        $countInfo = $db->getCurRecode(\PDO::FETCH_ASSOC);
        $day7_num = $countInfo['total'];
        $newCountDate['s_order_week_num'] = $day7_num;
        if($s_order_week_date != $day7_) {
            $newCountDate['s_order_week_date'] = $day7_;
        }
        //30天下单数量
        $day30_ = Timer::today(Func::lastDay(strtotime($ntime), 30));
        $sql = "SELECT count(*) FROM
        (SELECT l_id FROM `s_buss_log` where l_dotype='5' and l_addtime >='". $day30_ ."'  and l_wangwang='". $ww ."' GROUP BY l_actionid) ld";
        $db->Query($sql);
        $countInfo = $db->getCurRecode(\PDO::FETCH_ASSOC);
        $s_order_month_num = $countInfo['total'];
        $newCountDate['s_order_month_num'] = $s_order_month_num ;
        if($s_order_month_date != $day30_) {
            $newCountDate['s_order_month_date'] = $day30_;
        }
        //每次提交肯定需要更新数量或可能带日期
        DbBase::updateByData("c_shike_wangwang", $ww_sid, $newCountDate, "s_id");
    }

    //更新订单统计情况
    public  function refreshOrderCount($sid, $ntime) {
        $db = mysql::getInstance();
        $sql = "select s_wangwang  FROM c_shike_wangwang WHERE s_id='". $sid ."' limit 1";
        $db->Query($sql); //执行sql语句
        $wInfo =  $db->getCurRecode(\PDO::FETCH_ASSOC);
        if(count($wInfo) == 0) {
            return false;
        }
        $ww = $wInfo['s_wangwang'];
        if(!$ww) {
            return message::getMsgJson('0502','旺旺不存在');
        }
        $jsonData = Func::getWangwangCodeLevelTaoDaXiang($ww);
        //旺旺号
        //等级和经验值
        if(  !$jsonData['exp'] || !$jsonData['url'] ) {
            return message::getMsgJson('0502','获取不到等级');
        }
        $ww_xinyong = intval($jsonData['exp']);
        $w_level_url = $jsonData['url'];
        if(!$w_level_url) {
            $w_level_url = "/b_red_1.gif";
        } else {
            $w_level_url = "/".$w_level_url;
        }
        $editData = array(
            's_xinyong' => $ww_xinyong,
            's_level_url' => $w_level_url,
        );
        DbBase::updateByData("c_shike_shenqing_info", $sid, $editData, "s_id");
    }

    //商家、管理员审核试用
    public function passApp( $bs_id='', $operator=0, $pass=0, $mytime='', $isAdmin = false) {
        $db = mysql::getInstance();
        if(!$mytime) $mytime = Func::ntime();
        //单线程操作商品
        $cache = new Cache();
        if(!$cache->Memcache) {
            return (message::getMessage('0425'));//memcache失效,请汇报客服
        }
        $actionClass = new scoreaction();
        if(!$bs_id) {
            return '没有bsid';
        }
        //获取单个试用ID
        $bs_id = trim($bs_id, ',');
        $bs_array = explode(",", $bs_id);
        foreach ($bs_array as $n=>$v) {
            $newBs_id = $v;
            $errId = '0055'; //试用报告审核失败
            $cfg_cache = $GLOBALS['cfg']['queue_data']['12'];
            $cacheName = $cfg_cache['tag'].$newBs_id;
            if(!$cache->checkActive($cacheName, "正在被".$operator."审核中..")) {
                continue;
            }
            //添加事务
            $db->BeginTRAN();
            try {
                $bsInfo = $this->getBusinessByIdLock($newBs_id,"bs_buyerid,bs_sellerid,bs_actionid,bs_status,bs_sqsj,bs_wangwang", "bs_status = '1'");
                if(!$bsInfo) {
                    $errId = '0548';
                    throw new Exception('试用状态已经改变',-1);
                }
                $sellerId = $bsInfo['bs_sellerid'];
                $actionId = $bsInfo['bs_actionid'];
                $buyerId = $bsInfo['bs_buyerid'];
                $old_bs_status = $bsInfo['bs_status'];
                if($old_bs_status !=1) {
                    throw new Exception('试用状态已经改变',-1);
                }
                if($sellerId != $operator && !$isAdmin) {
                    throw new Exception('您没有权限，请刷新',-1);
                }

                $cfg_cache2 = $GLOBALS['cfg']['queue_data']['1'];
                $cacheName2 = $cfg_cache2['tag'].$actionId;
                if(!$cache->checkActive($cacheName2, "正在被".$operator."审核活动资格中..")) {
                    throw new Exception($cache->Get($cacheName2), -1);
                }
                //锁表查询 只取一条 必须放在begin事务内
                $acInfo = $actionClass->getActionByIdLock($actionId , 'a_status, a_stime, a_etime, a_smoney,a_newprice, a_total,a_num, a_score, a_nchecktry,a_atype,a_ww,a_redbag,a_money', "a_status='2'");
                if(!$acInfo) {
                    $errId = '0503';
                    throw new Exception('活动已经暂停不能释放资格',-1);
                }
                $seller_ww = $acInfo['a_ww'];//商家旺旺
                $a_total = $acInfo['a_total'];//产品实际数量
                $a_num = $acInfo['a_num'];//产品剩余数量
                $a_smoney = $acInfo['a_smoney'];//活动剩余资金
                $a_newprice = $acInfo['a_newprice'];//活动价
                $a_nchecktry = $acInfo['a_nchecktry'];//通过资格人数
                if($pass == 1 ){
                    //活动数量是否足够
                    if( $a_num <=0  ){
                        $errId = '0197';
                        throw new Exception('活动的产品数量已经为0，不可以再允许试用',-1);
                    }
                    //加判断活动金额 防止数量多出 多送金额
                    if(  (bccomp($a_smoney  , $a_newprice) <0 || $a_smoney < 0)){
                        $errId = '0139';
                        throw new Exception('试用资金没有剩余,审核失败',-1);
                    }
                    //如果剩余2件，要判断当前数量是否正确 【完成试用+获得资格的试用+剩余试用 = a_total】
                    if($a_num <= 2) {
                        if(DbBase::ifExist("a_business", " bs_actionid='". $actionId ."' AND bs_status not in('-1','-4','1') ") != ($a_total-$a_num) ) {
                            $errId = '0139';
                            throw new Exception('试用资金没有剩余,审核失败',-1);
                        }
                    }
                    //如果通过 通过人数+1 产品数量-1
                    $newPassData = array(
                        'a_nchecktry'=> $a_nchecktry+1,
                        'a_num' => $a_num - 1,
                        'a_edittime' => $mytime
                    );
                    if($actionClass->edit($actionId, $newPassData, 'a_id', 'a_saction') != 1) {
                        $errId = '0069';
                        throw new Exception('修改申请通过人数失败',-1);
                    }
                    //当试客获得资格时，入库 防止再次申请成功
                    if($actionClass->shike_get_application_log($buyerId,$seller_ww, $sellerId, $mytime, $newBs_id)!=1){
                        throw new Exception('记录试客获得资格失败',-1);
                    }
                }
                //试用更新状态
                $newState = ($pass == 1) ? 2 : -1;
                $removeStatus = $this -> removeBusinessStatus($newBs_id, $newState, $mytime, array()); //审核资格 状态 1 到 -1或2
                if($removeStatus != 1) {
                    $errId = $removeStatus;
                    throw new Exception(message::getMessage($removeStatus).'修改试用失败',-1);
                }
                $db->CommitTRAN();
            }catch (Exception $e){
                $db->RollBackTRAN();
                $cache->Delete($cacheName);//完成后清空保护
                $cache->Delete($cacheName2);//完成后清空保护
                return $e->getMessage();// 返回:错误ID
            }
            $cache->Delete($cacheName);//完成后清空保护试用
            $cache->Delete($cacheName2);//完成后清空保护活动
            if($pass == 1) {
                //短信息通知买家 :
                /* $messageTitle = '恭喜您获得试用资格！';
                 $messageContent = '恭喜您获得试用资格，【活动编号:'.$actionId .'】，您需要尽快拍下宝贝并填写订单. <a href="/free-view-'.$actionId.'.html">查看宝贝</a>';
                 message::addMessage(0, $buyerId,'', $messageTitle, $messageContent, $mytime);*/
                //写入business日志 允许试用
                $this->addBusinessLog($buyerId, $sellerId, $actionId, $newBs_id, $mytime, 0, $operator, 2);
            } else {
                //写入business日志 拒绝试用
                $this->addBusinessLog($buyerId, $sellerId, $actionId, $newBs_id, $mytime, 0, $operator, 3);
            }
        }
        return 'ok';
    }
    //试客、商家、管理员 删除试用
    public function del_buss( $bs_id=0, $operator=0, $mytime='', $isAdmin = false) {
        $db = mysql::getInstance();
        if(!$mytime) $mytime = Func::ntime();
        //单线程操作商品
        $cache = new Cache();
        if(!$cache->Memcache) {
            return (message::getMessage('0425'));//memcache失效,请汇报客服
        }
        $cfg_cache = $GLOBALS['cfg']['queue_data']['12'];
        $cacheName = $cfg_cache['tag'].$bs_id;
        if(!$cache->checkActive($cacheName, $operator ."对任务执行：删除申请资格")) {
            return ($cache->Get($cacheName));//数据正在被使用,请等候
        }
        $sactionClass = new scoreaction();
        $db->BeginTRAN();
        try {
            $businessInfo = $this->getBusinessByIdLock($bs_id, 'bs_actionid,bs_buyerid,bs_sellerid,bs_status,bs_sqsj,bs_shsj,bs_fksj', "1");
            if(count($businessInfo) == 0){
                throw new Exception('数据不存在',-1);
            }
            $bs_buyerid = $businessInfo['bs_buyerid'];//刷手id
            $sellerId = $businessInfo['bs_sellerid'];//商家id
            $actionId = $businessInfo['bs_actionid'];//活动ID
            $oldBusinessState = $businessInfo['bs_status'];//任务的旧状态
            if($oldBusinessState !=1 && $oldBusinessState !=2) {
                throw new Exception('任务状态不支持删除',-1);
            }
            if(!$isAdmin && $bs_buyerid != $operator && $sellerId !=$operator) {
                throw new Exception('您没有权限执行此操作',-1);
            }
            //如果资格已经过了
            $hasPass = false;
            if(in_array($oldBusinessState, array(2,3,4,5,-2,-3))) {
                $hasPass = true;
            }
            //修改任务状态
            $newBussData['bs_status'] = -1;
            $newBussData['bs_revokesj'] = $this->myTime;
            if($hasPass) {
                $newBussData['bs_status'] = -4;
            }
            if(!DbBase::updateByData("a_business", $bs_id, $newBussData, "bs_id")){
                throw new Exception('修改任务状态失败',-1);
            }

            $cfg_cache2 = $GLOBALS['cfg']['queue_data']['1'];
            $cacheName2 = $cfg_cache2['tag'].$actionId;
            if(!$cache->checkActive($cacheName2, $operator."对任务执行：删除申请资格")) {
                throw new Exception('数据正在被使用',-1);
            }

            //锁表查询 只取一条 必须放在begin事务内
            $actionInfo = $sactionClass->getActionByIdLock($actionId, "a_num,a_nchecktry,a_ncheckorder", '1');
            if(!$actionInfo) {
                throw new Exception('活动不存在',-1);
            }
            $a_num = $actionInfo['a_num'];
            $a_nchecktry = $actionInfo['a_nchecktry'];
            $a_ncheckorder = $actionInfo['a_ncheckorder'];
            //如果资格已经过了，那么数量要返还
            if($hasPass) {
                $newActionData['a_num'] = $a_num + 1;
                $newActionData['a_nchecktry'] = $a_nchecktry - 1;
            }
            //如果是撤销订单号
            if(in_array($oldBusinessState, array(4,-3))) {
                $newActionData['a_ncheckorder'] = $a_ncheckorder-1;
            }
            if($newActionData) {
                if(!$sactionClass -> edit($actionId, $newActionData, 'a_id','a_saction') ) {
                    throw new Exception('修改活动信息失败',-1);
                }
            }
            $db->CommitTRAN();
        } catch ( Exception $e ) {
            $db->RollBackTRAN();
            $cache->Delete($cacheName);//退出时清空保护
            $cache->Delete($cacheName2);//退出时清空保护
            return $e->getMessage();//修改失败
        }
        //写入business日志 删除资格【商家或刷手】
        $this->addBusinessLog($bs_buyerid, $sellerId, $actionId, $bs_id, $mytime, 0, $operator, 4);
        $cache->Delete($cacheName);//退出时清空保护
        $cache->Delete($cacheName2);//退出时清空保护
        return 'ok';
    }

    //商家、管理员审核订单号
    public function auditOrder( $bs_id='', $operator=0, $pass=0, $mytime='', $old_orderid='',$isAdmin = false, $reason='') {
        $db = mysql::getInstance();
        if(!$mytime) $mytime = Func::ntime();
        //单线程操作商品
        $cache = new Cache();
        if(!$cache->Memcache) {
            return (message::getMessage('0425'));//memcache失效,请汇报客服
        }
        $actionClass = new scoreaction();
        if(!$bs_id) {
            return '没有bsid';
        }
        $wh_ = "";
        if($old_orderid) {
            $wh_ = "bs_dingdan='". $old_orderid ."'";
        }
        //获取单个试用ID
        $bs_id = trim($bs_id, ',');
        $bs_array = explode(",", $bs_id);
        foreach ($bs_array as $n=>$v) {
            $newBs_id = $v;
            $cfg_cache = $GLOBALS['cfg']['queue_data']['12'];
            $cacheNameBusiness = $cfg_cache['tag'].$newBs_id;
            if(!$cache->checkActive($cacheNameBusiness, "正在被".$operator."审核订单中..")) {
                continue;
            }
            //添加事务
            $db->BeginTRAN();
            try {
                //获取试用信息 活动id
                $bsInfo = $this->getBusinessByIdLock($bs_id,"bs_actionid,bs_buyerid,bs_sellerid,bs_status, bs_dingdan,bs_fksj,bs_wangwang", $wh_ );
                if(count($bsInfo) == 0 || !$bsInfo) {
                    throw new Exception('试用状态已经改变',-1);
                }
                $actionId = $bsInfo['bs_actionid'];
                $buyerId = $bsInfo['bs_buyerid'];
                $sellerId = $bsInfo['bs_sellerid'];
                $oldBusinessState = $bsInfo['bs_status'];
                $bs_dingdan = $bsInfo['bs_dingdan'];//订单号
                if($oldBusinessState != 3) {
                    throw new Exception('试用状态已经改变',-1);
                }
                if(!$isAdmin && $sellerId != $operator) {
                    throw new Exception('您没有执行权限',-1);
                }
                $cfg_cache2 = $GLOBALS['cfg']['queue_data']['1'];
                $cacheName2 = $cfg_cache2['tag'].$actionId;
                if(!$cache->checkActive($cacheName2, $operator ."审核订单号")) {
                    throw new Exception('数据正在被使用:'. $cache->Get($cacheName2),-1);
                }
                if($pass==1) {
                    $editBussData = array();
                } else {
                    $editBussData = array(
                        'bs_memo' =>  $reason
                    );
                }
                //如果是信用活动 要加到商家的信用记录里
                $sactionInfo = $actionClass->getActionByIdLock($actionId, "a_newprice,a_redbag,a_isxinyong", "1");
                $a_newprice = $sactionInfo['a_newprice'];
                $a_redbag = $sactionInfo['a_redbag'];
                $isXinyong = $sactionInfo['a_isxinyong'];
                //如果是信用活动，通过订单后 计算返款
                if($isXinyong ==1 && $pass == 1 ) {
                    $xinyuInfo = $actionClass->getSellerXinyongLock($sellerId);
                    $x_waitingbackmoney = $xinyuInfo['x_waitingbackmoney'];
                    $needMoney = bcadd($a_newprice, $a_redbag, 2);
                    $editXinyongData = array(
                        'x_waitingbackmoney' => bcadd($x_waitingbackmoney, $needMoney, 2)
                    );
                    if(DbBase::updateByData("c_seller_xinyong", $sellerId, $editXinyongData, "x_uid") != 1) {
                        throw new Exception('修改信用值失败',-1);
                    }
                    //写入信用变动日志
                    $xinyongLogData= array (
                        'l_uid' => $sellerId,
                        'l_sactionid' => $actionId,
                        'l_waittingmoney' => $needMoney,
                        'l_addtime' => $mytime,
                        'l_operator' => $operator,
                        'l_dotype' => 2,
                        'l_desc' => "通过订单,计算待返款金额,试用编号:".$bs_id.",订单号:".$bs_dingdan,
                    );
                    if(DbBase::insertRows("c_seller_xinyong_log", $xinyongLogData) != 1) {
                        throw new Exception('写入信用变动日志失败',-1);
                    }
                }
                $newState = ($pass == 1) ? '4' : '-2';
                $removeStatus = $this -> removeBusinessStatus($bs_id, $newState, $mytime, $editBussData); //审核订单 状态 3 到 4或-2
                if($removeStatus != 1) {
                    throw new Exception('修改试用状态失败',-1);
                }
                //如果通过 通过人数加1
                if($pass == 1) {
                    $db->Execute("UPDATE a_saction SET a_ncheckorder=a_ncheckorder+1,a_edittime='". $mytime ."' WHERE a_id='". $actionId  ."'");
                }
                $db->CommitTRAN();
            }catch (Exception $e){
                $db->RollBackTRAN();
                //完成后清空保护
                $cache->Delete($cacheName2);
                $cache->Delete($cacheNameBusiness);
                return $e->getMessage();// 返回:订单审核失败
            }
            $cache->Delete($cacheName2);//完成后清空保护活动
            $cache->Delete($cacheNameBusiness);//完成后清空保护试用
            //邮件通知买家 :
            if($pass == 1) {
                if($GLOBALS['cfg_shike_order_pass_need_sms']) {
                    //写入business日志 通过订单
                    $this->addBusinessLog($buyerId, $sellerId, $actionId, $bs_id, $mytime, 0, $operator, 6);
                    /* 发短信 buyer_order_success */
                    $smsModel = new sendsms();
                    /* 获取用户手机信息 */
                    $phoneInfo = $smsModel->get_info_from_c_phone_list($buyerId, 'p_number, p_is_validate,p_receive');
                    /* 如果有手机号码 */
                    if(count($phoneInfo) > 0) {
                        $phone = $phoneInfo['p_number'];
                        $p_is_validate = $phoneInfo['p_is_validate'];
                        /* 检查用户是否愿意接收此短信 并且 */
                        if($smsModel->check_send_sms(4,$phoneInfo['p_receive'])){
                            //如果已验证
                            if($p_is_validate==1){
                                /* 获取短信模版 */
                                $sms_moban_one = $smsModel->get_sms_moban_one(4,'sms_mark,sms_content,sms_is_open,sms_moban_id');
                                //如果短信模版已开启
                                if($sms_moban_one['sms_is_open']==1){
                                    $sms_content = $sms_moban_one['sms_content'];
                                    $user_real_name = '';
                                    $rand_code = rand(100000,999999);
                                    //替换内容
                                    $sms_content = str_replace('[user_real_name]',$user_real_name,$sms_content);
                                    $sms_content = str_replace('[scoreactionid]',$actionId,$sms_content);
                                    //发送短信
                                    $smsModel->send_one($phone,$sms_content,$buyerId, 1, $rand_code,$sms_moban_one['sms_moban_id']);
                                }
                            }
                        }
                    }
                    /* end sms */
                }
            } else {
                //写入business日志 拒绝订单
                $this->addBusinessLog($buyerId, $sellerId, $actionId, $bs_id, $mytime, 0, $operator, 7);
                //发站内信
                message::addMessage(0,$buyerId,'','您的试用订单号审核不通过.请重新填写.', '您的试用订单号审核不通过【试用编号:'.$bs_id.'】。<br />请尽快填写正确的订单号，如果超过24小时不填写试用报告，系统有可能撤销您的试用资格哦', $mytime);
                //写入business日志 拒绝订单
                if($GLOBALS['cfg_shike_order_pass_need_sms']) {
                    /* 发短信 buyer_order_error */
                    $smsModel = new sendsms();
                    /* 获取用户手机信息 */
                    $phoneInfo = $smsModel->get_info_from_c_phone_list($buyerId, 'p_number, p_is_validate,p_receive');
                    /* 如果有手机号码 */
                    if(count($phoneInfo) > 0) {
                        $phone = $phoneInfo['p_number'];
                        $p_is_validate = $phoneInfo['p_is_validate'];
                        /* 检查用户是否愿意接收此短信 并且 */
                        if($smsModel->check_send_sms(3,$phoneInfo['p_receive'])){
                            //如果已验证
                            if($p_is_validate==1){
                                /* 获取短信模版 */
                                $sms_moban_one = $smsModel->get_sms_moban_one(3,'sms_mark,sms_content,sms_is_open,sms_moban_id');
                                //如果短信模版已开启
                                if($sms_moban_one['sms_is_open']==1){
                                    $sms_content = $sms_moban_one['sms_content'];
                                    $user_real_name = '';
                                    $rand_code = rand(100000,999999);
                                    //替换内容
                                    $sms_content = str_replace('[user_real_name]',$user_real_name,$sms_content);
                                    $sms_content = str_replace('[scoreactionid]',$actionId,$sms_content);
                                    //发送短信
                                    $smsModel->send_one($phone,$sms_content,$buyerId, 1, $rand_code,$sms_moban_one['sms_moban_id']);
                                }
                            }
                        }
                    }
                }
            }
        }
        return 'ok';
    }
}
