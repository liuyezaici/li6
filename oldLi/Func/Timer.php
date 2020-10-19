<?php
//时间通用方法
namespace Func;

class Timer
{
    //获取当前时间的随机数
    public static function getRadom($len = 32)
    {
        return date('YmdHis', time()) . Str::getRam($len);
    }

    //得到当前时间 文本格式
    public static function now($time='')
    {
        if(!$time) $time = time();
        return date('Y-m-d H:i:s', $time);
    }

    //获取当前 年
    public static function getYear($time = NULL)
    {
        if (!$time || $time == NULL) $time = time();
        if (!is_numeric($time)) $time = strtotime($time);
        return date('Y', $time);
    }

    //获取当前 年-月
    public static function getYearMonth($time = NULL)
    {
        if (!$time || $time == NULL) $time = time();
        if (!is_numeric($time)) $time = strtotime($time);
        return date('Y-m', $time);
    }

    //获取今天年-月 日期
    public static function today($time = NULL)
    {
        if (!$time || $time == NULL) $time = time();
        if ($time && strstr($time, " ")) {
            $time = strtotime($time);
        }
        return date('Y-m-d', $time);
    }

    //获取当前小时
    public static function getHouer($time = NULL)
    {
        if (!$time) $time = time();
        if ($time && strstr($time, " ")) {
            $time = strtotime($time);
        }
        return date('Y-m-d H:00:00', $time);
    }

    //获取明天 日期
    public static function tomorrow()
    {
        $oktime = time() + 86400;
        return date('Y-m-d', $oktime);
    }

    // 获取上月
    public static function lastMonth()
    {
        return date("Y-m-d", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
    }

    // 前几天
    public static function lastDay($ntime, $days)
    {
        $oktime = $ntime - $days * 86400;
        return $oktime;
    }

    //未来几天
    public static function addDay($ntime, $days)
    {
        $oktime = $ntime + $days * 86400;
        return $oktime;
    }

    //得到毫微秒级时间
    public static function getMtime()
    {
        $runtime = explode(' ', microtime());
        return $runtime[1] * 1000 + $runtime[0] * 1000;
    }

    //去掉秒数
    public static function noSecond($time)
    {
        return date('Y-m-d H:i', strtotime($time));
    }

    //判断日期格式是否正确
    public static function isDate($str, $format = "Y-m-d")
    {
        $strArr = explode("-", $str);
        if (empty($strArr)) {
            return false;
        }
        $newArr = array();
        foreach ($strArr as $val) {
            if (strlen($val) < 2) {
                $val = "0" . $val;
            }
            $newArr[] = $val;
        }
        $str = implode("-", $newArr);
        $unixTime = strtotime($str);
        $checkDate = date($format, $unixTime);
        if ($checkDate == $str)
            return true;
        else
            return false;
    }

    //判断 日期-时间 格式是否正确
    public static function isDate2($str, $format = "Y-m-d H:i:s")
    {
        $strArr = explode(" ", $str);
        if (empty($strArr)) {
            return false;
        }

        if (!self::isDate($strArr[0])) return false;
        $strArr2 = explode(":", $strArr[1]);
        if (empty($strArr2)) {
            return false;
        }
        if (count($strArr2) == 3) return true;
        else false;
    }

    //计算两日期间隔几小时
    public static function dayDiff($begin_time, $end_time)
    {
        $begin_time = strtotime($begin_time);
        $end_time = strtotime($end_time);
        if ($begin_time < $end_time) {
            $starttime = $begin_time;
            $endtime = $end_time;
        } else {
            $starttime = $end_time;
            $endtime = $begin_time;
        }
        $timediff = $endtime - $starttime;
        $hours = ($timediff / (3600));
        if ($hours > 1) {
            $hours = intval($hours);
        } else {
            $hours = $hours * 100;
            $hours = substr($hours, 0, 2);
            $hours = $hours / 100;
        }
        return $hours;
    }

    //数字 时间戳转 格林威治时间
    public static function GetDateTimeMk($mktime)
    {
        return strftime("%Y-%m-%d %H:%M:%S", $mktime);
    }

    //计算某个时间距离现在多少秒
    public static function getLastSecond($lasttime, $nowtime = NULL)
    {
        $nowtime = is_null($nowtime) ? time() : $nowtime;
        if (strstr($lasttime, " ")) $lasttime = strtotime($lasttime);
        if (strstr($nowtime, " ")) $nowtime = strtotime($nowtime);
        return $nowtime - $lasttime; //距离(秒)
    }

    //计算某个时间过去了多久 (小时间 大时间)
    public static function getLastTime($lasttime, $nowtime = NULL)
    {
        $nowtime = is_null($nowtime) ? time() : $nowtime;
        if (strstr($lasttime, " ")) $lasttime = strtotime($lasttime);
        if (strstr($nowtime, " ")) $nowtime = strtotime($nowtime);
        $l_str = "";
        $l_hour = 0;
        $l_minute = 0;
        $l_day = 0;
        $l_month = 0;
        $l_year = 0;
        $max_time = 24 * 60 * 60 * 30 * 12 * 2; //2年前不计算
        $l_s = $nowtime - $lasttime; //距离(秒)
        // 超过一段时间，则不统计时间距离
        if ($l_s > $max_time) return date("Y-m-d H:i:s", $lasttime);
        //几分钟前
        if ($l_s > 60) {
            $l_minute = intval($l_s / 60);
            $l_sec2 = $l_s - $l_minute * 60;
        } else {
            $l_sec2 = $l_s;
        }
        //几小时前
        if ($l_minute > 60) {
            $l_hour = intval($l_minute / 60);
            $l_minutes2 = $l_minute - $l_hour * 60;
            $l_sec2 = '';
            //$l_minutes2 = '';
        } else {
            $l_minutes2 = $l_minute;
        }
        //几天前
        if ($l_hour >= 24) {
            $l_day = intval($l_hour / 24);
            $l_hour2 = $l_hour - $l_day * 24;
            // $l_hour2 = '';
        } else {
            $l_hour2 = $l_hour;
        }
        //几个月前
        if ($l_day > 30) {
            $l_month = intval($l_day / 30);
            $l_day2 = $l_day - $l_month * 30;
            $l_day2 = '';
        } else {
            $l_day2 = $l_day;
        }
        //几年前
        if ($l_month > 12) {
            $l_year = intval($l_month / 12);
            $l_month2 = $l_month - $l_month * 12;
            $l_month2 = '';
        } else {
            $l_month2 = $l_month;
        }
        return ($l_year ? $l_year . "年" : '') . ($l_month2 ? $l_month2 . "个月" : '') . ($l_day2 ? $l_day2 . "天" : '') . ($l_hour2 ? $l_hour2 . "小时" : '') . ($l_minutes2 ? $l_minutes2 . "分钟" : '') . ($l_sec2 ? $l_sec2 . "秒" : '');
    }


    //计算某个时间过去了多久 (小时间 大时间) 只取最大时间值
    public static function getLastTimeMax($lasttime, $nowtime = NULL)
    {
        $nowtime = is_null($nowtime) ? time() : $nowtime;
        if (strstr($lasttime, " ")) $lasttime = strtotime($lasttime);
        if (strstr($nowtime, " ")) $nowtime = strtotime($nowtime);
        $l_str = "";
        $l_hour = 0;
        $l_minute = 0;
        $l_day = 0;
        $l_month = 0;
        $l_year = 0;
        $max_time = 24 * 60 * 60 * 30 * 12 * 2; //2年前不计算
        $l_s = $nowtime - $lasttime; //距离(秒)
        // 超过一段时间，则不统计时间距离
        if ($l_s > $max_time) return date("Y-m-d H:i:s", $lasttime);
        //几分钟前
        if ($l_s > 60) {
            $l_minute = intval($l_s / 60);
            $l_sec2 = $l_s - $l_minute * 60;
        } else {
            $l_sec2 = $l_s;
        }
        //几小时前
        if ($l_minute > 60) {
            $l_hour = intval($l_minute / 60);
            $l_minutes2 = $l_minute - $l_hour * 60;
            $l_sec2 = '';
            $l_minutes2 = '';
        } else {
            $l_minutes2 = $l_minute;
        }
        //几天前
        if ($l_hour >= 24) {
            $l_day = intval($l_hour / 24);
            $l_hour2 = $l_hour - $l_day * 24;
            $l_hour2 = '';
        } else {
            $l_hour2 = $l_hour;
        }
        //几个月前
        if ($l_day > 30) {
            $l_month = intval($l_day / 30);
            $l_day2 = $l_day - $l_month * 30;
            $l_day2 = '';
        } else {
            $l_day2 = $l_day;
        }
        //几年前
        if ($l_month > 12) {
            $l_year = intval($l_month / 12);
            $l_month2 = $l_month - $l_month * 12;
            $l_month2 = '';
        } else {
            $l_month2 = $l_month;
        }
        return ($l_year ? $l_year . "年" : '') . ($l_month2 ? $l_month2 . "个月" : '') . ($l_day2 ? $l_day2 . "天" : '') . ($l_hour2 ? $l_hour2 . "小时" : '') . ($l_minutes2 ? $l_minutes2 . "分钟" : '') . ($l_sec2 ? $l_sec2 . "秒" : '');
    }

    //计算某段时间是几时几分几秒
    public static function secondToTime($seconds)
    {
        $l_s = $seconds; //距离(秒)
        $l_year = 0;
        $l_month = 0;
        $l_day = 0;
        $l_hour = 0;
        $l_minute = 0;
        //几分钟前
        if ($l_s > 60) {
            $l_minute = intval($l_s / 60);
            $l_sec2 = $l_s - $l_minute * 60;
        } else {
            $l_sec2 = $l_s;
        }
        //几小时前
        if ($l_minute > 60) {
            $l_hour = intval($l_minute / 60);
            $l_minutes2 = $l_minute - $l_hour * 60;
        } else {
            $l_minutes2 = $l_minute;
        }
        //几天前
        if ($l_hour > 24) {
            $l_day = intval($l_hour / 24);
            $l_hour2 = $l_hour - $l_day * 24;
        } else {
            $l_hour2 = $l_hour;
        }
        //几个月前
        if ($l_day > 30) {
            $l_month = intval($l_day / 30);
            $l_day2 = $l_day - $l_month * 30;
        } else {
            $l_day2 = $l_day;
        }
        //几年前
        if ($l_month > 12) {
            $l_year = intval($l_month / 12);
            $l_month2 = $l_month - $l_month * 12;
        } else {
            $l_month2 = $l_month;
        }
        return ($l_year ? $l_year . "年" : '') . ($l_month2 ? $l_month2 . "月" : '') . ($l_day2 ? $l_day2 . "天" : '') . ($l_hour2 ? $l_hour2 . "时" : '') . ($l_minutes2 ? $l_minutes2 . "分" : '') . ($l_sec2 ? $l_sec2 . "秒" : '');
    }

    //判断时间戳是否合法
    public static function isTimeStr($unixTime, $formats = array('Y/m/d H:i:s')) {
        if(!$unixTime) { //无法用strtotime转换，说明日期格式非法
            return false;
        }
        //校验日期合法性，只要满足其中一个格式就可以
        foreach ($formats as $format) {
            $dateStr = date($format, $unixTime);
            $dateStr2 = strtotime($dateStr);
            if($dateStr2 == $unixTime) {
                return true;
            }
        }
        return false;
    }
    //获取页面请求时的时间戳
    public static function getTime(){
        return $_SERVER['REQUEST_TIME'];
    }
}
