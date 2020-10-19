<?php
/* 活动类 */
class scoreaction
{
	private $a_id = NULL;//商品id
	private $a_taobaoid = NULL;//商品的淘宝id
	private $a_name;//商品名称
	private $a_pic;//图片地址
	private $a_type;//商品类型
	private $a_taotype;//供应商类型     淘宝/天猫   系统参数
	private $a_sid;//卖家id
	private $a_etime;//修改时间
	private $a_postage = "";//邮资
	private $a_price = NULL;//价格
	private $a_hit = NULL;//浏览次数
	private $a_memo = NULL;//备注
	
	public function __construct( )
	{
	}
	//添加商品
	public function add( $vartab )
	{
        $db = mysql::getInstance();
		return DbBase::insertRows('a_saction',$vartab);
	}
	
	//修改商品
	public function edit($ids, $vartab , $flag = 'a_id' , $dbname = 'a_saction')
	{
		$db = mysql::getInstance();
        $status = DbBase::updateByData( $dbname , $ids , $vartab , $flag );
        //去掉商品缓存
        if(is_array($ids)) {
            foreach($ids as $n => $v) {
                cachemysqltable::delete( $v, 'a_saction');
            }
        } else {
            cachemysqltable::delete($ids, 'a_saction');
        }
		return $status;
	}
	
	//删除商品
	public function del( $id,  $flag , $and , $dbname = 'a_saction' )
	{
	   $db = mysql::getInstance();
		return $db->DeleteRecord( $dbname, $id , $flag , $and );
	}

    //根据活动ids获取活动信息(,分割) 无视图
    public function getActionById( $ids , $fildes = '*' , $and_ = '1' ,$orderby = null)
    {
        $db = mysql::getInstance();
        if(strstr($ids, ',')) {
            $ids = mysql::quo($ids);
            $inSql = "in (". $ids .")";
            $orderby = is_null($orderby)? ' ORDER BY a_id DESC': $orderby;
        } else {
            $inSql = "= '". $ids ."'";
            $orderby = '';
        }
        $sql = "SELECT ". $fildes ." FROM a_saction WHERE a_id ". $inSql ." AND ". $and_ . $orderby ."";
        $db->Query( $sql );
        return $db->getAllRecodes( \PDO::FETCH_ASSOC );
    }
    //根据账户id得到活动信息 锁表
    public function getActionByIdLock( $id , $fildes = '*' , $and_ = '1' )
    {
        $db = mysql::getInstance();
        $sql = "SELECT ".$fildes." FROM a_saction WHERE a_id = '". $id ."' AND ".$and_." FOR UPDATE";
        $db->Query($sql);
        return $db->getCurRecode(\PDO::FETCH_ASSOC);
    }

	//判断活动费、手续费
    public static  function getActionFee($vip = 0 , $a_atype = 0 , $a_redbag=0, $num = 1 )
    {
        return 0;//暂不收费
    }

    //判断活动是否允许被绑定
	public function checkWangwang($wangwang = '', $employid = 0)
    {
        if(!$wangwang || !$employid) return false;
        $db = mysql::getInstance();
        $num = DbBase::ifExist("c_wangwang", "b_wangwang='". $wangwang ."' AND b_employid<>'0'");
        if (!$num ||  $num == 0){ //允许绑定
            return true;
        } else {
            $numMy = DbBase::ifExist("c_wangwang", "b_wangwang='". $wangwang ."' AND b_employid = '". $employid ."' ");
            if(!$numMy || $numMy == 0) { //为空不可以绑定
                return false;
            } else {
                return true;
            }
        }
    }
    //绑定雇员id和旺旺
    public function bindWangWang($wangwang, $employid, $b_sellerid =0, $time_ = '')
    {
        if(!$time_) $time_ = Func::ntime();
        $db = mysql::getInstance();
        $numMy = DbBase::ifExist("c_wangwang", "b_wangwang='". $wangwang ."' ");
        if(!$numMy || $numMy == 0) {
            $newDate = array(
                'b_wangwang' => $wangwang,
                'b_employid' => $employid,
                'b_sellerid' => $b_sellerid,
                'b_actions'=> 1,
                'b_addtime'=> $time_,
                'b_last_actiontime'=> $time_,
            );
            return DbBase::insertRows('c_wangwang', $newDate);
        } else {
            //更新
            $newDate = array(
                'b_employid'=> $employid,
                'b_sellerid'=> $b_sellerid,
                'b_last_actiontime'=> $time_,
            );
            return DbBase::updateByData('c_wangwang', $wangwang, $newDate, "b_wangwang","b_actions");
        }
    }
    //查找旺旺绑定情况
    public function getWangWang($wangwang)
    {
        $db = mysql::getInstance();
        $sql = "SELECT b_wangwang,b_employid,b_addtime FROM c_wangwang where b_wangwang = '". $wangwang ."' ";
        $db->Query( $sql );
        return $db->getCurRecode( \PDO::FETCH_ASSOC );
    }
    //查找旺旺绑定的会员
    public function getWangWangBybid($b_id=0)
    {
        $db = mysql::getInstance();
        $sql = "SELECT b_wangwang,b_employid,b_sellerid,b_addtime FROM c_wangwang where b_id = '". $b_id ."' ";
        $db->Query( $sql );
        return $db->getCurRecode( \PDO::FETCH_ASSOC );
    }
    //操作活动日志 行为分类[1开启,2暂停,3再次启动,4结束活动,5核算结款,6编辑活动,7删除活动,8修改活动数量,
    //9审核资格,10审核订单,11审核报告,12编辑活动时间,13撤销资格,14订单直接完成试用,15置顶活动时间]
    public function addLog($type_ = 1, $uid_=0, $time_, $a_actionid=0, $a_businessid=0, $memo='', $a_dotype=0) {
        //$type_ 1:活动id, 2:试用编号
        $db = mysql::getInstance();
        if(!$time_) $time_ = Func::ntime();
        $newDate = array(
            'a_type' => $type_,
            'a_uid' => $uid_,
            'a_createtime'=> $time_,
            'a_actionid'=> $a_actionid,
            'a_businessid'=> $a_businessid,
            'a_dotype'=> $a_dotype,
            'a_desc'=> $memo
        );
        return DbBase::insertRows('a_saction_log', $newDate);
    }
    //解绑旺旺日志
    public function addWWLog($operatorUid=0, $time_, $oldUid=0, $newUid=0, $wangwang) {
        //$type_ 1:活动id, 2:试用编号
        $db = mysql::getInstance();
        if(!$time_) $time_ = Func::ntime();
        $newDate = array(
            'l_operator' => $operatorUid,
            'l_wangwang'=> $wangwang,
            'l_olduid'=> $oldUid,
            'l_newuid'=> $newUid,
            'l_addtime'=> $time_
        );
        return DbBase::insertRows('c_unbindwwlog', $newDate);
    }

    //通过物品的分类id得到链接菜单[free list 使用]
    public static function getGoodsTypeLinkMenu($currentId=0, $model='', $icon=false) {
        $allTypeArray = $GLOBALS['cfg_all_goodsType'];
        $memu_ = '';
        foreach($allTypeArray as $key=>$value){
            if($key==$currentId) {
                $active = ' class="active" ';
            }
            else{
                $active = '';
            }
            if($icon) {
                $icoHtm = '<i class="class_ico'.$key.'"></i>';
            } else {
                $icoHtm = '';
            }
            $memu_ .= '<li '.$active.' ><a href="'.$model.'&gtype='. $key .'" target="_self">'.$icoHtm.$value.'</a></li>';
        }
        return $memu_ ;
    }

    //通过类型的id得到活动类型
    public function getGoodsTypeName($currentId=0) {
        $allTypeArray = $GLOBALS['cfg_all_goodsType'];
       if(isset($allTypeArray[$currentId])) {
           return $allTypeArray[$currentId];
       } else {
           return '无此分类';
       }
    }

    //启动活动
    public function beginSaction( $aid=0, $operator=0, $mytime='', $isAdmin = false) {
        $db = mysql::getInstance();
        if(!$mytime) $mytime = Func::ntime();
        $account = new account();
        //活动状态只能是未发布或暂停才可以执行
        if(!DbBase::ifExist('a_saction', "a_id = '".$aid."' and a_status in(1,5)")){
            return (message::getMessage('0182'));//活动状态不支持此操作
        }
        //单线程操作商品
        $cache = new Cache();
        if(!$cache->Memcache) {
            return (message::getMessage('0425'));//memcache失效,请汇报客服
        }
        $cfg_cache = $GLOBALS['cfg']['queue_data']['1'];
        $cacheName = $cfg_cache['tag'].$aid;
        if(!$cache->checkActive($cacheName, $operator ."开启活动")) {
            return ($cache->Get($cacheName));//数据正在被使用,请等候
        }
        //获取产品活动的保证金金额 和商家ID
        $acInfo = DbBase::getRowBy("a_saction", 'a_sid, a_atype, a_newprice,a_stime, a_status, a_total,a_enick,a_redbag,a_isxinyong', "a_id = '". $aid ."' AND a_status in(1,5)");
        if(!$acInfo) {
            $cache->Delete($cacheName);//完成后清空保护
            return (message::getMessage('0182'));//活动状态不支持此操作
        }
        $sellerUid = $acInfo['a_sid'];
        $a_atype = $acInfo['a_atype'];
        $action_state = $acInfo['a_status'];
        $a_newprice = $acInfo['a_newprice'];
        $a_total = $acInfo['a_total'];
        $a_enick = $acInfo['a_enick']; //邀请的雇员id
        $a_redbag = $acInfo['a_redbag']; //红包
        $a_isxinyong = $acInfo['a_isxinyong']; //是否信用活动 信用活动要从管理员开始透支资金给商家
        $a_bzj_money = bcmul(bcadd($a_newprice, $a_redbag, 2) , $a_total, 2);//(下单价+红包)与数量相乘
        if (!$isAdmin && $sellerUid != $operator ){
            $cache->Delete($cacheName);
            return message::getMessage('0027');//没有执行权限
        }
        $actionName = '免费试用';
        if($a_total <= 0 || $a_bzj_money <=0) {
            //完成后清空保护
            $cache->Delete($cacheName);
            return message::getMessage('0502' ,'活动数量不能为0');
        }
        if(!$a_enick || $a_enick == 0) {
            //完成后清空保护
            $cache->Delete($cacheName);
            return message::getMessage('0294');//没有绑定雇员id 无法启动活动
        }
        //判断商家是不是VIP
        $user = new Users();
        $vip = new vip();
        //判断商家是否新商家
        if (DbBase::ifExist("c_yeji","c_newseller = '".$sellerUid."' AND c_flid=1") > 0){
            $newSeller = false;
        } else {
            $newSeller = true;
        }
        //首次开启活动需要扣除保证金 ,判断卖家的账户资金是否足够，如果足够允许发布，添加金额异动，如果不能发布，提示充值
        if($action_state == 1) {
            $zjFromUid = $sellerUid;
            if($a_isxinyong == 1) {
                $zjFromUid = $GLOBALS['cfg_admin_uid'];
            }
            //获取商家VIP信息
            $myVipInfo = $vip->getVipByUid($sellerUid, "v_userlevel");
            if($myVipInfo) {
                $u_seller_level = $myVipInfo['v_userlevel'];
            } else {
                $u_seller_level = 0;
            }
            if($a_atype == $GLOBALS['free_actionTypeId']) { //免费试用
                $actionName = '免费试用';
            } else if($a_atype == $GLOBALS['huasuan_actionTypeId']) { //超划算
                $actionName = '超划算';
            } else if( $a_atype == $GLOBALS['yongjin_actionid'] ) { //佣金专区
                $actionName = '佣金专区';
            }
            //商家需要压的钱 如果带红包，并且是非VIP，则需要加1元
            $shouxufei = $this->getActionFee($u_seller_level, $a_atype, $a_redbag, $a_total);
            $seller_pay_money = bcadd($a_bzj_money, $shouxufei, 2);// 要压的全部资金
            if($a_isxinyong == 0) {
                $userInfo = $user->getUserByID($sellerUid, 'ac_ldzj');
                $seller_ldzj = $userInfo['ac_ldzj'] ;
                if( bccomp( $seller_ldzj , $seller_pay_money, 2) < 0 ) {
                    $cache->Delete($cacheName);//完成后清空保护
                    return ('商家金额不足'.$seller_pay_money.' 无法启动活动');
                }
            }
        }
        $errId = '0095';
        $db->BeginTRAN();
        try {
            if($action_state == 1) {
                //如果是信用活动，要减去商家信用资金
                if($a_isxinyong == 1) {
                    $xinyuInfo = $this->getSellerXinyongLock($sellerUid);
                    $x_usemoney = $xinyuInfo['x_usemoney'];
                    $x_leftmoney = $xinyuInfo['x_leftmoney'];
                    if(bccomp($x_leftmoney, $seller_pay_money, 2 ) < 0) {
                        throw new Exception('商家信用资金不足(可用信用资金:'.$x_leftmoney.'|需要资金:'. $seller_pay_money .')',-1);
                    }
                    $editXinyongData = array(
                        'x_usemoney' => bcadd($x_usemoney, $seller_pay_money, 2),
                        'x_leftmoney' => bcsub($x_leftmoney, $seller_pay_money, 2)
                    );
                    if(DbBase::updateByData("c_seller_xinyong", $sellerUid, $editXinyongData, "x_uid") != 1) {
                        throw new Exception('修改信用值失败',-1);
                    }
                    //写入信用变动日志
                    $xinyongLogData= array (
                        'l_uid' => $sellerUid,
                        'l_sactionid' => $aid,
                        'l_addmoney' => $seller_pay_money,
                        'l_addtime' => $mytime,
                        'l_operator' => $operator,
                        'l_dotype' => 1,
                        'l_desc' => "启动信用活动". $aid .",需要扣除商家信用金额",
                    );
                    if(DbBase::insertRows("c_seller_xinyong_log", $xinyongLogData) != 1) {
                        throw new Exception('写入信用变动日志失败',-1);
                    }
                }
                //首次启动活动，需要将活动发布所需要的钱转移到冻结资金
                $vipText = $u_seller_level >= 1 ? 'VIP':'';
                if (!$account->operatAccount('publish', $seller_pay_money, array($zjFromUid, 'ac_ldzj'),
                    array($sellerUid,'ac_djzj'), $operator, $vipText.'开启'.$actionName.'活动:'. $aid .',压保证金', $mytime)){
                    throw new Exception('账户冻结资金转账失败',-1);
                }
                //启动活动 添加业绩
                if($a_enick > 0) {
                    $newYejiDate = array(
                        'c_uid' => $a_enick ,
                        'c_addmoney' => $seller_pay_money,
                        'c_addtime'=> $mytime,
                        'c_actionid'=> $aid,
                        'c_actiontype'=> $a_atype,
                        'c_creator'=> $operator,
                        'c_flid' => 1
                    );
                    //如果是新商家，加一个selleruid
                    if($newSeller) {
                        $newYejiDate['c_newseller'] = $sellerUid;
                    }
                    if (!DbBase::insertRows('c_yeji', $newYejiDate)){
                        $errId = '0293';
                        throw new Exception('添加业绩失败',-1);
                    }
                }
                $actionNewdata['a_stime'] =  $mytime;
                $actionNewdata['a_etime'] =  Timer::GetDateTimeMk(Timer::addDay(time(), 30));
                //修改产品保证金
                $actionNewdata['a_money'] =  $seller_pay_money;
                $actionNewdata['a_smoney'] =  $seller_pay_money;
                $actionNewdata['a_num'] =  $a_total;
                $actionNewdata['a_top'] = $this->myTime;
                $actionNewdata['a_sellervip'] = $u_seller_level;
            }
            $actionNewdata['a_status'] = 2;
            $actionNewdata['a_edittime'] = $this->myTime;
            if($this -> edit($aid, $actionNewdata, 'a_id', 'a_saction') !=1 ){
                throw new Exception('修改状态失败',-1);
            }
            $db->CommitTRAN();
        }catch (Exception $e){
            $db->RollBackTRAN();
            $cache->Delete($cacheName); //完成后清空保护
            return ($e->getMessage()); //活动发布失败
        }
        //写入日志
        if($action_state == 1) {
            $do_type = 1;
            $dowords="首次开启活动";
            $this->addLog(1, $operator, $mytime, $aid, 0, '雇员执行:'.$dowords, $do_type);
        } else {
            $do_type = 3;
            $dowords="再次开启活动";
            $this->addLog(1, $operator, $mytime, $aid, 0, '雇员执行:'.$dowords, $do_type);
        }
        $cache->Delete($cacheName); //完成后清空保护
        return 'ok';
    }
    //核算活动
    public function balanceSaction( $aid=0, $operator=0, $mytime='', $isAdmin = false) {
        $db = mysql::getInstance();
        if(!$mytime) $mytime = Func::ntime();
        // 判断是否还有申请试用没有被审核
        if (DbBase::ifExist('a_business',"bs_actionid = '".$aid."' AND bs_status in('1','2','3','4','5','-2','-3') ") > 0){
            return (message::getMsgJson('0038'));// 活动还有申请记录未完成，结款失败
        }
        $accountClass = new account();
        //判断该商品是否属于本人，而且必须是结束状态才可以结款
        if( !DbBase::ifExist("a_saction", "a_id = '".$aid."' AND a_status = 3")){
            return message::getMsgJson('0042');//活动不存在
        }
        //单线程操作商品
        $cache = new Cache();
        if(!$cache->Memcache) {
            return (message::getMessage('0425'));//memcache失效,请汇报客服
        }
        $cfg_cache = $GLOBALS['cfg']['queue_data']['1'];
        $cacheName = $cfg_cache['tag'].$aid;
        if(!$cache->checkActive($cacheName, $operator ."对活动核算结款")) {
            return (message::getMsgJson('0424', $cache->Get($cacheName)));//数据正在被使用,请等候
        }
        //添加事务
        $db->BeginTRAN();
        try {
            //获取 商家ID 商品剩余数量 剩余押金
            //锁表查询 只取一条 必须放在begin事务内
            $acInfo = $this->getActionByIdLock($aid, 'a_sid,a_num,a_atype,a_smoney,a_newprice,a_redbag,a_sellervip,a_stime,a_enick,a_isxinyong','a_status=3');
            if(!$acInfo) {
                throw new Exception('活动不存在',-1);
            }
            $sellerUid = $acInfo['a_sid'];
            $restNum = $acInfo['a_num'];
            $a_smoney = $acInfo['a_smoney'];
            $a_newprice = $acInfo['a_newprice'];
            $a_redbag = $acInfo['a_redbag'];
            $a_stime = $acInfo['a_stime'];
            $a_enick = $acInfo['a_enick'];
            $a_atype = $acInfo['a_atype'];
            $a_sellervip = $acInfo['a_sellervip'];
            $a_isxinyong = $acInfo['a_isxinyong']; //是否用信用垫付的活动,如果是 那么结算还给系统
            if(!$isAdmin && $sellerUid != $operator) {
                throw new Exception( '您没有执行权限，请刷新');
            }
            //活动时间必须超过15天才可以结算
            $timeLimit = $GLOBALS['cfg_saction_balance_time_limit'];
            if($timeLimit > 0 ){
                if(time() - strtotime($a_stime) < $timeLimit) {
                    throw new Exception( '活动开始时间才：'.Func::get_lasttime($a_stime),-1);
                }
            }
            if($restNum > 0 && $a_smoney > 0) {
                $restMoney =  bcmul(bcadd($a_newprice, $a_redbag, 2) , $restNum, 2);//计算剩余资金
                $shouexufei = $this->getActionFee($a_sellervip, $a_atype, $a_redbag, $restNum);
                $restMoney = bcadd($restMoney, $shouexufei, 2);
                //比较剩余资金是否一直
                if(bccomp($a_smoney, $restMoney, 2) != 0 ) {
                    throw new Exception('资金不对'. $a_smoney .'|'.$restMoney .'，请检查会员是否切换了VIP',-1);
                }

                //如果是用信用垫付的活动 结算要还给系统
                if($a_isxinyong == 1) {
                    $backToUid = $GLOBALS['cfg_admin_uid'];
                    $xinyuInfo = $this->getSellerXinyongLock($sellerUid);
                    $x_usemoney = $xinyuInfo['x_usemoney'];
                    $x_leftmoney = $xinyuInfo['x_leftmoney'];
                    $editXinyongData = array(
                        'x_usemoney' => bcsub($x_usemoney, $restMoney, 2),
                        'x_leftmoney' => bcadd($x_leftmoney, $restMoney, 2)
                    );
                    if(DbBase::updateByData("c_seller_xinyong", $sellerUid, $editXinyongData, "x_uid") != 1) {
                        throw new Exception('修改信用值失败',-1);
                    }
                    //写入信用变动日志
                    $xinyongLogData= array (
                        'l_uid' => $sellerUid,
                        'l_sactionid' => $aid,
                        'l_backmoney' => $restMoney,//返款金额
                        'l_addmoney' => -$restMoney,//取负数 方便统计
                        'l_addtime' => $mytime,
                        'l_operator' => $operator,
                        'l_dotype' => 4,
                        'l_desc' => "活动". $aid .",核算结款,恢复商家信用金额:￥".$restMoney,
                    );
                    if(DbBase::insertRows("c_seller_xinyong_log", $xinyongLogData) != 1) {
                        throw new Exception('写入信用变动日志失败',-1);
                    }
                } else {
                    $backToUid = $sellerUid;
                }
                $memo = '活动结束 商家执行核算结款';
                if (!$accountClass->operatAccount('balance', $restMoney, array($sellerUid, 'ac_djzj'), array($backToUid, 'ac_ldzj'), $operator , $memo, $mytime)){
                    throw new Exception('资金扣除失败',-1);
                }
                //计算扣除的业绩
                if($a_enick > 0) {
                    $newYejiDate = array(
                        'c_uid' => $a_enick ,
                        'c_backmoney' => $restMoney,
                        'c_addtime'=> $mytime,
                        'c_actionid'=> $aid,
                        'c_actiontype'=> $a_atype,
                        'c_creator'=> $operator,
                        'c_flid' => 2
                    );
                    if (!DbBase::insertRows('c_yeji', $newYejiDate)){
                        throw new Exception('添加业绩失败',-1);
                    }
                }
            }
            //修改商品数量和剩余押金
            $newActionData = array(
                'a_num'=> 0,
                'a_smoney'=> 0,
                'a_status'=> 4,
                'a_edittime' => $mytime
            );
            if($this->edit($aid, $newActionData, 'a_id', 'a_saction') != 1){
                throw new Exception('修改失败',-1);
            }
            $db->CommitTRAN();
        }catch (Exception $e){
            $db->RollBackTRAN();
            $cache->Delete($cacheName);//完成后清空保护
            return $e->getMessage();
        }
        $cache->Delete($cacheName);//完成后清空保护
        //写入日志
        $this->addLog(1, $operator, $mytime, $aid, 0, '商家对活动:'. $aid .'执行核算结款', 5);
        //消息通知商家 :
        /*  $messageTitle = '您发布的商品【编号：'.$aid.'】已经核算结款，请查看您的冻结资金.';
          $messageContent = '您发布的商品【编号：'.$aid.'】已经核算结款。<br /> 请查看您的冻结资金 ';
          message::addMessage(0,$sellerUid,'',$messageTitle, $messageContent, $mytime);*/
        return 'ok';
    }
    //判断商家是否绑定过会员，如果绑定返回雇员uids
    public function getSellerEmployids($sellerid=0) {
        $db = mysql::getInstance();
        $sql = "SELECT sl_employid,sl_employids,sl_type FROM s_seller_employ where sl_selleruid= '".$sellerid."'";
        $db->Query($sql);
        $result = $db->getAllRecodes(\PDO::FETCH_ASSOC );
        if(count($result) < 1) return 0;
        $result = $result[0];
        $oldEmployid = $result['sl_employid'];
        $oldEmployids = $result['sl_employids'];
        $oldType = $result['sl_type'];
        if($oldType == 0) {
            return $oldEmployid;
        } else {
            return $oldEmployids;
        }
    }
    //判绑定商家和业务员
    public function bindSellerToEmploy($sellerUid=0, $employUid=0, $mytime='') {
        $db = mysql::getInstance();
        if(!$mytime) $mytime = Func::ntime();
        if(!$sellerUid || !$employUid) {
            return false;
        }
        if(DbBase::ifExist('s_seller_employ', "sl_selleruid= '".$sellerUid."'") > 0) {
            return false;
        } else {
            $newdata = array(
                "sl_selleruid" => $sellerUid,
                "sl_addtime" => $mytime,
                "sl_employid" => $employUid,
                "sl_employids" => '',
                "sl_type" => 0,
            );
            return DbBase::insertRows('s_seller_employ', $newdata);
        }
    }

    //当试客获得资格时， 更新试客最后获得某商家旺旺的试用资格时间
    //c_shike_uid = $buyerId
    //c_seller_ww = $seller_ww
    //c_seller_uid = $sellerId
    //c_shike_get_application_time = strtotime($ntime)
    //c_shike_last_business_id = $bs_id 上一次获得的活动ID
    public static function  shike_get_application_log($buyerId,$seller_ww,$sellerId,$ntime,$bs_id=0){
        $now_shike_get_application_time = strtotime($ntime);//当前试客获得资格时间

        $sql = " c_shike_uid= '".$buyerId."' and ";  //试客
        $sql .=" c_seller_ww='".$seller_ww."' "; //商家旺旺
        //如果没有记录
        if( DbBase::ifExist('c_shike_get_application', $sql ) ==0) {
            $newdata = array(
                "c_shike_uid" => $buyerId,
                "c_seller_ww" => $seller_ww,
                "c_seller_uid" => $sellerId,
                "c_shike_get_application_time" => $now_shike_get_application_time,
                'c_shike_last_business_id' => $bs_id,
            );
            if( DbBase::insertRows('c_shike_get_application', $newdata)==-1){
                throw new Exception('新增试客获得资格失败',-1);
            }
        }
        else{
            if( $db->Execute(" update c_shike_get_application set c_shike_get_application_time='".$now_shike_get_application_time."',c_shike_last_business_id='".$bs_id."' where c_shike_uid='".$buyerId."' and c_seller_ww='".$seller_ww."' ")==-1 ){
                throw new Exception('修改试客获得资格时间失败',-1);
            }
        }
        //清除缓存
        cachemysqltable::delete($buyerId.md5($seller_ww),'c_shike_get_application');
        return 1;
    }
    //获取物品分类下拉菜单
    public function getGoodsSelectBox($currentId=0) {
        $allTypes = $GLOBALS['cfg_all_goodsType'];
        $menuHtml = "";
        foreach ($allTypes as $n=>$v) {
            $sel_ = '';
            if( $currentId == $n) $sel_ = ' selected';
            $menuHtml .= "<option value='".$n."'".$sel_.">".$v."</option>";
        }
        return $menuHtml;
    }
    //信用查询，没有则写入
    public function getSellerXinyong($sellerid=0) {
        $db = mysql::getInstance();
        $xinyongInfo = DbBase::getRowBy("c_seller_xinyong", "x_maxmoney,x_usemoney,x_leftmoney,x_waitingbackmoney", "x_uid='". $sellerid ."'");
        if(!$xinyongInfo) {
            $vipClass = new vip();
            //如果商家是VIP 则赠送
            $vipInfo = $vipClass -> getVipByUid($sellerid, "v_id");
            if($vipInfo && isset($vipInfo['v_id'])) {
                $maxMoney = $GLOBALS['cfg_seller_xinyong_max'];
                $xinyongInfo = array(
                    'x_uid'=> $sellerid,
                    'x_maxmoney'=> $maxMoney,
                    'x_usemoney'=> 0,
                    'x_leftmoney'=> $maxMoney,
                    'x_waitingbackmoney'=> 0,
                );
                DbBase::insertRows("c_seller_xinyong", $xinyongInfo);
            } else {
                return array(
                    'x_uid'=> $sellerid,
                    'x_maxmoney'=> 0,
                    'x_usemoney'=> 0,
                    'x_leftmoney'=> 0,
                    'x_waitingbackmoney'=> 0,
                );
            }
        }
        return $xinyongInfo;
    }
    //信用查询，没有则写入
    public function getSellerXinyongLock($sellerid=0) {
        $db = mysql::getInstance();
        $sql = "SELECT x_maxmoney,x_usemoney,x_leftmoney,x_waitingbackmoney FROM c_seller_xinyong WHERE x_uid = '". $sellerid ."' FOR UPDATE";
        $db->Query($sql);
        $xinyongInfo = $db->getCurRecode(\PDO::FETCH_ASSOC);
        if(!$xinyongInfo) {
            $vipClass = new vip();
            //如果商家是VIP 则赠送
            $vipInfo = $vipClass -> getVipByUid($sellerid, "v_id");
            if($vipInfo && isset($vipInfo['v_id'])) {
                $maxMoney = $GLOBALS['cfg_seller_xinyong_max'];
                $xinyongInfo = array(
                    'x_uid'=> $sellerid,
                    'x_maxmoney'=> $maxMoney,
                    'x_usemoney'=> 0,
                    'x_leftmoney'=> $maxMoney,
                    'x_waitingbackmoney'=> 0,
                );
                DbBase::insertRows("c_seller_xinyong", $xinyongInfo);
            } else {
                return array(
                    'x_uid'=> $sellerid,
                    'x_maxmoney'=> 0,
                    'x_usemoney'=> 0,
                    'x_leftmoney'=> 0,
                    'x_waitingbackmoney'=> 0,
                );
            }
        }
        return $xinyongInfo;
    }

}