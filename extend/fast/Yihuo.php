<?php

namespace fast;

/**
 * 通用的php异或操作字符串
 * @author LR <rui6ye@163.com> 2019.1.5
 */
class Yihuo
{

    //求异或 '0b' ^ '01' ^ 'a0' ^ '00' 的结果
//    $str1 = self::to2jj($s1);
//    $str2 = self::to2jj($s2);
//    $str3 = self::yh2jz($str1, $str2);
    //$ab16 = base_convert($str3, 2, 16);

    //得到二进制格式的字符串
    public static function to2jj($s1) {
        $str1 = '';
        for($i = 0; $i<strlen($s1); $i ++) {
            $tmpLetter = substr($s1, $i, 1);
            $s01 = base_convert($tmpLetter, 16, 2);
            $str1 .= self::full0($s01, 4);
        }
        return $str1;
    }
    //得到异或结果 二进制格式
    public static function  strYh2jj($s1, $s2) {
        $str1 = self::to2jj($s1);
        $str2 = self::to2jj($s2);
        return self::yh2jz($str1, $str2);
    }
    //异或二进制格式的数据 000010101010101 ^ 101010101001010
    public static function yh2jz($sA, $sB) {
        $sNew = '';
        for($i = 0; $i< strlen($sA); $i++) {
            $sNew .= intval(substr($sA, $i, 1)) ^ intval(substr($sB, $i, 1)) ;
        }
        return $sNew;
    }
    //补齐N位0
    public static function full0($ss, $num=2) {
        if(strlen($ss) < $num) $ss = str_repeat('0', $num-strlen($ss)) . $ss;
        return $ss;
    }

    //十进制转16进制
    public static function tenTo16($str) {
        return strtoupper(base_convert($str, 10, 16));
    }

    //二进制转16进制
    public static function twoTo16($str) {
        return strtoupper(base_convert($str, 2, 16));
    }

    //异或柜子指令的校验位
    public static function yihuoCupboardJiaoyanwei($boxStr, $tezhengma, $dataStr) {
        if(is_numeric($boxStr)) $boxStr = $boxStr.'';
        $cupStr = self::full0(self::tenTo16($boxStr), 2);
        $a2jj = self::to2jj($cupStr);
        $b2jj = self::to2jj($tezhengma);
        $end2jj = self::yh2jz($a2jj, $b2jj);
        if(strlen($dataStr) == 4) {
            $dataStr1 = substr($dataStr, 0, 2);
            $dataStr2 = substr($dataStr, 2);
            $c2jj1 = self::to2jj($dataStr1);
            $c2jj2 = self::to2jj($dataStr2);
            $end2jj = self::yh2jz($end2jj, $c2jj1);
            $end2jj = self::yh2jz($end2jj, $c2jj2);
        } else {
            $c2jj = self::to2jj($dataStr);
            $end2jj = self::yh2jz($end2jj, $c2jj);
        }
        return self::twoTo16($end2jj);
    }
}
