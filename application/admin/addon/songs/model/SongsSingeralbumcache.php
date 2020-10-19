<?php
namespace app\admin\addon\songs\model;

use think\Model;

class SongsSingeralbumcache extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = '';
    protected $updateTime = '';
   //今天是否加载过
    public static function hasGetPage($singeridWY, $page=0) {
        if($info = self::where([
            'wysingerid' => $singeridWY,
            'page' => $page,
        ])->field('albumids,pagenum')->find()) {
            return [
                'albumList' => json_decode($info['albumids'], true),
                'pagenum' => $info['pagenum'],
            ];
        } else {
            return false;
        }
    }

    //更新专辑单页内容
    public static function insertPageCache($singeridWY, $page, $pagenum=0, $albumids=[]) {
        self::insert([
            'wysingerid' => $singeridWY,
            'page' => $page,
            'pagenum' => $pagenum,
            'albumids' => json_encode($albumids),
        ]);
    }
}
