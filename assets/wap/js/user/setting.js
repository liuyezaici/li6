(function() {
    var getInfoUrl = '/adppp/usercenter/api/User/getInfo/' ;
    var editInfoApi = '/adppp/usercenter/api/User/edit/' ;
    var avatarUrl = '/adppp/usercenter/api/User/avatar/id/' ;
    var uploadAvatarApi = '/adppp/usercenter/api/User/uploadAvatar' ;
    var editMobileApi = '/adppp/usercenter/api/User/changemobile/' ;
    //渲染数据
    var postDa = {
    };
    postDa[parent.wapCfg.tokenKey] = parent.userToken;
    var userData = {
        url: getInfoUrl,
        data_key: 'data',
        post_data: postDa,
        success_key: 'code',
        success_value: '1',
        err_func: function (e) {
            if(wapPubFunc.isLogOut(e.code)) {
                wapPubFunc.getWeixinOpenid();
                return;
            }
            msgTisf(e.msg);
        },
    };
    //头部
    var header = makeDiv({
        'id': 'navigation',
        data_from: userData ,
        value: makeDiv({
            'style': "position: relative;",
            value: [ makeDiv({
                'style': "padding: 0.15rem; background-color: #fff;height: 1.2rem; ",
                value: [
                    makeSpan({
                        'style': "color: #555;display:inline-block; width: 1rem; padding: 0.3rem 0 0 0.2rem; font-size: 0.24rem;",
                        value: '头像'
                    }),
                    makeDiv({
                        'style': "border-radius: 1rem; cursor:pointer;margin: 0 auto; display:block; position:absolute; right: 0.56rem;top:0.2rem;overflow:hidden;background-color: #fff; width: 0.8rem; height: 0.8rem;",
                        value: makeInput({
                            name: 'avatar',
                            type: 'file',
                            value: '',  //图片url
                            url: uploadAvatarApi, //上传url
                            success_key: 'code',
                            success_value: '1',
                            style: 'width: 100%;height: 100%; border-radius: 2rem; display:block; border: 0.01rem solid #ddd;',
                            background: 'url('+avatarUrl +'{uid}) 0 0 /cover',
                            success_func: function (input_, response) {
                                msgTisf(response.msg);
                                input_.css('background', function () {
                                    var lastBg = input_.css('background');
                                    lastBg = lastBg.replace(/url\("([^"]+)"\)/, 'url("$1&r='+ makeRadom(10) +'")');
                                    return lastBg;
                                });
                            },
                            err_func: function (data) {
                                msgTisf(response.msg);
                            },
                        }),
                        click: function () {
                            location.hash='setting';
                        }
                    }),
                    makeSpan({
                        'class': "jt_right",
                        style: 'top: .52rem',
                        value: ''
                    })
                ]
            }),
                makeDiv({
                    'style': "margin-top: 0.18rem;cursor:pointer;padding-left: 0.15rem;position: relative;background-color: #fff;height: 0.9rem;",
                    value: [
                        makeSpan({
                            'style': "color: #555;display:inline-block; width: 1rem; padding: 0.3rem 0 0 0.2rem; font-size: 0.24rem;",
                            value: '昵称'
                        }),
                        makeDiv({
                            'style': " margin: 0 auto;position:absolute; right: 0.56rem;top:0.3rem;",
                            value: '{nickname}'
                        }),
                        makeSpan({
                            'class': "jt_right",
                            style: 'top: .4rem',
                            value: ''
                        })
                    ],
                    click: function () {
                        makeFormEdit({
                            width: '70%',
                            data_from: userData ,
                            url: editInfoApi,
                            post_data: postDa,
                            success_key: 'code',
                            success_value: '1',
                            success_func: function (e) {
                                hideNewBox();
                                header.renewData();
                                msgTisf(e.msg);
                            },
                            err_func: function (e) {
                                if(wapPubFunc.isLogOut(e.code)) {
                                    wapPubFunc.getWeixinOpenid();
                                    return;
                                }
                                msgTisf(e.msg);
                            },
                            value: [makeInput({
                                'name': 'nickname',
                                'class': 'no_radius form-control',
                                value: '{nickname}'
                            })]
                        });
                    }
                }),
                makeDiv({
                    'style': "margin-top: 0.18rem;cursor:pointer;padding-left: 0.15rem;position: relative;background-color: #fff;height: 0.9rem;",
                    value: [
                        makeSpan({
                            'style': "color: #555;display:inline-block; width: 1rem; padding: 0.3rem 0 0 0.2rem; font-size: 0.24rem;",
                            value: '手机'
                        }),
                        makeDiv({
                            'style': "margin: 0 auto;position:absolute; right: 0.56rem;top:0.3rem;",
                            value: '{mobile}'
                        }),
                        makeSpan({
                            'class': "jt_right",
                            style: 'top: .3rem',
                            value: ''
                        })
                    ],
                    click: function () {
                        makeFormEdit({
                            width: '70%',
                            url: editMobileApi,
                            post_data: postDa,
                            success_key: 'code',
                            success_value: '1',
                            success_func: function (e) {
                                msgTisf(e.msg);
                            },
                            err_func: function (e) {
                                if(wapPubFunc.isLogOut(e.code)) {
                                    wapPubFunc.getWeixinOpenid();
                                    return;
                                }
                                msgTisf(e.msg);
                            },
                            value: [
                                makeDiv({
                                'class': 'input-group',
                                value: [
                                    makeInput({
                                        'name': 'new_mobile',
                                        'place': '输入新手机',
                                        'class': 'form-control',
                                        value: ''
                                    }),
                                    makeDiv({
                                        'class': 'input-group-btn',
                                        value:makeBtn({
                                                'class': 'btn btn-success btn-block',
                                                value: '获取短信',
                                                rest_time: 0,
                                                click: function (obj) {
                                                    var newTel = new_mobile.value;
                                                    if (!newTel) {
                                                        msgTisf('请输入新的手机');
                                                        return;
                                                    }
                                                    global__.wapPubFunc.tokenPost({
                                                        post_url: global__.wapCfg.getSmsToLoginApi,
                                                        post_data: {
                                                            mobile: newTel,
                                                            'event': 'changemobile',
                                                        },
                                                        success_key: 'code',
                                                        success_value: '1',
                                                        success_func: function () {
                                                            msgTis('发送成功，请勿将手机短信告知他人');
                                                            obj.subTime(60);
                                                        },
                                                        err_func: function (e) {
                                                            msgTis(e.msg);
                                                        }
                                                    });
                                                }
                                        })
                                    })
                                ]
                            }),makeDiv({
                                'margin_top': '0.3rem',
                                value: [
                                    makeInput({
                                        'class': 'btn-block',
                                        'name': 'code',
                                        place: '输入短信验证码'
                                    })
                                ]
                            })]
                        });
                    }
                }),
                makeDiv({
                    'style': "margin-top: 0.18rem;cursor:pointer;padding-left: 0.15rem;position: relative;background-color: #fff;height: 0.9rem;",
                    value: [
                        makeSpan({
                            'style': "color: #ff0000;display:inline-block; width: 1rem; padding: 0.3rem 0 0 0.2rem; font-size: 0.24rem;",
                            value: '退出',
                            click: function () {
                                global__.wapPubFunc.logOut();
                            }
                        }),
                    ]
                }),
            ],
        })
    });
    contentObj.appendHeader(header);

    contentObj.appendBody('');

    contentObj.addBody('');

    document.title = '控制面板';
});