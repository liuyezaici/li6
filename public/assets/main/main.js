(function (win) {
    'use strict';
    require.config({
        baseUrl: '/assets/libs/lr/ele/',
        paths: {
            jquery: '/assets/libs/jquery/dist/jquery-2.2.1.min',
            'front': '/assets/index/front',
            lrBox: 'https://js.li6.cc/assets/libs/lr/box.ver/lrBox.1.1',
            'slimscroll': '/assets/libs/jquery-slimscroll/jquery.slimscroll',
        },
        shim: {
            'slimscroll': {
                deps: ['jquery'],
                exports: '$.fn.extend'
            },
        }
    });
    require(['jquery', 'lrBox', 'front', 'slimscroll'], function ($, lrBox, front, slimscroll) {
        var resizeBox = $("#resizeBox");
        var topBar = resizeBox.find("#topBar");
        topBar = null;
        var hashChangeFromUs = false;//hash改变的执行者
        var leftBar = resizeBox.find(".leftBar");
        var mainFrame = resizeBox.find(".mainFrameContainer .inner");

        //获取页面hash
        function gethash(){
            return window.location.hash.replace(/^#/,"");
        }

        mainFrame.slimScroll({
            width: '100%', //可滚动区域宽度
            height: '100%', //可滚动区域高度
            size: '4px', //滚动条宽度，即组件宽度
            color: '#888', //滚动条颜色
            position: 'right', //组件位置：left/right
            distance: '1px', //组件与侧边之间的距离
            start: 'top', //默认滚动位置：top/bottom
            opacity: .4, //滚动条透明度
            alwaysVisible: false, //是否 始终显示组件
            disableFadeOut: true, //是否 鼠标经过可滚动区域时显示组件，离开时隐藏组件
            railVisible: false, //是否 显示轨道
            railColor: '#333', //轨道颜色
            railOpacity: .2, //轨道透明度
            railDraggable: true, //是否 滚动条可拖动
            railClass: 'slimScrollRail', //轨道div类名
            barClass: 'slimScrollBar', //滚动条div类名
            wrapperClass: 'slimScrollDiv', //外包div类名
            allowPageScroll: true, //是否 使用滚轮到达顶端/底端时，滚动窗口
            wheelStep: 20, //滚轮滚动量
            touchScrollStep: 200, //滚动量当用户使用手势
            borderRadius: '7px', //滚动条圆角
            railBorderRadius: '7px' //轨道圆角
        });

        leftBar.find('.cmdBtn').click(function (e) {
            e.preventDefault();
            var btn = $(this);
            var cmd = btn.attr('data-cmd');
            hashChangeFromUs = true;
            front.loadPage(cmd, btn, function () {
                hashChangeFromUs = false;
            });

        });
        var winHash = win.location.hash;
        if(winHash) {
            winHash = gethash();
            if(winHash) {
                front.loadPage(winHash);
            }
        }
        window.onhashchange = function() {
            if(hashChangeFromUs) return;
            var hash = gethash();
            if(!hash) {
                window.location.reload();
                return;
            }
            var lastUrl;
            if(hash.indexOf(',') != -1) {
                var hashArray_ = hash.split(',');
                lastUrl = hashArray_[hashArray_.length-1];
            } else {
                lastUrl = hash;
            }
            front.loadPage(lastUrl);
        };

        return {};
    });
})(this);
