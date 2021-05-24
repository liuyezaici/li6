<?php

namespace fast;

/**
 * 日期时间处理类
 */
class Date
{

    const YEAR = 31536000;
    const MONTH = 2592000;
    const WEEK = 604800;
    const DAY = 86400;
    const HOUR = 3600;
    const MINUTE = 60;

    /**
     * 计算两个时区间相差的时长,单位为秒
     *
     * $seconds = self::offset('America/Chicago', 'GMT');
     *
     * [!!] A list of time zones that PHP supports can be found at
     * <http://php.net/timezones>.
     *
     * @param string $remote timezone that to find the offset of
     * @param string $local timezone used as the baseline
     * @param mixed $now UNIX timestamp or date string
     * @return  integer
     */
    public static function offset($remote, $local = NULL, $now = NULL)
    {
        if ($local === NULL) {
            // Use the default timezone
            $local = date_default_timezone_get();
        }
        if (is_int($now)) {
            // Convert the timestamp into a string
            $now = date(DateTime::RFC2822, $now);
        }
        // Create timezone objects
        $zone_remote = new DateTimeZone($remote);
        $zone_local = new DateTimeZone($local);
        // Create date objects from timezones
        $time_remote = new DateTime($now, $zone_remote);
        $time_local = new DateTime($now, $zone_local);
        // Find the offset
        $offset = $zone_remote->getOffset($time_remote) - $zone_local->getOffset($time_local);
        return $offset;
    }

    //判断时间格式是否正确
    public static function isDate($date = '')
    {
        if (strtotime($date) == strtotime(date('Y-m-d H:i:s', strtotime($date)))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 计算两个时间戳之间相差的时间
     *
     * $span = self::span(60, 182, 'minutes,seconds'); // array('minutes' => 2, 'seconds' => 2)
     * $span = self::span(60, 182, 'minutes'); // 2
     *
     * @param int $remote timestamp to find the span of
     * @param int $local timestamp to use as the baseline
     * @param string $output formatting string
     * @return  string   when only a single output is requested
     * @return  array    associative list of all outputs requested
     */
    public static function span($remote, $local = NULL, $output = 'years,months,weeks,days,hours,minutes,seconds')
    {
        // Normalize output
        $output = trim(strtolower((string)$output));
        if (!$output) {
            // Invalid output
            return FALSE;
        }
        // Array with the output formats
        $output = preg_split('/[^a-z]+/', $output);
        // Convert the list of outputs to an associative array
        $output = array_combine($output, array_fill(0, count($output), 0));
        // Make the output values into keys
        extract(array_flip($output), EXTR_SKIP);
        if ($local === NULL) {
            // Calculate the span from the current time
            $local = time();
        }
        // Calculate timespan (seconds)
        $timespan = abs($remote - $local);
        if (isset($output['years'])) {
            $timespan -= self::YEAR * ($output['years'] = (int)floor($timespan / self::YEAR));
        }
        if (isset($output['months'])) {
            $timespan -= self::MONTH * ($output['months'] = (int)floor($timespan / self::MONTH));
        }
        if (isset($output['weeks'])) {
            $timespan -= self::WEEK * ($output['weeks'] = (int)floor($timespan / self::WEEK));
        }
        if (isset($output['days'])) {
            $timespan -= self::DAY * ($output['days'] = (int)floor($timespan / self::DAY));
        }
        if (isset($output['hours'])) {
            $timespan -= self::HOUR * ($output['hours'] = (int)floor($timespan / self::HOUR));
        }
        if (isset($output['minutes'])) {
            $timespan -= self::MINUTE * ($output['minutes'] = (int)floor($timespan / self::MINUTE));
        }
        // Seconds ago, 1
        if (isset($output['seconds'])) {
            $output['seconds'] = $timespan;
        }
        if (count($output) === 1) {
            // Only a single output was requested, return it
            return array_pop($output);
        }
        // Return array
        return $output;
    }

    /**
     * 格式化 UNIX 时间戳为人易读的字符串
     *
     * @param int    Unix 时间戳
     * @param mixed $local 本地时间
     *
     * @return    string    格式化的日期字符串
     */
    public static function human($remote, $local = null)
    {
        $timediff = (is_null($local) || $local ? time() : $local) - $remote;
        $chunks = array(
            array(60 * 60 * 24 * 365, 'year'),
            array(60 * 60 * 24 * 30, 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24, 'day'),
            array(60 * 60, 'hour'),
            array(60, 'minute'),
            array(1, 'second')
        );

        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            if (($count = floor($timediff / $seconds)) != 0) {
                break;
            }
        }
        return __("%d {$name}%s ago", $count, ($count > 1 ? 's' : ''));
    }

    /**
     * 获取一个基于时间偏移的Unix时间戳
     *
     * @param string $type 时间类型，默认为day，可选minute,hour,day,week,month,quarter,year
     * @param int $offset 时间偏移量 默认为0，正数表示当前type之后，负数表示当前type之前
     * @param string $position 时间的开始或结束，默认为begin，可选前(begin,start,first,front)，end
     * @param int $year 基准年，默认为null，即以当前年为基准
     * @param int $month 基准月，默认为null，即以当前月为基准
     * @param int $day 基准天，默认为null，即以当前天为基准
     * @param int $hour 基准小时，默认为null，即以当前年小时基准
     * @param int $minute 基准分钟，默认为null，即以当前分钟为基准
     * @return int 处理后的Unix时间戳
     */
    public static function unixtime($type = 'day', $offset = 0, $position = 'begin', $year = null, $month = null, $day = null, $hour = null, $minute = null)
    {
        $year = is_null($year) ? date('Y') : $year;
        $month = is_null($month) ? date('m') : $month;
        $day = is_null($day) ? date('d') : $day;
        $hour = is_null($hour) ? date('H') : $hour;
        $minute = is_null($minute) ? date('i') : $minute;
        $position = in_array($position, array('begin', 'start', 'first', 'front'));

        switch ($type) {
            case 'minute':
                $time = $position ? mktime($hour, $minute + $offset, 0, $month, $day, $year) : mktime($hour, $minute + $offset, 59, $month, $day, $year);
                break;
            case 'hour':
                $time = $position ? mktime($hour + $offset, 0, 0, $month, $day, $year) : mktime($hour + $offset, 59, 59, $month, $day, $year);
                break;
            case 'day':
                $time = $position ? mktime(0, 0, 0, $month, $day + $offset, $year) : mktime(23, 59, 59, $month, $day + $offset, $year);
                break;
            case 'week':
                $time = $position ?
                    mktime(0, 0, 0, $month, $day - date("w", mktime(0, 0, 0, $month, $day, $year)) + 1 - 7 * (-$offset), $year) :
                    mktime(23, 59, 59, $month, $day - date("w", mktime(0, 0, 0, $month, $day, $year)) + 7 - 7 * (-$offset), $year);
                break;
            case 'month':
                $time = $position ? mktime(0, 0, 0, $month + $offset, 1, $year) : mktime(23, 59, 59, $month + $offset, cal_days_in_month(CAL_GREGORIAN, $month + $offset, $year), $year);
                break;
            case 'quarter':
                $time = $position ?
                    mktime(0, 0, 0, 1 + ((ceil(date('n', mktime(0, 0, 0, $month, $day, $year)) / 3) + $offset) - 1) * 3, 1, $year) :
                    mktime(23, 59, 59, (ceil(date('n', mktime(0, 0, 0, $month, $day, $year)) / 3) + $offset) * 3, cal_days_in_month(CAL_GREGORIAN, (ceil(date('n', mktime(0, 0, 0, $month, $day, $year)) / 3) + $offset) * 3, $year), $year);
                break;
            case 'year':
                $time = $position ? mktime(0, 0, 0, 1, 1, $year + $offset) : mktime(23, 59, 59, 12, 31, $year + $offset);
                break;
            default:
                $time = mktime($hour, $minute, 0, $month, $day, $year);
                break;
        }
        return $time;
    }

    /**
     *  获取当前时间文本，年
     */
    public static function toY($time = 0)
    {
        $time = $time ?: time();
        return date('Y', $time);
    }
    /**
     *  获取当前时间文本，年-月
     */
    public static function toYM($time = 0)
    {
        $time = $time ?: time();
        return date('Y-m', $time);
    }
    /**
     *  获取当前时间文本，年-月-日
     */
    public static function toYMD($time = 0)
    {
        $time = $time ?: time();
        return date('Y-m-d', $time);
    }
    /**
     *  获取当前时间文本，月
     */
    public static function toM($time = 0)
    {
        $time = $time ?: time();
        return date('m', $time);
    }
    /**
     *  获取当前时间文本，月
     */
    public static function toM2($time = 0)
    {
        $time = $time ?: time();
        return date('M', $time);
    }
    /**
     *  获取当前时间文本，月-日
     */
    public static function toMD($time = 0)
    {
        $time = $time ?: time();
        return date('m-d', $time);
    }

    /**
     *  获取当前日时分文本，月-日 时:分
     */
    public static function toMDH($time = 0)
    {
        $time = $time ?: time();
        return date('m-d H:i', $time);
    }

    /**
     *  获取当前时分文本，时:分
     */
    public static function toHi($time = 0)
    {
        $time = $time ?: time();
        return date('H:i', $time);
    }

    /**
     *  获取当前星期 返回 一 二 三 ...日
     */
    public static function toWeek($time = 0)
    {
        $time = $time ?: time();
        $week = self::toWeekInt($time);
        switch ($week) {
            case 1 :
                return "一";
            case 2 :
                return "二";
            case 3 :
                return "三";
            case 4 :
                return "四";
            case 5 :
                return "五";
            case 6 :
                return "六";
            case 0 :
                return "日";
        };
        return $week;
    }

    /**
     *  获取当前星期 返回数字
     */
    public static function toWeekInt($time = 0)
    {
        $time = $time ?: time();
        return date('w', $time);
    }

    /**
     *  获取当前时间文本，时间戳 转 时间
     */
    public static function toYMDHI($time = 0)
    {
        $time = $time ?: time();
        return date('Y-m-d H:i', $time);
    }

    /**
     *  时间戳 转 时间  1521006954 -> 2018-03-14 13:55:54
     * @return false|string
     */
    public static function toYMDS($time = 0)
    {
        $time = $time ?: time();
        return date('Y-m-d H:i:s', $time);
    }

    /**
     *  获取当前日期的时间戳
     * @return false|string
     */
    public static function todayInt()
    {
        return strtotime(date('Y-m-d', time()));
    }

    /**
     *  获取之前日期的时间戳 默认昨天
     * @return false|string
     */
    public static function subdayInt($n = 1)
    {
        return strtotime(date('Y-m-d', time() - $n * 86400));
    }

    /**
     *  获取当前月份的时间戳
     * @return false|string
     */
    public static function monthInt($time)
    {
        $time = $time ?: time();
        return strtotime(date('Y-m', $time));
    }

    /**
     *  获取当前年份的时间戳
     * @return false|string
     */
    public static function yearInt()
    {
        return strtotime(date('Y', time()));
    }

    //添加天数
    public static function addDay($now = 0, $days = 0)
    {
        if (!$now) $now = time();
        return $now + $days * 86400;
    }


    //获取某个时间的上一个月 下一个月的时间戳
    public static function getNextMonth($time, $next)
    {
        //得到系统的年月
        $tmp_date = date("Ym", $time);
        //切割出年份
        $tmp_year = substr($tmp_date, 0, 4);
        //切割出月份
        $tmp_mon = substr($tmp_date, 4, 2);
        $tmp_nextmonth = mktime(0, 0, 0, $tmp_mon + 1, 1, $tmp_year);
        $tmp_forwardmonth = mktime(0, 0, 0, $tmp_mon - 1, 1, $tmp_year);

        //得到当前时间的下一个月
        if ($next) return $fm_next_month = strtotime(date("Y-m",$tmp_nextmonth));
        //得到当前时间的上一个月
        return $fm_forward_month = strtotime(date("Y-m",$tmp_forwardmonth));
    }

    //转Unix时间戳
    public static function toInt($now = '')
    {
        return strtotime($now);
    }

    //秒数转换为分秒 get_second_m_s(8000)
    public static function get_second_m_s($seconds)
    {
        return gmdate("i:s", $seconds);
    }

    //获取所有时区
    public static function getMyTimezone()
    {
        return ["Africa/Abidjan" => 0, "Africa/Accra" => 0, "Africa/Addis_Ababa" => 3, "Africa/Algiers" => 1, "Africa/Asmara" => 3, "Africa/Bamako" => 0, "Africa/Bangui" => 1, "Africa/Banjul" => 0, "Africa/Bissau" => 0, "Africa/Blantyre" => 2, "Africa/Brazzaville" => 1, "Africa/Bujumbura" => 2, "Africa/Cairo" => 2, "Africa/Casablanca" => 1, "Africa/Ceuta" => 2, "Africa/Conakry" => 0, "Africa/Dakar" => 0, "Africa/Dar_es_Salaam" => 3, "Africa/Djibouti" => 3, "Africa/Douala" => 1, "Africa/El_Aaiun" => 1, "Africa/Freetown" => 0, "Africa/Gaborone" => 2, "Africa/Harare" => 2, "Africa/Johannesburg" => 2, "Africa/Juba" => 3, "Africa/Kampala" => 3, "Africa/Khartoum" => 3, "Africa/Kigali" => 2, "Africa/Kinshasa" => 1, "Africa/Lagos" => 1, "Africa/Libreville" => 1, "Africa/Lome" => 0, "Africa/Luanda" => 1, "Africa/Lubumbashi" => 2, "Africa/Lusaka" => 2, "Africa/Malabo" => 1, "Africa/Maputo" => 2, "Africa/Maseru" => 2, "Africa/Mbabane" => 2, "Africa/Mogadishu" => 3, "Africa/Monrovia" => 0, "Africa/Nairobi" => 3, "Africa/Ndjamena" => 1, "Africa/Niamey" => 1, "Africa/Nouakchott" => 0, "Africa/Ouagadougou" => 0, "Africa/Porto-Novo" => 1, "Africa/Sao_Tome" => 0, "Africa/Tripoli" => 2, "Africa/Tunis" => 1, "Africa/Windhoek" => 2, "America/Adak" => -9, "America/Anchorage" => -8, "America/Anguilla" => -4, "America/Antigua" => -4, "America/Araguaina" => -3, "America/Argentina/Buenos_Aires" => -3, "America/Argentina/Catamarca" => -3, "America/Argentina/Cordoba" => -3, "America/Argentina/Jujuy" => -3, "America/Argentina/La_Rioja" => -3, "America/Argentina/Mendoza" => -3, "America/Argentina/Rio_Gallegos" => -3, "America/Argentina/Salta" => -3, "America/Argentina/San_Juan" => -3, "America/Argentina/San_Luis" => -3, "America/Argentina/Tucuman" => -3, "America/Argentina/Ushuaia" => -3, "America/Aruba" => -4, "America/Asuncion" => -4, "America/Atikokan" => -5, "America/Bahia" => -3, "America/Bahia_Banderas" => -5, "America/Barbados" => -4, "America/Belem" => -3, "America/Belize" => -6, "America/Blanc-Sablon" => -4, "America/Boa_Vista" => -4, "America/Bogota" => -5, "America/Boise" => -6, "America/Cambridge_Bay" => -6, "America/Campo_Grande" => -4, "America/Cancun" => -5, "America/Caracas" => -4.5, "America/Cayenne" => -3, "America/Cayman" => -4, "America/Chicago" => -5, "America/Chihuahua" => -6, "America/Costa_Rica" => -6, "America/Creston" => -7, "America/Cuiaba" => -4, "America/Curacao" => -4, "America/Danmarkshavn" => 0, "America/Dawson" => -7, "America/Dawson_Creek" => -7, "America/Denver" => -6, "America/Detroit" => -4, "America/Dominica" => -4, "America/Edmonton" => -6, "America/Eirunepe" => -5, "America/El_Salvador" => -6, "America/Fortaleza" => -3, "America/Glace_Bay" => -3, "America/Godthab" => -2, "America/Goose_Bay" => -3, "America/Grand_Turk" => -4, "America/Grenada" => -4, "America/Guadeloupe" => -4, "America/Guatemala" => -6, "America/Guayaquil" => -5, "America/Guyana" => -4, "America/Halifax" => -3, "America/Havana" => -4, "America/Hermosillo" => -7, "America/Indiana/Indianapolis" => -4, "America/Indiana/Knox" => -5, "America/Indiana/Marengo" => -4, "America/Indiana/Petersburg" => -4, "America/Indiana/Tell_City" => -5, "America/Indiana/Vevay" => -4, "America/Indiana/Vincennes" => -4, "America/Indiana/Winamac" => -4, "America/Inuvik" => -6, "America/Iqaluit" => -4, "America/Jamaica" => -5, "America/Juneau" => -8, "America/Kentucky/Louisville" => -4, "America/Kentucky/Monticello" => -4, "America/Kralendijk" => -4, "America/La_Paz" => -4, "America/Lima" => -5, "America/Los_Angeles" => -7, "America/Lower_Princes" => -4, "America/Maceio" => -3, "America/Managua" => -6, "America/Manaus" => -4, "America/Marigot" => -4, "America/Martinique" => -4, "America/Matamoros" => -5, "America/Mazatlan" => -6, "America/Menominee" => -5, "America/Merida" => -5, "America/Metlakatla" => -8, "America/Mexico_City" => -5, "America/Miquelon" => -2, "America/Moncton" => -3, "America/Monterrey" => -5, "America/Montevideo" => -3, "America/Montserrat" => -4, "America/Nassau" => -4, "America/New_York" => -4, "America/Nipigon" => -4, "America/Nome" => -8, "America/Noronha" => -2, "America/North_Dakota/Beulah" => -5, "America/North_Dakota/Center" => -5, "America/North_Dakota/New_Salem" => -5, "America/Ojinaga" => -6, "America/Panama" => -5, "America/Pangnirtung" => -4, "America/Paramaribo" => -3, "America/Phoenix" => -7, "America/Port-au-Prince" => -4, "America/Port_of_Spain" => -4, "America/Porto_Velho" => -4, "America/Puerto_Rico" => -4, "America/Rainy_River" => -5, "America/Rankin_Inlet" => -5, "America/Recife" => -3, "America/Regina" => -6, "America/Resolute" => -5, "America/Rio_Branco" => -5, "America/Santa_Isabel" => -7, "America/Santarem" => -3, "America/Santiago" => -3, "America/Santo_Domingo" => -4, "America/Sao_Paulo" => -3, "America/Scoresbysund" => 0, "America/Sitka" => -8, "America/St_Barthelemy" => -4, "America/St_Johns" => -2.5, "America/St_Kitts" => -4, "America/St_Lucia" => -4, "America/St_Thomas" => -4, "America/St_Vincent" => -4, "America/Swift_Current" => -6, "America/Tegucigalpa" => -6, "America/Thule" => -3, "America/Thunder_Bay" => -4, "America/Tijuana" => -7, "America/Toronto" => -4, "America/Tortola" => -4, "America/Vancouver" => -7, "America/Whitehorse" => -7, "America/Winnipeg" => -5, "America/Yakutat" => -8, "America/Yellowknife" => -6, "Antarctica/Casey" => 8, "Antarctica/Davis" => 7, "Antarctica/DumontDUrville" => 10, "Antarctica/Macquarie" => 11, "Antarctica/Mawson" => 5, "Antarctica/McMurdo" => 12, "Antarctica/Palmer" => -3, "Antarctica/Rothera" => -3, "Antarctica/Syowa" => 3, "Antarctica/Troll" => 2, "Antarctica/Vostok" => 6, "Arctic/Longyearbyen" => 2, "Asia/Aden" => 3, "Asia/Almaty" => 6, "Asia/Amman" => 3, "Asia/Anadyr" => 12, "Asia/Aqtau" => 5, "Asia/Aqtobe" => 5, "Asia/Ashgabat" => 5, "Asia/Baghdad" => 3, "Asia/Bahrain" => 3, "Asia/Baku" => 5, "Asia/Bangkok" => 7, "Asia/Beirut" => 3, "Asia/Bishkek" => 6, "Asia/Brunei" => 8, "Asia/Chita" => 8, "Asia/Choibalsan" => 9, "Asia/Colombo" => 5.5, "Asia/Damascus" => 3, "Asia/Dhaka" => 6, "Asia/Dili" => 9, "Asia/Dubai" => 4, "Asia/Dushanbe" => 5, "Asia/Gaza" => 3, "Asia/Hebron" => 3, "Asia/Ho_Chi_Minh" => 7, "Asia/Hong_Kong" => 8, "Asia/Hovd" => 8, "Asia/Irkutsk" => 8, "Asia/Jakarta" => 7, "Asia/Jayapura" => 9, "Asia/Jerusalem" => 3, "Asia/Kabul" => 4.5, "Asia/Kamchatka" => 12, "Asia/Karachi" => 5, "Asia/Kathmandu" => 5.75, "Asia/Khandyga" => 9, "Asia/Kolkata" => 5.5, "Asia/Krasnoyarsk" => 7, "Asia/Kuala_Lumpur" => 8, "Asia/Kuching" => 8, "Asia/Kuwait" => 3, "Asia/Macau" => 8, "Asia/Magadan" => 10, "Asia/Makassar" => 8, "Asia/Manila" => 8, "Asia/Muscat" => 4, "Asia/Nicosia" => 3, "Asia/Novokuznetsk" => 7, "Asia/Novosibirsk" => 6, "Asia/Omsk" => 6, "Asia/Oral" => 5, "Asia/Phnom_Penh" => 7, "Asia/Pontianak" => 7, "Asia/Pyongyang" => 9, "Asia/Qatar" => 3, "Asia/Qyzylorda" => 6, "Asia/Rangoon" => 6.5, "Asia/Riyadh" => 3, "Asia/Sakhalin" => 10, "Asia/Samarkand" => 5, "Asia/Seoul" => 9, "Asia/Shanghai" => 8, "Asia/Singapore" => 8, "Asia/Srednekolymsk" => 11, "Asia/Taipei" => 8, "Asia/Tashkent" => 5, "Asia/Tbilisi" => 4, "Asia/Tehran" => 4.5, "Asia/Thimphu" => 6, "Asia/Tokyo" => 9, "Asia/Ulaanbaatar" => 9, "Asia/Urumqi" => 6, "Asia/Ust-Nera" => 10, "Asia/Vientiane" => 7, "Asia/Vladivostok" => 10, "Asia/Yakutsk" => 9, "Asia/Yekaterinburg" => 5, "Asia/Yerevan" => 4, "Atlantic/Azores" => 0, "Atlantic/Bermuda" => -3, "Atlantic/Canary" => 1, "Atlantic/Cape_Verde" => -1, "Atlantic/Faroe" => 1, "Atlantic/Madeira" => 1, "Atlantic/Reykjavik" => 0, "Atlantic/South_Georgia" => -2, "Atlantic/St_Helena" => 0, "Atlantic/Stanley" => -3, "Australia/Adelaide" => 9.5, "Australia/Brisbane" => 10, "Australia/Broken_Hill" => 9.5, "Australia/Currie" => 10, "Australia/Darwin" => 9.5, "Australia/Eucla" => 8.75, "Australia/Hobart" => 10, "Australia/Lindeman" => 10, "Australia/Lord_Howe" => 10.5, "Australia/Melbourne" => 10, "Australia/Perth" => 8, "Australia/Sydney" => 10, "Europe/Amsterdam" => 2, "Europe/Andorra" => 2, "Europe/Athens" => 3, "Europe/Belgrade" => 2, "Europe/Berlin" => 2, "Europe/Bratislava" => 2, "Europe/Brussels" => 2, "Europe/Bucharest" => 3, "Europe/Budapest" => 2, "Europe/Busingen" => 2, "Europe/Chisinau" => 3, "Europe/Copenhagen" => 2, "Europe/Dublin" => 1, "Europe/Gibraltar" => 2, "Europe/Guernsey" => 1, "Europe/Helsinki" => 3, "Europe/Isle_of_Man" => 1, "Europe/Istanbul" => 3, "Europe/Jersey" => 1, "Europe/Kaliningrad" => 2, "Europe/Kiev" => 3, "Europe/Lisbon" => 1, "Europe/Ljubljana" => 2, "Europe/London" => 1, "Europe/Luxembourg" => 2, "Europe/Madrid" => 2, "Europe/Malta" => 2, "Europe/Mariehamn" => 3, "Europe/Minsk" => 3, "Europe/Monaco" => 2, "Europe/Moscow" => 3, "Europe/Oslo" => 2, "Europe/Paris" => 2, "Europe/Podgorica" => 2, "Europe/Prague" => 2, "Europe/Riga" => 3, "Europe/Rome" => 2, "Europe/Samara" => 4, "Europe/San_Marino" => 2, "Europe/Sarajevo" => 2, "Europe/Simferopol" => 3, "Europe/Skopje" => 2, "Europe/Sofia" => 3, "Europe/Stockholm" => 2, "Europe/Tallinn" => 3, "Europe/Tirane" => 2, "Europe/Uzhgorod" => 3, "Europe/Vaduz" => 2, "Europe/Vatican" => 2, "Europe/Vienna" => 2, "Europe/Vilnius" => 3, "Europe/Volgograd" => 3, "Europe/Warsaw" => 2, "Europe/Zagreb" => 2, "Europe/Zaporozhye" => 3, "Europe/Zurich" => 2, "Indian/Antananarivo" => 3, "Indian/Chagos" => 6, "Indian/Christmas" => 7, "Indian/Cocos" => 6.5, "Indian/Comoro" => 3, "Indian/Kerguelen" => 5, "Indian/Mahe" => 4, "Indian/Maldives" => 5, "Indian/Mauritius" => 4, "Indian/Mayotte" => 3, "Indian/Reunion" => 4, "Pacific/Apia" => 13, "Pacific/Auckland" => 12, "Pacific/Bougainville" => 11, "Pacific/Chatham" => 12.75, "Pacific/Chuuk" => 10, "Pacific/Easter" => -5, "Pacific/Efate" => 11, "Pacific/Enderbury" => 13, "Pacific/Fakaofo" => 13, "Pacific/Fiji" => 12, "Pacific/Funafuti" => 12, "Pacific/Galapagos" => -6, "Pacific/Gambier" => -9, "Pacific/Guadalcanal" => 11, "Pacific/Guam" => 10, "Pacific/Honolulu" => -10, "Pacific/Johnston" => -10, "Pacific/Kiritimati" => 14, "Pacific/Kosrae" => 11, "Pacific/Kwajalein" => 12, "Pacific/Majuro" => 12, "Pacific/Marquesas" => -9.5, "Pacific/Midway" => -11, "Pacific/Nauru" => 12, "Pacific/Niue" => -11, "Pacific/Norfolk" => 11.5, "Pacific/Noumea" => 11, "Pacific/Pago_Pago" => -11, "Pacific/Palau" => 9, "Pacific/Pitcairn" => -8, "Pacific/Pohnpei" => 11, "Pacific/Port_Moresby" => 10, "Pacific/Rarotonga" => -10, "Pacific/Saipan" => 10, "Pacific/Tahiti" => -10, "Pacific/Tarawa" => 12, "Pacific/Tongatapu" => 13, "Pacific/Wake" => 12, "Pacific/Wallis" => 12, "UTC" => 0];
    }
}
