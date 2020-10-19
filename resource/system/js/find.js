//邮箱重置密码事件
function emailEven() {
    var form = $('#email_reset_form');
    var email = form.find("input[name='email']");
    form.submit(function(e) {
        e.preventDefault();
        if(!email.val() || email.val().length < 1 ) {
            msg('邮箱不能空');
            return;
        }
        if (! (/(\,|^)([\w+._]+@\w+\.(\w+\.){0,3}\w{2,4})/).test(email.val().replace(/-|\//g, ''))) {
            msg('邮箱格式不对');
            return;
        }
        rePost('/?s=system&do=email_reset_password',{email: email.val()},function(data) {
            hideAllBox();
            if(data.id != '0092') {
                if(data.info) data.msg += data.info;
                msg(data.msg,4);
                if(data.id == '0022') {
                    form.find('#code_img').click();
                }
            } else {
                msg(data.msg,6);
            }
        });
        msgWait('邮件发送中');
    });


}
//短信重置密码事件
function smsEven() {
    var form = $('#sms_reset_form');
    var makeTelBox = form.find('#make_tel_box');
    //倒计时  获取按钮
    var getSmsBtn = makeBtn({'class': 'btn btn-default get_sms_btn',value:'获取短信', rest_time: makeTelBox.attr('data-value')});
    var btnWrap = makeDiv({'class': 'input-group-btn', value: getSmsBtn});
    getSmsBtn.attr('data-validate', -1).click(function () {
        //获取验证码
        var newTel = form.find("[name='u_tel']").val();
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
        rePost('/?s=system&do=phone_reset_password&json=true', {code: sessioncode, phone: newTel},function(data) {
            getSmsBtn.attr('data-validate', -1);
            hideAllBox();
            if(data.id != '0092') {
                if(data.info) data.msg += data.info;
                msg(data.msg,4);
                if(data.id == '0022') {
                    form.find('#code_img').click();
                }
            } else {
                msg(data.msg,6);
            }
        });
    });
    //创建手机
    makeTelBox.after(btnWrap).after(makeInput({name:'u_tel', 'class': 'form-control', type:'text', maxlen: 11, limit:'int'})).remove();//创建获取按钮 然后移除自身以防破坏结构
}
//初始化
$(function() {
    emailEven(); //邮箱重置密码事件
    smsEven(); //短信重置密码事件
});