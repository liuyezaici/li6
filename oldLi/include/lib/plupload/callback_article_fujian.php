<?php
require_once("../../../config.php");//系统参数配置//页面初始化

$mytime = Timer::now();

$fileUrl = isset($_POST['filename']) ? trim($_POST['filename']) : '-';//第三方在网盘中的绝对路径url /aaa/aaa/aa.jpg
$filesize = isset($_POST['size']) ? trim($_POST['size']) : '';
$uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
$aid = isset($_POST['aid']) ? trim($_POST['aid']) : '';
$uhash = isset($_POST['uhash']) ? trim($_POST['uhash']) : '';
if(!Users::checkUserSafeStr($uhash)) {
    file_put_contents('text_u_login_err.txt', "$uhash 不合法");
    echo("$uhash 不合法");
    exit;
}
$array_ = explode("|", $uhash);
$uid_ = isset($array_[0]) ? $array_[0] : 0;
if($uid_ != $uid) {
    file_put_contents('text_u_login_err.txt', "$uid 不一致");
    echo("$uid 不一致");
    exit;
}
$db = mysql::getInstance();
//写入文件
//获取分享信息
$articleInfo = DbBase::getRowBy('s_articles', 'a_id', "a_id={$aid}");
if(!$articleInfo) {
    file_put_contents('text_err.txt', "$aid 文章不存在");
    echo('文章不存在');
    exit;
}


//取出文件名 无格式后缀
$fileNameRightArray = explode('/', $fileUrl);
$fileNameRightGeshi = end($fileNameRightArray);
$fileNameRightGeshiArray = explode('.', $fileNameRightGeshi);
array_pop($fileNameRightGeshiArray);
$fileNameRight = join($fileNameRightGeshiArray, '.');
if(strstr($fileNameRight, '_lr_rl_r_is_rad_')) {//去掉真实文件名字中的随机码,以便前端展示用
    $arr_ = explode('_lr_rl_r_is_rad_', $fileNameRight);
    $fileNameRight = $arr_[0];
}
//取出格式
$geshi = Str::geshi($fileUrl);
article::addArticleFujian($aid, $uid, $fileNameRight, $fileUrl, $filesize, $geshi, $mytime); //更新文件索引

header("Content-Type: application/json");
$data = array("Status"=>"Ok");
echo json_encode($data);