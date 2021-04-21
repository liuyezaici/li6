require.config({
    paths: {
        jquery: '/resource/pub/js/jq/jquery-3.2.1',
        lrBase: '/assets/libs/lr/jquery-lr_base',
        lrBox: 'https://js.li6.cc/assets/libs/lr/box.ver/lrBox.1.1',
        lrEle: '/assets/libs/lr/jquery-lr_element',
        webuploader: '/assets/libs/webuploader/webuploader',
    }
});
require(['jquery', 'lrEle', 'lrBox', 'lrBase', 'webuploader'], function ($, lrEle, lrBox, lrBase, webuploader) {

    //ajax绑定hash事件
    function ajaxBindWindowHash() {
        var this_ = this;
        var hashChangeFromUs = false;//hash改变的执行者
        //获取页面hash
        this.gethash = function(){
            return window.location.hash.replace(/^#/,"");
        };
        window.onhashchange = function() {
            if(hashChangeFromUs) return;
            var hash = this_.gethash();
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
            lastUrl = $.url.decode(lastUrl);
            ajaxOpen(lastUrl);
        };
        //ajax打开页面
        window.ajaxOpen = function(url) {
            if(!url) {
                msg('url不能为空');
                return;
            }
            url = url.replace(/\s/g,'%20');
            //去掉hash
            if(url.indexOf('#') != -1) {
                url = url.split('#')[0];
            }
            //重新计算页面的hash值
            hash = encodeURIComponent(url);
            window.location.hash = hash;
            hashChangeFromUs = true;
            var gotoUrl = url;
            $('#root_right').find('.right_content').load(gotoUrl,function() {
                lrBox.noLoading();
                hashChangeFromUs = false;
            });
            lrBox.loading();
            var topMenu = $('#header').find('.nav .menu');
            topMenu.find('.active').removeClass('active');
            $('#new_left').removeAttr('style');
            if(url.indexOf('noscroll') == -1) {
                $('body,html').stop().animate({scrollTop:0},500);
            }
        };

        //检测url的hash 自动跳转
        var hash = this_.gethash();
        if(!hash) return;
        var lastUrl;
        if(hash.indexOf(',') != -1) {
            var hashArray_ = hash.split(',');
            lastUrl = hashArray_[hashArray_.length-1];
        } else {
            lastUrl = hash;
        }
        lastUrl = $.url.decode(lastUrl);
        ajaxOpen(lastUrl);
    }
//左边特效
    function leftEvent(className) {
        var leftMenu = $('#left_menu');
        if(className){
            leftMenu.find('.active').removeClass('active')
            leftMenu.find('li').each(function(e) {
                var _this = $(this);
                if(_this.hasClass(className)){
                    _this.addClass('active');
                }
            });
        }
    }

//后台首页云层特效
    var mouseWasOnBg = false;
    function cloudEven() {
        var eventX, objLeft, downPosX, changePosX,bgPosX;
        var bg = $('#user_center_box');
        objLeft = bg.offset().left;
        if(!lrBox.isPc()) {//wap端
            bg[0].addEventListener('touchstart', function (e) {
                mouseWasOnBg = true;
                e = e.touches[0];
                downPosX = e.clientX - objLeft;
                bgPosX = parseFloat(bg.css('background-position-x'));
                $(document)[0].addEventListener('touchmove', function (evt) {
                    var touch = evt.touches[0];
                    if(!mouseWasOnBg) return;
                    eventX = touch.clientX - objLeft;
                    changePosX = (eventX - downPosX);
                    downPosX += changePosX;//开始的x位置也要随着背景移动而释放  否则会移动越来越快
                    bgPosX += changePosX;
                    if(bgPosX < -2030) bgPosX = -2030; //图片宽度3000 - 页面宽1000 +30 padding
                    if(bgPosX > 0) bgPosX = 0;
                    bg.css('background-position-x', bgPosX);

                });
                $(document)[0].addEventListener('touchend', function () {
                    if(mouseWasOnBg) {
                        mouseWasOnBg = false;
                        downPosX = 0;
                        bg.removeClass('move');
                        lrEle.rePost('/user/index/save_bg_pos', {bg_x: bgPosX} , function (res) {
                            if(res.msg) {
                                lrBox.msgTisf(res.msg);
                            }
                        });
                    }
                });
            });

        } else {
            bg.off().on({
                'mousedown': function (event) {
                    mouseWasOnBg = true;
                    downPosX = event.clientX - objLeft;
                    bg.addClass('move');
                    bgPosX = parseFloat(bg.css('background-position-x'));
                },
                'mouseup': function (event) {
                    if(mouseWasOnBg) {
                        mouseWasOnBg = false;
                        downPosX = 0;
                        bg.removeClass('move');
                        lrEle.rePost('/user/index/save_bg_pos', {bg_x: bgPosX} , function (res) {
                            if(res.msg) {
                                lrBox.msgTisf(res.msg);
                            }
                        });
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
                    if(bgPosX < -2030) bgPosX = -2030; //图片宽度3000 - 页面宽1000 +30 padding
                    if(bgPosX > 0) bgPosX = 0;
                    bg.css('background-position-x', bgPosX);
                }
            });
        }

    }

    $(document).ready(function(){
        //子页面跳转
        var url = window.location.toString();
        var newurl = '';
        if (url.indexOf('&goto=') !== -1) {
            newurl = url.split('&goto=');
            newurl = newurl[1];
            newurl = $.url.decode(newurl);
            if(newurl.substr(0,1) != '?') newurl = '?'+newurl;
            ajaxOpen(newurl);
        }
        cloudEven();////后台首页云层特效
        ajaxBindWindowHash(); //ajax绑定hash事件
    });

});




