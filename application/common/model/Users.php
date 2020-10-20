<?php

namespace app\common\model;

use think\Model;
use think\Db;
use app\admin\library\Auth;
use think\Config;
use think\Validate;

/**
 * 用户模型
 */
class Users Extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    //admin->model
    public static $adminGroupId = 1; //定义管理员所在分组 1

    /**
     *  检测用户信息是否已经被注册
     * @param string $username
     * @param int $uid
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\Db Exception
     */

    public static function hasReg($fieldName='', $checkVal='', $uid=0) {
        if(!$checkVal) return true;
        if($uid) {
            return self::where($fieldName, $checkVal)->where('id', '<>', $uid)->find();
        } else {
            return self::where($fieldName, $checkVal)->find();
        }
    }

	//删除会员调整下线层级树
	public function fixUserPath($ids){
		if(!is_array($ids))$ids = explode(',', $ids);
		foreach($ids as $k => $v){
			if(empty($v))continue;
			$childs = $this->all("FIND_IN_SET('".$v."', path)");
			if(!empty($childs)){
				foreach($childs as $kk => $vv){
					if($vv->id == $v)continue;//排除自己
					$path = trim(str_replace(','.$v.',', '', ','.$vv->path.','), ',');
					$paths = explode(',', $path);
					$pid = intval(end($paths));
					$vv->save(array('pid' => $pid, 'path' => $path));
				}
			}
		}
	}

    //定义 admin 所有状态
    protected static $statusNormal = 1;
    protected static $statusLock = -1;
    //获取 admin 所有状态
    public static function getAdminAllStatus() {
        return [
            self::$statusNormal => '正常',
            self::$statusLock => '锁定',
        ];
    }
    //获取 admin 所有状态 给前端radio用
    public static function getAdminAllStatusForRadio() {
        $allStatus = self::getAdminAllStatus();
        $newData = [];
        foreach ($allStatus as $k =>$v) {
            $newData[] = [
                'value' => $k,
                'text' => $v,
            ];
        }
        return $newData;
    }
    //获取 admin 默认状态
    public static function getAdminDefaultStatus() {
        return self::$statusNormal;
    }
    //获取 user 默认状态
    public static function getUserNormalStatus() {
        return self::$statusNormal;
    }
    //获取 admin 正常状态
    public static function getAdminNormalStatus() {
        return self::$statusNormal;
    }
    //判断状态是否正确
    public static function isWrongStatus($status='') {
        $allStatus = array_keys(self::getAdminAllStatus());
        return !in_array($status, $allStatus);
    }

    //获取 admin 状态名字
    public static function getAdminStatusName($status=0) {
        $allStatus = self::getAdminAllStatus();
        return isset($allStatus[$status]) ? $allStatus[$status] : $status;
    }

    /**
     * 重置用户密码
     * @author baiyouwen
     */
    public static function resetPassword($uid, $NewPassword)
    {
        $salt = self::getfieldbyid($uid, 'salt');//取出用户的密码盐
        $passwd = self::encryptPassword($NewPassword, $salt);
        return self::where(['id' => $uid])->update(['password' => $passwd]);
    }

    // 密码加密
    public static function encryptPassword($password, $salt = '', $encrypt = 'md5')
    {
        return $encrypt($password . $salt);
    }
    //统计所有用户
    public static function countAllUser() {
        self::where(['utype'=> (new Auth())->getIdentBuyer()])->count();
    }
    //创建用户入口
    public static function createAdmin($params=[]) {
        if(!$params['username']) return('username不能为空');
        if(!$params['password']) return('密码不能为空');
        if(!$params['groupid']) return('分组不能为空');
        if(!isset($params['status']) || self::isWrongStatus($params['status'])) {
            $params['status'] = self::getAdminDefaultStatus();
        };
        //检测长度 验证码
        $rule = [
            'username'  => 'require|length:3,30',
            'password'  => 'require|length:3,30',
        ];
        if (Config::get('fastadmin.login_captcha'))
        {
            $rule['captcha'] = 'require|captcha';
        }
        $validate = new Validate($rule);
        $result = $validate->check($params);
        if (!$result)
        {
            return $validate->getError();
        }
        $userName = $params['username'];
        $email = isset($params['email']) ? $params['email'] : '';
        $params['salt'] = \fast\Random::alnum();
        $params['password'] = self::encryptPassword($params['password'], $params['salt']);
        $params['avatar'] = '/assets/img/avatar.png'; //设置新管理员默认头像。

        //检测帐号 邮箱
        if(self::where('username', $userName)->find())  return('username已经被注册');
        if($email) {
            if(self::where('email', $email)->find())  return('email已经被注册');
        }
        Db::startTrans();
        try {
            $result = self::create($params);
            if ($result === false)
            {
                throw new \Exception('error:'.self::getError());
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        return Db::getLastInsID();
    }
    //登录
    public static function adminLogin($data=[]) {
//        $data = [
//            'username'  => $username,
//            'password'  => $password,
//            'keeplogin'  => $keeplogin,
//            'captcha'  => $captcha,
//        ];
        $username = $data['username'];
        $password = $data['password'];
        $keeplogin = $data['keeplogin'];
        $rule = [
            'username'  => 'require|length:2,30',
            'password'  => 'require|length:3,30',
        ];
        if (Config::get('fastadmin.login_captcha'))
        {
            $rule['captcha'] = 'require|captcha';
        }
        $validate = new Validate($rule, [], ['username' => __('Username'), 'password' => __('Password'), 'captcha' => __('Captcha')]);
        $result = $validate->check($data);
        if (!$result)
        {
            return $validate->getError();
        }
        $Auth = new Auth();
        $result = $Auth->login($username, $password, $keeplogin ? 86400 : 0);
        if ($result === true)
        {
            return true;
        }
        else
        {
            $msg = $Auth->getError();
            return $msg;
        }
    }
    //手机登录 无须密码
    public static function phoneLogin($data=[]) {
//        $data = [
//            'username'  => $username,
//            'password'  => $password,
//            'keeplogin'  => $keeplogin,
//            'captcha'  => $captcha,
//        ];
        $mobile = $data['mobile'];
        $userInfo = self::field('username')->where(['mobile' => $mobile])->find();
        if(!$userInfo) return '手机未注册,无法登录';
        $username = $userInfo['username'];
        $Auth = new Auth();
        $result = $Auth->login($username, '', 86400, false);
        if ($result === true)
        {
            return true;
        }
        else
        {
            $msg = $Auth->getError();
            return $msg;
        }
    }
    //过滤掉不存在的uid
    public static function filterUid($uids='', $split_= ',') {
        if(!$uids) return '';
        if(strstr($uids, '，')) {
            $uids = str_replace('，', ',', $uids);
            $split_ = ',';
        }
        $uids = trim($uids, $split_);
        $uidArray = explode($split_, $uids);
        $existUidArray = [];
        foreach ($uidArray as $tmpUid) {
            $tmpUid = trim($tmpUid);
            if(!Users::get($tmpUid)) continue; //用户不存在 跳过
            $existUidArray[] = $tmpUid;
        }
        return join($split_, $existUidArray);
    }
    //获取用户姓名
    public static function getUserNames($uids='', $split_= ',') {
        if(!$uids) return '';
        if(strstr($uids, '，')) {
            $uids = str_replace('，', ',', $uids);
            $split_ = ',';
        }
        $uids = trim($uids, $split_);
        $uidArray = explode($split_, $uids);
        $existUidArray = [];
        foreach ($uidArray as $tmpUid) {
            $tmpUid = trim($tmpUid);
            if(!$uInfo = Users::get($tmpUid)) continue; //用户不存在 跳过
            $existUidArray[] = $uInfo['username'];
        }
        return join($split_, $existUidArray);
    }
    //获取用户的代理id
    public static function getAgentid($uid=0) {
        return self::getfieldbyid($uid, 'agent_id');
    }
}
