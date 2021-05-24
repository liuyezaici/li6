<?php

namespace fast;

use app\admin\model\User as UserModel;
/**
 * RSA签名类
 */
class User
{


    //操作用户邮箱缓存
    public static function operateUserEmail($operate='set/get/', $uid=0, $newVal='') {
        $cacheNameRDS = 'oss.userEmail';
        $cacheNameRdsFields = "{$uid}";
        //数据库中用户昵称
        $__getDb = function($uid)
        {
            return UserModel::getFieldByid($uid, 'email');
        };
        if($operate =='set') {
            UserModel::where($uid, $uid)->update(['email' => $newVal]);
            RDS::hset($cacheNameRDS, $cacheNameRdsFields, $newVal);
            return true;
        } elseif($operate =='get') {
            if(!RDS::hexists($cacheNameRDS, $cacheNameRdsFields)) {
                $newName = $__getDb($uid);
                if(!$newName) $newName = '';
                RDS::hset($cacheNameRDS, $cacheNameRdsFields, $newName);
                return $newName;
            } else {
                return (string) RDS::hget($cacheNameRDS, $cacheNameRdsFields);
            }
        }
    }

    //操作Admin邮箱缓存
    public static function operateAdminEmail($operate='set/get/', $uid=0, $newVal='') {
        $cacheNameRDS = 'oss.adminEmail';
        $cacheNameRdsFields = "{$uid}";
        //数据库中用户昵称
        $__getDb = function($uid)
        {
            return UserModel::getFieldByid($uid, 'email');
        };
        if($operate =='set') {
            UserModel::where($uid, $uid)->update(['email' => $newVal]);
            RDS::hset($cacheNameRDS, $cacheNameRdsFields, $newVal);
            return true;
        } elseif($operate =='get') {
            if(!RDS::hexists($cacheNameRDS, $cacheNameRdsFields)) {
                $newName = $__getDb($uid);
                if(!$newName) $newName = '';
                RDS::hset($cacheNameRDS, $cacheNameRdsFields, $newName);
                return $newName;
            } else {
                return (string) RDS::hget($cacheNameRDS, $cacheNameRdsFields);
            }
        }
    }
}
