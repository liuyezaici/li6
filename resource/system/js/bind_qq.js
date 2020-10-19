$(document).ready(function(){
    //检测提交
    $('#bind_form').submit(function(e){
        e.preventDefault();
        var form = $(this);
        var uid = form.find('#nick');
        var pass = form.find('#pwd');
        var openid = form.find('#openid').val();
        if(!uid.val() || uid.val().length < 1 ) {
            uid.val('').focus();
            msg('请输入用户名',4);
            return;
        }
        if(!pass.val() || pass.val().length < 1 ) {
            pass.val('').focus();
            msg('请输入密码',4);
            return;
        }
        if(!openid || openid.length < 1 ) {
            msg('缺少QQ绑定安全码',4);
            return;
        }

        var lognData = {
            nick: uid.val(),
            pwd: pass.val(),
            openid: openid
        };
        post('/?s=system&do=bind_qq',lognData,function(data) {
            if(data.id != '0253') {
                msg(data.msg,4);
                if(data.id == '0022') {
                    renew_code();
                }
                return;
            } else {
                msg(data.msg,1);
                window.location = '/' ;
                return;
            }
        });
    });
    setTimeout(function(){
        //首次去input样式
        if($('#nick').val().length > 0) $('#nick').addClass('uid_active').val('');
        if($('#pwd').val().length > 0)  $('#pwd').addClass('pwd_active').val('');
    },100);
});