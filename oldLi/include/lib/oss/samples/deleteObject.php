<?php
require_once __DIR__ . '/Common.php';

/**
 * 删除object
 *
 * @param OssClient $ossClient OSSClient实例
 * @param string $bucket bucket名字
 * @return null
 */
$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();

function deleteObject($ossClient, $bucket, $object='user-dir/024.jpg')
{
    try{
        $ossClient->deleteObject($bucket, $object);
    } catch(OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}
//deleteObject($ossClient, $bucket);