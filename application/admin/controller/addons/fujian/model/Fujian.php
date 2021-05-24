<?php

namespace app\admin\controller\addons\fujian\model;

use fast\Img;
use think\Config;
use think\Model;
use think\Session;

class Fujian extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'ctime';
    protected $updateTime = 'update_time';


    //更新附件的sid
    public function updateSid($sid=0, $urlArray=[]) {
        if(!is_array($urlArray)) $urlArray = [];
        foreach ($urlArray as $url_) {
            if(strpos($url_, '?')) {
                $url_ = explode('?', $url_)[0];
            }
            $urlHash_ = MD5($url_);
            $fujianInfo = $this->get(['filename_hash' => $urlHash_]);
            if($fujianInfo) {
                $fujianInfo['addon_sourceid'] = $sid;
                $fujianInfo->where([
                    'filename_hash' => $urlHash_
                ])->update(['addon_sourceid' => $sid]);
            }
        }
    }
    //保存文件
    public static function saveFile($uid, $fileInfo, $pubPath, $addonName, $addonSourceId, $fromTmp=true, $newName='', $isImg=true) {
        if(!$fileInfo) return 'getInfo获取不到文件信息,请重试.';
        $type = $fileInfo['type'];
        $fileName = $fileInfo['name'];
        $fileTmpName = isset($fileInfo['tmp_name']) ? $fileInfo['tmp_name'] : '';
        $fileSize = $fileInfo['size'];
        if($fileName == 'blob') {
            $array_ = explode('/', $type);
            $suffix = end($array_);
        } else {
            $suffix = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $suffix = $suffix ? $suffix : 'jpg';
        }
        $upload = Config::get('upload');
        $mimetypeArr = explode(',', $upload['mimetype']);
        $typeArr = explode('/', $type);

        //验证文件后缀
        if ($upload['mimetype'] !== '*' && !in_array($suffix, $mimetypeArr) && !in_array($type, $mimetypeArr) && !in_array($typeArr[0] . '/*', $mimetypeArr))
        {
            return __('Uploaded file format is limited');
        }
        $uploadDir =  '/uploads/'. $pubPath . '/'.date("Y-m-d") .'/'. $addonSourceId.'/';
        \fast\File::creatdir(dirname(ROOT_PATH . $uploadDir.$fileName));
        $radom_ = \fast\Str::getRadomTime(12). \fast\Str::getRandChar(8);
        $newFileName = ($newName ? $newName : $radom_). '.'. $suffix;
        $miniName = ($newName ? $newName.'_min' : $radom_). '.'. $suffix;
        $newUrl = ROOT_PATH . $uploadDir . $newFileName;
        if($fromTmp) {
            move_uploaded_file($fileTmpName, $newUrl);
        } else {
            rename($fileTmpName, $newUrl);
        }
        if(!file_exists($newUrl)) {
            return(($fromTmp?1:0) . '文件转移失败'. $newUrl);
        }
        $imageWidth = $imageHeight = 0;
        if($isImg) {
            //压缩小图
            new Img($newUrl,120, 120, ROOT_PATH. $uploadDir.$miniName);
            if (in_array($suffix, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf']))
            {
                $imgInfo = getimagesize($newUrl);
                $imageWidth = isset($imgInfo[0]) ? $imgInfo[0] : $imageWidth;
                $imageHeight = isset($imgInfo[1]) ? $imgInfo[1] : $imageHeight;
            }
            $fileUrlMiniLocal = $uploadDir . $miniName;
        } else {
            $fileUrlMiniLocal = '';
        }
        
        $fileUrlLocal = $uploadDir . $newFileName;
        //计算最新排序
        $attachmentClass= new Fujian();
        $newOrder = $attachmentClass->where([
            'addon_name'  => $addonName,
            'addon_sourceid'  => $addonSourceId,
        ])->order('orderby', 'asc')->value('orderby');
        if(!$newOrder) {
            $newOrder = 1000000;
        } else {
            $newOrder -= 1000;
        }
        $params = array(
            'title'    => $newFileName,
            'filesize'    => $fileSize,
            'imagewidth'  => $imageWidth,
            'imageheight' => $imageHeight,
            'geshi'   => $suffix,
            'mimetype'    => $type,
            'fileurl_local'         => $fileUrlLocal,
            'fileurl_local_min'         => $fileUrlMiniLocal,
            'filename_hash'         => MD5($fileUrlLocal),
            'ctime'  => time(),
            'cuid'  => $uid,
            'addon_name'  => $addonName,
            'addon_sourceid'  => $addonSourceId,
            'orderby'        => $newOrder,
        );
        $attachmentClass->data(array_filter($params));
        $attachmentClass->save();
        if($addonSourceId) {
            //更新原数据的附件索引
            $addonClass = Addon::getModel($addonName);
            if(method_exists($addonClass, 'refreshPics')) {
                $addonClass->refreshPics($addonSourceId);
            }
        }
        $fileData = [
            'url' => $fileUrlLocal,
            'fileId' => $attachmentClass->id,
        ] ;
        return $fileData;
    }
    //删除文件
    public static function removeFileImg($ids) {
        if(!$ids) return true;
        $idArray = explode(',', $ids);
        foreach ($idArray as $id_) {
            $result = self::field('addon_name,addon_sourceid,fileurl_local,fileurl_local_min')->where('id', $id_)->find();
            if(!$result) return true;
            $fileurl_local = $result['fileurl_local'];
            $fileurl_local_min = $result['fileurl_local_min'];
            if($fileurl_local && file_exists(ROOT_PATH . $fileurl_local)) unlink(ROOT_PATH . $fileurl_local);
            if($fileurl_local_min && file_exists(ROOT_PATH . $fileurl_local_min)) unlink(ROOT_PATH . $fileurl_local_min);
            self::destroy($id_);
        }
    }
}
