<?php
//买卖家签到的模块类。
class mod_sign extends pageuser
{
	
	public function __construct($options='', $checkuser = true)
	{
		parent::__construct($options, $checkuser = true);
		$this->name = "manage/list";
	}

	//处理相关功能
	function doAction(){
        $userid = $this->userClass->getUserAttrib('userId');
        switch ($this->options['do']){
            //提交签到
            case 'sign':
                $db = mysql::getInstance();
                $sign = new sign();
                $mytime = Func::ntime();
                $ac = new account();
                $userClass = new Users();
                //获取用户最新的签到记录
                $signinfo = $sign->getSignInfoByUserId( $userid, '*', 1);
                $new_score = 2;
                $snum = 1;
                if(count($signinfo) > 0 ) {
                    //最新签到次数
                    $snum = intval($signinfo[0]['sg_num']);
                    //最新签到时间
                    $stime = $signinfo[0]['sg_ctime'];
                    $stime_day = explode(' ',$stime);
                    $stime_day = $stime_day[0];
                    $today = Timer::today();
                    if($today == $stime_day) {
                        return  Message::getMsgJson('0231');//返回'今天已签到'
                    }
                    //已经间隔2天
                    if(Timer::daydiff($today, $stime_day) > 24 ) {
                        $new_score = 2;//间隔1天 金币数为2
                        $snum =  1; //连续签到天数
                    }else{
                        $snum ++ ;
                        $score_score = $GLOBALS['sign_score'];
                        $get_days = $snum;
                        $get_days = $get_days > 8 ? 8 : $get_days; //限制下标最大为 8
                        $array_ = explode(',',$score_score);
                        $new_score = 0;
                        foreach ($array_ as $n=>$v) {
                            $this_array = explode('|',$v);
                            $this_day = $this_array[0];
                            $this_score = $this_array[1];
                            if($this_day == $get_days ) {
                                $new_score = $this_score;
                                break;
                            }
                        }
                        if($new_score > 8) $new_score = 8;
                    }
                }
                $newdata = array(
                    'sg_uid' => $userid,
                    'sg_num' => $snum,
                    'sg_code' => $new_score,
                    'sg_ctime' => $mytime

                );
                //开启事务
                $db->BeginTRAN();
                try {
                    //增加账户金币
                    if ( !$ac->operatScore( 'add', $userid , $new_score ,'签到' , $mytime )){
                        throw new Exception('创建账户失败',-1);
                    }
                    //创建签到记录
                    if ( $sign->add( $newdata , $db ) != 1 ){
                        throw new Exception('创建签到失败',-1);
                    }
                    //签到加1经验 addExp($uid=0, $addNum = 0, $operator = 1,$mytime = '', $flid=1, $memo = "每日签到")
                    $addNum = 1;
                    if(!$userClass ->addExp($userid, $addNum, $userid, $mytime, 3, "每日签到") ){
                        throw new Exception('增加经验值事务出错',-1);
                    }
                    $db->CommitTRAN();
                    return  Message::getMsgJson('0103',$new_score);//返回‘签到成功’
                }catch (Exception $e){
                    $db->RollBackTRAN();
                    return  Message::getMsgJson('0104',$new_score);//返回'签到失败'
                }
                break;
            case 'getlastsign':
                $us = new Users;
                $userType = $us->getUserAttrib('userType');
                $sign = new sign();
                //获取用户最新的签到记录
                $signinfo = $sign->getSignInfoByUserId( $userid );
                //记录是否已经签到
                $hasSign = 0;
                if(count($signinfo) == 0) {
                    $resultObj = array(
                        'id' => '',
                        'sg_num' => 0,
                        'has_sign' => $hasSign,
                        'sg_tomorrow' => 3
                    );
                    // 输出连续签到次数,
                    return json_encode($resultObj);
                    break;
                }
                //最新签到次数
                $snum = $signinfo['sg_num'];
                if(!$snum) $snum = 1;
                //最新签到时间
                $stime = $signinfo['sg_ctime'];
                //存在上次签到记录
                if($stime) {
                    $stime_day = explode(' ',$stime);
                    $stime_day = $stime_day[0];
                    $today = Func::ntime();
                    $today = explode(' ',$today);
                    $today = $today[0];
                    //同一天 隐藏签到按钮
                    if($today == $stime_day) {
                        $hasSign = 1;
                    }
                    //已经间隔2天
                    if(Timer::daydiff($today, $stime_day) > 24 ) {
                        $sg_tomorrow = 3;
                    }else{
                        $score_score = $GLOBALS['sign_score'];
                        $get_days = $snum;
                        $get_days = $get_days > 8 ? 8 : $get_days; //限制下标最大为 8
                        $array_ = explode(',',$score_score);
                        $new_score = 0;
                        foreach ($array_ as $n=>$v) {
                            $this_array = explode('|',$v);
                            $this_day = $this_array[0];
                            $this_score = $this_array[1];
                            if($this_day == $get_days ) {
                                $new_score = $this_score;
                                break;
                            }
                        }
                        $sg_tomorrow = $new_score + 1;
                        if($sg_tomorrow > 8) $sg_tomorrow = 8;
                    }
                }
                $resultObj = array(
                    'id' => '',
                    'sg_num' => $snum,
                    'sg_tomorrow' => $sg_tomorrow,
                    'has_sign' => $hasSign
                );
                // 输出连续签到次数,
                return json_encode($resultObj);
                break;
            //获取我的签到记录
            case 'mysign':
                $us = new Users;
                $sign = new sign();
                //获取用户的签到记录
                $signinfo = $sign->getUserSignList( $userid, 1, 20 ,'sg_num,sg_ctime,sg_code');
                $html = '';
                if(count($signinfo) > 0 ) {
                    foreach ($signinfo as $n=>$v) {
                        $html .= "<li>
                    <span class='sgNum'>签到:". $v['sg_num'] ."次</span>
                    <span class='sgTime'>". $v['sg_ctime'] ."</span>
                    <span class='sgCode'>+". $v['sg_code'] ."金币</span>
                   </li>";
                    }
                } else {
                    $html = '<li>您没有签到记录.</li>';
                }
                $resultObj = array(
                    'id' => '',
                    'msg' => $html
                );
                return json_encode($resultObj);
                break;
        }
	}
	
	function getData(){
        $userid = $this->userClass->getUserAttrib('userId');
        $page = !empty($this->options['page'])?$this->options['page']:1;
        $model = 'sign';
        $fildes = 'sg_num, sg_ctime, sg_code';
        $wh = " WHERE sg_uid = '". $userid ."' ";
        $sql = "SELECT {$fildes} FROM a_sign $wh ORDER BY sg_ctime DESC";
        $ac = new Divpage( $sql, $model, $fildes , $page, $pagesize = 10, $menustyle = 9 ,"", "view_my_qiandao");
        $ac->getDivPage();
        $arr = array(
                'sign_list' => $ac->getPage(),
                'divmenu' => $ac->getMenu()
        );
        //组织显示数据
        $htmlname = 'manage/sign_list';
        $this->setTempData ($arr);//组织显示数据
		$this->setTempPath($htmlname);//设置模板
	}
}

?>	