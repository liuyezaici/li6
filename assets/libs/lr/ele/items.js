define(['require'], function (require) {
    var global = {};
    var objValObjKey = 'obj_val_objs';//当前对象包含的obj  每个人对象创建成功后，其val都会保存当前值或dom对象 字符串形式的value除非
    var objBindAttrsName = 'bind_attrs';
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入
    //创建items 自定义单元
    global.makeItems = function(sourceOptions, sureSource) {
        var core = require('core');
        sourceOptions = sourceOptions || {};
        sureSource = sureSource || false;
        var obj = $('<dd></dd>');
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            obj.sor_opt = sureSource ?  core.cloneData(sourceOptions) : core.cloneData(core.copySourceOpt(sourceOptions));
        }
        var options = core.cloneData(sourceOptions);
        var setBind = core.getOptVal(options, ['bind'], '');
        var sourceVal = core.getOptVal(options, ['value'], '');
        var realVal = [];
        //统一头部判断结束
        obj['tag'] = 'items';
        obj[objValIsNode] = false;
        obj['createItem'] = false;
        obj['multi'] = undefined;
        obj['noNeedEven'] = true;//不需要绑定事件，因为所有的鼠标事件都是items的子单元实现的，让它们继承这些事件的参数即可
        var valueSetted = false;//当前对象的value是否格式化完成
        var menuListSuccess = false;//当前对象的menu是否渲染完成 [menu需要渲染 才会用到]

        //单独的格式化value的括号
        obj.formatVal = function (opt) {
            opt = opt || [];
            var newData = core.getOptVal(opt, ['data'], {});
            realVal = core._onFormatVal(obj, newData,  sourceVal);
            console.log('format -- ObjVal', realVal);
            valueSetted = true;
            if(menuListSuccess) {
                console.log('menuListSuccess');
                if(realVal !== '') {
                    obj.setItemVal(realVal, [obj], true);
                }
            }
            //console.log('format_val');
            //如果值是数组 并且多个值 并且未定义是否多选，则默认支持多选
            if($.isArray(realVal) && realVal.length>0 && obj['multi'] == undefined) {
                obj['multi'] = true;
            }
            if(obj.lazyCall) {
                obj.lazyCall(obj, newData);
            }

        };

        //支持外部设置 取值
        Object.defineProperty(obj, 'value', {
            set: function (newVal) {
                // console.log('set newVal:' ,newVal);
                valueSetted = true;
                obj.setItemVal(newVal, [obj], true);
                renewMenuTextByVal();
            },
            get: function () {
                return obj['multi'] ? realVal : ($.isArray(realVal) ? realVal.join('') : realVal) ;
            }
        });
        //支持外部取选中的文本 返回数组格式
        Object.defineProperty(obj, 'text', {
            get: function () {
                //多选时，才返回数组
                return obj['multi'] ? obj.itemTxtArray : ($.isArray(obj.itemTxtArray) ? obj.itemTxtArray.join('') : obj.itemTxtArray) ;
            }
        });
        //获取当前选中的文本
        obj.reGetValAndText = function () {
            var valArray_ = [];
            var textArray_ = [];
            var ulLis = obj['menu'].value;
            var liVal, liTitle;
            $.each(ulLis, function(n, tmpItem) {
                liVal = tmpItem.attr('data-value');
                liTitle = tmpItem.attr('data-title');
                if(tmpItem.hasClass('active')) {
                    valArray_.push(liVal);
                    textArray_.push(liTitle);
                }
            });
            realVal = valArray_;
            obj.itemTxtArray = textArray_;
        };
        //公共的初始化触发渲染菜单和值的方法
        //当菜单渲染完成，并且item的值渲染完成 才能获取菜单的文本
        function renewMenuTextByVal() {
            if(menuListSuccess && valueSetted) {
                var textArray = obj.getItemTextByVal();//获取当前li选中的内容
                var setText = core.getOptVal(options, ['set_text', 'setText'], null);
                //通知更新text
                if(setText) {
                    var selectText = $.isArray(textArray) ? textArray.join(',') : textArray;
                    core.updateBindObj($.trim(setText), selectText, [obj]);
                    if(obj[objBindAttrsName] && !core.objIsNull(obj[objBindAttrsName]) && !core.isUndefined(obj[objBindAttrsName][setText])) {
                        core.renewObjBindAttr(obj, setText);
                    }
                }
            }
        }
        //通过当前val取text
        obj.getItemTextByVal = function () {
            var newValAy = realVal;
            if(!newValAy || !core.hasData(newValAy)) {
                return '';
            }
            var textStr = '';
            newValAy = newValAy || [];
            if(!$.isArray(newValAy)) newValAy = newValAy.toString().split(',');
            //设置(更新)select的text
            var valArray_ = [];
            var textArray_ = [];
            var ulLis = obj['menu']['value'];
            // console.log('ulLis', ulLis);
            $.each(ulLis, function(n, tmpItem) {
                // console.log('tmpItem', n, tmpItem);
                var liVal = tmpItem.attr('data-value');
                var liTitle = tmpItem.attr('data-title');
                //console.log('liVal:'+ liVal);
                //console.log(newValAy);
                if(newValAy) {
                    if(core.strInArray(liVal, newValAy) !=-1) {
                        tmpItem.addClass('active');
                        valArray_.push(liVal);
                        textArray_.push(liTitle);
                    } else {
                        tmpItem.removeClass('active');
                    }
                } else {
                    tmpItem.removeClass('active');
                }
            });
            obj.itemTxtArray = textArray_;
            realVal = valArray_;
            return textArray_;
        };
        //更新选中的值和文本
        obj.setItemVal = function(newVal, exceptObj, renewBind) {
            exceptObj = exceptObj || [];
            renewBind = core.isUndefined(renewBind) ? true : renewBind;
            var newValArray;
            if(!$.isArray(newVal)) {
                if(typeof newVal != 'string') newVal = newVal+'';//int转字符串
                newValArray = newVal.split(',');
            } else {
                newValArray = newVal;
            }
            var valStr = newVal;
            if($.isArray(valStr)) {
                valStr = valStr.join(',');
            }
            // console.log('setItemVal');
            // console.log(newValArray);
            if(valStr !== obj.attr('data-value')) {
                obj.attr('data-value', valStr);
            }
            realVal = newValArray;
            var ulLis = obj['menu']['value'];
            console.log('ulLis', ulLis.length);
            $.each(ulLis, function(n, tmpItem) {
                var liVal = tmpItem.attr('data-value');
                if(newValArray) {
                    if(core.strInArray(liVal, newValArray) !=-1) {
                        tmpItem.addClass('active');
                    } else {
                        tmpItem.removeClass('active');
                    }
                } else {
                    tmpItem.removeClass('active');
                }
            });
            if(renewBind && setBind) {
                //触发数据同步  触发赋值 */
                if($.inArray(obj, exceptObj) == -1) exceptObj.push(obj);
                if(valStr.length) {
                    core.updateBindObj($.trim(setBind), valStr, exceptObj);
                } else {
                    var lastVal = core.isUndefined(core.livingObj['data'][setBind]) ? null : core.livingObj['data'][setBind];
                    if(lastVal) {
                        obj.value = lastVal;
                    }
                }
                if(obj[objBindAttrsName] && !core.objIsNull(obj[objBindAttrsName]) && !core.isUndefined(obj[objBindAttrsName][setBind])) {
                    core.renewObjBindAttr(obj, setBind);
                }
            }
        };
        obj.extend({
            //主动更新数据
            renew: function(options_) {
                options_ = options_ || {};
                var hasSetData = !core.isUndefined(options_['data']);
                if(core.isUndefined(options_['value'])) options_['value'] = ''; //强制加value 否则外部无法取
                if(core.isUndefined(options_['text'])) options_['text'] = ''; //强制加text 否则外部无法取
                var sValueStr = !core.isUndefined(options_['value']) ? options_['value'] : [] ;
                obj['multi'] = core.getOptVal(options_, ['mul', 'multi', 'multity'], undefined); //是否支持多选
                var lazyCall = core.getOptVal(options_, ['lazy_call', 'lazyCall'], null);
                var itemsOpt = core.getOptVal(options_, ['items'], {});
                var liOpt = core.cloneData(itemsOpt);
                // console.log('items:');
                // console.log(JSON.stringify(itemsOpt));
                var valueKey = core.getOptVal(liOpt, ['value_key', 'valueKey'], '');
                var titleKey = core.getOptVal(liOpt, ['title_key', 'titleKey', 'text_key', 'textKey'], '');
                //如果值是数组 并且多个值 并且未定义是否多选，则默认多选
                if($.isArray(sValueStr) && sValueStr.length>0 && obj['multi'] == undefined) {
                    obj['multi'] = true;
                }
                options_['class'] = core.classAddSubClass(options_['class'], 'diy_items', true);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                //先设定options参数 下面才可以修改options
                var itemValueArray = !core.isUndefined(options_['value']) ? options_['value'] : [] ;
                if(!itemValueArray) itemValueArray = [];
                if(!$.isArray(itemValueArray)) {
                    if(typeof itemValueArray == 'number') {
                        itemValueArray = itemValueArray + '';
                    }
                    if( core.isStrOrNumber(itemValueArray)   && !core.strHasKuohao(itemValueArray)) {
                        if(itemValueArray.indexOf(',') !=-1) {
                            itemValueArray = itemValueArray.split(',');
                        } else {
                            itemValueArray = [itemValueArray];
                        }
                    }
                }
                var ulListObj;
                //只生成一次子对象
                if(!obj['createItem']) {
                    var liDataKey = 'data-value';
                    if(valueKey) {//li中输出值
                        liOpt[liDataKey] = '{'+ valueKey +'}';
                    }
                    core.delProperty(liOpt, ['value_key', 'valueKey',
                        'need_parent_key', 'needParentKey',
                        'title_key', 'titleKey', 'text_key', 'textKey',
                    ]);
                    var liTitleKey = 'data-title';
                    if(titleKey) {//li中输出标题
                        liOpt[liTitleKey] = '{'+ titleKey +'}';
                    }
                    //将用户定义的text专为opt.value
                    liOpt['value'] = liOpt['text'];
                    core.delProperty(liOpt, ['text']);
                    // console.log('liOpt ______:');
                    // console.log(JSON.stringify(liOpt));
                    var systemClick = function (clickObj) {//支持点击事件扩展
                        var liVal = clickObj[liDataKey];
                        // console.log('liVal:', clickObj, liVal);
                        if(obj['multi']) {//多选
                            clickObj.toggleClass('active');
                            obj.reGetValAndText();
                        } else {//单选
                            obj.setItemVal([liVal], [obj],true);
                            renewMenuTextByVal();
                        }
                    };
                    var diyClick = core.getOptVal(liOpt, ['click'], null);
                    if(diyClick) {
                        liOpt['click'] = function (li_, even_, scope) {
                            systemClick(li_);//提前更新value给外部获取
                            diyClick(li_, obj, even_, scope);
                        }
                    } else {
                        liOpt['click'] = function (li_) {
                            systemClick(li_);
                        }
                    }

                    liOpt['disabled'] = "{{this.disabled}==true || {this.disabled}=='true' || {this.disabled}==1}";
                    // console.log('liOpt');
                    // console.log(liOpt);
                    //items参数里的data要给makeList,自己不需要
                    var listOpt = {};
                    //items参数里的data要给makeList,自己不需要
                    if(!core.isUndefined(liOpt['data'])) listOpt['data'] = liOpt['data'];
                    var hasDataFrom = core.getOptVal(liOpt, ['data_from', 'dataFrom'], null);
                    if(hasDataFrom) {
                        listOpt['dataFrom'] = hasDataFrom;
                        menuListSuccess = false;
                    }
                    core.delProperty(liOpt, ['dataFrom', 'data_from','data']);
                    listOpt['li'] = liOpt;
                    listOpt['needParentKey'] = core.getOptNeedParentKey(itemsOpt);
                    listOpt['lazyCall'] = function () {
                        if(!hasDataFrom) {
                            menuListSuccess = true;
                        }
                        console.log('lazyCall');
                        if(valueSetted) {
                            console.log('renewMenuTextByVal', realVal);
                            renewMenuTextByVal();
                        }
                        setTimeout(function () {
                            //延迟执行父绑定的延迟事件
                            if(lazyCall) lazyCall(obj, itemValueArray, core.livingObj);
                        }, 100);
                    };
                    ulListObj = core.makeList(listOpt);
                    var sons = ulListObj.value;
                    var disableVals = [];
                    sons.map(function (v, n) {
                        if(v.disabled == 'true') {
                            disableVals.push(v.attr('data-value'));
                        }
                    });
                    obj['disableVals'] = disableVals;
                    core.optionDataFrom(obj, options_);//
                    ulListObj['parent'] = obj;//设置其父对象
                    obj['menu'] = ulListObj;
                    obj.append(ulListObj);
                    obj['createItem'] = true;

                }

                core.optionDataFrom(obj, options_);
                core.strObj.formatAttr(obj, options_, 0, hasSetData);
            },
            updates: function(dataName, exceptObj) {//数据同步
                console.log('updates items');
                exceptObj = exceptObj || [];
                if(setBind && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(obj);
                    this.setItemVal(getObjData($.trim(setBind)), exceptObj, false);
                    renewMenuTextByVal();
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    core.renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function() {
                var opt = core.cloneData(obj.sor_opt);
                return global.makeItems(opt, true);
            }
        });
        core.objBindVal(obj, options, [{'key_':'bind', 'val_':'value'}, {'key_':'set_text/setText', 'val_':'text'}]);//数据绑定
        obj.renew(options);
        core.optionGetSet(obj, options); // format AttrVals 先获取options遍历更新 再设置读写
        core.addCloneName(obj);//支持克隆
        //console.log('item_obj');
        //console.log(obj);
        return obj; //makeItems
    };

    return global;
});

