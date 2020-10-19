

//添加所有事件
$(function()
{
    var wrap = $('#finish_form');
    //注册提交事件
    wrap.submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var u_tel = $.trim(form.find("input[name='u_tel']").val());
        var u_nick_hash = $.trim(form.find("#u_nick_hash").val());
        var smsCode = $.trim(form.find("input[name='tel_code']").val());
        if(!u_tel || u_tel.length < 11  ) {
            msg('请输入正确手机号');
            return;
        }
        if(!smsCode) {
            msg('请输入短息验证码');
            return;
        }
        var regDate = {
            u_tel: u_tel,
            sms_code: smsCode,
            u_nick_hash: u_nick_hash
        }
        rePost("/?s=system&do=submit_finish_tel", regDate, function(data){
            hideNewBox();
            if(data.id != '0043') {
                if(data.info) data.msg += data.info;
                msg(data.msg);
            } else {
                msg('手机绑定成功！',1);
                //设置登录后跳转到我的个人中心
                var  gotoUrl = '/?s=user';
                setTimeout(function(){
                    window.location=gotoUrl;
                }, 1000);
            }
        });
        msgWait('提交中,请稍等');
    });
    var enterCodeBox = wrap.find('#enter_code_box');
    var enterSmsBox = wrap.find('#make_sms_box');
    //倒计时  获取按钮
    var getMsnBtn = makeBtn({value:'获取短信', rest_time: enterCodeBox.attr('data-value')});
    getMsnBtn.attr('data-validate', -1).click(function () {
        //获取验证码
        var newTel = wrap.find("[name='u_tel']").val();
        if(!newTel) {
            msg('请输入您的手机');
            return;
        }
        var sessioncode = getMsnBtn.attr('data-validate');
        if(sessioncode == '-1') {
            makeValidate(getMsnBtn, 'top');
            return;
        }
        rePost('/?s=system&do=get_phone_code_to_reg&json=true', {pic_code: sessioncode, new_phone:newTel},function(data) {
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
                getMsnBtn.subTime(60);
            }
        });
    });
    //创建手机 输入框
    wrap.find('#make_tel_box').append(makeInput({name:'u_tel', type:'text',width: 160, maxlen: 11, limit:'int',
        ajax_menu: {url:'/?s=system/check_new_tel',post_name:'new_tel',width:'200','key_len_check': 11,'get_menu_val': false, value_key:'id',menu_text:'{result}'}}))
        .append(getMsnBtn);//创建获取按钮;
    //创建短信验证码 输入框
    enterSmsBox.append(makeInput({name:'tel_code', type:'text', width: 160, maxlen: 4, limit:'int'})).append(makeSpan({value: ' *', 'class': 'red'}));
    
});