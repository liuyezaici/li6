<?=$header?>
<link rel="stylesheet" href="/resource/system/css/find.css" />
<div class="container reset_body">
    <div class="page-header">
        <h2>重置密码</h2>
        <div class="small pull-right other">
            <a href="javascript: void(0);" onclick="loginIn($(this));" target="_self">直接登录</a>
        </div>
    </div>
    <form class="form form-horizontal" method="post" id="email_reset_form" role="form">
        <div class="input-group" style="width: 400px;">
            <label class="input-group-addon">输入邮箱</label>
            <input class="form-control" name="email" type="text" value="" />
            <div class="input-group-btn">
                <input type="submit" value="获取邮件" class="btn btn-default" />
            </div>
        </div>
    </form>
    <form class="form form-horizontal" method="post" id="sms_reset_form" role="form">
        <div class="input-group" style="width: 400px;">
            <label class="input-group-addon">或：手机</label>
            <div id="make_tel_box" data-value="<?=$leftTime?>"></div>
        </div>
    </form>


</div>
<script src="/resource/system/js/find.js"></script>
<?=$footer?>