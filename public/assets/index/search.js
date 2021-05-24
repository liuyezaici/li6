(function () {
    'use strict';
    require.config({
        baseUrl: '/assets/libs/lr/ele/',
        paths: {
            jquery: '/assets/libs/jquery/dist/jquery',
            'front': '/assets/index/front',
            lrBox: 'https://js.li6.cc/assets/libs/lr/box.ver/lrBox.1.1',
        }
    });
    require(['jquery', 'lrBox', 'front'], function ($, lrBox, front) {
        var indexPage = $("#indexPage");
        var topSearchForm = indexPage.find(".topSearchForm");
        topSearchForm.find('.form-control').on('dblclick', function () {
            var input = $(this);
            if($.trim(input.val()) ==='') {
                input.val(input.attr('placeholder'));
            }
        });
        return {};
    });
})();
