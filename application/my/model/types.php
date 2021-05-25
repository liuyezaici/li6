<?php

namespace app\my\model;

use think\Model;

class types extends Model
{

    protected $name = 'article_types';


    //获取用户(钥匙)的所有分类
    public static function getUserTypes($uid=0) {
        return self::field('id,title')->where([
            'cuid'=> $uid,
        ])->select();
    }
    //获取用户(钥匙)的根分类
    public static function getUserRootTypes() {
        return self::field('id,title')->select();
    }
    //搜索分类
    public static function searchUserRootTypes($like='') {
        if($like) {
            $where_ = [
                'title' => ['like', "%{$like}%"]
            ];
        } else {
            $where_ = [];
        }
        return self::field('id,title')->where($where_)->select();
    }
    //搜索分类
    public static function getTypeTitle($id1=0, $id2=0, $join=',') {
        $title = [];
        if($id1) {
            $title[] = self::getfieldbyid($id1, 'title');
        }
        if($id2) {
            $title[] = self::getfieldbyid($id2, 'title');
        }
        return join($join, $title);
    }
    //分类名字 是否已经存在
    public static function hasTitle($title, $id=0) {
        $where = [
            'title' => $title,
        ];
        if($id) {
            $where['id'] = ['<>', $id];
        }
        return self::where($where)->count()>0;
    }
    //添加分类
    public static function addType($title) {
        return self::insert([
            'title' => $title,
        ]);
    }
    //修改分类
    public static function editType($id, $title) {
        return self::where('id', $id)->update([
            'title' => $title,
        ]);
    }



}
