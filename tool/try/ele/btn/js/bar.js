define(['require'], function (require) {
    var global = {};
    var objBindAttrsName = 'bind_attrs';
    var objAttrHasKh = 'obj_opt_has_kuohao';//obj的属性包含有{} 则可能绑定全局变量
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入

    //创建拖动条
    //创建文本dom /a/p/span/div/li/td/em/i////
    global.makeBar = function (sourceOptions, sureSource) {
        var core = require('core');
        sourceOptions = sourceOptions || {};
        sureSource = sureSource || false;
        var obj = $('<div></div>');
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            obj.sor_opt = sureSource ?  core.cloneData(sourceOptions || {}) : core.cloneData(core.copySourceOpt(sourceOptions));
        }
        var options = core.cloneData(sourceOptions);
        var setBind = core.getOptVal(options, ['bind'], '');
        var sourceVal = core.getOptVal(options, ['value'], '');
        //统一头部判断结束

        obj.returnVal = sourceVal;
        obj.htmObj = [];//初始化element节点
        if(core.isUndefined(options['value'])) options['value'] = '';
        obj[objValIsNode] = false;
        obj[objAttrHasKh] = false;
        var valueStrFormatdSuccess = false;
        // //支持外部设置值
        Object.defineProperty(obj, 'value', {
            get: function () {
                return obj.returnVal;
            },
            set: function (newVal) {
                if($.isArray(newVal)) newVal = newVal.join(',');
                obj.returnVal = newVal;
                obj.moveBtnByVal(newVal);
            }
        });
        //更新val
        obj.formatVal = function (opt) {
            var newVal;
            var newData = core.getOptVal(opt, ['data'], {});
            var newVal = core._onFormatVal(obj, newData,  sourceVal);
            obj.returnVal = newVal; //参数要改变 防止外部取出来的仍是括号
            if (sourceVal != newVal) {
                valueStrFormatdSuccess = true;
            }
            var renewBind = obj[objAttrHasKh]==true;
            if(setBind && renewBind) {//触发数据同步  触发赋值 */
                core.updateBindObj($.trim(setBind), newVal, [obj]);
            }
            if(valueStrFormatdSuccess) {
                if(obj.lazyCall) {
                    obj.lazyCall(obj, newData, core.livingObj);
                }
            }
            obj.moveBtnByVal(newVal);
        };

        //外部设置val
        obj.extend({
            //主动更新数据
            renew: function(options_) {
                if(!options_)  return;
                var barVal = core.isUndefined(options_['value']) ? '': options_['value'];
                var hasSetData = !core.isUndefined(options_['data']);
                core.optionDataFrom(obj, options_);
                //console.log(dataFrom);
                //console.log(data_);
                var iconOpt = options_['icon']|| {};
                var movingFunc = options_['moving']|| null;
                var direction = options_['direction']|| 'x';
                var mouseUpFunc = options_['mouse_up'] || options_['mouseup'] || options_['mouseUp'] || null;
                var maxVal = core.isUndefined(options_['max']) ? null : options_['max'];
                var minVal = core.isUndefined(options_['min']) ? null : options_['min'];
                var decNum = core.isUndefined(options_['dec']) ? null : options_['dec'];//保留几位小数
                maxVal = core.toNumber(maxVal);
                minVal = core.toNumber(minVal);
                iconOpt['class_extend'] = 'icon';
                var iconMinLeft = iconOpt['min-left']||  iconOpt['min_left']|| iconOpt['minLeft']|| 0;
                var iconMinTop = iconOpt['min-top']||  iconOpt['min_top']|| iconOpt['minTop'] || (direction=='x'?-2:-1);
                iconOpt['left'] = core.toNumber(iconMinLeft) + 'px';
                iconOpt['top'] = parseFloat(iconMinTop) + 'px';
                //console.log('min_left:'+ iconMinLeft);
                iconOpt['width'] = core.isUndefined(iconOpt['width']) ? 30 : iconOpt['width'];
                //console.log('iconOpt');
                //console.log(iconOpt);
                var iconObj = core.makeSpan(iconOpt);
                options_['class_extend'] = 'diy_bar';
                options_['style'] = 'border:1px solid #ddd';
                obj.html('').append(iconObj);
                //根据value定位拖动按钮
                obj.moveBtnByVal = function(newVal) {
                    var iconWidth = core.toNumber(iconObj.outerWidth());
                    var barWidth = core.toNumber(obj.width);
                    var maxLeft = barWidth - iconWidth -2;
                    var iconHeight = core.toNumber(iconObj.outerHeight());
                    var maxTop = obj.outerHeight() - iconHeight -2;
                    if(direction=='x') {
                        var newLeft = (newVal / maxVal) * barWidth;
                        if(iconMinLeft) newLeft = Math.max(newLeft, iconMinLeft);
                        if(maxLeft) newLeft = Math.min(newLeft, maxLeft);
                        iconObj.css('left', newLeft);
                    } else {
                        var newTop = (newVal / maxVal) * barHeight;
                        if(iconMinTop) newTop = Math.max(newTop, iconMinTop);
                        if(maxTop) newTop = Math.min(newTop, maxTop);
                        iconObj.css('top', newTop);
                    }
                };
                //点击定位滚动条
                var userDiyClick = core.getOptVal(options_, ['click'], null)
                options_['click'] = function(obj_, e) {
                    if(e.target !== obj_[0]) return; //not click bar
                    var clientPos = '',xy;
                    if(direction=='x') {
                        var btnWidth = core.toNumber(iconObj.outerWidth(true));
                        var barWidth = core.toNumber(obj.width);
                        clientPos = e.clientX - obj_.offset().left;
                        clientPos -= btnWidth/2; //居中按钮
                        if(clientPos + btnWidth > barWidth -2) clientPos = barWidth - btnWidth -2;
                        if(clientPos < iconMinLeft) clientPos = iconMinLeft;
                        //console.log('clientPos:'+ clientPos);
                        //console.log('btnWidth:'+ (btnWidth/2) );
                        //console.log('barWidth:'+ barWidth);
                        //console.log(bar);
                        xy = [clientPos, 0];
                        iconObj.css('left', clientPos);
                    } else {
                        var btnHeight = core.toNumber(iconObj.outerHeight(true));
                        var barHeight = core.toNumber(obj.height);
                        clientPos = e.clientY - obj_.offset().top;
                        clientPos -= btnHeight/2; //居中按钮
                        if(clientPos + btnHeight > barHeight -2) clientPos = barHeight - btnHeight -2;
                        if(clientPos < iconMinTop) clientPos = iconMinTop;
                        xy = [0, clientPos];
                        iconObj.css('top', clientPos);
                    }
                    xy = obj.countVal(xy);
                    var newVal = direction =='x' ? xy[0] : xy[1];
                    if(!core.isUndefined(options_['bind']) && options_['bind']) {
                        core.updateBindObj($.trim(options_['bind']), newVal, [bar]);
                    }
                    obj.returnVal = newVal;
                    if(userDiyClick) {
                        userDiyClick(obj_, newVal,  e);
                    }
                };
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                //先设定options_参数 下面才可以修改options_
                //console.log('renew call_formatAttr:');
                core.strObj.formatAttr(this, options_, 0, hasSetData); //里面找出事件来绑定
                //console.log('finish');
                //console.log(this);
                //等创建好对象再初始化所有高度
                setTimeout(function () {
                    if(core.strInArray(direction, ['x', 'y']) ==-1) direction = 'x';
                    var iconWidth = iconOpt['width'] || iconObj.outerWidth();
                    iconWidth = core.toNumber(iconWidth);
                    var iconHeight = iconOpt['height'] || iconObj.outerHeight();
                    iconHeight = core.toNumber(iconHeight);
                    var barHeight;
                    if(direction=='x') {
                        barHeight = iconOpt['height'] || iconHeight;
                    } else {
                        barHeight = options['height'] || obj.outerHeight();
                    }
                    barHeight = core.toNumber(barHeight);
                    var barWidth = options['width'] ||  obj.outerWidth();
                    barWidth = core.toNumber(barWidth);
                    //计算刻度尺 当前位置应该得到的值
                    obj.countVal = function (xy) {
                        var distance_,newVal;
                        if(!maxVal) maxVal = barWidth;
                        if(direction=='x') {
                            distance_ = parseFloat(xy[0]);
                            newVal = maxVal * (distance_ - iconMinLeft) / (barWidth - iconWidth-2);
                            newVal = core.formatFloat(newVal, decNum);
                            if(newVal < minVal) newVal = minVal;
                            if(newVal > maxVal) newVal = maxVal;
                            xy[0] = newVal;
                        } else {
                            distance_ = parseFloat(xy[1]);
                            newVal = maxVal * (distance_ - iconMinLeft) / (barHeight - iconHeight-2);
                            newVal = core.formatFloat(newVal, decNum);
                            if(newVal < minVal) newVal = minVal;
                            if(newVal > maxVal) newVal = maxVal;
                            xy[1] = newVal;
                        }
                        return xy;
                    };
                    var movingBackFunc = function (xy, icon, bar_) {
                        xy = obj.countVal(xy);
                        var newVal = direction =='x' ? xy[0] : xy[1];
                        if(!core.isUndefined(options_['bind']) && options_['bind']) {
                            core.updateBindObj($.trim(options_['bind']), newVal, [bar]);
                        }
                        obj.returnVal = newVal;
                        if(movingFunc) movingFunc(xy, icon, bar_);
                    };
                    var movingUpBackFunc = function (xy, icon, bar_) {
                        //console.log(xy);
                        xy = obj.countVal(xy);
                        var newVal = direction =='x' ? xy[0] : xy[1];
                        if(!core.isUndefined(options_['bind']) && options_['bind']) {
                            core.updateBindObj($.trim(options_['bind']), newVal, [bar]);
                        }
                        obj.returnVal = newVal;
                        //console.log(xy);
                        if(mouseUpFunc) mouseUpFunc(xy, icon, bar_);
                    };
                    var maxTop = obj.outerHeight() - iconHeight -2;
                    var maxLeft = barWidth - iconWidth -2;
                    if(maxLeft <iconMinLeft) maxLeft = iconMinLeft;
                    //console.log('iconWidth:'+ iconWidth);
                    //console.log('min_left:'+ iconMinLeft);
                    //console.log('maxLeft:'+ maxLeft);
                    var opt = {
                        min_top: iconMinTop,
                        min_left: iconMinLeft,
                        max_left: maxLeft,
                        max_top: maxTop,
                        draging:  function (v) {
                            movingBackFunc(v, iconObj, obj);
                        },
                        dragup:  function (v) {
                            movingUpBackFunc(v, iconObj, obj);
                        },
                    };
                    iconObj.Drag('', '', opt);
                    //被更时 触发按钮移动
                    if(!core.isUndefined(options_['bind']) && options_['bind']) {
                        var pubVal = core.getObjData($.trim(options_['bind']));
                        if(pubVal ==='') {
                            pubVal = barVal;
                        }
                        obj.moveBtnByVal(pubVal);
                    } else if(barVal) {
                        if(core.isNumber(barVal)){
                            obj.moveBtnByVal(barVal);
                        }
                    }
                }, 20);
            },
            //克隆当前对象
            cloneSelf: function() {
                var opt = core.cloneData(obj.sor_opt);
                return core.makeBar(opt, true);
            },
            updates: function(dataName, exceptObj) {//数据被动同步
                //console.log('updates this:'+ dataName);
                //console.log(exceptObj);
                exceptObj = exceptObj || [];
                if(setBind) {
                    if($.inArray(this, exceptObj) == -1) {
                        exceptObj.push(this);
                        //console.log('update this');
                        //console.log(exceptObj);
                        var pubVal = core.getObjData($.trim(setBind));
                        //console.log('updateNodeText this：'+ pubVal);
                        obj.returnVal = pubVal;
                        obj.moveBtnByVal(pubVal);
                    }
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) {
                    //attrs(如:class) 中含{公式 {dataName} > 2}
                    //如果value中含{}也会由此处开始更新
                    //console.log('renew ObjAttr this');
                    //console.log(this);
                    core.renewObjBindAttr(this, dataName);
                }
            }
        });
        // if(tag == 'form') console.log('here form op:');
        //console.log('herea:');
        //console.log(bar);
        //console.log(defaultOps['value']);
        obj.renew(options);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        core.optionGetSet(obj, options);
        core.objBindVal(obj, options);//数据绑定
        core.addCloneName(obj);//支持克隆
        return obj;
    };

    return global;
});

