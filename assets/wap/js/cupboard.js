(function(global__) {
    if(!global__.openid) {
        msg('您没有授权过微信登录.请先退出后使用微信授权登录');
        return;
    }
    var getCupboardUrl = '/adppp/cupboard/api/index/details/' ;
    var getCartTotalUrl = '/adppp/cart/api/index/getCartTotal/' ;
    var addToCartUrl = '/adppp/cart/api/index/addGidToCart/gid/' ;
    var balanceUrl = '/wap#balance?id=';
    var cupboardId = getUrlParam('id');
    //头部
    contentObj.appendHeader('');

    //渲染数据
    var postDa = {
    };
    postDa[global__.wapCfg.tokenKey] = global__.userToken;
    var bodyObj = makeDiv({
        'line_height': '0.5rem',
        data_from: {
            url: getCupboardUrl + 'id/'+ cupboardId,
            data_key: 'data',
            post_data: postDa,
            success_key: 'code',
            success_value: '1',
            err_func: function (e) {
                if(wapPubFunc.isLogOut(e.code)) {
                    wapPubFunc.getWeixinOpenid();
                    return;
                }
                msg(e.msg);
            },
        },
        value:  [
            makeDiv({
                'data': '{goodsList}',
                'class': 'bg-warning',
                'style': 'color: #333; font-size: 0.2rem; padding-left: .2rem;',
                show: '{{this.length} ==0}',
                value: '柜子没有分配商品'
            }),
            makeList({
                'data': '{goodsList}',
                show: '{{this.length} >0}',
                'class': 'list-group container',
                li: {
                    'class': 'list-group-item row',
                    value: [
                        makeImg({
                            'class': 'col-xs-4 thumbnail'
                            ,'width': '2rem'
                            ,src: '{cover}'
                        }),
                        makeDiv({
                            'class': 'col-xs-8',
                            style: 'position: relative; ',
                            value:
                                [
                                    makeDiv({
                                    value: '{title}'
                                    }),
                                    makeDiv({
                                        title: 'gid:{id}',
                                        value: '库存:{sku}'
                                    }),
                                    makeDiv({
                                        value: '仓口:{device_no}'
                                    }),
                                    makeDiv({
                                        value: '价格:￥{price}'
                                    }),
                                    makeChecked({
                                        name: 'check_goods',
                                        'class': 'check_goods',
                                        style: 'position: absolute; right: 0.2rem; top: 0;',
                                        value: '{id}',
                                        data_value: '{id}',
                                        data_price: '{price}',
                                        checked: '{{added}==1}',
                                        disabled: '{{sku}==0}',
                                        click: function (obj_) {
                                            var postDa = {
                                                'cupboardid': cupboardId
                                            };
                                            postDa[global__.wapCfg.tokenKey] = global__.userToken;
                                            var gid = obj_.attr('data_value');
                                            global__.wapPubFunc.tokenPost({
                                                post_url: addToCartUrl + gid,
                                                post_data: postDa,
                                                success_key: 'code',
                                                success_value: '1',
                                                success_func: function (e) {
                                                    msgTisf(e.msg);
                                                    recountTotalMoney();
                                                },
                                                err_func: function (e) {
                                                    msgTisf(e.msg);
                                                    obj_.checked = !obj_.checked;
                                                }
                                            });
                                        }
                                    })
                                ]
                        })
                    ]
                }
            })
        ]
    });
    contentObj.appendBody(bodyObj);

    //购物车数量
    var postDa = {cupboardId: cupboardId};
    postDa[global__.wapCfg.tokenKey] = global__.userToken;

    var countBar = makeDiv({
        data_from: {
            url: getCartTotalUrl,
            post_data: postDa,
            success_key: 'code',
            success_value: '1',
            data_key: 'msg',
            err_func: function (e) {
                msgTisf(e.msg);
            }
        },
        'style': "position: fixed; bottom: 0;left: 0;background-color: #fff; border-top:1px solid #dedede; width: 100%; height: 1rem;",
        value: [
            makeSpan({
                'style': "background-color: #edca9d;height: 88%; margin-top: 0.05rem; text-align: center;position: absolute; left: 0.2rem;top:0; border-radius: .6rem;padding: 0.15rem;",
                value: [makeImg({
                    src: '/assets/wap/img/cart.png',
                    height: '100%'
                }),
                makeSpan({
                    'style': "background-color: #ff0000;font-size: 0.22rem; color: #fff; margin-top: 0.05rem; text-align: center;position: absolute; right: 0;top: -0.05rem; border-radius: .6rem;padding: 0 0.08rem;",
                    value: '{num}'
                })]
            }),
            makeSpan({
                'style': "margin-left: 1.5rem;height: 100%;line-height: 0.9rem;",
                value: "共<span style='color: #ff9f10;' id='showTotalMoney'>￥{money}</span>"
            }),
            makeA({
                'style': "background-color: #edca9d;color:#000;font-size: 0.4rem; text-align: center;position: absolute; right: 0;top:0; border: 0; line-height: 1rem; height: 100%; padding: 0 0.5rem;",
                value: '去结算',
                click: function () {
                    var checks =  bodyObj.findClass('diy_checked');
                    var hasChecked = false;
                    checks.forEach(function (o_) {
                       if(o_.checked == true) {
                           hasChecked = true;
                           return false;
                       }
                    });
                    if(!hasChecked) {
                        msgTisf('请先选择商品');
                        return;
                    }
                    location = balanceUrl + cupboardId;
                },
            })
        ]
    });
    contentObj.addBody(countBar);
    function recountTotalMoney() {
        var checkeds = bodyObj.findClass('check_goods');
        var totalMoney = 0;
        var tmpMoney = 0;
        var tmpChecked = 0;
        console.log(checkeds);
        $.each(checkeds, function (n, v) {
            tmpChecked = v;
            if(tmpChecked.checked) {
                tmpMoney= tmpChecked.attr('data_price');
                tmpMoney = parseFloat(tmpMoney);
                totalMoney += tmpMoney;
            }
        });
        totalMoney = totalMoney.toFixed(2);
        countBar.find('#showTotalMoney').html('￥'+ totalMoney);
    }
    document.title = '商品';
});