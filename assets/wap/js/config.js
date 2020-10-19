(function (global__, $) {
    global__.wapCfg = {
        defaultRouter : 'mapSearch' // 默认路由
        ,wapBodyId: 'lr_body'  //定义 页头 页中 页脚 的id
        ,wapTopId: 'lr_top_nav'
        ,wapMainBodyId: 'lr_main_body'
        ,wapFooterId: 'lr_footer'
        ,checkBindMobile: '/adppp/usercenter/api/user/checkIsbangMobile/'   //检测是否绑定手机
        ,getSmsToLoginApi: '/adppp/sms/api/index/send/'   //获取短信
        ,submitBindMobileUrl: '/adppp/usercenter/api/user/changemobile'   //修改手机
        ,loginTimeOutCode: '10000'   //未登录的提示代码
        ,tokenKey: 'token'   //未登录的提示代码
        ,socketUrl: ''   //socketUrl 连接设备socket的url
        ,secrectAesKey: 'l~!*(){]Uvs<2>.r'   //aes加密密钥
        ,mustBindMobile: true  //是否强制买家绑定手机
        ,checkLoginUrl: '/index/System/checklogin'  //补货员 检测登录的url
        ,logoutUrl: '/index/System/logout'  //退出登录
    };
    //可以在刷新时继承全局token
    //可以在刷新时继承全局socketurl
    var autoSocketUrl = $("meta[name='socketUrl']").attr('content');
    if(autoSocketUrl.length>0) {
        global__.wapCfg.socketUrl = autoSocketUrl;
    }
    //公共全局方法
    global__.wapPubFunc = {
        tokenPost: function(options) {
            options = options || {};
            if(!options['post_data']) options['post_data'] = {};
            options['post_data']['token'] = global__.userToken;
            postAndDone(options);
        },
        //获取微信openid
        getWeixinOpenid: function() {
            //如果已经登录 直接检测是否绑定手机
            // msg('urlcode:'+ getUrlParam('code'));
            if (global__.userToken) {
                // msgTis('has_login:'+ global__.userToken);
                if(global__.wapCfg.mustBindMobile) global__.wapPubFunc.ifBindMobile();
                return;
            }
            // if(global__.uip.indexOf('127.0') || global__.uip.indexOf('192.168')) {
            //     return;
            // }
            if (getUrlParam('code')) {
                var weixinCode = getUrlParam('code');
                // msgTis('weixinCode:'+ weixinCode);
                global__.wapPubFunc.tokenPost({
                    post_url: '/admin/addon/usercenter/api/user/thirdlogin',
                    post_data: {
                        code: weixinCode,
                        platform: 'wechat'
                    },
                    success_key: 'code',
                    success_value: '1',
                    success_func: function (res) {
                        global__.openid = res.openid;
                        global__.userToken = res.userToken;
                        if(global__.wapCfg.mustBindMobile) global__.wapPubFunc.ifBindMobile();
                    },
                    err_func: function (res) {
                        msg('login_error:'+ res.msg);
                    }
                });
            } else {
                window.location.replace('https://open.weixin.qq.com/connect/oauth2/authorize?appid='+ weixin_appid +'&redirect_uri=' + $.url.encode(window.location.href) + '&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect')
            }
        },
        checkLogin : function () {
            //第一步先微信登录
            global__.wapPubFunc.getWeixinOpenid();
        },
        //检测是否绑定手机
        ifBindMobile : function () {
            global__.wapPubFunc.tokenPost({
                url: global__.wapCfg.checkBindMobile,
                success_key: 'code',
                success_value: '1',
                success_func: function (e) {

                },
                err_func: function (e) {
                    global__.wapPubFunc.gotoBindMobile();
                    msgTisf(e.msg);
                },
            })
        }
        //检测是否登录
        ,isLogOut: function(code) {
            return global__.wapCfg.loginTimeOutCode == code;
        }
        //退出登录
        ,logOut: function() {
            postAndDone({
                url: global__.wapCfg.logoutUrl,
                success_key: 'code',
                success_val: '1',
                success_func: function () {
                    msgTisf('退出成功');
                },
            });
        }
        //提示绑定手机
        ,gotoBindMobile: function () {
            var diyForm = makeForm({
                'style': 'width: 90%;margin:0 auto 0.5rem auto;',
                'type': 'post',
                'url' : wapCfg.submitBindMobileUrl,
                value:  [
                    makeInput({type: 'hidden', name: 'event', value: 'changemobile'}),
                    makeTable({
                        tr_1: [{
                            id: 'account_tr',
                            td: [
                                {
                                    padding_top: '10px',
                                    value: [
                                        makeInput({
                                            name: 'new_mobile',
                                            size: 'lg',
                                            'class': 'btn-block no_radius_right' ,
                                            place: '您的手机',
                                            type: 'text',
                                            limit: 'int',
                                            maxlen: 11,
                                            value: '',
                                            null_func: function () {
                                                msgTisf('请输入您的手机');
                                            }
                                        })
                                    ]
                                }
                                ,{
                                    padding_top: '10px',
                                    value:  makeBtn({value:'获取短信',
                                        size: 'lg',
                                        'class': 'btn btn-default btn-block no_radius_left',
                                        rest_time: 0,
                                        click: function (obj) {
                                            var newTel = new_mobile.value;
                                            if (!newTel) {
                                                msgTisf('请输入您的手机');
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
                                                console.log(obj);
                                                obj.subTime(60);
                                            },
                                            err_func: function (e) {
                                                msgTis(e.msg);
                                            }
                                        });
                                        }
                                    })
                                }
                            ]
                        },{
                            td: [
                                {
                                    padding_top: '10px',
                                    value: makeInput({
                                        name:'code',
                                        size: 'lg',
                                        'class': 'btn-block no_radius_right',
                                        place:'短信验证码', type: 'text',
                                        limit: 'int', maxlen: 6,
                                        value: '',
                                        null_func: function () {
                                            msgTis('验证码呢？');
                                        }
                                    })
                                },
                                {
                                    padding_top: '10px',
                                    value: makeBtn({
                                        type:'submit',
                                        size: 'lg',
                                        value:'绑定',
                                        'class': 'btn btn-info btn-block no_radius_left'
                                    })
                                }
                            ]
                        } ]
                    })],
                success_key: 'code',
                success_value: '1',
                success_func: function (data) {
                    hideNewBox();
                    msgTisf(data.msg);
                },
                err_func: function (data) {
                    msgTisf(data.msg);
                }
            });
            msgWin('请先绑定手机', diyForm, 400, 1,{
                bg: true,//背景遮挡
                'class': 'new_loginbox',
                'canDrag': false,
                'closeBtn': false
            });
        }
    };
})(this, jQuery);