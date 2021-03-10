define(['require'], function (require) {
    var global = {};
    var objBindAttrsName = 'bind_attrs';
    var objAttrHasKh = 'obj_opt_has_kuohao';//obj的属性包含有{} 则可能绑定全局变量
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入
    global.makeImg = function (sourceOptions, sureSource) {
        var core = require('core');
        sourceOptions = sourceOptions || {};
        sourceOptions['tag'] = 'img';
        sureSource = sureSource || false;
        var obj = $('<img />');
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            obj.sor_opt = sureSource ?  core.cloneData(sourceOptions) : core.cloneData(core.copySourceOpt(sourceOptions));
        }
        var options = core.cloneData(sourceOptions);
        var sourceVal = core.getOptVal(options, ['src', 'value'], '');
        var setBind = core.getOptVal(options, ['bind'], '');
        var valKey = !core.isUndefined(options['src']) ? 'src': 'value';
        obj[objValIsNode] = false;
        var valueStrFormatdSuccess = false;
        //单独的格式化value的括号
        obj.formatVal = function (opt) {
            opt = opt || [];
            var newData = !core.isUndefined(opt['data']) ? opt['data'] : false;
            // console.log('format Val_before:', sourceVal);
            var newVal = core._onFormatVal(obj, newData,  sourceVal, valKey);
            if(newVal && !core.strHasKuohao(newVal)) {
                obj.attr('src', newVal);
            }
            var renewBind = obj[objAttrHasKh]==true;
            if(setBind && renewBind) {//触发数据同步  触发赋值 */
                core.updateBindObj($.trim(setBind), newVal, [obj]);
            }
            if(valueStrFormatdSuccess) {
                if(obj.lazyCall) {
                    obj.lazyCall(obj, newData);
                }
            }
        };
        //外部设置val
        obj.extend({
            //主动更新数据
            renew: function(options_) {
                var hasSetData = !core.isUndefined(options_['data']);
                //console.log('renew img');
                //console.log(options_);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                //先设定options参数 下面才可以修改options
                var loadFunc = options_['load'] || null;
                var loadError = options_['error'] || null;
                core.optionDataFrom(this, options_);
                core.strObj.formatAttr(this, options_, 0, hasSetData);
                //console.log(options_);
                //onload完成事件
                obj.off('load').on('load', function (e) {
                    if(loadFunc) loadFunc(obj, e);
                }).on('error', function (e) {
                    if(loadError) loadError(obj, e);
                });
            },
            updates: function(dataName, exceptObj) {//数据同步
                exceptObj = exceptObj || [];
                if(setBind && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(this);
                    this.attr('src', core.getObjData($.trim(setBind)));
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    core.renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function() {
                var opt = core.cloneData(obj.sor_opt);
                return global.makeImg(opt, true);
            }
        });
        //支持value
        Object.defineProperty(obj, 'value', {
            get: function () {
                return this.attr('src');
            },
            set: function(n) {     //支持外部设值
                //console.log('set img val:'+ n);
                if (!core.isUndefined(setBind)) {
                    core.updateBindObj($.trim(setBind), n, [obj]);//同步更新
                }
                this.attr('src', n);
            }
        });
        Object.defineProperty(obj, 'src', {
            //console.log('set img src:'+ n);
            get: function () {
                //读取值 用于外部表单打包数据
                return this.attr('src');
            },
            set: function(n) {     //支持外部设值
                if (!core.isUndefined(setBind)) {
                    core.updateBindObj($.trim(setBind), n, [obj]);//同步更新
                }
                this.attr('src', n);
            }
        });
        obj.renew(options);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        core.optionGetSet(obj, options, 'src'); //参数读写绑定 参数可能被外部重置 所以要同步更新参数
        core.objBindVal(obj, options, [{'key_':'bind', 'val_':'src'}]);
        core.addCloneName(obj, options);//支持克隆
        return obj;
    };
    return global;
});

