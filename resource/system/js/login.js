$(function(){

    var form = $('#login_form');
    //placeholder
    form.find('.input').placeholder({isUseSpan:true,onInput:false});
    //刷新验证码
    form.find('#code').click(function(e){
        $(this).attr('src',function(i,oldsrc){
            return oldsrc+'&t='+Math.random();
        })
    });
    //检测登录
    form.submit(function(e){
        hideAllBox();
        e.preventDefault();
        var uid = form.find('#nick');
        var pass = form.find('#pwd');
        var my_ans = form.find('#my_ans').val();
        var validate = form.find('#validate');
        //var loginday = form.find('#loginday').val();
        if(!uid.val() || uid.val().length < 1 ) {
            msg('请输入用户名');
            return;
        }
        if(!pass.val() || pass.val().length < 6 ) {
            msg('请输入密码 至少6位');
            return;
        }
        if(!validate.val() ) {
            msg('请输入验证码');
            return;
        }
        var loginData = {
            nick: uid.val(),
            pwd: pass.val(),
            my_ans: my_ans,
            validate: validate.val()
        };
        rePost('/?s=system&do=login',loginData,function(data) {
            hideAllBox();
            if(data.id != '0001') {
                if(data.info) data.msg += data.info;
                msg(data.msg,4);
                //刷新验证码
                if(data.id == '0002' || data.id == '0022' || data.id == '0024' || data.id == '0482' || data.id == '0486') {
                    form.find('#code').click();
                    validate.focus();
                }
                return;
            } else {
                if(data.u_hash) {
                    window.local_uid = data.u_hash;
                }

                //获取页头rurl来路
                var rurl = '';
                var url = window.location.toString().toLowerCase();
                if(url.indexOf('rurl=') !== -1 ){
                    var fromUrl = url.split('rurl=');
                    var rurls = fromUrl[1];
                    if($.trim(rurls)) {
                        rurl = $.url.decode(rurls);
                    }
                }
                //默认买家身份登录
                var gotoUrl = '/?s=user';
                if($.trim(rurl)!=''){
                    gotoUrl = rurl;
                }
                else if(data.info && data.info ==3 ) {
                    gotoUrl = '/?s=employee';
                }
                else if(data.info && (data.info ==1 || data.info ==2) ) {
                    gotoUrl = '/?s=user';
                }

                if(data.sid) {
                    gotoUrl += "&sid=" + data.sid;
                }
                msg(data.msg, 1);
                setTimeout(function() {
                    window.location = gotoUrl ;
                }, 500);
            }
        });
        msg('正在登录...');
    });
    //自动缩放背景高度为浏览器实际高度
/*    var winH = $(document).height();
    winH = winH < 700?724:winH;
    $('body').height(winH);*/
});