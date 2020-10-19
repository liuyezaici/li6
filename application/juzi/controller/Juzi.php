<?php

namespace app\juzi\controller;

use app\common\controller\Frontend;
use app\common\model\Users;
use think\Config;
use think\Request;
use fast\Addon;
use fast\Date;
use app\admin\addon\juzi\model\Juzi as juziModel;
use app\admin\addon\juzi\model\Juzi_author as juziAuthorModel;
use app\admin\addon\songs\model\Songs as songsModel;
use app\admin\addon\songs\model\SongsUser as songsUserModel;

class Juzi extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected static $webTitle = '';
    protected static $webdesc = '';
    protected static $webLogo = '';
    protected static $tongjiCode = '';
    protected static $footContent = '';

    public function _initialize()
    {
        parent::_initialize();
        //实例化配置组件
        $settingModel = Addon::getModel('setting');
        if(!$settingModel) {
            self::$webTitle = '未安装setting组件';
            self::$webdesc = '未安装setting组件';
            self::$webLogo = Config::get('default_img');
            self::$tongjiCode = '';
            self::$footContent = '';
        } else {
            self::$webTitle = $settingModel->getSetting('web_title');//站点名字设置
            self::$webdesc = $settingModel->getSetting('web_desc');//站点描述
            self::$webLogo = $settingModel->getSetting('web_logo');//站点logo
            self::$tongjiCode = $settingModel->getSetting('tongji_code');//统计代码
            self::$footContent = $settingModel->getSetting('foot_content');//页脚内容
        }
        $this->view->assign('webLogo', self::$webLogo);//站点名
        $this->view->assign('front_header', $this->view->fetch('common/header'));
        $this->view->assign('webTitle', self::$webTitle);//站点名
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('tongjiCode', self::$tongjiCode);//统计代码
        $this->view->assign('footContent', self::$footContent);//页脚内容
    }


    public static function get_https_img($url, $ref='',$coo=''){
        $header = array("Referer: ".$ref."","Cookie: ".$coo);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_HEADER, 0);//get cookies
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//
        //----
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        //$contents = curl_exec($ch);
        ob_start();
        curl_exec($ch);
        $contents = ob_get_contents();
        ob_end_clean();
        curl_close($ch);
        return $contents;
    }
    //本地采集评论
    public function caijiComment() {
        $id = input('id' ,0, 'intval');
        $page = input('page' ,1, 'intval');
        $songuri = input('songuri' ,0, 'trim');
        if(!$id) {
            return 'noid';
        }
        $res =  songsModel::caijiComments($id, $page, $songuri);
        echo $res;
        exit;
    }

    //本地采集歌词
    public function caijiImg($url=NULL) {
        if(!$url) {
            return 'nourl';
        }
        $url = base64_decode($url);
        $url = urldecode($url);
//        echo $url ;exit;
        $res =  self::get_https_img($url, 'https://www.nihaojewelry.com');
        header('Content-type:image/jpeg');
        echo $res;
        exit;
    }

    //本地采集歌词
    public function caijiGeci() {
        $id = input('id' ,0, 'intval');
        if(!$id) {
            return 'noid';
        }
        $res =  songsModel::caijiGeci($id);
        echo $res;
        exit;
    }



    public function index()
    {
        $totalJuzi = juziModel::count();
        $lastJuzi= juziModel::getLastJuzi();
        foreach ($lastJuzi as &$v) {
           $v['authorLink'] = $v['author']>0 ? "/juzi/author/id/{$v['author']}/1" : "#";
           $v['authorName'] = $v['author'] ? juziAuthorModel::getfieldbyid($v['author'], 'title') : Users::getfieldbyid($v['cuid'], 'nickname');
           $v['createtime'] = \fast\Date::toYMDS($v['createtime']);
        }
        unset($v);
        $this->view->assign('totalJuzi', $totalJuzi);
        $this->view->assign('lastJuzi', $lastJuzi);
        print_r($this->view->fetch());
    }

}
