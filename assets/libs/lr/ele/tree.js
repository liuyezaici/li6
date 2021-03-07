//创建简单的li对象
define(['core'], function (core) {
    var global = {};

    var objValObjKey = 'obj_val_objs';//当前对象包含的obj  每个人对象创建成功后，其val都会保存当前值或dom对象 字符串形式的value除非
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入
    var objBindAttrsName = 'bind_attrs';
    //创建树菜单对象 只能更新、修改起data的长度 data不能设置对象
    var __makeTreeInnerObj = function(sourceOptions) {
        var options = core.cloneData(sourceOptions);
        var obj = makeDiv(options);
        //更新循环的tree的date
        obj.renewOldTree = function(newData) {
            //console.log('renew.SonData');
            //console.log(newData);
            var sons;
            if(hasData(this['treeFirstSons'])) {//只更新循环部分的tr
                sons = this['treeFirstSons'];
                if(!$.isArray(newData)) newData = [newData];
                var sonData;
                $.each(sons, function (n, son) {
                    sonData = newData[n];
                    if(!sonData) sonData = []; //数据突然为空
                    renewObjData(son, sonData);
                })
            }
        };
        //更新tree.data 如果含有带循环的tree 则只更新data的tr；反之更新全部tr
        obj.renewSonLen = function(opt) {
            var newData = core.getOptVal(opt, ['data'], {});
            //console.log(obj);
            var nowValLen = newData.length;
            var sons;
            if(hasData(this['treeFirstSons'])) {
                //console.log('hasData _____o');
                sons = this['treeFirstSons'];
                if(!$.isArray(newData)) newData = [newData];
                //如果之前产生过多的儿子而新数量变少要剔除
                var lastValLen = sons.length;
                //console.log('lastValLen:'+ lastValLen);
                //console.log('nowValLen:'+ nowValLen);
                //console.log('nowValLen:'+ nowValLen);
                if(lastValLen > nowValLen) { //多出来 裁掉
                    sons.splice(nowValLen, lastValLen-nowValLen).forEach(function (o) {
                        o.remove();
                    });
                    obj['treeFirstSons'] = sons; //移除son
                    //console.log('remove more td,now:');
                    //console.log(sons);
                    //更新data
                    var tmpTreeCheckDataId; //第几行
                    var tmpData;
                    for(tmpTreeCheckDataId = 0; tmpTreeCheckDataId < nowValLen; tmpTreeCheckDataId++) {
                        tmpData =  newData[tmpTreeCheckDataId];
                        //console.log('tmpTreeCheckDataId:'+ tmpTreeCheckDataId);
                        if(!core.isUndefined(sons[tmpTreeCheckDataId])) {
                            //console.log('renew_tmpIndex:'+ tmpIndex);
                            sons[tmpTreeCheckDataId]['data'] = tmpData;
                        }
                    }
                } else if(lastValLen < nowValLen) { //数据累加 要克隆第一个tr 并且累加到最后一个循环的对象背后
                    var newChecked;
                    //console.log('lastValLen < nowValLen');
                    //console.log('newData');
                    //console.log(newData);
                    var tmpTreeCheckDataId; //tr第几组
                    var tmpData;
                    for(tmpTreeCheckDataId = 0; tmpTreeCheckDataId < nowValLen; tmpTreeCheckDataId++) {
                        tmpData =  newData[tmpTreeCheckDataId];
                        //console.log('tmpTreeCheckDataId:'+ tmpTreeCheckDataId);
                        //console.log('tmpTreeCheckDataId:'+ tmpTreeCheckDataId);
                        //console.log('tmpData');
                        //console.log(tmpData);
                        if(!core.isUndefined(sons[tmpTreeCheckDataId])) {
                            //console.log('renew_tmpIndex:'+ tmpIndex);
                            sons[tmpTreeCheckDataId]['data'] = tmpData;
                        } else {
                            newChecked = sons[0].cloneSelf();
                            //console.log('cloneOpt newChecked');
                            //console.log(newChecked);
                            //console.log(tmpData);
                            newChecked['parent'] = this;
                            sons[sons.length-1].after(newChecked);
                            sons[sons.length] = newChecked;
                            //等克隆完tr的属性才能更新data 不然提早渲染的data可能无法再次刷新
                            newChecked['data'] = tmpData;
                        }
                    }
                } else {
                    //刷新循环的tr
                    obj.renewOldTree(newData);
                }
            }
        };
        return obj;
    };
    //创建树形分类 用于快速管理树形分类
    global.makeTree = global.makeTrees = function(sourceOptions, sureSource) {
        var core = require('core');
        sourceOptions = sourceOptions || {};
        sureSource = sureSource || false;
        var obj = $('<div></div>');
        obj.tag = 'tree';
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            obj.sor_opt = sureSource ?  core.cloneData(sourceOptions) : core.cloneData(copySourceOpt(sourceOptions));
        }
        var options = core.cloneData(sourceOptions);
        var setBind = core.getOptVal(options, ['bind'], '');
        var sourceVal = core.getOptVal(options, ['value'], '');
        //统一头部判断结束
        obj[objValIsNode] = false;
        obj['createTree'] = false;
        obj['multi'] = undefined;
        obj[objValObjKey] = [];//子checked对象的集合
        obj['treeFirstSons'] = [];  //一共有多少个首级分类
        var treeMenuOpt = core.getOptVal(options, ['menu'], {});
        var valueKey = core.getOptVal(treeMenuOpt, ['value_key', 'valueKey'], '');
        var titleKey = core.getOptVal(treeMenuOpt, ['title_key', 'titleKey', 'text_key', 'textKey'], '');
        var sonDataKey = core.getOptVal(treeMenuOpt, ['son_key', 'sonKey', 'son_data_key', 'sonDataKey'], null);//data子数据
        var treeLiOpt = core.getOptVal(treeMenuOpt, ['li'], null);//单元格附加显示内容
        var valueHasSeted = true;//当前对象的value是否渲染完成
        var xuanranTreemenuSuccess = true;//当前对象的menu是否渲染完成
        var parentCheckedsKey = 'parent_checked';//上级复选框
        var sonCheckedsKey = 'son_checkeds';//下级复选框
        //单独的格式化value的括号
        obj.formatVal = function (opt) {
            opt = opt || [];
            //每次格式化 优先取格式化前的source value
            var newData = core.getOptVal(opt, ['data'], {});
            var newVal = core._onFormatVal(obj, newData,  sourceVal);
            opt['value'] = newVal; //参数要改变 防止外部取出来的仍是括号
            if(core.strHasKuohao(sourceVal)) {
                if (sourceVal != newVal) {
                    valueHasSeted = true;
                    if(xuanranTreemenuSuccess === true) {
                        obj.setTreeVal(newVal, (newVal !=''));
                    }
                }
            } else {
                obj.setTreeVal(newVal, (newVal !=''));
            }
            //console.log('format_val');
            //如果值是数组 并且多个值 并且未定义是否多选，则默认支持多选
            if($.isArray(newVal) && newVal.length>0 && obj['multi'] == undefined) {
                obj['multi'] = true;
            }
            if(valueHasSeted) {
                if(obj.lazyCall) {
                    obj.lazyCall(obj, newData);
                }
            }
        };

        //外部设置tree选中项
        obj.setTreeVal = function(newVal, notifyOther, exceptObj) {
            notifyOther = notifyOther || false;
            exceptObj = exceptObj || [];
            if(!Array.isArray(newVal)) newVal = newVal.toString().split(',');

            $.each(obj[objValObjKey], function (i, obj_) {
                if(obj_.value !=='' && strInArray(obj_.value, newVal) !=-1) {
                    obj_.checked = 1;
                } else {
                    obj_.checked = 0;
                }
            });
            if(setBind && notifyOther) {
                if($.inArray(obj, exceptObj) ==-1) {
                    exceptObj.push(obj);
                }
                core.updateBindObj($.trim(setBind), newVal, exceptObj);
            }
        };
        //支持外部设置 取值
        Object.defineProperty(obj, 'value', {
            set: function (newVal) {
                return obj.setTreeVal(newVal, true);
            },
            get: function () {
                var newVal = [];
                $.each(obj[objValObjKey], function (i, obj_) {
                    if(obj_.value !=='') newVal.push(obj_.value);
                });
                return newVal;
            }
        });
        //支持外部取选中的文本 返回数组格式
        Object.defineProperty(obj, 'text', {
            get: function () {
                var newVal = [];
                $.each(obj[objValObjKey], function (i, obj_) {
                    if(obj_.value !=='') newVal.push(obj_.title);
                });
                return ($.isArray(newVal) ? newVal.join(',') : newVal) ;
            }
        });

        //移除所有子checked的选中状态
        function __sonRemoveChecked(obj_) {
            if(obj_[sonCheckedsKey]) {
                $.each(obj_[sonCheckedsKey], function (index, tmpObj) {
                    if(tmpObj.checked==true) tmpObj.checked = false;
                    __sonRemoveChecked(tmpObj);
                });
            }
        }
        //给所有子checked的添加选中状态
        function __sonAddChecked(obj_) {
            if(obj_[sonCheckedsKey]) {
                $.each(obj_[sonCheckedsKey], function (index, tmpObj) {
                    if(tmpObj.checked == false) tmpObj.checked = true;
                    __sonAddChecked(tmpObj);
                });
            }
        }
        //给所有父checked的添加选中状态
        function __parAddChecked(obj_) {
            if(obj_[parentCheckedsKey]) {
                var tmpParObj = obj_[parentCheckedsKey];
                if(tmpParObj.checked == false) tmpParObj.checked = true;
                __parAddChecked(tmpParObj);
            }
        }
        //克隆多行的可数据循环的tr
        function createRepeatDataTree(optionsData, liOpt_, checkOpt, appendTo, parentObj) {
            var tmpLiOpt = core.cloneData(liOpt_);
            var tmpCheckOpt = core.cloneData(checkOpt);
            var dataLen = 0; //循环的tr内部数量
            $.each(tmpCheckOpt, function () {
                dataLen ++;
            });
            //创建多个子对象
            function makeTreeSons(_liOpt, _checkOpt, _treeData, dataParentIndex) {
                //new trs
                var liObj;
                var copyLiOpt = core.cloneData(_liOpt);
                var checkOpt,diyClick,
                    sonMenuPbj = null,  //子层data留空
                    checkObj;
                $.each(_treeData, function (n, tmpData) {
                    diyClick = _checkOpt['click'];
                    checkOpt = core.cloneData(_checkOpt);
                    //console.log(diyClick.toString());
                    checkOpt['click'] = function (obj_, e) {
                        if(diyClick) {
                            diyClick(obj_, e);
                        }
                        //更新父子选项
                        // 如果当前取消,子选项要全部取消
                        // 如果当前取消,并且同级都是已取消，父选项要一起取消
                        // 如果当前选择选中，父选项要选中
                        //console.log(obj_.checked);
                        if(!obj_.checked) {
                            __sonRemoveChecked(obj_);
                        } else {
                            //所有子都要打勾
                            __sonAddChecked(obj_);
                            //所有父都要打勾
                            __parAddChecked(obj_);
                        }
                        if(setBind) {
                            core.updateBindObj($.trim(setBind), obj.value, [obj]);
                        }
                    };

                    checkObj = global.makeCheck(checkOpt);
                    if(parentObj) {
                        checkObj[parentCheckedsKey] = parentObj;
                        if(parentObj[sonCheckedsKey]) {
                            parentObj[sonCheckedsKey].push(checkObj);
                        } else {
                            parentObj[sonCheckedsKey] = [checkObj];
                        }
                    }
                    if(!core.isUndefined(tmpData[sonDataKey])) {
                        sonMenuPbj = global.makeDiv({
                            'class': 'son_menu'
                        });
                        copyLiOpt['value'] = [
                            checkObj,
                            sonMenuPbj
                        ];
                        if(treeLiOpt) {
                            var sonVal = [];
                            if(!$.isArray(treeLiOpt.value)) {
                                treeLiOpt.value = [treeLiOpt.value];
                            }
                            treeLiOpt.value.forEach(function (tmpObj) {
                                var  newSon ;
                                if(core.isOurObj(tmpObj)) {
                                    //保留之前的li的value 继续复制一个li 不能从源opt开始克隆，会丢失之后渲染的li.value
                                    var newOpt = core.cloneData(tmpObj['options']);
                                    newSon = tmpObj.cloneSelf(newOpt);

                                } else {
                                    newSon = tmpObj.clone();
                                }
                                sonVal.push(newSon);
                            });
                            treeLiOpt.value = sonVal;
                            copyLiOpt['value'].push(core.makeSpan(treeLiOpt));
                        }
                    } else {
                        if(treeLiOpt) {
                            var sonVal = [];
                            if(!$.isArray(treeLiOpt.value)) {
                                treeLiOpt.value = [treeLiOpt.value];
                            }
                            treeLiOpt.value.forEach(function (tmpObj) {
                                var  newSon ;
                                if(core.isOurObj(tmpObj)) {
                                    //保留之前的li的value 继续复制一个li 不能从源opt开始克隆，会丢失之后渲染的li.value
                                    var newOpt = core.cloneData(tmpObj['options']);
                                    newSon = tmpObj.cloneSelf(newOpt);
                                } else {
                                    newSon = tmpObj.clone();
                                }
                                sonVal.push(newSon);
                            });
                            treeLiOpt.value = sonVal;
                            copyLiOpt['value'] = [checkObj, global.makeSpan(treeLiOpt)];
                        } else {
                            copyLiOpt['value'] = checkObj;
                        }
                    }
                    liObj = global.makeLi(core.cloneData(copyLiOpt));
                    liObj[parentObjKey] = obj;//分配父对象
                    obj[objValObjKey].push(checkObj);//累计子对象li
                    liObj['data'] = tmpData; //必须克隆完再更新data
                    //console.log('append li :');
                    //console.log(liObj);
                    appendTo.append(liObj);
                    if(dataParentIndex==0) obj['treeFirstSons'].push(liObj); //带数据的tr 缓存obj的子对象
                    //子层data渲染
                    if(!core.isUndefined(tmpData[sonDataKey])) {
                        //console.log('append_son');
                        createRepeatDataTree(tmpData[sonDataKey], liOpt_, _checkOpt, sonMenuPbj, checkObj)
                    }
                });
            }
            //有数组数据才循环
            makeTreeSons(tmpLiOpt, tmpCheckOpt, optionsData, 0);
        }
        obj.extend({
            //主动更新数据
            renew: function(options_) {
                if(core.isUndefined(options_['value'])) options_['value'] = ''; //强制加value 否则外部无法取
                var sValueStr = !core.isUndefined(options_['value']) ? options_['value'] : [] ;
                obj['multi'] = core.getOptVal(options, ['mul', 'multi', 'multity'], undefined); //是否支持多选
                var selectValueArray = sValueStr;
                //如果值是数组 并且多个值 并且未定义是否多选，则默认多选
                if($.isArray(sValueStr) && sValueStr.length>0 && obj['multi'] == undefined) {
                    obj['multi'] = true;
                }
                options_['class'] = classAddSubClass(options_['class'], 'diy_trees', true);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                if(core.isStrOrNumber(selectValueArray) && core.strHasKuohao(selectValueArray)) {
                    valueHasSeted = false; //设为未渲染完成
                }

                treeMenuOpt['disabled'] = "{{this.disabled}==true || {this.disabled}=='true' || {this.disabled}==1}";
                if(core.getOptVal(treeMenuOpt, ['data_from', "dataFrom"], null)) {
                    xuanranTreemenuSuccess = false;
                }
                //console.log('ulOpt');
                //console.log(obj);
                //console.log(JSON.stringify(ulOpt));
                //console.log(JSON.stringify(options_));
                var liOpt = core.cloneData(treeMenuOpt);
                var checkOpt = {
                    value: "{"+ valueKey +"}",
                    text: "{"+ titleKey +"}"
                };
                treeMenuOpt['class_extend'] = 'tree_inner';
                var objInner = __makeTreeInnerObj(treeMenuOpt);
                obj.append(objInner);
                //console.log('tree.options_');
                //console.log(JSON.stringify(options_));
                var hasSetData = !core.isUndefined(options_['data']);
                core.optionDataFrom(objInner, options_);
                core.delProperty(liOpt, ['data', 'son_key', 'sonKey']);
                core.copyEvens(liOpt, checkOpt);
                createRepeatDataTree(treeMenuOpt['data'], liOpt, checkOpt, objInner, null);
                obj['son'] = objInner;
                objInner[parentObjKey] = obj; //设置其父对象
                core.strObj.formatAttr(obj, options_, 0, hasSetData);
            },
            updates: function(dataName, exceptObj) {//数据同步
                //console.log('uptree.dates______:'+dataName);
                //console.log(exceptObj);
                exceptObj = exceptObj || [];
                if(setBind && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(obj);
                    this.setTreeVal(core.getObjData($.trim(setBind)), false, exceptObj);
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    core.renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function() {
                var opt = core.cloneData(obj.sor_opt);
                return core.makeTree(opt, true);
            },
        });
        core.objBindVal(obj, options, [{'key_':'bind', 'val_':'value'}, {'key_':'set_text/setText', 'val_':'text'}]);//数据text绑定
        obj.renew(options);
        core.optionGetSet(obj, options); // format AttrVals 先获取options遍历更新 再设置读写
        core.addCloneName(obj, options);//支持克隆 
        return obj; //makeTree
    };
    return global;
});
