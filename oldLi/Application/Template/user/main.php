<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>会员中心 - <?=$GLOBALS['cfg_webname']?></title>
    <link href="/min/?f=/resource/pub/bootstrap-3.3.7/css/bootstrap.css" rel="stylesheet" media="all" />
<!--    <link href="/min/?f=/resource/pub/css/jquery.lr_box.css" rel="stylesheet" media="all" />-->
<!--    <link href="/min/?f=/resource/pub/css/pub.css" rel="stylesheet" media="all" />-->
<!--    <link href="/min/?f=/resource/pub/css/jquery.lr_element.css" rel="stylesheet" media="all" />-->
    <link href="/include/lib/editormd/css/editormd.css" rel="stylesheet" media="all" />
    <link href="/min/?f=/resource/pub/bootstrap-3.3.7/css/bootstrap.css,/resource/pub/css/jquery.lr_box.css,/resource/pub/css/pub.css,/resource/pub/css/jquery.lr_element.css,/include/lib/webuploader/webuploader.css" rel="stylesheet" media="all" />
    <script src="/resource/pub/js/jq/jquery-3.2.1.js"></script>
    <script type="text/javascript" src="/resource/pub/bootstrap-3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/resource/pub/js/jquery-lr_element.js"></script>
    <script type="text/javascript" src="/resource/pub/js/jquery-lr_base.js"></script>
    <script type="text/javascript" src="/resource/pub/js/jquery-lr_box.js"></script>
    <script type="text/javascript" src="/include/lib/webuploader/webuploader.js"></script>
    <script type="text/javascript" src="/include/lib/editormd/editormd.js"></script> <!-- 编辑器 -->
    <link href="/resource/manage/css/user_manage.css?t=201502031354" rel="stylesheet" media="all" />
    <!-- flash upload -->
    <script src="/include/lib/uploadify/jquery.uploadify.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="/include/lib/uploadify/uploadify.css">
    <base target="_blank">
<body>
<div id="header">
    <div class="header_main">
        <a class="logo" href="/?s=user" target="_self">li6.cc <i class="ch">个人中心</i> </a>
        <div class="nav">
            <ul>
                <li>
                    <img src="<?=$u_logo?>" onclick="ajaxOpen('/?s=user/edit_my_info');" target="_self" title="编辑头像" class="userface">
                    <?=$user_nick?>  [<a href="/?s=system/logout" target="_self">退出</a>]
                </li>
                <li>
                    <a href="/?fr=user_manage" target="_self"><em></em>返回首页</a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="clearfix new_main" id="new_main">
    <div id="new_left">
        <div class="left_menu" id="left_menu">
            <dl>
                <dt>
                    我的菜单
                </dt>
                <dd>
                    <ul>
                        <li class="my_articles">
                            <em></em><a href="javascript:" onclick="ajaxOpen('/?s=uarticles');" target="_self">文章</a>
                        </li>
<!--                        <li class="fonts">-->
<!--                            <em></em><a href="javascript:" onclick="ajaxOpen('/?s=ufonts');" target="_self">字库</a>-->
<!--                        </li>-->
                        <li class="edit_info">
                            <em></em><a href="javascript:" onclick="ajaxOpen('/?s=user/edit_my_info');" target="_self">控制面板</a>
                        </li>
                    </ul>
                </dd>
            </dl>
        </div>
    </div>
    <div id="root_right" class="new_right clearfix" >
        <div class="right_content">
            <div class="user_box">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td valign="top" width="100" >
                            <div id="user_center_box" style="background-position: <?=$bg_x?>px bottom">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var local_uid = '<?=$u_id?>'; //本地uid 防止登录多ID
</script>
<script type="text/javascript" src="/resource/manage/js/user_manage.js"  ></script>
<script type="text/javascript" src="/include/lib/plupload/js/plupload.full.min.js"></script> <!-- 阿里云oss上传插件-->
<div class="footer">
    <span class="content">
    </span>
</div>
</body>
</html>