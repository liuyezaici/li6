<?php

namespace app\tool\controller;

use app\common\controller\Frontend;
use think\Config;
use think\Db;
use fast\Addon;
use app\admin\library\Auth;
use app\common\model\Users;
use app\admin\addon\usercenter\model\Third;
use OSS\OssClient;
class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function saveTask() {
        $list = input('list', '');
        file_put_contents(LOG_PATH .'task.txt', '|'.base64_encode(json_encode($list)));exit;
        echo 'success';
    }

    public function getTask() {
        $taskStr =  file_get_contents(LOG_PATH .'task.txt');
        if(!$taskStr) {
            print_r(json_encode(['code'=>1, 'msg'=> 'noTask']));
            exit;
        }
        $list = explode('|', $taskStr);
        $outTaskList = [];
        foreach ($list as $str_) {
            if($str_) {
                $outTaskList[] = json_decode(base64_decode($str_), true);
            }
        }
        $this->success('hasTask', '', ['task' => $outTaskList]);
    }
    public function test() {
        print_r(new Db());exit;
        echo 666;
    }

    public function oss() {
        require_once(ROOT_PATH .'vendor/ali_oss/autoload.php');
        require_once(ROOT_PATH .'vendor/ali_oss/samples/Common.php');
        $bucketName = \Common::getBucketName();
        $cacheUrl = 'http://www.1000se.com/shoppic/2019-04-16/2019041662_01.jpg';
        $object = 'test1.jpg';
        $ossClient = \Common::getOssClient();
        $status = $ossClient->uploadFile($bucketName, $object, $cacheUrl);
        print_r($status);exit;
    }

    //百度地图 搜索场地
    public function map() {
        $cfg = Addon::getAddonConfig('weixinpay');
        if(!$cfg) {
            $this->error('未配置微信支付信息');
        }
        if(!isset($cfg['JSAPI'])) {
            $this->error('未配置微信支付JSAPI信息');
        }
        $appid = $cfg['JSAPI']['appid'];
        if(!$appid) {
            $this->error('未配置微信支付的 appid');
        }
        $userToken = '';
        $userAppid = '';
        $this->auth = Auth::instance();
        $uid = $this->auth->id;
        //测试阶段默认uid
        $testToken = Config::get('test_token');
        if(!$uid && $testToken) {
            $userToken = $testToken;
            $uid = Users::getfieldbytoken($userToken, 'id');
        }
        if($uid) {
            $userToken = Users::getfieldbyid($uid, 'token');
            $userAppid = Third::getfieldbyuserId($uid, 'openid');
        }
        $city = input('city', '', 'trim');
        $this->view->assign('weixin_appid', $appid);
        $this->view->assign('user_openid', $userAppid);
        $this->view->assign('city', $city);
        $this->view->assign('baiduApiKey', Config::get('baiduApiKey'));
        $this->view->assign('qqApiKey', Config::get('qqApiKey'));
        $this->view->assign('appName', Config::get('qqApiName'));
        $token = \think\Session::get('user_token')['token'];
        $this->view->assign('user_token', $token ? $token : Config::get('test_token'));
        print_r($this->view->fetch());
    }
    //百度经纬度
    public function baidumapjingweidu(){
        $lng = input('lng', '', 'trim');
        $lat = input('lat', '', 'trim');
        $city = input('city', '', 'trim');
        $this->view->assign('lng', $lng);
        $this->view->assign('lat', $lat);
        $this->view->assign('city', $city);
        $this->view->assign('baiduApiKey', Config::get('baiduApiKey'));
        $this->view->assign('qqApiKey', Config::get('qqApiKey'));
        print_r($this->view->fetch());
    }
}
