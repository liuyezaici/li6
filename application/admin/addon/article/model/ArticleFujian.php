<?php

namespace app\admin\addon\article\model;

use think\Model;

class ArticleFujian extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';


    public static function getPostFile($id, $field='*') {
        return self::where('id', $id)->field($field)->find();
    }

    public static function deleteFile($id) {
        return self::where('id', $id)->delete();
    }



    //获取靠前的附件信息
    public static function getPostFileLeft($postId='', $orderId=0, $fields='*') {
        return self::field($fields)->where([
            'sid'=> $postId,
            'order'=> ['<', $orderId],
            'status'=>0
        ])->order('order', 'DESC')->find()->limit(1);
    }
    //获取靠前的附件信息
    public static function getPostFileRight($postId='', $orderId=0, $fields='*') {
        return self::field($fields)->where([
            'sid'=> $postId,
            'order'=> ['>', $orderId],
            'status'=>0
        ])->order('order', 'DESC')->find()->limit(1);
    }
    //获取文件
    public static function getFileById($id, $fields='*') {
        return self::field($fields)->where('id', $id)->find();
    }

    //修改附件信息
    public static function editFile($fid=0, $editData=[]) {
        if(!$fid) return false;
        return self::where('id', $fid)->update($editData);
    }
}
