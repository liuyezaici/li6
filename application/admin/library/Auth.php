<?php

namespace app\admin\library;

use app\common\model\Users;
use app\admin\model\AuthRule;
use fast\Random;
use fast\Tree;
use fast\Addon;
use think\Config;
use think\Cookie;
use think\Request;
use think\Session;
use think\Db;

class Auth extends \fast\Power
{

    protected $_error = '';
    protected $requestUri = '';
    protected $breadcrumb = [];
    protected $logined = false; //登录状态
    protected static $adminIdentId = 0;//管理员的身份id 外部查询需要用到
    protected static $agentIdentId = 1;//代理商的身份id
    protected static $sellerIdentId = 2;//商家的身份id
    protected static $bhyIdentId = 3;//补货员的身份id
    protected static $buyerIdentId = 4;//买家的身份id
    protected $pwdMaxWrongTimes = 6;//密码最多可以错几次
    protected $_user = NULL; //内部当前user查询对象

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取当前Token
     * @return string
     */
    public function getToken()
    {
        return Session::get('user_token');
    }
    /**
     * 获取当前 user查询对象
     * @return string
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * 判断帐号是不是补货员
     */
    public function accountIsBhy($account)
    {
        $info = Users::get(['username' => $account]);
        if(!$info) {
            return '帐号不存在';
        }
        if(!$this->isBhy($info['utype'])) {
            return '您不是补货员(accountIsBhy)';
        }
        return true;
    }
    /**
     * 判断手机是不是补货员
     */
    public function phoneIsBhy($mobile)
    {
        $info = Users::field('utype')->where(['mobile' => $mobile])->find();
        if(!$info) {
            return '手机未注册不存在';
        }
        if(!$this->isBhy($info['utype'])) {
            return '您不是补货员:'. $info['utype'];
        }
        return true;
    }

    public function __get($authPath)
    {
        //user_token.id 会自动获取user_token里的id
        return Session::get('user_token.' . $authPath);
    }

    //生成token
    protected function createTokenKey($id, $keeptime, $expiretime, $token) {
        return md5(md5($id) . md5($keeptime) . md5($expiretime) . $token);
    }
    //获取 用户 所有类型
    public static function getAdminAllTypes() {
        return [
            self::$adminIdentId => '管理员',
            self::$agentIdentId => '代理商',
            self::$sellerIdentId => '商家',
            self::$buyerIdentId => '买家',
        ];
    }
    //获取 用户 所有类型 给前端radio用
    public static function getAdminAllTypesForRadio() {
        $allStatus = self::getAdminAllTypes();
        $newData = [];
        foreach ($allStatus as $k =>$v) {
            $newData[] = [
                'value' => $k,
                'text' => $v,
            ];
        }
        return $newData;
    }
    //获取 admin 状态名字
    public static function getAdminTypeName($typeid=0) {
        $allStatus = self::getAdminAllTypes();
        return isset($allStatus[$typeid]) ? $allStatus[$typeid] : $typeid;
    }



    /**
     * 根据Token初始化
     *
     * @param string       $token    Token
     * @return boolean
     */
    public function init($token='')
    {
        if ($this->_logined)
        {
            return TRUE;
        }
        if (!$token) {
            return FALSE;
        }
        if ($this->_error) {
            return FALSE;
        }
        $admin = Users::get(['token' => $token]);
        if (!$admin)
        {
            return FALSE;
        }
        $user_id = intval($admin['id']);
        if ($user_id > 0)
        {
            unset($admin['password']);//禁止输出密码 安全一点
            $admin->loginfailure = 0;
            $admin->logintime = time();
            $this->_user = $admin;
            $admin->token = $token;
            $admin->save();
            Session::set("user_token", ['identity' => $admin->utype] + $admin->toArray());
            $this->keeplogin(86400);
            return true;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * 管理员登录
     * 
     * @param   string  $username   用户名
     * @param   string  $password   密码
     * @param   int     $keeptime   有效时长
     * @return  boolean
     */
    public function login($username, $password, $keeptime = 0, $checkPwd=true)
    {
        $admin = Users::get(['username' => $username]);
        if (!$admin)
        {
            $this->setError('帐号不存在:'. $username);
            return false;
        }
        if ($admin->loginfailure >= $this->pwdMaxWrongTimes && time() - $admin->updatetime < 86400)
        {
           // $this->setError('请一天后再试');
            //return false;
        }
        //如果之前的密码已经被手动清空 （允许管理员手动调试） 则允许直接登录
        if($admin->password == '') {
            Users::resetPassword($admin->id, $password);
        }
        if($checkPwd) {
            if ($admin->password && $admin->password != Users::encryptPassword($password, $admin->salt))
            {
                $admin->loginfailure++;
                $admin->save();
                $this->setError('密码不正确');
                return false;
            }
        }
		if($admin->status !=  Users::getAdminNormalStatus()){
            $this->setError('状态已经被锁定');
			return false;
		}
        // 此时的Model中只包含部分数据
        $admin->loginfailure = 0;
        $admin->logintime = time();
        $admin->token = Random::uuid();
        $this->_user = $admin;
        $admin->save();
        Session::set("user_token", ['identity' => $admin->utype] + $admin->toArray());
        $this->keeplogin($keeptime);
        return true;
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $admin = Users::get(intval($this->id));
        if (!$admin)
        {
            Session::delete("user_token");
            Cookie::delete("keeplogin");
            return true;
        }
        $admin->token = '';
        $this->_user = null;
        $admin->save();
        //删除Token
        Session::delete("user_token");
        Cookie::delete("keeplogin");
        return true;
    }

    /**
     * 自动登录
     * @return boolean
     */
    public function autologin()
    {
        $keeplogin = Cookie::get('keeplogin');
        if (!$keeplogin)
        {
            return false;
        }
        list($id, $keeptime, $expiretime, $key) = explode('|', $keeplogin);
        if ($id && $keeptime && $expiretime && $key && $expiretime > time())
        {
            $admin = Users::get($id);
            if (!$admin || !$admin->token)
            {
                return false;
            }
            //token有变更
            if ($key != $this->createTokenKey($id, $keeptime, $expiretime, $admin->token))
            {
                return false;
            }
            Session::set("user_token", ['identity' => $admin->utype] + $admin->toArray());
            //刷新自动登录的时效
            $this->keeplogin($keeptime);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 刷新保持登录的Cookie
     * 
     * @param   int     $keeptime
     * @return  boolean
     */
    protected function keeplogin($keeptime = 0)
    {
        if ($keeptime)
        {
            $expiretime = time() + $keeptime;
            $key = $this->createTokenKey($this->id, $keeptime, $expiretime, $this->token);
            $data = [$this->id, $keeptime, $expiretime, $key];
            Cookie::set('keeplogin', implode('|', $data), 86400 * 30);
            return true;
        }
        return false;
    }
	
    //判断管理员身份类型
    //判断用户身份 是否代理商
    public static function identIsAgent($identity=null) {
        return $identity==self::$agentIdentId;
    }
    //判断用户身份 是否商家
    public static function identIsSeller($identity=null) {
        return $identity==self::$sellerIdentId;
    }
    //判断用户身份 是否买家
    public static function identIsBuyer($identity = '')
    {
        return $identity == self::$buyerIdentId;
    }
    //是否普通管理员
    //是否普通管理员
    public static function identIsNormalAdmin($identity=null) {
        return $identity==self::$adminIdentId;
    }
    //获取代理身份类型
    public static function getIdentAgent() {
        return self::$agentIdentId;
    }
    //获取商家身份类型
    public static function getIdentSeller() {
        return self::$sellerIdentId;
    }
    //获取商家身份类型
    public static function getIdentBuyer() {
        return self::$buyerIdentId;
    }
    //获取补货员身份类型
    public static function getIdentBuhuoyuan() {
        return self::$bhyIdentId;
    }
    //获取普通管理员类型
    public static function getIdentNormalAdmin() {
        return self::$adminIdentId;
	}
    //判断我是否补货员
    public function isBhy($utype='') {
        return self::$bhyIdentId === $utype;
	}

    public function checkAuth($authPath, $uid = '', $relation = 'or', $mode = 'url')
    {
        return parent::checkPower($authPath, $this->id, $relation, $mode);
    }

    /**
     * 检测当前控制器和方法是否匹配传递的数组
     *
     * @param array $arr 需要验证权限的数组
     */
    public function match($arr = [])
    {
        $request = Request::instance();
        $arr = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr)
        {
            return FALSE;
        }

        // 是否存在
        if (in_array(strtolower($request->action()), $arr) || in_array('*', $arr))
        {
            return TRUE;
        }

        // 没找到匹配
        return FALSE;
    }

    /**
     * 注册微信用户
     */
    public function registerWeixinUser($nickName, $avatar, $inviteData = [])
    {
        $ip = request()->ip();
        $time = time();
        $this->buyerCfg = Addon::getAddonConfig('buyer');
        $this->buyerGroup = Addon::getAddonConfigAttr('buyer', 'groupid');
        if(!$this->buyerGroup) {
            return ('未配置买家所在组');
        }
        $username = Random::alnum(20);
        $password = Random::alnum(6);
        $userToken = Random::uuid();
        $params =[
            'username' => $username,
            'nickname'  => $nickName,
            'avatar'  => $avatar,
            'salt'      => Random::alnum(),
            'loginip'   => $ip,
            'createip'   => $ip,
            'createtime'  => $time,
            'token'  => $userToken,
            'logintime' => $time,
            'status'    => Users::getUserNormalStatus()
        ];
        $params['pid'] = Users::$adminGroupId; //创建人
        $params['groupid'] = $this->buyerGroup; //所属分组
        $params['utype'] = $this->getIdentBuyer(); //买家类型
        $params['salt'] = Random::alnum();
        $params['password'] = Users::encryptPassword($password, $params['salt']);
        //账号注册时需要开启事务,避免出现垃圾数据
        Db::startTrans();
        try
        {
            $user = Users::create($params);
            //邀请注册时 需要执行打包事件
            if(isset($inviteData['invite_code']) && $inviteData['invite_code']) {
                $inviteAddon = \fast\Addon::getModel('userinvite');
                if($inviteAddon && method_exists($inviteAddon, 'runInviteKeyFunc')) {
                    $inviteAddon->runInviteKeyFunc($inviteData['invite_code'], $user->id, 'reg');
                }
            }
            Db::commit();
            // 此时的Model中只包含部分数据
            $this->_user = Users::get($user->id);
            $this->_token = $userToken;
            $this->_logined = TRUE;
            return TRUE;
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 检测是否登录
     *
     * @return boolean
     */
    public function isLogin()
    {
        $token = Session::get('user_token');
        if (!$token)
        {
            return false;
        }
        $this->logined = true;
        return true;
    }

    /**
     * 获取当前请求的URI
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * 设置当前请求的URI
     * @param string $uri
     */
    public function setRequestUri($uri)
    {
        $this->requestUri = $uri;
    }

    public function getMyGroupId($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;
        return parent::getMyGroupId($uid);
    }

    public function getRuleList($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;
//        print_r('getRuleList');
//        print_r($uid);
//        exit;
        return parent::getRuleList($uid);
    }

    public function getUserInfo($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;
        return $uid != $this->id ? Users::get(intval($uid)) : Session::get('user_token');
    }

    public function getRuleIds($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;
        return parent::getRuleIds($uid);
    }
    public function getMyRules($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;
        return parent::getMyRules($uid);
    }

    //判断用户是不是代理 支持多个uid
    public function userIsAgent($uids = '')
    {
        if(!$uids) return false;
        $uidArray = explode(',', $uids);
        foreach ($uidArray as $agentId_) {
            if(!Users::get(['id' => $agentId_, 'utype'=>self::$agentIdentId])) {
                return false;
            }
        }
        return true;
    }
    //判断用户是不是商家 支持多个uid
    public function userIsSeller($uids = '')
    {
        if(!$uids) return false;
        $uidArray = explode(',', $uids);
        foreach ($uidArray as $sellerId_) {
            if(!Users::get(['id' => $sellerId_, 'utype'=>self::$sellerIdentId])) {
                return false;
            }
        }
        return true;
    }
    public function isSuperAdmin()
    {
        return $this->getRuleIds() == '*' ? TRUE : FALSE;
    }

    /**
     * 获取管理员所属于的分组ID
     * @param int $uid
     * @return array
     */
    public function getGroupIds($uid = null)
    {
        $groups = $this->getMyGroupId($uid);
        $groupIds = [];
        foreach ($groups as $K => $v)
        {
            $groupIds[] = (int) $v['id'];
        }
        return $groupIds;
    }



    /**
     * 取出当前管理员所拥有权限的管理员
     * @param boolean $withself 是否包含自身
     * @param array $gids groupids 当前查询要限制于某些分组下
     * @param string $getType /agent/seller 获取子角色类型
     *
     * @return array
     */
    public function getChildrenAdminIds($withself = false, $getType = 'admin')
    {
        //打包自己id
        $addMyUid = function ($newUids) {
            if(is_string($newUids)) $newUids = explode(',', $newUids); //转数组
            array_push($newUids, $this->id);
            $newUids = array_unique($newUids);
            return trim(join(',', $newUids), ',');
        };
        //定义角色类型
        $normalAdmin = $this->getIdentNormalAdmin();
        $agentAdmin = $this->getIdentAgent();
        $sellerAdmin = $this->getIdentSeller();
        //方法：获取所有下级 (普通 管理员/代理/商家)
        $getSonAdmin = function ($parentIds=0, $utype='') use(&$getSonAdmin, $addMyUid) {
            $parentIds = trim($parentIds, ',');
            $map = [];
            $map['utype'] = $utype;
            $whereOr = [
                'pid' => ['in', $parentIds]
            ];
            $whereOr2 = [];
            //找上级：pid 或 agent_id
            if($this->identIsAgent($utype) || $this->identIsSeller($utype)) {
                $whereOr2 = [
                    'agent_id' => ['in', $parentIds]
                ];
            }
            $sonAdminIds = (array)Users::where($map)
                ->where(function($query) use($whereOr, $whereOr2){
                    $query->where($whereOr)->whereOr($whereOr2);
                })->column('id');

            if(!$sonAdminIds) return '';
            if(!$sonAdminIds) return '';
            $sonAdminIds = join(',', $sonAdminIds);
            $sonsonAdminIds = $getSonAdmin($sonAdminIds, $utype);
            if($sonsonAdminIds) {
                $sonAdminIds = $sonAdminIds .','.$sonsonAdminIds;
            }
            $sonAdminIds = trim($sonAdminIds, ',');
            if($sonAdminIds==0) return '';
            return $sonAdminIds;
        };
        //当前是管理员，要拿到所有子级admin
        $sonAdminIds = '';
        if($this->identIsNormalAdmin($this->identity)) {
            $sonAdminIds = $getSonAdmin($this->id, $normalAdmin);
        }
        if($getType == 'admin') {
            if($withself) $sonAdminIds = $addMyUid($sonAdminIds);
            return $sonAdminIds;
        }
        //我的下级管理员 不能加自己
        //获取下级的所有代理
        $allAgentIds = 0;
//        print_r($getType);exit;
        if($getType == 'agent') {
            if($sonAdminIds) {
                $getFromIds = $sonAdminIds.','.$this->id;
            } else {
                $getFromIds = $this->id;
            }
            $allAgentIds = $getSonAdmin($getFromIds, $agentAdmin);
            if($withself) $allAgentIds = $addMyUid($allAgentIds);
            return $allAgentIds;
        }
        //获取下级的所有商家
        if($getType == 'seller') {
            $allSellerIds = '';
            //查找子管理的商家 自然要包含我创建的
            if($allAgentIds) {
                $getFromIds = $allAgentIds.','.$this->id;
            } else {
                $getFromIds = $this->id;
            }
            //代理或管理 才可以获取所有下级管理
            if($this->identIsNormalAdmin($this->identity) || $this->identIsAgent($this->identity)) {
            $allAgentIds = $getSonAdmin($getFromIds, $agentAdmin);
            }
            //如果我是代理则获取包含我自己下面的商家
            if($this->identIsAgent($this->identity)) {
                $allAgentIds = $addMyUid($allAgentIds);//算上我自己
            }
            if($allAgentIds) $allSellerIds = $getSonAdmin($allAgentIds, $sellerAdmin);
            if($this->identIsAgent($this->identity) && !$allSellerIds) { //代理没有子商家 则让外部查询自己的数据
                $allSellerIds = $this->id;
            }
            if($this->identIsSeller($this->identity)) {
                $allSellerIds = $addMyUid($allSellerIds);//算上我自己
            }
            return $allSellerIds;
        }
    }

    /**
     * 取出当前管理员的下级分组
     * @param boolean $withself 是否包含当前所在的分组
     * @return array
     */
    public function getChildrenGroupIds($withself = false)
    {
        //取出当前管理员所有的分组
        $groupId = $this->getMyGroupId();
        $ruleIds = $this->getRuleIds();
        $groupModel = new \app\admin\model\AuthGroup;
        $strClass = new \fast\Str;

        // 取出所有分组
        $groupList = $groupModel->where(['status' => $groupModel->getAdminGroupNormalStatus()])->select();
        $objList = [];
//        print_r('$ruleIds:');
//        print_r(json_encode($ruleIds));
//        exit;
        if ($ruleIds === '*') {
            $objList = $groupList;
//            $objList = json_encode($groupList);
//            print_r($objList);
//            exit;
        } else {
//            print_r('$groupList:');
//            print_r(json_encode($groupList));
//            exit;
            $groupList = collection($groupList)->toArray();
            $objList = $strClass::diguiArray($groupList, $groupId, 'sons', 'pid', 'id');
            $mergeList = [];
            foreach ($objList as $k => &$tmpV)
            {
                if(!empty($tmpV['sons'])) $mergeList = array_merge($mergeList, $tmpV['sons']);
                unset($tmpV['sons']);
                unset($tmpV);
            }
            if($mergeList) $objList = array_merge($objList, $mergeList);
//            print_r('$groupId:'.$groupId);
//            print_r('$objList:');
//            print_r(json_encode($objList));
//            exit;
        }
        $childrenGroupIds = [];
        foreach ($objList as $k => $v)
        {
            $childrenGroupIds[] = $v['id'];
        }
//        print_r($childrenGroupIds);exit;
        if ($withself)
        {
            array_push($childrenGroupIds, $groupId);
//            print_r($childrenGroupIds);
            $childrenGroupIds = array_unique($childrenGroupIds);
        }

//        print_r($childrenGroupIds);
//        exit;
        return $childrenGroupIds;
    }

    //获取代理的所有商家
    public function getAgentSellerIds($agentId=0) {
        //方法：获取所有下级 (/代理/商家)
        $getSonAdmin = function ($parentIds=0, $utype='') use(&$getSonAdmin) {
            $parentIds = trim($parentIds, ',');
            $map = [];
            if($utype) {
                $map['utype'] = $utype;
            }
            $whereOr = [
                'pid' => ['in', $parentIds]
            ];
            $whereOr2 = [];
            //找上级：pid 或 agent_id
            if($this->identIsAgent($utype) || $this->identIsSeller($utype)) {
                $whereOr2 = [
                    'agent_id' => ['in', $parentIds]
                ];
            }
            $sonAdminIds = (array)Users::where($map)
                ->where(function($query) use($whereOr, $whereOr2){
                    $query->where($whereOr)->whereOr($whereOr2);
                })->column('id');
            if(!$sonAdminIds) return '';
            $sonAdminIds = join(',', $sonAdminIds);
            $sonsonAdminIds = $getSonAdmin($sonAdminIds, $utype);
            if($sonsonAdminIds) {
                $sonAdminIds = $sonAdminIds .','.$sonsonAdminIds;
            }
            $sonAdminIds = trim($sonAdminIds, ',');
            if($sonAdminIds==0) return '';
            return $sonAdminIds;
        };
        $sonAgentIds = $getSonAdmin($agentId, self::$agentIdentId);
        $sonAgentIdArray = explode(',', $sonAgentIds);
        $sonAgentIdArray[] = $agentId;
        $sonAgentIdArray = array_unique($sonAgentIdArray);
        $sonAgentIds = join($sonAgentIdArray, ',');
        $sonSellerIds = $getSonAdmin($sonAgentIds, self::$sellerIdentId);
        return $sonSellerIds;
    }
    /**
     * 获得面包屑导航
     * @param string $path
     * @return array
     */
    public function getBreadCrumb($path = '')
    {
        if ($this->breadcrumb || !$path)
            return $this->breadcrumb;
        $path_rule_id = 0;
        foreach ($this->rules as $rule)
        {
            $path_rule_id = $rule['auth_path'] == $path ? $rule['id'] : $path_rule_id;
        }
        if ($path_rule_id)
        {
            $this->breadcrumb = Tree::instance()->init($this->rules)->getParents($path_rule_id, true);
            foreach ($this->breadcrumb as $k => &$v)
            {
                $v['url'] = url($v['auth_path']);
                $v['title'] = __($v['title']);
            }
        }
        return $this->breadcrumb;
    }

    /**
     * 获取左侧菜单栏
     *
     * @param array $params URL对应的badge数据
     * @return string
     */
    public function getSidebar($params = [], $fixedPage = 'dashboard')
    {
        $colorArr = ['red', 'green', 'yellow', 'blue', 'teal', 'orange', 'purple'];
        $colorNums = count($colorArr);
        $badgeList = [];
        $module = request()->module();
        // 生成菜单的badge
        foreach ($params as $k => $v)
        {

            $url = $k;

            if (is_array($v))
            {
                $nums = isset($v[0]) ? $v[0] : 0;
                $color = isset($v[1]) ? $v[1] : $colorArr[(is_numeric($nums) ? $nums : strlen($nums)) % $colorNums];
                $class = isset($v[2]) ? $v[2] : 'label';
            }
            else
            {
                $nums = $v;
                $color = $colorArr[(is_numeric($nums) ? $nums : strlen($nums)) % $colorNums];
                $class = 'label';
            }
            //必须nums大于0才显示
            if ($nums)
            {
                $badgeList[$url] = '<small class="' . $class . ' pull-right bg-' . $color . '">' . $nums . '</small>';
            }
        }

        // 读取管理员当前拥有的权限节点
        $userRule = $this->getRuleList();
//        print_r('$userRule');
//        print_r($userRule);
//        exit;
        $select_id = 0;
        // 必须将结果集转换为数组
        $ruleList = collection(AuthRule::where(
            ['ismenu'=> 1])
            ->order('order', 'desc')->select())->toArray();
//        print_r('$ruleList');
//        print_r($ruleList);
//        exit;
        foreach ($ruleList as $k => &$v)
        {
            if (!in_array($v['auth_path'], $userRule))
            {
                unset($ruleList[$k]);
                continue;
            }
            $select_id = $v['auth_path'] == $fixedPage ? $v['id'] : $select_id;
            $v['url'] = '/' . $module . '/' . $v['auth_path'];
            $v['badge'] = isset($badgeList[$v['auth_path']]) ? $badgeList[$v['auth_path']] : '';
            $v['title'] = __($v['title']);
        }
        // 构造菜单数据
        Tree::instance()->init($ruleList);
        $menu = Tree::instance()->getTreeMenu(
            0,
            '<li class="@class"><a href="@url@addtabs" addtabs="@id" url="@url"><i class="@icon"></i> <span>@title</span> <span class="pull-right-container">@caret @badge</span></a> @childlist</li>',
            $select_id, '',
            'ul', 'class="treeview-menu"');
        return $menu;
    }

    /**
     * 设置错误信息
     *
     * @param $error 错误信息
     */
    public function setError($error)
    {
        $this->_error = $error;
        return $this;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->_error ? __($this->_error) : '';
    }

    /**
     * 修改密码
     * @param string    $newpassword        新密码
     * @param string    $oldpassword        旧密码
     * @param bool      $ignoreoldpassword  忽略旧密码
     * @return boolean
     */
    public function changepwd($newpassword, $oldpassword = '', $ignoreoldpassword = false)
    {
        $admin = Users::get(intval($this->id));
        if (!$admin)
        {
            $this->setError('用户名不正确');
            return false;
        }
        //判断旧密码是否正确
        if ($admin->password == Users::encryptPassword($oldpassword, $admin->salt)){
            $admin->salt = Random::alnum();//重置密码盐
            $admin->password =  Users::encryptPassword($newpassword, $admin->salt);
            $admin->save();
            return true;
        }
        else
        {
            $this->setError('密码不正确');
            return false;
        }
    }

    /**
     * 通过uid 和 第三方登录的记录ID 直接登录账号
     * @param int $user_id
     * @return usertoken
     */
    public function loginByUidAndThirdid($user_id, $third = 0)
    {
        $user = Users::get($user_id);
        if ($user)
        {
            $ip = request()->ip();
            $time = time();
            $userToken = Random::uuid();
            //记录本次登录的IP和时间
            $user->loginfailure = 0;
            $user->loginip = $ip;
            $user->logintime = $time;
            $user->token = $userToken;
            $user->save();
            $this->_user = $user;
            $this->_token = $userToken;
            $this->_third = $third;
            $this->_logined = TRUE;
            $this->keeplogin();
            Session::set("user_token", ['identity' => $user->utype] + $user->toArray());
            return $userToken;

        }
        else
        {
            return '';
        }
    }

}
