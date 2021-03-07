define(['require'], function (require) {
    var global = {};
    var objValObjKey = 'obj_val_objs';//当前对象包含的obj  每个人对象创建成功后，其val都会保存当前值或dom对象 字符串形式的value除非
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入
    //创建循环数据List
    /*
    id : '',
    'class': '',
    name: '',
    li:{
    value : '',
    'data-val': '{u_id}',
    value: 'uid:{u_id} unick: {u_nick}',
    click: function (li, parentObj) {
    }
    }
    */
    global.makeList = function(sourceOptions, sureSource) {
        var core = require('core');
        sourceOptions = sourceOptions || {};
        sureSource = sureSource || false;
        sourceOptions['tag'] = 'list';
        var obj = $('<ul></ul>');
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染
            obj.sor_opt = sureSource ?  core.cloneData(sourceOptions) : core.copySourceOpt(sourceOptions);
        }
        // console.log('makeList.sor_opt.data', obj, obj.sor_opt.data);
        var options = core.cloneData(sourceOptions);
        obj[objValObjKey] = [];//子对象
        obj[objValIsNode] = false;
        if(core.isUndefined(options['value'])) options['value'] = []; //为了外部可以统一输出 要配置value(其自身可能是用li来设置值的)
        obj.INeedParentValFlag = undefined;//我需要父value来获取data select场景使用
        if(!obj.hasOwnProperty('value')) {
            Object.defineProperty(obj, 'value', {
                get: function () {
                    //console.log('get val');
                    return obj[objValObjKey];
                }
                ,set: function (newVal) {
                    obj[objValObjKey] = (newVal);
                }
            });
        }
        //外部通知事件 比如select的更新text指令会下发给list来延迟调取
        obj.lazyCall = core.getOptVal(options, ['lazy_call', 'lazyCall'], null);

        //更新list.data
        obj.renewSonLen = function (opt) {
            // console.log('renew.SonLen', opt);
            var newData = core.getOptVal(opt, ['data'], {});
            // console.log('renew SonLen List.sor_opt.data', obj.sor_opt.data);
            // console.log('newData',  newData);
            var sons = obj[objValObjKey];
            var sonFirst = sons[0];
            if(core.isUndefined(opt['data'])) return;
            var maxNum = core.getOptVal(opt, ['maxNum', 'max_num', 'maxLen', 'max_len'], false);
            if(core.hasData(newData) && maxNum && isNumber(maxNum)) {
                newData = newData.slice(0, maxNum);
            }
            // console.log('renew.SonLen');
            // console.log('sons');
            // console.log(newData);
            //如果之前产生过多的儿子而新数量变少要剔除
            var lastValLen = sons.length;
            var nowValLen = newData.length;
            var tmpIndex;
            // console.log('newData:', newData);
            console.log('lastValLen:'+ lastValLen);
            console.log('nowValLen:'+ nowValLen);
            if(lastValLen > nowValLen) { //多出来 裁掉
                //console.log('remove more');
                //如果没有data，要copy一个临时的
                if(nowValLen ==0 && !obj['default_li'] ) {
                    //保留之前的li的value 继续复制一个li 不能从源opt开始克隆，会丢失之后渲染的li.value
                    var newOpt = core.cloneData(sonFirst['sor_opt']);
                    newOpt['tag'] = 'li';
                    obj['default_li'] = sonFirst.cloneSelf(newOpt);
                }
                sons.splice(nowValLen, lastValLen-nowValLen).forEach(function (o) {
                    o.remove();
                });
                for(tmpIndex = 0; tmpIndex < nowValLen ; tmpIndex++) {
                    newTmpData = newData[tmpIndex];
                    core.renewObjData(sons[tmpIndex], newTmpData);
                }
            } else if(lastValLen < nowValLen) { //数据累加 要克隆第一个li
                //console.log('lastValLen:'+ lastValLen);
                //console.log('nowValLen:'+ nowValLen);
                //console.log(sons);
                //console.log(sonFirst);
                var tmpIndex,newLi, newTmpData;
                //console.log(newData);
                for(tmpIndex = 0; tmpIndex < nowValLen ; tmpIndex++) {
                    newTmpData = newData[tmpIndex];
                    if(!core.isUndefined(sons[tmpIndex])) {
                        core.renewObjData(sons[tmpIndex], newTmpData);
                    } else {
                        if(!core.hasData(sons)) {
                            // console.log('保留之前的li的value 继续复制一个li 不能从源opt开始克隆，会丢失之后渲染的li.value');
                            newLi = obj['default_li'].cloneSelf();
                            sonFirst = newLi;
                        } else {
                            console.log('sonFirst___is___:');
                            console.log(sonFirst);
                            console.log(sonFirst['sor_opt']);
                            console.log(JSON.stringify(sonFirst['sor_opt']['data']));
                            //console.log(sonFirst['options']['value']);
                            //保留之前的li的value 继续复制一个li  opt不能提早带上新data 会导致后续无法克隆新li的原始data
                            var newOpt = core.cloneData(sonFirst['sor_opt']);
                            newOpt['tag'] = 'li';
                            console.log('newOpt', newOpt, newOpt['data']);
                            newLi = sonFirst.cloneSelf(newOpt);
                            console.log('sonFirst.clone');
                            console.log(newLi);
                            console.log(newLi.data);
                        }
                        //console.log('cloneSonLi');
                        //console.log(newLi);
                        newLi['parent'] = obj;
                        sons[sons.length] = newLi;
                        console.log('等克隆完li的属性才能更新data 不然提早渲染的data可能无法再次刷新');
                        newLi['data'] = newData[tmpIndex];
                        this.append(newLi);
                    }

                }
            } else {
                //长度未变 只更新子data
                for(tmpIndex = 0; tmpIndex < nowValLen ; tmpIndex++) {
                    newTmpData = newData[tmpIndex];
                    // console.log('renewObjData', sons[tmpIndex], newData[tmpIndex]);
                    core.renewObjData(sons[tmpIndex], newData[tmpIndex]);
                }
            }
            // console.log('renew SonLenEEEE List.sor_opt.data', obj.sor_opt.data);
            //console.log(sons);
        };

        //创建多个子对象
        function makeRepeatLi(liOpt, liOneData, dataIndex) {
            var hasSetData = core.hasData(liOneData);
            var extendParentData = true;
            // console.log('make RepeatLi :', dataIndex, liOneData, extendParentData);
            //new trs
            //第一行data直接用子对象生成 span->div->li
            if(dataIndex == 0) {
                // console.log('line1_________', extendParentData, liOpt);
                var tmpOpt_ = core.cloneData(liOpt);
                tmpOpt_['extendParentData'] = extendParentData;
                // console.log('make 000000000000:', tmpOpt_);
                var newLiObj = core.makeLi(tmpOpt_);
                // console.log('make RepeatLi00000:', sonFirstOpt);
                core.objPushVal(obj, newLiObj);
                //之前的son由于提前创建好的，所以其data都是空的，所以更新
                if(hasSetData) {
                    // console.log('之前的son由于提前创建好的，data都是空的，所以更新:', obj, liOneData);
                    core.renewObjData(newLiObj, liOneData);
                }
                // console.log('after renewObjData Li00000:', newLiObj['sor_opt']);
            } else {
                //第2行data开始 要用sor_opt重新生成新的 li->div->span
                var sons = obj[objValObjKey];
                var sonFirstOpt = sons[0]['sor_opt'];
                var tmpOpt_ = core.cloneData(sonFirstOpt);//克隆
                // console.log('make  li2_____________', tmpOpt_, tmpOpt_['data'], liOneData);
                //这里不应该提前设置data 会让tr的sor_opt误以为先天d带data
                tmpOpt_['extendParentData'] = extendParentData;
                // console.log('tmpOpt_', tmpOpt_, tmpOpt_['value']);
                var newLiObj = core.makeLi(tmpOpt_);
                // console.log('make RepeatLi_1+++:', newLiObj);
                core.objPushVal(obj, newLiObj);
                if(hasSetData) {
                    // console.log('renew data: RepeatLi_1+++:', newLiObj);
                    core.renewObjData(newLiObj, liOneData);
                }
            }
        }

        //克隆li的可循环数据
        function createListSon(obj, options) {
            //console.log('clone.ListSon:');
            //console.log(obj);
            var liOpt = $.extend({}, options['li'] || {});//子的公共配置 li: {}
            if(!core.hasData(liOpt)) {
                console.log('未定义li');
                return;
            }
            var optionsData = options['data'] || null; // data: {son_data}

            //有数组数据才循环
            if($.isArray(optionsData) && core.hasData(optionsData)) {
                $.each(optionsData, function (dataIndex, tmpData) {//data循环数据
                    makeRepeatLi(liOpt, tmpData, dataIndex);
                });
            } else {
                makeRepeatLi(liOpt, optionsData, 0);
            }

        }


        obj.extend({
            //主动更新数据
            renew: function(options_) {
                obj.INeedParentKey = core.getOptNeedParentKey(options_);
                obj.INeedParentValFlag = obj.INeedParentKey;//需要父参数渲染好才能请求url
                // console.log('renew list');
                // console.log(obj);
                // console.log(options_);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                //先设定options参数 下面才可以修改options
                var hasSetData = !core.isUndefined(options_['data']);
                core.optionDataFrom(this, options_);
                core.strObj.formatAttr(this, options_, 0, hasSetData);//其内容 已经在clone li里全部生成过了 只差数据来格式化了
                //console.log('end ');
            },
            //克隆当前对象
            cloneSelf: function() {
                var opt = core.cloneData(obj.sor_opt);
                return global.makeList(opt, true);
            }
        });
        createListSon(obj, options);
        obj.renew(options);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        core.optionGetSet(obj, options);
        core.objBindVal(obj, options);//数据绑定
        core.addCloneName(obj, options);//支持克隆
        //对象直接设置了data 可以触发 延迟执行
        var dataFrom = core.getOptVal(options, ['data_from', 'dataFrom'], null);
        if(obj.lazyCall) {
            //设置了data 可以立刻延迟执行
            if(core.hasData(core.getOptVal(options, ['data']))) {
                obj.lazyCall(obj, core.livingObj);
            } else {
                //没有设置 data_from 可以立刻延迟执行
                if(!dataFrom) {
                    obj.lazyCall(obj, core.livingObj);
                }
            }
        }
        //console.log(obj);
        return obj;
    };
    return global;
});

