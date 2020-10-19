<?php

namespace app\tool\controller;

use app\common\controller\Frontend;
use think\Config;
use think\Db;
use fast\Addon;
use fast\File;
use app\admin\library\Auth;
use app\common\model\Users;
use app\admin\addon\usercenter\model\Third;
use OSS\OssClient;

class Upload extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    //上传采集图片
    public function downImgs(){
        set_time_limit(60);
        $datum = input('datum');
        $caijiid = input('caijiid', 0, 'intval');
        $finish = input('finish', 0, 'intval');
        if(!$datum) {
            $this->error('no urls');
        }
        $v= json_decode($datum, true);

        // [surl] => https://img.nihaojewelry.com/media/2019/12/26/1210160764426522624_thumb_600x600/NHSC191257.jpg
        //    [nurl] => /uploads/cached/2020-05-30/49/cover.jpg
        //    [flag] => cover
        //    [fid] => 2304
        $sUrl = $v['surl'];
        $nurl = $v['nurl'];
        $flag = $v['flag'];
        $fid = $v['fid'];
        $fileContent = File::get_https_img($sUrl, 'https://www.nihaojewelry.com/');
        $cacheUrl = CACHE_PATH . \fast\Str::getRandChar(8).'jpg';
        file_put_contents($cacheUrl, $fileContent);
        require_once(ROOT_PATH .'vendor/ali_oss/autoload.php');
        require_once(ROOT_PATH .'vendor/ali_oss/samples/Common.php');
        $bucketName = \Common::getBucketName();
        if(strstr($nurl, '.com/')) {
            $nurl = explode('.com/', $nurl)[1];
        }
        $object = ltrim($nurl, '/');
        $ossClient = \Common::getOssClient();
        $status = $ossClient->uploadFile($bucketName, $object, $cacheUrl);
//        print_r($status);exit;
        if(!isset($status['info']['http_code']) && $status['info']['http_code'] !== 200) {
            $this->error('发送失败:'. json_encode($status));
        }
        unlink($cacheUrl);
        File::send_post('https://www.wencyjewelry.com/goods/index/receiveFinish', [
            'caijiid' => $caijiid,
            'finish' => $finish,
            'fid' => $fid,
            'url' => $status['info']['url'],
        ]);
        $this->success('图片下载完成:'.$nurl);

    }
    //socket采集商品图片
    public function socketToCaijiGoodsImg(){
        print_r($this->view->fetch());
    }
    //百度地图
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
