//author: lirui
(function (global, $) {
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
    function isUndefined(variable) {return typeof variable == 'undefined' ? true : false;}
    //拖动事件 obj.Drag(son, '', '');
    $.fn.Drag = function (titleId, positionType, opt) {
        //drag_up_func 设置回调参数,array, 0:回调函数,1:自定义对象1,2:自定义对象2
        //最终会回调执行 回调函数([x,y], obj1, obj2)
        var mousedown_data = opt && (opt['mousedown_data'] || opt['mousedowndata'] || opt['mouse_down_data'] || opt['mousedownData'] || opt['mouseDownData']) || null;
        var draging_data = opt && (opt['draging_data'] || opt['dragingData']) || null;
        var drag_up_data = opt && (opt['drag_up_data'] || opt['dragUpData']) || null;
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
        var box = this;
        var doc = document;
        var mousedownX = 0;
        var mousedownY = 0;
        var moveTop = 0;
        var moveLeft = 0;
        var moveable = false;
        //清除鼠标选中的内容
        var clearSel = "getSelection" in window ? function () {
            global.getSelection().removeAllRanges();
        } : function () {
            document.selection.empty();
        };
        positionType = positionType || 'absolute';
        //禁止选择内容
        box.select(function (e) {
            if (moveable) {
                e.preventDefault();
            }
        });
        box.find(contentClassGet).select(function (e) {
            if (moveable) {
                e.preventDefault();
            }
        });
        var titleObj;
        var hasSetWidth = false;
        var beforeWidth = false;
        var hasSetBottom = false;
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
                e = e.touches[0];
                clearSel();
                moveable = true;
                var pos = box.position();
                moveTop = pos.top;
                mousedownY = moveTop - e.clientY;
                if(mousedown_data) {
                    var down_func = mousedown_data[0];
                    if(down_func) down_func([x, y], draging_data[1], draging_data[2], draging_data[3]);
                }
                hasSetWidth = box.attr('style') && box.attr('style').toLocaleString().indexOf('width') !=-1;
                hasSetBottom = box.attr('style') && box.attr('style').toLocaleString().indexOf('bottom') !=-1;
                beforeWidth = parseFloat(box.css('width'));
                $(doc)[0].addEventListener('touchmove', function (evt) {
                    window.event? window.event.returnValue = false : evt.preventDefault();//防止屏幕跟着滚动
                    var touch = evt.touches[0];
                    if (moveable) {
                        var hideTop = $(doc).scrollTop();
                        var y = mousedownY + touch.clientY;
                        y = y < hideTop ? hideTop : y;
                        box.css({
                            left: x,
                            top: y,
                            bottom: 'auto',
                            'z-index': 1000000
                        });
                        if(draging_data) {
                            var draging_func = draging_data[0];
                            if(draging_func) draging_func([x, y], draging_data[1], draging_data[2], draging_data[3]);
                        }
                    }
                });
                $(doc)[0].addEventListener('touchend', function () {
                    if (moveable) {
                        if(drag_up_data) {
                            var drag_up_func = drag_up_data[0];
                            if(drag_up_func) drag_up_func([x, y], drag_up_data[1], drag_up_data[2], drag_up_data[3]);
                        }
                        box.css('z-index', box.attr('last-index'));
                        moveable = false;
                        mousedownY = 0;
                        moveTop = 0;
                        //如果之前未设置过宽度 停止时要去掉width
                        if(!hasSetWidth) {
                            var newStyle = box.attr('style');
                            newStyle = newStyle.replace(/width:\s*([^;]+);*/, '');
                            box.attr('style', newStyle);
                        }
                        //如果之前未设置过bottom 停止时要去掉bottom
                        if(!hasSetBottom) {
                            var newStyle = box.attr('style');
                            newStyle = newStyle.replace(/bottom:\s*([^;]+);*/, '');
                            box.attr('style', newStyle);
                        }
                    }
                });
            });
        } else {//pc端
            var titleObj;
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
            titleObj.mousedown(function (e) {
                e.preventDefault();
                clearSel();
                if(e.button == 2) return ;//右键无效
                moveable = true;
                mousedownX = e.pageX;
                mousedownY = e.pageY;
                // console.log('mousedownX:'+ (mousedownX));
                if(!setMaxTop && parentBox) {
                    maxTop = parentBox.outerHeight() + parentBox.offset().top  - liHeight;
                }
                hasSetWidth = box.attr('style') && box.attr('style').toLocaleString().indexOf('width') !=-1;
                hasSetBottom = box.attr('style') && box.attr('style').toLocaleString().indexOf('bottom') !=-1;
                beforeWidth = parseFloat(box.css('width'));
                var pos = box.position();
                if(bottomDistance> 0) {
                    maxTop -= bottomDistance;
                }
                var hideTop = $(document).scrollTop();
                var x, y, wd;
                moveTop = pos.top;
                // if(hideTop) moveTop -= hideTop; //不能减去滚动的高度 否则对象的Y坐标会偏移
                moveLeft = pos.left;

                if(mousedown_data) {
                    var down_func = mousedown_data[0];
                    if(down_func) down_func([x, y], draging_data[1], draging_data[2], draging_data[3]);
                }

                $(doc).mousemove(function (evt) {
                    if (moveable) {
                        var dc_wd = $(document).outerWidth();
                        var hideTop = 0;
                        hideTop = hideTop + minTop;
                        x = moveLeft + evt.pageX - mousedownX;
                        // console.log('x1:'+ x);
                        // console.log('maxLeft:'+ maxLeft);
                        if(positionType == 'absolute') {
                            y = moveTop + evt.pageY - mousedownY;
                        } else {
                            y = (moveTop-hideTop) + (evt.pageY - mousedownY);
                        }
                        // console.log('x + wd:'+ (x + wd));
                        // console.log('dc_wd:'+ (dc_wd));
                        if(limitX) {
                            wd = box.outerWidth();
                            if((x + wd) > dc_wd - 2 ) {
                                x = (dc_wd - wd - 2) ;
                                mousedownX = evt.pageX;
                                moveLeft = box.position().left;
                            } else if(x < minLeft) {
                                x = minLeft;
                                mousedownX = evt.pageX;
                                moveLeft = box.position().left;
                            }
                            // console.log('x3:'+ x);
                            if(maxLeft) x = x > maxLeft ? maxLeft : x;
                        }
                        if(limitY) {
                            if(maxTop) y = y > maxTop ? maxTop : y;
                            // console.log('maxTop:'+ maxTop);
                            // console.log('minLeft:'+ minLeft);
                            // console.log('hideTop:'+ hideTop);
                            if(positionType == 'absolute') {
                                // console.log('hideTop:'+ hideTop);
                                if(y < hideTop) {
                                    y = hideTop;
                                    mousedownY = evt.pageY;
                                    moveTop = box.position().top;
                                }
                                // console.log('y:'+ y);
                            } else {
                                if(y < 0) {
                                    y = 0;
                                    mousedownY = evt.pageY;
                                    moveTop = box.position().top;
                                }
                            }
                        }
                        // console.log('x4:'+ x);
                        var setCss = {
                            left: x,
                            top: y,
                            bottom: 'auto',
                            'z-index': 1000000
                        };
                        setCss['width'] =  beforeWidth;
                        box.css(setCss);
                        if(draging_data) {
                            var draging_func = draging_data[0];
                            if(draging_func) draging_func([x, y], draging_data[1], draging_data[2], draging_data[3]);
                        }
                    }
                });
                $(doc).mouseup(function (event) {
                    if (moveable) {
                        box.css('z-index', box.attr('last-index'));
                        if(drag_up_data && !isUndefined(x) && !isUndefined(y)) {
                            var drag_up_func = drag_up_data[0];
                            if(drag_up_func) drag_up_func([x, y], drag_up_data[1], drag_up_data[2], drag_up_data[3]);
                        }
                        //如果之前未设置过宽度 停止时要去掉width
                        if(!hasSetWidth) {
                            var newStyle = box.attr('style');
                            newStyle = newStyle.replace(/width:\s*([^;]+);*/, '');
                            box.attr('style', newStyle);
                        }
                        //如果之前未设置过bottom 停止时要去掉bottom
                        if(!hasSetBottom) {
                            var newStyle = box.attr('style');
                            newStyle = newStyle.replace(/bottom:\s*([^;]+);*/, '');
                            box.attr('style', newStyle);
                        }
                        moveable = false;
                        mousedownX = 0;
                        mousedownY = 0;
                        moveTop = 0;
                        moveLeft = 0;
                        $(doc).unbind('mousemove');
                        $(doc).unbind('mouseup');
                    }
                });
            });
        }
    };
    var boxIDName = 'lr_move_box_';//定义统一的box id前缀
    var boxBgId = 'lr_box_bg';//要和css的名字对应
    var dialogTopId = 'lr_box_top_bar';//标题栏的id
    var boxBarId = 'lr_box_btn_bar';//按钮栏的id
    var boxObjArray = []; //所有的box对象
    //获取最新的box对象
    var getLastBoxObj = function() {
        if(boxObjArray.length == 0) return '';
        return boxObjArray[(boxObjArray.length - 1)];
    };
    //取唯一的背景
    var getBg = function () {
        return $('#'+ boxBgId);
    };
    //定义背景是否正在消失，新的窗口打开时要检测背景是否正在消失，是的话立刻去掉背景
    var bgIsFadingOut = false;
    //获取box新的x、y坐标 以适应页面大小
    var getBoxXy = function (boxWidth, addTop) {
        var winWidth = parseFloat($(global).outerWidth(true));
        if(!isNaN(boxWidth) && boxWidth > winWidth) boxWidth = winWidth;
        //重置宽和高和坐标
        var y_ = getScrollY(addTop);
        var x_ = (winWidth / 2) - (parseFloat(boxWidth) / 2);
        if(x_<0) x_ = 0;
        return {x: x_, y:y_};
    };
    //隐藏背景 背景隐藏的过程不能太久 以为新的图层打开需要重新生成背景 如果这个背景一直在消失 新的图层会丢失背景
    var hideAndRemoveBg = function () {
        //背景如果延迟移除，在关闭窗口并且刷新时，会导致新的窗口误以为仍有背景，导致最终背景丢失。
        /* getBg().css({zIndex: 0}).animate({
         backgroundColor: '#fff'
         }, 100, function() {
         $(this).remove();
         });*/
        var bg = getBg();
        if(bg.length > 0) {
            bgIsFadingOut = true;
            bg.fadeOut(200, function () {
                $(this).remove();
                bgIsFadingOut = false;
            })
        }
    };
    //移除背景
    var removeBg = function () {
        getBg().remove();
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
            removeBoxObj(boxObj);
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
                // console.log('remove.removeLastFaddingBox');
                removeBoxObj(this.boxObj);
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
                removeBoxObj(this.boxObj);
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
        if(!boxObj) return;
        if(typeof boxObj != 'object') return;
        timer = isUndefined(timer) ? 0 : timer;
        if(boxObj.stop) boxObj.stop(); //停止当前层的消失动作 ,防止当前层在消失过程中被关闭 引起注其他新层的错误
        //移除内部所有我们创建的的lr_name... window对象
        var allNames = boxObj.find('[name]');
        $.each(allNames, function (n, obj_) {
            var tagName = $(obj_).attr('name');
            if(window[tagName]) {
                window[tagName] = null;
                //console.log('remove:'+ tagName);
            }
        });
        // console.log(allNames);
        if (timer > 0) {
            boxObj.fadeOut(timer, function () {
                $(this).remove();
                removeQueueBoxObj(boxObj); //删除队列
            });
        } else {
            boxObj.remove();
            removeQueueBoxObj(boxObj);//删除队列
        }
        //每次移除box 都要判断是否还有全屏窗口 全屏窗口会让body overflow:hidden
        if($('body').hasClass('remove_scroll_bar')) {
            if($('.full_msg_box').length == 0 ) {
                $('body').removeClass('remove_scroll_bar');
            }
        }
        if (boxObjArray.length == 0) {
            hideAndRemoveBg();
        }
    };
    //从队列中清除某个boxid
    var removeQueueBoxObj = function(boxObj) {
        for (var i = 0; i < boxObjArray.length; i++) {
            if (boxObjArray[i] == boxObj) {
                boxObjArray.splice(i, 1);
            }
        }
        if(boxObjArray.length == 0) {
            hideAndRemoveBg();
            return;
        }
        var lastBox = getLastBoxObj();
        if(lastBox) { //背景后移
            //不能按global的去减1 因为最后一个box的zindex可能比global的zindex小
            getBg().css({zIndex: (lastBox.zIndex - 1) });
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
        var winH = $(global).outerHeight(true);//浏览器实际可见高度
        if (addTop.toString().indexOf('%') != -1) {
            addTop = (parseFloat(addTop.replace(/%/, '')) / 100) * winH;
        }
        addTop = Math.abs(addTop) || 0;//累加头部高度
        var winScrolltop = $(document).scrollTop();
        var y_ = winScrolltop + addTop;
        // console.log('y_:'+ y_);
        y_ = y_ < winScrolltop ? winScrolltop : y_;
        y_ = y_ < 0 ? 0 : y_;
        return y_;
    };
    //按钮放大变成窗口
    global.makeMovingBox = function (box, width, x_, y_, opener, url) {
        url = url || '';
        var fadeBtn = $('<div id="fadding_btn"></div>');
        $('body').append(fadeBtn);
        var btnPoss = opener.offset();
        box.css({width: width, display: 'none', left: x_ + "px", top: y_});
        fadeBtn.css({
            left: btnPoss.left,
            top: btnPoss.top,
            width: opener.outerWidth(),
            height: opener.outerHeight()
        }).fadeIn(5);
        if(url.length > 0) {
            box.find(contentClassGet).html('<p class="loading_box">努力加载中...</p>').css({'min-height': 100}).load(url + '&rad=' + Math.random(), function () {
                var newLeft = $(global).width()/2 + box.outerWidth()/2;
                var controlBtns = box.find('#control_btn_fix');
                controlBtns.css({left: newLeft, display: 'block'});
                fadeBtn.stop().animate({
                    width: width,
                    height: box.outerHeight(),
                    left: x_,
                    top: y_
                }, 260, function () {
                    box.fadeIn(50);
                    //去掉按钮
                    fadeBtn.fadeOut(function () {
                        $(this).remove();
                    });
                });
            });
        } else {
            var newLeft = $(global).width()/2 + box.outerWidth()/2;
            var controlBtns = box.find('#control_btn_fix');
            controlBtns.css({left: newLeft, display: 'block'});
            fadeBtn.animate({
                width: width,
                height: box.outerHeight(),
                left: x_,
                top: y_
            }, 260, function () {
                box.fadeIn(50);
                //去掉按钮
                fadeBtn.fadeOut(function () {
                    $(this).remove();
                });
            });
        }
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
               // 'x' : 0,// 默认弹窗绝对定位left值 传递时js已经直接算好
               // 'y' : 0,// 默认弹窗绝对定位top值 传递时js已经直接算好
            'canDrag' : false,//能不能拖动
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
            'timer' : 2000
        };
        if(isUndefined(options['tag']))  options['tag'] = 'div';
        if($.inArray(options['tag'], ['div', 'span']) ==-1) options['tag'] = 'div';
        options = $.extend({}, defaultOption, options);
        //转驼峰
        if(!isUndefined(options['can_drag'])) options['canDrag'] = options['can_drag'];
        if(!isUndefined(options['close_btn'])) options['closeBtn'] = options['close_btn'];
        if(!isUndefined(options['position_type'])) options['positionType'] = options['position_type'];
        if(!isUndefined(options['msg_hide'])) options['msgHide'] = options['msg_hide'];
        if(!isUndefined(options['bg_click'])) options['bgClick'] = options['bg_click'];
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
        // console.log('fadeIn', fadeIn);
        // console.log('msgHideWait', msgHideWait);
        // console.log('msgHideTime', msgHideTime);
        var fangda = options['fangda'] || options['fangDa'] || options['fd'] || false;//放大
        if(fangda) fadeIn = false;//放大时 不能渐渐出现
        var fadeInTime = options['fadeInTime'] || 500;//出现的过程时间
        var fangdaTime = options['fangdaTime'] || 300;//放大的过程时间
        var newBoxId = options['id'];
        var text_ = options.text;
        //初始化window的全局zIndex
        var currentBoxIndex = 0; //当前box的 zIndex 值
        if(zIndex) {
            currentBoxIndex = zIndex;
        } else {
            if(typeof global.zIndex != 'undefined') {
                currentBoxIndex = global.zIndex + 2; //必须加2 因为要给背景层退1留值
                global.zIndex = currentBoxIndex;
            } else {
                currentBoxIndex = global.zIndex = 1600;
            }
        }

        // < ckeditor的层是 z-index: 10010;
        // > bootstrap .navbar-fixed-top, .navbar-fixed-bottom z-index: 1030
        if ($('#' + newBoxId).length > 0) {
            $('#' + newBoxId).show();
            return;
        }
        var boxObj = $('<'+ options['tag'] +' id="'+ newBoxId +'" class="'+ options['class'] +'"></'+ options['tag'] +'>');
        //允许外部获取box的id
        boxObj.id = newBoxId;
        boxObj.zIndex = currentBoxIndex;
        boxObj.css('z-index', currentBoxIndex).attr('last-index', currentBoxIndex);
        var titleObj;
        var msgType = options['type'];
        if (options['title']) {
            titleObj = $('<div class="'+ dialogTopId +'" >' + options['title'] + '</div>');
            if(options['closeBtn']) {
                var closeXX = $('<button type="button" class="'+ (isPc() ? 'close_pc': 'close_wap') +'" aria-hidden="true"></button>');
                closeXX.on('click', function (e) {
                    e.preventDefault();
                   // e.stopPropagation();//需要触发外部的body事件，如 任务功能的日历菜单，当发布任务的窗口已经关闭，则不需要再展开日历菜单
                    removeBoxObj(boxObj);
                });
                titleObj.append(closeXX);
            }
        } else {//加个顶盖
            titleObj = $('<div class="top_angle" ></div>');
        }
        boxObj.append(titleObj);
        var contentObj = $('<div class="'+ contentClass +' clearfix"></div>');
        if(msgType=='msg') {
            contentObj.addClass('msg');
        }
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
                var obj_ = $('<div>' +
                    '<img src="data:image/gif;base64,R0lGODlhEAAQALMPAHp6evf394qKiry8vJOTk83NzYKCgubm5t7e3qysrMXFxe7u7pubm7S0tKOjo////yH/C05FVFNDQVBFMi4wAwEAAAAh+QQJCAAPACwAAAAAEAAQAAAETPDJSau9NRDAgWxDYGmdZADCkQnlU7CCOA3oNgXsQG2FRhUAAoWDIU6MGeSDR0m4ghRa7JjIUXCogqQzpRxYhi2HILsOGuJxGcNuTyIAIfkECQgADwAsAAAAABAAEAAABGLwSXmMmjhLAQjSWDAYQHmAz8GVQPIESxZwggIYS0AIATYAvAdh8OIQJwRAQbJkdjAlUCA6KfU0VEmyGWgWnpNfcEAoAo6SmWtBUtCuk9gjwQKeQAeWYQAHIZICKBoKBncTEQAh+QQJCAAPACwAAAAAEAAQAAAEWvDJORejGCtQsgwDAQAGGWSHMK7jgAWq0CGj0VEDIJxPnvAU0a13eAQKrsnI81gqAZ6AUzIonA7JRwFAyAQSgCQsjCmUAIhjDEhlrQTFV+lMGLApWwUzw1jsIwAh+QQJCAAPACwAAAAAEAAQAAAETvDJSau9L4QaBgEAMWgEQh0CqALCZ0pBKhRSkYLvM7Ab/OGThoE2+QExyAdiuexhVglKwdCgqKKTGGBgBc00Np7VcVsJDpVo5ydyJt/wCAAh+QQJCAAPACwAAAAAEAAQAAAEWvDJSau9OAwCABnBtQhdCQjHlQhFWJBCOKWPLAXk8KQIkCwWBcAgMDw4Q5CkgOwohCVCYTIwdAgPolVhWSQAiN1jcLLVQrQbrBV4EcySA8l0Alo0yA8cw+9TIgAh+QQFCAAPACwAAAAAEAAQAAAEWvDJSau9WA4AyAhWMChPwXHCQRUGYARgKQBCzJxAQgXzIC2KFkc1MREoHMTAhwQ0Y5oBgkMhAAqUw8mgWGho0EcCx5DwaAUQrGXATg6zE7bwCQ2sAGZmz7dEAAA7"/> ' +
                    '努力加载中...</div>');
                contentObj.append(obj_);
                obj_.load(text_.substr(5));
            } else {
                contentObj.append(text_);
            }
        }
        boxObj.append(contentObj);
        if(bgIsFadingOut) {
            removeBg();
        }
        var boxBg = getBg();
        //加背景 其他元素不可控制
        if (options.bg) {
            var bgCss = {};
            if (boxBg.length == 0) {
                boxBg = $("<div id='"+ boxBgId +"' data-click='"+ options.bgClick +"'></div>");
                if(options.bgOpacity) boxBg.css('opacity', options.bgOpacity);
                boxBg.on({
                    'click': function () {
                        // console.log('options.bgClick');
                        // console.log(options.bgClick);
                        if(options.bgClick == 'close') {
                            // console.log('closed');
                            hideNewBox();
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
                boxBg.fadeIn(400);//首次创建背景 才加渐变
            }
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
                removeBoxObj(boxObj);
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
        boxObjArray.push(boxObj);
        //如果弹窗要渐渐显示，开始必须要先隐藏
        if(fadeIn) boxObj.hide();
        $('body').append(boxObj);
        if (options.canDrag) {
            if(!options.positionType) options.positionType = '';
            if (!options.title) {
                if(!hasBar) boxBarId = contentClass;
                if(boxBar) boxBar.css('cursor', 'move');
                boxObj.Drag(boxBar, options.positionType, {drag_up_func: options.dragUp}); //注册拖拽事件
            } else {
                boxObj.find(titleObj).css('cursor', 'move');
                boxObj.Drag(titleObj, options.positionType, {drag_up_func: options.dragUp});
            }
        }
        var boxWidth = options.width ? options.width : boxObj.outerWidth();//允许外部提前定义box的宽度 因为像gif这样的图片会加载完才能获取到box真实的width
        var boxHeight = options.height ? options.height : 0;
        if(!boxWidth) boxWidth = 100;
        var x_,y_;
        // console.log('options.top:');
        if(options.width == 'auto') {
            boxWidth = parseFloat(boxObj.outerWidth());
        }
        var widthXY = getBoxXy(boxWidth, options.top);
        x_ = widthXY.x;
        y_ = widthXY.y;
        if(!isUndefined(options.x))  x_ = options.x;//直接传入绝对位置时
        if(!isUndefined(options.y))  y_ = options.y;//直接传入绝对位置时
        if(options.positionType == 'fixed') y_ = options.top;//fixed 直接按填写的值
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
                // console.log('msgHideWait:', msgHideWait);
                var tmpTimeId = setTimeout(function () {
                    boxObj.fadeOut(msgHideTime, function () {
                        removeBoxObj(boxObj);
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
        // console.log('fadeIn__', fadeIn);
        // console.log('fadeInTime', fadeInTime);
        if(fadeIn) {
            var setCss = {width: boxWidth,  "position": options.position_type, 'display': 'none'};
            if(boxHeight) setCss['height'] = boxHeight;
            if(x_ !=='') setCss['left'] = x_;
            if(y_ !== '') setCss['top'] = y_;
            boxObj.css(setCss);
            setTimeout(function () {
                // console.log('fadeIn:');
                boxObj.fadeIn(fadeInTime, function () {
                    _callFadeout();
                });
            }, 20);
        } else if(fangda) {
            var setCss = {width: boxWidth, "position": options.positionType, "display": 'block'};
            if(x_) setCss['left'] = x_;
            if(y_) setCss['top'] = y_;
            var fangdaClass = ' fangda_t' + parseInt(fangdaTime/100);
            boxObj.css(setCss).addClass('fangda_box' + fangdaClass);
        } else {
            var setCss = {display: 'block', left: x_, 'position': options.positionType};
            if(x_) setCss['left'] = x_;
            if(y_) setCss['top'] = y_;
        }
        var _changeJuli = function (jl_, fxNew) {
            if(jl_.toString().indexOf('%') !=-1) {
                jl_ = parseFloat(jl_.toString().replace('%'))/100;
            }
            if(jl_.toString().indexOf('.') !=-1) {
                if(fxNew =='s' || fxNew =='x') jl_ = jl_ * $(window).height();
                if(fxNew =='y' || fxNew =='z') jl_ = jl_ * $(window).width();
            }
            if(fxNew=='x') jl_ += $(window).scrollTop();
            return jl_;
        };
        if(msgMoveDirection) {
            setCss['display'] = 'block';
            //初始化方向
            var fx_ = _checkDir(msgMoveDirection);
            if(fx_ =='s') {
                setCss['top'] = 'auto';
                setCss['bottom'] = 0;
            } else if(fx_ =='x') {
                setCss['top'] = 0;
                setCss['bottom'] = 'auto';
            } else if(fx_ =='z') {
                setCss['top'] = options['top'] || '35%';
                setCss['right'] = 0;
                setCss['left'] = 'auto';
            } else if(fx_=='y') {
                setCss['top'] = options['top'] || '35%';
                setCss['left'] = 0;
            }
            boxObj.css(setCss);
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
            //console.log('浮起来', fadeIn);
        } else {
            // console.log('');
            boxObj.css(setCss);
            if(!fadeIn) _callFadeout();
        }
        return boxObj;
    };

    //清除所有队列和提示框
    global.hideAllBox = function() {
        var len = boxObjArray.length;
        for(var i =0;i < len;i++) {
            removeBoxObj(getLastBoxObj());
        }
        //释放所有的动画变量
        bgIsFadingOut = false;
        // console.log('release');
    };
    //删除最新的提示框
    global.hideNewBox = function() {
        //取最新的boxid
        removeBoxObj(getLastBoxObj());
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
        return makeBox(options);
    };
    //提示框扩展:提示内容 无自动隐藏 按钮 放大
    global.msgf = function(text, btnText, options) {
        options = $.extend({}, {bg: false, 'fangda': true}, options);
        return msg(text, btnText, options)
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
            canDrag: isPc() //pc端才允许拖动
        }, options);
        return makeBox(options);
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
        msgView('', '<img src="'+ url +'" />'+ moreText, width, scrollTop, options);
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
            canDrag: isPc() //pc端才允许拖动
        }, options);
        makeBox(options);
    };

    //无边的浮动 只有右上角带关闭按钮
    global.msgViewAd = function(url, options) {
        options = options || [];
        var scrollTop = !isUndefined(options['top']) ? options['top'] : '';//减去高度
        var width = !isUndefined(options['width']) ? options['width'] : '';
        var text_ = (url.substr(0, 1) == '/' || url.substr(0, 7) == 'http://' || url.substr(0, 8) == 'https://') ? '<img src="'+ url +'" />' : url;
        makeBox({
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
            // console.log('currentViewImgData');
            // console.log(currentViewImgData);
            if(findItem) {
                // console.log('findItem');
                // console.log(findItem);
                var imgNewCss = [];
                previewImg.removeAttr('data-rotate');   //清空之前图片的旋转属性
                previewImg.removeAttr('style');   //清空之前图片的旋转属性
                previewImg.attr('src', findItem.url);
                if(findItem.width>0 && findItem.height > 0) {
                    var width_ = findItem.width;
                    var height_ = findItem.height;
                    var imgSizes = __countImgSize(width_, height_);
                    var imgResizeWidth = imgSizes[0];
                    var imgResizeHeight = imgSizes[1];
                    imgNewCss.push("width:" + imgResizeWidth +"px");
                    imgNewCss.push("height:" + imgResizeHeight +"px");
                    imgNewCss.push("left:" + (($(window).width() - imgResizeWidth) /2) +"px");
                    imgNewCss.push("top:" + (($(window).height() - imgResizeHeight) /2) +"px");
                    //重置图片比例
                    imgWrap.removeAttr('data-bili').attr('style', imgNewCss.join(';'));
                    //下载按钮重置
                    downloadObj.attr('data-success', 0);
                }
            }
            //计算  下一张 的按钮样式
            resetPrevNextBtn(getIndex);
        }
        var newBoxId = createNewBoxId();
        var currentUrlEncode = $.md5(currentImgurl);
        var findImgData = currentViewImgData[currentUrlEncode] || null;
        if(!findImgData) {
            console.log(currentViewImgData);
            console.log('轮播中找不到图片:'+ currentImgurl);
            return;
        }
        currentViewImgIndex = findImgData['index'];
        if(currentViewImgIndex == -1) {
            msgTisf('找不到url:'+ currentImgurl);
            return;
        }
        // console.log(findImgData);
        var width_ = findImgData['width'];
        var height_ = findImgData['height'];
        var imgResizeWidth = 0;
        var imgResizeHeight = 0;
        var imgLeft = 0;
        var imgTop = 0;
        //初始化图片尺寸：如果提前定义好了宽度和高度
        if(width_>0 && height_>0 ) {
            var imgSizes = __countImgSize(width_, height_);
            imgResizeWidth = imgSizes[0];
            imgResizeHeight = imgSizes[1];
            imgLeft = ($(window).width() - imgResizeWidth) /2;
            imgTop = ($(window).height() - imgResizeHeight) /2;
        } else {
            imgLeft = $(window).width() /2;
            imgTop = $(window).height() /2;
        }
        var mapCss = "left:" + imgLeft + 'px;top: '+ imgTop + 'px;';
        if(imgResizeWidth > 0) {
            mapCss += "width:"+ imgResizeWidth + "px;";
            mapCss += "height:"+ imgResizeHeight + "px;";
        }

        //img必须带id来实时获取 ,第一次旋转之后img会被 替换为canvas
        var mapObj = $("<div id='preview_img_box'>" +
                    "<div class='img-wrap' style=' "+ mapCss +"'>" +
                    "<img class='rote_img' alt='预览图片' />" +
            "</div></div>");
        var viewImgBox = $("<div id='view_list_img_box'><div class='inner'></div></div>");
        var closeBtn = $("<div class='close_btn'> <img src='/Static/box/close.png' width='100%' alt='关闭预览' title='关闭预览' /></div>");
        closeBtn.click(function () {
            loadingObj.removeLastLoadingBox();//把loading也一起关闭
            removeBoxObj(global.viewFullImgObj);
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
            removeBoxObj(global.viewFullImgObj);
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
            hide: false //自动隐藏
        };
        opts['x'] = 0;
        opts['y'] = 0;
        opts['width'] = '100%';
        opts['height'] = '100%';
        opts['class'] = 'view_full_img';
        opts['top'] = 0;
        opts['bgOpacity'] = 0.6;
        opts['positionType'] = 'fixed';
        opts['bgClick'] = 'close';  //背景点击：关闭窗口
        var winBox = makeBox(opts);
        global.viewFullImgObj = winBox;
        //计算图片缩小后的大小
        function __countImgSize(imgWidth, imgHeight) {
            var maxMapWidth = 1200;
            var maxMapHeight = 700;
            var bili = imgWidth / imgHeight;
            var maxData = [];
            var imgResizeWidth = imgWidth;
            var imgResizeHeight = imgHeight;
            // console.log('lastwidth > imgHeight:'+  imgWidth + '>'+ imgHeight);
            if(imgWidth > maxMapWidth) {
                imgResizeWidth = maxMapWidth;
                imgResizeHeight = maxMapWidth / bili;
                if(imgResizeHeight > maxMapHeight) {
                    imgResizeHeight = maxMapHeight;
                    imgResizeWidth = imgResizeHeight * bili;
                    maxData = [imgResizeWidth, imgResizeHeight];
                }
            }
            if(imgHeight > maxMapHeight) {
                var imgResizeHeight = maxMapHeight;
                var imgResizeWidth = maxMapHeight * bili;
                if(imgResizeWidth > maxMapWidth) {
                    imgResizeWidth = maxMapWidth;
                    imgResizeHeight = imgResizeWidth / bili;
                }
            }
            maxData = [imgResizeWidth, imgResizeHeight];
            return maxData;
        }
        //滚动滚轮 设置图片大小
        function _onWheel(e) {
            var e = e || window.event;
            e.preventDefault(); //禁止滚动页面
            var imgPos  = imgWrap.offset();
            var clientX = e.clientX;
            var clientY = e.clientY;
            var distanceX = clientX - imgPos.left;
            var distanceY = clientY - imgPos.top;
            var deltaY = e.deltaY || e.detail; //火狐底下y就是 detail值
            var lastWidth = imgWrap.outerWidth(true);
            var lastHeight = imgWrap.outerHeight(true);
            var mouseBiliX = distanceX / lastWidth;
            var mouseBiliY = distanceY / lastHeight;
            // console.log('lastWidth', lastWidth);
            // console.log('lastHeight', lastHeight);
            // console.log('mapObj.scrollTop():'+ mapObj.scrollTop());
            // console.log(distanceY / lastHeight);
            var bili = 0;
            if(!imgWrap.attr('data-bili')) {
                bili = lastWidth/lastHeight;
                imgWrap.attr('data-bili', bili);
            } else {
                bili = imgWrap.attr('data-bili');
            }
            var lastStyle = imgWrap.attr('style');
            var changeWidth = 100;
            if(deltaY > 0) { //缩小
                if(lastWidth < 150) return ;
                var newWidth = lastWidth - changeWidth;
                var newHeight = newWidth / bili;
                var changeHeight = lastHeight - newHeight;
                // console.log('缩小');
                // console.log('lastStyle:'+ lastStyle);
                lastStyle = lastStyle.replace(/height:\s*([0-9.-]+)px;/i, "height:"+ newHeight + 'px;');
                lastStyle = lastStyle.replace(/width:\s*([0-9.-]+)px;/, "width:"+ newWidth + 'px;');
                //图片没有被拖拽过时时 才时刻保持居中
                // if(!imgWrap.attr('moved')) {
                //     var imgLeft = ($(window).width() - newWidth) / 2;
                //     var imgTop = ($(window).height() - newHeight) / 2;
                //     console.log('imgTop:'+ imgTop);
                //     lastStyle = lastStyle.replace(/left:\s*([0-9.-]+)px/, "left:" + imgLeft + 'px');
                //     lastStyle = lastStyle.replace(/top:\s*([0-9.-]+)px/, "top:" + imgTop + 'px');
                // } else {
                    //根据变化的xy,自适应修改坐标
                    var lastLeft = imgPos.left;
                    var lastTop = imgPos.top;
                    var lastLeft =  lastLeft + (changeWidth * mouseBiliX);
                    var lastTop =  lastTop + (changeHeight * mouseBiliY);
                    lastStyle = lastStyle.replace(/left:\s*([0-9.-]+)px/, "left:" + lastLeft + 'px');
                    lastStyle = lastStyle.replace(/top:\s*([0-9.-]+)px/, "top:" + lastTop + 'px');
                // }
                // console.log('newStyle:'+ lastStyle);
                imgWrap.attr('style', lastStyle);
            } else {//放大
                var newWidth = lastWidth + 100;
                var newHeight = newWidth / bili;
                var changeHeight = newHeight - lastHeight;
                // console.log('放大');
                // console.log('lastStyle:'+ lastStyle);
                // console.log('newWidth:'+ newWidth);
                lastStyle = lastStyle.replace(/height:\s*([0-9.-]+)px;/i, "height:"+ newHeight + 'px;');
                lastStyle = lastStyle.replace(/width:\s*([0-9.-]+)px/i, "width:"+ newWidth + 'px');
                //图片没有被拖拽过时时 才时刻保持居中
                // if(!imgWrap.attr('moved')) {
                //     var imgLeft = ($(window).width() - newWidth) /2;
                //     var imgTop = ($(window).height() - newHeight) /2;
                //     console.log('imgTop:'+ imgTop);
                //     lastStyle = lastStyle.replace(/left:\s*([0-9.-]+)px/, "left:"+ imgLeft + 'px');
                //     lastStyle = lastStyle.replace(/top:\s*([0-9.-]+)px/, "top:"+ imgTop + 'px');
                // } else {
                    //根据变化的xy,自适应修改坐标
                    var lastLeft = imgPos.left;
                    var lastTop = imgPos.top;
                    var lastLeft =  lastLeft - (changeWidth * mouseBiliX);
                    var lastTop =  lastTop - (changeHeight * mouseBiliY);
                    lastStyle = lastStyle.replace(/left:\s*([0-9.-]+)px/, "left:" + lastLeft + 'px');
                    lastStyle = lastStyle.replace(/top:\s*([0-9.-]+)px/, "top:" + lastTop + 'px');
                // }
                // console.log('newStyle:'+ lastStyle);
                imgWrap.attr('style', lastStyle);
            }

        }
        previewImg.on('load', function () {
            // console.log('on_load');
            loadingObj.removeLastLoadingBox();
            //注册拖拽事件
            if(!imgWrap.attr('regdrag')) {
                imgWrap.attr('regdrag', 1);
                imgWrap.Drag(imgWrap, 'relative', {
                    'parent_box': mapObj,
                    'limit_x': false,
                    'limit_y': false
                });
                var element = imgWrap[0];
                if (typeof element.onmousewheel == "object") {
                    element.onmousewheel = function(e) {
                        _onWheel(e);
                    };
                }
                if (typeof element.onmousewheel == "undefined") {
                    element.addEventListener("DOMMouseScroll", _onWheel, false);
                }
            }
            if(imgResizeWidth ==0) {
                setTimeout(function () {
                    var width_ = $(this).outerWidth();
                    var height_ = $(this).outerHeight();
                    if(width_>0 && height_>0 ) {
                        var imgSizes = __countImgSize(width_, height_);
                        imgResizeWidth = imgSizes[0];
                        imgResizeHeight = imgSizes[1];
                        imgLeft = ($(window).width() - imgResizeWidth) /2;
                        imgTop = ($(window).height() - imgResizeHeight) /2;
                        imgWrap.css({
                            left: imgLeft,
                            top: imgTop
                        })
                    }
                }, 100);
            }
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
            // console.log(box);
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
                hideNewBox();
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
        return makeBox(defaultOpt);
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
        var box = makeBox(defaultOption);
        //隐藏body的滚动条
        $('body').addClass('remove_scroll_bar');
        box.css({left: 0, top: 0, height: '100%'});
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
        var box = makeBox(defaultOption);
        //隐藏body的滚动条
        $('body').addClass('remove_scroll_bar');
        var winHeight = $(global).outerHeight(true);//浏览器实际可见高度
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
            hideNewBox();
        };
        if(typeof cancelFunc == 'string') {
            btnCancel.on('click', function () {
                eval(cancelFunc);
            });
        } else {
            btnCancel.on('click', cancelFunc);
        }
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
        return makeBox(defaultOption);
    };
    //对话框：弹出提示确认  按钮绑定事件：confirmFunc_ cancelFunc 都是传入字符串
    global.msgConfirmf = function(text_, confirmBtnText, cancelBtnText, confirmFunc_, cancelFunc, moreOption) {
        moreOption = $.extend({}, {fangda: true}, moreOption);
        return msgConfirm(text_, confirmBtnText, cancelBtnText, confirmFunc_, cancelFunc, moreOption);
    };
    //黑色提示语 从下往上 或放大 目前手机专用
    //效果：从底部向上浮动一个黑色半透明背景的矩形，只可写文字。
    global.msgTis = function(tisText, options) {
        return __makeTisfBox(tisText, options, false)
    };
    global.msgTisf =  function(tisText, options) {
        options = options || {};
        if(isObj(tisText)) tisText = JSON.stringify(tisText);
        if(getBg().length) options['bg'] = true;
        options = $.extend({
            'wait': 500,
            'hideTime': 300
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
                    // console.log(obj_[tmpName]);
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
        var box = makeBox(options);
        return box;
    };

    //效果：页面中间显示一个loading图标 用于等候数据提交的等待.
    global.loading = function(needBg) {
        if(typeof needBg == 'undefined') needBg = false;
        var loadObj = makeBox({
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
            border: 7
        };
        defaultOpt = $.extend({}, defaultOpt, opt);
        var loadClass = defaultOpt['class'];
        var width = defaultOpt.width;
        var border = defaultOpt.border;
        if(typeof needBg == 'undefined') needBg = false;
        var loadObj = makeBox({
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
    $(document).keyup(function (event) {
        if (event.keyCode == 27) {
            hideNewBox();
        }
    });
})(this, jQuery);


/*
 依赖方法：
 $.url.decode       窗口查看内容使用
 isPc 判断客户端是网页版还是wap   推拽窗口使用
 rotate 旋转图片  查看图片窗口使用

 输出可用函数

* msg 文本提示 生成 需要手动确认关闭
* msgTis 生成 文本提示后 自动消失
* msgView 生成 查看文本信息的窗口
* msgViewImg 查看图片
* msgViewAd 无边的浮动广告  只有右上角带关闭按钮
* msgWait 生成 等待窗口
* msgViewImgFull 生成 浏览图片窗口
* msgWin 生成 大层窗口
* msgWinFull 生成 全屏窗口 wap用
* msgWinHalf 生成 半屏窗口 wap用
* msgConfirm 生成 二选一的窗口
* loginIn 全局登录窗口 。 loginOut 退出登录
* msgTis 底部弹出黑色的提示语
* loading  窗口正中间加loading 。 noLoading  移除所有Loading层
 * hideNewBox  关闭最新的窗口
 * hideAllBox  关闭所有窗口
*
 * loginIn 全局登录窗口 。 logOut 退出登录
* */