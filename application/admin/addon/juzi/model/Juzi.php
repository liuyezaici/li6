<?php
namespace app\admin\addon\juzi\model;

use think\Model;
use think\Cookie;

class Juzi extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';

    //定义系统分类
    public static $typeIdGushi = 1; //古诗
    public static $typeNameGushi = '古诗';
    public static $typeIdSanwen = 2; //散文
    public static $typeNameSanwen = '散文';
    public static $typeIdXiaoshuo = 3; //小说
    public static $typeNameXiaoshuo= '小说';
    public static $typeIdXiandaiWen = 10; //现代文
    public static $typeNameXiandaiWen = '现代文';

    protected $juziStatusPause = 0;
    protected $juziStatusNoraml = 1;

    //保存句子的标签
    public static function saveJuziTagIds($juziId, $tagIdArray=[]) {
        self::where([
            'id' => $juziId,
        ])->update([
            'tagids' => join(',', $tagIdArray)
        ]);
    }
    //替换所有全角符号
    public static function changeQuanjiaoCode($content='')
    {
        $content = str_replace('：', ':', $content);
        $content = str_replace('；', ';', $content);
        $content = str_replace('？', '?', $content);
        $content = str_replace('！', '!', $content);
        $content = str_replace('‘', '\'', $content);
        $content = str_replace('’', '\'', $content);
        $content = str_replace('“', '"', $content);
        $content = str_replace('”', '"', $content);
        $content = str_replace('，', ',', $content);
        $content = str_replace('。', '.', $content);
        $content = str_replace('—', '-', $content);
        $content = str_replace('（', '(', $content);
        $content = str_replace('）', ')', $content);
        return $content;
    }
    public static function removeAllCode($content='') {
        $content = str_replace('&nbsp;', '', $content);
        $content = str_replace('&rdquo;', '"', $content);
        $content = str_replace('&ldquo;', '"', $content);
        $content = str_replace('&mdash;', '-', $content);
        $coudeStr = '\\&^$#@`|';
        $coudeStr .= '﹡（）—﹢﹦﹠﹪﹩﹟～￣﹫﹋﹉﹊﹏﹍﹎｜‖·¡︴ˉ〈〉｛｝［］︵︶︷︸︿﹀︹︺︽︾﹄﹃︻︼﹁﹂︻︼「」〔〕‹›';
//        //数字 序号
        $coudeStr .= '㈠㈡㈢㈣㈤㈥㈦㈧㈨㈩ⅠⅡⅢⅣⅤⅥⅦⅧⅨⅩ❶❷❸❹❺❻❼❽❾❿①②③④⑤⑥⑦⑧⑨⑩⑪⑫⑬⑭⑮⑯⑰⑱⑲⑳';
        $coudeStr .= '⒈⒉⒊⒋⒌⒍⒎⒏⒐⒑⒒⒓⒔⒕⒖⒗⒘⒙⒚⒛⑴⑵⑶⑷⑸⑹⑺⑻⑼⑽⑾⑿⒀⒁⒂⒃⒄⒅⒆⒇';
        //数字 单位
        $coudeStr .='＋－×÷﹢﹣±／＝≈≡≠∧∨∑∏∪∩∈⊙⌒⊥∥∠∽≌＜＞≤≥≮≯∧∨√﹙﹚﹛﹜∫∮∝∞⊙º¹²⁴ⁿ₁₂₃∶½⅔¼¾⅛⅜⅝⅞∴∷';
        $coudeStr .= 'αβγδεζηθικλμνξοπρστυφχψω％‰℅°℃℉′″￠〒¤○㎎㎏㎜㎝㎞㎡㎥㏄㏎㏕＄￡￥€㏒㏑';
        //希腊 俄文
        $coudeStr .= 'ΑΒΓΔΕΖΗΘΙΚΛΜνξοπρστυφχψωΝΞΟΠΡΣΤΥΦΧΨΩ';
        $coudeStr .= 'абвгдеёжзийкАБВГДЕЁЖЗЙКмнопрстуфхцЛМНОПРСТУФХЦ';
        $coudeStr .= 'чшщъыьэюяЧШЩЪЫЬЭЮЯ';
         //拼音 注音
        $coudeStr .= 'āáǎàūúǔùēéěèêŌÓǑÒīíǐìǖǘǚǜü';
        $coudeStr .= 'ㄅㄆㄇㄈㄉㄊㄋㄌㄍㄎㄏㄐㄑㄒㄓㄔㄕㄖㄗㄘㄙㄚㄛㄜㄝㄟㄠㄡㄢㄣㄤㄥㄦㄧㄨㄩ';
        $coudeStr .= '艹丶乛亠冖宀冫丷灬丨亅丿乚勹匚冂凵爫忄丬纟疒阝刂卩犭辶廴钅礻衤罒覀夂癶牜虍歺糹釒飠丨丿丶乛乀乁乙乚';
        $coudeStr .= '┌┬┐┏┳┓╒╤╕╭─╮├┼┤┣╋┫╞╪╡│╳┃└┴┘┗┻┛╘╧╛╰━╯';
        $coudeStr .= '┏━┓╔╦╗─│━┃┄┆┃┃╠╬╣┉┋┈┊┉┋┗━┛╚╩╝╲╱';
        $coudeStr .= '┞┟┠┡┢┦┧┨┩┪╉╊┭┮┯┰┱┲┵┶┷┸╇╈┹┺┽┾┿╀╁╂╃╄╅╆';
        $coudeStr .= '○◇□△▽▷◁☆♤♡♢♧●◆■▲▼▶◀★♠♥♦♣☼☺◘☏☜◐☽♀☑√✔㏂☀☻◙☎☞◑☾♂☒×✘㏘';
        $coudeStr .= '✎✐▁▂▃▄▅▆▇█⊙◎✉☯♨۞✄☄☢☣➴➵卍卐✈✁〠〄♝♞◕†‡¬￢✌☭❂☃☂❦❧✲❈❉✪☉⊕Θ⊿▫◈▣❤✙۩✖✚';
        $coudeStr .= '♩♪♫♬¶♭♯∮‖§Ψ☠⊱⋛⋌⋚⊰⊹▪•‥❀々㊤㊥㊦㊧㊨㊚㊛㊣㊙㈜№㏇㈱㍿㉿®℗™℡✍甴甴囍';
        $coudeStr .= '▧▤▨▥▩▦▣▓∷▒░☌╱╲▁▏↖↗↑←↔◤◥☍╲╱▔▕↙↘↓→↕◣◢☋';
        $coudeStr .= '㍙㍚㍛㍜㍝㍞㍟㍠㍡㍢㍣㍤㍥㍦㍧㍨㍩㍪㍫㍬㍭㍮㍯㍰㍘';
        $coudeStr .= '㏠㏡㏢㏣㏤㏥㏦㏧㏨㏩㏪㏫㏬㏭㏮㏯㏰㏱㏲㏳㏴㏵㏶㏷㏸㏹㏺㏻㏼㏽㏾㋁㋂㋃㋄㋅㋆㋇㋈㋉㋊㋋';
        $coudeStr .= '⒜⒝⒞⒟⒠⒡⒢⒣⒤⒥⒦⒧⒨⒩⒪⒫⒬⒭⒮⒯⒰⒱⒲⒳⒴⒵ⓐⓑⓒⓓⓔⓕⓖⓗⓘⓙⓚⓛⓜⓝⓞⓟⓠⓡⓢⓣⓤⓥⓦⓧⓨⓩ';
        $coudeArray= \fast\Str::splitStr($coudeStr);
        foreach ($coudeArray as $index=> $tmpW) {
            $content = str_replace($tmpW, '', $content);
        }
        return $content;
    }
    //句子重复性检测
    public static function hasJuzi($content='', $id=0) {
        if($id) {
            return self::where([
                    'id' => ['<>', $id],
                    'contenthash' => MD5($content)
                ])->count() > 0 ;
        } else {
            return self::where([
                    'contenthash' => MD5($content)
                ])->count() > 0 ;
        }
    }
    //删除分类后 释放句子分类
    public static function releaseJuziType($myUid, $tid, $defaultTid) {
        return self::where([
            'cuid' => $myUid,
            'typeid' => $tid,
        ])->update([
            'typeid' => $defaultTid,
        ]);
    }
    //获取最新的句子
    public static function getLastJuzi() {
        return self::field('uri,content,createtime,cuid,author,fromid')->order('id', 'desc')->limit(2)->select();
    }
    //修改人气+1
    public static function updateRq($code='') {
        $cacheName = 'readJuzi:'.$code;
        $lastTime = Cookie::get($cacheName);
        if(!$lastTime || $lastTime < time()-60) {
            Cookie::set($cacheName, time(), 60);
            self::where([
                'uri' => $code,
            ])->setInc('rq');
        }
    }
}
