define(['require'], function (require) {
    var global = {};
    var objBindAttrsName = 'bind_attrs';
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入
    //创建开关1 移动的圆球 [属性：name,value,width,
    /*
        value_key: 'value', //默认data的值的键名
        text_key: 'text', //默认data的文本的键名
        item: [{
            value: 1,
            text: 'boy'
        },{
            value: 0,
            text: 'girl'
        }],
        */
    global.makeSwitch = function(sourceOptions, sureSource) {
        var core = require('core');
        sourceOptions = sourceOptions || {};
        sureSource = sureSource || false;
        sourceOptions['tag'] = 'switch';
        var obj = $('<span></span>');
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            obj.sor_opt = sureSource ?  cloneData(sourceOptions || {}) : cloneData(copySourceOpt(sourceOptions));
        }
        var options = cloneData(sourceOptions);
        if(isUndefined(options['value'])) options['value'] = '';
        var selectVal = getOptVal(options, ['value'], '');
        var setBind = getOptVal(options, ['bind'], '');
        options['class_extend'] = 'diy_switch';
        obj[objValIsNode] = false;
        obj['switchVal'] = selectVal;
        obj['switchText'] = '';
        var sourceVal = getOptVal(options, 'value', '');
        var iconObj = $('<span class="icon_box"><span class="icon_par"><i class="icon"></i></span><span class="text1"></span><span class="text2"></span></span>');
        var innerText1 = iconObj.find('.text1');
        var innerText2 = iconObj.find('.text2');
        obj.append(iconObj);
        //单独的格式化value的括号
        obj.formatVal = function (opt) {
            opt = opt || [];
            var newData = getOptVal(opt, ['data'], {});
            var selectVal = _onFormatVal(obj, newData,  sourceVal);
            opt['value'] = selectVal; //参数要改变 防止外部取出来的仍是括号
            obj.valChange(selectVal, [obj], false);//自身格式化 不能更新自己的bind 会导致死循环
            if(obj.lazyCall) {
                obj.lazyCall(obj, newData, core.livingObj);
            }
        };

        //支持外部取值 data-value
        Object.defineProperty(obj, 'value', {
            get: function() {
                return obj['switchVal'];
            },
            set: function(V) {
                obj.valChange(V, [this], true);
            }
        });
        //支持外部取值
        Object.defineProperty(obj, 'text', {
            get: function() {
                return obj['switchText'];
            }
        });

        //外部设置属性
        obj.extend({
            //值的修改
            valChange: function (newVal, exceptObj, renewBind) {
                // console.log('val Change', newVal);
                exceptObj = exceptObj || [];
                renewBind = isUndefined(renewBind) ? true : renewBind;
                if(newVal != obj.attr('data-value')) {//obj['value']可能已经提前被同步修改 所以要用attr对比
                    obj.attr('data-value', newVal);
                    if(newVal == innerText1.attr('data-val')) {
                        iconObj.addClass('active');
                        obj['switchText'] = innerText1.data('text') ;
                        obj.attr('title', innerText1.data('text')) ;
                        if(obj.activeColor || obj.active_color) {
                            iconObj.css('backgroundColor', (obj.activeColor||obj.active_color));
                        }
                        innerText1.addClass('activeSw');
                        innerText2.removeClass('activeSw');
                    } else {
                        iconObj.removeClass('active');
                        obj['switchText'] = innerText2.data('text') ;
                        obj.attr('title', innerText2.data('text') ) ;
                        if(obj.inActiveColor || obj.inactive_color) {
                            iconObj.css('backgroundColor', (obj.inActiveColor||obj.inactive_color));
                        }
                        innerText1.removeClass('activeSw');
                        innerText2.addClass('activeSw');
                    }
                }
                obj['switchVal'] = newVal;
                var setText = getOptVal(options, ['setText', 'set_text'], null);
                var newText = obj.text;
                // console.log('newText', newVal, setText, renewBind, newText);
                if($.inArray(obj, exceptObj) == -1) exceptObj.push(obj);
                if(renewBind) {
                    if(newVal.length && setBind && renewBind) {
                        updateBindObj($.trim(setBind), newVal, exceptObj);
                    } else {
                        var lastVal = isUndefined(core.livingObj['data'][setBind]) ? null : core.livingObj['data'][setBind];
                        if(lastVal) {
                            obj.value = lastVal;
                        }
                    }
                    if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][setBind])) {
                        renewObjBindAttr(obj, setBind);
                    }
                }
                if(setText && newText !=='') {
                    updateBindObj($.trim(setText), newText, exceptObj);
                }
            },
            //主动更新数据
            renew: function(options_) {
                var selectItem = options_['item']|| [{'value': 1}, {'value': 0}];
                var valueKey = !isUndefined(options_['value_key']) ? options_['value_key'] : 'value'; //没有下标则取value
                var textKey = !isUndefined(options_['text_key']) ? options_['text_key'] : 'text'; //没有下标则取value
                var type_ = !isUndefined(options_['type']) ? options_['type'] : ''; //1,2,3,4,5样式
                var disabled_ = getOptVal(options_, ['disabled', 'disable'], ''); //boolean
                var showText = getOptVal(options_, ['show_text', 'showText'], false); //显示文本
                var readonly = getOptVal(options_, ['readonly', 'readOnly'], false); //只读
                var hasSetData = !isUndefined(options_['data']);
                var size_ = options_['size']||''; //xs/sm/md/lg
                var objExtendClass = '';
                if(sizeIsXs(size_)) {
                    objExtendClass = 'switch-xs';
                } else if(sizeIsSm(size_)) {
                    objExtendClass = 'switch-sm';
                } else if(sizeIsMd(size_)) {
                    objExtendClass = 'switch-md';
                } else if(sizeIsLg(size_)) {
                    objExtendClass = 'switch-lg';
                }
                innerText1.attr('data-val', selectItem[0][valueKey]);
                innerText2.attr('data-val', selectItem[1][valueKey]);
                if(!isUndefined(selectItem[0][textKey])) {
                    if(showText!==false && showText!==0) {
                        innerText1.html(selectItem[0][textKey]);
                    }
                }
                if(!isUndefined(selectItem[1][textKey])) {
                    if(showText!==false && showText !==0) {
                        innerText2.html(selectItem[1][textKey]);
                    }
                }
                if(disabled_) {//纠正disable
                    if(options_['disable'] && !options_['disabled']) {
                        options_['disabled'] = disabled_;
                        delProperty(options_, ['disable']);
                    }
                }
                options_['class_extend'] = 'diy_switch'+ (type_ && type_!=1? type_: '') +
                    (disabled_==true ? ' isDisable' : '') +
                    (objExtendClass?' '+objExtendClass : '');
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                optionDataFrom(this, options_);
                var systemClick = function (obj_) {
                    if(readonly) return false;
                    var newVal = (obj_['switchVal'] == innerText1.attr('data-val')) ? innerText2.attr('data-val') : innerText1.attr('data-val');
                    obj.valChange(newVal); //单纯的改变样式 赋值
                };
                var userDiyClick = options_['click'];
                options_['click'] = function (e) {
                    systemClick(obj, e);
                    if(userDiyClick) {
                        userDiyClick(obj, e);
                    }
                };
                //先设定options参数 下面才可以修改options
                strObj.formatAttr(this, options_, 0, hasSetData);
            },
            updates: function(dataName, exceptObj) {//数据同步
                exceptObj = exceptObj || [];
                console.log('updates.switch');
                if(setBind && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(this);
                    this.valChange(getObjData($.trim(setBind)), exceptObj, false)
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function() {
                var opt = cloneData(obj.sor_opt);
                return global.makeSwitch(opt, true);
            }
        });
        obj.renew(options);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        optionGetSet(obj, options);
        objBindVal(obj, options, [{'key_':'bind', 'val_':'value'}, {'key_':'set_text/setText', 'val_':'text'}]);//数据绑定
        addCloneName(obj);//支持克隆
        obj.valChange(selectVal);//首次赋值
        return obj; //makeSwitch
    };
    return global;
});

