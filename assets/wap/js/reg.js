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
                        value: '注册账号'
                    })]
                })
            ]
        })
    });
    // console.log(header);
    contentObj.appendHeader(header);

    //登录框
    //js造一个简单的表单
    var bodyObj = makeForm({
        'name': '',
        'style': "background-color: #eee;",
        'padding': '50px 25px',
        'type': 'post',
        url: '/adppp/buyer/login',
        value:  [
            makeTable({
                tr_1: [
                    {
                    id: 'account_tr',
                    td: {
                        colspan: 2,
                        value: makeInput({
                            place: '推荐人手机号码',
                            name: 'inviter',
                            'class': 'no_border no_radius input-group-lg btn-block'
                        })
                    }
                },
                    {
                    id: 'account_tr',
                    td: [
                        {
                        padding: '20px 10px 0 0',
                        value: '帐号'
                        },
                        {
                            padding_top: '20px',
                            value: makeInput({
                                place: '帐号',
                                name: 'account',
                                'class': 'no_border no_radius input-group-lg btn-block',
                                null_func: function (data) {
                                    msgTisf('请输入帐号');
                                }
                            })
                        }
                    ]
                },  {
                    id: 'account_tr',
                    td: [
                        {
                        padding: '20px 10px 0 0',
                        value: '微信号'
                        },
                        {
                            padding_top: '20px',
                            value: makeInput({
                                place: '微信号',
                                name: 'weixin',
                                'class': 'no_border no_radius input-group-lg btn-block',
                                null_func: function (data) {
                                    msgTisf('请输入微信号');
                                }
                            })
                        }
                    ]
                },  {
                    id: 'account_tr',
                    td: [
                        {
                        padding: '20px 10px 0 0',
                        value: '手机号'
                        },
                        {
                            padding_top: '20px',
                            value: makeInput({
                                place: '手机号',
                                name: 'phone',
                                limit: 'int',
                                'class': 'no_border no_radius input-group-lg btn-block',
                                null_func: function (data) {
                                    msgTisf('请输入手机号');
                                }
                            })
                        }
                    ]
                },  {
                    id: 'account_tr',
                    td: [
                        {
                        padding_top: '20px',
                        value: '短信验证码'
                        },
                        {
                            padding_top: '20px',
                            value: makeInput({
                                place: '短信验证码',
                                name: 'sms_code',
                                'class': 'no_border no_radius input-group-lg btn-block',
                                null_func: function (data) {
                                    msgTisf('请输入短信验证码');
                                }
                            })
                        }
                    ]
                },{
                    id: 'account_tr',
                    td: [
                        {
                        padding: '20px 2px 0 0',
                        value: '性别'
                        },
                        {
                            padding_top: '20px',
                            value: makeRadios({
                                name: 'sex',
                                item_data:[{'value': 1,'text': '男'},{'value': 0,'text': '女'}]
                            })
                        }
                    ]
                },{
                    id: 'account_tr',
                    td: [
                        {
                            padding_top: '20px',
                        value: '出生年月日'
                        },
                        {
                            padding_top: '20px',
                            value: makeRili({
                                name: 'birth_day',
                                from_year: 1900,
                                place: '选择年月日',
                                to_year: 2010,
                                null_func: function (data) {
                                    msgTisf('请选择出生年月日');
                                }
                            })
                        }
                    ]
                },{
                    id: 'account_tr',
                    td: [
                        {
                            padding_top: '20px',
                        value: '身份证号码'
                        },
                        {
                            padding_top: '20px',
                            value: makeInput({
                                place: '',
                                name: 'id_card_number',
                                'class': 'no_border no_radius input-group-lg btn-block',
                                null_func: function (data) {
                                    msgTisf('请输入短信验证码');
                                }
                            })
                        }
                    ]
                },{
                    id: 'account_tr',
                    td: [
                        {
                            padding_top: '20px',
                        value: '身份证正面'
                        },
                        {
                            padding_top: '20px',
                            value: makeInput({
                                place: '',
                                name: 'id_card_front',
                                'class': 'no_border no_radius input-group-lg btn-block',
                                null_func: function (data) {
                                    msgTisf('请输入短信验证码');
                                }
                            })
                        }
                    ]
                },{
                    id: 'account_tr',
                    td: [
                        {
                            padding_top: '20px',
                        value: '身份证背面'
                        },
                        {
                            padding_top: '20px',
                            value: makeInput({
                                place: '',
                                name: 'id_card_back',
                                'class': 'no_border no_radius input-group-lg btn-block',
                                null_func: function (data) {
                                    msgTisf('请输入短信验证码');
                                }
                            })
                        }
                    ]
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
    contentObj.appendBody(bodyObj);
});