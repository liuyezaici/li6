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
        var bg = $('#user_center_box');

        leftBar.find('.cmdBtn').click(function (e) {
            e.preventDefault();
            var btn = $(this);
            var cmd = btn.attr('data-cmd');
            hashChangeFromUs = true;
            bg.hide();
            front.loadPage(cmd, btn, function () {
                hashChangeFromUs = false;
            });

        });
        var winHash = win.location.hash;
        if(winHash) {
            winHash = gethash();
            if(winHash) {
                bg.hide();
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


//后台首页云层特效
        var mouseWasOnBg = false;
        function cloudEven() {
            var eventX, objLeft, downPosX, changePosX,bgPosX;
            var imgWidth = 3000;
            var bg = $('#user_center_box');
            objLeft = bg.offset().left;
            var savePos = function (pos) {
                front.postAndDone({
                    url:  '/index/main/save_bg_pos',
                   postData: {bg_x: bgPosX} ,
                    successFunc: function  (res) {
                        if(res.msg) {
                            lrBox.msgTisf(res.msg);
                        }
                    }
                });
            };
            if(!lrBox.isPc()) {//wap端
                bg[0].addEventListener('touchstart', function (e) {
                    mouseWasOnBg = true;
                    e = e.touches[0];
                    downPosX = e.clientX - objLeft;
                    var imgWrapWidth = bg.width();
                    var masPosX = imgWidth - imgWrapWidth;
                    bgPosX = parseFloat(bg.css('background-position-x'));
                    $(document)[0].addEventListener('touchmove', function (evt) {
                        var touch = evt.touches[0];
                        if(!mouseWasOnBg) return;
                        eventX = touch.clientX - objLeft;
                        changePosX = (eventX - downPosX);
                        downPosX += changePosX;//开始的x位置也要随着背景移动而释放  否则会移动越来越快
                        bgPosX += changePosX;
                        if(bgPosX < -masPosX) bgPosX = -masPosX; //图片宽度3000 - 页面宽1000 +30 padding
                        if(bgPosX > 0) bgPosX = 0;
                        bg.css('background-position-x', bgPosX);

                    });
                    $(document)[0].addEventListener('touchend', function () {
                        if(mouseWasOnBg) {
                            mouseWasOnBg = false;
                            downPosX = 0;
                            bg.removeClass('move');
                            savePos(bgPosX);
                        }
                    });
                });

            } else {
                var imgWrapWidth = bg.width();
                var masPosX = imgWidth - imgWrapWidth;
                bg.off().on({
                    'mousedown': function (event) {
                        mouseWasOnBg = true;
                        downPosX = event.clientX - objLeft;
                        bg.addClass('move');
                        imgWrapWidth = bg.width();
                        masPosX = imgWidth - imgWrapWidth;
                        bgPosX = parseFloat(bg.css('background-position-x'));
                    },
                    'mouseup': function (event) {
                        if(mouseWasOnBg) {
                            mouseWasOnBg = false;
                            downPosX = 0;
                            bg.removeClass('move');
                            savePos(bgPosX);
                        }
                    },
                    'mouseout': function (event) {
                        mouseWasOnBg = false;
                        downPosX = 0;
                        bg.removeClass('move');
                    },
                    'mousemove': function (event) {
                        if(!mouseWasOnBg) return;
                        eventX = event.clientX - objLeft;
                        changePosX = (eventX - downPosX);
                        downPosX += changePosX;//开始的x位置也要随着背景移动而释放  否则会移动越来越快
                        bgPosX += changePosX;
                        if(bgPosX < -masPosX) bgPosX = -masPosX; //图片宽度3000 - 页面宽1000 +30 padding
                        if(bgPosX > 0) bgPosX = 0;
                        bg.css('background-position-x', bgPosX);
                    }
                });
            }
            $(window).resize(function () {
                var imgWrapWidth = bg.width();
                var masPosX = imgWidth - imgWrapWidth;
                var posX = bg.css('background-position-x');
                posX = Math.abs(parseFloat(posX));
                if(posX > masPosX) {
                    posX = -masPosX;
                    bg.css('background-position-x', posX);
                }
            });
        }

        cloudEven();////后台首页云层特效
        return {};
    });
})(this);
