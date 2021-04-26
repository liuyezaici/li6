<?php

namespace app\tool\controller;

use app\common\controller\Frontend;
use fast\File;
use fast\Str;
use think\Config;
use think\Db;
use fast\Addon;
use app\admin\library\Auth;
use app\common\model\Users;
use app\admin\addon\usercenter\model\Third;
use OSS\OssClient;
class Img extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
    }

    public function downBase64() {
        $url = input('post.url', '', 'trim');
        if(!$url) {
            $this->error('empty url');
        }
        if(!preg_match('/^https?\:\/\//i', $url, $match)) {
            $this->error('网址必须加上http(s)://前缀:');
        }
        function base64EncodeImage ($image_file) {
            $base64_image = '';
            $image_info = getimagesize($image_file);
            $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
            $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
            return $base64_image;
        }
        
        $getIcon = function($url) {
            $array_ = explode('//', $url);
            $http = $array_[0];
            $domainStr = $array_[1];
            $array_ = explode('/', $domainStr);
            $domain = $array_[0];
            $html = File::get_https_img($url, $url);
            return $html;
        };
        $res =  $getIcon($url);
        $tmpPath = CACHE_PATH .'/tmp'. time() .'_'. Str::getRam(6) .'.jpg';
        file_put_contents($tmpPath, $res);
        $res =  base64EncodeImage($tmpPath);
        @unlink($tmpPath);
        $this->success('success', '' , ['url' => $res]);
    }
}
