<?php
//删除远程图片 此处包含魔术函数 所以以下的系统类都不能再自动调取。
require_once 'samples/Common.php';
$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();

$fileurl = isset($_GET['url']) ? $_GET['url'] : '';
if(!$fileurl) {
    file_put_contents('text_err.txt', 'no fileurl');
    print_r('no fileurl');
    exit;
}
$fileurl = base64_decode($fileurl);
$status = ($ossClient->deleteObject($bucket, $fileurl));
//file_put_contents('del_status.txt', print_r($status, true));
