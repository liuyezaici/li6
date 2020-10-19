(function(global__) {
    var getCupboardUrl = '/adppp/station/api/bhy/getBhyCupboardGoods/id/' ;
    var buhuoApi = '/adppp/cupboardgoods/api/index/buhuo/id/' ;
    var cupboardId = getUrlParam('id');
    //头部
    var header = makeDiv({
        'id': 'navigation',
        value: makeDiv({
            'class': 'navbar navbar-default',
            padding_bottom: '0.2rem',
            margin_bottom: '0',
            value: [
                makeDiv({
                    'class': 'navbar-header',
                    value: [makeSpan({
                        'class': 'navbar-brand',
                        'max-width': '120px',
                        value: makeImg({
                            'class': 'navbar-brand',
                            padding: 0,
                            src: '/assets/wap/img/station.png'
                        })
                    }),makeDiv({
                        'class': 'top_title',
                        value: '商品补货'
                    })]
                })
            ]
        })
    });
    // console.log(header);
    contentObj.appendHeader(header);

    //登录框
    //js造一个简单的表单
    var postDa = {
    };
    postDa[global__.wapCfg.tokenKey] = global__.userToken;

    var bodyObj = makeDiv({
        'line_height': '0.5rem',
        data_from: {
            url: getCupboardUrl + cupboardId,
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
                value: '柜子【{cupboardTitle}】的商品:',
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
                                        value: '库存:{sku}'
                                    }),
                                    makeDiv({
                                        value: '仓口:{device_no}'
                                    }),
                                    makeDiv({
                                        value: '价格:￥{price}'
                                    }),
                                    makeBtn({
                                        style: 'position: absolute; right: 0.2rem; top: 0;',
                                        data_gid: '{id}',
                                        data_boxid: '{device_no}',
                                        value: '补货',
                                        disabled: '{{sku}>0}',
                                        'class': 'btn btn-sm {{sku}==0 ? " btn-info":" btn-default"}',
                                        click: function (obj_, e) {
                                            var postDa = {
                                                'cupboardid': cupboardId
                                            };
                                            postDa[global__.wapCfg.tokenKey] = global__.userToken;
                                            var gid = obj_.attr('data_gid');
                                            var boxid = obj_.attr('data_boxid');
                                            global__.wapPubFunc.tokenPost({
                                                post_url: buhuoApi + gid,
                                                post_data: postDa,
                                                success_key: 'code',
                                                success_value: '1',
                                                success_func: function (e) {
                                                    // msgTis(e.msg);
                                                    var boxid_ = parseInt(boxid);
                                                    if(!boxid_) {
                                                        msgTis('请输入格子编号');
                                                        return;
                                                    }
                                                    var dataStr = {
                                                        'cupboardId': cupboardId,
                                                        'boxId': boxid_
                                                    };
                                                    window.socketObject.send(JSON.stringify({"ctrl": "bhy","cmd": "opendoor","data": dataStr}))
                                                },
                                                err_func: function (e) {
                                                    msgTis(e.msg);
                                                }
                                            });
                                        }
                                    })
                                ]
                        })
                    ]
                }
            }),
            makeDiv({
                'data': '{goodsList}',
                'class': 'bg-warning',
                'style': 'color: #333; font-size: 0.2rem; padding-left: .2rem;',
                show: '{{this.length} ==0}',
                value: '柜子没有分配商品'
            })
        ]
    });
    contentObj.appendBody(bodyObj);
    document.title = '柜子补货';
    var buhuoSuccessFunc = function () {
        bodyObj.renewData();
    };

    var windowSocket = window.socketObject || null;
    $(function () {
        //socket操作对象
        var socketObj = {
            wsUrl: global__.wapCfg.socketUrl,
            secrectAesKey: global__.wapCfg.secrectAesKey,
            tips_erroring: false, //是否正在提示异常
            socketConnection: null, //实例化连接
            isReLinkSocket: false,     //是否正在重连Socket
            socketLinkErrMaxSize: 10, //初始化异常最大次数
            socketLinkErrSize: 0, //socket服务链接错误次数达到一定量之后报错
            connectBtnHtml: '未连接设备', //初始化异常次数
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
            },
            //连接成功
            success: function () {
                this.socketLinkErrSize = 0;
                msgTisf('连接成功！');
            },
            //登录socket
            loginSocket: function () {
                this.send(JSON.stringify({"ctrl": "bhy","cmd": "bhy_login"}));
            },
            //重连socket
            restScoket: function () {
                console.log('正在重链接...' + this.socketLinkErrSize);
                this.isReLinkSocket = true;
                msgTisf('正在重链接...');
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
                    //console.log("超出次数" + this.socketLinkErrSize);
                    msgTisf(this.connectBtnHtml);
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
                        var data = JSON.parse(msgDataString);
                        var flag = data.flag || '';
                        if(flag =='buhuo_success') {
                            buhuoSuccessFunc();
                        }
                        msgTis(data.msg);
                    }
                };
                this.socketConnection.onerror = function (event) {
                    console.log('连接失败！');
                    console.log(event);
                    socketObj.socketConnection = null;
                    socketObj.socketErrTips();
                };
                this.socketConnection.onclose = function (event) {
                    console.log('onclose');
                    console.log(event);
                    socketObj.socketConnection = null;
                    socketObj.socketErrTips();
                }
            }
        };
        //连接socket
        if(!windowSocket) {
            // console.log('connect socket');
            socketObj.socketLoad();
            window.socketObject = socketObj;
        }
    });
});