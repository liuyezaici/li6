<?php
namespace app\admin\addon\juzi\model;

use think\Model;

class JuziCaijigushirule extends Model
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


    //外部插入年份
    public static function addYear($title='', $uid=0) {
        $hasId = Db('gushiyear')->getfieldbytitle($title, 'id');
        if($hasId) return $hasId;
        return Db('gushiyear')->insertGetId([
            'title' => $title,
            'ctime' => time(),
            'cuid' => $uid
        ]);
    }

    //获取年份名
    public static function getYearTitle($id=0) {
        return Db('gushiyear')->getfieldbyid($id, 'title');
    }
}
