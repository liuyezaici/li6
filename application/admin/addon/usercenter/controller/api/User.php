<?php

namespace app\admin\addon\usercenter\api\controller;

use app\api\controller\Common;
use app\common\library\Ems;
use app\admin\addon\sms\model\Sms;
use app\common\model\Users as userModel;
use app\common\model\Users;
use fast\Addon;
use fast\Random;
use think\Validate;
use think\Config;
use think\Db;
use app\admin\addon\usercenter\library\Service;

/**
 * 会员接口
 */
class User extends Common
{
    protected $noNeedLogin = ['login', 'mobilelogin', 'register', 'thirdlogin', 'thirdticket'];
    protected $noNeedRight = '*';
    protected $version = '';

    public function _initialize()
    {
        parent::_initialize();
        $this->addonName = 'usercenter';
    }

    //我的钱包
    public function myWallet(){

        $uInfo = db('users')->where(['id'=>$this->auth->id])->field('id,username,avatar')->find();
        //获取余额
        $uMoneyInfo = Addon::getModel('money')->getUserMoneyInfo($this->auth->id);

        $uIncome = $uMoneyInfo['income'];
        $uDeposit = $uMoneyInfo['deposit'];
        $uMoney = $uMoneyInfo['money'];
        $umoneygift = $uMoneyInfo['money_gift'];

        $data = [
            'uInfo' =>$uInfo,
            'uIncome'   =>$uIncome,
            'uMoney'   =>$uMoney,
            'uDeposit'  =>$uDeposit,
            'umoneygift'  =>$umoneygift,
        ];
        $this->success('success',$data);
    }
	
    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }

    /**
     * 会员登录
     * 
     * @param string $account 账号
     * @param string $password 密码
     */
    public function login()
    {
        $account = input('account');
        $password = input('password');
        if (!$account) $this->error('未输入账号');
        if (!$password) $this->error('未输入密码');
        if (Validate::is($account, 'mobile')) {
            $account = userModel::getFieldbymobile($account, 'username');
            if (!$account) $this->error('手机未注册');
        }
        $ret = $this->auth->login($account, $password);
        if ($ret) {
            $this->success('登录成功', $this->auth->getUserinfo());
        } else {
            $this->error($this->auth->getError());
        }
    }

    //检查用户是否绑定手机
    public function checkIsbangMobile()
    {
        $mobile = userModel::where(['id'=>$this->auth->id])->value('mobile');
        if($mobile) {
            $this->success('已绑定',true);
        }else{
            $this->error('未绑定',false);
        }

    }


    /**
     * 手机验证码登录/注册
     * 
     * @param string $mobile 手机号
     * @param string $captcha 验证码
     */
    public function mobilelogin()
    {
        $mobile = input('mobile');
        $captcha = input('captcha');
        $inviteCode = input('invite_code');
        if (!$mobile) $this->error('手机不能为空');
        if (!$captcha) $this->error('验证码不能为空');
        if (!$mobile || !$captcha)
        {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($mobile, 'mobile')) $this->error('手机格式不正确');
		\think\Hook::add('sms_check', function() {
            return true;
        });
        if (!Sms::checkSms($mobile, $captcha, 'mobilelogin'))
        {
            $this->error(__('Captcha is incorrect'));
        }
        $user = userModel::hasReg('mobile', $mobile);
        if ($user)
        {
            //如果已经有账号则直接登录
            $ret = $this->auth->direct($user->id);
        }
        else
        {

            $ret = $this->auth->register($mobile, Random::alnum(), '', $mobile,[], ['invite_code'=>$inviteCode]);
        }
        if ($ret)
        {
            Sms::flush($mobile, 'mobilelogin');
            $this->success(__('Logged in successful'), $this->auth->getUserinfo());
        }
        else
        {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注册会员
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email 邮箱
     * @param string $mobile 手机号
     */
    public function register()
    {
        $userName = input('username');
        $password1 = input('password1');
        $password2 = input('password2');
        $inviteCode = input('invite_code');

        if (!$userName)  $this->error('请输入帐号');
        if (!$password1)  $this->error('请输入密码1');
        if (!$password2)  $this->error('请输入密码2');
        if (strlen($password2) < 6)  $this->error('密码至少6位');
        if (strlen($password2) > 30)  $this->error('密码最多30位');
        if ($password1 != $password2) $this->error('密码不一致');
        //检测帐号 邮箱
        if(Users::where('username', $userName)->find())  return('帐号已经被注册');
        Db::startTrans();
        try {
            $params = [
                'username' => $userName,
                'password' => $password1,
            ];
            $params['createip'] = \fast\Ip::getIp();
            $params['utype'] = $this->auth->getIdentBuyer();
            $params['createtime'] = time();
            $params['salt'] = Random::alnum();
            $params['password'] = userModel::encryptPassword($params['password'], $params['salt']);
            $result = Users::create($params);
            if ($result === false)
            {
                throw new \Exception('error:'.Users::getError());
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        $this->success('注册成功');
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        $this->success(__('Logout successful'));
    }

    //获取用户信息[缓存]
	public function profile(){
        $userInfo = $this->auth->getUserinfo();
        $mobile = $userInfo['mobile'];
        if($mobile) $mobile = substr($mobile, 0, 3) & '****' & substr($mobile, -4, 4);
        $userInfoOut = [
            'nickname' => $userInfo['nickname'],
            'avatar' => $userInfo['avatar'],
            'mobile' => $mobile,
        ];
		$this->success(__('Profile successful'), $userInfoOut);
	}
    //获取用户信息 -实时更新
	public function getInfo(){
        $userInfo = userModel::get($this->auth->id);
        $mobile = $userInfo['mobile'];
        if($mobile) $mobile = substr($mobile, 0, 3) & '****' & substr($mobile, -4, 4);
        $userInfoOut = [
            'uid' => $userInfo['id'],
            'nickname' => $userInfo['nickname'],
            'mobile' => $mobile,
        ];
		$this->success(__('Profile successful'), $userInfoOut);
	}

    /**
     * 修改会员个人信息
     *
     * @param string $avatar 头像地址
     * @param string $username 用户名
     * @param string $nickname 昵称
     * @param string $bio 个人简介
     */
    public function edit()
    {
        $user = $this->auth->getUser();
		$post = input('post.');
//        print_r($user);exit;
        if(isset($post['nickname']))$user->nickname = $post['nickname'];
        if(isset($post['bio']))$user->bio = $post['bio']; //格言
        if(isset($post['avatar']))$user->avatar = $post['avatar'];//头像
        if(isset($post['birthday']))$user->avatar = $post['birthday'];//生日
        if(isset($post['androidtoken']))$user->androidtoken = $post['androidtoken'];
        if(isset($post['iphonetoken']))$user->iphonetoken = $post['iphonetoken'];

		$avatar = $this->upload('avatar', true);
		if($avatar === false){
			//$this->error(__('No file upload or server upload limit exceeded'));
		}elseif($avatar['url']){
			$user->avatar = $avatar['url'];
		}else{
			$this->error($avatar);
		}

        $user->save();
		foreach($post as $k => $v){
			$this->auth->$k = $v;
		}
        $this->success('修改成功');
    }

    /**
     * 修改邮箱
     *
     * @param string $email 邮箱
     * @param string $captcha 验证码
     */
    public function changeemail()
    {
        $user = $this->auth->getUser();
        $email = $this->request->post('email');
        $captcha = input('captcha');
        if (!$email || !$captcha)
        {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($email, "email"))
        {
            $this->error(__('Email is incorrect'));
        }
        if (userModel::hasReg('email', $email, $this->auth->id))
        {
            $this->error(__('Email already exists'));
        }
        $result = Ems::check($email, $captcha, 'changeemail');
        if (!$result)
        {
            $this->error(__('Captcha is incorrect'));
        }
        $user->email = $email;
        $user->save();

        Ems::flush($email, 'changeemail');
        $this->success();
    }


    /**
     * 修改手机号
     *
     * @param string $email 手机号
     * @param string $captcha 验证码
     */
    public function changemobile()
    {
        $mobile = input('new_mobile');
        $captcha = input('code');
        $events = 'changemobile';
        if (!$mobile || !$captcha)
        {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($mobile, 'mobile'))
        {
            $this->error(__('Mobile is incorrect'));
        }
        if (userModel::hasReg('mobile', $mobile, $this->auth->id))
        {
            $this->error(__('Mobile already exists'));
        }
		\think\Hook::add('sms_check', function() {
            return true;
        });
        $result = Sms::checkSms($mobile, $captcha, $events);
        if ($result !== true)
        {
            $this->error($result);
        }
        userModel::where('id', $this->auth->id)->update(['mobile'=>$mobile]);
        Sms::setSmsUsedByMobile($mobile, $events);
        $this->success('手机绑定成功');
    }

    /**
     * 微信等第三方登录
     *
     * @param string $platform 平台名称 (qq/wechat/weibo/wechatapp/app)
     * @param string $code 用户授权微信返回的Code
     */
    public function thirdlogin()
    {
        //如果用户已经登录过，刷新而已 则返回用户的token和openid
        if ($this->auth->getToken())
        {
            $this->success('登录成功', [
                'userToken' => $this->auth->getToken(),
                'openid' => userModel::getfieldbyuserid($this->auth->id, 'openid'),
            ]);
        }
        $platform = input("platform");
        $code = input("code");
        $config = Addon::getAddonConfig($this->addonName);
        //app_id,app_secret,callback
        if (!$config)
        {
            \fast\Log::addlog('未配置'. $platform .'的登录信息');
            $this->error('未配置'. $platform .'的登录信息');
        }
        \fast\Log::addlog('platform:'. $platform );
        try {
            if($platform =='wechat') {
                $app = new \app\admin\addon\usercenter\library\Wechat($config['wechat']);
            } else if($platform =='qq') {
                $app = new \app\admin\addon\usercenter\library\Qq($config['qq']);
            } else if($platform =='weibo') {
                $app = new \app\admin\addon\usercenter\library\Weibo($config['weibo']);
            } else if($platform =='wechatapp') {
                $app = new \app\admin\addon\usercenter\library\WechatApp($config['wechatapp']);
            } else {
                $this->error('不支持的 platform:'. $platform);
            }
        } catch  (\Exception $e) {
            \fast\Log::addlog('类加载失败'. $e->getMessage());
        }
//        \fast\Log::addlog('tttttt');
        //通过code换access_token和绑定会员
        $result = $app->getUserInfo(['code' => $code]);
//        \fast\Log::addlog('getUserInfo');
//        \fast\Log::addlog($result);
        if ($result)
        {
            $openid = $result['openid'];
            //保存第三方登录uniqueid和openid等信息
            $userData = Service::connect($platform, $result);
            if(!is_array($userData)) {
                \fast\Log::addlog('Service::connect失败:'. $userData);
            }
            if ($userData)
            {
                $this->success('登录成功.'.$this->auth->isLogin(), [
                    'userToken' => $userData['userToken'],
                    'openid' => $openid,
                ]);
            }
        }
        $this->error('登录失败.'.$this->auth->isLogin());
    }

	public function thirdticket(){
        $platform = input("platform");
        $configResult = Addon::getAddonConfig($this->addonName);
        if (!$configResult || !isset($configResult[$platform]))
        {
            $this->error('未配置'. $platform .'的登录信息');
        }

        $thirdPath = '\app\admin\\addon\\usercenter\library\\'.$platform;
        if(!class_exists($thirdPath)) {
            \fast\Log::addlog('类'. $thirdPath .'不存在');
            $this->error('类'. $platform .'不存在');
        }
        $app = new $thirdPath($configResult);
        //通过code换access_token和绑定会员
        $result = $app->{$platform}->getTicket();
        exit(json_encode(['code'=>1,'msg'=>'获取成功','data'=>$result]));
//		$this->success('', $result);
	}

    /**
     * 重置密码
     *
     * @param string $mobile 手机号
     * @param string $newpassword 新密码
     * @param string $captcha 验证码
     */
    public function resetpwd()
    {
        $email = input("email");
        $newpassword = input("newpassword");
        $captcha = input("captcha");
        if (!$newpassword || !$captcha)
        {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($email, "email"))
        {
            $this->error(__('Email is incorrect'));
        }
        $emailEven = 'resetpwd';
        $user = userModel::getByEmail($email); //tp魔术方法 getBy
        if (!$user)
        {
            $this->error(__('User not found'));
        }
        $ret = Ems::check($email, $captcha, $emailEven);
        if (!$ret)
        {
            $this->error(__('Captcha is incorrect'));
        }
        Ems::flush($email, $emailEven);
        //模拟一次登录
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($newpassword, '', true);
        if ($ret) {
            $this->success(__('Reset password successful'));
        } else {
            $this->error($this->auth->getError());
        }
    }
    /**
     * 发送邮件
     *
     * @param string $mobile 手机号
     * @param string $newpassword 新密码
     * @param string $captcha 验证码
     */
    public function sendEmail()
    {
        $email = input("email");
        $emailEven = input("event");
        if (!$email || !$emailEven)
        {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($email, "email"))
        {
            $this->error(__('Email is incorrect'));
        }
        $user = userModel::getByEmail($email); //tp魔术方法 getBy
        if (!$user)
        {
            $this->error('邮箱未注册');
        }
        Ems::send($email, '', $emailEven);
        $this->success('发送成功');
    }
    /*
        * 验证旧密码来重置密码
        *$oldpassword          旧密码
        * $newpassword1        验证的密码
        * $newpassword2        新密码
        * */
    public function resetpwdwithOldPwd(){
        $oldpassword=input("oldpassword");
        $newpassword1=input("newpassword1");
        $newpassword2=input("newpassword2");
        if(!$oldpassword || !$newpassword2 || !$newpassword1){
            $this->error(__('请保证密码、确认密码、新密码输入完整'));
        }
        if($oldpassword != $newpassword2){
            $this->error(__('密码或确认密码不一致'));
        }
        if($this->auth->changepwd($newpassword1, $oldpassword, false)){
            $this->success(__('Reset password successful'));
        }else{
            $this->error($this->auth->getError());
        }
    }

    /*检查用户需要绑定手机*/
    public function checkUserNeedBangding(){
        $usercenterModel = Addon::getModel('usercenter');
        $needBanding = $usercenterModel->checkUserNeedBangding($this->auth->id);
        $this->success('success',$needBanding);
    }

    //访问头像
    public function avatar($id=0) {
        $uid = intval($id);
        $upload = Config::get('upload');
        $fileUrl = ROOT_PATH . $upload['avatar_dir'] .'/'. substr($uid, 0, 1) .'/'. $uid.'.jpg';
        if(!file_exists($fileUrl)) {
//            echo 'file_no_exist:'. $fileUrl;
//            return;
            $fileUrl = ROOT_PATH .'/assets/img/avatar.png';
        }
        $info = getimagesize($fileUrl);
        $geshi = $info['mime'];
        $im = file_get_contents($fileUrl);
        Header("Content-type: {$geshi}");
        echo($im);
    }

    /**
     * 上传文件
     *  上传url /adppp/fujian/index/upload/?addon=Viptc
     */
    public function uploadAvatar()
    {
        $fileObj = $this->request->file();
        $fileNameArray = array_keys($fileObj);
        $fileName = $fileNameArray[0];
        $file = $this->request->file($fileName);
        if (empty($file))
        {
            $this->error(__('No file upload or server upload limit exceeded'));
        }

        //判断是否已经存在附件
        $upload = Config::get('upload');
        preg_match('/(\d+)(\w+)/', $upload['maxsize'], $matches);
        $type = strtolower($matches[2]);
        $typeDict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $size = (int) $upload['maxsize'] * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);
        $fileInfo = $file->getInfo();
        $suffix = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $suffix = $suffix ? $suffix : 'file';

        $mimetypeArr = explode(',', $upload['mimetype']);
        $typeArr = explode('/', $fileInfo['type']);
        //验证文件后缀
        if ($upload['mimetype'] !== '*' && !in_array($suffix, $mimetypeArr) && !in_array($fileInfo['type'], $mimetypeArr) && !in_array($typeArr[0] . '/*', $mimetypeArr))
        {
            $this->error(__('Uploaded file format is limited'));
        }
        $uploadDir = $upload['avatar_dir'] .'/'. substr($this->auth->id, 0, 1);
        $fileName = $this->auth->id . '.jpg';
        //
        $splInfo = $file->validate(['size' => $size])->move(ROOT_PATH . $uploadDir, $fileName);
        if ($splInfo)
        {
            $fileUrlLocal = $uploadDir . $splInfo->getSaveName();
            \think\Hook::listen("upload_after", $attachment);
            $this->success('上传成功', [
                'url' => $fileUrlLocal
            ]);
        }
        else
        {
            // 上传失败获取错误信息
            $this->error($file->getError());
        }
    }
}
