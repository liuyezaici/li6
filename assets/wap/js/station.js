(function(global__) {
    var getStationUrl = '/adppp/station/api/index/details/' ;
    var viewCupboardUrl = '/wap#cupboard?id=';
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
                        value: '场地信息'
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
        'class': 'well',
        'line_height': '0.5rem',
        data_from: {
            url: getStationUrl + 'id/'+ getUrlParam('id'),
            data_key: 'data',
            post_data: postDa,
            success_key: 'code',
            success_value: '1',
            succ_func: function (e) {
                if(wapPubFunc.isLogOut(e.code)) {
                    wapPubFunc.getWeixinOpenid();
                    return;
                } else {
                    //如果已经登录 直接检测是否绑定手机
                    if (getStorage('userToken')) {
                        if(wapCfg.mustBindMobile) wapPubFunc.ifBindMobile();
                        return;
                    }
                }
                console.log(e);
            },
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
                value: '场地:【{title}】',
            }),
            makeDiv({
                value: '地址:{address}',
            }),
            makeDiv({
                value: '状态:{status_name}',
            }),
            makeDiv({
                value: '包含柜子:',
            }),
            makeDiv({
                'data': '{cupboardList}',
                'class': 'bg-warning',
                'style': 'color: #333; font-size: 0.2rem; padding-left: .2rem;',
                show: '{{this.length} ==0}',
                value: '场地没有分配柜子'
            }),
            makeList({
                show: '{{this.length} >0}',
                'data': '{cupboardList}',
                'class': 'list-group',
                li: {
                    'class': 'list-group-item',
                    value: makeA({
                        value: '{title}',
                        href: viewCupboardUrl + '{id}'
                    })
                }
            })
        ]
    });
    contentObj.appendBody(bodyObj);
    document.title = '场地详情';
});