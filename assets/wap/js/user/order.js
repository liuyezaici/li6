(function() {
    var getOrderUrl = '/adppp/order/api/index/getOrderList/' ;
    var viewOrderUrl = '/adppp/order/api/index/getOrderDetails/id/' ;
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
        value:  [
            makeDiv({
                'class': 'bg-warning',
                'style': 'color: #333; font-size: 0.3rem; padding: .5rem 0; text-align: center;',
                show: '{{this.length} ==0}',
                value: '您没有订单'
            }),
            makeList({
                data: '{list}',
                show: '{{this.length} >0}',
                'class': 'list-group',
                li: {
                    'class': 'list-group-item row',
                    'style': "position: relative; ",
                    'data-id': "{id}",
                    value: [
                        makeSpan({
                            value: makeImg({
                                width: '0.26rem',
                                src: '/assets/wap/img/address.png',
                            })
                        }),
                        makeSpan({
                            'style': "color: #333; font-size: 0.26rem;",
                            margin_left: '.2rem',
                            value: '{station_title}',
                        }),
                        makeSpan({
                            'style': "color: #ff7f00; font-size: 0.26rem;position: absolute; right: 0.5rem; top: 0.2rem;",
                            value: '￥{orders_money}',
                        }),
                        makeDiv({
                            'style': "color: #999; font-size: 0.24rem;",
                            margin_left: '.45rem',
                            value: '{noSecond[run]({createtime})}',
                        })
                    ],
                    click: function (li_) {
                        var oid = li_.attr('data-id');
                        var viewObj = makeDiv({
                            data_from: {
                                url: viewOrderUrl + oid,
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
                            'style': "padding: 0.2rem 0 0.2rem 0.2rem;border-bottom: 0.02rem solid #eee;",
                            value: [
                                makeSpan({
                                    value: makeImg({
                                        width: '0.26rem',
                                        src: '/assets/wap/img/address.png',
                                    })
                                }),
                                makeSpan({
                                    'style': "color: #333; font-size: 0.26rem; ",
                                    margin_left: '.2rem',
                                    value: '{station_title}',
                                }),
                                makeList({
                                    data: '{goodsList}',
                                    style: 'padding: 0;margin: 0.3rem 0 0 0;',
                                    li: {
                                        style: 'position: relative;list-style-type:none;padding: 0.2rem;margin:0;border-bottom: 0.02rem solid #eee;',
                                        value: [
                                            makeImg({
                                                width: '1rem;',
                                                height: '1rem;',
                                                data: '{goods_info}',
                                                src: '{cover}',
                                                style: 'display: inline-block;',
                                            }),
                                            makeSpan({
                                                style: 'display: inline-block;vertical-align: top;margin-left: 0.2rem;',
                                                data: '{goods_info}',
                                                value: '{title}',
                                            }),
                                            makeSpan({
                                                style: 'position: absolute; left: 1.4rem; font-size: 0.22rem; bottom: 0.17rem;color: #999;',
                                                value: '￥{price}',
                                            }),
                                            makeSpan({
                                                style: 'position: absolute; right: 0.5rem; top: 0.2rem;',
                                                value: 'x{num}',
                                            })
                                        ]
                                    }
                                }),
                                makeDiv({
                                    'style': "color: #333; font-size: 0.26rem; margin-bottom: 0.2rem;text-align:right; padding: 0.2rem 0.2rem 0.2rem 0; border-bottom: 0.1rem solid #eee;margin-right:0.1rem;",
                                    value: '共{totalNum}件商品，合计:￥{orders_money}元',
                                }),
                                makeDiv({
                                    'style': "color: #333; font-size: 0.26rem; margin-bottom: 0.1rem",
                                    value: '订单编号:{number}',
                                }),
                                makeDiv({
                                    'style': "color: #333; font-size: 0.26rem;margin-bottom: 0.1rem ",
                                    value: '订单状态:{status_name}',
                                }),
                                makeDiv({
                                    'style': "color: #333; font-size: 0.26rem; margin-bottom: 0.1rem",
                                    value: '创建时间:{noSecond[run]({createtime})}',
                                }),
                                makeDiv({
                                    data: '{payinfo}',
                                    'style': "color: #333; font-size: 0.26rem; ",
                                    value: '支付时间:{noSecond[run]({paytime})}',
                                }),
                            ]
                        });
                        msgWin('消费详情', viewObj, '90%',1);
                    }
                }
            })
        ]
    });
    contentObj.appendBody(bodyObj);
    contentObj.addBody('');
    document.title = '消费记录';
});