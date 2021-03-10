define(['require'], function (require) {
    var global = {};
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入
    global.makeForm = function(sourceOptions) {
        var core = require('core');
        var options = core.cloneData(sourceOptions);
        if(core.isUndefined(options['type'])) options['type'] = 'post';//post/get//upload
        var url = options['url'] || '#';//post url
        var formType = options['type'];
        var defaultSubmit = options['submit'] || null;
        var replaceDataFunc = core.getOptVal(options, ['replaceData', 'replace_data'], null);
        var postData = core.getOptVal(options, ['post_data', 'postData'], null);
        if(formType == 'upload') options['enctype'] = 'multipart/form-data'; //文件上传表单

        var successObj =  $.extend({}, options);
        options['submit'] = function (thisObj, e) {
            e.preventDefault();
            // console.log('submit thisObj', thisObj);
            var postDataForm = core._getFormData(thisObj);
            var nullFunc = thisObj.getFormNullErr();//找到是否有禁止留空的拦截
            if(postData) postDataForm = core.cloneData(postData, postDataForm);
            if(nullFunc && nullFunc.length>0) {
                if(typeof nullFunc[0] != 'string') {
                    nullFunc[1].focus();
                    nullFunc[0](nullFunc[1], nullFunc[2]);
                }
                return false;
            }
            //格式化数据
            if(replaceDataFunc) {
                postDataForm = replaceDataFunc(postDataForm, thisObj, e);
            }
            //系统函数前置则会注定提交 该由用户来决定是否要提交
            if(defaultSubmit) {
                var response = defaultSubmit(thisObj, e);
                if(response === false) return false;
            }
            //console.log(formType);
            if(formType == 'post') {//执行post以及post成功之后的回调动作
                successObj['post_data'] = postDataForm;
                successObj['post_url'] = url;
                core.postAndDone(successObj, thisObj);
            } else if(formType == 'get') {//执行get 需要用到 target
                window.open(url + $.serialize(postDataForm),'_blank');
            }
        };
        options[objValIsNode] = false; //不允许再append val
        return core.makeDom({tag:'form', 'options': options});
    };
    return global;
});

