<?php
//删除远程图片 此处包含魔术函数 所以以下的系统类都不能再自动调取。
require_once 'samples/Common.php';
$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
$status = ($ossClient->deleteObject($bucket, $fileurl));
//file_put_contents('del_status.txt', print_r($status, true));
