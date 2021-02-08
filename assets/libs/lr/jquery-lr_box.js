define(['jquery'], function ($) {
    var global = {};
    //author: lirui
    var doc = document;
    var contentClass = 'lr_content';
    var contentClassGet = '.'+contentClass;
    function isPc(){
        var userAgentInfo = navigator.userAgent;
        var Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");
        var flag = true;
        for (var v = 0; v < Agents.length; v++) {
            if (userAgentInfo.indexOf(Agents[v]) > 0) { flag = false; break; }
        }
        return flag;
    }
    //清除鼠标选中的内容
    var clearSel = "getSelection" in window ? function () {
        window.getSelection().removeAllRanges();
    } : function () {
        document.selection.empty();
    };
    function isUndefined(variable) {return typeof variable == 'undefined' ? true : false;}
    //拖动事件 obj.Drag(son, '', '');
    $.fn.Drag = function (titleId, positionType, opt) {
        var box = this;
        //drag_up_func 设置回调参数,array, 0:回调函数,1:自定义对象1,2:自定义对象2
        //最终会回调执行 回调函数([x,y], obj1, obj2)
        var mousedownFunc = opt['mousedown'] || opt['mouseDown'] || null;
        var dragingFunc = opt['draging'] || null;
        var dragupFunc = opt['dragup'] || opt['dragUp'] || null;
        var minTop = opt && opt['min_top'] || 0;
        var maxTop = opt && opt['max_top'] || null;
        var setMaxTop = false;
        if(maxTop) setMaxTop  = true;
        var bottomDistance = opt && opt['bottom_distance'] || 0;//底部距离（空的li/tr）
        var minLeft = opt && opt['min_left'] || 0;
        var maxLeft = opt && opt['max_left'] || null;
        var parentBox = opt && opt['parent_box'] || null;
        var limitX = isUndefined(opt['limit_x']) ? true : opt['limit_x'];
        var limitY = isUndefined(opt['limit_y']) ? true : opt['limit_y'];

        var liHeight = 0;
        if(opt && !isUndefined(opt['liHeight'])) {
            liHeight = parseFloat(opt['liHeight']);
        } else if(opt && !isUndefined(opt['li_height'])) {
            liHeight = parseFloat(opt['li_height']);
        }
        if(maxLeft && maxLeft <minLeft) maxLeft = minLeft;
        var mousedownEventX = 0;
        var mousedownEventY = 0;
        var mousedownBoxTop = 0;
        var mousedownBoxLeft = 0;
        var isDraging = false;
        positionType = positionType || 'absolute';
        //禁止选择内容
        box.select(function (e) {
            if (isDraging) {
                e.preventDefault();
            }
        });
        box.find(contentClassGet).select(function (e) {
            if (isDraging) {
                e.preventDefault();
            }
        });
        var titleObj;
        var hasSetWidth = false;
        var beforeWidth = false;
        var hasSetBottom = false;
        var x=0,y=0,wd=box.outerWidth();
        //提升当前box的层级
        box.setIndexToMax = function() {
            if(!window.zIndex) window.zIndex = 1000;
            var globalZIndex = window.zIndex;
            var newZIndex = box.zIndex || 0;
            if(newZIndex < globalZIndex) {
                newZIndex = globalZIndex + 2;
                window.zIndex = newZIndex;
                box.zIndex = newZIndex;
            }
            var reCss = {
                'zIndex': newZIndex,
            };
            box.css(reCss);
        };
        if(!isPc()) {//wap端
            if(!titleId) return; //无标题 不需要绑定拖动事件
            if(typeof titleId == 'string') {
                if(box.find('.'+titleId).length !=1 ) return;
                titleObj = box.find('.'+titleId)[0];
            } else if(typeof titleId == 'object') {
                if(titleId[0]) {
                    titleObj = titleId[0];
                } else {
                    titleObj = titleId;
                }
            } else {
                titleObj = this;
            }
            titleObj.addEventListener('touchstart', function (e) {
                var parentWidth = box.parent().outerWidth();
                e = e.touches[0];
                clearSel();
                isDraging = true;
                var pos = box.position();
                hideLeft = $(doc).scrollLeft();
                hideTop = $(doc).scrollTop();
                mousedownEventX = e.pageX;
                mousedownEventY = e.pageY;
                mousedownBoxTop = pos.top;  //不能减去滚动的高度 否则对象的Y坐标会偏移
                mousedownBoxLeft = pos.left;
                if(!setMaxTop && parentBox) {
                    maxTop = parentBox.outerHeight() + parentBox.offset().top  - liHeight;
                }
                hasSetWidth = box.attr('style') && box.attr('style').toLocaleString().indexOf('width') !=-1;
                hasSetBottom = box.attr('style') && box.attr('style').toLocaleString().indexOf('bottom') !=-1;
                beforeWidth = parseFloat(box.outerWidth());
                if(bottomDistance> 0) {
                    maxTop -= bottomDistance;
                }
                if(mousedownFunc) {
                    var status =  mousedownFunc([mousedownBoxTop, mousedownBoxLeft], titleObj, e);
                    if(status === false) return;//可以手动停止拖拽事件
                }
                box.setIndexToMax();
                var reCss = {
                    left: mousedownBoxLeft,
                    top: mousedownBoxTop,
                    width: beforeWidth,
                    position: positionType,
                };
                box.css(reCss);
                var touchMoveEve = function (evt) {
                    window.event? window.event.returnValue = false : evt.preventDefault();//防止屏幕跟着滚动
                    var touch = evt.touches[0];
                    if (isDraging) {
                        var changeX = touch.pageX - mousedownEventX;
                        var changeY = touch.pageY - mousedownEventY;
                        x = mousedownBoxLeft + changeX;
                        y = mousedownBoxTop + changeY;
                        if(limitX) {
                            wd = box.outerWidth();
                            if((x + wd) > parentWidth - 2 ) {
                                x = (parentWidth - wd - 2) ;
                            } else if(x < minLeft) {
                                x = minLeft;
                            }
                            if(maxLeft) {
                                x = x > maxLeft ? maxLeft : x;
                            }
                        }
                        if(limitY) {
                            if(maxTop) y = y > maxTop ? maxTop : y;
                            if(positionType == 'absolute') {
                                if(y < minTop) {
                                    y = minTop;
                                    mousedownEventY = touch.pageY;
                                    mousedownBoxTop = box.position().top;
                                }
                            } else {
                                if(y < 0) {
                                    y = 0;
                                    mousedownEventY = touch.pageY;
                                    mousedownBoxTop = box.position().top;
                                }
                            }
                        }
                        var reCss = {
                            left: x,
                            top: y,
                            bottom: 'auto',
                            position: positionType,
                        };
                        reCss['width'] =  beforeWidth;
                        box.css(reCss);
                        if(dragingFunc) {
                            dragingFunc([x, y], titleObj, evt);
                        }
                    }
                };
                var touchEndEve = function (e) {
                    if (isDraging) {
                        if(dragupFunc) {
                            dragupFunc([x, y], titleObj, e);
                        }
                        //如果之前未设置过宽度 停止时要去掉width
                        if(!hasSetWidth) {
                            var newStyle = box.attr('style');
                            if(newStyle) {
                                newStyle = newStyle.replace(/width:\s*([^;]+);*/, '');
                                box.attr('style', newStyle);
                            }
                        }
                        //如果之前未设置过bottom 停止时要去掉bottom
                        if(!hasSetBottom) {
                            var newStyle = box.attr('style');
                            if(newStyle) {
                                newStyle = newStyle.replace(/bottom:\s*([^;]+);*/, '');
                                box.attr('style', newStyle);
                            }
                        }
                        isDraging = false;
                        mousedownEventX = 0;
                        mousedownEventY = 0;
                        mousedownBoxTop = 0;
                        mousedownBoxLeft = 0;
                        $(doc)[0].removeEventListener('mousemove', touchMoveEve);
                        $(doc)[0].removeEventListener('touchend', touchEndEve);
                    }
                };
                $(doc)[0].addEventListener('touchmove', touchMoveEve);
                $(doc)[0].addEventListener('touchend', touchEndEve);
            });
        } else {//pc端
            if(typeof titleId == 'string') {
                if(titleId) {
                    titleObj = box.find('.' + titleId);
                } else {
                    titleObj = this;
                }
            }  else if(typeof titleId == 'object') {
                titleObj = titleId;
            } else {
                titleObj = this;
            }
            var hideLeft = 0;
            var hideTop = 0;
            titleObj.mousedown(function (e) {
                var parentWidth = box.parent().outerWidth();
                e.preventDefault();
                clearSel();
                if(e.button == 2) return ;//右键无效
                isDraging = true;
                var pos = box.position();
                hideLeft = $(doc).scrollLeft();
                hideTop = $(doc).scrollTop();
                mousedownEventX = e.pageX;
                mousedownEventY = e.pageY;
                mousedownBoxTop = pos.top;  //不能减去滚动的高度 否则对象的Y坐标会偏移
                mousedownBoxLeft = pos.left;
                if(!setMaxTop && parentBox) {
                    maxTop = parentBox.outerHeight() + parentBox.offset().top  - liHeight;
                }
                hasSetWidth = box.attr('style') && box.attr('style').toLocaleString().indexOf('width') !=-1;
                hasSetBottom = box.attr('style') && box.attr('style').toLocaleString().indexOf('bottom') !=-1;
                beforeWidth = parseFloat(box.outerWidth());
                if(bottomDistance> 0) {
                    maxTop -= bottomDistance;
                }
                if(mousedownFunc) {
                    var status = mousedownFunc([mousedownBoxTop, mousedownBoxLeft], titleObj, e);
                    if(status === false) return;//可以手动停止拖拽事件
                }
                box.setIndexToMax();
                var reCss = {
                    left: mousedownBoxLeft,
                    top: mousedownBoxTop,
                    width: beforeWidth,
                    position: positionType,
                };
                box.css(reCss);
                var mousemoveEven = function (evt) {
                    if (isDraging) {
                        var changeX, changeY;
                        changeX =  evt.pageX - mousedownEventX;
                        changeY = evt.pageY - mousedownEventY;
                        x = mousedownBoxLeft + changeX;
                        y = mousedownBoxTop + changeY;
                        if(limitX) {
                            wd = box.outerWidth();
                            if((x + wd) > parentWidth - 2 ) {
                                x = (parentWidth - wd - 2) ;
                            } else if(x < minLeft) {
                                x = minLeft;
                            }
                            if(maxLeft) x = x > maxLeft ? maxLeft : x;
                        }
                        if(limitY) {
                            if(maxTop) y = y > maxTop ? maxTop : y;
                            if(positionType == 'absolute') {
                                if(y < minTop) {
                                    y = minTop;
                                    mousedownEventY = evt.pageY;
                                    mousedownBoxTop = box.position().top;
                                }
                            } else {
                                if(y < 0) {
                                    y = 0;
                                    mousedownEventY = evt.pageY;
                                    mousedownBoxTop = box.position().top;
                                }
                            }
                        }
                        var reCss = {
                            left: x,
                            top: y,
                            bottom: 'auto',
                            position: positionType,
                        };
                        reCss['width'] =  beforeWidth;
                        box.css(reCss);
                        if(dragingFunc) {
                            dragingFunc([x, y], titleObj, evt);
                        }
                    }
                };
                $(doc).bind('mousemove', mousemoveEven);
                var mouseupEven = function (e) {
                    if (isDraging) {
                        if(dragupFunc) {
                            dragupFunc([x, y], titleObj, e);
                        }
                        //如果之前未设置过宽度 停止时要去掉width
                        if(!hasSetWidth) {
                            var newStyle = box.attr('style');
                            if(newStyle) {
                                newStyle = newStyle.replace(/width:\s*([^;]+);*/, '');
                                box.attr('style', newStyle);
                            }
                        }
                        //如果之前未设置过bottom 停止时要去掉bottom
                        if(!hasSetBottom) {
                            var newStyle = box.attr('style');
                            if(newStyle) {
                                newStyle = newStyle.replace(/bottom:\s*([^;]+);*/, '');
                                box.attr('style', newStyle);
                            }
                        }
                        isDraging = false;
                        mousedownEventX = 0;
                        mousedownEventY = 0;
                        mousedownBoxTop = 0;
                        mousedownBoxLeft = 0;
                        $(doc).unbind('mousemove', mousemoveEven);
                        $(doc).unbind('mouseup', mouseupEven);
                    }
                };
                $(doc).bind('mouseup', mouseupEven);
            });
        }
    };
    var boxIDName = 'lr_move_box_';//定义统一的box id前缀
    var boxBgClass = 'lr_box_bg';//要和css的名字对应
    var dialogTopId = 'lr_box_top_bar';//标题栏的id
    var boxBarId = 'lr_box_btn_bar';//按钮栏的id
    var boxObjArray = []; //所有的box对象
    //获取最新的box对象
    var getLastBoxObj = function() {
        if(boxObjArray.length == 0) return '';
        var newMaxIndex = 0;
        var newBox = false;
        $.each(boxObjArray, function (n, obj_) {
            if(obj_.css('zIndex') > newMaxIndex) {
                newMaxIndex = obj_.css('zIndex');
                newBox = obj_;
            }
        });
        return newBox;
    };

    //定义背景是否正在消失，新的窗口打开时要检测背景是否正在消失，是的话立刻去掉背景
    var bgIsFadingOut = false;
    //获取box新的x、y坐标 以适应页面大小
    var getBoxXy = function (boxWidth, addTop) {
        var winWidth = parseFloat($(window).outerWidth(true));
        if(!isNaN(boxWidth) && boxWidth > winWidth) boxWidth = winWidth;
        //重置宽和高和坐标
        var y_ = getScrollY(addTop);
        var x_ = (winWidth / 2) - (parseFloat(boxWidth) / 2);
        if(x_<0) x_ = 0;
        return {x: x_, y:y_};
    };

    //不支持点击背景时，点背景会闪烁
    var dialogFlash = function() {
        var newBox = getLastBoxObj();
        if(!newBox) return;
        var toolbarId;
        if (newBox.find("."+ dialogTopId).length > 0) {
            toolbarId = dialogTopId;
        } else {
            toolbarId = boxBarId;
        }
        newBox.find("." + toolbarId).fadeOut(50).fadeIn(50);
    };
    //创建一个关闭按钮，内部通用
    var makeClosebtn = function (boxObj) {
        var closeBtn = $('<a class=\'btn btn-default\' href="javascript: void(0);" target=\'_self\' data-check=\'close_btn\' >关闭</a> </div>');
        closeBtn.on('click', function (e) {
            e.preventDefault();
            global.removeBoxObj(boxObj);
        });
        return closeBtn;
    };
    //管理正在消失的对象
    var faddingObj = {
        boxObj: null,
        timer: null,
        get: function () {
            return this.boxObj;
        },
        removeLastFaddingBox: function () {
            if(this.boxObj) {
                this.boxObj.stop();
                global.removeBoxObj(this.boxObj);
            }
            this.boxObj = null;
            if(this.timer) {
                clearTimeout(this.timer);
            }
        },
        set: function (obj_, lastTimeId) {
            this.removeLastFaddingBox();
            this.boxObj = obj_;
            this.timer = lastTimeId;
        }
    };
    //管理loading对象
    var loadingObj = {
        boxObj: null,
        get: function () {
            return this.boxObj;
        },
        removeLastLoadingBox: function () {
            if(this.boxObj) {
                global.removeBoxObj(this.boxObj);
                this.boxObj = null;
            }
        },
        set: function (obj_) {
            this.removeLastLoadingBox();
            this.boxObj = obj_;
        }
    };

    //移除单个box事件 [内部使用]
    global.removeBoxObj = function (boxObj, timer) {
        if(!boxObj) {
            return;
        }
        if(typeof boxObj != 'object') {
            return;
        }
        timer = isUndefined(timer) ? 0 : timer;
        //停止当前层的消失动作 ,防止当前层在消失过程中被关闭 引起注其他新层的错误
        //移除内部所有我们创建的的lr_name... window对象
        if(boxObj.stop) {
            boxObj.stop();
        }
        var allNames = boxObj.find('[name]');
        $.each(allNames, function (n, obj_) {
            var tagName = $(obj_).attr('name');
            if(window[tagName]) {
                window[tagName] = null;
            }
        });
        if (timer > 0) {
            boxObj.fadeOut(timer, function () {
                $(this).remove();
                removeQueueBoxObj(boxObj); //删除队列
            });
        } else {
            if(boxObj.bgObj) {
                boxObj.bgObj.remove();
            }
            boxObj.remove();
            removeQueueBoxObj(boxObj);//删除队列
        }
        //每次移除box 都要判断是否还有全屏窗口 全屏窗口会让body overflow:hidden
        if($('body').hasClass('remove_scroll_bar')) {
            if($('.full_msg_box').length == 0 ) {
                $('body').removeClass('remove_scroll_bar');
            }
        }
        var beforeClose = boxObj['beforeClose'] || boxObj['onClose'] || null;
        if(beforeClose) {
            beforeClose(boxObj);
        }
        if(boxObj.bgObj) {
            boxObj.bgObj.remove();
        }
    };
    //从队列中清除某个boxid
    var removeQueueBoxObj = function(boxObj) {
        for (var i = 0; i < boxObjArray.length; i++) {
            if (boxObjArray[i] == boxObj) {
                boxObjArray.splice(i, 1);
            }
        }
    };
    //创建新的boxId
    var createNewBoxId = function(newId) {
        newId = newId || boxObjArray.length + 1;
        var newBoxName = boxIDName + newId;
        if($('#'+newBoxName).length>0) {
            newId ++;
            return createNewBoxId(newId);
        }
        return newBoxName;
    };
    //获取页面卷去高度 以定位box的y
    var getScrollY = function (addTop) {
        var winH = $(window).outerHeight(true);//浏览器实际可见高度
        if (addTop.toString().indexOf('%') != -1) {
            addTop = (parseFloat(addTop.replace(/%/, '')) / 100) * winH;
        }
        addTop = Math.abs(addTop) || 0;//累加头部高度
        var winScrolltop = $(document).scrollTop();
        var y_ = winScrolltop + addTop;
        y_ = y_ < winScrolltop ? winScrolltop : y_;
        y_ = y_ < 0 ? 0 : y_;
        return y_;
    };
    //按钮放大变成窗口
    global.msgMovingBox = function (title,content, width, addTop, opener, opt) {
        opt = opt || {};
        if($('#fadding_btn').length) $('#fadding_btn').remove();
        var fadeBtn = $('<div id="fadding_btn"></div>');
        $('body').append(fadeBtn);
        var btnPos = opener.offset();
        var x_ = btnPos.left;
        var y_ = btnPos.top;
        var speed = getOptVal(opt, ['speed'], 100);
        var animate = getOptVal(opt, ['animate'], 'ease');
        opt['show'] = false;
        opt['onLoad'] = function (box_) {
            var boxOffset = {left: box_.css('left'), top:  box_.css('top')};
            var toX_ = boxOffset.left;
            var toY_ = boxOffset.top;
            fadeBtn.css({
                left: x_,
                top: y_,
                width: opener.outerWidth(),
                height: opener.outerHeight(),
            }).fadeIn(100);
            fadeBtn.animate({
                width: width,
                height: box_.outerHeight(),
                backgroundColor: '#fff',
                left: toX_,
                top: toY_
            }, speed, animate, function () {
                //去掉按钮
                setTimeout(function () {
                    fadeBtn.fadeOut(speed, function () {
                        fadeBtn.remove();
                    });
                }, speed);
            });
            setTimeout(function () {
                box_.stop().animate({'opacity': 1}, speed-200);
            }, speed);
        };
        return global.msgView(title, content, width, addTop, opt);
    };

    //判断方向
    function _checkDir(fx) {
        if(strInArray(fx, ['top','up','u','t', '上', 's']) !=-1) {
            return 's';
        } else if(strInArray(fx, ['right','r', '右', 'y']) !=-1) {
            return 'y';
        }  else if(strInArray(fx, ['down','d','bottom','b', '下', 'x']) !=-1) {
            return 'x';
        } else if(strInArray(fx, ['left','l', '左', 'z']) !=-1) {
            return 'z';
        }else {
            return '';
        }
    }
    //创建弹窗
    global.makeBox = function(options) {
        options = options || {};
        var defaultOption = {
            'id' : createNewBoxId(),
            'title' : '',
            'class' : 'msg_box',
            'type' : '',//msg/
            'text' : '',
            'textDecode' : [],//支持两种格式的解码 base64,url ,格式:'base|url|base'
            'width' : '',
            'height' : '',
            'top' : 0,//向上移动多少像素 默认弹窗是上下居中的
            'canDrag' : false,//能不能拖动
            'isRound' : true,//弹窗是否圆角
            'dragUp' : '',//拖拽后执行事件
            'bg' : false,//是否带背景
            'bgOpacity' : 0.1,//背景透明度
            'bgClick' : '',//点背景执行事件
            'btns' : '',//底部按钮组
            'btnOne' : '',//底部单按钮
            'btnTwo' : '',//底部双按钮
            'positionType' : 'absolute',
            'msgHide': false,//弹窗后 自动隐藏
            'msgHideThen': false,// 自动隐藏 然后执行
            'fadeIn' : true, //渐渐出现
            'fangda' : false, //渐渐放大
            'closeBtn' : true, //右上角显示关闭按钮
            'timer' : 2000,
            'beforeClose' : false,//关闭前的动作
            'resize' : false,//是否允许调整尺寸
            'noPadding' : false,//内容是否需要间距
        };
        if(isUndefined(options['tag']))  options['tag'] = 'div';
        if($.inArray(options['tag'], ['div', 'span']) ==-1) options['tag'] = 'div';
        options = $.extend({}, defaultOption, options);
        //转驼峰
        if(!isUndefined(options['can_drag'])) options['canDrag'] = options['can_drag'];
        if(!isUndefined(options['close_btn'])) options['closeBtn'] = options['close_btn'];
        if(!isUndefined(options['position_type'])) options['positionType'] = options['position_type'];
        if(!isUndefined(options['msg_hide'])) options['msgHide'] = options['msg_hide'];
        if(!isUndefined(options['text_decode'])) options['textDecode'] = options['text_decode'];
        if(!isUndefined(options['msg_hide_then'])) options['msgHideThen'] = options['msg_hide_then'];
        if(!isUndefined(options['bg_opacity'])) options['bgOpacity'] = options['bg_opacity'];
        var msgMoveDirection = getOptVal(options, ['fx','fangxiang','fangXiang','direction', '方向', '移动方向'], null);//移动方向
        var msgHideTime = getOptVal(options,['hide_time','hideTime','消失时长','hideKeep'], 1200);//消失动画持续时间
        var stopDistance = getOptVal(options,['distance','juli','jl','距离', '移动距离'], 200);//移动距离
        var boxMoveSpeed = getOptVal(options,['speed','速度','移动速度','移动时长'], 280);//移动速度
        var hideThenFunc = getOptVal(options,['hide_func','hideFunc','消失后执行','消失回调'], false);//消失后执行方法
        var msgHideWait = getOptVal(options,['msgHideWait','msg_hide_wait','wait','keep','停留时长'], false);//停留时长
        var fadeIn = getOptVal(options,['fadeIn','fadein'], null);//缓慢出现
        var zIndex = getOptVal(options,['zIndex','z-index'], null);//zIndex
        var shown = getOptVal(options,['show'], true);//是否显示
        var bgClickEven = getOptVal(options,['bgclick','bg-click', 'bgClick'], null);//背景点击事件
        var fangda = options['fangda'] || options['fangDa'] || options['fd'] || false;//放大
        var canResize = options['resize'] || false;//支持手动放大缩小
        var resizeDown = options['resizeDown'] || false;//缩放开始时的回调方法
        var minWidth = options['minWidth'] || null;//缩放的最小宽度
        var minHeight = options['minHeight'] || null;//缩放的最小高度
        var resizing = options['resizing'] || false;//缩放结束的回调方法
        var resizeUp = options['resizeUp'] || false;//缩放结束的回调方法
        var onLoadFunc = options['onLoad'] || false;//加载url后的回调方法
        var bodyCss = options['bodyCss'] || false;//自定义内部的body样式
        if(fangda) fadeIn = false;//放大时 不能渐渐出现
        var fadeInTime = options['fadeInTime'] || 500;//出现的过程时间
        var fangdaTime = options['fangdaTime'] || 300;//放大的过程时间
        var addLeft = options['addLeft'] || 0;//向右移动距离
        var subTop = options['subTop'] || 0;//向下移动距离
        var diyCss = options['css'] || false;//默认自定义css
        var noPadding = options['noPadding'] || false;
        var newBoxId = options['id'];
        var text_ = options.text;
        //初始化window的全局zIndex
        var currentBoxIndex = 0; //当前box的 zIndex 值
        if(zIndex) {
            currentBoxIndex = zIndex;
        } else {
            if(typeof window.zIndex != 'undefined') {
                currentBoxIndex = window.zIndex + 2; //必须加2 因为要给背景层退1留值
                window.zIndex = currentBoxIndex;
            } else {
                currentBoxIndex = window.zIndex = 1600;
            }
        }

        // < ckeditor的层是 z-index: 10010;
        // > bootstrap .navbar-fixed-top, .navbar-fixed-bottom z-index: 1030
        if ($('#' + newBoxId).length > 0) {
            $('#' + newBoxId).show();
            return;
        }
        var boxObj = $('<'+ options['tag'] +' id="'+ newBoxId +'" class="'+ options['class'] +'"></'+ options['tag'] +'>');
        if(shown===false) {
            boxObj.css('opacity', 0);
        }
        //允许外部获取box的id
        boxObj.id = newBoxId;
        boxObj.zIndex = currentBoxIndex;
        boxObj.css('z-index', currentBoxIndex);
        boxObj.beforeClose = getOptVal(options,['beforeClose'], false);//关闭前执行事件
        var setLeftTop = false;
        if(diyCss) {
            if(!isObj(diyCss)) {
                var cssObj = {};
                var array_ = diyCss.split(';');
                array_.forEach (function (v, n) {
                    var array2_ = v.split(':');
                    cssObj[$.trim(array2_[0])] = $.trim(array2_[1]);
                });
            } else {
                cssObj = diyCss;
            }
            if(shown===false) {
                cssObj['display'] = 'none';
                cssObj['opacity'] = 0;
            }
            setLeftTop = cssObj.left || cssObj.top;
            boxObj.css(cssObj);
        }
        //支持外部触发标题闪烁
        boxObj.shanShuo =  boxObj.twinkle = function () {
            boxObj.titleObj.fadeOut(50).fadeIn(50);
            return boxObj;
        };
        var titleObj;
        var msgType = options['type'];
        if (options['title']) {
            titleObj = $('<div class="'+ dialogTopId +'" >' + options['title'] + '</div>');
            if(options['closeBtn']) {
                var closeXX = $('<button type="button" class="'+ (isPc() ? 'close_pc': 'close_wap') +'" aria-hidden="true"></button>');
                closeXX.on('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();//不要触发外部的body事件，有时候其他窗口不需要这里关闭的时候 一起被body触发click关闭
                    global.removeBoxObj(boxObj);
                });
                titleObj.append(closeXX);
            }
        } else {//加个顶盖
            titleObj = $('<div class="top_angle" ></div>');
        }
        boxObj.append(titleObj);
        boxObj.titleObj = titleObj;
        var contentObj = $('<div class="'+ contentClass +' clearfix"></div>');
        if(bodyCss) {
            contentObj.attr('style', bodyCss);
        }
        if(noPadding) {
            contentObj.css('padding', 0);
        }
        if(msgType=='msg') {
            contentObj.addClass('msg');
        }
        var contentIsUrl = false;
        if(typeof text_ == 'object') {//传入的内容是dom对象
            $.each(text_, function (n_, tmpText) {
                contentObj.append(tmpText);
            });
        } else {
            //文本解密
            if(options['textDecode']) {
                var decodeMethods = options['textDecode'].toString();
                decodeMethods = decodeMethods.replace(/,/, '|');
                var decodeMethodsAy = decodeMethods.split('|');
                decodeMethodsAy.forEach(function (tmpMethod) {
                    if(tmpMethod) {
                        if(tmpMethod == 'url') text_ = $.url.decode(text_);
                        if(tmpMethod == 'base') text_ = $.base64.decode(text_);
                    }
                });
            }
            if(text_ && typeof text_ == 'string' && text_.substr(0, 5) =='[url]') {
                contentIsUrl = true;
                var obj_ = $('<div>' +
                    '<img src="data:image/gif;base64,R0lGODlhEAAQALMPAHp6evf394qKiry8vJOTk83NzYKCgubm5t7e3qysrMXFxe7u7pubm7S0tKOjo////yH/C05FVFNDQVBFMi4wAwEAAAAh+QQJCAAPACwAAAAAEAAQAAAETPDJSau9NRDAgWxDYGmdZADCkQnlU7CCOA3oNgXsQG2FRhUAAoWDIU6MGeSDR0m4ghRa7JjIUXCogqQzpRxYhi2HILsOGuJxGcNuTyIAIfkECQgADwAsAAAAABAAEAAABGLwSXmMmjhLAQjSWDAYQHmAz8GVQPIESxZwggIYS0AIATYAvAdh8OIQJwRAQbJkdjAlUCA6KfU0VEmyGWgWnpNfcEAoAo6SmWtBUtCuk9gjwQKeQAeWYQAHIZICKBoKBncTEQAh+QQJCAAPACwAAAAAEAAQAAAEWvDJORejGCtQsgwDAQAGGWSHMK7jgAWq0CGj0VEDIJxPnvAU0a13eAQKrsnI81gqAZ6AUzIonA7JRwFAyAQSgCQsjCmUAIhjDEhlrQTFV+lMGLApWwUzw1jsIwAh+QQJCAAPACwAAAAAEAAQAAAETvDJSau9L4QaBgEAMWgEQh0CqALCZ0pBKhRSkYLvM7Ab/OGThoE2+QExyAdiuexhVglKwdCgqKKTGGBgBc00Np7VcVsJDpVo5ydyJt/wCAAh+QQJCAAPACwAAAAAEAAQAAAEWvDJSau9OAwCABnBtQhdCQjHlQhFWJBCOKWPLAXk8KQIkCwWBcAgMDw4Q5CkgOwohCVCYTIwdAgPolVhWSQAiN1jcLLVQrQbrBV4EcySA8l0Alo0yA8cw+9TIgAh+QQFCAAPACwAAAAAEAAQAAAEWvDJSau9WA4AyAhWMChPwXHCQRUGYARgKQBCzJxAQgXzIC2KFkc1MREoHMTAhwQ0Y5oBgkMhAAqUw8mgWGho0EcCx5DwaAUQrGXATg6zE7bwCQ2sAGZmz7dEAAA7"/> ' +
                    'loading...</div>');
                contentObj.append(obj_);
                obj_.load(text_.substr(5), function (res) {
                    if(res.substring(0, 1) =='{' && JSON.parse(res)) {
                        var resObj = JSON.parse(res);
                        if(resObj.msg) obj_.html(resObj.msg);
                    }
                    if(onLoadFunc) onLoadFunc(boxObj);
                });
            } else {
                contentObj.append(text_);
            }
        }
        boxObj.append(contentObj);
        boxObj.bgObj = null;
        //加背景 其他元素不可控制
        if (options.bg) {
            var boxBg = $("<div class='"+ boxBgClass +"'></div>");
            var bgCss = {};
            if(options.bgOpacity) boxBg.css('opacity', options.bgOpacity);
            boxBg.on({
                'click': function (e) {
                    if(bgClickEven) {
                        bgClickEven(e, $(this));
                    } else {
                        dialogFlash();
                    }
                },
                'keyDown': function (e) {
                    e.preventDefault();
                },
                'keyPress': function (e) {
                    e.preventDefault();
                }
            });
            $('body').append(boxBg);
            boxObj.bgObj = boxBg;
            boxBg.fadeIn(400);//首次创建背景 才加渐变
            bgCss['zIndex'] = (currentBoxIndex-1);//背景的层级要小于当前boxObj
            boxBg.css(bgCss);
        }
        var boxBar;
        var hasBar = false;
        if (options['btns']) {//如果有按钮组
            hasBar = true;
            boxBar = $('<div class="'+ boxBarId +'"></div>');
            if(typeof options['btns'] == 'object') {//传入的按钮是dom对象
                $.each(options['btns'], function (n_, tmpBtn) {
                    boxBar.append(tmpBtn);
                });
                if (boxBar.find("[data-check=close_btn]").length == 0) {//强制要带上关闭按钮
                    boxBar.append(makeClosebtn(boxObj));
                }
            } else if(options['btns'].length >0) {//传入的按钮是字符串
                boxBar.append(options['btns']);
                if (options['btns'].indexOf('close_btn') == '-1') {//强制要带上关闭按钮
                    boxBar.append(makeClosebtn(boxObj));
                }
            }
            boxObj.append(boxBar);
        }
        if (options.btnOne) {//如果唯一的大按钮 生成单行按钮格式
            boxBar = $('<div class="one_block_btn"><button>' + options.btnOne + '</button></div>');
            boxBar.find('button').click(function () {
                global.removeBoxObj(boxObj);
            });
            boxObj.append(boxBar);
        }
        if (options.btnTwo) {//如果有一对按钮 生成左右结构的按钮格式
            if(typeof options.btnTwo == 'object') {//传入的按钮是dom对象
                boxBar = $('<div class="double_btn"></div>');
                $.each(options.btnTwo, function (n_, tmpBtn) {
                    boxBar.append(tmpBtn);
                });
            } else {//传入的按钮是字符串
                boxBar = $('<div class="double_btn">' + options.btnTwo + '</div>');
            }
            boxObj.append(boxBar);
        }
        if(canResize) {
            var resizeBtn = $('<span class="resizeIcon"></span>');
            var moveBottomLineAble = false;
            var mouseX = 0;
            var mouseY = 0;
            if(!isPc()) {//wap端
                resizeBtn[0].addEventListener('touchstart',  function (e) {
                    e = e.touches[0];
                    clearSel();
                    resizeBtn.addClass('active');
                    e.preventDefault();
                    moveBottomLineAble = true;
                    mouseX = e.pageX;
                    mouseY = e.pageY;
                    var lastWidth = boxObj.outerWidth();
                    var lastHeight = boxObj.outerHeight();
                    let changeX, changeY;
                    if (resizeDown) resizeDown(e, resizeBtn, boxObj);
                    var touchMoveEve = function (evt) {
                        if (moveBottomLineAble) {
                            changeX = evt.pageX - mouseX;
                            changeY = evt.pageY - mouseY;
                            var newWidth = lastWidth + changeX;
                            var newHeight = lastHeight + changeY;
                            boxObj.css({
                                width: newWidth,
                                height: newHeight,
                            });
                            if (resizing) resizing(evt, resizeBtn, boxObj);
                        }
                    };
                    $(doc)[0].addEventListener('touchmove', touchMoveEve);

                    var touchEndEve =  function (event) {
                        if (moveBottomLineAble) {
                            moveBottomLineAble = false;
                        }
                        resizeBtn.removeClass('active');
                        if (resizeUp) resizeUp(event, resizeBtn, boxObj);
                        $(doc)[0].removeEventListener('touchmove', touchMoveEve);
                        $(doc)[0].removeEventListener('touchend', touchEndEve);
                    };
                    $(doc)[0].addEventListener('touchend',touchMoveEve);
                });
            } else {
                resizeBtn.bind('mousedown', function(e) {
                    resizeBtn.addClass('active');
                    e.preventDefault();
                    moveBottomLineAble = true;
                    mouseX = e.pageX;
                    mouseY = e.pageY;
                    var lastWidth = boxObj.outerWidth();
                    var lastHeight = boxObj.outerHeight();
                    let changeX,changeY;
                    if(resizeDown) resizeDown(e, resizeBtn, boxObj);
                    var mousemoveEven = function(evt) {
                        if (moveBottomLineAble) {
                            changeX = evt.pageX - mouseX;
                            changeY = evt.pageY - mouseY;
                            var newWidth = lastWidth + changeX;
                            var newHeight = lastHeight + changeY;
                            if(minWidth) {
                                newWidth = Math.max(newWidth, minWidth);
                            }
                            if(minHeight) {
                                newHeight = Math.max(newHeight, minHeight);
                            }
                            boxObj.css({
                                width: newWidth,
                                height: newHeight,
                            });
                            if (resizing) resizing(evt, resizeBtn, boxObj);
                        }
                    };
                    $(doc).bind('mousemove', mousemoveEven);
                    var mouseUpEven = function (event) {
                        if (moveBottomLineAble) {
                            moveBottomLineAble = false;
                            $(doc).unbind('mousemove', mousemoveEven);
                            $(doc).unbind('mouseup', mouseUpEven);
                        }
                        resizeBtn.removeClass('active');
                        if(resizeUp) resizeUp(event, resizeBtn, boxObj);
                    };
                    $(doc).bind('mouseup', mouseUpEven);
                });
            }
            boxObj.append(resizeBtn);
        }
        boxObjArray.push(boxObj);
        //如果弹窗要渐渐显示，开始必须要先隐藏
        if(fadeIn) boxObj.hide();
        $('body').append(boxObj);
        if (options.canDrag) {
            if(!options.positionType) options.positionType = '';
            if (!options.title) {
                if(!hasBar) boxBarId = contentClass;
                if(boxBar) {
                    boxBar.css('cursor', 'move');
                    boxBar.addClass('noSelect');
                }
            } else {
                boxObj.find(titleObj).css('cursor', 'move').addClass('noSelect');
                boxObj.Drag(titleObj, options.positionType, {
                    dragUp: options.dragUp || false,
                    mousedown: options.mousedown || false,
                    draging: options.draging || false,
                });
            }
        }
        if (options.isRound) {
            boxObj.addClass('isRound');
        }
        var boxWidth;
        if(cssObj && cssObj.width) {
            boxWidth = cssObj.width;
        } else {
            boxWidth = options.width ? options.width : boxObj.outerWidth();//允许外部提前定义box的宽度 因为像gif这样的图片会加载完才能获取到box真实的width
        }

        var x_=0,y_=0;
        if(options.width == 'auto') {
            boxWidth = parseFloat(boxObj.outerWidth());
        }
        //如果css未定义坐标 允许重置居中
        if(!setLeftTop) {
            var widthXY = getBoxXy(boxWidth, options.top);
            x_ = widthXY.x;
            y_ = widthXY.y;
        }
        if(!isUndefined(options.x))  x_ = options.x;//直接传入绝对位置时
        if(!isUndefined(options.y))  y_ = options.y;//直接传入绝对位置时
        if(options.positionType == 'fixed') y_ = options.top;//fixed 直接按填写的值
        //允许自定义向右移动
        if(addLeft) {
            x_ += addLeft;
        }
        //允许自定义向下移动
        if(subTop) {
            y_ += subTop;
        }
        //坐标如果是纯数字 要加上单位
        if(!isNaN(x_)) x_ = x_ + "px";
        if(!isNaN(y_)) y_ = y_ + "px";
        //设置data属性
        if(!isUndefined(options.data)) {
            $.each(options.data, function (n, v) {
                boxObj.attr('data-'+n, v);
            });
        }
        var _callFadeout = function () {
            if(msgHideWait) {
                var tmpTimeId = setTimeout(function () {
                    boxObj.fadeOut(msgHideTime, function () {
                        global.removeBoxObj(boxObj);
                        if(hideThenFunc) {
                            if(typeof hideThenFunc == 'string') {
                                eval(hideThenFunc);
                            } else {
                                hideThenFunc();
                            }
                        }
                    })
                }, msgHideWait);
                faddingObj.set(boxObj, tmpTimeId);
            }
        };
        if(fadeIn) {
            var reCss = {'display': 'none', width: boxWidth};
            if(x_) reCss['left'] = x_;
            if(y_) reCss['top'] = y_;
            boxObj.css(reCss);
            setTimeout(function () {
                boxObj.fadeIn(fadeInTime, function () {
                    _callFadeout();
                });
            }, 20);
        } else if(fangda) {
            var reCss = {width: boxWidth, "position": options.positionType, "display": 'block'};
            if(x_) reCss['left'] = x_;
            if(y_) reCss['top'] = y_;
            var fangdaClass = ' fangda_t' + parseInt(fangdaTime/100);
            boxObj.css(reCss).addClass('fangda_box' + fangdaClass);
        } else {
            var reCss = {display: 'block', left: x_, 'position': options.positionType};
            if(x_) reCss['left'] = x_;
            if(y_) reCss['top'] = y_;
        }
        var _changeJuli = function (jl_, fxNew) {
            if(jl_.toString().indexOf('%') !=-1) {
                jl_ = parseFloat(jl_.toString().replace('%'))/100;
            }
            if(jl_.toString().indexOf('.') !=-1) {
                if(fxNew =='s' || fxNew =='x') jl_ = jl_ * $(window).height();
                if(fxNew =='y' || fxNew =='z') jl_ = jl_ * $(window).width();
            }
            return jl_;
        };
        if(msgMoveDirection) {
            reCss['display'] = 'block';
            //初始化方向
            var fx_ = _checkDir(msgMoveDirection);
            if(fx_ =='s') {
                reCss['top'] = 'auto';
                reCss['bottom'] = 0;
            } else if(fx_ =='x') {
                reCss['top'] = 0;
                reCss['bottom'] = 'auto';
            } else if(fx_ =='z') {
                reCss['top'] = options['top'] || '35%';
                reCss['right'] = 0;
                reCss['left'] = 'auto';
            } else if(fx_=='y') {
                reCss['top'] = options['top'] || '35%';
                reCss['left'] = 0;
            }
            boxObj.css(reCss);
            var animateCss;
            var fxNew = _checkDir(msgMoveDirection);
            //目标新方向
            stopDistance = _changeJuli(stopDistance, fxNew);
            if(fxNew=='s') {
                animateCss = {bottom: stopDistance};
            } else if(fxNew=='x') {
                animateCss = {top: stopDistance};
            } else if(fxNew=='z') {
                animateCss = {right: stopDistance};
            } else if(fxNew=='y') {
                animateCss = {left: stopDistance};
            }
            boxObj.animate(animateCss, boxMoveSpeed, function () {
                if(!fadeIn) _callFadeout();
            });
            //浮起来 ,停顿, 消失
        } else {
            boxObj.css(reCss);
            if(!fadeIn) _callFadeout();
        }

        if(bgIsFadingOut) {
            boxObj.bgObj.remove();
        }
        if(!contentIsUrl) {
            if(onLoadFunc) onLoadFunc(boxObj);
        }
        return boxObj;
    };

    //清除所有队列和提示框
    global.hideAllBox = function() {
        var len = boxObjArray.length;
        for(var i =0;i < len;i++) {
            global.removeBoxObj(getLastBoxObj());
        }
        //释放所有的动画变量
        bgIsFadingOut = false;
    };
    //删除最新的提示框
    global.hideNewBox = function() {
        //取最新的boxid
        global.removeBoxObj(getLastBoxObj());
    };
    //获取最新的提示框
    global.getLastBoxObj = function() {
        return getLastBoxObj();
    };
    //提示框扩展:提示内容 无自动隐藏 按钮
    global.msg = function(text, btnText, options) {
        btnText = isUndefined(btnText) ? '确定': btnText;
        options = options || {};
        options = $.extend({
            canDrag: true,
            bg: true,//背景遮挡
            btnOne: btnText,
            text: text,
            top: 100,
            type: 'msg',
            hide: false //不自动隐藏
        }, options);
        return global.makeBox(options);
    };
    //提示框扩展:提示内容 无自动隐藏 按钮 放大
    global.msgf = function(text, btnText, options) {
        options = $.extend({}, {bg: false, 'fangda': true}, options);
        return global.msg(text, btnText, options)
    };
    //提示框扩展: 查看文本信息
    global.msgt = global.msgView = function(title_, urlOrText, width, addTop, options) {
        options = options || {};
        title_ = title_ || '';
        width = width || 450;
        addTop = isUndefined(addTop) ? 100 : addTop;
        options = $.extend({
            title: title_,
            'class': (isPc() ? 'msg_box': 'msg_box_wap'),
            text: urlOrText,
            width: width,
            top: addTop,
            hide: false, //自动隐藏
            canDrag: true
        }, options);
        return global.makeBox(options);
    };
    //提示框扩展:内容无间距
    global.msgViewE = global.msgViewEmpty = function(title_, urlOrText, width, addTop,options) {
        options = options || {};
        var defaultOption = {
            'class': 'msg_box msg_box_empty',
            hide: false, //自动隐藏
            fadeIn: true //缓慢出现
        };
        defaultOption = $.extend({}, defaultOption, options);
        return global.msgView(title_, urlOrText, width, addTop, defaultOption);
    };
    //查看图片
    global.msgViewImg = function(url, options) {
        options = options || {};
        var width = !isUndefined(options['width']) ? options['width'] : 100;
        var scrollTop = !isUndefined(options['top']) ? options['top'] : 22;//头部高度
        var moreText = !isUndefined(options['text']) ? options['text'] : '';//附加文本
        options['btns'] = '&nbsp;';
        delete options['text'];
        global.msgView('', '<img src="'+ url +'" />'+ moreText, width, scrollTop, options);
    };
    //提示框扩展: 等待提示,有背景遮罩  有关闭按钮
    global.msgWait = function(text, options) {
        if(isUndefined(text)) text = '努力提交中,请勿刷新或关闭页面';
        options = $.extend({
            bg: true,//背景遮挡
            text: text,
            type: 'msg',
            top: 100,
            hide: false,//自动隐藏
            canDrag: isPc(),//pc端才允许拖动
        }, options);
        global.makeBox(options);
    };

    //无边的浮动 只有右上角带关闭按钮
    global.msgViewAd = function(url, options) {
        options = options || [];
        var scrollTop = !isUndefined(options['top']) ? options['top'] : '';//减去高度
        var width = !isUndefined(options['width']) ? options['width'] : '';
        var text_ = (url.substr(0, 1) == '/' || url.substr(0, 7) == 'http://' || url.substr(0, 8) == 'https://') ? '<img src="'+ url +'" />' : url;
        global.makeBox({
            'class': 'msg_no_border',
            bg: 1,//背景遮挡
            text: text_,
            width: width,
            top: scrollTop,
            btn: '',
            hide: false, //自动隐藏
            canDrag: isPc(),//pc端才允许拖动
            fadeIn: true //缓慢出现
        });
    };
    //imgArray 图片数组
    //imgArray = {"url_md5":{index:0,width:111,height:222,url:''}};
    global.msgViewImgFull = function(currentImgurl, imgArray) {
        //循环查看图片功能 . 定义当前图片url 和 查看的所有图片数组
        var currentViewImgIndex = -1;
        var currentViewImgData = imgArray;//注意 currentViewImgData是无序的对象 遍历查找只能按key类对比 不能按index
        var imgLen = 0;
        $.each(currentViewImgData, function (key, item) {
            imgLen ++;
        });
        //计算按钮样式
        function resetPrevNextBtn(getIndex) {
            //计算  下一张 的按钮样式
            if(getIndex == imgLen-1) {
                nextObj.addClass('no_more');
            } else {
                nextObj.removeClass('no_more');
            }
            //计算上一张 的按钮样式
            if(getIndex == 0) {
                preObj.addClass('no_more');
            } else {
                preObj.removeClass('no_more');
            }
        }
        //访问第n张图片
        function visitImgIndex(getIndex) {
            var findItem = null;
            $.each(currentViewImgData, function (key, item) {
                if(item.index == getIndex) {
                    findItem = item;
                    return false;
                }
            });
            if(findItem) {
                var imgNewCss = [];
                previewImg.removeAttr('data-rotate');   //清空之前图片的旋转属性
                previewImg.removeAttr('style');   //清空之前图片的旋转属性
                previewImg.attr('src', findItem.url);
                if (findItem.canDownload == 1) {
                    downloadObj.show();
                } else {
                    downloadObj.hide();
                }

            }
            //计算  下一张 的按钮样式
            resetPrevNextBtn(getIndex);
        }
        var newBoxId = createNewBoxId();
        var currentUrlEncode = $.md5(currentImgurl);
        var findImgData = currentViewImgData[currentUrlEncode] || null;
        if(!findImgData) {
            console.log('轮播中找不到图片:'+ currentImgurl);
            return;
        }
        currentViewImgIndex = findImgData['index'];
        if(currentViewImgIndex == -1) {
            msgTisf('找不到url:'+ currentImgurl);
            return;
        }
        //初始化图片尺寸：如果提前定义好了宽度和高度
        //img必须带id来实时获取 ,第一次旋转之后img会被 替换为canvas
        var mapObj = $("<div id='preview_img_box'>" +
            "<div class='img-wrap' >" +
            "<img class='rote_img' alt='预览图片' />" +
            "</div></div>");
        var viewImgBox = $("<div id='view_list_img_box'><div class='inner'></div></div>");
        var closeBtn = $("<div class='close_btn'> <img src='/Static/box/close.png' width='100%' alt='关闭预览' title='关闭预览' /></div>");
        closeBtn.click(function () {
            loadingObj.removeLastLoadingBox();//把loading也一起关闭
            global.removeBoxObj(global.viewFullImgObj);
        });
        var downloadObj = $("<a class='control_btn' href='javascript: void(0);'> <img class='download_img' src='/Static/box/download.png' width='100%' alt='下载' title='下载图片' /></a>");
        var rotateObj = $("<div class='control_btn'> <img class='rotate_img' src='/Static/box/rotate.png' width='100%' alt='向右转90度' title='向右转90度' /></div>");
        var imgWrap = mapObj.find('.img-wrap');
        var previewImg = mapObj.find('.rote_img');
        downloadObj.find('img').on('click', function (e) {
            var downed = downloadObj.attr('data-success');
            //下载过文件 不用再渲染事件 默认执行href下载
            if(downed == 1)  {
                return;
            }
            e.preventDefault();
            var url = previewImg.attr('src');
            strObj.downBlobFileOnBtn(downloadObj, url, 'image/jpeg', 'download.jpg');
        });
        rotateObj.find('img').on('click', function (e) {
            e.preventDefault();
            var tmpImg = previewImg;
            var oldRotate = tmpImg.attr('data-rotate');
            if(isUndefined(oldRotate)) oldRotate = 0;
            var newRotate = parseInt(oldRotate) +90;
            var newStr = {
                'data-rotate' : newRotate,
                'style' : 'transform:rotate('+ newRotate +'deg);'
            };
            tmpImg.attr(newStr);
        });
        var preObj, nextObj;
        preObj = $("<div class='control_btn'>" +
            "<img  class='pre_img' src='/Static/box/pre.png' width='100%' alt='上一张' title='查看上一张("+ (currentViewImgIndex + 1) +")' /></a></div>");
        preObj.find('img').on('click', function (e) {
            callPrev(e);
        });
        nextObj = $("<div class='control_btn'><img class='next_img' src='/Static/box/next.png' width='100%' alt='下一张' title='查看下一张("+ (currentViewImgIndex + 2) +")' /></div>");
        nextObj.find('img').on('click', function (e) {
            callNext(e);
        });
        viewImgBox.find('.inner').append(preObj).append(downloadObj).append(rotateObj).append(nextObj);
        mapObj.append(viewImgBox);
        //点击背景 隐藏图层
        mapObj.click(function (e) {
            loadingObj.removeLastLoadingBox();//把loading也一起关闭
            global.removeBoxObj(global.viewFullImgObj);
        });
        //点击图片不隐藏图层
        previewImg.click(function (e) {
            e.stopPropagation();
        });
        //点击控制条不隐藏图层
        viewImgBox.click(function (e) {
            e.stopPropagation();
        });
        var opts = {
            'id': newBoxId,
            'class': (isPc() ? 'msg_box': 'msg_box_wap'),
            bg: 1,//背景遮挡
            text: [mapObj, closeBtn],
            hide: false, //自动隐藏
        };
        opts['x'] = 0;
        opts['y'] = 0;
        opts['width'] = '100%';
        opts['height'] = '100%';
        opts['class'] = 'view_full_img';
        opts['top'] = 0;
        opts['bgOpacity'] = 0.6;
        opts['positionType'] = 'fixed';
        //背景点击：关闭窗口
        opts['bgClick'] = function () {
            global.hideAllBox();
        };
        var winBox = global.makeBox(opts);
        global.viewFullImgObj = winBox;
        //计算图片缩小后的大小
        previewImg.on('load', function () {
            loadingObj.removeLastLoadingBox();
        });
        function callPrev(e) {
            if (currentViewImgIndex <= 0) {
                msgTisf('没有上一张了');
                return;
            }
            loadingObj.removeLastLoadingBox();
            e.preventDefault();
            currentViewImgIndex --;
            loadingObj.set(loadingSvg(true));
            imgWrap.removeAttr('style');
            visitImgIndex(currentViewImgIndex);
        }
        function callNext(e) {
            if (currentViewImgIndex >= imgLen -1) {
                msgTisf('没有下一张了');
                return;
            }
            loadingObj.removeLastLoadingBox();
            e.preventDefault();
            currentViewImgIndex ++;
            loadingObj.set(loadingSvg(true));
            imgWrap.removeAttr('style');
            visitImgIndex(currentViewImgIndex);
        }
        /**
         * Perform the keyboard actions
         *
         */
        function _keyboard_action(objEvent) {
            if($('#preview_img_box').length==0) return;
            var keycode, escapeKey;
            // To ie
            if ( objEvent == null ) {
                keycode = event.keyCode;
                escapeKey = 27;
                // To Mozilla
            } else {
                keycode = objEvent.keyCode;
                escapeKey = objEvent.DOM_VK_ESCAPE;
            }
            // Verify the keys to close the ligthBox
            if (keycode == escapeKey) {
                global.hideNewBox();
            }
            // Verify the key to show the previous image
            if (  keycode == 37 ) {
                objEvent.preventDefault();
                // If we are not showing the first image, call the previous
                callPrev(objEvent);
            }
            // Verify the key to show the next image
            if ( keycode == 39 ) {
                objEvent.preventDefault();
                // If we are not showing the last image, call the next
                callNext(objEvent);
            }
        }
        $(document).off('keydown').on('keydown', function (e) {
            _keyboard_action(e);
        });

        //初始化加载图片
        loadingObj.set(loadingSvg(false));
        previewImg.attr('src', currentImgurl);
        resetPrevNextBtn(currentViewImgIndex);
        return winBox;
    };
    //提示框扩展:大层,内容以url加载
    global.msgWin = function(title, urlOrText, width, scrollTop, option) {
        //wap端 也可以绝对定位 因为编辑内容时 会有很多图片占据高度
        width = width || 500;
        scrollTop = isUndefined(scrollTop) ? 300 : scrollTop;
        var newBoxId = createNewBoxId();
        var defaultOpt = {
            title: title,
            'id': newBoxId,
            width: isNaN(width) ? width : width+'px',
            'class': (isPc() ? 'msg_box': 'msg_box_wap'),
            top: scrollTop,
            bg: 1,//背景遮挡
            text: urlOrText,
            canDrag: isPc(),//pc端才允许拖动
            btn: false,
            closeBtn: true,
            hide: false,//自动隐藏
            positionType: 'absolute',//浮动类型
            fadeIn: true //缓慢出现
        };
        if(option) defaultOpt = $.extend({}, defaultOpt, option);
        return global.makeBox(defaultOpt);
    };
    //提示框扩展:全屏无框大层,内容以url加载 wap专用
    global.msgWinFull = function(urlOrText, options) {
        options = options || {};
        var defaultOption = {
            width: '100%',
            bg: true,//背景遮挡
            text: urlOrText,
            'class': 'full_msg_box',
            positionType: 'fixed',
            hide: false, //自动隐藏
            fadeIn: true //缓慢出现
        };
        defaultOption = $.extend({}, defaultOption, options);
        var box = global.makeBox(defaultOption);
        //隐藏body的滚动条
        $('body').addClass('remove_scroll_bar');
        box.css({left: 0, top: 0, height: '100%'});
        return box;
    };
    //提示框扩展: 半屏无框大层,内容以url加载 wap专用 点击背景要关闭窗口
    global.msgWinHalf = function(urlOrText, timer, bili, options) {
        if(typeof timer == 'undefined'|| !timer) timer = 220;
        if(typeof bili == 'undefined'|| !bili) bili = 0.5;
        bili = parseFloat(bili);
        var defaultOption = {
            bg: 1,//背景遮挡 并且点击隐藏窗口
            text: urlOrText,
            'class': 'full_msg_box',
            canDrag: false,
            positionType: 'fixed',
            hide: false, //自动隐藏
            fadeIn: false //缓慢出现
        };
        defaultOption = $.extend({}, defaultOption, options);
        var box = global.makeBox(defaultOption);
        //隐藏body的滚动条
        $('body').addClass('remove_scroll_bar');
        var winHeight = $(window).outerHeight(true);//浏览器实际可见高度
        var boxHeight = winHeight * bili;
        box.css({left: 0, top: winHeight});
        box.animate({top: (winHeight-boxHeight)}, timer);
    };
    //对话框：弹出提示确认  按钮绑定事件：confirmFunc_ cancelFunc 都是传入字符串
    global.msgConfirm = function(text_, confirmBtnText, cancelBtnText, confirmFunc_, cancelFunc, moreOption) {
        if(confirmBtnText =='') confirmBtnText = '确定';
        if(cancelBtnText =='') cancelBtnText = '取消';
        moreOption = moreOption || {};
        var btnConfirm = $("<input type='button' class='half_btn confirm_submit' value='"+ confirmBtnText +"' /> ");
        var btnCancel = $("<input type='button' class='half_btn' value='"+ cancelBtnText +"'/> ");
        if(typeof confirmFunc_ == 'string') {
            btnConfirm.on('click', function () {
                eval(confirmFunc_);
            });
        } else {
            btnConfirm.on('click', confirmFunc_);
        }
        //默认取消按钮就是关闭层，无须每次都传入一样的内容
        if (typeof cancelFunc == 'undefined' || !cancelFunc) cancelFunc = function () {
            global.hideNewBox();
        };
        if(typeof cancelFunc == 'string') {
            btnCancel.on('click', function () {
                eval(cancelFunc);
            });
        } else {
            btnCancel.on('click', cancelFunc);
        }
        moreOption['class'] = moreOption['class'] + ' msg_box';//一定要带默认样式
        var defaultOption = $.extend({}, {
            title: null,
            bg: 1,//背景遮挡
            text: text_,
            btnTwo: [btnConfirm, btnCancel],
            hide: false, //自动隐藏
            type: 'msg',
            top: 100,
            canDrag: false,
            fadeIn: true //缓慢出现
        }, moreOption);
        return global.makeBox(defaultOption);
    };
    //对话框：弹出提示确认  按钮绑定事件：confirmFunc_ cancelFunc 都是传入字符串
    global.msgConfirmf = function(text_, confirmBtnText, cancelBtnText, confirmFunc_, cancelFunc, moreOption) {
        moreOption = $.extend({}, {fangda: true}, moreOption);
        return global.msgConfirm(text_, confirmBtnText, cancelBtnText, confirmFunc_, cancelFunc, moreOption);
    };
    //黑色提示语 从下往上 或放大 目前手机专用
    //效果：从底部向上浮动一个黑色半透明背景的矩形，只可写文字。
    global.msgTis = function(tisText, options) {
        return __makeTisfBox(tisText, options, false)
    };
    global.msgTisf =  function(tisText, options) {
        options = options || {};
        if(isObj(tisText)) tisText = JSON.stringify(tisText);
        options = $.extend({
            'wait': 1200,
            'hideTime': 300,
        }, options);
        return __makeTisfBox(tisText, options, true)
    };
    function isObj( value ) {
        return typeof value == 'object';
    }
    //克隆的类型
    function getCloneType(newData) {
        if($.isArray(newData)) {
            return [];
        } else {
            return {};
        }
    }
    //克隆data
    function cloneData(newData, oldData) {
        if(!isUndefined(oldData)) {
            return $.extend(getCloneType(newData), oldData, newData);
        } else {
            return $.extend(getCloneType(newData), newData);
        }
    }
    //获取属性值
    function getOptVal(obj_, keyname, defaultVal) {
        if(!obj_) return defaultVal;
        if($.isArray(keyname)) {
            var findKey = false;
            var findVal = false;
            $.each(keyname, function (index_, tmpName) {
                if(!isUndefined(obj_[tmpName])) {
                    //对象要克隆 否则会反作用原对象
                    findKey = true;
                    findVal = isObj(obj_[tmpName]) ? cloneData(obj_[tmpName]) : obj_[tmpName];
                    return false;
                }
            });
            if(findKey) {
                return findVal;
            } else {
                return defaultVal;
            }
        } else {
            if(!isUndefined(obj_[keyname])) {
                //对象要克隆 否则会反作用原对象
                return isObj(obj_[keyname]) ? cloneData(obj_[keyname]) : obj_[keyname];
            }
        }
        return defaultVal;
    }
    function __makeTisfBox(tisText, options, fangda) {
        faddingObj.removeLastFaddingBox();
        options = options || {};
        var bg = options['bg'] || options['背景'] || options['遮罩']|| false;//是否需要背景遮挡
        var boxClass = 'diy_tis_box';
        if(typeof tisText == 'object')  tisText = tisText.toString();
        var defCfg = {
            bg: bg,//背景遮挡
            width: 'auto',
            text: tisText,
            fangda: fangda, //向上移动 也可以带放大的效果
            'class': boxClass,
            'tag': 'span',
            'top': '40%', //出现顶部距离
            positionType: 'fixed'
        };
        var msgHideWait = getOptVal(options,['msgHideWait','msg_hide_wait','wait','keep','stop','停留时长'], false);//停留时长
        if(msgHideWait === false) defCfg['wait'] = 1500;
        options = $.extend(defCfg, options);
        var box = global.makeBox(options);
        return box;
    };

    //效果：页面中间显示一个loading图标 用于等候数据提交的等待.
    global.loading = function(needBg) {
        if(typeof needBg == 'undefined') needBg = false;
        var loadObj = global.makeBox({
            'class': 'diy_loading',
            bg: needBg,//背景遮挡
            text: '<div class="la-ball-spin-clockwise" style="color: #999">' +
                '<div></div><div></div>' +
                '<div></div><div></div>' +
                '<div></div><div></div>' +
                '<div></div><div></div>' +
                '</div>',
            width: 42,
            top: '30%'
        });
        loadingObj.set(loadObj);
        return loadObj;
    };
    //效果：页面中间显示一个loading图标 用于等候数据提交的等待.
    global.loadingSvg = function(needBg, opt) {
        opt = opt || {};
        var defaultOpt = {
            'class': 'diy_loading',
            width: 42,
            border: 7,
        };
        defaultOpt = $.extend({}, defaultOpt, opt);
        var loadClass = defaultOpt['class'];
        var width = defaultOpt.width;
        var border = defaultOpt.border;
        if(typeof needBg == 'undefined') needBg = false;
        var loadObj = global.makeBox({
            'class': loadClass,
            bg: needBg,//背景遮挡
            text: '<svg class="svgLoading" viewBox="25 25 50 50">' +
                '<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="'+ border +'" stroke-miterlimit="10"></circle>' +
                '</svg>',
            width: width,
            top: '30%'
        });
        loadingObj.set(loadObj);
        return loadObj;
    };
    //移除所有Loading层
    global.noLoading = function() {
        loadingObj.removeLastLoadingBox();
    };
    //是pc还是手机wap
    global.isPc = function() {
        return isPc();
    };
    $(document).keyup(function (event) {
        if (event.keyCode == 27) {
            global.hideNewBox();
        }
    });
    return global;
});
