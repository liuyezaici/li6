(function(global__) {
    //头部
    var header = makeDiv({
        'id': 'navigation',
        value: makeDiv({
            'class': 'navbar navbar-default',
            'margin-bottom': '0',
            value: [
                makeDiv({
                    'class': 'navbar-header',
                    value: [makeSpan({
                        'class': 'navbar-brand',
                        'max-width': '120px',
                        value: makeImg({
                            'class': 'navbar-brand',
                            padding: 0,
                            src: '/assets/img/logo.png'
                        })
                    }),makeDiv({
                        'class': 'top_title',
                        value: '登录'
                    })]
                })
            ]
        })
    });
    // console.log(header);

    //登录框
    //js造一个简单的表单
    var bodyObj = makeForm({
        'name': '',
        'padding': '50px 25px',
        'type': 'post',
        'style': "background: url(/assets/img/index_bg.jpg) center center;",
        url: '/adppp/buyer/login',
        value:  [
            makeTable({
                tr_1: [{
                    id: 'account_tr',
                    td: {
                        value: makeInput({
                            place: '帐号',
                            name: 'account',
                            'class': 'no_border no_radius input-group-lg btn-block',
                            null_func: function (data) {
                                msgTisf('请输入帐号');
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
                            name: 'password',
                            null_func: function (data) {
                                msgTisf('请输入密码');
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
                            'class': 'btn btn-lg btn-pink btn-block no_radius'
                        })
                    }
                },{
                    'class': 'more_tr',
                    td: {
                        colspan: '2',
                        style: "padding: 20px 10px 0 0; text-align: right;",
                        value: [
                            makeA({
                            value: '忘记密码',
                            click: "wapRouter.goto('forget_pwd')",
                        }),
                            makeA({
                                value: '注册',
                                style: "margin-left: 20px;",
                                click: "wapRouter.goto('reg')"
                            })]
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
            msgTis(data.msg);
        },
        err_func: function (data) {
            msgTis(data.msg);
        }
    });
    contentObj.appendHeader(header);
    contentObj.appendBody(bodyObj);
});