<?php
/**
 *  系统参数设置
 *  功能：系统参数设置
 *  作者：LR  2018.5.13
 */
namespace app\admin\addon\setting\model;

use think\Model;

class setting extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'ctime';
    protected $updateTime = false;

    //value的类型定义
    public static $valTypeWords = 'words'; //单行文字
    public static $valTypeText = 'article';//文章
    public static $valTypePicture = 'pic';//图片url 比如logo
    public static $valTypeJson = 'json';//数组 比如轮播图

    //获取 value的 所有类型
    public static function getValAllTypes() {
        return [
            self::$valTypeWords => '单行文字',
            self::$valTypeText => '文章',
            self::$valTypePicture => '图片url',
            self::$valTypeJson => 'json数组',
        ];
    }
    //获取 value的 所有类型 给前端radio用
    public static function getValAllTypesForRadio() {
        $allStatus = self::getValAllTypes();
        $newData = [];
        foreach ($allStatus as $k =>$v) {
            $newData[] = [
                'value' => $k,
                'text' => $v,
            ];
        }
        return $newData;
    }
    //判断值的类型是否图片
    public static function valTypeIsPic($type_) {
        return $type_ == self::$valTypePicture;
    }

    //获取配置的参数值
    public function getSetting($keyName='') {
        return $this->where('keyname', $keyName)->value('value');
    }
}
