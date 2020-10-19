(function(global__) {
    var getArticleUrl = '/adppp/help/api/index/getArticleByKeyName/?keyname=company_desc' ;
    //登录框
    //js造一个简单的表单
    var bodyObj = makeTable({
        data_from: {
            url: getArticleUrl,
            data_key: 'data',
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
        tr_1: [{
            id: 'account_tr',
            td: {
                value: '{content}'
            }
        }]
    });
    contentObj.appendHeader('');
    contentObj.appendBody(bodyObj);
    document.title = '公司简介';
});