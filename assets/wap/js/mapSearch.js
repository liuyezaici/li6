(function(global__) {
    var baiduMapUrl = '/Tool/index/map/?city=';
    //头部
    var header = makeForm({
        name: 'top_search_form',
        'class': 'map_navigation',
        'style': 'padding: 2% 0;',
        submit: function(form_, e) {
            console.log(form_);
            e.preventDefault();
            var datas = form_.getFormDatas();
            var enterCity = datas.address;
            // msgTis(enterCity);
            enterCity = $.trim(enterCity);
            window['search_map'].attr('src', baiduMapUrl + enterCity);
            return false;
        },
        value: makeInput({
                'name': 'address',
                'class': 'search_bar',
                'place': '输入您的地址',
                value: '',
                blur: function () {
                    top_search_form.trigger('submit');
                }
            })
    });
    // console.log(header);

    //登录框
    //js造一个简单的表单
    var bodyObj = makeDiv({
        'class': 'main_map',
        value:  [
            makeIframe({
                width: '100%',
                height: '100%',
                resize: function (obj_) {
                    obj_.css('height', $(window).outerHeight());
                },
                src: baiduMapUrl,
                name: 'search_map'
            }),
            makeImg({
                'name': 'getLocation',
                'class': 'getLocation',
                'src': '/assets/wap/img/local.png'
            })
       ]
    });
    contentObj.appendHeader(header);
    contentObj.appendBody(bodyObj);
    document.title = '搜索场地';
});