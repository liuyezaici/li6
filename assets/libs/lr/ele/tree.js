define(['require'], function (require) {
    var global = {};
    var objValObjKey = 'obj_val_objs';//当前对象包含的obj  每个人对象创建成功后，其val都会保存当前值或dom对象 字符串形式的value除非
    var objBindAttrsName = 'bind_attrs';
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入
    //创建树形菜单
    global.makeTree = function(sourceOptions, sureSource) {
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
        var onReadyEven = core.getOptVal(options, ['onload', 'onLoad', 'ready', 'onReady'], null);
        var sourceVal = core.getOptVal(options, ['value'], '');
        var realValArray = [];
        //统一头部判断结束
        obj['tag'] = 'tree';
        obj[objValIsNode] = false;
        obj['createItem'] = false;
        var isMultity = undefined;
        var valueSetted = false;//当前对象的value是否格式化完成
        var menuListSuccess = false;//当前对象的menu是否渲染完成 [menu需要渲染 才会用到]
        var valIsNumber = false;
        var parentChecked = 'parent_checked';//上级复选框
        var sonCheckeds = 'son_checkeds';//下级复选框
        var menuOpt = core.getOptVal(options, ['menu'], null);
        var liOpt = core.getOptVal(options, ['li'], null);
        var liCheckedBox = core.getOptVal(liOpt, ['checkedBox'], null);
        var liVal = core.getOptVal(liOpt, ['value'], null);
        if(!menuOpt) {
            console.log('no set menu');
            return;
        }
        if(!liOpt) {
            console.log('no set menu.li');
            return;
        }
        var valueKey = core.getOptVal(menuOpt, ['value_key', 'valueKey'], '');
        var titleKey = core.getOptVal(menuOpt, ['title_key', 'titleKey', 'text_key', 'textKey'], '');
        if(!valueKey) {
            console.log('no set menu.valueKey');
            return;
        }
        if(!titleKey) {
            console.log('no set menu.titleKey');
            return;
        }
        var sonDataKey = core.getOptVal(menuOpt, ['son_key', 'sonKey', 'son_data_key', 'sonDataKey'], null);//data子数据

        //单独的格式化value的括号
        obj.formatVal = function (opt) {
            opt = opt || [];
            var newData = core.getOptVal(opt, ['data'], {});
            realValArray = core._onFormatVal(obj, newData,  sourceVal);
            // console.log('format -- ObjVal', realValArray);
            valueSetted = true;
            // console.log('menuListSuccess:', menuListSuccess);
            if(menuListSuccess) {
                if(realValArray !== '') {
                    valIsNumber = core.isNumber(realValArray);
                    obj.setItemVal(realValArray, [obj], true);
                }
            }
            //console.log('format_val');
            //如果值是数组 并且多个值 并且未定义是否多选，则默认支持多选
            if($.isArray(realValArray) && realValArray.length>0 && isMultity == undefined) {
                isMultity = true;
            }
            if(obj.lazyCall) {
                obj.lazyCall(obj, newData);
            }

        };

        //支持外部设置 取值
        Object.defineProperty(obj, 'value', {
            set: function (newVal) {
                if(valIsNumber) {
                    newVal = parseFloat(newVal);
                    // console.log('set number:' ,newVal);
                }
                // console.log('set newVal:' , valIsNumber,newVal);
                valueSetted = true;
                obj.setItemVal(newVal, [obj], true);
                renewMenuTextByVal();
            },
            get: function () {
                if(isMultity) {
                    return $.isArray(realValArray) ? realValArray : realValArray.toString().split(',');
                } else {
                    return $.isArray(realValArray) ? realValArray.join('') : realValArray ;
                }
            }
        });
        //支持外部取选中的文本 返回数组格式
        Object.defineProperty(obj, 'text', {
            get: function () {
                //多选时，才返回数组
                return isMultity ? obj.itemTxtArray : ($.isArray(obj.itemTxtArray) ? obj.itemTxtArray.join('') : obj.itemTxtArray) ;
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
                if(core.isNumber(liVal) && valIsNumber) {
                    liVal = parseFloat(liVal);
                }
                if(tmpItem.hasClass('active')) {
                    valArray_.push(liVal);
                    textArray_.push(liTitle);
                }
            });
            realValArray = valArray_;
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

        var checkLiIsInVal = function(liVal) {
            if(Array.isArray(realValArray)) {
                return core.strInArray(liVal, realValArray) !=-1;
            } else {
                return liVal == realValArray;
            }
        };
        //通过当前val取text
        obj.getItemTextByVal = function () {
            if(!realValArray || !realValArray.length) {
                return '';
            }
            if(!Array.isArray(realValArray)) {
                if(core.isNumber(realValArray) && valIsNumber) {
                    realValArray = [parseFloat(realValArray)];
                } else {
                    realValArray = realValArray.toString().split(',');
                }
            }
            //设置(更新)select的text
            var textArray_ = [];
            var ulLis = obj[objValObjKey];
            // console.log('realValArray', realValArray);
            // console.log('ulLis', ulLis.length);
            var checkedObj ;
            $.each(ulLis, function(n, li_) {
                checkedObj = li_.value;
                // console.log('tmpItem', n, tmpItem);
                var liVal = checkedObj.value;


                var liTitle = tmpItem.attr('data-title');
                // console.log('liVal:', liVal);
                if(realValArray) {
                    if(checkLiIsInVal(liVal, realValArray)) {
                        li_.addClass('active');
                        textArray_.push(liTitle);
                    } else {
                        li_.removeClass('active');
                    }
                } else {
                    li_.removeClass('active');
                }
            });
            obj.itemTxtArray = textArray_;
            return textArray_;
        };
        //更新选中的值和文本
        obj.setItemVal = function(newVal, exceptObj, renewBind) {
            if(!Array.isArray(newVal)) {
                if(core.isNumber(newVal) && valIsNumber) {
                    realValArray = [parseFloat(newVal)];
                } else {
                    realValArray = newVal.toString().split(',');
                }
            } else {
                realValArray = newVal;
            }
            exceptObj = exceptObj || [];
            renewBind = core.isUndefined(renewBind) ? true : renewBind;
            var valStr = realValArray.join(',');
            if(valStr !== obj.attr('data-value')) {
                obj.attr('data-value', valStr);
            }
            var ulLis = obj[objValObjKey];
            // console.log('ulLis', ulLis.length);
            var checkedObj_, checkedVal;
            $.each(ulLis, function (i, li_) {
                checkedObj_ = li_.value;
		        checkedVal = checkedObj_.value;
                if(checkedVal !=='' && realValArray) {
                    if(checkLiIsInVal(checkedVal, realValArray)) {
                        checkedObj_.checked = 1;
                    } else {
                        checkedObj_.checked = 0;
                    }
                } else {
                    checkedObj_.checked = 0;
                }
            });

            if(renewBind && setBind) {
                //触发数据同步  触发赋值 */
                if($.inArray(obj, exceptObj) == -1) exceptObj.push(obj);
                if(realValArray.length) {
                    core.updateBindObj($.trim(setBind), realValArray, exceptObj);
                }
                if(obj[objBindAttrsName] && !core.objIsNull(obj[objBindAttrsName]) && !core.isUndefined(obj[objBindAttrsName][setBind])) {
                    core.renewObjBindAttr(obj, setBind);
                }
            }
        };
        //创建根菜单 只有根菜单可以带even 其他子节点不能带
        var createRootMenu = function() {
            var optArray = core.cutOptEvens(menuOpt);
            var menuNoEven = optArray[0];

            var liValObj = [];
            if(liCheckedBox) {
                var systemClickChecked = function (obj_) {
                    console.log('toggle checked parent');
                };
                var diyClick = core.getOptVal(liCheckedBox, 'click', null);
                if(diyClick) {
                    liCheckedBox['click'] = function (obj_, e_, scope) {
                        systemClickChecked(obj_, e_, scope);
                        diyClick(obj_, e_, scope);
                    }
                } else {
                    liCheckedBox['click'] = function (obj_, e, scope) {
                        systemClickChecked(obj_, e_, scope);
                    }
                }
                liValObj.push(core.makeChecked(liCheckedBox));
            }
            if(core.isStrOrNumber(liVal)) {
                liValObj.push(core.makeSpan({value: liVal}));
            } else {
                liValObj.push(liVal);
            }
            if(sonDataKey) {
                liValObj.push(core.makeList(menuNoEven));
            }
            menuNoEven['li']['value'] = liValObj;
            return core.makeList(menuNoEven);
        };
        //创建子菜单 不能带even
        var createRootMenu = function() {
            var menu_Opt = menuOpt;
            return _createMenu(menu_Opt);
        };
        obj.extend({
            //主动更新数据
            renew: function(options_) {
                options_ = options_ || {};
                var hasSetData = !core.isUndefined(options_['data']);
                if(core.isUndefined(options_['value'])) options_['value'] = ''; //强制加value 否则外部无法取
                if(core.isUndefined(options_['text'])) options_['text'] = ''; //强制加text 否则外部无法取
                var sValueStr = !core.isUndefined(options_['value']) ? options_['value'] : [] ;
                isMultity = core.getOptVal(options_, ['mul', 'multi', 'multity'], undefined); //是否支持多选
                var lazyCall = core.getOptVal(options_, ['lazy_call', 'lazyCall'], null);
                var itemsOpt = core.getOptVal(options_, ['items'], {});
                var liOpt = core.cloneData(itemsOpt);
                // console.log('items:');
                // console.log(JSON.stringify(itemsOpt));
                //如果值是数组 并且多个值 并且未定义是否多选，则默认多选
                if($.isArray(sValueStr) && sValueStr.length>0 && isMultity === undefined) {
                    isMultity = true;
                } else {
                    if(sValueStr && core.isNumber(sValueStr)) {
                        isMultity = false;
                    }
                }
                // console.log('valIsNumber', valIsNumber);
                // console.log('isMultity', isMultity);
                options_['class'] = core.classAddSubClass(options_['class'], 'diy_items', true);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                obj.rootMenu = createRootMenu();
                obj.append(obj.rootMenu);
                core.optionDataFrom(obj, options_);
                core.strObj.formatAttr(obj, options_, 0, hasSetData);
            },
            updates: function(dataName, exceptObj) {//数据同步
                // console.log('update!!');
                exceptObj = exceptObj || [];
                if(setBind && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(obj);
                    this.setItemVal(core.getObjData($.trim(setBind)), exceptObj, false);
                    renewMenuTextByVal();
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    core.renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function() {
                var opt = core.cloneData(obj.sor_opt);
                return global.makeTree(opt, true);
            }
        });
        obj.renew(options);
        core.optionGetSet(obj, options); // format AttrVals 先获取options遍历更新 再设置读写
        core.objBindVal(obj, options, [{'key_':'bind', 'val_':'value'}, {'key_':'set_text/setText', 'val_':'text'}]);//数据绑定
        core.addCloneName(obj);//支持克隆
        core.addTimer(obj);//添加定时器绑定
        if(onReadyEven) {
            onReadyEven(obj);
        }
        return obj; //makeTree
    };

    return global;
});

