(function () {
    'use strict';
    require.config({
        baseUrl: '/assets/libs/lr/ele/',
        paths: {
            jquery: '/resource/pub/js/jq/jquery-1.8.3.min',
            'front': '/tool/txt/src/front',
            lrBox: 'https://js.li6.cc/assets/libs/lr/box.ver/lrBox.1.1',
        }
    });
    require(['jquery', 'lrBox', 'front'], function ($, lrBox, front) {
        var indexPage = $("#indexPage");
        var loginForm = indexPage.find("#loginForm");
        indexPage.find('.delCookiesBtn').click(function () {
            front.deleteAllCookies();
        });
        indexPage.find('.forgotKeyBtn').click(function (e) {
            e.preventDefault();
            lrBox.closeAllBox();
            var title = $(this).attr('data-title');
            lrBox.msg(title, '谢谢',
                {fd:true, move: {dir: 'd', jl: '30%'}}
            );
        });
        indexPage.find('.aboutBtn').click(function (e) {
            e.preventDefault();
            lrBox.closeAllBox();
            var title = $(this).next().html();
            lrBox.msg(title, '知道了',
                {fd:true, move: {dir: 'd', jl: '20%'}}
            );
        });

        front.formSubmitEven(loginForm, {
            before: function (res) {
                if(!res['key']) {
                    lrBox.msgTisf('请先输入钥匙');
                    loginForm.find('#loginKey').focus();
                    return false;
                }
                lrBox.loading(true);
                res['key'] = front.md5(front.md5(front.md5(res['key'])));
                return res;
            },
            url: '/txtTool/index/login',
            successKey: 'code',
            successVal: 1,
            successFunc: function (res) {
                lrBox.noLoading();
                lrBox.hideNewBox();
                window.location= '/tool/txt/main';
            },
            errorFunc: function (res) {
                lrBox.noLoading();
                lrBox.msgTisf(res.msg);
            }
        });
        return {};
    });
})();
