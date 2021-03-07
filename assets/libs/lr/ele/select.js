define(['require'], function (require) {
    var global = {};
    var menuZindexClass = 'menu_add_zindex';
    var objBindAttrsName = 'bind_attrs';
    var objAttrHasKh = 'obj_opt_has_kuohao';//obj的属性包含有{} 则可能绑定全局变量
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入

//生成 下拉菜单
    global.makeSelect = function(sourceOptions, sureSource) {
        var core = require('core');
        sourceOptions = sourceOptions || {};
        sureSource = sureSource || false;
        var obj = $('<div></div>');
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            obj.sor_opt = sureSource ?  core.cloneData(sourceOptions) : core.cloneData(core.copySourceOpt(sourceOptions));
        }
        var options = core.cloneData(sourceOptions);
        var setBind = core.getOptVal(options, ['bind'], '');
        var setText = core.getOptVal(options, ['set_text', 'setText'], null);
        var menuOpenEven = core.getOptVal(options, ['onMenuOpen','menuOpen', 'menu_pen'], null);//菜单展开时触发的动作
        var menuCloseEven = core.getOptVal(options, ['onMenuClose','menuClose', 'menu_close'], null);//菜单关闭时触发的动作
        var sourceVal = core.getOptVal(options, ['value'], '');
        //统一头部判断结束
        //div + contenteditable="true" 可输入 tabindex 用于触发丢焦
        var objExtendClass = 'btnGLr';
        var inputSize = options['size'] || '';
        if(core.sizeIsXs(inputSize)) {
            objExtendClass = 'btnGLr btnGLrXs';
        } else if(core.sizeIsSm(inputSize)) {
            objExtendClass = 'btnGLr btnGLrSm';
        } else if(core.sizeIsMd(inputSize)) {
            objExtendClass = 'btnGLr btnGLrMd';
        } else if(core.sizeIsBg(inputSize)) {
            objExtendClass = 'btnGLr btnLrBg';
        }  else if(core.sizeIsLg(inputSize)) {
            objExtendClass = 'btnGLr btnGLrLg';
        }
        var selectDefaultText = core.getOptVal(options, ['default_text', 'defaultText'], '请选择');
        if(!core.isUndefined(options['defaultText']) && !core.isUndefined(options['default_text']))  {
            options['default_text'] = selectDefaultText;
            core.delProperty(options, ['defaultText']);
        }
        obj.append($('<div class="inner"> \
        <div class="title_wrap '+ objExtendClass +' ">\
            <button class="select_text btnLr btnLrDefault" tabindex="1">'+ selectDefaultText +'</button>\
            <span class="btnLr btnLrDefault" type="button"><span class="caret"></span></> \
        </div> \
     </div>'));
        obj.textObj = obj.find('.select_text');

        obj['multi'] = false;
        obj[objValIsNode] = false;
        obj['createMenu'] = false;
        obj['menu'] = false;
        obj['clear_btn'] = null;
        obj['selectValArray'] = [];
        obj['selectTxtArray'] = [];
        var selectMosHvr = false;
        var objInner = obj.find('.inner');
        objInner.click(function (even_, obj_) {
            even_.stopPropagation();
            var clickTag = $(even_.target);
            if(clickTag.hasClass('lrXX')) return; //clear
            obj['menu'].show();
            obj.addClass(menuZindexClass);
            obj.textObj.focus();
            if(menuOpenEven) {
                menuOpenEven(even_, obj);
            }
        });
        objInner.on({
            'mouseenter': function () {
                selectMosHvr = true;
            },
            'mouseleave': function () {
                selectMosHvr = false;
            },
        });
        obj.textObj.on({
            'blur': function() {
                if(!selectMosHvr) {
                    setTimeout(function () {
                        obj['menu'].hide();
                        if(menuCloseEven) {
                            menuCloseEven(even_, obj);
                        }
                    }, 100);
                } else {
                    //每次选择 要给按钮对焦  这样鼠标点击外部就可以触发关闭下拉层
                    obj.textObj.focus();
                }
            }
        });

        var sonSelectKey = 'son'; //子下拉菜单的键名 外部调取就用son 不能改的
        obj.INeedParentValFlag = false;  //当前select对象需要父的value去取menu的data
        var autoRenewSelectMenu = false; //当前select的menu和val是否同时设置完成
        var clearBtn = core.getOptVal(options, ['clear'], false);
        obj.hasRenewSonObj = false;//定义是否已更新子菜单
        //检测子对象是否跟随父value改变
        function __checkIfNeedParent() {
            if(!core.isUndefined(obj[sonSelectKey])) {
                var sonItem = obj[sonSelectKey];
                var itemUlList = sonItem.menu.menu || null;
                if(itemUlList && itemUlList.INeedParentValFlag) {
                    return true;
                }
            }
            return false;
        }
        //检测是否更新子对象的value
        function __checkIfRenewSonObj(val, ifClearVal) {
            ifClearVal = ifClearVal || false;
            if($.isArray(val)) val = val.join(',');
            if(__checkIfNeedParent()) {
                //console.log('need');
                var sonSelect = obj[sonSelectKey];
                var itemUlList = sonSelect.menu.menu || null;
                if(ifClearVal) {
                    sonSelect.value = '';
                }
                if(itemUlList['getDataWithParentVal']) {
                    itemUlList['getDataWithParentVal'](val);
                } else if(itemUlList['getDataFromParentData']) {
                    itemUlList['getDataFromParentData'](obj, val, sonSelect);
                }
            } else {
                //console.log('no need');
            }
        }
        //更新显示文本
        obj.renewText = function(newTextArray) {
            if(!newTextArray || core.isUndefined(newTextArray)) newTextArray = obj['menu'].text;
            var newTextStr;
            if(!newTextArray) {
                newTextArray = [selectDefaultText];
                newTextStr = selectDefaultText;
            } else {
                if($.isArray(newTextArray) && newTextArray.length==0) {
                    newTextArray = [selectDefaultText];
                }
                newTextStr = $.isArray(newTextArray) ? newTextArray.join(',') : newTextArray;
            }
            obj.textObj.setSelectMenuText(newTextArray, newTextStr);
            if(setText) {//触发数据同步  触发赋值 */
                core.updateBindObj($.trim(setText), newTextStr, [obj]);
                if(obj[objBindAttrsName] && !core.objIsNull(obj[objBindAttrsName]) && !core.isUndefined(obj[objBindAttrsName][setText])) {
                    core.renewObjBindAttr(obj, setText);
                }
            }
            //清除内容的按钮
            if(clearBtn) {
                if(!obj['clear_btn']) {
                    obj['clear_btn'] = core.makeSpan({
                        'class' : 'lrXX'+ (newTextStr=='' ? ' hidden': ''), //有内容时要 +hidden 隐藏
                        'click': function (btn_) {
                            btn_.addClass('hidden');
                            obj.value='';
                        }
                    });
                    objInner.addClass('has_clear').append(obj['clear_btn']);
                } else {
                    if(newTextStr.length>0) {
                        obj['clear_btn'].removeClass('hidden');
                    } else {
                        obj['clear_btn'].addClass('hidden');
                    }
                }
            }
        };
        //select:单独的格式化value的括号 更新data时会触发
        obj.formatVal = function (opt) {
            opt = opt || [];
            //每次格式化 优先取格式化前的source value
            var newData = core.getOptVal(opt, ['data'], {});
            var newVal = core._onFormatVal(obj, newData,  sourceVal);
            if(obj.menu.menuXuanranSuccess === true && !obj.hasRenewSonObj) {
                if(obj[sonSelectKey]) {
                    obj.hasRenewSonObj = true;
                    //console.log('format_Val __ call __checkIf RenewSonObj');
                    __checkIfRenewSonObj(newVal);//检测是否需要触发子对象刷新data
                }
            }
            //console.log('newVal:', newVal);
            //如果值是数组 并且多个值 并且未定义是否多选，则默认支持多选
            if($.isArray(newVal) && newVal.length>0 && obj['multi'] == undefined) {
                obj['multi'] = true;
            }
            obj.setSelectVal(newVal, [obj]);
            if(obj[objAttrHasKh]==true && obj.menu.menuXuanranSuccess && obj.menu.valueSeted) {
                obj.renewText();
                autoRenewSelectMenu = true;
            }
            if(obj.lazyCall) {
                obj.lazyCall(obj, newData, core.livingObj);
            }
        };

        //更新选中的值和文本
        obj.setSelectVal = function(newVal, exceptObj, renewBind) {
            exceptObj = exceptObj || [];
            renewBind = core.isUndefined(renewBind) ? true : renewBind;
            if(obj['menu'].value != newVal) obj['menu'].value = newVal;
            // console.log('setSelectVal', newVal, renewBind);
            if(renewBind) {
                if(setBind) {
                    //触发数据同步  触发赋值 */
                    if($.inArray(obj, exceptObj) == -1) exceptObj.push(obj);
                    if(newVal !== '') {
                        core.updateBindObj($.trim(setBind), newVal, exceptObj);
                    } else {
                        var lastVal = core.isUndefined(livingObj['data'][setBind]) ? null : core.livingObj['data'][setBind];
                        if(lastVal !== '') {
                            obj.setSelectVal(lastVal, [obj]);
                        }
                    }
                    if(obj[objBindAttrsName] && !core.objIsNull(obj[objBindAttrsName]) && !core.isUndefined(obj[objBindAttrsName][setBind])) {
                        core.renewObjBindAttr(obj, setBind);
                    }
                }
                if(setText) {
                    //触发数据同步  触发赋值 */
                    if($.inArray(obj, exceptObj) == -1) exceptObj.push(obj);
                    if(newVal !== '') {
                        core.updateBindObj($.trim(setText), obj.text, exceptObj);
                    }
                    if(obj[objBindAttrsName] && !core.objIsNull(obj[objBindAttrsName]) && !core.isUndefined(obj[objBindAttrsName][setText])) {
                        core.renewObjBindAttr(obj, setText);
                    }
                }
            }
        };
        //支持外部设置 取值
        Object.defineProperty(obj, 'value', {
            set: function (newVal) {
                obj['menu'].value = newVal;
                obj.renewText();
            },
            get: function () {
                return obj['menu'].value;
            }
        });
        //支持外部设置菜单的 data
        Object.defineProperty(obj, 'menuData', {
            set: function (newVal) {
                obj['menu']['menu']['data'] = newVal;
                obj['menu'].getItemTextByVal();
                obj.renewText();
            }
        });
        //支持外部取选中的文本 返回数组格式
        Object.defineProperty(obj, 'text', {
            get: function () {
                var texts = obj['menu'].text;
                return $.isArray(texts) ? texts.join(',') : texts;
            }
        });
        //div文本赋值 为兼容 方法 format.Content 要和input的方法同名
        obj.textObj.setSelectMenuText = function(newVal, newValStr) {
            //console.log('newVal', newVal);
            this.html(($.isArray(newVal) && newVal.length>1 ? '已选'+ newVal.length +'个' : newVal)).attr('data-old', newValStr);
        };

        obj.extend({
            //主动更新数据
            renew: function(options_) {
                options_ = options_ || {};
                var hasSetData = !core.isUndefined(options_['data']);
                if(core.isUndefined(options_['value'])) options_['value'] = ''; //强制加value 否则外部无法取
                var sValueStr = core.getOptVal(options_, ['value'], []) ;
                var itemValueArray = sValueStr;
                obj.INeedParentValFlag = core.getOptNeedParentKey(options_);//需要父参数渲染好才能请求url
                var itemsMenuOpt = core.getOptVal(options_, ['li'], {});
                var pageObj = core.getOptVal(options_, ['pageObj'], null);
                var optData = options_['data'] || {};
                var lazyCall = core.getOptVal(options_, ['lazy_call', 'lazyCall'], null);
                obj['multi'] = core.getOptVal(options_, ['mul', 'multi', 'multity'], undefined); //是否支持多选
                //如果值是数组 并且多个值 并且未定义是否多选，则默认多选
                if($.isArray(sValueStr) && sValueStr.length>0 && obj['multi'] == undefined) {
                    obj['multi'] = true;
                }
                var sourceVal = options_['value']||'';
                //初始化 确认是否val需要渲染
                if(core.strHasKuohao(sourceVal, 'public')) {
                    obj[objAttrHasKh] = true;
                } else if(core.strHasKuohao(sourceVal, 'data')) {
                    obj[objAttrHasKh] = true;
                } else {
                    obj[objAttrHasKh] = false;
                }

                //多级子菜单
                var selectSonOpt = options_['son'] || {};
                //生成子对象
                var sonObj = null;
                if(core.hasData(selectSonOpt)) {
                    // 缺省则沿用父属性
                    var sonExtendOptNames = 'default_text/value_key/title_key/text_key/li/url/post_name/data_key/success_key/successValue/success_value/success_func'.split('/');
                    selectSonOpt['INeedParentValFlag'] = core.getOptNeedParentKey(selectSonOpt);
                    sonExtendOptNames.forEach(function(opt_) {
                        if(core.isUndefined(selectSonOpt[opt_]) && !core.isUndefined(options_[opt_])) {
                            selectSonOpt[opt_] = options_[opt_];
                        }
                    });
                    sonObj = core.makeSelect(selectSonOpt);
                    sonObj['parent'] = obj;
                    obj[sonSelectKey] = sonObj;
                }
                //只生成一次下拉菜单
                if(!obj['createMenu']) {
                    //select自身可以用data和data_from 但是前提是menu参数里必须要设定data或data_from
                    var parentDataFrom = core.getOptVal(options_, ['data_from', "dataFrom"], null);
                    var menuOpt = core.getOptVal(options_, ['menu'], {});
                    var menuDataFrom = core.getOptVal(menuOpt, ['data_from', "dataFrom"], null);
                    var menuSetData = core.getOptVal(menuOpt, ['data'], null);
                    //继承当前select属性 是否需要父value来更新
                    if(!menuDataFrom && !menuSetData) {
                        //console.log('son___no_data');
                        //console.log(obj);
                        if(parentDataFrom) {
                            itemsMenuOpt['data_from'] = core.cloneData(parentDataFrom);
                            core.delProperty(options_, ['data_from']);
                            //console.log('del data_from');
                        } else if(optData) {
                            itemsMenuOpt['data'] = core.cloneData(optData);
                            core.delProperty(options_['data']);
                            //console.log('del data');
                        }
                    } else {
                        //console.log('menuDataFrom');
                        //console.log(obj);
                        //console.log(menuDataFrom);
                        if(menuDataFrom) {
                            itemsMenuOpt['data_from'] = menuDataFrom;
                        } else {
                            itemsMenuOpt['data'] = core.cloneData(menuSetData);
                        }
                        //console.log(obj);
                        core.delProperty(menuOpt, ['data']); //item对象不需要渲染data
                        //渲染select自己的data 和son菜单无关
                        core.optionDataFrom(obj, options_);
                    }
                    if(!core.isUndefined(itemsMenuOpt['value']) && core.isUndefined(itemsMenuOpt['text'])) {
                        itemsMenuOpt['text'] = itemsMenuOpt['value'];
                        core.delProperty(itemsMenuOpt, ['value']);
                    }
                    //旧版会把这两个配置写在opt里 也支持读取覆盖
                    var optValKey = core.getOptVal(options_, ['value_key', 'valueKey'], 'value');
                    var optTitKey = core.getOptVal(options_, ['title_key', 'titleKey', 'text_key', 'textKey'], null);
                    if(!optTitKey && !itemsMenuOpt['title_key']) {
                        console.log('select未定义title_key ');
                        return;
                    }
                    if(optValKey) itemsMenuOpt['value_key'] = optValKey;
                    if(optTitKey) itemsMenuOpt['title_key'] = optTitKey;
                    var userDiyClick = core.getOptVal(itemsMenuOpt, ['click'], '');
                    itemsMenuOpt['click'] = function (li, dd, ev_, scope_) {
                        ev_.stopPropagation();//不能触发inner的下拉事件
                        obj.renewText();
                        obj.setSelectVal(obj.value, [obj]);
                        __checkIfRenewSonObj(obj.value, 1); //检测是否需要触发子对象刷新data
                        if(userDiyClick) userDiyClick(li, dd, scope_);
                        if(!obj['multi']) {
                            obj['menu'].hide();
                        }
                    };
                    menuOpt['lazy_call'] = function(item_) {
                        setTimeout(function () {
                            if (obj.menu.valueSeted == true) {
                                if(obj[objAttrHasKh]==true && !autoRenewSelectMenu || !obj[objAttrHasKh]) {
                                    //让菜单自己更新文本
                                    var textArray = item_.getItemTextByVal();
                                    //console.log('lazy_call');
                                    obj.renewText(textArray);
                                    //如果有子菜单 并且子菜单需要渲染数据 更新子菜单data
                                    if(obj.value && !obj.hasRenewSonObj) {//检测是否需要触发子对象刷新data
                                        obj.hasRenewSonObj = true;
                                        __checkIfRenewSonObj(obj.value);
                                    }
                                }
                            }
                            //延迟执行父绑定的延迟事件
                            if (lazyCall) {
                                lazyCall(obj, itemValueArray, core.livingObj);
                            }
                        }, 50);
                    };
                    //刷新data的事件交由list去做 先把属性给Items
                    itemsMenuOpt['needParentKey'] = obj.INeedParentValFlag;
                    menuOpt = $.extend({}, menuOpt, {
                        'items': itemsMenuOpt,
                        'multi': obj['multi'],
                        'value': itemValueArray
                    });

                    //item自身不能继承data菜单
                    core.delProperty(menuOpt, ['data_from', 'dataFrom']);
                    var menu_obj = core.makeItems(menuOpt);
                    menu_obj['parent'] = obj;//设置其父对象
                    obj['menu'] = menu_obj;//对外方便更新和获取菜单
                    objInner.append(menu_obj);
                    if(pageObj) {
                        obj['menu'].append(pageObj);
                    }
                    obj['createMenu'] = true;
                }
                var newInputEven = {};
                options_['class_extend'] = 'select_box';
                core.delProperty(newInputEven, ['value']);
                //强制加value参数 否则无法触发初始化渲染value事件：format Val
                var formatOpt = core.cloneData(options_);
                if(core.isUndefined(formatOpt['value'])) formatOpt['value'] = '';
                core.strObj.formatAttr(obj, formatOpt, 0, hasSetData);
            },
            updates: function(dataName, exceptObj) {//bind数据同步
                //console.log('updates:'+dataName);
                exceptObj = exceptObj || [];
                if(setBind && $.inArray(this, exceptObj) == -1) {
                    this.value = (core.getObjData($.trim(setBind)));
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    core.renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function() {
                var opt = core.cloneData(obj.sor_opt);
                return core.makeSelect(opt, true);
            }
        });
        core.objBindVal(obj, options, [{'key_':'bind', 'val_':'value'}, {'key_':'set_text/setText', 'val_':'text'}]);//数据绑定
        obj.renew(options);
        core.optionGetSet(obj, options);
        core.addCloneName(obj);//支持克隆
        //console.log('select_obj');
        //console.log(obj);
        return obj; //makeSelect
    };
    return global;
});

