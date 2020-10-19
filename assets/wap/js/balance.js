(function(global__) {
    if(!global__.openid) {
        msg('您没有授权过微信登录.请先退出后使用微信授权登录');
        return;
    }
    var getCartUrl = '/adppp/cart/api/index/getCupCartList/' ;
    var submitCartUrl = '/adppp/cart/api/index/submitCartList/' ;
    var getWeixinQrcodeUrl = '/adppp/weixinpay/api/index/getWeixinQrcodeTopay/';
    var getCouponUrl = '/adppp/coupon/api/index/myCoupons/';
    var getSuccessTokenForOpenDoorUrl = '/adppp/usekey/api/index/getMyCode/';
    var releaseOrderAndGoodsUrl = '/adppp/pay/api/index/cancelPay/';
    var cupboardId = getUrlParam('id');
    //头部
    var header = makeDiv({
        'id': 'navigation',
        value: makeDiv({
            value: [
                makeSpan({
                    'max-width': '50px',
                    value: makeImg({
                        'width': '0.4rem',
                        margin_left: '0.15rem',
                        margin_right: '0.1rem',
                        src: '/assets/wap/img/wx_ico.png',
                    })
                }),makeSpan({
                    line_height: '0.8rem',
                    value: '微信支付'
                })
            ]
        })
    });
    contentObj.appendHeader(header);

    //渲染数据
    var postDa = {
    };
    postDa[global__.wapCfg.tokenKey] = global__.userToken;
    var bodyObj = makeForm({
        url: submitCartUrl,
        success_key: 'code',
        success_value: '1',
        submit: function(form_) {
            if(!global__.openid) {
                msgTisf('没有定义openid变量');
                return false;
            }
            var postData_ = form_.getFormData();
            if(postData_['cartids'].length == '') {
                msgTisf('请先选择商品');
                return false;
            }
        },
        success_func: function (res) {
            // msg('submitCartUrl:'+ JSON.stringify(res));
            res = res.data;
            var payNumber = res.pay_number;
            global__.wapPubFunc.tokenPost(
                {
                    post_url: getWeixinQrcodeUrl,
                    post_data: {
                        openid: global__.openid,
                        total_money: res.total_money,
                        trade_no: payNumber,
                        trade_type: 'JSAPI'
                    },
                    success_key: 'code',
                    success_value: '1',
                    success_func: function (res2) {
                        // msg('getWeixinQrcodeUrl:'+ JSON.stringify(res2));
                        var jsApiParameters = res2.data;
                        //微信内置函数
                        WeixinJSBridge.invoke(
                            'getBrandWCPayRequest',
                            jsApiParameters, // 提交的支付信息
                            function(res) {
                                // msg('getBrandWCPayRequest:'+ JSON.stringify(res));
                                if (res.err_msg == 'get_brand_wcpay_request:ok') {
                                    msgTisf('支付成功,正在开门');
                                    //开门码需要微信支付回调 起码2秒成功
                                    setTimeout(function () {
                                        global__.wapPubFunc.tokenPost({
                                            post_url: getSuccessTokenForOpenDoorUrl,
                                            success_key: 'code',
                                            success_value: '1',
                                            success_func: function (resForKey) {
                                                var keycode = resForKey.data.keycode;
                                                // msgTisf('code:'+ keycode);
                                                var boxStr = boxids.val().toString();
                                                $.each(boxStr.split(','), function (n, boxid) {
                                                    setTimeout(function () {
                                                        msgTisf('正在打开柜子:'+ boxid);
                                                        //执行打开柜子
                                                        var dataStr = {
                                                            'cupboardId': cupboardId,
                                                            'keycode': keycode,
                                                            'boxId': boxid
                                                        };
                                                        // msg('打开柜子:'+ JSON.stringify(dataStr));
                                                        global__.socketObject.send(JSON.stringify({"ctrl": "buyer","cmd": "opendoor","data": dataStr}));
                                                    }, 10000 * (n));
                                                });
                                            },
                                            err_func: function (e) {
                                                if(wapPubFunc.isLogOut(e.code)) {
                                                    global__.wapPubFunc.getWeixinOpenid();
                                                    return;
                                                } else {
                                                    msgTisf('获取失败:'+ e.msg);                                            }
                                            }
                                        });
                                    }, 2000);

                                } else {
                                    msgTisf('取消支付');
                                    //删除订单 释放商品
                                    global__.wapPubFunc.tokenPost({
                                        post_url: releaseOrderAndGoodsUrl,
                                        post_data: {
                                            payNumber: payNumber
                                        },
                                    });
                                }
                            }
                        );
                    },
                    err_func: function (res2) {
                        msg('getWeixinQrcode_error:'+ JSON.stringify(res2));
                    }
                }
            )
        },
        err_func: function (e) {
            console.log(e);
            if(wapPubFunc.isLogOut(e.code)) {
                global__.wapPubFunc.getWeixinOpenid();
                return;
            }
            msgTisf(e.msg);
        },
        'line_height': '0.5rem',
        'padding': '0 0.15rem',
        data_from: {
            url: getCartUrl + 'id/'+ cupboardId,
            data_key: 'msg',
            post_data: postDa,
            success_key: 'code',
            success_value: '1',
            err_func: function (e) {
                if(wapPubFunc.isLogOut(e.code)) {
                    global__.wapPubFunc.getWeixinOpenid();
                    return;
                }
                msgTisf(e.msg);
            },
        },
        value:  [
            makeInput({type: 'hidden', name: 'cartids', value: '{cartids}'}),
            makeInput({type: 'hidden', name: 'boxids', value: '{boxids}'}),
            makeInput({type: 'hidden', name: 'coupondid', value: '0'}),
            makeDiv({
                'data': '{goodsList}',
                'class': 'bg-warning',
                'style': 'color: #333; font-size: 0.3rem; padding: .2rem;',
                show: '{{this.length} ==0}',
                value: '您购物车为空'
            }),
            makeList({
                'data': '{goodsList}',
                'show': '{{this.length} >0}',
                'class': 'list-group',
                li: {
                    'data': '{goodsInfo}',
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
                                        value: '仓口:{device_no}'
                                    }),
                                    makeDiv({
                                        style: 'color: #fcbd20;',
                                        value: '￥{price}'
                                    }),
                                    makeDiv({
                                        style: 'position:absolute; right: 0.5rem; top: 0.5rem;',
                                        value: 'x{sku}'
                                    }),
                                ]
                        })
                    ]
                }
            }),
            makeDiv({
                'class': "row",
                'style': "margin-top: 0.18rem;padding-bottom: 2rem;",
                value: [
                    makeDiv({
                        'style': "padding: 0.15rem; position: relative;background-color: #fff;",
                        click: function() {
                            var couponBox = makeDiv({
                                data_from: {
                                    url: getCouponUrl,
                                    success_key: 'code',
                                    success_val: '1',
                                    data_key: 'data.list',
                                },
                                'class': "container",
                                value: [
                                    makeDiv({
                                        'class': "row",
                                        'style': "border-top: 0.01rem solid #ccc;padding: 0.11rem 0;background-color: #fafafa;",
                                        value: [
                                            makeSpan({
                                                'class': 'col-xs-4',
                                                'style': "color: #555; font-size: 0.3rem;padding-left: 0.2rem;",
                                                value: '取消',
                                                click: function () {
                                                    hideNewBox();
                                                }
                                            }),
                                            makeSpan({
                                                'class': 'col-xs-4',
                                                'style': "color: #aaa; font-weight: bold; font-size: 0.28rem;text-align: center;",
                                                value: '选择优惠券'
                                            }),
                                            makeSpan({
                                                'class': 'col-xs-4',
                                                'style': "color: #555; font-size: 0.3rem;text-align: right;padding-right: 0.4rem;",
                                                value: '确定',
                                                click: function () {
                                                    hideNewBox();
                                                }
                                            })
                                        ],
                                    }),
                                    makeDiv({
                                        show: '{{this.length}==0}',
                                        value: '您没有优惠券'
                                    }),
                                    makeTable({
                                        show: '{{this.length}>0}',
                                        tr_1: {
                                            style: 'border-top: 0.01rem solid #dedede;',
                                            td: [
                                                {width: '0.5rem'},
                                                {width: 'auto'},
                                                {width: '1.6rem'},
                                                {width: '0.6rem'},
                                            ]
                                        },
                                        tr:{
                                            style: 'border-bottom: 0.01rem solid #dedede;',
                                            td: [
                                                {
                                                    style: 'padding: 0.2rem 0;',
                                                    value: makeSpan({
                                                            value: makeImg({
                                                                width: '0.35rem',
                                                                src: '/assets/wap/img/coupon.png',
                                                            })
                                                        })
                                                }, {
                                                    style: 'padding: 0.2rem 0;',
                                                    value: makeSpan({
                                                        value:  '{title}'
                                                    })
                                                }, {
                                                    style: 'padding: 0.2rem 0;',
                                                    value: makeSpan({
                                                        value:  '{money_title}'
                                                    })
                                                }, {
                                                    style: 'padding: 0.2rem 0;',
                                                    value: makeSpan({
                                                        value:  makeChecked({
                                                            value: '{id}',
                                                            'class': 'checked_warning',
                                                            name: 'chose_couponid',
                                                            single: 'couponid',
                                                            checked: '{'+ coupondid.value +' == {couponid}}',
                                                            data_title: '{money_title}',
                                                            click: function (o_, e, scope) {
                                                                coupondid.value = o_.value;
                                                                if(o_.checked) {
                                                                    scope['coupon_title'] = '('+ o_.attr('data_title') +')';
                                                                } else {
                                                                    scope['coupon_title'] = '';
                                                                }
                                                            }
                                                        })
                                                    })
                                                }
                                            ]
                                        },
                                    })
                                ]
                            });
                            msgWinHalf(couponBox, 0, 0.3, {bg:1});
                        },
                        value: [
                            makeSpan({
                                margin_left: '0.2rem',
                                value: makeImg({
                                    width: '0.45rem',
                                    src: '/assets/wap/img/coupon.png',
                                })
                            }),
                            makeSpan({
                                'style': "color: #555; font-size: 0.28rem;",
                                margin_left: '0.2rem',
                                value: '优惠券'
                            }),
                            makeSpan({
                                'class': "jt_right",
                                margin_left: '.2rem',
                                value: ''
                            })
                        ]
                    }),
                ]
            }),
            makeDiv({
                data: '{count}',
                'style': "position: fixed; bottom: -0.02rem;left: 0;background-color: #fff; border-top:1px solid #dedede; width: 100%; height: 1rem;",
                value: [
                    makeSpan({
                        'style': "background-color: #edca9d;height: 88%; margin-top: 0.05rem; text-align: center;position: absolute; left: 0.2rem;top:0; border-radius: .6rem;padding: 0.15rem;",
                        value: [makeImg({
                            src: '/assets/wap/img/cart.png',
                            height: '100%'
                        }),
                            makeSpan({
                                'style': "background-color: #ff0000;font-size: 0.22rem; color: #fff; margin-top: 0.05rem; text-align: center;position: absolute; right: 0;top: -0.05rem; border-radius: .6rem;line-height: 0.3rem;padding: 0 0.08rem;",
                                value: '{num}'
                            })]
                    }),
                    makeSpan({
                        'style': "margin-left: 1.5rem;height: 100%;line-height: 0.9rem;",
                        value: [makeSpan({
                            value:"共<span style='color: #ff9f10;'>￥{money}</span>"
                        }),makeSpan({
                            'style': "margin-left: 0.2rem;",
                            value:"{{coupon_title}}"
                        })]
                    }),
                    makeBtn({
                        'style': "background-color: #edca9d;color:#000;font-size: 0.4rem; text-align: center;position: absolute; right: 0;top:0; border: 0; line-height: 1rem; height: 100%; padding: 0 0.5rem;",
                        value: '去结算',
                        type: 'submit'
                    })
                ]
            })
        ]
    });
    contentObj.appendBody(bodyObj);
    setGlobalData('coupon_title', '');
    contentObj.addBody('');
    document.title = '确认订单';

    //socket事件

    var windowSocket = global__.socketObject || null;
    //socket操作对象
    var socketObj = {
        wsUrl: global__.wapCfg.socketUrl,
        secrectAesKey: global__.wapCfg.secrectAesKey,
        tips_erroring: false, //是否正在提示异常
        socketConnection: null, //实例化连接
        isReLinkSocket: false,     //是否正在重连Socket
        socketLinkErrMaxSize: 10, //初始化异常最大次数
        socketLinkErrSize: 0, //socket服务链接错误次数达到一定量之后报错
        connectBtnHtml: '未链接socket', //初始化异常次数
        // connectBtnHtml: '未链接！请确定电脑的网络链接是否正常', //初始化异常次数
        send: function (sendData, callBack) { //socket统一发送方法
            //console.log("sendData------------------>");
            //console.log(sendData);
            if (!socketObj.socketConnection) {
                msgTisf('logout');
                return;
            }
            var callBackStr = '';
            if (callBack) {
                callBackStr = ',"callback": "' + callBack + '"';
            }
            socketObj.socketConnection.send(
                '{' +
                '"encrypted": "'+ aesEncode(sendData, socketObj.secrectAesKey) +'"' +
                callBackStr + '}'
            );
            console.log("sendData------------------>");
            console.log(sendData);
        },
        //连接成功
        success: function () {
            this.socketLinkErrSize = 0;
            // msgTisf('连接成功！');
        },
        //登录socket
        loginSocket: function () {
            this.send(JSON.stringify({"ctrl": "buyer","cmd": "buyer_login"}));
        },
        //重连socket
        restScoket: function () {
            // console.log('正在重链接...' + this.socketLinkErrSize);
            this.isReLinkSocket = true;
            // msgTisf('正在重链接...');
            this.conneSocket();
        },
        //连接socket
        socketLoad: function () {
            this.socketLinkErrSize = 0;
            if (!this.socketConnection && !this.isReLinkSocket) {
                this.conneSocket();
            }
        },
        //socket异常处理
        socketErrTips: function () {
            //防止error和close同时请求
            if (this.tips_erroring) {
                return;
            }
            this.tips_erroring = true;
            //超出异常次数
            if (this.socketLinkErrSize >= this.socketLinkErrMaxSize) {
                msgTisf("超出次数" + this.socketLinkErrSize);
                // msgTisf(this.connectBtnHtml);
                socketObj.tips_erroring = false; //提示完成
            } else {
                this.socketLinkErrSize++;
                var stimer = setTimeout(function () {
                    socketObj.restScoket();
                    socketObj.tips_erroring = false; //提示完成
                }, 2000);
                msgTisf(this.connectBtnHtml);
            }
        },
        //链接Socket服务
        conneSocket: function () {
            if (this.socketConnection) {
                return false;
            }
            //console.log("111");
            this.socketConnection = new WebSocket(this.wsUrl);
            this.socketConnection.onopen = function () {
                //链接成功清除异常信息
                socketObj.success();
                socketObj.loginSocket();
                //发送用户信息确认登录系统，需发送token验证
                // console.log('onopen_login');
            };
            this.socketConnection.onmessage = function (msg) {
                var msgDataString = msg.data;
                if (msgDataString == "1") {
                    socketObj.socketConnection.send('2');
                    return false;
                } else {
                    // console.log(msg);
                    msgTis(msg.data);
                }
            };
            this.socketConnection.onerror = function (event) {
                // console.log('连接失败！');
                // console.log(event);
                socketObj.socketConnection = null;
                socketObj.socketErrTips();
            };
            this.socketConnection.onclose = function (event) {
                // console.log('onclose');
                // console.log(event);
                socketObj.socketConnection = null;
                socketObj.socketErrTips();
            }
        }
    };
    //连接socket
    if(!windowSocket) {
        // console.log('connect socket');
        socketObj.socketLoad();
        global__.socketObject = socketObj;
    }
});