<?php
namespace app\admin\addon\songs\model;

use app\admin\addon\songs\model\SongsUser as userModel;
use fast\Date;
use think\Model;
use think\Cookie;
use \fast\Str;
use \fast\File;

class Songs extends Model
{

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';


    protected $songsStatusPause = 0;
    protected $songsStatusNoraml = 1;

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
        $content = str_replace('…', '...', $content);
        return $content;
    }
    public static function removeAllCode($content='') {
        $coudeStr = '*/\\_+=&^%$#@`~|';
        $coudeStr .= '﹡（）—﹢﹦﹠﹪﹩﹟～￣﹫﹋﹉﹊﹏﹍﹎｜‖·¡︴ˉ【】『』〖〗《》〈〉｛｝［］︵︶︷︸︿﹀︹︺︽︾﹄﹃︻︼﹁﹂︻︼「」〔〕‹›';
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
        $coudeStr .= '✎✐▁▂▃▄▅▆▇█⊙◎✉☯♨۞✄☄☢☣➴➵卍卐✈✁〠〄♝♞◕†‡¬￢✌☭❂☃☂❦❧✲❈❉*✪☉⊕Θ⊿▫◈▣❤✙۩✖✚';
        $coudeStr .= '♩♪♫♬¶♭♯∮‖§Ψ☠⊱⋛⋌⋚⊰⊹▪•‥❀々㊤㊥㊦㊧㊨㊚㊛㊣㊙㈜№㏇㈱㍿㉿®℗™℡✍甴甴囍';
        $coudeStr .= '▧▤▨▥▩▦▣▓∷▒░☌╱╲▁▏↖↗↑←↔◤◥☍╲╱▔▕↙↘↓→↕◣◢☋';
        $coudeStr .= '㍙㍚㍛㍜㍝㍞㍟㍠㍡㍢㍣㍤㍥㍦㍧㍨㍩㍪㍫㍬㍭㍮㍯㍰㍘';
        $coudeStr .= '㏠㏡㏢㏣㏤㏥㏦㏧㏨㏩㏪㏫㏬㏭㏮㏯㏰㏱㏲㏳㏴㏵㏶㏷㏸㏹㏺㏻㏼㏽㏾㋁㋂㋃㋄㋅㋆㋇㋈㋉㋊㋋';
        $coudeStr .= '⒜⒝⒞⒟⒠⒡⒢⒣⒤⒥⒦⒧⒨⒩⒪⒫⒬⒭⒮⒯⒰⒱⒲⒳⒴⒵ⓐⓑⓒⓓⓔⓕⓖⓗⓘⓙⓚⓛⓜⓝⓞⓟⓠⓡⓢⓣⓤⓥⓦⓧⓨⓩ';
        $coudeArray= Str::splitStr($coudeStr);
        foreach ($coudeArray as $index=> $tmpW) {
            $content = str_replace($tmpW, '', $content);
        }
        return $content;
    }
    //歌曲重复性检测
    public static function hasSongs($songid='', $id=0) {
        if($id) {
            return self::where([
                    'id' => ['<>', $id],
                    'songid' => $songid
                ])->count() > 0 ;
        } else {
            return self::where([
                    'songid' => $songid
                ])->count() > 0 ;
        }
    }

    //获取最新的歌曲
    public static function getLastSongs() {
        return self::field('uri,title,singer')->order('id', 'desc')->find();
    }
    //修改人气+1
    public static function updateRq($code='') {
        $cacheName = 'readSongs:'.$code;
        $lastTime = Cookie::get($cacheName);
        if(!$lastTime || $lastTime < time()-60) {
            Cookie::set($cacheName, time(), 60);
            self::where([
                'uri' => $code,
            ])->setInc('rq');
        }
    }
    //插入歌曲
    public static function insertSongs($songName, $songId, $singerIdList=[]) {
        sort($singerIdList);
        $newData = [
        'uri' => Str::getRadomTime(20),
        'title' => $songName,
        'songid' => $songId,
        'singer' => join(',', $singerIdList),
        ];
        self::insert($newData);
        return $newData;
    }

    /**
     *  加密获取params
     * @param $param            // 待加密的明文信息数据
     * @param string $method    // 加密算法
     * @param string $key       // key
     * @param string $options   // options 是以下标记的按位或： OPENSSL_RAW_DATA 、 OPENSSL_ZERO_PADDING
     * @param string $iv        // 非 NULL 的初始化向量
     * @return string
     *
     * $key 在加密 params 中第一次用的是固定的第四个参数 0CoJUm6Qyw8W8jud,在第二次加密中用的是 js 中随机生成的16位字符串
     */
    private static function __aesGetParams($param,$method = 'AES-128-CBC',$key = 'JK1M5sQAEcAZ46af',$options = '0',$iv = '0102030405060708')
    {
        $firstEncrypt = openssl_encrypt($param,$method,'0CoJUm6Qyw8W8jud',$options,$iv);
        $secondEncrypt = openssl_encrypt($firstEncrypt,$method,$key,$options,$iv);
        return $secondEncrypt;
    }
    /**
     *  encSecKey 在 js 中有 res 方法加密。
     *  其中三个参数分别为上面随机生成的16为字符串,第二个参数 $second_param,第三个参数 $third_param 都是固定写死的，这边使用抄下来的一个固定 encSecKey
     * @return bool
     */
    protected static function __getEncSecKey()
    {
        return '2a98b8ea60e8e0dd0369632b14574cf8d4b7a606349669b2609509978e1b5f96ed8fbe53a90c0bb74497cd2eb965508bff5bfa065394a52ea362539444f18f423f46aded5ed9a1788d110875fb976386aa4f5d784321433549434bccea5f08d1888995bdd2eb015b2236f5af15099e3afbb05aa817c92bfe3214671e818ea16b';
    }
    protected static function __httpPost($url,$header,$data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);                // 0不带头文件，1带头文件（返回值中带有头文件）
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS , $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);        // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);        // 使用自动跳转
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);           // 自动设置Referer
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);        //设置等待时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);              //设置cURL允许执行的最长秒数
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }


    //采集评论
    public static function caijiComments($songidWY, $page=1, $songuri) {
        // 设置请求头
        $headers = array(
            'Accept:*/*',
            'Accept-Language:zh-CN,zh;q=0.9',
            'Connection:keep-alive',
            'Content-Type:application/x-www-form-urlencoded',
            'Host:music.163.com',
            'Origin:https://music.163.com',
            // 模拟浏览器设置 User-Agent ，否则取到的数据不完整
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36'
        );
        // 拼接歌曲的url
        $url = 'https://music.163.com/weapi/v1/resource/comments/R_SO_4_'. $songidWY .'?csrf_token=';
        // 拼接加密 params 用到的第一个参数
        $pageSize = 20;
        $offset = ($page-1) * $pageSize;
        $first_param = '{"rid":"R_SO_4_'. $songidWY .'","offset":"'. $offset .'","total":"true","limit":"'. $pageSize .'","csrf_token":""}';
        $params = array('params' => self::__aesGetParams($first_param), 'encSecKey' => self::__getEncSecKey());
        $htmlInfo = self::__httpPost($url, $headers, http_build_query($params));
        $jsonData = json_decode($htmlInfo, true);
        if(!$jsonData) return '获取不到内容:'.$htmlInfo;
        if(!isset($jsonData['comments'])) {
            return '获取不到comments';
        }
        $comments = $jsonData['comments'];
//        print_r($comments);
//        exit;
        $total = $jsonData['total'];
        $more = $jsonData['more'];
        $cacheList = [];
        $addComment = function ($item) use($songuri,$songidWY, &$addComment, &$cacheList) {
            $userObj = $item['user'];
            $wyUid = $userObj['userId'];
            $wyNickname = $userObj['nickname'];
            $wyAvatar = $userObj['avatarUrl'];
            $content = $item['content'];
            $commentId = isset($item['commentId']) ? $item['commentId']: 0;
            $time = isset($item['time']) ? $item['time']: 0;
            $likedCount = isset($item['likedCount']) ? $item['likedCount']: 0;
            $beReplied = isset($item['beReplied']) ? $item['beReplied']: [];
            $sonReplied = [];
            if($beReplied) {
                foreach ($beReplied as $sonItem) {
                    $sonReplied[] = $addComment($sonItem);
                }
            }
            $userUri = SongsUser::addUserGetUri($wyUid, $wyNickname, $wyAvatar);

            $itemBack =  [
                'nickname'=> $wyNickname,
                'useruri'=> $userUri,
                'avatar'=> $wyAvatar,
                'content'=> $content,
                'time'=> $time,
                'likedCount'=> $likedCount,
                'beReplied'=> $sonReplied,
            ];
            if($commentId) {
                $cacheList[] = $itemBack;
            }
            return $itemBack;
        };
        foreach ($comments as $item) {
            $addComment($item);
        }
        return [
            'commids' => json_encode($cacheList),
            'total' => $total,
            'more' => $more,
        ];
    }

    protected static $localDomain = 'http://juzi0.natapp1.cc';
    //通过本地电脑采集评论
    public static function caijiLocalComments($songidWY, $page=1, $songuri) {
        $url = self::$localDomain ."/juzi/Juzi/caijiComment?id={$songidWY}&page={$page}&songuri={$songuri}";
        $jsons = File::get_nr($url, '', '', false);
        return json_decode($jsons, true);
    }
    //通过本地电脑采集评论
    public static function caijiLocalGeci($songidWY) {
        $url = self::$localDomain ."/juzi/Juzi/caijiGeci?id={$songidWY}";
        $geci = File::get_nr($url, '', '', false);
        return $geci;
    }

    //采集歌词
    public static function caijiGeci($songidWY) {
        // 拼接歌曲的url
        $url = 'http://music.163.com/api/song/lyric?id='. $songidWY .'&lv=1&kv=1&tv=-1';
//        $url = 'https://music.163.com/weapi/song/lyric?csrf_token=';
        // 拼接加密 params 用到的第一个参数
//        $first_param = '{"rid":"R_SO_4_'. $songidWY .'","csrf_token":""}';
//        $params = array('params' => self::__aesGetParams($first_param), 'encSecKey' => self::__getEncSecKey());
//        $htmlInfo = self::__httpPost($url, $headers, http_build_query($params));
        $htmlInfo = File::post_nr_from($url, 'music.163.com');
        if(!$htmlInfo) return '获取不到内容:'.$htmlInfo;
        $jsonData = json_decode($htmlInfo, true);
        if(!$jsonData) return '获取不到内容:'.$htmlInfo;
        if(!isset($jsonData['lrc'])) {
            return '没有lrc歌词';
        }
        if(!isset($jsonData['lrc']['lyric'])) {
            return '获取不到lrc.lyric';
        }
        $lrc = $jsonData['lrc']['lyric'];
        // 记录评论缓存
        self::where('songid', $songidWY)->update([
            'geci' => $lrc
        ]);
        return $lrc;
    }
    //格式化歌词
    public static function formatGeci($geci='') {
        $geci = str_replace("\n", '<br/>', $geci);
        $geci = preg_replace("/\[([0-9.:]+)\]/", '', $geci);
        return $geci;
    }

    //采集歌曲的歌手
    public static function caijiSong($songidWY) {
        $url = "https://music.163.com/song?id={$songidWY}";
        $htmlInfo = File::get_https($url, 'https://music.163.com');
        if(!$htmlInfo) return '获取不到内容:'.$url;
//        print_r(htmlentities($htmlInfo));exit;
        $songHtml = Str::sp_('<script type="application/ld+json">','</script>', $htmlInfo);
        if(!$songHtml) {
            return '获取不到歌曲的js:'.$url;
        }
        $songName = Str::sp_('"title": "','",', $songHtml);
//      print_r($songName);exit;
        $singerHtml = Str::sp_('<p class="des s-fc4">歌手：','</p>', $htmlInfo);
        if(!$singerHtml) {
            return '获取不到歌曲的歌手:'.$url;
        }
        //<span title="黄晓明 / 邓超 / 佟大为"><a class="s-fc7" href="/artist?id=3094" >黄晓明</a> / <a class="s-fc7" href="/artist?id=2623" >邓超</a> / <a class="s-fc7" href="/artist?id=5234" >佟大为</a></span>
        $singerHtml = strip_tags($singerHtml,'<a>');
        $singerArray = explode('</a>', $singerHtml);
        $singerList = [];
        foreach ($singerArray as $tmp) {
            $tmp = trim($tmp);
            if(!$tmp) continue;
            if(!strstr($tmp, '/artist?id=')) continue;
            $singerId = Str::sp_('/artist?id=','"', $tmp);
            $array_ = explode('>', $tmp);

            $singerName = $array_[1];
            $singerList[] = [
                'id'=> $singerId,
                'name'=> $singerName,
            ];
        }
        $singerIdArray = SongsSinger::insertSingers($singerList);
        $ourSongInfo = self::where('songid', $songidWY)->find();
        if(!$ourSongInfo) {
            $ourSongInfo = self::insertSongs($songName, $songidWY, $singerIdArray);
        }
        return $ourSongInfo;
    }
    //采集歌曲的歌手
    public static function caijiSongSinger($songidWY) {
        $url = "https://music.163.com/song?id={$songidWY}";
        // 设置请求头
        $headers = array(
            'Accept:*/*',
            'Accept-Language:zh-CN,zh;q=0.9',
            'Connection:keep-alive',
            'Content-Type:application/x-www-form-urlencoded',
            'Host:music.163.com',
            'Origin:https://music.163.com',
            // 模拟浏览器设置 User-Agent ，否则取到的数据不完整
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36'
        );
        $htmlInfo = self::__httpPost($url, $headers, []);
        if(!$htmlInfo) return '获取不到内容:'.$url;
        $singerHtml = Str::sp_('<p class="des s-fc4">歌手：','</p>', $htmlInfo);
        if(!$singerHtml) {
            return '获取不到歌曲的歌手:'.$url;
        }
        //<span title="黄晓明 / 邓超 / 佟大为"><a class="s-fc7" href="/artist?id=3094" >黄晓明</a> / <a class="s-fc7" href="/artist?id=2623" >邓超</a> / <a class="s-fc7" href="/artist?id=5234" >佟大为</a></span>
        $singerHtml = strip_tags($singerHtml,'<a>');
        $singerArray = explode('</a>', $singerHtml);
        $singerList = [];
        foreach ($singerArray as $tmp) {
            $tmp = trim($tmp);
            if(!$tmp) continue;
            if(!strstr($tmp, '/artist?id=')) continue;
            $singerId = Str::sp_('/artist?id=','"', $tmp);
            $array_ = explode('>', $tmp);

            $singerName = $array_[1];
            $singerList[] = [
                'id'=> $singerId,
                'name'=> $singerName,
            ];
        }
        $singerIdArray = SongsSinger::insertSingers($singerList);
        return $singerIdArray;
    }
    //搜索网易云音乐
    public static function searchCloudMusic($keyword='', $page=1, $insertLog = false) {
        if($info = self::__getSearchLog($keyword, $page)) {
//            print_r($info);
//            exit;
            return [
                'list'=> json_decode($info['cached'], true),
                'total'=> $info['num'],
            ];
        }
        $url = "https://music.163.com/weapi/cloudsearch/get/web?csrf_token=";
        // 设置请求头
        $headers = array(
            'Accept:*/*',
            'Accept-Language:zh-CN,zh;q=0.9',
            'Connection:keep-alive',
            'Content-Type:application/x-www-form-urlencoded',
            'Host:music.163.com',
            'Origin:https://music.163.com',
            // 模拟浏览器设置 User-Agent ，否则取到的数据不完整
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36'
        );
        // 拼接歌曲的url
        // 拼接加密 params 用到的第一个参数
        $pageSize = 10;
        $offset = ($page-1) * $pageSize;
        $first_param = '{"hlpretag":"<span class=\"s-fc7\">","hlposttag":"</span>","type":"1","s":"'. $keyword .'","offset":"'. $offset .'","total":"true","limit":"'. $pageSize .'","csrf_token":""}';
        $params = array('params' => self::__aesGetParams($first_param), 'encSecKey' => self::__getEncSecKey());
        $htmlInfo = self::__httpPost($url, $headers, http_build_query($params));
        $jsonData = json_decode($htmlInfo, true);
        if(!$jsonData) return '获取不到内容:'.$htmlInfo;
        if(!isset($jsonData['result'])) {
            return '获取不到 result';
        }
        $result = $jsonData['result'];
        if(!$result) {
            return '获取不到 songs:'. $result;
        }
        if(is_string($result) && !strpos('["', $result)) {
            $result = self::jiemiMusic($result);
            $result = json_decode($result, true);
        }
        if(!isset($result['songs'])) {
            return '获取不到 songs:'. $htmlInfo;
        }
        $songs= $result['songs'];
        $total= $result['songCount'];
//        [name] => 喜欢你
//        [id] => 346073
//        [pst] => 0
//        [t] => 0
//        [ar] => Array
//        (
//            [0] => Array
//            (
//                [id] => 11127
//                            [name] => Beyond
                    //        [tns] => Array
                    //        (
                    //        )
                    //
                    //        [alias] => Array
                    //        (
                    //        )
//
//             )
//        )
        $songOurList = [];
        foreach ($songs as &$item) {
            $wySongid = $item['id'];
            $songName = $item['name'];
            $artists = $item['ar'];
            $albumInfo = $item['al'];
            $singerList = [];
            foreach ($artists as $v2) {
                $singerList[] = [
                    'id'=> $v2['id'],
                    'name'=> $v2['name'],
                ];
            }
            $item['singerlist'] = $singerList;
            //插入专辑
//            id: 34110
//            name: "25周年精选"
//            picUrl: "http://p2.music.126.net/ghmbmEQS-IJfZPjdA3KGxg==/82463372084291.jpg"
            $albumIdWy = $albumInfo['id'];
            $albumTitle = $albumInfo['name'];
            $albumAvatar = $albumInfo['picUrl'];
            $singeridWY = $singerList[0]['id'];
            if(!$albumUri = Db('songsSingeralbum')->where('wyalbumidid', $albumIdWy)->value('uri')) {
                $albumUri = Str::getRadomTime(20);
                Db('songsSingeralbum')->insert([
                    'uri' => $albumUri,
                    'title' => $albumTitle,
                    'wyavatar' => $albumAvatar,
                    'wyalbumidid' => $albumIdWy,
                    'wysingerid' => $singeridWY
                ]);
//              print_r('插入'.$albumId.'-'.$albumTitle."\n");
            }
            if(!$ourSongInfo = self::field('uri,songid,title,singer')->where('songid', $wySongid)->find()) {
                $singerIdArray = SongsSinger::insertSingers($singerList);
                $ourSongInfo = self::insertSongs($songName, $wySongid, $singerIdArray);
            }
            $ourSongInfo['album'] = [
                'albumAvatar' => $albumAvatar,
                'albumTitle' => $albumTitle,
                'albumUri' => $albumUri,
            ];
            $songOurList[] = $ourSongInfo;
        }
//        exit;
//        print_r(json_encode($songOurList));exit;
        if($songOurList && $insertLog) {
            self::__insertSearchLog($keyword, $page, $songOurList, $total);
        }
        return [
            'list'=> $songOurList,
            'total'=> $total,
        ];
    }

    //查询记录缓存
    protected static function __getSearchLog($keyword='', $page=1) {
        $info = Db('songsSearch')->field('cached,num')->where([
            'title' => $keyword,
            'page' => $page,
        ])->find();
        return $info;
    }
    //插入查询记录
    protected static function __insertSearchLog($keyword='', $page=1, $resultList=[], $num=0) {
        if(!$has = self::__getSearchLog($keyword, $page)) {
            Db('songsSearch')->insert([
                'title' => $keyword,
                'page' => $page,
                'time' => time(),
                'num' => $num,
                'cached' => json_encode($resultList),
            ]);
        }
    }

    //解密搜索歌曲内容
    public static function jiemiMusic($Hg, $bF='fuck~#$%^&*(458')  {
        function sy($iL) {
            if ($iL < -128) {
                return sy(128 - (-128 - $iL));
            } else if ($iL >= -128 && $iL <= 127) {
                return $iL;
            } else if ($iL > 127) {
                return sy(-129 + $iL - 127);
            }
        };

        function bjl($dL) {
            $ret = array();

            $len = strlen($dL);

            for($i=0; $i<$len; $i++){
                $ret[] = ord(substr($dL, $i, 1));
            }

            return $ret;
        };

        //将hex字符串转换为 数组
        function bdn($Gl) {

            $lJ = array();
            $bin = hex2bin($Gl);
            $len = strlen($bin);

            for($i=0; $i<$len; $i++){
                $lJ[] = sy(ord(substr($bin, $i, 1)));
            }

            return $lJ;
        };


        function biS($lV) {
            $bdB = array();

            $len = count($lV);

            for ($i = 0; $i < 64; $i++) {
                $bdB[$i] = $lV[$i % $len];
            }

            return $bdB;
        };

        function biQQ($GV) {
            $MZ = array();
            $cl = 0;
            $biC = count($GV) / 64;
            for ($i = 0; $i < $biC; $i++) {
                $MZ[$i] = array();
                for ($j = 0; $j < 64; $j++) {
                    $MZ[$i][$j] = $GV[$cl++];
                }
            }
            return $MZ;
        };

        function biA($bdV)  {
            $biU = [82, 9, 106, -43, 48, 54, -91, 56, -65, 64, -93, -98, -127, -13, -41, -5, 124, -29, 57, -126, -101, 47, -1, -121, 52, -114, 67, 68, -60, -34, -23, -53, 84, 123, -108, 50, -90, -62, 35, 61, -18, 76, -107, 11, 66, -6, -61, 78, 8, 46, -95, 102, 40, -39, 36, -78, 118, 91, -94, 73, 109, -117, -47, 37, 114, -8, -10, 100, -122, 104, -104, 22, -44, -92, 92, -52, 93, 101, -74, -110, 108, 112, 72, 80, -3, -19, -71, -38, 94, 21, 70, 87, -89, -115, -99, -124, -112, -40, -85, 0, -116, -68, -45, 10, -9, -28, 88, 5, -72, -77, 69, 6, -48, 44, 30, -113, -54, 63, 15, 2, -63, -81, -67, 3, 1, 19, -118, 107, 58, -111, 17, 65, 79, 103, -36, -22, -105, -14, -49, -50, -16, -76, -26, 115, -106, -84, 116, 34, -25, -83, 53, -123, -30, -7, 55, -24, 28, 117, -33, 110, 71, -15, 26, 113, 29, 41, -59, -119, 111, -73, 98, 14, -86, 24, -66, 27, -4, 86, 62, 75, -58, -46, 121, 32, -102, -37, -64, -2, 120, -51, 90, -12, 31, -35, -88, 51, -120, 7, -57, 49, -79, 18, 16, 89, 39, -128, -20, 95, 96, 81, 127, -87, 25, -75, 74, 13, 45, -27, 122, -97, -109, -55, -100, -17, -96, -32, 59, 77, -82, 42, -11, -80, -56, -21, -69, 60, -125, 83, -103, 97, 23, 43, 4, 126, -70, 119, -42, 38, -31, 105, 20, 99, 85, 33, 12, 125];
            $ud = $bdV >> 4 & 15;
            $tY = $bdV & 15;
            $cl = $ud * 16 + $tY;
            return $biU[$cl];
        };

        function bec($Ni) {
            $bed = array();
            for ($i = 0, $ck = count($Ni); $i < $ck; $i++) {
                $bed[$i] = biA($Ni[$i]);
            }
            return $bed;
        };

        function bdh($CA, $Mg) {
            if (count($CA) != count($Mg)) {
                return $CA;
            }
            $lJ = array();
            $bjz = count($CA);
            for ($i = 0, $ck = $bjz; $i < $ck; $i++) {
                $lJ[$i] = bjC($CA[$i], $Mg[$i]);
            }
            return $lJ;
        };

        function bjC($Mb, $Cw) {
            return sy(sy($Mb) ^ sy($Cw));
        };

        function bjO($iL, $cl) {
            return sy($iL + $cl);
        };

        function bjG($FF) {
            $lJ = array();
            $bjF = count($FF);
            for ($i = 0, $ck = $bjF; $i < $ck; $i++) {
                $lJ[$i] = sy(0 - $FF[$i]);
            }
            return $lJ;
        };

        function bjK($Fy, $LP) {
            $lJ = array();
            $bjJ = count($LP);
            for ($i = 0, $ck = count($Fy); $i < $ck; $i++) {
                $lJ[$i] = bjO($Fy[$i], $LP[$i % $bjJ]);
            }
            return $lJ;
        };

        function MI($dW, $ME, $GC, $bja, $ck) {
            for ($i = 0; $i < $ck; $i++) {
                $GC[$bja + $i] = $dW[$ME + $i];
            }
            return $GC;
        };

        function biq($vR, $lV) {
            $lV = biS($lV);
            $Np = $lV;
            $Nq = biQQ($vR);
            $CR = array();
            $bip = count($Nq);
            for ($i = 0; $i < $bip; $i++) {
                $Nw = bec($Nq[$i]);
                $Nw = bec($Nw);
                $Nx = bdh($Nw, $Np);
                $bio = bjK($Nx, bjG($Np));
                $Nx = bdh($bio, $lV);
                for($ii=0; $ii<64; $ii++){
                    $CR[$ii + ($i * 64)] = $Nx[$ii];
                }
                $Np = $Nq[$i];
            }
            $bef = array();
            for($ii=0; $ii<4; $ii++){
                $bef[$ii] = $CR[(count($CR) - 4) + $ii];
            }

            $ck = bje($bef);
            $lJ = array();

            for($ii=0; $ii<$ck; $ii++){
                $lJ[$ii] = $CR[$ii];
            }
            return $lJ;
        }

        function bii($Hg, $bF) {
            return biq(bdn($Hg), bjl($bF));
        };

        function bje($po) {
            $cQ = 0;
            $cQ += ($po[0] & 255) << 24;
            $cQ += ($po[1] & 255) << 16;
            $cQ += ($po[2] & 255) << 8;
            $cQ += $po[3] & 255;
            return $cQ;
        };

        function bjp($tO) {
            $ck = count($tO);
            $wM = array();
            for ($i = 0; $i < $ck; $i++) {
                $wM[] = bjr($tO[$i]);
            }
            return implode('', $wM);
        };

        function bjr($dE) {
            $bdk = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f"];
            $wM = array();
            $wM[] = $bdk[$dE >> 4 & 15];
            $wM[] = $bdk[$dE & 15];
            return implode('', $wM);
        };

        function settmusic($Hg, $bF) {
            return hex2bin(bjp(bii($Hg, $bF)));
        }
        return settmusic($Hg, $bF);
    }
    //采集歌曲
    public static function caijiSongUrl($song_id=0, $br=999000) {
        // key
        function prepare($raw)
        {
            function aes_encode($secretData, $secret)
            {
                $AES_VI='0102030405060708';
                return openssl_encrypt($secretData, 'aes-128-cbc', $secret, false, $AES_VI);
            }
            $ENCRYPT_NONCE = '0CoJUm6Qyw8W8jud';
            $secretKey='TA3YiYCfY2dDJQgg';
            $encSecKey='84ca47bca10bad09a6b04c5c927ef077d9b9f1e37098aa3eac6ea70eb59df0aa28b691b7e75e4f1f9831754919ea784c8f74fbfadf2898b0be17849fd656060162857830e241aba44991601f137624094c114ea8d17bce815b0cd4e5b8e2fbaba978c6d1d14dc3d1faf852bdd28818031ccdaaa13a6018e1024e2aae98844210';
            $data['params'] = aes_encode(json_encode($raw), $ENCRYPT_NONCE);
            $data['params'] =  aes_encode($data['params'], $secretKey);
            $data['encSecKey'] = $encSecKey;
            return $data;
        }
        /**
         * CURL 模块
         * @param  string $uri      目的地址
         * @param  string $postData POST数组
         * @param  string $cookie   携带Cookie
         * @param  string|array $header   自定义Header
         * @return string
         */
        function http_requests($uri, $postData = '', $cookie = '', $header = '') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            if ($postData) { // post提交
                if (is_array($postData)) $postData = http_build_query($postData);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            }
            if ($cookie) // 伪造cookie
                curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            if ($header) // 自定义header
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }
        $url = 'http://music.163.com/weapi/song/enhance/player/url?csrf_token=';
        $data = [
            'ids' => [$song_id],
            'br' => $br,
            'csrf_token' => '',
        ];
        $json = http_requests(
            $url,
            prepare($data),
            'os=pc; osver=Microsoft-Windows-10-Professional-build-10586-64bit; appver=2.0.3.131777; channel=netease; __remember_me=true',
            [
                'Origin: http://music.163.com',
                'X-Real-IP: 183.30.197.115',
                'Accept-Language: q=0.8,zh-CN;q=0.6,zh;q=0.2',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                'Referer: http://music.163.com/'
            ]
        );
        $json = json_decode($json, true);
        if(!isset($json['data'])) {
            return '';
        }
        $data = $json['data'];
        if(!isset($data[0]['url'])) {
            return '';
        }
        $url = $data[0]['url'];
        self::where('songid',$song_id)->update(['playurl' => $url] );
        return $url;
    }


    //采集用户的信息
    public static function caijiUserInfo($wyUid) {
        // 设置请求头
        $headers = array(
            'Accept:*/*',
            'Accept-Language:zh-CN,zh;q=0.9',
            'Connection:keep-alive',
            'Content-Type:application/x-www-form-urlencoded',
            'Host:music.163.com',
            'Origin:https://music.163.com',
            // 模拟浏览器设置 User-Agent ，否则取到的数据不完整
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36'
        );
        // 拼接歌曲的url
        $url = 'https://music.163.com/user/home?id='.$wyUid;
        $htmlInfo = File::get_https($url, 'https://music.163.com');
        if(!$htmlInfo) return '获取不到内容:'.$htmlInfo;
        $htmlInfo = Str::sp_('<script'.' type="application/ld+json">', '</script>', $htmlInfo);
        if(!$htmlInfo) return '获取不到js内容:'.$htmlInfo;
        $htmlInfo = json_decode($htmlInfo, true);
        $wyAvatar = $htmlInfo['images'][0];
        $wyNickname = $htmlInfo['title'];

//        Array
//        (
//            [@context] => https://ziyuan.baidu.com/contexts/cambrian.jsonld
//    [@id] => http://music.163.com/user/home?id=306782123
//    [appid] => 1582028769404989
//    [title] => DonDoblo
//        [images] => Array
//        (
//            [0] => http://p1.music.126.net/wXAaC_gwPmgV51e_46bJJQ==/19124905253829455.jpg
//        )
//
//    [description] => DonDoblo的最近常听、歌单、DJ节目、音乐口味、动态。
//        [pubDate] => 2016-07-19T14:45:18
//)

        if(!$wyAvatar || strlen($wyAvatar) < 10) {
            print_r($htmlInfo);
            return '获取不到头像';
        }
        SongsUser::addUserGetUri($wyUid, $wyNickname, $wyAvatar);
        $uInfo = userModel::field('id,uri,title,avatar,wyuid,rq')->where('wyuid', $wyUid)->find();
        return $uInfo;
    }

    //采集歌单的歌曲
    public static function caijiGedan($wyid) {
        // 设置请求头
        $headers = array(
            'Accept:*/*',
            'Accept-Language:zh-CN,zh;q=0.9',
            'Connection:keep-alive',
            'Content-Type:application/x-www-form-urlencoded',
            'Host:music.163.com',
            'Origin:https://music.163.com',
            // 模拟浏览器设置 User-Agent ，否则取到的数据不完整
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36'
        );
        // 拼接歌曲的url
        $url = 'https://music.163.com/weapi/v3/playlist/detail?csrf_token=';
        $first_param = '{"id":"'. $wyid .'","n":"'. 100000 .'","s":"8"}';
        $params = array('params' => self::__aesGetParams($first_param), 'encSecKey' => self::__getEncSecKey());
        $htmlInfo = self::__httpPost($url, $headers, http_build_query($params));
        $jsonData = json_decode($htmlInfo, true);
        if(!$jsonData) return '获取不到内容:'.$htmlInfo;
        if(!isset($jsonData['playlist'])) {
            return '获取不到 playlist';
        }
//        print_r(json_encode($jsonData));exit;
        $playlist = $jsonData['playlist'];
        if(!isset($playlist['tracks'])) {
            return '获取不到 playlist.tracks';
        }
        $title = $playlist['name'];
        $userId = $playlist['userId'];
        $coverImgUrl = $playlist['coverImgUrl'];
        $description = $playlist['description'];
        $tracks = $playlist['tracks'];
        $gedanSongsList = [];
        $addGedanSong = function ($item) {
            $wySongid = $item['id'];
            $songName = $item['name'];
            $artists = $item['ar'];//['id'=>'','name'=>'']
            $ourSongInfo = [
                'wySongid' => $wySongid,
                'title' => $songName,
                'singer' => $artists,
            ];
            return $ourSongInfo;
        };
        foreach ($tracks as $item) {
            $gedanSongsList[] = $addGedanSong($item);
        }
        return [
            'songList'=>$gedanSongsList,
            'userId'=> $userId,
            'coverImgUrl'=> $coverImgUrl,
            'description'=> $description,
            'title'=> $title,
        ];
    }

    //采集用户的歌单
    public static function caijiUserGedan($wyuid, $page=1) {
        // 设置请求头
        $headers = array(
            'Accept:*/*',
            'Accept-Language:zh-CN,zh;q=0.9',
            'Connection:keep-alive',
            'Content-Type:application/x-www-form-urlencoded',
            'Host:music.163.com',
            'Origin:https://music.163.com',
            // 模拟浏览器设置 User-Agent ，否则取到的数据不完整
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36'
        );
        // 拼接歌曲的url
        $url = 'https://music.163.com/weapi/user/playlist?csrf_token=';
        // 拼接加密 params 用到的第一个参数
        $pageSize = 1000;
        $offset = ($page-1) * $pageSize;
        $first_param = '{"uid":"'. $wyuid .'","offset":"'. $offset .'","total":"true","limit":"'. $pageSize .'"}';
        $params = array('params' => self::__aesGetParams($first_param), 'encSecKey' => self::__getEncSecKey());
        $htmlInfo = self::__httpPost($url, $headers, http_build_query($params));
        $jsonData = json_decode($htmlInfo, true);
        if(!$jsonData) return '获取不到内容:'.$htmlInfo;
        if(!isset($jsonData['playlist'])) {
            return '获取不到 playlist';
        }
        $playlist = $jsonData['playlist'];
        $gedanUriList = [];
        $addGedan = function ($item) use($wyuid) {
            $wyUid = $item['creator']['userId'];
            $wyUsername = $item['creator']['nickname'];
            $wyUserAvatar = $item['creator']['avatarUrl'];
            $wyid = $item['id'];
            $gedanTitle = $item['name'];
            $gedanAvatar = $item['coverImgUrl'];
            $description = $item['description'];
            $gedanCreateTime = $item['createTime'];
            return [
                'wyGedanId' => $wyid,
                'title' => $gedanTitle,
                'avatar' => $gedanAvatar,
            ];
        };
        foreach ($playlist as $item) {
            $gedanUriList[] = $addGedan($item);
        }
        //更新用户歌单总数
        $totalGedan = count($playlist);
        SongsUser::where('wyuid', $wyuid)->update([
            'gedans' => $totalGedan
        ]);
        return $gedanUriList;
    }
    //采集用户最近听歌记录
    //$type1: 最近一周, 0: 所有时间
    public static function caijiUserRecent($wyuid, $type) {
        // 设置请求头
        $headers = array(
            'Accept:*/*',
            'Accept-Language:zh-CN,zh;q=0.9',
            'Connection:keep-alive',
            'Content-Type:application/x-www-form-urlencoded',
            'Host:music.163.com',
            'Origin:https://music.163.com',
            // 模拟浏览器设置 User-Agent ，否则取到的数据不完整
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36'
        );
        // 拼接歌曲的url
        $url = 'https://music.163.com/weapi/v1/play/record?csrf_token=';

        $first_param = '{"uid":"'. $wyuid .'","type":"'. $type .'"}';
        $params = array('params' => self::__aesGetParams($first_param), 'encSecKey' => self::__getEncSecKey());
        $htmlInfo = self::__httpPost($url, $headers, http_build_query($params));
        $jsonData = json_decode($htmlInfo, true);
        if(!$jsonData) return '获取不到内容:'.$htmlInfo;
        if(!isset($jsonData['weekData'])) {
            print_r($jsonData);
            return '获取不到 weekData';
        }
        $playlist = $jsonData['weekData'];
        $songList = [];
        $addSong = function ($item) {
            $songInfo = $item['song'];
            $wySongid = $songInfo['id'];
            $songName = $songInfo['name'];
            $artists = $songInfo['ar'];
            $singerList = [];
            foreach ($artists as $v2) {
                $singerList[] = [
                    'id'=> $v2['id'],
                    'name'=> $v2['name'],
                ];
            }
            $singerList = [];
            foreach ($artists as $v2) {
                $singerList[] = [
                    'id'=> $v2['id'],
                    'name'=> $v2['name'],
                ];
            }
            if(!$ourSongInfo = self::field('uri,songid,title,singer')->where('songid', $wySongid)->find()) {
                $singerIdArray = SongsSinger::insertSingers($singerList);
                $ourSongInfo = self::insertSongs($songName, $wySongid, $singerIdArray);
            }
            return $ourSongInfo;
        };
        foreach ($playlist as $v) {
            $songList[] = $addSong($v);
        }
        return $songList;
    }

    //$a = new NeteaseMusicAPI();
    //$result = $a->url('18614850', 96000);
    //$array = json_decode($result, true);
    //var_dump($array);
}
