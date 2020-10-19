<?php
namespace app\admin\addon\juzi\model;

use think\Model;
use think\Cookie;

class JuziCaijirule extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = '';

    //句子重复性检测
    public static function hasCaiji($url='', $id=0) {
        if($id) {
            return self::where([
                    'id' => ['<>', $id],
                    'url_reg' => MD5($url)
                ])->count() > 0 ;
        } else {
            return self::where([
                    'url_reg' => MD5($url)
                ])->count() > 0 ;
        }
    }
}
