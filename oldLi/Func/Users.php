<?php
namespace Func;


class Users
{
    public $userId = '';//用户数字id
    public $userNick = '';//用户帐号
    public $userType = '';//用户类型
	protected $userPwdMd5 = '';//密码md5
	public $outType = 0;////被踢类型 [0是超时 1是身份不同]
    protected $safe_hash = ''; //cookies的键值uid|uid的hash|u_type 用于伪造登录

	function __construct($postCookiesName='') {
        //本地文件自动备份 所以session要移除出站点
        //session_save_path($GLOBALS['cfg_session_path']); //首先要声明session路径 方便控制session寿命 注意：不能在session_start之后声明
        @session_start();
        if(!$postCookiesName) {
            //如果只是单纯的 new Users(); 为防止获取users->getUserAttrib 需要将属性全部重新载入$this的内存中
            $userCookiesStr = isset($_SESSION[\Config::get('login_cookiesname')]) ? $_SESSION[\Config::get('login_cookiesname')]: '';
        } else {
            //只允许在app和flash内置上传中传递此hash ；ajax因为安全问题暴露uhash,不需要传递
            $userCookiesStr = str_replace('%7c','|',$postCookiesName);  //android端提交退换货图片时，如果post请求里uhash有带‘|’，会请求不到接口，把android端的post请求里的uhash中的‘|’转义为%7c，接口端再转回去‘|’
            $_SESSION[\Config::get('login_cookiesname')] = $userCookiesStr; //传入的session名字要存入给session
        }
        $userData = self::checkUserSafeStr($userCookiesStr);
        if(!$userData) {
            $this->exitUser();
        }
        $post_uid = $userData[0];
        $userPwdFive = $userData[3];
        //本地ajax提交的uid如果已经和session的不一致，要退出
        if(isset($_GET['local_uid']) && intval($_GET['local_uid']) != $post_uid) {
            $this->exitUser();
        }
        $userCookiesArray = self::getUserCache($userCookiesStr);
       /* print_r($userCookiesArray);
        exit;*/
        if(!$userCookiesArray) {
            $this->exitUser();
        } else {
            $this->userId = isset($userCookiesArray['userId']) ? $userCookiesArray['userId']: '';
            $this->userNick = isset($userCookiesArray['userNick']) ? $userCookiesArray['userNick']: '';
            $this->userPwdMd5 = isset($userCookiesArray['userPwdMd5']) ? $userCookiesArray['userPwdMd5']: '';
            $this->userType = isset($userCookiesArray['userType']) ? $userCookiesArray['userType']: '';
            $this->safe_hash = isset($userCookiesArray['safe_hash']) ? $userCookiesArray['safe_hash']: '';
            $this->sess_time = isset($userCookiesArray['sess_time']) ? $userCookiesArray['sess_time']: '';
            //超时操作要退出登录
            if( $post_uid !=  $this->userId || $userPwdFive !=  substr($this->userPwdMd5, 0, 5) || !$this->sess_time || time() - $this->sess_time  > \Config::get('cfg_session_time')) {
                $this->exitUser();
            } else {
                //未超时登录 就给session续时 每2分钟续时
                if(time() - $this->sess_time  > 120) {
                    $_SESSION[\Config::get('login_cookiesname')] = $userCookiesStr;
                    $userCookiesArray['sess_time'] = time();
                    self::saveUserCache($userCookiesStr, $userCookiesArray);
                }
            }
        }
	}
	//会员状态定义
    public static $userStatusPause = 0;
    public static $userStatusOk = 1;
    public static $userStatusLock = -1;
    //获取会员状态名字
    public static function getUserStatus($status=0) {
        if($status == self::$userStatusPause ) return '暂停';
        if($status == self::$userStatusOk ) return '正常';
        if($status == self::$userStatusLock ) return '锁定';
        return $status;
    }
    //会员身份类型 定义
    private static $userTypeFactory = 1; //工厂
    private static $userTypeEmployee = 3; //雇员
    //检测是否工厂身份
    public static function isFactory($uType=0) {
        return $uType == self::$userTypeFactory;
    }
    //检测是否雇员
    public static function isEmployee($uType=0) {
        return $uType == self::$userTypeEmployee;
    }
    //会员身份转中文
    public static function formatUtypeName($uType=0) {
        if(self::isFactory($uType)) return '工厂';
        if(self::isEmployee($uType)) return '雇员';
        return $uType;
    }

    //给登录身份加密钥 防止伪造登录 以及密码是否更改
    public static function hashUserSess($uid, $pwdFive) {
        return md5(md5("[$]".$uid."|pwd|". strtolower($pwdFive) ."[l*1000rui6ye000]"));
    }
    //生成支持伪造会员登录身份的密钥 //用于存储登录身份的cookies 或 判断登录
    public static function makeUserSafeLoginStr($uid, $pwdFive='', $userType=0) {
        return $uid .'|'. self::hashUserSess($uid, $pwdFive) .'|'. $userType .'|'. $pwdFive;
    }
    //校验会员的登录密钥 是否合法 $fromLocal 是否本地检测登录 是则需要md5判断
    public static function checkUserSafeStr($uStr='') {
        if(!$uStr || !strstr($uStr, '|'))  {
            return [0, 0, 0, 0];
        }
        $array_ = explode("|", $uStr);
        $uid_ = isset($array_[0]) ? $array_[0] : 0;
        $userPwdHash = isset($array_[1]) ? $array_[1] : '';
        $userType = isset($array_[2]) ? $array_[2] : '';
        $pwdFive = isset($array_[3]) ? $array_[3] : '';
        if(!$uid_ || !$userPwdHash|| !$userType|| !$pwdFive) {
            return [0, 0, 0, 0];
        }
        if($userPwdHash !== self::hashUserSess($uid_, $pwdFive)) {
            return [0, 0, 0, 0];
        }
        return [$uid_, $userPwdHash, $userType, $pwdFive];
    }

    //写入登录缓存
    public static function saveUserCache($cacheName='', $cacheObj=array()) {
        
        if(!$cacheName) return;
        $table_='s_user_login_cache';
        $oldData = DbBase::getRowBy($table_, 'l_id', "l_cachename='". $cacheName ."'");
        if(!$oldData) {
            $newData = array(
                'l_cachename' => $cacheName,
                'l_cache_content' => json_encode($cacheObj)
            );
            DbBase::insertRows($table_, $newData);
        } else {
            DbBase::updateByData($table_, array('l_cache_content' => json_encode($cacheObj) ), 'l_id='. $oldData['l_id']);
        }
    }

    //获取登录缓存
    public static function getUserCache($cacheName='') {
        if(!$cacheName) return array();
        $table_='s_user_login_cache';
        $oldData = Dbbase::getRowBy($table_, 'l_cache_content', "l_cachename='". $cacheName ."'");
        if(!$oldData) return array();
        return json_decode($oldData['l_cache_content'], true);
    }
    //是否管理员
    public static function isAdmin($userId=0) {
	    return $userId == $GLOBALS['cfg_admin_uid'];
    }
    //注册帐号
    public function createUser($regData, $tuijian_uid, $mytime='') {
        if(!$mytime) $mytime = Timer::now();
        //校对推荐人
        if(!is_numeric($tuijian_uid) || $tuijian_uid == 0) {
            $tuijian_uid = 0;
            $add_tuijian = false; //添加积分记录  推荐一人获得积分
        } else {
            if(!Dbbase::getRowBy("c_user", "u_id='". $tuijian_uid ."'")) {
                $tuijian_uid = 0;
                $add_tuijian = false; //添加积分记录  推荐一人获得积分
            } else {
                $add_tuijian = true;
            }
        }
        //创建用户
        if ( Dbbase::insertRows('c_user', $regData) != 1 ){
            return  '创建用户失败';
        }
        $newUid = Dbbase::lastInsertId();
        //推荐人记录
        if( $add_tuijian){
            //添加邀请记录
            $inviteData = array(
                'l_from_uid' => $tuijian_uid,
                'l_addtime' => $mytime,
                'l_new_uid' => $newUid,
                'l_day' => Timer::today(),
            );
            Dbbase::insertRows("c_user_invites", $inviteData);
        }
        return  $newUid;//返回uid
    }
	//结束用户的会话状态
	public function exitUser($postCookiesName = '')
	{
        if ($postCookiesName){
            $cookiesStr = $postCookiesName;
        } else {
            $cookiesStr = isset($_SESSION[\Config::get('login_cookiesname')]) ? $_SESSION[\Config::get('login_cookiesname')]: '';
        }
        //$cookiesStr = $_COOKIE[\Config::get('login_cookiesname')]; //会员已经登录过会生成的 cookies
        //setcookie($cookiesStr, '');
        $this->userId = 0;
        $this->userNick = '';
        $this->userPwdMd5 = '';
        $this->userType = 0;
        $this->safe_hash = '';
        $this->sess_time = 0;
        $_SESSION[\Config::get('login_cookiesname')] = '';
        if($cookiesStr) {
            // 销毁服务器的缓存
            self::saveUserCache($cookiesStr, array());
        }
	}

	//获得用户属性值
    function getUserAttrib($name) {
    	return $this->$name;
    }

    //根据用户id得到会员基本信息，不包括账户资金信息。
    public static function getUserInfo($uid=0, $fields='*'){
        if(is_numeric($uid)) {
            return DbBase::getRowBy("c_user", $fields, "u_id ={$uid}");
        } else {
            return DbBase::getRowBy("c_user", $fields, "u_nick ='{$uid}''");
        }
    }
    //根据用户id得到会员基本信息，不包括账户资金信息。
    public static function editUserInfo($uid=0, $data) {
        return DbBase::updateByData("c_user", $data, "u_id ={$uid}");
    }
    //根据用户昵称 缺省则显示帐号
    public static function getUserNick($uid){
        $uInfo = DbBase::getRowBy("c_user", 'u_name,u_nick', "u_id = {$uid}");
        if(!$uInfo) return $uid;
        return strlen(trim($uInfo['u_name']))>0 ? $uInfo['u_name'] : $uInfo['u_nick'];
    }
    //检测用户是否被注册
    function checkUserId($uNick) {
        return  DbBase::getRowBy("c_user", "u_nick = '". $uNick ."'");
    }
    //判断是否登录  $pageType 当前页面支持的会员身份 1.买家 2卖家 3雇员 4前台会员 5任何会员 6只允许门店和服务商身份时
    public function checkLogin($pageType = 5) {
        $hasLogin = true;
        //会员已经登录过会生成的 cookies 名字  , 格式 uid|hash(uid)|utype
        $userType = $this->userType;
        //检测当前登录身份 是否有权限访问此模块
        //$pageType 当前页面支持的会员身份 1.买家 2卖家 3雇员 4只允许门店和服务商身份时 5任何会员
        if($pageType == 1 || $pageType == 2 || $pageType == 3) {
            if($userType <> $pageType) {
                if($userType) $this->outType = 1;
                $hasLogin = false;
            }
        }
        //4前台会员
        if($pageType == 4) {
            if($userType != 1 && $userType !=2  && $userType !=8 ) {
                if($userType) $this->outType = 1;
                $hasLogin = false;
            }
        }
        //5任何会员
        if($pageType == 5) {
            if(!$userType ) $hasLogin = false;
        }
        if(!$hasLogin) {
            $this->exitUser();
        }
        return $hasLogin;
    }
	

    //生成会员头像
    public static function createUserFaceUrl($userId) {
        return $GLOBALS['cfg_user_face_path']."/". $userId."_lr_". Str::getMD5($userId.'|'.Str::getRam(8), 8).".jpg";
    }

	//平台帐号登录（内部登录可用帐号为昵称、手机、邮箱）
    public function checkUser($user_nick, $pwd, $isAdmin = false, $systemLogin = false) {
        
        //如果是系统登录，无需再作md5加密
		if(!$systemLogin) {
            $pwd = md5($pwd);
        }
        $uInfo = DbBase::getRowBy("c_user", "u_id,u_nick,u_logo,u_type,u_pwd,u_status,u_power", "u_nick='". $user_nick ."'");
        if (count($uInfo) == 0){
            return '00061';//00061
        }
		if ($uInfo['u_status'] == -1){
			return '0003';//此用户帐号已经被禁止登录
		}
        if ($pwd != $uInfo['u_pwd']){
			return '0002';//您的登录密码错误
		}
        $userType = $uInfo['u_type'];
        //检测是不是雇员身份
        if ($isAdmin) {
            if ($userType !=3) {
                return '00062';//您不是雇员,没有登录后台的权限
            }
        }
        $time = Timer::now();
        $userIp = Ip::getIp();
        //保存登录状态的cookiesName
        $cookiesStr = self::makeUserSafeLoginStr($uInfo['u_id'], substr($uInfo['u_pwd'], 0, 5), $userType);
        //写入登录日志
        $loginLogData = array(
            'l_uid' => $uInfo['u_id'],
            'l_unick' => $user_nick,
            'l_login_time' => $time,
            'l_login_ip' => $userIp,
        );
        DbBase::insertRows('s_user_login_log', $loginLogData);
        //修改最后登录时间
        $editData = array(
            'u_logintime' => $time
        );
        DbBase::updateByData('c_user', $editData, 'u_id='.$uInfo['u_id']);
        //用户数据要存储 登录成功 后面会直接使用
        $this->userId = $uInfo['u_id'];
        $this->userNick = ($uInfo['u_nick']);
        $this->userPwdMd5 = $uInfo['u_pwd'];
        $this->userType = $userType;//保存身份标志
        $this->safe_hash = $cookiesStr;//用于辅助登录
        //保存用户状态
        $cookiesArray = array(
            'userId'	=> $uInfo['u_id'],
            'userNick' => ($uInfo['u_nick']),
            'userType' => $userType,
            'userPwdMd5' => $uInfo['u_pwd'],
            'safe_hash' => $cookiesStr,
            'sess_time' => time()
        );
        $_SESSION[\Config::get('login_cookiesname')] = $cookiesStr;
        //生成用户唯一标识cookiesname ，用于每次判断身份的调用
        #setcookie($cookiesName, $cookiesName, time()+ ($this->loginday), '/'); //Cookies时间要加上现在时间
        //保存用户信息到数据库
        self::saveUserCache($cookiesStr, $cookiesArray);
		return '0001';//成功
	}

    //解绑微信登录 直接删除微信绑定记录
    function delWXByUid($uid){
        

        //直接解绑 无须删除数据
        return DbBase::updateByData("c_user_weixinlogin",
            array('w_uid'=> 0,'w_unionid' => date('YmdHis',time()).Str::getRam(12), 'w_openid'=> date('YmdHis',time()).Str::getRam(12)),
            "w_uid=".$uid);
    }
    //创建手机用户 ph+随机数字
    public static function createPhoneUser() {
        
        $u_nick = 'ph'.Str::getRam(8);
        //如果帐号已经被使用，重新生成
        if( DbBase::getRowBy("c_user", "u_nick='". $u_nick ."'", "u_id")) {
            $u_nick = self::createPhoneUser();
        }
        return $u_nick;
    }
    //判断会员是否vip
    public static function checkIfVip($uid=0) {
        
        $vipLog = DbBase::getRowBy('c_user_extra', 'e_vip', 'e_uid='.$uid);
        if(!$vipLog) return 0;
        return $vipLog['e_vip'];
    }
    //创建随机的用户帐号
    public static function createUnick() {
        
        $u_nick = 'sss'.Str::getRam(mt_rand(3,8));
        //如果帐号已经被使用，重新生成
        if( DbBase::getRowBy("c_user", "u_nick='". $u_nick ."'", "u_id")) {
            $u_nick = self::createUnick();
        }
        return $u_nick;
    }

    //操作会员类型
    public static $operatorUserType =  array(
        'reg_by_phone'=>"手机注册",
        'login'=>"登录",
        'change_password'=>"修改密码",
        'change_email'=>"修改邮箱",
        'change_info'=>"修改基本信息",
        'edit_power'=>"编辑权限",
        'lock_user'=>"锁定用户",
        'ok_user' => "恢复正常",
        'del_power_group_user'=>"移除权限组的用户",
        'add_power_group_user'=>"权限组添加用户",
        'edit_power_group'=>"修改权限组",
        'goto_user' => "登录会员",
        'join_in_position' => "加入岗位",
        'remove_position' => "移出岗位",
        'other' =>'其他',
    );
    //获取所有操作日志
    public static function getAllOperateType() {
        return self::$operatorUserType;
    }
    //获取操作日志名字
    public static function getOperateTypeName($dotype='') {
        return isset(self::$operatorUserType[$dotype]) ? self::$operatorUserType[$dotype] : $dotype;
    }
    //写入操作会员的日志
    public static function addUserOperateLog($operaterUid=0, $targetUid=0, $logType='', $reason='', $mytime='') {
        
        $mytime = !$mytime? Timer::now() : $mytime;
        $operateData= array(
            'l_operator_uid' => $operaterUid,
            'l_desc' => $reason,
            'l_target_uid'=> $targetUid,
            'l_type'=> $logType,
            'l_addday'=> Timer::today($mytime),
            'l_addtime'=> $mytime,
        );
        DbBase::insertRows('c_user_operate_log', $operateData);
    }
}