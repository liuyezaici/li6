<?php

//繁体转简体
class fanti
{
    protected static $baiduUrl = 'http://fanyi.baidu.com/v2transapi';
    //百度转简体
    public static function toJian($sContent)
    {
        $str=(func::post_nr_from(self::$baiduUrl, 'http://fanyi.baidu.com', [
            'from' => 'cht',
            'query' => $sContent,
            'simple_means_flag' => '3',
            'to' => 'zh',
            'transtype' => 'realtime',
        ]));
        $str = json_decode($str, true);
        $result = isset($str['trans_result']['data'][0]['dst']) ? $str['trans_result']['data'][0]['dst'] : '';
        return($result);
    }

    //百度转繁体
    public static function toFan($sContent)
    {
        $str=(func::post_nr_from(self::$baiduUrl, 'http://fanyi.baidu.com', [
            'from' => 'zh',
            'query' => $sContent,
            'simple_means_flag' => '3',
            'to' => 'cht',
            'transtype' => 'realtime',
        ]));
        $str = json_decode($str, true);
        $result = $str['trans_result']['data'][0]['dst'];
        return($result);
    }
    //应用例子:
    //繁体转简体：
    //echo fanti::toJian('ab罷皚敗頒辦絆的人-好的搖堯遙窯人謠')."\n";
    //显示结果：ab罢皑败颁办绊的人-好的摇尧遥窑人谣
    //简体转繁体：
    //echo::toFan('ab罢皑败颁办绊的人-好的摇尧遥窑人谣');
    //显示结果：ab罷皚敗頒辦絆的人-好的搖堯遙窯人謠
}