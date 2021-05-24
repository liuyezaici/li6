<?php

namespace app\index\model;

use fast\Date;
use fast\File;
use fast\RDS;
use think\Model;
use fast\Str;
use fast\Aes;
use think\Session;

class Article extends Model
{
    protected  $name = 'txtkey_article';



    //cookies的aes加密钥匙
    protected static $articleBatchCookies = 'userArticleTmpBatch';
    //内容保存临时路径
    protected static function getNrTmpSavePath() {
        return rtrim(ROOT_PATH, '/')  .'/articleFile/tmp/';
    }

    //内容保存路径
    protected static function getNrSavePath() {
        $url =   '/articleFile/'. Date::toY() .'/'. Date::toYM() .'/';
        return [ rtrim(ROOT_PATH, '/') .$url, $url];
    }


    //生成批次 保存
    public static function saveContentBatchId() {
        $batchId = Str::getRandChar(32);
        Session::set(self::$articleBatchCookies, $batchId);
        return $batchId;
    }

    //获取批次
    public static function getContentBatchId() {
        return Session::get(self::$articleBatchCookies);
    }
    //移除批次
    public static function removeContentBatchId() {
        return Session::delete(self::$articleBatchCookies);
    }

    //根据批次 保存内容
    public static function saveNrToTmpFile($batchId, $pieceNr) {
        $path = self::getNrTmpSavePath();
        File::creatdir($path);
        $url = $path . $batchId .'.txt';
        $lastNr = '';
        if(file_exists($url)) {
            $lastNr = file_get_contents($url);
        }
        $newNr = $lastNr . $pieceNr;
        file_put_contents($url, $newNr);
    }
    //获取url的内容
    public static function getContentByUrl($url) {
        $lastNr = '';
        if(file_exists(ROOT_PATH . $url)) {
            $lastNr = file_get_contents(ROOT_PATH . $url);
        }
        return $lastNr;
    }
    //覆盖url的内容
    public static function saveNrToUrl($url, $pieceIndex, $nr='') {
        if(file_exists(ROOT_PATH . $url)) {
            if($pieceIndex==0) {
                file_put_contents(ROOT_PATH.$url, $nr);
            } else {
                $lastNr = file_get_contents(ROOT_PATH . $url);
                $lastNr.=$nr;
                file_put_contents($url, $lastNr);
            }
        }
    }
    //删除url
    public static function delUrl($url) {
        if(file_exists(ROOT_PATH . $url)) {
            unlink(ROOT_PATH . $url);
        }
    }
    //根据批次 转移临时内容
    public static function moveTmpFile($batchId, $articleId) {
        $tmpPath = self::getNrTmpSavePath();
        $tmpUrl = $tmpPath . $batchId .'.txt';
        if(!file_exists($tmpUrl)) {
            throw new \Exception('内容不存在，请重新提交');
        }
        $lastNr = file_get_contents($tmpUrl);
        $newPathArray = self::getNrSavePath();
        $newSavePath = $newPathArray[0];
        $outFileUrl = $newPathArray[1];
        File::creatdir($newSavePath);
        $newUrl = $newSavePath . $articleId .'.txt';
        file_put_contents($newUrl, $lastNr);
        @unlink($tmpUrl);
        return $outFileUrl . $articleId .'.txt';
    }

}
