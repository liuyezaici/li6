

//添加所有事件
$(function()
{
    var wrap = $('#reg_form');
    var makeTelBox = wrap.find('#make_tel_box');
    //注册提交事件
    wrap.submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var u_tel = $.trim(form.find("input[name='u_tel']").val());
        var u_nick = $.trim(form.find("input[name='u_nick']").val());
        var code = $.trim(form.find('#tel_code').val());
        var pwd = form.find('#u_pwd').val();
        var inviter = parseInt(form.find('#inviter').val());
        if(!u_tel || u_tel.length < 11  ) {
            msg('请输入正确手机号');
            return;
        }
        if(!code) {
            msg('请输入短息验证码');
            return;
        }
        if(!pwd || pwd.length < 6  ) {
            msg('密码至少要6位数');
            return;
        }
        var sessioncode = form.find(".get_sms_btn").attr('data-validate');
        if(sessioncode == '-1') {
            makeValidate(getSmsBtn, 'top');
            return;
        }
        var regDate = {
            u_tel: u_tel,
            sms_code: code,
            u_nick: u_nick,
            pwd: pwd,
            inviter: inviter
        }
        rePost("/?s=system&do=reg", regDate, function(data){
            hideNewBox();
            if(data.id != '0001') {
                if(data.info) data.msg += data.info;
                msg(data.msg);
            } else {
                msg('注册成功,正在进行自动登录...',1);
                //设置登录后跳转到我的个人中心
                var  gotoUrl = '/?s=user';
                setTimeout(function(){
                    window.location=gotoUrl;
                }, 2000);
            }
        });
        loading();
    });
    //倒计时  获取按钮
    var getSmsBtn = makeBtn({'class': 'btn btn-default get_sms_btn',value:'获取短信', rest_time: makeTelBox.attr('data-value')});
    var btnWrap = makeDiv({'class': 'input-group-btn', value: getSmsBtn});
    getSmsBtn.attr('data-validate', -1).click(function () {
        //获取验证码
        var newTel = wrap.find("[name='u_tel']").val();
        if(!newTel) {
            msg('请输入您的手机');
            return;
        }
        var sessioncode = getSmsBtn.attr('data-validate');
        if(sessioncode == '-1') {
            makeValidate(getSmsBtn, 'bottom');
            return;
        }
        msgWait('短信发送中...');
        rePost('/?s=system&do=get_phone_code_to_reg&json=true', {pic_code: sessioncode, new_phone:newTel},function(data) {
            hideNewBox();
            if(!data || !data.id) return;
            if(data.id == '0375') {
                setTimeout(function() {window.location.reload();}, 1000);
                return;
            }
            if(data.id != '0376') {
                if(data.info) data.msg += data.info;
                msg(data.msg); 
            } else {
                msgTis('发送成功，请勿将手机短信告知他人');
                getSmsBtn.subTime(60);
            }
        });
    });
    //按钮加bootstrap包裹
    //创建手机
    makeTelBox.append(makeInput({name:'u_tel', 'class': 'form-control', type:'text', maxlen: 11, limit:'int',
        ajax_menu: {url:'/?s=system&do=check_new_tel',post_name:'new_tel', 'key_len_check': 11,'get_menu_val': false, value_key:'id',menu_text:'{result}'}}))
        .append(btnWrap);//创建获取按钮;
 
    //创建帐号
    var unickBox = wrap.find('#make_unick_box');
    unickBox.append(makeInput({name:'u_nick', 'value': unickBox.attr('data-value'),type:'text',
        ajax_menu: {url:'/?s=system/check_nick',post_name:'nick',width:'140','get_menu_val': false,value_key:'id',menu_text:'{result}'}}));
    //显示和隐藏帐号和密码选项
    var hideControl = wrap.find('#hide_control');
    hideControl.find('a').click(function () {
        wrap.find('.default_option').toggleClass('hide');
    });

});