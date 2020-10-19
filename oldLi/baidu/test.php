<?php
/**
* Pandownload复刻版
*
* 功能描述：使用百度SVIP账号获取真实下载地址，与Pandownload原版无关
*
* 使用的时候请保留一下作者信息呀（就是菜单栏的Made by Yuan_Tuo），谢~
*
* 有的注释不是很完整，见谅~
*
* @author Yuan_Tuo <yuantuo666@gmail.com>
* @version 1.0
* @link https://imwcr.cn/
* @link https://space.bilibili.com/88197958
*/

define("BDUSS", "jBZSXQtN3l2RmFvSEFmcnpyYXZqWDJWcTIxdm0yN1R2cmJPdVZUODkydG1IMXhmRVFBQUFBJCQAAAAAAAAAAAEAAABbFOfjtKXK1m5iAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAGaSNF9mkjRfR");
define("STOKEN", "dcc9cbab32a0fe15dbafc60a3542e8f838d7a880829d9c855465816adbc12cb5");

function post($url, $data, array $headerArray)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); //忽略ssl
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
//function get($url, array $headerArray)
//{
//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_URL, $url);
//    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //忽略ssl
//    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//    curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
//    $output = curl_exec($ch);
//    curl_close($ch);
//    return $output;
//}
function get($url, array $headerArray)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headerArray);
    curl_setopt($ch, CURLOPT_HEADER, 0);//get cookies
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//
    //----
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    //$contents = curl_exec($ch);
    ob_start();
    curl_exec($ch);
    $contents = ob_get_contents();
    ob_end_clean();
    curl_close($ch);
    return $contents;
}
function head($url, array $headerArray)
{
    // curl 获取响应头
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //忽略ssl
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出
    curl_setopt($ch, CURLOPT_HEADER, true); // 返回 response header 默认 false 只会获得响应的正文
    curl_setopt($ch, CURLOPT_NOBODY, true); // 有时候为了节省带宽及时间，只需要响应头
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE); // 获得响应头大小
    $header = substr($response, 0, $header_size); // 根据头大小获取头信息
    curl_close($ch);
    return $header;
}
function getSubstr($str, $leftStr, $rightStr)
{
    $left = strpos($str, $leftStr);
    //echo '左边:'.$left;
    $right = strpos($str, $rightStr, $left);
    //echo '<br>右边:'.$right;
    if ($left < 0 or $right < $left) return '';
    return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
}
//格式化size显示
function formatSize($b, $times = 0)
{
    if ($b > 1024) {
        $temp = $b / 1024;
        return formatSize($temp, $times + 1);
    } else {
        $unit = 'B';
        switch ($times) {
            case '0':
                $unit = 'B';
                break;
            case '1':
                $unit = 'KB';
                break;
            case '2':
                $unit = 'MB';
                break;
            case '3':
                $unit = 'GB';
                break;
            case '4':
                $unit = 'TB';
                break;
            case '5':
                $unit = 'PB';
                break;
            case '6':
                $unit = 'EB';
                break;
            case '7':
                $unit = 'ZB';
                break;
            default:
                $unit = '单位未知';
        }
        return sprintf('%.2f', $b) . $unit;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Yuan_Tuo" />
    <meta name="description" content="PanDownload网页版,百度网盘分享链接在线解析工具" />
    <meta name="keywords" content="PanDownload,百度网盘,分享链接,下载,不限速" />
    <link rel="icon" href="https://pandownload.com/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/css/bootstrap.min.css">
    <script src="https://cdn.staticfile.org/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/popper.js/1.12.5/umd/popper.min.js"></script>
    <script src="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/js/bootstrap.min.js"></script>

    <!-- 可以异步 -->
    <link href="https://cdn.staticfile.org/font-awesome/5.8.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.staticfile.org/bootstrap-sweetalert/1.0.1/sweetalert.min.js"></script>
    <link href="https://cdn.staticfile.org/bootstrap-sweetalert/1.0.1/sweetalert.min.css" rel="stylesheet">

    <style>
        body {
            background-image: url("https://pandownload.com/img/baiduwp/bg.png");
        }

        .logo-img {
            width: 1.1em;
            position: relative;
            top: -3px;
        }
    </style>
    <title>PanDownload复刻版</title>
    <style>
        .form-inline input {
            width: 500px;
        }

        .input-card {
            position: relative;
            top: 7.0em;
        }

        .card-header {
            height: 3.2em;
            font-size: 20px;
            line-height: 2.0em;
        }

        form input,
        form button {
            height: 3em;
        }

        .alert {
            position: relative;
            top: 5em;
        }

        .alert-heading {
            height: 0.8em;
        }
    </style>
    <script>
        function validateForm() {
            var link = document.forms["form1"]["surl"].value;
            if (link == null || link == "") {
                document.forms["form1"]["surl"].focus();
                return false;
            }
            var uk = link.match(/uk=(\d+)/);
            var shareid = link.match(/shareid=(\d+)/);
            if (uk != null && shareid != null) {
                document.forms["form1"]["surl"].value = "";
                $("form").append('<input type="hidden" name="uk" value="' + uk[1] + '">');
                $("form").append('<input type="hidden" name="shareid" value="' + shareid[1] + '">');
                return true;
            }
            var surl = link.match(/surl=([A-Za-z0-9-_]+)/);
            if (surl == null) {
                surl = link.match(/1[A-Za-z0-9-_]+/);
                if (surl == null) {
                    document.forms["form1"]["surl"].focus();
                    return false;
                } else {
                    surl = surl[0];
                }
            } else {
                surl = "1" + surl[1];
            }
            document.forms["form1"]["surl"].value = surl;
            return true;
        }

        function dl(fs_id, timestamp, sign, randsk, share_id, uk) {
            var form = $('<form method="post" action="./?download" target="_blank"></form>');
            form.append('<input type="hidden" name="fs_id" value="' + fs_id + '">');
            form.append('<input type="hidden" name="time" value="' + timestamp + '">');
            form.append('<input type="hidden" name="sign" value="' + sign + '">');
            form.append('<input type="hidden" name="randsk" value="' + randsk + '">');
            form.append('<input type="hidden" name="share_id" value="' + share_id + '">');
            form.append('<input type="hidden" name="uk" value="' + uk + '">');
            $(document.body).append(form);
            form.submit();
        }

        function getIconClass(filename) {
            var filetype = {
                file_video: ["wmv", "rmvb", "mpeg4", "mpeg2", "flv", "avi", "3gp", "mpga", "qt", "rm", "wmz", "wmd", "wvx", "wmx", "wm", "mpg", "mp4", "mkv", "mpeg", "mov", "asf", "m4v", "m3u8", "swf"],
                file_audio: ["wma", "wav", "mp3", "aac", "ra", "ram", "mp2", "ogg", "aif", "mpega", "amr", "mid", "midi", "m4a", "flac"],
                file_image: ["jpg", "jpeg", "gif", "bmp", "png", "jpe", "cur", "svgz", "ico"],
                file_archive: ["rar", "zip", "7z", "iso"],
                windows: ["exe"],
                apple: ["ipa"],
                android: ["apk"],
                file_alt: ["txt", "rtf"],
                file_excel: ["xls", "xlsx"],
                file_word: ["doc", "docx"],
                file_powerpoint: ["ppt", "pptx"],
                file_pdf: ["pdf"],
            };
            var point = filename.lastIndexOf(".");
            var t = filename.substr(point + 1);
            if (t == "") {
                return "";
            }
            t = t.toLowerCase();
            for (var icon in filetype) {
                for (var type in filetype[icon]) {
                    if (t == filetype[icon][type]) {
                        return "fa-" + icon.replace('_', '-');
                    }
                }
            }
            return "";
        }
        $(document).ready(function() {
            $(".fa-file").each(function() {
                var icon = getIconClass($(this).next().text());
                if (icon != "") {
                    if (icon == "fa-windows" || icon == "fa-android" || icon == "fa-apple") {
                        $(this).removeClass("far").addClass("fab");
                    }
                    $(this).removeClass("fa-file").addClass(icon);
                }
            });
        });
    </script>
</head>

<body>
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="./">
                <img src="https://pandownload.com/img/baiduwp/logo.png" class="img-fluid rounded logo-img mr-2" alt="LOGO">PanDownload
            </a>
            <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#collpase-bar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collpase-bar">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="./">主页</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://pandownload.com/" target="_blank">网盘下载器</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://imwcr.cn/" target="_blank">Made by pantabang</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">

        <?php
        //开始判断
         if (isset($_POST["surl"])) {

            $surl = $_POST["surl"];
            $surl_1 = substr($surl, 1);

            function verifyPwd($surl_1, $pwd)
            { //验证密码 
                $url = 'https://pan.baidu.com/share/verify?channel=chunlei&clienttype=0&web=1&app_id=250528&surl=' . $surl_1;
                $data = "pwd=$pwd";
                $headerArray = array("user-agent:netdisk", "Referer:https://pan.baidu.com/disk/home");
                $json1 = post($url, $data, $headerArray);
                $json1 = json_decode($json1, true);
                //-12验证码错误
                if ($json1["errno"] == 0) {
                    return $json1["randsk"];
                } else {
                    return 1;
                }
            }

            function getSign($surl, $randsk)
            {
                if ($randsk == 1) {
                    return 1;
                }
                $url = 'https://pan.baidu.com/s/' . $surl;
                $headerArray = array(
                    "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
                    "Cookie:BDUSS=" . constant("BDUSS") . ";STOKEN=" . constant("STOKEN") . ";BDCLND=" . $randsk . ";"
                );
                $json2 = get($url, $headerArray);
                $re = '/yunData.setData\(({.+)\);/';
                $re = '/yunData.setData\(\{(.*)?\}\);/';
                if (preg_match($re, $json2, $matches)) {
                    $json2 = $matches[0];
                    $json2 = substr($json2, 16, -2);
                    $json2 = json_decode($json2, true);
                    return $json2;
                } else {
                    return 1;
                }
            }

            function getFileList($shareid, $uk, $randsk)
            {
                $url = 'https://pan.baidu.com/share/list?app_id=250528&channel=chunlei&clienttype=0&desc=0&num=100&order=name&page=1&root=1&shareid=' . $shareid . '&showempty=0&uk=' . $uk . '&web=1';
                $headerArray = array(
                    "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
                    "Cookie:BDUSS=" . constant("BDUSS") . ";STOKEN=" . constant("STOKEN") . ";BDCLND=" . $randsk . ";",
                    "Referer:https://pan.baidu.com/disk/home"
                );
                $json3 = get($url, $headerArray);
                $json3 = json_decode($json3, true);
                return $json3;
            }
             $pwd = '';
             $randsk = '';
//            if ($pwd != "") {
//                $randsk = verifyPwd($surl_1, $pwd);
//            } else {
//                $randsk = "";
//            }

            $json2 = getSign($surl, $randsk);
             print_r($json2);exit;

            if ($json2 != 1) {
                $sign = $json2["sign"];
                $timestamp = $json2["timestamp"];
                $shareid = $json2["shareid"];
                $uk = $json2["uk"];
                $filejson = getFileList($shareid, $uk, $randsk);
                if ($filejson["errno"] == -21) {
                    //链接失效
                    echo '<div class="row justify-content-center">
                    <div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
                    <h5 class="alert-heading">链接不存在</h5>
                    <hr>
                    <p class="card-text">此链接分享内容可能被取消或因涉及侵权、色情、反动、低俗等信息，无法访问！</p>
                    </div></div></div>';
                } else {
                    //var_dump($filejson);
                    $filecontent = '<ol class="breadcrumb my-4">
                文件列表(' . count($filejson["list"]) . ') </ol>
                <div>
                <ul class="list-group ">';
                    for ($i = 0; $i < count($filejson["list"]); $i++) {
                        $file = $filejson["list"][$i];
                        if ($file["isdir"] == 0) {
                            $filecontent .= '<li class="list-group-item border-muted rounded text-muted py-2">
                        <i class="far fa-file mr-2"></i>
                        <a href="javascript:void(0)" onclick="dl(\'' . $file["fs_id"] . '\',' . $timestamp . ',\'' . $sign . '\',\'' . $randsk . '\',\'' . $shareid . '\',\'' . $uk . '\')">' . $file["server_filename"] . '</a>
                        <span class="float-right">' . formatSize($file["size"]) . '</span>
                        </li>';
                        } else {
                            $filecontent .= '<li class="list-group-item border-muted rounded text-muted py-2">
                    <i class="far fa-folder mr-2"></i>
                    <a href="javascript:void(0)" onclick="sweetAlert(\'Sorry~\',\'暂不支持文件夹下载\r\n你可以转存到自己网盘、分享后重试\',\'error\')">' . $file["server_filename"] . '</a>
                    <span class="float-right"></span>
                    </li>';
                        }
                    }
                    $filecontent .= "</ul>";
                    echo $filecontent;
                }
            } else {
                echo '<div class="row justify-content-center">
                <div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
                <h5 class="alert-heading">提示</h5>
                <hr>
                <p class="card-text">提取码错误或文件失效</p>
                </div></div></div>';
            }

        ?>

        <?php } elseif (isset($_GET["download"])) {
            $fs_id = $_GET["fs_id"];
            $timestamp = $_GET["time"];
            $sign = $_GET["sign"];
            $randsk = $_GET["randsk"];
            $share_id = $_GET["share_id"];
            $uk = $_GET["uk"];
            function getDlink($fs_id, $timestamp, $sign, $randsk, $share_id, $uk)
            {
                $postdata = "";
                $postdata .= "encrypt=0";
                $postdata .= "&extra=" . urlencode("{\"sekey\":\"" . urldecode($randsk) . "\"}"); //被这个转义坑惨了QAQ
                $postdata .= "&fid_list=[$fs_id]";
                $postdata .= "&primaryid=$share_id";
                $postdata .= "&uk=$uk";
                $postdata .= "&product=share";
                $postdata .= "&type=nolimit";
                $url = 'https://pan.baidu.com/api/sharedownload?app_id=250528&channel=chunlei&clienttype=12&sign=' . $sign . '&timestamp=' . $timestamp . '&web=1';

                $headerArray = array(
                    "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
                    "Cookie:BDUSS=" . constant("BDUSS") . ";STOKEN=" . constant("STOKEN") . ";BDCLND=" . $randsk . ";",
                    "Referer:https://pan.baidu.com/disk/home"
                );

                $res3 = post($url, $postdata, $headerArray);
                $res3 = json_decode($res3, true);

                //var_dump($postdata, $res3);

                //没有referer就112，然后没有sekey参数就118  -20？？？
                // 参数	                类型	    描述
                // list	                json array	文件信息列表
                // names	            json	    如果查询共享目录，该字段为共享目录文件上传者的uk和账户名称
                // list[0]["category"]	int	        文件类型
                // list[0]["dlink”]	    string	    文件下载地址
                // list[0]["file_name”]	string	    文件名
                // list[0]["isdir”]	    int	        是否是目录
                // list[0]["server_ctime”]	int	    文件的服务器创建时间
                // list[0]["server_mtime”]	int	    文件的服务修改时间
                // list[0]["size”]	    int	        文件大小
                // list[0]["thumbs”]		        缩略图地址
                // list[0]["height”]	int	        图片高度
                // list[0]["width”]	    int	        图片宽度
                // list[0]["date_taken”]int	        图片拍摄时间
                return $res3;
            }
            $json4 = getDlink($fs_id, $timestamp, $sign, $randsk, $share_id, $uk);

            if ($json4["errno"] == 0) {
                $dlink = $json4["list"][0]["dlink"];
                $md5 = $json4["list"][0]["md5"];
                $filename = $json4["list"][0]["server_filename"];
                $size = $json4["list"][0]["size"];
                $server_ctime = (int)$json4["list"][0]["server_ctime"] + 28800; //服务器创建时间 +8:00
                $headerArray = array(
                    'User-Agent:LogStatistic',
                    'Cookie:BDUSS=' . constant("BDUSS") . ';'
                );
                $getRealLink = head($dlink, $headerArray); //禁止重定向
                $getRealLink = strstr($getRealLink, "Location");
                $getRealLink = substr($getRealLink, 10);
                $realLink = getSubstr($getRealLink, "http://", "\r\n"); //除掉http://
                // 3. 使用dlink下载文件
                // 4. dlink有效期为8小时
                // 5. 必需要设置User-Agent字段
                // 6. dlink存在302跳转
                echo '<div class="row justify-content-center">
                <div class="col-md-7 col-sm-8 col-11">
                <div class="alert alert-primary" role="alert">
                <h5 class="alert-heading">获取下载链接成功</h5>
                <hr>
                <p class="card-text">文件名: <b>' . $filename . '</b></p>
                <p class="card-text">文件大小: <b>' . formatSize($size) . '</b></p>
                <p class="card-text">文件MD5: <b>' . $md5 . '</b></p>
                <p class="card-text">上传时间: <b>' . date("Y年m月d日 H:i:s", $server_ctime) . '</b></p>
                <p class="card-text"><a href="http://' . $realLink . '" target=_blank>下载链接(http)</a>
                <a href="https://' . $realLink . '" target=_blank>下载链接(https)</a></p>
                <p class="card-text"><a href="?help" target=_blank>下载链接使用方法（必读）</a></p>
                </div></div></div>';
            } else {
                echo '<div class="row justify-content-center">
                <div class="col-md-7 col-sm-8 col-11">
                <div class="alert alert-danger" role="alert">
                    <h5 class="alert-heading">获取下载链接失败</h5>
                    <hr>
                    <p class="card-text">未知错误</p>
                    <p class="card-text">链接十分钟有效</p>
                    </div></div></div>';
            }
        ?>

        <?php } else { ?>
            <div class="col-lg-6 col-md-9 mx-auto mb-5 input-card">
                <div class="card">
                    <div class="card-header bg-dark text-light">分享链接在线解析</div>
                    <div class="card-body">
                        <form name="form1" method="post" action="./test.php" onsubmit="return validateForm()">
                            <div class="form-group my-2">
                                <input type="text" class="form-control" name="surl" placeholder="分享链接">
                            </div>
                            <div class="form-group my-4">
                                <input type="text" class="form-control" name="pwd" placeholder="提取码">
                            </div>

                            <button type="submit" class="mt-4 mb-3 form-control btn btn-success btn-block">打开</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</body>

</html>