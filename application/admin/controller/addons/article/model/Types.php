<?php

namespace app\admin\controller\addons\article\model;

use fast\File;
use fast\Str;
use think\Model;
use think\Session;

class Types extends Model
{

    protected $name = 'articleTypes';


    //获取用户(钥匙)的所有分类
    public static function getUserTypes($uid=0, $pid=0) {
        return self::field('id,title')->where([
            'uid'=> $uid,
            'pid' => $pid
        ])->select();
    }
    //获取用户(钥匙)的根分类
    public static function getUserRootTypes($uid=0) {
        $where_ = [
            'uid'=> $uid,
            'pid' => 0
        ];
        return self::field('id,title')->where($where_)->select();
    }
    //搜索分类
    public static function searchUserRootTypes($uid=0, $like='') {
        return self::field('id,title')->where([
            'uid'=> $uid,
            'title' => ['like', "%{$like}%"]
        ])->select();
    }
    //搜索分类
    public static function getTypeTitle($id1=0, $join=',') {
        $title = [];
        if($id1) {
            $title[] = self::getfieldbyid($id1, 'title');
        }
        return join($join, $title);
    }
    //分类名字 是否已经存在
    public static function hasTitle($uid, $title, $id=0) {
        $where = [
            'uid'=> $uid,
            'title' => $title,
        ];
        if($id) {
            $where['id'] = ['<>', $id];
        }
        return self::where($where)->count()>0;
    }
    //添加分类
    public static function addType($uid, $title, $pid) {
        return self::insert([
            'uid'=> $uid,
            'title' => $title,
            'pid' => $pid,
        ]);
    }
    //修改分类
    public static function editType($id, $title, $pid) {
        return self::where('id', $id)->update([
            'title' => $title,
            'pid' => $pid,
        ]);
    }

    //删除分类
    public static function remove($uid, $id) {
        //删除子分类
        self::where([
            'uid'=> $uid,
            'pid' => $id,
        ])->delete();
        return self::where([
            'uid'=> $uid,
            'id' => $id,
        ])->delete();
    }

    //当前分类是否包含子分类
    public static function hasSon($id) {
        return  self::where([
                'pid' => $id,
            ])->count() >0 ;
    }

}
