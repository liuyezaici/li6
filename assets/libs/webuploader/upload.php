<?php
require_once(dirname(__FILE__)."/../../../config.php");//系统参数配置//页面初始化
/*
Upload  文件上传
Author:rui
Date:2017-9-14
*/
//设置上传目录
$p_number =  isset($_POST['p_number']) ? $_POST['p_number'] : '';
$savePath =  isset($_POST['save_path']) ? $_POST['save_path'] : '';
$pathSafeHash =  isset($_POST['pathSafeHash']) ? $_POST['pathSafeHash'] : '';//路径安全码
$uhash =  isset($_POST['uhash']) ? $_POST['uhash'] : '';//用户身份标识
if(!$savePath) die('no set pathSafeHash');
$mytime = Timer::now();
//flash插件决定 $_FILES['Filedata']的文件名 Filedata
if (isset($_FILES['file'])) {
    $fileData = $_FILES['file'];
} elseif(isset($_FILES['Filedata'])) {
    $fileData = $_FILES['Filedata'];
} elseif (isset($_FILES['fileList'])) {
    $fileData = $_FILES['fileList'];
}
$userClass = new Users($uhash);
$db = mysql::getInstance(); //实例化数据库
$userId = $userClass->userId; //保存当前登录的用户id
if(!$p_number)   die('no p_number');
if(!$userId)   die('no login');
if($pathSafeHash != \Func\Func::makeSafeUploadCode($savePath, $userId) ) {
    die('文件上传目录被手动篡改了:'.$savePath);
}

//获取帖子信息
$shareInfo = DbBase::getRowBy('c_post', 'p_id', "p_number='{$p_number}'");
if(!$shareInfo) {
    file_put_contents('text_err.txt', "$p_number 主题不存在");
    echo('主题不存在');
    exit;
}
$pid = $shareInfo['p_id'];
$savePath = $savePath .'/'. $p_number;
if (!empty($fileData)) {
	//得到上传的临时文件流
	$tempFile = $fileData['tmp_name'];
	$fileSize = $fileData['size'];
    $geshi = file::geshi($fileData['name']);
	//允许的文件后缀
	$fileTypes = array('jpg','jpeg','gif','png');
	//得到文件原名
	$fileName = Str::getRandChar(16). '.'. $geshi;
	$fileParts = pathinfo($fileData['name']);
	$parthUrl = $savePath .'/'. $fileName;
    $saveRoot = root.trim($savePath, "/");
    Func::creatdir($saveRoot);
	if (move_uploaded_file($tempFile, $saveRoot. '/'. $fileName)){
        if(!Ip::isLocal()) {//非本地要上传到第三方远程
            $uploadResponse = file::moveFile($saveRoot. '/'. $fileName, $parthUrl);
            if ($uploadResponse[0] == 'success') {
                $filebackurl_our = $uploadResponse[1];
                $filebackurl = $uploadResponse[2];
                //unlink($targetPath);//删除本地文件
            } else {
                echo $fileName."上传失败！";
            }
        } else {
            $filebackurl = $parthUrl;
        }
        //写入附件
        post::addPostFujian($pid, $userId, $fileName, $filebackurl, $fileSize, $geshi, $mytime); //更新文件索引
    }else{
		echo $fileName."上传失败！";
	}
}
?>