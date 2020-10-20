
//form里的快速注册
function fastReg() {
    msgView('注册帐号', makeForm({
        'url': '/?s=system&do=fast_reg',
        success_key: 'id',
        success_value: '0001',
        success_func: function (e) {
            hideNewBox();
            msgTis('注册成功,正在进行自动登录...',1);
            //设置登录后跳转到我的个人中心
            checkLogin();
        },
        err_func: function (e) {
            msgTis(e.info);
        },
        'value': makeTable({
            tr_1: [
                {
                    td: [
                        {
                            value: makeInput({
                                'class': 'btn-block input-group-lg',
                                name: 'account',
                                place:'帐号',
                                maxlen: 25, null_func: function () {
                                    msgTis('帐号呢？');
                                }
                            })
                        }
                    ]
                },
                {
                    td: [
                        {
                            padding_top: '10px',
                            value: makeInput({name:'pwd1', 'class': 'btn-block input-group-lg', place:'设置密码', type: 'password', value: '', maxlen: 25, null_func: function () {
                                    msgTis('密码呢？');
                                }
                            })
                        }
                    ]
                },
                {
                    td: [
                        {
                            padding_top: '10px',
                            value: makeInput({name:'pwd2', 'class': 'btn-block input-group-lg',  place:'重输密码', type: 'password', value: '', maxlen: 25, null_func: function () {
                                    msgTis('密码呢？');
                                }})
                        }
                    ]
                },
                {
                    td: [
                        {
                            padding_top: '10px',
                            value: makeBtn({type:'submit', value:'注册', 'class': 'btn btn-info btn-lg btn-block'})
                        }
                    ]
                }
            ]
        })
    }), 420, 250,{
        bg: true //背景遮挡
    });
}
//form里的找回密码
function forget() {
    hideNewBox();
    msgView('找回密码', makeForm({
        'url': '/?s=system&do=email_reset_password&json=true',
        success_key: 'id',
        success_value: '0043',
        success_func: function (e) {
            msgTis(e.msg);
            setTimeout(function () {
                window.location.reload();
            }, 700)
        },
        err_func: function (e) {
            msgTis(e.msg+e.info);
        },
        'value': makeTable({
            tr_1: [
                {
                    td: [
                        {

                            value: makeInput({
                                'class': 'btn-block no_radius_right input-group-lg',
                                name: 'my_email',
                                place:'您的邮箱'
                            })
                        },
                        {
                            value: makeBtn({
                                'value': '获取邮件',
                                'class': 'btn btn-default no_radius_left btn-lg',
                                'type': 'button',
                                click: function () {
                                    var newEmail = my_email.value;
                                    if(!newEmail) {
                                        msgTis('请先输入您的邮箱');
                                        return;
                                    }
                                    postAndDone({
                                        'post_url': '/?s=system&do=send_mail&json=true',
                                        'post_data': {my_mail: my_email.value},
                                        'success_key': 'id',
                                        'success_value': '0038',
                                        'success_func': function () {
                                            msg('发送成功，请登录邮箱查看验证码');
                                        },
                                        err_func: function (e) {
                                            msgTis(e.msg+e.info);
                                        }
                                    });
                                }
                            })
                        }
                    ]
                },
                {
                    td: [
                        {
                            padding_top: '10px',
                            colspan: 2,
                            value: makeInput({name:'new_pwd', 'class': 'btn-block  input-group-lg',  place:'输入新密码', type: 'password', value: '', maxlen: 25, null_func: function () {
                                    msgTis('新密码呢？');
                                }})
                        }
                    ]
                },
                {
                    td: [
                        {
                            padding_top: '10px',
                            value: makeInput({
                                'class': 'btn-block no_radius_right  input-group-lg',
                                name: 'email_code',
                                place: '邮箱验证码'
                            })
                        },
                        {
                            padding_top: '10px',
                            value: makeBtn({type:'submit', value:'提交修改', 'class': 'btn btn-info no_radius_left  btn-lg'})
                        }
                    ]
                }
            ]
        })
    }), 400, 300);
}

//检测访客是否登录
function checkLogin() {
    var topMenu = $('#navigation');
    var stateBox = topMenu.find('.status_box');
    rePost('/index/system/checkLogin',{}, function(data){
        if( data.nick && data.nick!= '' ){
            if(data.local_uid) {
                window.local_uid = data.local_uid;
            }
            var model = 'user';
            if(data.uType == 3) model = 'employee';
            if(stateBox.length > 0 ) {
                var my_menu = "user：<a target='_self' href='/?s="+ model +"'>"+ data.nick +"</a>   <em></em>  <a  href=\"javascript: void(0);\" onclick=\"logOut();\" target=\"_self\">out</a><em></em> ";
                stateBox.html(my_menu );
            }
        } else {
            if(stateBox.length > 0 ) {
                var my_menu = '<a class="login" onclick="loginIn();" target="_self" href="javascript: void(0);">登录</a>';
                stateBox.html( my_menu );
            }
        }
    });
}

//全局退出登录
function logOut() {
    rePost('/?s=system/ajax_out', {}, function (data) {
        if (data.id != '0233') {
            if (data.info) data.msg += data.info;
            msg(data.msg, 4);
        } else {
            msgTis(data.msg);
            setTimeout(function () {
                var url = window.location.toString();
                if (url.indexOf('#') != -1) {
                    url = url.split('#');
                    url = url[0];
                    window.location = url;
                } else {
                    checkLogin();
                }
            }, 200);
        }
    });
}

//全局登录窗口
window.loginIn = function (requestUrl) {
    requestUrl = requestUrl || '';
    //js造一个简单的表单
    var diyForm = makeForm({
        'name': '',
        'type': 'post',
        'url' : '/?s=system/pwd_login',
        value:  [
            makeInput({
                name: 'request',
                value: requestUrl,
                type: 'hidden'
            }),
            makeTable({
            tr_1: [{
                id: 'account_tr',
                td: {
                        value: makeInput({
                                place: '帐号',
                                name: 'u_nick',
                                'class': 'no_border no_radius input-group-lg btn-block',
                                null_func: function (data) {
                                    msgTis('帐号呢');
                                }
                            })
                    }
            },{
                td: {
                        padding_top: '20px',
                        value: makeInput({
                                place: '密码',
                                'class': 'no_border no_radius input-group-lg btn-block',
                                type: 'password',
                                name: 'u_pwd',
                                null_func: function (data) {
                                    msgTis('密码呢');
                                }
                            })
                    }
            },{
                'class': 'submit_tr',
                td: {
                    colspan: '2',
                    padding_top: '20px',
                    'class': 'submit_box',
                    value: makeBtn({
                        value: '登 录',
                        type: 'submit',
                        'class': 'btn btn-lg btn-success btn-block no_radius'
                    })
                }
            }]
        })],
        submit: function (obj, ev) {//提交时回调
            // console.log(obj);
            // console.log(ev);
            // ev.preventDefault();
            // console.log('on submit');
            // return false;// 可以让表单停止提交 但要记得 ev.preventDefault();
        },
        success_key: 'id',
        success_value: '0001',
        success_func: function (data) {
            hideNewBox();
            checkLogin();
            msgTis(data.msg);
        },
        err_func: function (data) {
            msgTis(data.msg);
        }
    });
    msgWin('登录', diyForm, 400, 100,{
        bg: true,//背景遮挡
        'class': 'new_loginbox',
        canDrag: false
    });
};

$(document).ready(function () {
    checkLogin();
});
