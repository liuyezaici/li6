<?php
/**
 *  邮箱验证码组件
 *  功能：系统参数设置
 *  作者：LR  2018.9.15
 */
namespace app\admin\addon\emailcode\model;

use think\Model;

class emailcode extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'ctime';
    protected $updateTime = false;

    //code的类型定义
    public static $codeTypeChangePwd = '10'; //修改密码

    //获取 value的 所有类型
    public static function getValAllTypes() {
        return [
            self::$codeTypeChangePwd => '修改密码',
        ];
    }
    //获取 type 的 所有类型 给前端radio用
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
    //获取 type 的 类型
    public static function getTypeName($typeid=0) {
        $allStatus = self::getValAllTypes();
        return isset($allStatus[$typeid]) ? $allStatus[$typeid] : '';
    }

    //创建邮箱随机码
    public static function getEmailCode($newEmail='', $typeid=0) {
        if(!$typeid) $typeid = self::$codeTypeChangePwd;
        //得到随机数字id
        function getRamCode($len=32) {
            $radomNum = '';
            for($i = 0; $i< $len; $i++) {
                $radomNum .= mt_rand(0, 9);
            }
            return $radomNum;
        }
        return substr(md5(md5($newEmail. '|'. getRamCode(). '|'. $typeid)),3,10);
    }

    //检测是否生成过邮箱随机码
    public static function checkNoSendEmailCode($newEmail='', $typeid=0) {
        if(!$typeid) $typeid = self::$codeTypeChangePwd;
        if(!$newEmail) return '邮箱呢';
        $emailCodeInfo = self::field('code,status')->where(  [
            "email"=> $newEmail,
            "typeid"=> $typeid
        ])->find();
        if(!$emailCodeInfo) return true;
        $emailStatus = $emailCodeInfo['status'];
        if($emailStatus == 0) return '您已经申请过验证码';
        return true;
    }
    //校验邮箱随机码
    public static function checkEmailCode($newEmail='', $email_code='', $typeid=0) {
        if(!$typeid) $typeid = self::$codeTypeChangePwd;
        if(!$newEmail) return '邮箱呢';
        if(!$email_code) return '邮箱验证码呢';
        $emailCodeInfo = self::field('code,status')->where(  [
            "email"=> $newEmail,
            "typeid"=> $typeid
        ])->find();
        if(!$emailCodeInfo) return '您未申请验证码';
        $emailStatus = $emailCodeInfo['status'];
        $rightCode = $emailCodeInfo['code'];
        if($emailStatus != 0) return '验证码已经失效';
        if($rightCode !== $email_code) return '验证码错误';
        //作废验证码
        self::where(  "email", $newEmail)->update([
            'status' => 1
        ]);
        return true;
    }
}
