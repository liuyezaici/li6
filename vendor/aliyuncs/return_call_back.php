<?php
require_once("../../../config.php");//系统参数配置//页面初始化
require_once("samples/Common.php");//oss参数配置

//popen打开此页面，只能用$argv 来取参数 [0]=>当前文件名 [1]...参数值
$optionsStr = isset($argv[1]) ? $argv[1] : '';
$optionsStr = unserialize(base64_decode($optionsStr));

$uid = isset($optionsStr['uid']) ? intval($optionsStr['uid']) : 0;
$f_id = isset($optionsStr['f_id']) ? intval($optionsStr['f_id']) : 0;
$shareNumber = isset($optionsStr['shareNumber']) ? trim($optionsStr['shareNumber']) : '';
$fileUrl = isset($optionsStr['fileUrl']) ? trim($optionsStr['fileUrl']) : '';
if(!$uid) {
    echo "no uid ";
    exit;
}
if(!$shareNumber) {
    echo "no  shareNumber";
    exit;
}
if(!$f_id) {
    echo "no f_id";
    exit;
}

$ossClient = Common::getOssClient();
$bucketName = Common::getBucketName();
//取路径+文件名 保存到本地
$fileObj = explode('.com/', $fileUrl)[1];
$sourceFile = $ossClient->getObject($bucketName, $fileObj);
$tmpSourcePath = "upload_{$uid}_test_20mb.jpg";
$tmpSourceCoverPath = "upload_{$uid}_test_cover.jpg";
$tmpSourcePrevPath = "upload_{$uid}_test_prev.jpg";
file_put_contents(rtrim(root, '/') . '/'. $tmpSourcePath, $sourceFile);//保存到临时大图
$coverFile = file::resizeImage($tmpSourcePath, 100, $tmpSourceCoverPath); //生成临时小图
$prevFile = file::resizeImage($tmpSourcePath, share::$previewImgWidth, $tmpSourcePrevPath);//生成临时预览图
//远程阿里云路径
$coverRemoteurl = share::createShareFileCoverName($shareNumber, $uid, $fileUrl, $f_id); //  /upload/share_files/1712/uid_2/171209193133482567/201712_42778_cover_574831.jpg
$coverRemoteurl = trim($coverRemoteurl, '/');
$previewRemoteUrl = share::createShareFilePreviewName($shareNumber, $uid, $fileUrl, $f_id);
$previewRemoteUrl = trim($previewRemoteUrl, '/');
$coverResponse = $ossClient->uploadFile($bucketName, $coverRemoteurl, rtrim(root, '/') . '/'. $coverFile);
$prevResponse = $ossClient->uploadFile($bucketName, $previewRemoteUrl, rtrim(root, '/') . '/'. $prevFile);
unlink(rtrim(root, '/') . '/' .$tmpSourcePath);
$coverurl = $previewUrl = '';
//返回信息
if(isset($coverResponse['info']['url'])){
    unlink(rtrim(root, '/') . '/' .$coverFile);
    $newUrl = $coverResponse['info']['url'];
    $newUrlRight = explode('.aliyuncs.com', $newUrl)[1];
    $coverurl = $GLOBALS['cfg_aliyun_oss_weburl']. $newUrlRight;
}
//返回信息
if(isset($prevResponse['info']['url'])){
    unlink(rtrim(root, '/') . '/' .$prevFile);
    $newUrl = $prevResponse['info']['url'];
    $newUrlRight = explode('.aliyuncs.com', $newUrl)[1];
    $previewUrl = $GLOBALS['cfg_aliyun_oss_weburl']. $newUrlRight;
}
if($coverurl && $previewUrl) {
    share::editShareFilesInfo($f_id, ['f_cover'=>$coverurl, 'f_preview_url'=>$previewUrl]);
    echo "{$coverurl}";
    echo "{$previewUrl}";
}