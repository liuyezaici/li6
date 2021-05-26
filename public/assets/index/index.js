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
        var indexImg = indexPage.find('.login_pictures img');
        indexPage.find('.delCookiesBtn').click(function () {
            front.deleteAllCookies();
        });
        indexPage.find('.aboutMyTool').click(function (e) {
            e.preventDefault();
            lrBox.closeAllBox();
            var title = $(this).attr('data-title');
            lrBox.msg(title, '知道了',
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
        indexPage.find('.joinBtn').click(function (e) {
            e.preventDefault();
            lrBox.closeAllBox();
            var btn = $(this);
            var title = btn.html();
            var url = btn.attr('data-link');
            lrBox.msgT(title, '[url]'+ url,
                {fd:true, move: {dir: 'd', jl: '15%', from: btn, time: 500}, width: '400px'}
            );
        });
        var indexUrls = [
            '/assets/index/index.png',
            '/assets/index/index2.webp',
            '/assets/index/index3.gif',
            '/assets/index/index4.jpg',
            '/assets/index/index5.png',
            '/assets/index/index6.jpg',
        ];
        function changeBg() {
            var randomUrl = indexUrls[front.getRandomInt(0, 5)];
            indexImg.attr('src', randomUrl);
        }
        changeBg();
        indexImg.click(function () {
            changeBg();
        });
        return {};
    });
})();
