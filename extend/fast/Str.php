<?php

namespace fast;

use think\Config;
use fast\File;

/**
 * 通用的字符串处理类
 * @author LR <rui6ye@163.com>
 */
class Str
{
//unicode加密
    public static function unicodeEncode($c) {
        $scill = '';
        $len = strlen($c);
        $a = 0;
        while ($a < $len)
        {
            $ud = 0;
            if (ord($c[$a]) >=0 && ord($c[$a])<=127)
            {
                $ud = ord($c[$a]);
                $a += 1;
            }
            else if (ord($c[$a]) >=192 && ord($c[$a])<=223)
            {
                $ud = (ord($c[$a])-192)*64 + (ord($c[$a+1])-128);
                $a += 2;
            }
            else if (ord($c[$a]) >=224 && ord($c[$a])<=239)
            {
                $ud = (ord($c[$a])-224)*4096 + (ord($c[$a+1])-128)*64 + (ord($c[$a+2])-128);
                $a += 3;
            }
            else if (ord($c[$a]) >=240 && ord($c[$a])<=247)
            {
                $ud = (ord($c[$a])-240)*262144 + (ord($c[$a+1])-128)*4096 + (ord($c[$a+2])-128)*64 + (ord($c[$a+3])-128);
                $a += 4;
            }
            else if (ord($c[$a]) >=248 && ord($c[$a])<=251)
            {
                $ud = (ord($c[$a])-248)*16777216 + (ord($c[$a+1])-128)*262144 + (ord($c[$a+2])-128)*4096 + (ord($c[$a+3])-128)*64 + (ord($c[$a+4])-128);
                $a += 5;
            }
            else if (ord($c[$a]) >=252 && ord($c[$a])<=253)
            {
                $ud = (ord($c[$a])-252)*1073741824 + (ord($c[$a+1])-128)*16777216 + (ord($c[$a+2])-128)*262144 + (ord($c[$a+3])-128)*4096 + (ord($c[$a+4])-128)*64 + (ord($c[$a+5])-128);
                $a += 6;
            }
            else if (ord($c[$a]) >=254 && ord($c[$a])<=255)
            { //error
                $ud = false;
            }
            $scill .= "&#$ud;";
        }
        return $scill;
    }
    //unicode解密
    public static function unicodeDecode($str, $prefix = "&#") {
        $utf = '';
        $str = str_replace($prefix, "", $str);
        $a = explode(";", $str);
        foreach ($a as $dec) {
            if(!$dec) continue;
            if ($dec < 128) {
                $utf .= chr($dec);
            } else if ($dec < 2048) {
                $utf .= chr(192 + (($dec - ($dec % 64)) / 64));
                $utf .= chr(128 + ($dec % 64));
            } else {
                $utf .= chr(224 + (($dec - ($dec % 4096)) / 4096));
                $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
                $utf .= chr(128 + ($dec % 64));
            }
        }
        return $utf;
    }
    //html输出时 逆转<>""'
    public  static  function tohtml($str) {
        $str = str_replace("&#34;", "\"",  $str);
        $str = str_replace("&#39;", "'",  $str);
        $str = str_replace("&#60;", "<",  $str);
        $str = str_replace("&#62;", ">",  $str);
        return $str;
    }
    //去掉所有的html标签
    public  static  function nohtml($str) {
        $str = strip_tags($str);
        return $str;
    }

    //得到随机数字id
    public static function getRam($len=32) {
        $radomNum = '';
        for($i = 0; $i< $len; $i++) {
            $radomNum .= mt_rand(0, 9);
        }
        return $radomNum;
    }
    //得到随机数字id
    public static function getRadomTime($len = 20, $format = 'YmdHis'){
        list($usec, $sec) = explode(" ", microtime());
        $rand = \fast\Random::numeric($len);
        return substr(date($format, $sec).substr($usec, 2, 3).$rand, 0, $len);
    }
    //随机生成字符串 $len随机生成字符串的长度
    public static function getRandChar($len){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
        for($i = 0 ; $i < $len ; $i++ ){
            $str .= $strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }
    //得到字符的MD5值
    public static function getMD5($str,$len=32) {
        return substr(md5($str),0,$len);
    }
    //得到字符的hash_MD5值
    public static function getHashMD5($str, $len=32) {
        $hash = 'LR_forever';
        return substr(md5($str . $hash),0,$len);
    }

    //转gb2312转utf-8编码
    public  static  function gbktoutf8($str) {
        return iconv("gbk", "UTF-8//IGNORE", $str);
    }
    //模仿JS的escape
    public  static  function escape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2') {
        $return = '';
        if (function_exists('mb_get_info')) {
            for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) {
                $str = mb_substr ( $string, $x, 1, $in_encoding );
                if (strlen ( $str ) > 1) { // 多字节字符
                    $return .= '%u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) );
                } else {
                    $return .= '%' . strtoupper ( bin2hex ( $str ) );
                }
            }
        }
        return $return;
    }

// ----------------------------------  字符串处理函数 ---------------------------- //
//保留几位字符串
    public static function substr( $sourcestr, $cutlength )
    {
        $returnstr = "";
        $i = 0;
        $n = 0;
        $str_length = strlen( $sourcestr);
        while ( $n < $cutlength && $i <= $str_length )
        {
            $temp_str = substr( $sourcestr, $i, 1 );
            $ascnum = ord( $temp_str );
            if ( 224 <= $ascnum )
            {
                $returnstr .= substr( $sourcestr, $i, 3 );
                $i += 3;
                ++$n;
            }
            else if ( 192 <= $ascnum )
            {
                $returnstr .= substr( $sourcestr, $i, 2 );
                $i += 2;
                ++$n;
            }
            else if ( 65 <= $ascnum && $ascnum <= 90 )
            {
                $returnstr .= substr( $sourcestr, $i, 1 );
                $i += 1;
                ++$n;
            }
            else
            {
                $returnstr .= substr( $sourcestr, $i, 1 );
                $i += 1;
                $n += 0.5;
            }
        }
        return $returnstr;
    }
    //移除1维数组中的某个元素
    public static function removeArrayItem($key_='', $array_) {
        foreach($array_ as $n=>$v) {
            if($v==$key_) {
                array_splice($array_, $n, 1);
            }
        }
        return $array_;
    }
    //移除1维数组中的某些数组
    public static function removeArrayFromArray($array_old=[], $array_remove=[]) {
        foreach($array_old as $n => $v) {
            if(in_array($v, $array_remove)) {
                unset($array_old[$n]); //不能用 array_splice
            }
        }
        $array_new= [];
        foreach($array_old as $n => $v) {
            $array_new[] = $v;
        }
        return $array_new;
    }
    //切割字符串
    public static function sp_($c1,$c2,$ss){
        if(strstr($ss,$c1)){$datab = explode($c1,$ss);}else{return false;}
        if(strstr($datab[1],$c2)){$datac = explode($c2,$datab[1]);$str = $datac[0];}else{return false;}
        return $str;
    }
    //将对象转换为多层数组
    public static function objectToArray($e){
        $e=(array)$e;
        foreach($e as $k=>$v){
            if( gettype($v)=='resource' ) return;
            if( gettype($v)=='object' || gettype($v)=='array' )
                $e[$k]=(array)self::objectToArray($v);
        }
        return $e;
    }
    //将数组转换成对象
    public static function arrayToObject($e){
        if( gettype($e)!='array' ) return;
        foreach($e as $k=>$v){
            if( gettype($v)=='array' || getType($v)=='object' )
                $e[$k]=(object)self::arrayToObject($v);
        }
        return (object)$e;
    }
    //分割中文和半角字符 用于搜索引擎
    public static function splitword($string, $len=1) {
        $start = 0;
        $strlen = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string,$start,$len,"utf8");
            $string = mb_substr($string, $len, $strlen,"utf8");
            $strlen = mb_strlen($string);
        }
        return $array;
    }
    //分割中文词组 用于搜索引擎
    public static function splitAllWords($string) {
        require_once(root.'/include/lib/scws/pscws4.class.php');
        $pscws = new PSCWS4();
        $pscws->set_dict(root.'/include/lib/scws/scws/dict.utf8.xdb');
        $pscws->set_rule(root.'/include/lib/scws/scws/rules.utf8.ini');
        $pscws->set_ignore(true);
        $pscws->send_text($string);
        $words = $pscws->get_tops(5);
        $tags = array();
        foreach ($words as $val) {
            $tags[] = $val['word'];
        }
        $pscws->close();
        return $tags;
    }

    //冒泡排序 $sort 1升序 2降序
    public static  function sort($array, $sort = 1) {
        $count = count($array);
        if ($count <= 0) return false;
        for($i=0; $i<$count; $i++){
            for($j=$count-1; $j>$i; $j--) {
                if($sort == 1) {
                    //如果后一个元素小于前一个，则调换位置
                    if ($array[$j] < $array[$j-1]){
                        $tmp = $array[$j];
                        $array[$j] = $array[$j-1];
                        $array[$j-1] = $tmp;
                    }
                } else {
                    //如果后一个元素大于前一个，则调换位置
                    if ($array[$j] > $array[$j-1]){
                        $tmp = $array[$j];
                        $array[$j] = $array[$j-1];
                        $array[$j-1] = $tmp;
                    }
                }
            }
        }
        return $array;
    }
    //二维数组排序
    public static function arraySort($arr,$keys,$type='asc'){
        $keysvalue = $new_array = array();
        foreach ($arr as $k=>$v){
            $keysvalue[$k] = $v[$keys];
        }
        if($type == 'asc'){
            asort($keysvalue);
        }else{
            arsort($keysvalue);
        }
        reset($new_array);
        $n = 0;
        foreach ($keysvalue as $k=>$v){
            $new_array[$n] = $arr[$k];
            $n ++;
        }
        return $new_array;
    }
    /*
     * 可以统计中文字符串长度的函数
     * @param $str 要计算长度的字符串
     * @param $type 计算长度类型，0(默认)表示一个中文算一个字符，1表示一个中文算两个字符
     */
    public static function abslength($str)
    {
        if(empty($str)){
            return 0;
        }
        if(function_exists('mb_strlen')){
            return mb_strlen($str,'utf-8');
        }
        else {
            preg_match_all("/./u", $str, $ar);
            return count($ar[0]);
        }
    }
    //删除数组指定的元素
    public static function delStrArray($allStr='', $removeStr='', $split=',') {
        if(!$allStr || $allStr=='') return '';
        if(!$removeStr || $removeStr=='') return '';
        $allStr = trim($allStr);
        $allStr = trim($allStr, $split);
        if(strstr($allStr, $split)) {
            $idArray = explode($split, $allStr);
            $idArray = array_flip($idArray);
            unset($idArray[$removeStr]);
            $idArray = array_flip($idArray);
            $newStr = join($idArray, $split);
            return $newStr;
        } else {
            if(trim($allStr) == trim($removeStr)) {
                return '';
            } else {
                return $allStr;
            }
        }
    }
    //提取字符串中的第一个数字
    public static function findNum($str='') {
        $str=trim($str);
        if(empty($str)){return '';}
        $temp=array('1','2','3','4','5','6','7','8','9','0');
        $result='';
        for($i=0;$i<strlen($str);$i++){
            if(in_array($str[$i],$temp)){
                $result.=$str[$i];
                return $result;
            }
        }
        return 0;
    }

    /* 获取中文的拼音声母 */
    public static function getWordsShengMu($words){
        $fchar = ord($words[0]);
        if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($words[0]);
        $s1 = iconv("UTF-8","gb2312", $words);
        $s2 = iconv("gb2312","UTF-8", $s1);
        if($s2 == $words){$s = $s1;}else{$s = $words;}
        $asc = ord($s[0]) * 256 + ord($s[1]) - 65536;
        if($asc >= -20319 and $asc <= -20284) return "A";
        if($asc >= -20283 and $asc <= -19776) return "B";
        if($asc >= -19775 and $asc <= -19219) return "C";
        if($asc >= -19218 and $asc <= -18711) return "D";
        if($asc >= -18710 and $asc <= -18527) return "E";
        if($asc >= -18526 and $asc <= -18240) return "F";
        if($asc >= -18239 and $asc <= -17923) return "G";
        if($asc >= -17922 and $asc <= -17418) return "I";
        if($asc >= -17417 and $asc <= -16475) return "J";
        if($asc >= -16474 and $asc <= -16213) return "K";
        if($asc >= -16212 and $asc <= -15641) return "L";
        if($asc >= -15640 and $asc <= -15166) return "M";
        if($asc >= -15165 and $asc <= -14923) return "N";
        if($asc >= -14922 and $asc <= -14915) return "O";
        if($asc >= -14914 and $asc <= -14631) return "P";
        if($asc >= -14630 and $asc <= -14150) return "Q";
        if($asc >= -14149 and $asc <= -14091) return "R";
        if($asc >= -14090 and $asc <= -13319) return "S";
        if($asc >= -13318 and $asc <= -12839) return "T";
        if($asc >= -12838 and $asc <= -12557) return "W";
        if($asc >= -12556 and $asc <= -11848) return "X";
        if($asc >= -11847 and $asc <= -11056) return "Y";
        if($asc >= -11055 and $asc <= -10247) return "Z";
        return null;
    }
    /* 中文批量获取声母 */
    public static function getAllShengMu($zh){
        $ret = "";
        $s1 = iconv("UTF-8","gb2312", $zh);
        $s2 = iconv("gb2312","UTF-8", $s1);
        if($s2 == $zh){$zh = $s1;}
        for($i = 0; $i < strlen($zh); $i++){
            $s1 = substr($zh,$i,1);
            $p = ord($s1);
            if($p > 160){
                $s2 = substr($zh,$i++,2);
                $ret .= self::getWordsShengMu($s2);
            }else{
                $ret .= $s1;
            }
        }
        return $ret;
    }

    //获取字符串的首个拼音字母 [特殊字体无法使用]
    public static function getFirstLetter($str){
        $firstchar_ord=ord(strtoupper($str[0]));
        if (($firstchar_ord>=65 and $firstchar_ord<=91)or($firstchar_ord>=48 and $firstchar_ord<=57)) return $str[1];
        $s=iconv("UTF-8","gb2312", $str);
        $asc=ord($s[0])*256+ord($s[1])-65536;
        if($asc>=-20319 and $asc<=-20284)return "A";
        if($asc>=-20283 and $asc<=-19776)return "B";
        if($asc>=-19775 and $asc<=-19219)return "C";
        if($asc>=-19218 and $asc<=-18711)return "D";
        if($asc>=-18710 and $asc<=-18527)return "E";
        if($asc>=-18526 and $asc<=-18240)return "F";
        if($asc>=-18239 and $asc<=-17923)return "G";
        if($asc>=-17922 and $asc<=-17418)return "H";
        if($asc>=-17417 and $asc<=-16475)return "J";
        if($asc>=-16474 and $asc<=-16213)return "K";
        if($asc>=-16212 and $asc<=-15641)return "L";
        if($asc>=-15640 and $asc<=-15166)return "M";
        if($asc>=-15165 and $asc<=-14923)return "N";
        if($asc>=-14922 and $asc<=-14915)return "O";
        if($asc>=-14914 and $asc<=-14631)return "P";
        if($asc>=-14630 and $asc<=-14150)return "Q";
        if($asc>=-14149 and $asc<=-14091)return "R";
        if($asc>=-14090 and $asc<=-13319)return "S";
        if($asc>=-13318 and $asc<=-12839)return "T";
        if($asc>=-12838 and $asc<=-12557)return "W";
        if($asc>=-12556 and $asc<=-11848)return "X";
        if($asc>=-11847 and $asc<=-11056)return "Y";
        if($asc>=-11055 and $asc<=-10247)return "Z";
        return null;
    }
    //获取数组中出现最多的元素
    //如果有重复的字符串，则返回1 和对应的字符
    //如果没有重复的字符串，则返回0 和全部数组
    public static function mostRepeatedValues($array, $length=0) {
        if(empty($array) or !is_array($array)){
            return false;
        }
        //1. 计算数组的重复值
        $array = array_count_values($array);
        //2. 根据重复值 倒排序
        arsort($array);
        //取出出现最多次数的数组
        $maxTimes = 0;
        foreach($array as $key_ => $times_) {
            if(!$maxTimes || $times_ > $maxTimes ) {
                $maxTimes = $times_;
            }
        }
        //只有出现频率超过1次 才挑选出现最多的
        if($maxTimes> 1) {
            //3. 返回前 $length 重复值
            $array = array_slice($array, 0, $length, true);
        }
        return $array;
    }
    //替换中文(微信昵称)中的特殊符号
    public static function replaceSpecialWords($str) {
        $pa = '/[a-zA-Z\x{4e00}-\x{9fa5}]/u';
        preg_match_all($pa, $str, $arr);
        $arr = $arr[0];
        $str = join('', $arr);
        return $str;
    }

    /* 生成自定义菜单 的数据json结构 */
    //$val_tag 数据来源的值的key名字
    //$title_tag 数据来源的标题key名字
    public static function makeSelectData($data, $val_tag='', $title_tag='') {
        if(!$data) return [];
        if(!is_array($data)) {
            print_r($data);
            print_r('不是数组');
        };
        $newData = array();
        foreach ($data as $n => $v) {
            if($val_tag && $title_tag) {
                if(!isset($v[$val_tag])) {
                    print_r($val_tag. 'key undefined'. PHP_EOL);
                    continue;
                }
                $newData[] = array(
                    'value' => $v[$val_tag],
                    'title' => $v[$title_tag]
                );
            } elseif($val_tag==1) {//一维数组 下标0 并且输出值直接是中文
                $newData[] = array(
                    'value' => $v,
                    'title' => $v
                );
            } else {//一维数组 下标0
                $newData[] = array(
                    'value' => $n,
                    'title' => $v
                );
            }
        }
        return $newData;
    }

    /* 生成自定义菜单option  */
    //$data 数据选择数组
    //$selected 已选择
    public static function makeSelectOption($data,$selected ='') {
        $res = '';
        foreach ($data as $n => $v) {
            $res .= '<option value="'.$v['value'].'">'.$v['title'].'</option>';
        }
        if( $selected ){
            $res = str_replace('<option value="'. $selected .'">', '<option value="'. $selected .'" selected>', $res);
        }
        return $res;
    }

    /* 生成属性菜单 的数据json结构 */
    //$val_tag 数据来源的值的key名字
    //$title_tag 数据来源的标题key名字
    //$group_tag 数据来源的分组名字
    public static function makeTreesData($data, $val_tag='id', $title_tag='title', $group_tag='letter', $canNull=false) {
        //获取品牌菜单
        $pinpaiJsonData = array();
        foreach($data as $n => $v) {
            $pinpaiJsonData[strtoupper((isset($v[$group_tag]) ? $v[$group_tag] : '-'))][] = array(
                'value' => $v[$val_tag],
                'title' => $v[$title_tag]
            );
        }
        //允许为空 则加个不限的选项
        if($canNull) {
            $pinpaiJsonData[][] = array(
                'value' => 0,
                'title' => '不限'
            );
        }
        ksort($pinpaiJsonData);
        return $pinpaiJsonData;
    }
    //创建国家省份地区的权限表格
    public static function makeCountryProvinceBox($powerData,  $countryField='l_power_country', $provinceField='l_power_province', $cityField='l_power_city', $idField='l_id') {
        $areaClass = new \fast\Area;
        $newCountryData = [];
        $pushDataToNew = function ($newCountryData, $newData_) use($idField) {
            if($newCountryData) {
                foreach($newCountryData as $n=>$v) {
                    if($v[$idField] == $newData_[$idField]) {
                        return $newCountryData;
                    }
                }
                $newCountryData[] = $newData_; //反之累加
            } else {
                $newCountryData[] = $newData_; //默认为第一个
            }
            return $newCountryData;
        };
        foreach($powerData as $n => $v) {
            if(isset($v[$cityField]) && $v[$cityField]) {
                $newCountryData = $pushDataToNew($newCountryData, [$idField=> $v[$countryField] , 'l_title'=> $areaClass::getCountryName($v[$countryField]) , 'parent'=> 0]);
                $newCountryData = $pushDataToNew($newCountryData, [$idField=> $v[$provinceField], 'l_title'=> $areaClass::getAreaName($v[$provinceField]), 'parent'=> $v[$countryField] ]);
                $newCountryData = $pushDataToNew($newCountryData, [$idField=> $v[$cityField], 'l_title'=> $areaClass::getAreaName($v[$cityField]), 'parent'=> $v[$provinceField] ]);
            } else if(isset($v[$provinceField]) && $v[$provinceField]) {
                $newCountryData = $pushDataToNew($newCountryData, [$idField=> $v[$countryField], 'l_title'=> $areaClass::getCountryName($v[$countryField]), 'parent'=> 0 ]);
                $newCountryData = $pushDataToNew($newCountryData, [$idField=> $v[$provinceField], 'l_title'=> $areaClass::getAreaName($v[$provinceField]), 'parent'=> $v[$countryField]]);
            } else if(isset($v[$countryField]) && $v[$countryField]) {
                $newCountryData = $pushDataToNew($newCountryData, [$idField=> $v[$countryField], 'l_title'=> $areaClass::getCountryName($v[$countryField]), 'parent'=> 0]);
            }
        }
        //$newCountryData 输出格式
        /*
         * Array(
            [0] => Array (
                    [l_id] => CN
                    [l_title] => 中国
                    [parent] => 0
                ),...
            )*/
        $newCountryData = str::diguiArray($newCountryData, 0, 'sons', 'parent', $idField);
        //$newCountryData 输出格式
        /*
         * Array(
            [0] => Array (
                    [l_id] => CN
                    [l_title] => 中国
                    [parent] => 0
                    [sons] => Array (
                        [l_id] => 310000
                        [l_title] => 上海
                        [parent] => CN
                        [sons] => 0
                    )
                ),...
            )*/
        //
        return $newCountryData;
    }
    //根据所有字段，生成空值数组
    public static function makeNullDatas($fields='') {
        $array_ = explode(',', $fields);
        $newData = [];
        foreach ($array_ as $v) {
            $newData[$v] = '';
        }
        return $newData;
    }

    //合并递归的从属数组
    //$level 当前递归到第几层
    public static function diguiArray($array, $pid=0, $sonName = 'sons', $parentName = 'parent_id', $idName = 's_id'){
        if(!$array) return [];
        $newList = array();
        //生成所有父级数据
        $allParentData = []; //所有父级数据
        foreach($array as $k=>$v){
            $allParentData[$v[$parentName]][] = $v;
        }
//        print_r('$array');
//        print_r($array);
//        print_r('$allParentData');
//        print_r($allParentData);
//        exit;
        //获取子数据
        $getSonData = function ($parentData=[]) use($sonName, $parentName, $idName, $allParentData, &$getSonData) {
            $tmpSonList = array();
            foreach($parentData as $k=>$v){
                $pid_ = $v[$idName];
//              print_r($parentDataGlobal[$pid_]);exit;
                if(isset($allParentData[$pid_])) {
                    $v[$sonName] = $getSonData($allParentData[$pid_]);
                }
                $tmpSonList[]=$v;
            }
            return $tmpSonList;
        };
        if(!isset($allParentData[$pid])) {
//            print_r($allParentData);exit;
            return [];
        }
        //只遍历所有父级
        foreach($allParentData[$pid] as $k=>$v){
            $pid_ = $v[$idName];
//            print_r($parentDataGlobal[$pid_]);exit;
            if(isset($allParentData[$pid_])) {
                $v[$sonName] = $getSonData($allParentData[$pid_]);
            }
            $newList[]=$v;
        }
        return $newList;
    }
    //递归json数据转表格
    //$btns ='<input type="button" class="btn" onClick="addType(\'{tid}\');" value="添加"> <input type="button" class="btn" onClick="editType(\'{tid}\');" value="编辑">';
    //$tableHtml = str::makeJsonTable($array_, ['物品分类', '子分类','子细分类','子细细分类'], ['tid','title','son'], 'type_table', $btns);
    //data例子
    //$data = [['tid'=>'1','title'=>'aaa','son'=> [
    //$itemText 子数据排序类型: number 显示序号 12.;  input显示输入框 ;radio显示复选框 none不显示
    public static function makeJsonTable($jsonData, $topFields=['物品分类', '子分类','子细分类','子细细分类'],
                                         $dataFields=['t_id','t_title','son'], $tableClassName='lr_json_table', $btns='', $rootBtns='', $itemText='', $selectIdArray=[]) {
        $fieldSon = $dataFields[2];
        //生成头部tr的html
        $trHtml = '';
        foreach($topFields as $tr_) {
            $trHtml .= '<td>'. $tr_ .'</td>'. chr(10);
        }
        $trHtml = '<tr class="tr_">'. $trHtml .'</tr>'. chr(10);

        //统计所有子数据的数量
        function jsonTable_countSon($sonData, $fieldSon) {
            $thisItemLen = 0;
            foreach($sonData as $sonN =>$sonV) {
                if(isset($sonV[$fieldSon]) && $sonV[$fieldSon]) {
                    $sonInfo = jsonTable_countSon($sonV[$fieldSon], $fieldSon);
                    $sonData[$sonN][$fieldSon] = $sonInfo[0];
                    $sonData[$sonN]['son_len'] = $sonInfo[1];
                } else {
                    $sonData[$sonN]['son_len'] = 1;
                }
                $thisItemLen += $sonData[$sonN]['son_len'];
            }
            return [$sonData, $thisItemLen];
        }
        //遍历所有子数据的数量
        foreach($jsonData as $n =>$parentV) {
            if(isset($parentV[$fieldSon]) && $parentV[$fieldSon]) {
                $sonInfo = jsonTable_countSon($parentV[$fieldSon], $fieldSon);
                $jsonData[$n][$fieldSon] = $sonInfo[0];
                $jsonData[$n]['son_len'] = $sonInfo[1];
            } else {
                $jsonData[$n]['son_len'] = 0;
            }
        }
        $rootIndex = 0;
        //生成子数据的TD
        function makeSonTdHtml($sonData, $dataFields=['t_id','t_title','son'], $btns='', $rootBtns='', $rootIndex=0, $itemText='', $selectIdArray=[]) {
            $fieldId = $dataFields[0];
            $fieldTitle = $dataFields[1];
            $fieldSon = $dataFields[2];
            $sonHtml = '';
            if($sonData && is_array($sonData)) {
                foreach($sonData as $sonN=>$sonV) {
                    $sonRowspan = $sonV['son_len'] > 1 ? ' rowspan="'. $sonV['son_len'] .'"':'';
                    $tr_ = '';
                    if($sonN >0) {
                        $tr_ = '<tr>';
                    }
                    $sonSonHtml = '';
                    if(isset($sonV[$fieldSon])) {
                        $sonSonHtml = makeSonTdHtml($sonV[$fieldSon], $dataFields, $btns, $rootBtns, ($rootIndex+1), $itemText, $selectIdArray);
                    }
                    $btnsHtml = '';
                    if($btns) {
                        $btnsHtml = preg_replace("/\{([^}]*)\}/",  $sonV[$fieldId] , $btns);
                    }
                    $rootBtnsHtml = '';
                    if($rootBtns && $rootIndex == 0) {
                        $rootBtnsHtml = preg_replace("/\{([^}]*)\}/",  $sonV[$fieldId] , $rootBtns);
                    }
                    $dataTitle = $sonV[$fieldTitle];
                    if($itemText == 'number') {
                        $newItemText = $sonV[$fieldId] .'.'. $dataTitle;
                    } elseif($itemText == 'text') {
                        $newItemText = "<label><input type='text' value='{$sonV[$fieldId] }' /> {$dataTitle} </label>";
                    } elseif($itemText == 'radio') {
                        $checked = '';
                        if(in_array($sonV[$fieldId], $selectIdArray)) $checked = ' checked';
                        $newItemText = "<label><input type='checkbox' value='{$sonV[$fieldId] }' {$checked} /> {$dataTitle} </label>";
                    } else {//自定义文本
                        $newItemText = str::pregReplace($itemText, "/\{([^}]*)\}/",  $sonV);
                    }
                    $sonHtml .= $tr_ .'<td valign="top" '. $sonRowspan .'>'. $newItemText . $btnsHtml . $rootBtnsHtml .'</td>'. chr(10). $sonSonHtml;
                }
            }
            return $sonHtml;
        }
        //生成表格
        $tableHtml =  makeSonTdHtml($jsonData, $dataFields, $btns, $rootBtns, $rootIndex, $itemText, $selectIdArray);
        $tableHtml = '<table class="'. $tableClassName .'" border="0" cellpadding="0" cellspacing="0">'. $trHtml.$tableHtml .'</table>';
        return $tableHtml;
    }
    //所有二维数据转一维数据 返回 ['a','b','c'...]
    public static function twoArrayToOneArray($array1=[]) {
        $newExistSku = [];
        foreach ($array1 as $index=> $tmpVal) {
            if(is_array($tmpVal)) {
                $newExistSku = array_merge($newExistSku, $tmpVal);
            } else {
                $newExistSku = array_push($newExistSku, $tmpVal);
            }
        }
        return $newExistSku;
    }
    public static function getLetter($str){
        $str= iconv("UTF-8","gb2312", $str);//如果程序是gbk的，此行就要注释掉
        if (preg_match("/^[\x7f-\xff]/", $str)) {
            $fchar=ord($str[0]);
            if($fchar>=ord("A") and $fchar<=ord("z") )return strtoupper($str[0]);
            $a = $str;
            $val=ord($a[0])*256+ord($a[1])-65536;
            if($val>=-20319 and $val<=-20284)return "A";
            if($val>=-20283 and $val<=-19776)return "B";
            if($val>=-19775 and $val<=-19219)return "C";
            if($val>=-19218 and $val<=-18711)return "D";
            if($val>=-18710 and $val<=-18527)return "E";
            if($val>=-18526 and $val<=-18240)return "F";
            if($val>=-18239 and $val<=-17923)return "G";
            if($val>=-17922 and $val<=-17418)return "H";
            if($val>=-17417 and $val<=-16475)return "J";
            if($val>=-16474 and $val<=-16213)return "K";
            if($val>=-16212 and $val<=-15641)return "L";
            if($val>=-15640 and $val<=-15166)return "M";
            if($val>=-15165 and $val<=-14923)return "N";
            if($val>=-14922 and $val<=-14915)return "O";
            if($val>=-14914 and $val<=-14631)return "P";
            if($val>=-14630 and $val<=-14150)return "Q";
            if($val>=-14149 and $val<=-14091)return "R";
            if($val>=-14090 and $val<=-13319)return "S";
            if($val>=-13318 and $val<=-12839)return "T";
            if($val>=-12838 and $val<=-12557)return "W";
            if($val>=-12556 and $val<=-11848)return "X";
            if($val>=-11847 and $val<=-11056)return "Y";
            if($val>=-11055 and $val<=-10247)return "Z";
        } else {
            return '';
        }
    }
    //json u526f 转中文 副
    public static function unicodeToZh($sourceStr='') {
        if(!strstr($sourceStr, 'u')) return $sourceStr;
        $sourceStr = preg_replace('/u([^u\s]{3,4})/', '\\u\1', $sourceStr);
        $json = '{"str":"'.$sourceStr.'"}';
        $arr = json_decode($json,true);
        if(empty($arr)) return $sourceStr;
        return $arr['str'];
    }

    /*
    功能：补位函数
    str:原字符串
    type：类型，0为后补，1为前补
    len：新字符串长度
    msg：填补字符
    */
    public static function dispRepair($str,$len,$msg,$type='1') {
        $length = $len - strlen($str);
        if($length<1)return $str;
        if ($type == 1) {
            $str = str_repeat($msg,$length).$str;
        } else {
            $str .= str_repeat($msg,$length);
        }
        return $str;
    }

    //过滤特殊符号
    public static function replaceSpecialChar($strParam){
        $regex = "/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|/";
        return preg_replace($regex,"",$strParam);
    }
    //获取字符串前几位首字母 不够位数则用随机数补齐
    //$str 转换字符串 $strlen从第一位开始多少个字符取首字母 $len随机字符长度
    public static function getLetterByStr($str = '',$strlen=0,$otherlen=0){
        $str = str::replaceSpecialChar(trim($str));
        $res = '';
        if( $str && mb_strlen($str) >= $strlen ){
            for( $i=0 ; $i<$strlen ; $i++ ){
                $tempStr = mb_substr($str,$i,1,'utf-8');
                if(preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $tempStr) > 0){
                    $res .= str::getLetter($tempStr);
                }else{
                    $res.= $tempStr;
                }
            }
        }else{
            $res = str::getRandChar($strlen);
        }
        if( $otherlen > 0 ){
            $res .=  str::getRandChar($otherlen);
        }
        return strtoupper($res);
    }

    //提取二维数组的值为key（注意key的唯一性，一般使用唯一id）
    public static function replaceKey(Array $arr,$key = 'id'){
        $_arr = [];
        foreach($arr as $k => $v)
        {
            $_arr[$v[$key]] = $v;
        }
        return $_arr;
    }
    //替换左右两边的字符串 因为trim('a_b', 'and') 会去掉 a,n,d开头的字符 所以要自定义一个纯去掉and的函数
    public static function trimStr($mainstr='', $removeStr='') {
        if(!$mainstr) return $mainstr;
        $mainstr = trim($mainstr);
        $removeStr = strtolower($removeStr);
        $leftStr = substr($mainstr, 0, strlen($removeStr));
        $leftStr = strtolower($leftStr);//不区分大小写
        $rightStr = substr($mainstr, -strlen($removeStr));
        $rightStr = strtolower($rightStr);//不区分大小写
        if($leftStr == $removeStr) $mainstr = substr($mainstr, strlen($removeStr)); //移除左边
        if($rightStr == $removeStr ) $mainstr = substr($mainstr, 0, strlen($mainstr) - strlen($removeStr) );//移除右边
        return $mainstr;
    }
    //检测数字组是否正确 1,2,3
    public static function checkIds($ids='') {
        if(!strstr($ids, ',')) return true;
        $idArray = explode(',', $ids);
        foreach ($idArray as $tmpId) {
            if(!is_numeric($tmpId)) return false;
        }
        return true;
    }

    //创建性别option的下拉内容
    public static function makeSexOption($sex=0) {
        $sexHtml = '<option value="1">男</option><option value="0">女</option><option value="2">不详</option>';
        return str_replace('<option value="'. $sex .'">', '<option value="'. $sex .'" selected >', $sexHtml);
    }
    //创建性别下拉菜单内容

    //创建性别数组
    public static function makeSexArray() {
        $sexArray = [
            [ 'value'=> 1,  'title'=> '男'],
            [ 'value'=> 0,  'title'=> '女'],
            [ 'value'=> 2,  'title'=> '不详'],
        ];
        return $sexArray;
    }
    //转换行符
    public static function toBr($str='', $spilt='<br/>') {
        if(!$str) return $str;
        $str = str_replace(PHP_EOL, $spilt, $str);
        $str = str_replace(CHR(13), $spilt, $str);
        return $str;
    }
    //校验数字的字符串格式 12:444|22:566
    public static function isNum_Num($str='', $split1=':', $split2='|') {
        $strArray = explode($split2, $str);
        $newArray = [];
        foreach ($strArray as $tmpStr) {
            if(!$tmpStr) continue;
            if(preg_match("/^([0-9]{1,7}){$split1}([0-9,]*?)$/i", $tmpStr)) {
                $newArray[] = $tmpStr;
            }
        }
        return join($split2, $newArray);
    }
    //正则替换字符串
    public static function pregReplace($strSource="", $reg="",  $vals=[]) {
        $n= preg_match_all($reg,  $strSource, $maches);
        if($n > 0) {
            //$maches Array([0] => Array([0] => {a}[1] => {b})  [1] => Array([0] => a [1] => b))
            foreach($maches[1] as $math){
                if(isset($vals[$math])){
                    $strSource = str_replace('{'.$math.'}', $vals[$math], $strSource);
                } else {
                    $strSource = str_replace('{'.$math.'}', '', $strSource);
                }
            }
        }
        return $strSource;
    }

    //将内容中的图片加延时加载
    public static function lazyLoadImg($content='', $maxWidth=0) {
        $maxWidthHtml = '';
        if($maxWidth) $maxWidthHtml = " onload='if(this.width>{$maxWidth}) this.width={$maxWidth}'";
        return preg_replace("/<img([^\"]+)src=\"([^\"]+)\"([^\>]+)>/is", "<a href='\\2' class='lightBox'><img class='lazy' data-original=\"\\2\" {$maxWidthHtml} /></a>", $content);
    }
    //过滤内容中的图片 上传到远程
    public static function filterImages($s_content, $userId=0, $savePath='', $s_id=0, $flag='article') {
        $mytime = timer::now();
        $s_content = str::tohtml($s_content);
        $s_content = strip_tags($s_content, '<br><a><p><img><pre>');
        $pattern='/<img[^>]*[\s]src=[\'|"]?([^>\'"\s]*)[\'|"]?[^>]*>/i';
        preg_match_all($pattern, $s_content, $contentFileImageMatch);
        if(isset($contentFileImageMatch[1])) {
            foreach ($contentFileImageMatch[1] as $tmpUrl) {
                //引用链接要截取，如百度图片的url
                if(strstr($tmpUrl, 'src=http')) {
                    $realUrl = 'http'.explode('src=http', $tmpUrl)[1];
                } else {
                    $realUrl = $tmpUrl;
                }
                //外网转内网
                if(!strstr($realUrl, 'li6.cc')) {
                    $left5 = strtolower(substr($realUrl, 0, 5));
                    if($left5 == 'https') {//下载https的图片
                        $tmpImages = File::get_https($realUrl);
                        $geshi = File::getGeshi($realUrl);
                        $fileName = File::fileName($realUrl);
                    } else if($left5 == 'data:') {//截图
                        $tmpImages=base64_decode(substr($realUrl,strpos($realUrl,'base64,')+7));
                        $geshi = 'png';
                        $fileName = str::getRam(15) .'.'. $geshi;
                    } else if(strtolower(substr($realUrl, 0, 1)) != 'h') {//本地图片
                        continue;
                    }  else if(strtolower(substr($realUrl, 0, 7)) == 'http://') { //普通 http图片
                        $domain = strtolower(substr($realUrl, 7));
                        $domain = explode('/', $domain)[0];
                        if($domain == $_SERVER['SERVER_NAME']) continue; //本地域名 http://sasasui.com/的图片
                        $tmpImages = File::get_nr($realUrl);
                        $geshi = File::getGeshi($realUrl);
                        $fileName = File::fileName($realUrl);
                    }
                    $localUrl = ROOT_PATH. 'upload/'. $userId .'tmp_.'.$geshi;
                    File::creatdir(dirname($localUrl));
                    file_put_contents($localUrl, $tmpImages);
                    $newUrl = $savePath."/{$fileName}";
                    $result = File::uploadToHttpOss($localUrl, $newUrl);
                    if($result['id']=='0364') {
                        unlink($localUrl);
                        $newHttpUrl = $result['msg'];
                        //取出文件名 无格式后缀
                        $fileNameRightGeshiArray = explode('.', $fileName);
                        array_pop($fileNameRightGeshiArray);
                        $fileNameRight = join($fileNameRightGeshiArray, '.');
                        if($flag == 'article') {
//                            article::addArticleFujian($s_id, $userId, $fileNameRight, $newUrl, strlen($tmpImages), $geshi, $mytime); //更新文件索引
                        }
                        $s_content = str_replace($tmpUrl, $newHttpUrl, $s_content);
                    } else {
                        return $result;
                    }
                }
            }
        }
        $s_content = str_replace("\"", "&#34;", $s_content);
        $s_content = str_replace("'", "&#39;", $s_content);
        $s_content = str_replace("<", "&#60;", $s_content);
        $s_content = str_replace(">", "&#62;", $s_content);
        return $s_content;
    }
    //判断是不是邮箱格式
    public static function isEmail($u_email){
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if (!preg_match($pattern, $u_email)) {
            return  false;
        } else {
            return true;
        }
        // return  preg_match("/^[\\w\\-\\.]+@[\\w\\-\\.]+(\\.\\w+)+$/", $n);
    }
    //检测手机号
    public static function checkPhone($phoneStr) {
        $exp = "/^13[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$|15[0123456789]{1}[0-9]{8}$|17[0123456789]{1}[0-9]{8}$|18[0123456789]{1}[0-9]{8}$/";
        return preg_match($exp, $phoneStr);
    }
    // 座机验证
    public static function checkZuoji($phone){
        $isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
        return preg_match($isTel, $phone);
    }



    //保留内容中的代码
    public static function keepCode($s_content) {
        $pattern='/<pre[^\>\<]*>([^\>\<]*)<\/pre>/i';
        preg_match_all($pattern, $s_content, $contentFileImageMatch);
        if(isset($contentFileImageMatch[1])) {
            foreach ($contentFileImageMatch[1] as &$tmpCode) {
                $s_content = str_replace($tmpCode, self::unicodeEncode($tmpCode), $s_content);
            }
        }
        return $s_content;
    }
    //取随机数
    public static function makeRadomNum($fromNum=0, $maxNum=10) {
        $radomArray = [];
        for($i=0;$i<200; $i++) {
            $radomArray[] = mt_rand($fromNum, $maxNum);
        }
        $radomArray=array_count_values($radomArray);
        arsort($radomArray);//倒序
        $radomArray = array_flip($radomArray);
        return reset($radomArray);
    }

    //从数组中取中间的数
    public static function getMiddleNumbers($ay=[], $getNum=3, $getTotalNum=10 ) {
        $totalNum = count($ay);
        $halfNum = bcdiv($totalNum, 2);
        $getHalfNum = bcdiv($getTotalNum, 2);
        if($halfNum < $getNum) $halfNum = $getNum;
        if($getNum + 5 > $totalNum) {//剩余位数和要取的数相差不到5个时 直接现取
            $middleArray = $ay;
        } else {
            $middleArray =  array_slice($ay, -($halfNum+$getHalfNum), $halfNum);
        }
        print_r($middleArray);
        $radomIndexArray = [];//随机的下标
        $backNumberArray = [];
        function getRadomNum($maxNum, &$radomIndexArray) {
            $newIndex = mt_rand(0, $maxNum-1);
            if(in_array($newIndex, $radomIndexArray)
                || (count($radomIndexArray)-3> $maxNum && in_array($newIndex-1, $radomIndexArray)) //剩余位置还有3个时，如有连号要重新生成
                || (count($radomIndexArray)-3> $maxNum && in_array($newIndex+1, $radomIndexArray)) //剩余位置还有3个时，如有连号要重新生成
            ) return getRadomNum($maxNum, $radomIndexArray);
            array_push($radomIndexArray, $newIndex);
            echo $newIndex.'|';
            return $newIndex;
        }
        for($i=0;$i<$getNum; $i++) {
            $radomIndex = getRadomNum(count($middleArray), $radomIndexArray);
            $backNumberArray[] = $middleArray[$radomIndex];
        }
        sort($backNumberArray);
        return $backNumberArray;
    }
    //分割字符串文字
    public static function splitStr($str){
        return preg_split('/(?<!^)(?!$)/u', $str );
    }

    //根据数量分页
    public static function makeNumPage($url, $currentPage=1, $totalPage=1, $pageShowNum=5, $activerCss='current') {
        $fromPage = $currentPage - intval($pageShowNum/2);
        if($fromPage<1)  {
            $fromPage = 1;
            $toPage = $fromPage + $pageShowNum;
        } else {
            $toPage = $fromPage + $pageShowNum ;
        }
        if($fromPage < 1) $fromPage = 1;
        if($toPage>$totalPage) $toPage = $totalPage;
        if($toPage == $totalPage) $toPage = $totalPage+1; //到达尾部 直接显示全部页码
        if($fromPage == $toPage) {
            $fromPage = 1;
        }
        if($currentPage > $toPage) {
            $fromPage = $currentPage-1;
            $toPage = $toPage + $pageShowNum;
            if($fromPage < 1) $fromPage = 1;
            if($toPage > $totalPage) $toPage = $totalPage;
        }
        $repeatNum = 0;
        $li = '<ul class="pagination">';
        $prePage = $currentPage-1;
        if($prePage >0) {
            $url_ = str_replace('{page}', ($prePage), $url);
            $li .= "<li><a href=\"{$url_}\" target=\"_self\"><span>&laquo;</span></a></li>";
        }
        for($i = $fromPage; $i < $toPage; $i++) {
            $url_ = str_replace('{page}', $i, $url);
            if($repeatNum >= $pageShowNum) break;//break;
            $tmpActiverCss = '';
            if($currentPage == $i) $tmpActiverCss = " class='{$activerCss}'";
            $li .= "<li{$tmpActiverCss}><a href=\"{$url_}\" target=\"_self\"><span>{$i}</span></a></li>";
            $repeatNum ++;
        }
        $nextPage = $currentPage+1;
        if($nextPage < $totalPage) {
            $url_ = str_replace('{page}', $nextPage, $url);
            $li .= "<li><a href=\"{$url_}\" target=\"_self\"><span>&raquo;</span></a></li>";
        }
        $li .= '</ul>';
        return $li;
    }


    //上传文件目录加安全校验
    public static function makeSafeUploadCode($pathUrl, $uid_) {
        return self::getMD5($pathUrl."[save_hash_lr]".$uid_);
    }
}
