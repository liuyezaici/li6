<?=$header?>
<style>
    .reg_body {
        width: 600px;
        max-width: 100%;
    }
    .reg_body .form-group {
        margin-bottom: 40px;
    }
    .reg_body .reg_footer {
        font-size: 12px;
        color: #666;
        text-align: right;
        margin-top: 100px;
    }
    #reg_form ul li#hide_control a {
        color: #666;
    }
    #reg_form .hide {
        display: none;
    }
    #reg_form .default_option {

    }
    #reg_form ul li .submit_btn {
        margin-left: 108px;
        width: 201px;
        padding: 8px 0;
    }
</style>
<div class="container reg_body" id="new_reg">
    <div class="page-header">
        <h1>注册会员</h1>
    </div>
    <form class="form-horizontal" id="reg_form">
        <div class="form-group">
            <input id="inviter" type="hidden" value="<?=$inviter?>" />
            <label class="control-label col-sm-2 col-xs-3">手机号码</label>
            <div class="input-group col-sm-10 col-sm-9" style="max-width: 280px; z-index: 100;"> <!-- 为内部菜单而设置z-index 防止被下行input-group给遮挡-->
                <div id="make_tel_box" class="input-group" data-value="<?=$leftTime?>"></div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2 col-xs-3">短信验证</label>
            <div class="input-group col-sm-10 col-sm-9" style="max-width: 280px;">
                <input type="text" class="form-control" name="tel_code" maxlength="6" />
                <div class="input-group-btn">
                    <input type="submit" class="btn btn-info" value="提交注册" />
                </div>
            </div>
        </div>
    </form>
    <p class="reg_footer">
        <a class="login" onclick="loginIn($(this));" target="_self" href="javascript:;"> &gt;&gt;已有帐号,直接登录</a> &nbsp;
        | &nbsp;
        <a class="login" href="/qqlogin" target="_parent">QQ登录</a>
        | &nbsp;
        <a href="/system/forget" target="_parent"> 忘记密码? </a>
    </p>
</div>
<script src="/resource/system/js/reg.js"></script>

<?=$footer?>