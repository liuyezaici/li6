define(['require'], function (require) {
    var global = {};
    var objBindAttrsName = 'bind_attrs';
    var objAttrHasKh = 'obj_opt_has_kuohao';//obj的属性包含有{} 则可能绑定全局变量
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入
    //单选框
    global.makeRadio = function(sourceOptions, sureSource) {
        var core = require('core');
        sourceOptions = sourceOptions || {};
        sureSource = sureSource || false;
        var obj = $('<div><div class="inner"></div></div>');
        sourceOptions['tag'] = 'radio';
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            obj.sor_opt = sureSource ?  core.cloneData(sourceOptions || {}) : core.cloneData(core.copySourceOpt(sourceOptions));
        }
        var options = core.cloneData(sourceOptions);
        var setBind = core.getOptVal(options, ['bind'], '');
        var sourceVal = core.getOptVal(options, ['value'], '');
        //统一头部判断结束
        var objInner = obj.find('.inner');
        obj[objValIsNode] = false;
        obj['createItem'] = false;
        obj['itemsObj'] = null;
        //select:单独的格式化value的括号 更新data时会触发
        obj.formatVal = function (opt) {
            opt = opt || [];
            var newData = core.getOptVal(opt, ['data'], {});
            var newVal = core._onFormatVal(obj, newData,  sourceVal);
            // console.log('formatVal:', newVal);
            if ($.isArray(newVal)) newVal = newVal.join(',');
            //console.log(obj);
            if(newVal !=='' && !core.strHasKuohao(newVal)) {
                obj.callRenewBind(newVal, [], false);
            }
        };
        //更像绑定的值
        obj.callRenewBind = function(newVal, exceptObj, notify) {
            // console.log('call RenewBind:', newVal, notify);
            notify = notify || false;
            exceptObj = exceptObj || [];
            if(newVal ==='' || core.isUndefined(newVal)) {
                newVal = obj.value;
            } else {
                obj.value = newVal;
            }
            if(notify) {
                var renewBind = obj[objAttrHasKh] == true;
                if(renewBind) {
                    if(obj[objBindAttrsName] && !core.objIsNull(obj[objBindAttrsName]) && !core.isUndefined(obj[objBindAttrsName][setBind])) {
                        core.renewObjBindAttr(obj, setBind);
                    }
                }
                // console.log('notify', notify);
                if (setBind) {
                    // console.log('exceptObj :', exceptObj);
                    if($.inArray(obj, exceptObj) == -1) {
                        exceptObj.push(obj);
                        // console.log('updateBindObj :', newVal);
                        core.updateBindObj(setBind, newVal, exceptObj);
                    }
                }
                var setText = core.getOptVal(options, ['set_text', 'setText'], null);
                if (setText) {
                    if($.inArray(obj, exceptObj) == -1) {
                        exceptObj.push(obj);
                        core.updateBindObj(setText, obj.text, [exceptObj]);
                    }
                }
            }

        };
        //支持外部设置 取值
        Object.defineProperty(obj, 'value', {
            set: function (newVal) {
                obj['itemsObj'].value = newVal;
            },
            get: function () {
                return obj['itemsObj'].value;
            }
        });
        //支持外部取选中的文本 返回数组格式
        Object.defineProperty(obj, 'text', {
            get: function () {
                var texts = obj['itemsObj'].text;
                return $.isArray(texts) ? texts.join(',') : texts;
            }
        });
        //支持外部取其中的某个元素单独显示
        obj.getItem = function(i) {
            obj.items['menu']['value'][i].siblings('li').hide();
            return obj;
        };
        obj.extend({
            //主动更新数据
            renew: function (optionsGet) {
                optionsGet = optionsGet || {};
                var hasSetData = !core.isUndefined(optionsGet['data']);
                var options_ = core.cloneData(optionsGet);//保留默认的配置 用于克隆
                // console.log('renew radio::::::::::::::');
                //console.log(this);
                //console.log(options_['data']);
                if (core.isUndefined(options_['value'])) options_['value'] = ''; //强制加value 否则外部无法取
                var sValueStr = core.getOptVal(options_, ['value'], []);
                var itemsOpt = core.getOptVal(options_, ['items'], {});
                var type_ = !core.isUndefined(options_['type']) ? options_['type'] : ''; //1,2,3,4,5样式
                var objExtendClass = '';//默认class
                var radioSize = options_['size'] || '';
                //console.log('size:'+ radioSize);
                if (core.sizeIsXs(radioSize)) {
                    objExtendClass = 'radios-xs';
                } else if (core.sizeIsSm(radioSize)) {
                    objExtendClass = 'radios-sm';
                } else if (core.sizeIsMd(radioSize)) {
                    objExtendClass = 'radios-md';
                } else if (core.sizeIsLg(radioSize)) {
                    objExtendClass = 'radios-lg';
                }
                if(type_ && type_!=1) objExtendClass += ' radioType'+ type_;
                //console.log('size:'+ objExtendClass);
                options_['class_extend'] = 'diy_radio '+ objExtendClass;
                core.optionDataFrom(obj, options_);
                //title_key 配置支持写在 opt/items里
                var itemsTitleKey = core.getOptVal(itemsOpt, ['title_key', 'titleKey', 'text_key', 'textKey'], null);
                var itemsValKey = core.getOptVal(itemsOpt, ['value_key', 'valueKey'], null);
                if(!itemsTitleKey) {
                    var optTitKey = core.getOptVal(options_, ['title_key', 'titleKey', 'text_key', 'textKey'], 'title');
                    if (optTitKey) {
                        itemsTitleKey = itemsOpt['title_key'] = optTitKey;
                    }
                }
                if(!itemsValKey) {
                    var optValKey = core.getOptVal(options_, ['value_key', 'valueKey'], 'value');
                    if (optValKey) {
                        itemsValKey = itemsOpt['value_key'] = optValKey;
                    }
                }
                var userDiyClick = core.getOptVal(options_, ['click'], null)
                itemsOpt['click'] = function (o_, e_) {
                    // console.log('click', obj.value);
                    obj.callRenewBind(obj.value, [], true);
                    if(userDiyClick) {
                        userDiyClick(o_, e_);
                    }
                };
                // console.log('itemsTitleKey:', itemsTitleKey);
                // console.log('menuOpt:', itemsOpt);
                itemsOpt['text'] = "<span class='_icon'></span><span class='text'>{"+ itemsTitleKey +"}</span>";
                itemsOpt['disabled'] = "{disabled}";
                var menuOpt =  {
                    'items': itemsOpt,
                    'value': ''
                };
                if(sValueStr !=='' && !core.strHasKuohao(sValueStr)) {
                    menuOpt['value'] = sValueStr;
                }
                //item自身不能继承data菜单
                var menu_obj = core.makeItems(menuOpt);
                menu_obj['parent'] = obj;//设置其父对象
                obj['itemsObj'] = menu_obj;
                obj['menu'] = menu_obj;//对外方便更新和获取菜单
                obj['items'] = menu_obj;//对外方便更新和获取菜单
                objInner.append(menu_obj);
                //console.log('options_');
                // console.log('formatAttr radio');
                //添加数据
                core.strObj.formatAttr(obj, options_, 0, hasSetData);//无需再设置value //给input分配的事件 如 blur
            },
            updates: function (dataName, exceptObj) {//数据同步
                exceptObj = exceptObj || [];
                var newVal = core.getObjData($.trim(setBind));
                // console.log('updates:', newVal, obj[objBindAttrsName]);
                if( $.inArray(obj, exceptObj) == -1 && core.strInArray(newVal, obj['menu']['disableVals']) == -1) {
                    exceptObj.push(obj);
                    if(setBind) {
                        obj.callRenewBind(newVal, exceptObj, false);
                    }
                    if (obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                        core.renewObjBindAttr(obj, dataName);
                    }
                }
            },
            cloneSelf: function() {
                var opt = core.cloneData(obj.sor_opt);
                return core.makeRadio(opt, true);
            }
        });
        core.objBindVal(obj, options, [{'key_':'bind', 'val_':'value'}, {'key_':'setText/set_text', 'val_': 'text'}]);//数据绑定
        obj.renew(options);
        core.optionGetSet(obj, options); // format AttrVals 先获取options遍历更新 再设置读写
        core.addCloneName(obj);//支持克隆
        return obj;
    };
    return global;
});

