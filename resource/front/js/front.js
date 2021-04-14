require.config({
    paths: {
        jquery: '/resource/pub/js/jq/jquery-3.2.1',
        lrBase: '/assets/libs/lr/jquery-lr_base',
        lrBox: 'http://js.li6.cc/assets/libs/lr/box.ver/lrBox.1.1',
        lrEle: '/assets/libs/lr/jquery-lr_element',
    }
});
require(['jquery', 'lrEle', 'lrBox', 'lrBase'], function ($, lrEle, lrBox, lrBase) {

    //form里的快速注册
    window.fastReg= function() {
        lrBox.msgView('注册帐号', lrEle.makeForm({
            'url': '/?s=system&do=fast_reg',
            success_key: 'id',
            success_value: '0001',
            success_func: function (e) {
                lrBox.hideNewBox();
                lrBox.msgTisf('注册成功,正在进行自动登录...',1);
                //设置登录后跳转到我的个人中心
                window.checkLogin();
            },
            err_func: function (e) {
                lrBox.msgTisf(e.info);
            },
            'value': lrEle.makeTable({
                tr_1: [
                    {
                        td: [
                            {
                                value: lrEle.makeInput({
                                    'class': 'btn-block input-group-lg',
                                    name: 'account',
                                    place:'帐号',
                                    maxlen: 25, null_func: function () {
                                        lrBox.msgTisf('帐号呢？');
                                    }
                                })
                            }
                        ]
                    },
                    {
                        td: [
                            {
                                padding_top: '10px',
                                value: lrEle.makeInput({name:'pwd1', 'class': 'btn-block input-group-lg', place:'设置密码', type: 'password', value: '', maxlen: 25, null_func: function () {
                                        lrBox.msgTisf('密码呢？');
                                    }
                                })
                            }
                        ]
                    },
                    {
                        td: [
                            {
                                padding_top: '10px',
                                value: lrEle.makeInput({name:'pwd2', 'class': 'btn-block input-group-lg',  place:'重输密码', type: 'password', value: '', maxlen: 25, null_func: function () {
                                        lrBox.msgTisf('密码呢？');
                                    }})
                            }
                        ]
                    },
                    {
                        td: [
                            {
                                padding_top: '10px',
                                value: lrEle.makeBtn({type:'submit', value:'注册', 'class': 'btn btn-info btn-lg btn-block'})
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
    window.forget= function() {
        lrBox.hideNewBox();
        lrBox.msgView('找回密码', lrEle.makeForm({
            'url': '/?s=system&do=email_reset_password&json=true',
            success_key: 'id',
            success_value: '0043',
            success_func: function (e) {
                lrBox.msgTisf(e.msg);
                setTimeout(function () {
                    window.location.reload();
                }, 700)
            },
            err_func: function (e) {
                lrBox.msgTisf(e.msg+e.info);
            },
            'value': lrEle.makeTable({
                tr_1: [
                    {
                        td: [
                            {

                                value: lrEle.makeInput({
                                    'class': 'btn-block no_radius_right input-group-lg',
                                    name: 'my_email',
                                    place:'您的邮箱'
                                })
                            },
                            {
                                value: lrEle.makeBtn({
                                    'value': '获取邮件',
                                    'class': 'btn btn-default no_radius_left btn-lg',
                                    'type': 'button',
                                    click: function () {
                                        var newEmail = my_email.value;
                                        if(!newEmail) {
                                            lrBox.msgTisf('请先输入您的邮箱');
                                            return;
                                        }
                                        postAndDone({
                                            'post_url': '/?s=system&do=send_mail&json=true',
                                            'post_data': {my_mail: my_email.value},
                                            'success_key': 'id',
                                            'success_value': '0038',
                                            'success_func': function () {
                                                lrBox.msg('发送成功，请登录邮箱查看验证码');
                                            },
                                            err_func: function (e) {
                                                lrBox.msgTisf(e.msg+e.info);
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
                                value: lrEle.makeInput({name:'new_pwd', 'class': 'btn-block  input-group-lg',  place:'输入新密码', type: 'password', value: '', maxlen: 25, null_func: function () {
                                        lrBox.msgTisf('新密码呢？');
                                    }})
                            }
                        ]
                    },
                    {
                        td: [
                            {
                                padding_top: '10px',
                                value: lrEle.makeInput({
                                    'class': 'btn-block no_radius_right  input-group-lg',
                                    name: 'email_code',
                                    place: '邮箱验证码'
                                })
                            },
                            {
                                padding_top: '10px',
                                value: lrEle.makeBtn({type:'submit', value:'提交修改', 'class': 'btn btn-info no_radius_left  btn-lg'})
                            }
                        ]
                    }
                ]
            })
        }), 400, 300);
    }

//检测访客是否登录
    window.checkLogin = function() {
        var topMenu = $('#navigation');
        var stateBox = topMenu.find('.status_box');
        lrBase.rePost('/index/system/checkLogin',{}, function(res){
            var my_menu = '';
            if( res.data.account !== '' && res.data.nickname !== '' ){
                if(stateBox.length > 0 ) {
                    my_menu = "user：<a target='_self' href='/user/?token="+ res.data.token +"'>"+ res.data.nickname +"</a>   <em></em>  <a  href=\"javascript: void(0);\" onclick=\"logOut();\" target=\"_self\">out</a><em></em> ";
                }
            } else {
                if(stateBox.length > 0 ) {
                    my_menu = '<a class="login" onclick="loginIn();" target="_self" href="javascript: void(0);">登录</a>';
                }
            }
            stateBox.html(my_menu);
        });
    }

//全局退出登录
    window.logOut=function() {
        lrBase.rePost('/index/system/logout', {}, function (data) {
            if (data.id != '0233') {
                if (data.info) data.msg += data.info;
                lrBox.msgTisf(data.msg);
            } else {
                lrBox.msgTisf(data.msg);
                setTimeout(function () {
                    var url = window.location.toString();
                    if (url.indexOf('#') != -1) {
                        url = url.split('#');
                        url = url[0];
                        window.location = url;
                    } else {
                        window.checkLogin();
                    }
                }, 200);
            }
        });
    }

//全局登录窗口
    window.loginIn = function (requestUrl) {
        requestUrl = requestUrl || '';
        //js造一个简单的表单
        var diyForm = lrEle.makeForm({
            'name': '',
            'type': 'post',
            'url' : '/index/system/idLogin',
            value:  [
                lrEle.makeInput({
                    name: 'request',
                    value: requestUrl,
                    type: 'hidden'
                }),
                lrEle.makeTable({
                    tr_1: [{
                        id: 'account_tr',
                        td: {
                            value: lrEle.makeInput({
                                place: '帐号',
                                name: 'u_nick',
                                'class': 'input-group-lg btn-block',
                                null_func: function (data) {
                                    lrBox.msgTisf('帐号呢');
                                }
                            })
                        }
                    },{
                        td: {
                            padding_top: '20px',
                            value: lrEle.makeInput({
                                place: '密码',
                                'class': 'input-group-lg btn-block',
                                type: 'password',
                                name: 'u_pwd',
                                null_func: function (data) {
                                    lrBox.msgTisf('密码呢');
                                }
                            })
                        }
                    },{
                        td: {
                            colspan: '2',
                            padding_top: '20px',
                            'class': 'submit_box',
                            value: lrEle.makeBtn({
                                value: '登 录',
                                type: 'submit',
                                'class': 'btn btn-success btn-block'
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
            success_key: 'code',
            success_value: '1',
            success_func: function (res) {
                lrBox.hideNewBox();
                window.checkLogin();
                lrBox.msgTisf(res.msg);
            },
            err_func: function (data) {
                lrBox.msgTisf(data.msg);
            }
        });
        lrBox.msgWin('登录', diyForm, 400, 100,{
            bg: true,//背景遮挡
            'addClass': 'new_loginbox',
            canDrag: false
        });
    };

    $(document).ready(function () {
        window.checkLogin();
    });
});
