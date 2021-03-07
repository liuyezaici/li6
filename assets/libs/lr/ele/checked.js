define(['require'], function (require) {
    var global = {};
    var objBindAttrsName = 'bind_attrs';
    var objAttrHasKh = 'obj_opt_has_kuohao';//obj的属性包含有{} 则可能绑定全局变量
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入
    //创建check
    var onlyCheckeds = {};
    global.makeCheck = global.makeChecked = global.makeCheckbox = function(sourceOptions, sureSource) {
        var core = require('core');
        sourceOptions = sourceOptions || {};
        sourceOptions['tag'] = 'checked';
        sureSource = sureSource || false;
        var obj = $('<i></i>');
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            obj.sor_opt = sureSource ?  core.cloneData(sourceOptions) : core.cloneData(core.copySourceOpt(sourceOptions));
        }
        var options = core.cloneData(sourceOptions);
        var setBind = core.getOptVal(options, ['bind'], '');
        var sourceVal = core.getOptVal(options, ['value'], '');
        //统一头部判断结束
        options['tag'] = 'checked';
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            obj.sor_opt = sureSource ?  core.cloneData(sourceOptions || {}) : core.cloneData(core.copySourceOpt(defaultOps));
        }
        if(core.isUndefined(options['name'])) {
            var newname = core.createRadomName('check');
            options['name'] = newname;
        }
        obj[objValIsNode] = false;
        obj['createCheck'] = false;
        var onlyName = core.getOptVal(options, ['only', 'single', 'one'], null);
        if(onlyName) {
            if(core.isUndefined(onlyCheckeds[onlyName])) {
                onlyCheckeds[onlyName] = [];
            }
            onlyCheckeds[onlyName].push(obj);
        }
        //select:单独的格式化value的括号 更新data时会触发
        obj.formatVal = function (opt) {
            // console.log('format Val', obj);
            opt = opt || [];
            var newData = core.getOptVal(opt, ['data'], {});
            var newVal = core._onFormatVal(obj, newData,  sourceVal);
            //每次格式化 优先取格式化前的source value
            if ($.isArray(newVal)) newVal = newVal.join(',');
            var renewBind = core.strHasKuohao(newVal, 'public');
            obj.callRenewBind(newVal, [obj], renewBind);
            if(opt.lazyCall) {
                // console.log('format Val.lazyCall');
                opt.lazyCall(obj, newData, core.livingObj);
            }
        };

        //更像绑定的值
        obj.callRenewBind = function(newVal, exceptObj, renewBind) {
            exceptObj = exceptObj || [];
            renewBind = core.isUndefined(renewBind) ? true : renewBind;
            if(core.isUndefined(newVal)) {
                newVal = obj.checked_value;
            } else {
                obj.checked_value = newVal;
            }
            if (setBind && renewBind) {
                if($.inArray(obj, exceptObj) == -1) {
                    exceptObj.push(obj);
                    core.updateBindObj(setBind, newVal, exceptObj);
                }
                if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !core.isUndefined(obj[objBindAttrsName][setBind])) {
                    core.renewObjBindAttr(obj, setBind);
                }
            }
            var setText = core.getOptVal(options, ['set_text', 'setText'], null);
            if (setText) {
                core.updateBindObj(setText, obj.text, [obj]);
                if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !core.isUndefined(obj[objBindAttrsName][setText])) {
                    core.renewObjBindAttr(obj, setText);
                }
            }
        };
        //检测是否选中
        var hasChecked = function() {
            var checked = obj.attr('checked');
            if(!core.isUndefined(checked)) {
                if(checked==0 || checked=='0' || checked=='false' || !checked) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        };
        //支持外部设置 取值
        Object.defineProperty(obj, 'value', {
            set: function (newVal) {
                obj.checked_value = newVal;
            },
            get: function () {
                if(hasChecked()) return obj.checked_value;
                return '';
            }
        });
        //支持外部取选中的文本 返回数组格式
        Object.defineProperty(obj, 'text', {
            set: function (newVal) {
                obj.checked_title = newVal;
            },
            get: function () {
                if(hasChecked()) return obj.checked_title;
                return '';
            }
        });
        //支持外部设置 取值
        Object.defineProperty(obj, 'checked', {
            set: function (newVal) {
                if(newVal==0 || newVal == 'false' || !newVal) {
                    obj.removeAttr('checked');
                } else {
                    obj.attr('checked', newVal);
                }
            },
            get: function () {
                return hasChecked();
            }
        });
        obj.extend({
            //主动更新数据
            renew: function(options_) {
                options_ = options_ || {};
                var hasSetData = !core.isUndefined(options_['data']);
                var size_ = options_['size']||''; //xs/sm/md/lg
                var objExtendClass = '';
                if(core.sizeIsXs(size_)) {
                    objExtendClass = 'checked-xs';
                } else if(core.sizeIsSm(size_)) {
                    objExtendClass = 'checked-sm';
                } else if(core.sizeIsMd(size_)) {
                    objExtendClass = 'checked-md';
                } else if(core.sizeIsLg(size_)) {
                    objExtendClass = 'checked-lg';
                }
                var type_ = !core.isUndefined(options_['type']) ? options_['type'] : ''; //1,2,3,4,5样式
                options_['class_extend'] = 'diy_checked' + (type_ && type_!=1? ' checkStyle'+type_: '')+ (objExtendClass? ' '+ objExtendClass :'');

                var disabled = core.getOptVal(options_, ['disable','disabled'], '');
                if(!core.isUndefined(options_['disable']) && core.isUndefined(options_['disabled'])) {
                    options_['disabled'] = disabled;
                    delProperty(options_, ['disable']);
                }
                //重置value和title/text
                options_['checked_value'] = core.getOptVal(options_, ['checked', 'value'], 1);
                options_['checked_title'] = core.getOptVal(options_, ['text'], '');
                var dataTitle = core.getOptVal(options_, ['text'], '');
                if(disabled == 1) options_['disabled'] = true;
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                core.optionDataFrom(obj, options_);//
                //只生成一次子对象
                if(!obj['createCheck']) {
                    var sonObj = $('<span class="_inner">' +
                        '<span class="_icon"></span>' +
                        '<span class="_title">'+ dataTitle +'</span>' +
                        '</span>');
                    obj.append(sonObj);
                    obj['createCheck'] = true;
                    var userDiyClick = core.getOptVal(options_, ['click'], null)
                    var defaultClickFunc = function(obj_, e) {
                        if(obj_.attr('disabled')) return;
                        var lastChecked = hasChecked();
                        if(lastChecked) {
                            obj_.removeAttr('checked');
                        } else {
                            obj_.attr('checked', 'true');
                        }
                        var newVal = !lastChecked;
                        newVal = newVal ? obj.checked_value : 0;
                        obj_['options']['checked'] = newVal;
                        if(options_['bind']) {
                            core.updateBindObj($.trim(options_['bind']), newVal, [obj_]);
                            if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !core.isUndefined(obj[objBindAttrsName][options_['bind']])) {
                                core.renewObjBindAttr(obj, options_['bind']);
                            }
                        }
                        var setText = core.getOptVal(options, ['set_text', 'setText'], null);
                        if (setText) {
                            core.updateBindObj(setText, obj.text, [obj]);
                            if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !core.isUndefined(obj[objBindAttrsName][setText])) {
                                core.renewObjBindAttr(obj, setText);
                            }
                        }
                        //之前未选中，现在选中，判断是否设置单选
                        if(!lastChecked && onlyName && onlyCheckeds[onlyName]) {
                            $.each(onlyCheckeds[onlyName], function (index,o_) {
                                if(o_!==obj) {
                                    o_.checked = false;
                                }
                            });
                        }
                    };
                    options_['click'] = function (obj_, e) {
                        defaultClickFunc(obj_, e);
                        if(userDiyClick) {
                            userDiyClick(obj_, e);
                        }
                    };
                }
                //text渲染后更新显示的文本
                if(core.strHasKuohao(options_['text'])) {
                    //用户可以定义初始格式化text事件
                    var diyOnFormatText = options_['onFormat_text'] || null;
                    options_['onFormat_text'] = function (o, v, data) {
                        sonObj.find('._title').html(v);
                        if(diyOnFormatText) {
                            diyOnFormatText(o, v, data);
                        }
                    };
                }
                core.strObj.formatAttr(obj, options_, 0, hasSetData);
            },
            updates: function(dataName, exceptObj) {//数据同步
                //console.log('updates');
                //console.log(dataName);
                //console.log(obj[objBindAttrsName]);
                exceptObj = exceptObj || [];
                if(setBind && $.inArray(this, exceptObj) == -1) {
                    var checked = (getObjData($.trim(setBind)));
                    if(!checked || checked==0 || checked=='false') {
                        this.removeAttr('checked');
                    } else {
                        this.attr('checked', 1);
                    }
                }
                var setText = core.getOptVal(options, ['set_text', 'setText'], null);
                if(setText) {
                    this.attr('data_text', getObjData($.trim(setText)));
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    core.renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function() {
                var opt = core.cloneData(obj.sor_opt);
                return global.makeChecked(opt, true);
            }
        });
        core.objBindVal(obj, options, [{'key_':'bind', 'val_':'value'}, {'key_':'set_text/setText', 'val_':'text'}]);//数据绑定
        obj.renew(options);
        core.optionGetSet(obj, options); // format AttrVals 先获取options遍历更新 再设置读写
        core.addCloneName(obj);//支持克隆
        return obj;
    };
    return global;
});

