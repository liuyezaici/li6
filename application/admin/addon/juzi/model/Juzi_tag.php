<?php
namespace app\admin\addon\juzi\model;

use think\Model;

class Juzi_tag extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    //保存句子的标签索引
    public static function saveJuziTagsIndex($juziId, $tagArray=[]) {
        $tagIdArray = self::__checkTagIdArray($tagArray);
        $tagModel = db('juziTagindexs');
        //先删除旧的索引
        $tagModel->where([
            'juziid' => $juziId,
            'tagid' => ['not in', join(',', $tagIdArray)],
        ])->delete();
        //写入新的索引
        foreach ($tagIdArray as $tmpTagId) {
            if(!$tagModel->where([
                'juziid' => $juziId,
                'tagid' => $tmpTagId,
            ])->find()) {
                $tagModel->insert([
                    'juziid' => $juziId,
                    'tagid' => $tmpTagId,
                ]);
            }
        }
        return $tagIdArray;
    }
    //获取标签的id数组
    protected static function __checkTagIdArray($tagArray=[]) {
        $tagIdArray = [];
        foreach ($tagArray as $tmpTag) {
            $tmpTagId = self::getfieldbytitle($tmpTag, 'id');
            if(!$tmpTagId) {
                $tmpTagId = self::insertGetId([
                    'title' => $tmpTag,
                    'ctime' => time(),
                ]);
            }
            $tagIdArray[] = $tmpTagId;
        }
        return $tagIdArray;
    }


    //句子阅读获取taglist
    public static function getTagList($tagIds='')
    {
        if(!$tagIds) return [];
        return self::field('id,title')->where(['id'=> ['in', $tagIds]])->select();
    }
    //句子阅读获取tagname
    public static function getTagNames($idArray=[], $piece=',')
    {
        if(!$idArray) return '';
        if(!is_array($idArray)) $idArray = explode(',', $idArray);
        $list = self::getTagList(join(',', $idArray));
        $names = [];
        foreach ($list as $v) {
            $names[] = $v['title'];
        }
        return join($piece, $names);
    }
    //搜索tag
    public static function searchIndex($idArray=[], $page=1, $pageSize=10)
    {
        if(!$idArray) return [];
        $tagModel = db('juziTagindexs');
        $subQuery = $tagModel->field('juziid')->where(['tagid'=> ['in', join(',', $idArray)]])->group('juziid')->buildSql();
        return \think\db::table($subQuery.' a')->paginate($pageSize, false,
            [
                'page'=> $page,
            ]
        );
    }
}
