<style>
<!--

#quick_login_form {
    margin: 0 7%;
}
#quick_login_form .nav li  {
    width: 50%;
    text-align: center;
}
#quick_login_form .nav li a {
    font-size: 16px;
}
#quick_login_form form {
    margin-top: 35px;
    margin-left: 10px;
    margin-right: 10px;
}
#quick_login_form form .more_li {
    padding-bottom: 10px;
    text-align: right;
    color: #ccc;
}
#quick_login_form form .more_li a {
    color: #888;
    font-size: 12px;
    padding-top: 5px;
}
-->
</style>
<div id="quick_login_form">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#pass_login_form" data-toggle="tab"> 帐号密码登录 </a>
        </li>
        <li>
            <a href="#sms_login_form" data-toggle="tab"> 短信验证码登录 </a>
        </li>
    </ul>
    <div class="tab-content">
        <form method="post" target="_self" action="?" id="pass_login_form" class="tab-pane fade in active">
            <div class="form-group form-group-lg" id="tel_box"></div>
            <div class="form-group form-group-lg" id="pwd_box"></div>
            <div class="form-group form-group-lg">
                <button type="submit" class="btn btn-primary btn-block btn-lg">登录</button>
            </div>
            <p class="more_li">  <a href="/system/forget" target="_parent">忘记密码？</a>
            </p>
        </form>
        <form method="post" target="_self" class="tab-pane fade" id="sms_login_form">
            <div class="form-group form-group-lg">
                <div class="input-group">
                    <span class="diy_input_box form-control">
                        <input class="diy_input" name="u_tel" maxlength="11" type="text" value="" placeholder="手机" tabindex="1" />
                    </span>
                    <span id="make_sms_btn" data-value="<?=$leftTime?>"></span>
                </div>
            </div>
            <div class="form-group form-group-lg">
                <input class="form-control" name="sms_code" maxlength="6" type="text" value="" placeholder="短信验证码" tabindex="2" />
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">登录</button>
            <p class="more_li"> <a href="/system/forget" target="_parent">忘记密码？</a>
            </p>
        </form>
    </div>
</div>


<script type="text/javascript">
    $(function() {
        var box = $('#quick_login_form');
        var passForm = box.find('#pass_login_form');
        //创建手机输入框
        passForm.find('#tel_box').append(makeInput({
            'class': 'form-control',
            'name': 'u_nick',
            'maxlen': '30',
            'type': 'text',
            'place': '手机/帐号/邮箱',
            'clear': true
        }));//
        //创建密码输入框
        passForm.find('#pwd_box').append(makeInput({
            'type': 'password',
            'class': 'form-control',
            'name': 'u_pwd',
            'maxlen': '30',
            'place': '密码',
            'clear': true
        }));
        //登录回调
        function successLogin(data) {
            if(data.local_uid) {
                window.local_uid = data.local_uid;
            }
            hideNewBox();
            msgTis(data.msg);
            //如果是ajax超时登录 不需要刷新页面
            if( typeof checkLogin == 'function') {
                checkLogin();
            } else {
                var winUrl =  window.location.toString();
                if(winUrl.indexOf('login') !=-1) {
                    window.location='/?s=user';
                } else {
                    window.location.reload();
                }
            }
        }
        passForm.submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var postData = form.getFormDatas();
            var uid = postData.u_nick;
            var pass = postData.u_pwd;
            if(!uid || uid.length < 1 ) {
                msg('请输入您的帐号');
                return;
            }
            if(uid.length < 2 ) {
                msg('帐号至少2位');
                return;
            }
            if(!pass || pass.length < 1 ) {
                msg('请输入您的密码');
                return;
            }
            if(pass.length < 6) {
                msg('密码至少6位');
                return;
            }
            loading();
            rePost('/?s=system&do=login&login_type=pwd', postData, function(data) {
                hideNewBox();
                if(data.id != '0001') {
                    if(data.info) data.msg += data.info;
                    msg(data.msg,4);
                } else {
                    successLogin(data);
                }
            });
        });
        //短信验证码登录
        var smsForm = box.find('#sms_login_form');
        var makeSmsBox = smsForm.find('#make_sms_btn');
        //倒计时  获取按钮
        var getSmsBtn = makeBtn({value:'获取短信', 'class': 'btn btn-default btn-lg',rest_time: makeSmsBox.attr('data-value')});
        var btnWrap = makeDiv({'class': 'input-group-btn', value: getSmsBtn});
        //手机只能输入数字
        var telObj = smsForm.find("input[name='u_tel']");
        telObj.off().on('keyup', function () {
            var tmpObj = $(this);
            var text_ = tmpObj.val();
            if(isNaN(text_)) {
                text_ = text_.replace(/[^0-9]/gi,'');
                tmpObj.val(text_);
            }
        });
        //获取验证码
        getSmsBtn.attr('data-validate', -1).off().on('click', function () {
            var btn = $(this);
            //获取验证码
            var newTel = telObj.val();
            if(!newTel) {
                msg('请输入您的手机');
                return;
            }
            var sessioncode = btn.attr('data-validate');
            if(sessioncode == '-1') {
                makeValidate(getSmsBtn, 'bottom');
                return;
            }
            msgWait('短信发送中...');
            rePost('/?s=system&do=get_phone_code_to_login&json=true', {pic_code: sessioncode, new_phone:newTel}, function(data) {
                hideNewBox();
                if(!data || !data.id) return;
                if(data.id != '0376') {
                    if(data.info) data.msg += data.info;
                    msg(data.msg);
                } else {
                    msg('发送成功，请勿将手机短信告知他人', 1);
                    getSmsBtn.subTime(60);
                }
            });
        });
        makeSmsBox.before(btnWrap).remove();//创建sms按钮
        //提交登录
        smsForm.submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var postData = form.getFormDatas();
            var uTel = postData.u_tel;
            var sms_code = postData.sms_code;
            if(!uTel || uTel.length < 11 ) {
                msg('请输入您的手机');
                return;
            }
            if(sms_code.length < 4 ) {
                msg('请输入短信验证码');
                return;
            }
            //要求每次登录必须已经验证过
            var sessioncode = getSmsBtn.attr('data-validate');
            if(sessioncode == '-1') {
                makeValidate(getSmsBtn, 'bottom');
                return;
            }
            loading();
            rePost('/?s=system&do=login&login_type=tel', postData, function(data) {
                getSmsBtn.attr('data-validate', -1);
                hideNewBox();
                if(data.id != '0001') {
                    if(data.info) data.msg += data.info;
                    msg(data.msg,4);
                } else {
                    successLogin(data);
                }
            });
        });
    });

</script>