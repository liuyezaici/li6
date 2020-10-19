<?php
namespace app\admin\addon\log\model;

use think\Model;

class Log extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'ctime';

    public static function addlog($text='') {
        if(is_array($text)) $text = json_encode($text, true);
        $data = [
            'ctime' => time(),
            'text' => $text
        ];
        self::insert($data);
    }
}
