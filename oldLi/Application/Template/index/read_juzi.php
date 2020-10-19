<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?=$title?> - LR主页</title>
    <meta name="keywords" content="LR个人主页">
    <meta name="description" content="李大爷个人主页">
    <?=$header?>
    <link href="/resource/front/css/juzi.css" rel="stylesheet" media="all" /> <!--  首页 -->
<div class="juzi_body">
    <div class="juzi_info">
        <ul>
            <li>
                <em> 首页：</em> <a href="/juzi" target="_parent">返回LR主页</a>
            </li>
            <li>
                <em> 发布者：</em> <?=$author?>
            </li>
            <li>
                <em> 发布时间：</em> <?=$ctime?>
            </li>
            <li>
                <em> 浏览：</em> <?=$hit?>
            </li>
        </ul>
    </div>
    <div id="juzi_content">
        <?=$title?>
    </div>
</div>
<script>
    $(function() {
        //格式化内容 加田字簿背景
        var contentObj = $('#juzi_content');
        var content = contentObj.html();
        content = $.trim(content);
        var i,newHtml = [];
        for(i=0; i<content.length;i++){
            newHtml.push('<span class="icon">'+ content.charAt(i) + "</span>");
        }
        contentObj.html(newHtml.join(''));
    });
</script>
