<?php
/**
 * xheditor编辑器模块
 * 
 * 
 */

class xheditor
{

    public function __construct(){
    }

    //将上传内容过滤一遍 支持插入代码
    public static function contentFilter($text=''){
        $text = preg_replace_callback('/<pre([^>]*)>([^<]*)<\/pre>/i',
            function ($match) {
                $match[1]=strtolower($match[1]);
                if(!$match[1])$match[1]='plain';
                $match[2]=preg_replace("/&lt;/",'&#38;lt;',$match[2]);
                $match[2]=preg_replace("/&gt;/",'&#38;gt;',$match[2]);
                return '<pre class="code">'.$match[2].'</pre>';
            }, $text);
        return $text;
    }
    //将内容输出过滤一遍 支持插入代码
    public static function contentToHtml($text=''){
        $text = preg_replace_callback('/<pre([^>]*)>([^<]*)<\/pre>/i',
            function ($match) {
                $match[1]=strtolower($match[1]);
                if(!$match[1])$match[1]='plain';
                $match[2]=preg_replace("/&#38;lt;/",'&lt;',$match[2]);
                $match[2]=preg_replace("/&#38;gt;/",'&gt;',$match[2]);
                return '<pre class="code">'.$match[2].'</pre>';
            }, $text);
        return $text;
    }
}