//默认未检测用户账号
var v_checkEmail = 1;
//添加所有事件
function addEven() {
    var wrap = $('#email_reg_form');
    //注册提交事件
    wrap.submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var u_email = $.trim(form.find('#u_email').val());
        var pwd = form.find('#pwd').val();
        var rpwd = form.find('#rpwd').val();
        var inviter = parseInt(form.find('#inviter').val());
        var validate = $.trim(form.find('#validate').val());
        var take_checkbox = form.find('#take_checkbox').attr('checked');
        if(take_checkbox != "checked"){
            msg('请先确同意条款');
            return ;
        }
        if( u_email.length < 5 ) {
            msg('邮箱至少5个字');
            return;
        }
        if( u_email.indexOf('@') == -1 ) {
            msg('邮箱格式不正确');
            return;
        }
        if(!pwd || pwd.length < 6  ) {
            msg('密码至少要6位数');
            return;
        }
        if(!rpwd || rpwd.length < 6  ) {
            msg('新密码至少要6位数');
            return;
        }
        if(pwd !== rpwd) {
            msg('您两次密码输入不一致');
            return;
        }
        if(!validate || validate.length < 1 ) {
            msg('请输入您收到的邮箱验证码');
            return;
        }
        if( v_checkEmail == 1 ){
            wrap.find('#u_email').blur();
            msg('请先检测邮箱是否注册');
            return;
        }
        var regDate = {
            u_email: u_email,
            pwd: pwd,
            rpwd: rpwd,
            inviter: inviter,
            email_code: validate,
            u_type: 1 //默认注册买家
        }
        rePost('/?s=system&do=email_reg', regDate, function(data){
            hideNewBox();
            if(data.id != '0101') {
                if(data.info) data.msg += data.info;
                msg(data.msg);
                if(data.id == '0022') {
                    renew_code();
                }
                return;
            } else {
                msg(data.msg,1);
                //更新已经填写的信息
                form.find('#u_email').val('');
                form.find('#pwd').val('');
                form.find('#rpwd').val('');
                //设置登录后跳转到我的个人中心
                var  gotoUrl = '/?s=user';
                setTimeout(function(){
                    window.location.assign("/?s=system&rurl="+ encodeURIComponent(gotoUrl))
                },2000);
                return;
            }
        });
        msgWait();
    });
    //验证用户名是否注册
    wrap.find('#u_email').keyup(function(){
        wrap.find('#hint_email').html('');
    }).blur(function(){
        var u_email = $(this).val();
        if(u_email.length > 1 ){
            if( u_email.indexOf('@') == -1 ) {
                //wrap.find('#hint_email').html("✘ 邮箱格式不正确").css('color','#ff0000');
                //return;
            }
            rePost('/?s=system&do=check_email', { u_email : u_email }, function(data){
                if(data.id == '0502') {
                    v_checkEmail = 1;
                    wrap.find('#hint_email').html("✘ "+data.info).css('color','#ff0000');
                } else if( data.id != '0099' ){
                    v_checkEmail = 1;
                    wrap.find('#hint_email').html("✘ 邮箱已被注册").css('color','#ff0000');
                } else {
                    v_checkEmail = 0;
                    wrap.find('#hint_email').html("✔ 邮箱可以注册").css('color','#3C9900');
                }
            });
        }
    });
    //获取邮箱验证码
    wrap.find('#get_code_btn').click(function(){
        var u_email = $.trim(wrap.find('#u_email').val());
        if( u_email.indexOf('@') == -1 ) {
            msg('邮箱格式不正确');
            return;
        }
        rePost('/?s=system&do=get_email_code_for_reg',{email: u_email},function(data) {
            hideNewBox();
            if(data.id != '0092') {
                if(data.info) data.msg += data.info;
                msg(data.msg, 4);
                return;
            } else {
                var emailArray = u_email.split('@');
                var mailStr = emailArray[1];
                var usuallyEmail = ['outlook.com'];
                if($.inArray(mailStr, usuallyEmail) >= 0) { //奇葩邮箱 直接www开头
                    var emailUrl = 'http://www.'+ emailArray[1];
                } else {
                    var emailUrl = 'http://mail.'+ emailArray[1];
                }

                msg("邮件已发送，<a href='"+ emailUrl +"' target='_blank' style='color: #0000ff;text-decoration: underline; '>立即登录邮箱，查看验证码。</a>", 6);
            }
        });
        msgWait('邮件发送中');
    })
}
$(function(){
    addEven(); //添加所有事件
});