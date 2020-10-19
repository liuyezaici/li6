(function(global__) {
    var getStationUrl = '/adppp/station/api/bhy/getBhyStation/' ;
    var viewStationUrl = '/wap#buhuoCupboards?id=';
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
            url: getStationUrl,
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
                value: '管理场地:',
            }),
            makeList({
                'data': '{stationList}',
                'class': 'list-group',
                show: '{{this.length} >0}',
                li: {
                    'class': 'list-group-item',
                    value: makeA({
                        value: '{station_title}',
                        href: viewStationUrl + '{stationid}'
                    })
                }
            }),
            makeDiv({
                'data': '{stationList}',
                'class': 'bg-warning',
                'style': 'color: #333; font-size: 0.2rem; padding-left: .2rem;',
                show: '{{this.length} ==0}',
                value: '您没有分配场地'
            })
        ]
    });
    contentObj.appendBody(bodyObj);
    document.title = '维护的场地';
});