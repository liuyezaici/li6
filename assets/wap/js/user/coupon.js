(function() {
    var getOrderUrl = '/adppp/coupon/api/index/myCoupons/' ;
    contentObj.appendHeader('');
    //渲染数据
    var postDa = {
    };
    postDa[parent.wapCfg.tokenKey] = parent.userToken;
    var bodyObj = makeDiv({
        'line_height': '0.5rem',
        'padding': '0 0.15rem',
        data_from: {
            url: getOrderUrl,
            data_key: 'data.list',
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
        value:  [
            makeDiv({
                'class': 'bg-warning',
                'style': 'color: #333; font-size: 0.3rem; padding: .5rem 0; text-align: center;',
                show: '{{this.length} ==0}',
                value: '您没有优惠券'
            }),
            makeList({
                show: '{{this.length} >0}',
                'class': 'list-group',
                li: {
                    'style': "position: relative;",
                    value: [
                        makeSpan({
                            value: makeImg({
                                width: '100%',
                                src: '/assets/wap/img/{{invalid}==true? "coupon_invalid":"coupon_ok"}.png',
                            })
                        }),
                        makeSpan({
                            'style': "color: #fff; font-size: 0.56rem;position: absolute; left: 0.5rem; top: 0.6rem;",
                            margin_left: '.2rem',
                            value: '{money_title_user}',
                        }),
                        makeSpan({
                            'style': "color: #fff; font-size: 0.26rem;position: absolute; left: 3rem; top:{{invalid}==true? \"1.2rem\":\"0.6rem\"};",
                            value: '{time_title}',
                        }),
                        makeSpan({
                            show: '{invalid}',
                            'style': "color: #fff; font-size: 0.26rem;position: absolute; left: 3rem; top: 0.6rem;",
                            value: '已过期',
                        })
                    ]
                }
            })
        ]
    });
    contentObj.appendBody(bodyObj);
    contentObj.addBody('');
    document.title = '我的优惠券';
});