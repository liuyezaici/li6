(function () {
    'use strict';
    require.config({
        baseUrl: 'js/',
        paths: {
            jquery: '/resource/pub/js/jq/jquery-3.2.1',
            lrBox: '/assets/libs/lr/jquery-lr_box',
        }
    });
    //创建一个按钮
    require(['jquery', 'core', 'lrBox'], function ($, core, lrBox) {
        var global = {};
        var diyBtn = core.makeBtn({
            'class': 'btnLr btnLrSm btnLrSuccess',
            value: '按钮',
            click: function (obj_) {
                lrBox.msgTis('哈喽');
            },
        });

        $('#show_btn').append(diyBtn);
        return global;
    });
})();



