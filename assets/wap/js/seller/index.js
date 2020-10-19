(function(global__) {
    //登录框
    contentObj.appendHeader('');
    //登录框
    //js造一个简单的表单
    var postDa = {
    };
    postDa[global__.wapCfg.tokenKey] = global__.userToken;
    var checkLoginDiv = makeDiv({
        data_from: {
            url: global__.wapCfg.checkLoginUrl,
            post_data: postDa,
            success_key: 'code',
            success_val: [0, 1],
            success_func: function (res) {
                if(res.code==1) {
                    msgTisf('您已经登录');
                    setTimeout(function () {
                        location.hash = '#buhuoStation';
                    }, 1000);
                } else {
                    msgTisf('您未登录');
                    global__.wapPubFunc.getWeixinOpenid();
                    return;
                }
            },
            err_func: function (e) {
                msgTisf(e.msg);
            },
        },
        value: '登录状态:{msg}'
    });
    contentObj.appendBody(checkLoginDiv);
});