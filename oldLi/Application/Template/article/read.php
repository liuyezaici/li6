<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?=$a_title?> - LR主页</title>
    <meta name="keywords" content="LR个人主页">
    <meta name="description" content="李大爷个人主页">
    <?=$header?>
    <link href="/resource/front/css/article.css" rel="stylesheet" media="all" /> <!--  首页 -->
    <link href="/resource/pub/css/jquery.lr_comment.css" rel="stylesheet" media="all" />
    <link rel="stylesheet" href="/include/lib/markdown/markdown.css" />
    <script src="/resource/pub/js/jq_plug/jquery-lr_comment.js"></script>
    <script src="/resource/pub/js/jq_plug/jquery-lr_editor.js"></script>
    <script src="/resource/pub/js/jq_plug/jquery.imgLazyLoading.js"></script>

<div class="article_body">
    <div class="article_title">
        <h2>
            <?=$a_title?>
        </h2>
        <div class="interact_box">
        </div>
    </div>
    <div class="article_info">
        <ul>
            <li>
                <em> 首页：</em> <a href="/" target="_parent">返回</a>
            </li>
            <li>
                <em> 发布者：</em> <?=$author?>
            </li>
            <li>
                <em> 发布时间：</em> <?=$a_addtime?>
            </li>
            <li>
                <em> 文章分类：</em> <a href="/#where=>a_typeid=<?=$a_typeid?>"><?=$typeName?></a>
            </li>
            <li>
                <em> 浏览：</em> <?=$a_hit?>
            </li>
        </ul>
    </div>
    <div id="article_content">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td valign="top">
                    <p class="article_begin">正文开始</p>
                    <div class="content_box">
                        <div class="markdown-body">
                            <?=$a_content?>
                        </div>
                    </div>
                    <p class="article_end">正文结束</p>
                </td>
            </tr>
        </table>
    </div>
    <div class="alert text-center"><p>2018.5.11首次尝试纯js首页</p><a href="http://www.beian.miit.gov.cn">粤ICP备16054687号</a></div>
</div>
<script>
    var current_articleid = '<?=$a_id?>';
    $(function() {
        //图片延迟加载
        var contentBox = $('#article_content');
        contentBox.find('.lazy').lazyload();
    });
</script>
