<?php
namespace app\admin\addon\setting\model;

use think\Model;

class SettingType extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    //获取分类名字
    public static function getTitles($id=0, $piece=',') {
        if(!$id) return '';
        $parentTypes = [$id];
        $getParentType = function ($id_=0) use(&$parentTypes, &$getParentType) {
            $pid = self::getfieldbyid($id_, 'pid');
            if($pid) {
                $parentTypes[] = $pid;
                $getParentType($pid);
            }
        };
        $getParentType($id);
        array_reverse($parentTypes);//反向排序 让父级在最左边
        $parentTypeIds = join(',', $parentTypes);
        $typeTitleArray = self::field('group_concat(title separator \''. $piece .'\') as result_')->where(['id'=> ['in', $parentTypeIds]])->select();
        $typeTitleArray = $typeTitleArray[0];
        return $typeTitleArray['result_'];
    }

}
