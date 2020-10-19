<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>LR在线调试js</title>
<meta name="keywords" content="LR个人主页">
<meta name="description" content="李大爷个人主页">
<meta name="renderer" content="webkit|ie-comp|ie-stand"><!-- 强制360用急速模式 -->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<link href="/resource/pub/bootstrap-3.3.7/css/bootstrap.css" rel="stylesheet" media="all" />
<link href="/resource/pub/css/jquery.lr_box.css" rel="stylesheet" media="all" />
<link href="/resource/pub/css/jquery.lr_element.css" rel="stylesheet" media="all" />
<script src="/resource/pub/js/jq/jquery-3.2.1.js"></script>
<script src="/resource/pub/js/jquery-lr_box.js"></script>
<script src="/resource/pub/js/jquery-lr_base.js"></script>
<script src="/resource/pub/js/jquery-lr_element.js"></script>

    <script src="/resource/pub/codemirror/5.2.0/codemirror.js"></script>
    <link rel="stylesheet" href="/resource/pub/codemirror/5.2.0/codemirror.css">
    <script src="/resource/pub/codemirror/5.2.0/htmlmixed.js"></script>
    <script src="/resource/pub/codemirror/5.2.0/css.js"></script>
    <script src="/resource/pub/codemirror/5.2.0/javascript.js"></script>
    <script src="/resource/pub/codemirror/5.2.0/xml.js"></script>
    <script src="/resource/pub/codemirror/5.2.0/closetag.js"></script>
    <script src="/resource/pub/codemirror/5.2.0/closebrackets.js"></script>
    <link href="try.css" rel="stylesheet" media="all" /> <!-- 模版样式 -->
<?php
$fileName = isset($_GET['filename']) ? trim( $_GET['filename']) : 'welcome';
//echo $fileName. '.html';exit;
if(!file_exists($fileName. '.html')) $fileName = 'welcome';
$fileName = $fileName . '.html';
$fileContent = file_get_contents($fileName);
?>
<base target="_blank">
</head>
<body>
<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="/" title="回首页" target="_parent"><img class="logo" src="/resource/front/images/logo.png" alt="logo" /></a>
        </div>
    </div>
</nav>
<div class="container main_wrap">
    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-6">
                            <button class="btn btn-default" type="button">源代码：</button>
                        </div>
                        <div class="col-xs-6 text-right">
                            <button type="button" class="btn btn-success" onclick="submitTryit()" id="submitBTN"><span class="glyphicon glyphicon-send"></span> 点击运行</button>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <textarea class="form-control"  id="textareaCode" name="textareaCode"><?=$fileContent?></textarea>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <button class="btn btn-default" type="button">运行结果：</button>
                </div>
                <div class="panel-body">
                    <div id="iframe_wrapper"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var mixedMode = {
        name: "htmlmixed",
        scriptTypes: [{matches: /\/x-handlebars-template|\/x-mustache/i,
            mode: null},
            {matches: /(text|application)\/(x-)?vb(a|script)/i,
                mode: "vbscript"}]
    };
    var editor = CodeMirror.fromTextArea(document.getElementById("textareaCode"), {
        mode: mixedMode,
        selectionPointer: true,
        lineNumbers: false,
        matchBrackets: true,
        indentUnit: 4,
        indentWithTabs: true
    });

    window.addEventListener("resize", autodivheight);

    var x = 0;
    function autodivheight(){
        var winHeight=0;
        if (window.innerHeight) {
            winHeight = window.innerHeight;
        } else if ((document.body) && (document.body.clientHeight)) {
            winHeight = document.body.clientHeight;
        }
        //通过深入Document内部对body进行检测，获取浏览器窗口高度
        if (document.documentElement && document.documentElement.clientHeight) {
            winHeight = document.documentElement.clientHeight;
        }
        height = winHeight*0.68
        editor.setSize('100%', height);
        document.getElementById("iframeResult").style.height= height +"px";
    }

    function submitTryit() {
        var text = editor.getValue();
        var patternHtml = /<html[^>]*>((.|[\n\r])*)<\/html>/im
        var patternHead = /<head[^>]*>((.|[\n\r])*)<\/head>/im
        var array_matches_head = patternHead.exec(text);
        var patternBody = /<body[^>]*>((.|[\n\r])*)<\/body>/im;

        var array_matches_body = patternBody.exec(text);
        var basepath_flag = 1;
        var basepath = '';
        if(basepath_flag) {
            basepath = '<base target="_blank">';
        }
        if(array_matches_head) {
            text = text.replace('<head>', '<head>' + basepath );
        } else if (patternHtml) {
            text = text.replace('<html>', '<head>' + basepath + '</head>');
        } else if (array_matches_body) {
            text = text.replace('<body>', '<body>' + basepath );
        } else {
            text = basepath + text;
        }
        var ifr = document.createElement("iframe");
        ifr.setAttribute("frameborder", "0");
        ifr.setAttribute("id", "iframeResult");
        document.getElementById("iframe_wrapper").innerHTML = "";
        document.getElementById("iframe_wrapper").appendChild(ifr);

        var ifrw = (ifr.contentWindow) ? ifr.contentWindow : (ifr.contentDocument.document) ? ifr.contentDocument.document : ifr.contentDocument;
        ifrw.document.open();
        ifrw.document.write(text);
        ifrw.document.close();
        autodivheight();
    }
    submitTryit();
    autodivheight();
</script>