(function() {
    var getInfoUrl = '/adppp/usercenter/api/User/getInfo/';
    var avatarUrl = '/adppp/usercenter/api/User/avatar/id/' ;
    var myOrderUrl = '/wap#user_order';
    var myCouponUrl = '/wap#user_coupon';
    //渲染数据
    var postDa = {
    };
    postDa[parent.wapCfg.tokenKey] = parent.userToken;
    //头部
    var header = makeDiv({
        'id': 'navigation',
        data_from: {
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
        },
        value: makeDiv({
            'style': "position: relative;",
            value: [
                makeSpan({
                    value: makeImg({
                        'width': '100%',
                        src: '/assets/wap/img/user_center_bg.png',
                    })
                }),makeDiv({
                'style': "position: absolute; z-index: 1; bottom: 60%; left: 50%;height: 1rem; text-align: center;",
                    value: makeDiv({
                        'style': "margin-left: -50%; overflow:hidden;display: block;width:100%; ",
                        value: [
                            makeDiv({
                                'style': "border-radius: 1rem; cursor:pointer;margin: 0 auto; overflow:hidden;background-color: #fff; width: 1rem; height: 1rem;",
                                value: makeImg({
                                    'style': "border-radius: 1rem; width: 100%;",
                                    src: avatarUrl +'{uid}',  //图片url
                                    load: function (obj, e) {
                                        console.log('success');
                                        console.log(e);
                                    },
                                    error: function (obj, e) {
                                        obj.src = '/assets/wap/img/avatar.png';
                                    }
                                }),
                                click: function () {
                                    location.hash='setting';
                                }
                            }),
                            makeDiv({
                                'style': "color: #fff;position: absolute; font-size: 0.26rem; width: 100%; bottom: -0.5rem;text-align: center;",
                                value: '{nickname}'
                            }),
                            makeDiv({
                                'style': "color: #fff;position: absolute; font-size: 0.26rem; width: 100%; bottom: -0.7rem;text-align: center;",
                                value: '{mobile}'
                            })
                        ]
                    })
                })]
        })
    });
    contentObj.appendHeader(header);

    var bodyObj = makeDiv({
        value:  [
            makeDiv({
                'style': "margin-top: 0.18rem;background-color: #fff;",
                value: [
                    makeDiv({
                        'style': "padding: 0.15rem; position: relative;",
                        value: [
                            makeSpan({
                                value: makeImg({
                                    width: '0.26rem',
                                    src: '/assets/wap/img/order.png',
                                })
                            }),
                            makeSpan({
                                'style': "color: #555; font-size: 0.24rem;",
                                margin_left: '.2rem',
                                value: '消费记录',
                            }),
                            makeSpan({
                                'class': "jt_right",
                                margin_left: '.2rem',
                                value: ''
                            })
                        ],
                        click: function () {
                            location = myOrderUrl;
                        }
                    }),
                    makeDiv({
                        'style': "padding: 0.15rem; position: relative;",
                        value: [
                            makeSpan({
                                value: makeImg({
                                    width: '0.26rem',
                                    src: '/assets/wap/img/coupon.png',
                                })
                            }),
                            makeSpan({
                                'style': "color: #555; font-size: 0.24rem;",
                                margin_left: '.2rem',
                                value: '优惠券',
                            }),
                            makeSpan({
                                'class': "jt_right",
                                margin_left: '.2rem',
                                value: ''
                            })
                        ],
                        click: function () {
                            location = myCouponUrl;
                        }
                    }),
                ]
            })
        ]
    });
    contentObj.appendBody(bodyObj);

    contentObj.addBody('');
    document.title = '个人中心';
});