<?php
require_once("../../../config.php");//配置文件
@session_start();
$userCookiesName =  $_SESSION[$GLOBALS['cfg_login_cookiesname']]; //会员已经登录过会生成的 cookies
$us = new users();
if(!$userCookiesName) {
    $us->exitUser();
    print_r('/resource/system/images/no_login.png');
    exit;
}
$cache = new cache();
$userCookiesInfo = $cache->get($userCookiesName);
$uid = isset($userCookiesInfo['userID']) ? $userCookiesInfo['userID'] : '';
if(!$uid) {
    $us->exitUser();
    print_r('/resource/system/images/no_login.png');
    exit;
}

function alert($msg) {
    header('Content-type: text/html; charset=UTF-8');
    print_r($msg);
    exit;
}
/*
 * saveremoteimg demo for php
 * @requires xhEditor
 *
 * @author Yanis.Wang<yanis.wang@gmail.com>
 * @site http://xheditor.com/
 * @licence LGPL(http://www.opensource.org/licenses/lgpl-license.php)
 *
 * @Version: 0.9.1 (build 110703)
 *
 * 注：本程序仅为演示用，只实现了最简单的远程抓图及粘贴上传，如果要完善此功能，还需要自行开发以下功能：
 *		1，非图片扩展名的URL地址抓取
 *		2，大体积的图片转jpg格式，以及加水印等后续操作
 *		3，上传记录存入数据库以管理用户上传图片
 *
 * option explicit
 */
header('Content-Type: text/html; charset=UTF-8');
$attachDir='/upload';//上传文件保存路径，结尾不要带/
$dirType=1;//1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
$maxAttachSize=2097152;//最大上传大小，默认是2M
$upExt="jpg,jpeg,gif,png";//上传扩展名
ini_set('date.timezone','Asia/Shanghai');//时区

function saveRemoteImg($sUrl){
    global $upExt,$maxAttachSize,$attachDir;
    $reExt='('.str_replace(',','|',$upExt).')';
    if(substr($sUrl,0,10)=='data:image'){//base64
        if(!preg_match('/^data:image\/'.$reExt.'/i',$sUrl,$sExt))return false;
        $sExt=$sExt[1];
        $imgContent=base64_decode(substr($sUrl,strpos($sUrl,'base64,')+7));
    }
    else{//url图片
        if(!preg_match('/\.'.$reExt.'$/i',$sUrl,$sExt))return false;
        $sExt=$sExt[1];
        $imgContent=getUrl($sUrl);
    }
    if(strlen($imgContent)>$maxAttachSize)return false;//
    $localArray = getLocalPath($sExt);
    $sLocalFile = $localArray[0];
    $fileName = $localArray[1];
    file_put_contents($sLocalFile, $imgContent);
    $fileinfo= @getimagesize($sLocalFile);
    if(!$fileinfo||!preg_match("/image\/".$reExt."/i",$fileinfo['mime'])){
        @unlink($sLocalFile);
        return false;
    }
    $serverUrl = $sLocalFile;
    $serverUrl = str_replace($_SERVER["DOCUMENT_ROOT"],'',$serverUrl);
    $serverUrl = str_replace('upload/' ,'upload/'. date('Ymd') .'/', $serverUrl);
    $file_url = file::upFileTOImgSite($sLocalFile,substr($serverUrl,1));
    return $file_url;
}
function getUrl($sUrl,$jumpNums=0){
    $arrUrl = parse_url(trim($sUrl));
    if(!$arrUrl)return false;
    $host=$arrUrl['host'];
    $port=isset($arrUrl['port'])?$arrUrl['port']:80;
    $path=$arrUrl['path'].(isset($arrUrl['query'])?"?".$arrUrl['query']:"");
    $fp = @fsockopen($host,$port,$errno, $errstr, 30);
    if(!$fp)return false;
    $output="GET $path HTTP/1.0\r\nHost: $host\r\nReferer: $sUrl\r\nConnection: close\r\n\r\n";
    stream_set_timeout($fp, 60);
    @fputs($fp,$output);
    $Content='';
    while(!feof($fp))
    {
        $buffer = fgets($fp, 4096);
        $info = stream_get_meta_data($fp);
        if($info['timed_out'])return false;
        $Content.=$buffer;
    }
    @fclose($fp);
    global $jumpCount;
    if(preg_match("/^HTTP\/\d.\d (301|302)/is",$Content)&&$jumpNums<5)
    {
        if(preg_match("/Location:(.*?)\r\n/is",$Content,$murl))return getUrl($murl[1],$jumpNums+1);
    }
    if(!preg_match("/^HTTP\/\d.\d 200/is", $Content))return false;
    $Content=explode("\r\n\r\n",$Content,2);
    $Content=$Content[1];
    if($Content)return $Content;
    else return false;
}
function getLocalPath($sExt){
    global $dirType,$attachDir,$uid;
    switch($dirType)
    {
        case 1: $attachSubDir = 'day_'.date('ymd'); break;
        case 2: $attachSubDir = 'month_'.date('ym'); break;
        case 3: $attachSubDir = 'ext_'.$sExt; break;
    }
    $newAttachDir = $_SERVER["DOCUMENT_ROOT"].$attachDir;
    if(!is_dir($newAttachDir))
    {
        @mkdir($newAttachDir, 0777);
        @fclose(fopen($newAttachDir.'/index.htm', 'w'));
    }
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    $newFilename =$uid."_".date("YmdHis").mt_rand(1000,9999).'.'.$sExt;
    $targetPath = $newAttachDir.'/'.$newFilename;
    return array($targetPath,$newFilename);
}

$arrUrls=explode('|',$_POST['urls']);
$urlCount=count($arrUrls);
for($i=0;$i<$urlCount;$i++){
    $localUrl=saveRemoteImg($arrUrls[$i]);
    if($localUrl)$arrUrls[$i]=$localUrl;
}
echo implode('|',$arrUrls);
