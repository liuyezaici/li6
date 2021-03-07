define(['require'], function (require) {
    var global = {};
    var objValObjKey = 'obj_val_objs';//当前对象包含的obj  每个人对象创建成功后，其val都会保存当前值或dom对象 字符串形式的value除非
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入
    global.makeTable = function(sourceOptions, sureSource) {
        var core = require('core');
        sourceOptions = sourceOptions || {};
        sureSource = sureSource || false;
        var obj = $('<table width="100%" border="0"></table>');
        var tbody = $('<tbody></tbody>');
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            obj.sor_opt = sureSource ?  core.cloneData(sourceOptions) : core.copySourceOpt(sourceOptions);
        }
        var options = core.cloneData(sourceOptions);
        obj.append(tbody);
        obj.tag = 'table';
        obj.tBody = tbody;
        obj['noRepSons'] = [];//记录所有不循环的tr数组
        obj['repeatSons'] = [];//记录所有循环的tr数组 用于更新数据 新增裁剪等动作
        obj[objValIsNode] = false;
        var tableRepeatKey = '';
        if(!core.isUndefined(options['tr'])) {
            tableRepeatKey = 'tr';
        }

        //数据循环时 使用此方法更新数据列表
        var _renewRepeatSonLen = function (repeatSons, newData) {
            if(!$.isArray(newData)) newData = [newData];
            //如果之前产生过多的儿子而新数量变少要剔除
            var lastValLen = repeatSons.length;
            var nowValLen = newData.length ;
            //更新data
            var dataLines = newData.length;
            var tmpTrGroupid; //遍历在第N行data
            var tmpTrGroupFromIndex;
            var tmpIndex;
            var tmpData;
            // return;
            if(lastValLen > nowValLen) { //多出来 裁掉
                // console.log('多出来 裁掉');
                repeatSons.splice(nowValLen, lastValLen-nowValLen).forEach(function (o) {
                    o.remove();
                });
                obj['repeatSons'] = repeatSons;
                //遍历data数据
                for(tmpTrGroupid = 0; tmpTrGroupid < dataLines; tmpTrGroupid++) {
                    tmpTrGroupFromIndex = tmpTrGroupid;
                    tmpData =  newData[tmpTrGroupid];
                    if(!core.isUndefined(repeatSons[tmpTrGroupFromIndex])) {
                        repeatSons[tmpTrGroupFromIndex]['data'] = tmpData;
                    }
                }
            } else if(lastValLen < nowValLen) { //数据累加 要克隆第一个tr 并且累加到最后一个循环的对象背后
                // console.log('tr累加:');
                for(tmpTrGroupid = 0; tmpTrGroupid < dataLines; tmpTrGroupid++) {
                    tmpTrGroupFromIndex = tmpTrGroupid ;
                    tmpData =  newData[tmpTrGroupid];
                    //一组组更新/创建tr
                    if(!core.isUndefined(repeatSons[tmpTrGroupFromIndex])) {
                        repeatSons[tmpTrGroupFromIndex]['data'] = tmpData;
                    } else {
                        // console.log('直接用虚拟tr进行克隆 ', i2, tmpData);
                        var cloneTr = core.cloneData(obj['sor_opt']['tr']); //必须新克隆一个  不然会污染上一个tr的source opt
                        if($.isArray(cloneTr)) cloneTr = cloneTr[0];
                        //console.log('cloneTr', cloneTr);
                        cloneTr['tag'] = 'tr';
                        //data不能提前设置 因为sor_opt本来就是空的
                        cloneTr['extendParentData'] = true;//强制继承父data更新
                        var newTr = core.makeTr(cloneTr);
                        newTr['parent'] = obj;//分配父对象
                        console.log('call----> set data', newTr, tmpData);
                        newTr['data'] = tmpData;//更新data
                        //找到最后一个循环的tr 没有的话就直接append
                        if(obj['repeatSons'].length) {
                            var lastRepeatTr = obj['repeatSons'][obj['repeatSons'].length-1];
                            lastRepeatTr.after(newTr);
                        } else {
                            obj.tBody.append(newTr);
                        }
                        obj['repeatSons'].push(newTr);
                    }
                }
            } else {
                // console.log('更新旧的tr ', newData);
                // console.log('renew sons');
                // console.log(repeatSons);
                var sonData;
                $.each(repeatSons, function (n, son) {
                    sonData = newData[n]; //数据要每隔一组tr再更新
                    if(!sonData) sonData = []; //数据突然为空
                    core.renewObjData(son, sonData);
                })
            }
        };
        //外部触发更新子data
        obj.renewSonData = function (newData) {
            var repeatSons = obj['repeatSons'] ;
            //更新循环的tr数组 可能被清空过 需要恢复循环
            if(tableRepeatKey) {
                _renewRepeatSonLen(repeatSons, newData);
            }
            //更新不循环的tr数组
            var noRepeatSons = obj['noRepSons'] ;
            $.each(noRepeatSons, function (n, son) {
                core.renewObjData(son, newData);
            });
        };

        //创建多个子对象
        function makeRepeatTrs(trOpts, trOneData, dataIndex) {
            if($.isArray(trOpts)) trOpts = trOpts[0];
            var hasSetData = core.hasData(trOneData);
            var extendParentData = hasSetData ? false : true;
            // console.log('make RepeatTrs ________dataIndex:', dataIndex, trOneData, extendParentData);
            //new trs
            //第一行data直接用子对象生成 span->td->tr
            if(dataIndex == 0) {
                console.log('line1_________', trOneData, trOpts);
                var tmpOpt_ = core.cloneData(trOpts);
                tmpOpt_['extendParentData'] = extendParentData;
                // console.log('make 000000000000:', tmpOpt_);
                var newTrObj = core.makeTr(tmpOpt_);
                // console.log('make RepeatTrs ________newTrObj:', newTrObj);
                newTrObj['parent'] = obj;
                obj['repeatSons'].push(newTrObj); // 子对象
                //之前的son由于提前创建，其data是空的，所以更新
                if(hasSetData) {
                    console.log('call----> set data', newTrObj, trOneData);
                    core.renewObjData(newTrObj, trOneData);
                }
                obj.tBody.append(newTrObj);
            } else {
                //第2行data开始 要用sor_opt重新生成新的 tr->td->span
                // console.log('line2_________', trOpts);
                var tmpOpt_ = core.cloneData(core.copySourceOpt(trOpts));//克隆
                // console.log('tmpOpt_', tmpOpt_);
                //这里不应该提前设置data 会让tr的sor_opt误以为先天d带data
                tmpOpt_['extendParentData'] = extendParentData;
                var newTrObj = core.makeTr(tmpOpt_);
                if(hasSetData) {
                    // console.log('call----> set data', newTrObj, trOneData);
                    newTrObj['data'] = trOneData;
                }
                // console.log('make RepeatTrs newTrObj:', newTrObj);
                newTrObj['parent'] = obj;
                obj['repeatSons'].push(newTrObj); // 子对象
                obj.tBody.append(newTrObj);
            }
        }

        //克隆多行的可数据循环的tr
        function createRepeatDataTrs(options) {
            // console.log('create RepeatDataTrs RepeatTrs _________');
            var optionsData = options['data'] || null; // data: {son_data}
            var trOptions = options[tableRepeatKey] || {};//子的公共配置 tr: {}
            //有数组数据才循环
            if($.isArray(optionsData) && core.hasData(optionsData)) {
                $.each(optionsData, function (dataIndex, tmpData) {//data循环数据
                    makeRepeatTrs(trOptions, tmpData, dataIndex);
                });
            } else {
                makeRepeatTrs(trOptions, optionsData, 0);
            }
        }

        //写入TR
        obj.appendTrs = function(options_) {
            //提取所有的tr_ ,tr
            var tabData = options_['data'] || [];
            var i_ = 0;//计算tr出现的位置
            $.each(options_, function (key_, val_) {
                if(key_.substr(0,2) == 'tr' || key_.substr(0,3) == 'tr_') {
                    if(key_ == 'tr') {
                        if($.isArray(val_)) { //tr: {td: {}}
                            val_ = val_[0];
                        }
                        options_[tableRepeatKey] = val_;
                        //console.log('create____RepeatDataTrs_:::::::::::::', key_);
                        createRepeatDataTrs(options_);
                    } else if(key_.substr(0,2) == 'tr') {//生成不循环的tr_n 也可以解析data
                        //console.log(val_);
                        if(!$.isArray(val_)) { //tr_2:{td: {}}  => tr_2:[{td: {}},{td: {}}]
                            val_ = [val_];
                        }
                        val_.forEach(function (trOpt) {
                            var tabDataFrom = core.getOptVal(options_, ['data_from', 'dataFrom'], null);
                            if(!tabDataFrom && core.hasData(tabData)) {
                                var sonOptBack = core.optionAddData(trOpt, tabData);
                                var tmpData = sonOptBack[0];
                                trOpt['data'] = tmpData; //data不能提前赋予  否则会导致无法继承父data
                            }
                            var trObj = core.makeTr(trOpt);
                            trObj['parent'] = obj; //分配父对象
                            obj['noRepSons'].push(trObj);
                            obj.tBody.append(trObj);
                        });
                    }
                }
                i_ ++ ;
            });
        } ;
        obj.extend({
            renew: function(options_) {
                var hasSetData = !core.isUndefined(options_['data']);
                core.optionDataFrom(obj, options_);
                //console.log('renew table');
                //console.log(this);
                //console.log(options_);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                //先设定options参数 下面才可以修改options
                core.strObj.formatAttr(this, options_, 0, hasSetData);
            },
            cloneSelf: function() {
                var opt = core.cloneData(obj.sor_opt);
                return global.makeTable(opt, true);
            }
        });
        Object.defineProperty(obj, 'value', {
            get: function () {
                //读取值 用于外部表单打包数据
                return this['noRepSons'].concat(this['repeatSons']);
            }
        });
        Object.defineProperty(obj, objValObjKey, {
            get: function () {
                //读取值 用于外部表单打包数据
                return this['value'];
            }
        });
        //全选
        obj.selectAll = function(inputName) {
            inputName = inputName || '';
            if(!inputName) {
                console.log('no set inputName');
                return;
            }
            //有选中 则反选 支持 name/.class/findName
            var selectAllFlag = false;
            if(inputName.indexOf('.') !=-1) {
                selectAllFlag = true;
                if(obj.find("."+ inputName +"").first().prop('checked') == true) {
                    selectAllFlag = false;
                }
                obj.find("."+ inputName +"").prop('checked', selectAllFlag);
            } else {
                var findInputByName = obj.find("input[name='"+ inputName +"']");
                if(findInputByName.length) {
                    selectAllFlag = true;
                    if(findInputByName.first().prop('checked') == true) {
                        selectAllFlag = false;
                    }
                    findInputByName.prop('checked', selectAllFlag);
                } else {
                    selectAllFlag =  core.isUndefined(obj['selectAll'+inputName]) ? 1 : obj['selectAll'+inputName];
                    obj['selectAll'+inputName] = !selectAllFlag;
                    $.each(obj['value'], function (k, tr) {
                        if(tr.findName(inputName)) {
                            tr.findName(inputName).checked = selectAllFlag ? true: false;
                        }
                    });
                }
            }
        };
        //已选
        obj.selected = function(inputName) {
            inputName = inputName || '';
            if(!inputName) {
                console.log('no set inputName');
                return;
            }
            var inputs = [];
            if(inputName.indexOf('.') !=-1) {
                inputs = obj.find("."+ inputName +"");
            } else {
                inputs = obj.find("input[name='"+ inputName +"']");

            }
            var selectIds = [];
            if(inputs.length) {
                $.each(inputs, function (k, tmpTrInput_) {
                    if(tmpTrInput_.prop('checked')) {
                        selectIds.push(tmpTrInput_.val());
                    }
                });
            } else {
                $.each(obj['value'], function (k, tr) {
                    if(tr.findName(inputName) && tr.findName(inputName).checked) {
                        selectIds.push(tr.findName(inputName).value);
                    }
                });
            }
            return selectIds;
        };
        obj.appendTrs(options);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        obj.renew(options);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        core.optionGetSet(obj, options);
        core.addCloneName(obj, options);//支持克隆
        return obj;
    };
    return global;
});

