//创建简单的按钮对象
define(['require'], function (require) {
    var global = {};
    global.makeInput = function(sourceOptions, sureSource) {
        var core = require('core');
        sourceOptions = sourceOptions || {};
        sureSource = sureSource || false;
        sourceOptions['tag'] = 'input';
        var obj = $('<span></span>');
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            obj.sor_opt = sureSource ?  core.cloneData(sourceOptions) : core.cloneData(core.copySourceOpt(sourceOptions));
        }
        var options = core.cloneData(sourceOptions);
        //input不能直接带form-control样式 会被外层div引起弯角变化
        var inputBind = $.trim(core.getOptVal(options, ['bind'], '')); //数据绑定
        var inputType = $.trim(core.getOptVal(options, ['type'], 'text')); //数据绑定
        var sourceVal = core.getOptVal(options, 'value', '');
        obj.textClearObj  = null; //input右侧清除内容的小x
        obj.menu = null;
        obj.input = null;
        obj.realVal = null;//对象真实的value
        obj.prev = null;
        obj.hasCreateLrBtn = false; //是否已实例化左右按钮 防止二次生成
        obj[core.objValIsNode] = false;
        //支持外部设置 取值
        Object.defineProperty(obj, 'value', {
            get: function() {
                return obj.realVal;
            },
            set: function(newVal) {     //支持外部设值
                obj.setRealInputVal(newVal);
            }
        });
        //支持jq语法val
        obj.val = function (newVal) {
            if(core.isUndefined(newVal)) {
                return obj.value;
            } else {
                obj.value = newVal;
            }
        };
        //更新input的可读状态
        obj.renewReadonly = function(opt) {
            var sourceReadonly = core.getOptVal(opt, ['readonly','readOnly'], '');
            var readKey = core.isUndefined(opt['readonly']) ? 'readonly' : 'readOnly';
            var newData = core.getOptVal(opt, ['data'], {});
            var newVal = core._onFormatVal(obj, newData,  sourceReadonly, readKey);
            if(strInArray(newVal, [true, 'true', 'readonly', 'readOnly']) !==-1) {
                obj.input.attr('readonly', true);
            } else {
                obj.input.removeAttr('readonly');
            }
        };
        //更新input插件val
        obj.formatVal = function (opt) {
            var newData = core.getOptVal(opt, ['data'], {});
            var newVal = core._onFormatVal(obj, newData,  sourceVal);
            var renewBind = true;
            obj[core.objAttrHasKh] = true;
            obj.setRealInputVal(newVal, renewBind, true, [obj]);
            if(obj.setLrBtnDisable) obj.setLrBtnDisable();
            if(obj.lazyCall) {
                obj.lazyCall(obj, newData, core.livingObj);
            }

        }; 
        //input赋值 更新数据绑定
        obj.setRealInputVal = function(newVal, callRenewBindData, resetDomVal, exceptObj) {
            // console.log('set.RealInputVal:'+ newVal);
            exceptObj = exceptObj || [];
            obj.realVal = newVal;
            resetDomVal = core.isUndefined(resetDomVal) ? true : resetDomVal;//更新自己内容
            callRenewBindData = core.isUndefined(callRenewBindData) ? true : callRenewBindData;//召唤同步数据
            if(callRenewBindData && inputBind) {
                if($.inArray(obj, exceptObj) ==-1)  exceptObj.push(obj);
                // console.log('update.BindObj:', inputBind, newVal);
                core.updateBindObj($.trim(inputBind), newVal, exceptObj);
                //console.log('sss', obj[core.objBindAttrsName]);
                if(obj[core.objBindAttrsName] && !core.objIsNull(obj[core.objBindAttrsName]) && !core.isUndefined(obj[core.objBindAttrsName][inputBind])) {
                    core.renewObjBindAttr(obj, inputBind);
                }
            }
            if(resetDomVal) {
                if(inputType != 'file') {
                    obj.input.val(newVal);
                } else {
                    obj.input.attr('data-value', newVal);
                }
            }
            options['value'] = newVal;
            if(obj.textClearObj) {
                if(!newVal) {
                    obj.textClearObj.addClass('hidden');
                } else {
                    obj.textClearObj.removeClass('hidden');
                }
            }
            obj.input.attr('data-old', newVal);
        };
        //创建内置的input对象
        var _createInput = function(optionCopy) {
            var option_ = core.cloneData(optionCopy);
            //console.log('create.Input:');
            var valueKey = core.getOptVal(option_, ['value_key', 'valueKey'], '');
            var li_num = option_['li_num'] ? parseInt(option_['li_num']) : false;
            var input_useClearBtn = !core.isUndefined(options['clear']);//使用清空内容的按钮
            var searchMenuOpt = !core.isUndefined(option_['menu']) ? option_['menu'] : null;
            var searchLiOpt = core.getOptVal(searchMenuOpt, ['li'], null);
            var inputKeyUp = option_['keyup'] || '';
            var inputChange = option_['change'] || '';
            var dataKey = option_['data_key'] || 'data'; //menu的post回调的数据来源
            var issetMaxVal = !core.isUndefined(option_['max']);//不能用 || 写法 因为0也是设置值
            var issetMinVal = !core.isUndefined(option_['min']);
            var maxVal = issetMaxVal ? parseFloat(option_['max']) : '';//数字不能用bool来判断
            var minVal = issetMinVal ? parseFloat(option_['min']) : '';
            var inputTypeAuto =  option_['type'] || 'text';
            var useLrBtn = core.getOptVal(option_, ['lr_btn'], null);//使用左右数量按钮
            var readonly = core.getOptVal(option_, ['readonly', 'readOnly'], null);//readonly
            var autocomplete = core.getOptVal(option_, ['autocomplete'], 'off');//off
            var lrBtnStep = parseFloat(option_['lr_btn_step']) || 1;//左右-+按钮 增减的跳度
            var lrBtnType = option_['lr_btn_type'] || 'middle';//左右-+按钮默认样式 middle right left
            var callKeys = core.getCallData(option_);
            var ajaxPostName = core.getOptVal(option_, ['post_name', 'postName'], (option_['name']||'noname'));// ajax 提交key 默认是 name|post_name
            var ajaxEdit = !core.isUndefined(option_['ajax']) || false;
            var ajaxEditData = option_['post_data'] ||  option_['postData'] || false;
            var ajaxIfKeyLenOver = !core.isUndefined(option_['post_min']) ? option_['post_min'] : 0;//ajax触发请求需要输入的最少字数
            var successKey = callKeys['successKey'];
            var successVal = callKeys['successValue'];
            var successFunc = callKeys['successFunc'];
            var errFunc = callKeys['errorFunc'];
            var inputData = option_['data'] || null;//  data
            var inputPostData = core.getOptVal(option_, ['post_data', 'postData'], null);//  ajax_post_data
            if(successVal && !$.isArray(successVal)) successVal = successVal.toString().split(',');
            var keyup_extend = null;
            var change_extend = null;//输入框的 change 事件

            obj.input =  $('<input class="diy_input" type="'+ inputTypeAuto +'" autocomplete="'+ autocomplete +'" />');
            if(readonly !== null) {
                obj.input.attr('readOnly', true);
            }
            //文件上传还要用到name 外部设置表单的值 也需要用到name直接修改 这个体验还是要保留的好
            obj.empty().append(obj.input);
            if(!core.isUndefined(option_['place'])) obj.input.attr('placeholder', option_['place']);//input 默认背景文字  placeholder
            if(!core.isUndefined(option_['maxlen'])) obj.input.attr('maxlength', option_['maxlen']);//input 最多输入内容
            if(!core.isUndefined(option_['accept'])) {//input 允许的文件类型
                obj.input.attr('accept', option_['accept']);
                delProperty(option_, 'accept');
            }
            //添加 -+ 的左右按钮
            var subNumObj, addNumObj;
            //左右按钮 只实例化1次
            obj.setLrBtnDisable = false;
            if(useLrBtn && !obj.hasCreateLrBtn) {
                if(core.strHasKuohao(maxVal)) {
                    maxVal = formatStr(maxVal, inputData);
                }
                var lrBtnWrap;
                obj.hasCreateLrBtn = true;
                if(lrBtnType == 'right') {
                    lrBtnWrap = $('<div class="btns"></div>');
                    subNumObj = $('<i class="sub_num">-</i>');
                    addNumObj = $('<i class="add_num">+</i>');
                    lrBtnWrap.append(addNumObj).append(subNumObj);
                    obj.input.wrap('<div class="input-group"></div>').wrap('<div class="right_btns"></div>').before(lrBtnWrap);
                } else if(lrBtnType == 'left') {
                    lrBtnWrap = $('<div class="btns"></div>');
                    subNumObj = $('<i class="sub_num">-</i>');
                    addNumObj = $('<i class="add_num">+</i>');
                    lrBtnWrap.append(addNumObj).append(subNumObj);
                    obj.input.wrap('<div class="input-group"></div>').wrap('<div class="left_btns"></div>').before(lrBtnWrap);
                } else {//middle
                    subNumObj = $('<span class="sub_num input-group-addon">-</span>');
                    addNumObj = $('<span class="add_num input-group-addon">+</span>');
                    obj.input.wrap('<div class="input-group"></div>').addClass('text-center').before(subNumObj).after(addNumObj);
                }
                obj.setLrBtnDisable = function () {
                    if(minVal && obj.input.val() && issetMinVal && minVal >= obj.input.val()) {
                        subNumObj.addClass('disabled');
                    }
                    if(maxVal && obj.input.val() && issetMaxVal && maxVal <= obj.input.val()) {
                        addNumObj.addClass('disabled');
                    }
                };
                obj.setLrBtnDisable();
                var newVal;
                subNumObj.on('click', function () {
                    var thisVal = obj.input.val();
                    if(thisVal==='' || isNaN(thisVal)) thisVal = 0;
                    thisVal = parseFloat(thisVal);
                    newVal = thisVal - lrBtnStep;
                    if(issetMinVal && !obj.checkAddSubBtnOk(subNumObj, minVal, newVal<minVal) ) return;
                    obj.setRealInputVal(newVal);
                    //到达最小值也要加disable
                    if(issetMinVal && newVal - lrBtnStep <= minVal) {
                        subNumObj.addClass('disabled');
                    }
                    if(addNumObj.hasClass('disabled')) addNumObj.removeClass('disabled');
                    runFunc(inputKeyUp, obj); //触发keyup
                    runFunc(inputChange, obj); //触发change
                    thisVal = null;
                });
                addNumObj.on('click', function () {
                    var thisVal = obj.input.val();
                    var newMaxVal = obj.attr('max');
                    if(thisVal==='' || isNaN(thisVal)) thisVal=0;
                    thisVal = parseFloat(thisVal);
                    newVal = thisVal + lrBtnStep;
                    if(issetMaxVal && !obj.checkAddSubBtnOk(addNumObj, newMaxVal, newVal>newMaxVal) ) return;
                    obj.setRealInputVal(newVal);
                    //到达最大值也要加disable
                    if(issetMaxVal && newVal + lrBtnStep >= newMaxVal) {
                        addNumObj.addClass('disabled');
                    }
                    if(subNumObj.hasClass('disabled')) subNumObj.removeClass('disabled');
                    runFunc(inputKeyUp, obj); //触发keyup
                    runFunc(inputChange, obj); //触发change
                    thisVal = null;
                });
            }
            //在初始化的配置事件上补充扩展事件 要读默认的配置
            var optionsEvent = {};
            //blur事件扩展
            if(issetMinVal || ajaxEdit ) {
                var blur_extend = function () {
                    var thisVal = obj.input.val();
                    //最小值 要丢焦才能自动限制
                    if(issetMinVal && thisVal !=='') {
                        thisVal = parseFloat(thisVal);
                        if( subNumObj) {
                            if(!obj.checkAddSubBtnOk(subNumObj, minVal, thisVal<minVal)) obj.setRealInputVal(minVal);
                        } else {
                            if(thisVal < parseFloat(minVal)) {
                                obj.setRealInputVal(minVal);
                            }
                        }
                    }
                    //ajax修改  && oldVal != thisVal oldVal 值是提前被修改了
                    if(ajaxEdit) {
                        obj.input.attr('data-old', thisVal);//丢焦时才更新旧值
                        var postData = {};
                        if(ajaxEditData) {
                            postData = core.cloneData(ajaxEditData, postData);//支持自定义打包额外的数据
                        }
                        var newUrl = obj.attr('url');
                        postData[ajaxPostName] = $.trim(thisVal);//name必须重新获取 因为上面的是临时变量
                        if(inputPostData) postData = core.cloneData(inputPostData, postData);
                        global.postAndDone({
                            postUrl: newUrl,
                            postData: postData,
                            successValue: successVal,
                            successKey: successKey,
                            successFunc: successFunc,
                            errorFunc: errFunc
                        }, obj);
                    }
                };
                if(option_['blur']) {
                    optionsEvent['blur_extend'] = option_['blur'];
                    optionsEvent['blur'] = blur_extend;
                } else {
                    optionsEvent['blur'] = blur_extend;
                }
            }

            //搜索菜单
            if(searchMenuOpt && !obj.menu) {
                var ulListOpt = searchMenuOpt || {};
                ulListOpt['li'] = searchLiOpt;
                //限制数量
                if(li_num) {
                    ulListOpt['maxNum'] = li_num;
                }
                if(valueKey) {
                    searchLiOpt['data-value'] = "{"+ valueKey +"}";
                }
                var menuOpt = {'class': ' ajax_menu', value: global.makeList(ulListOpt)};
                var searchMenu = global.makeDiv(menuOpt);
                obj.menu = searchMenu;
                searchMenu['parent'] = obj;//设置父亲
                obj.menu['input'] = obj;//设置input 暴露给外部调取
                obj.append(obj.menu);
                //click 事件扩展
                var systemClickEven = function () {
                    var inputVal = obj.input.val();
                    if (inputVal ) {
                        if(obj.input.attr('data-old') != inputVal) {
                            obj.menu.show();
                            obj.addClass(menuZindexClass);
                        }
                    }
                };
                var userDiyClick = option_['click'];
                optionsEvent['click'] = function (e) {
                    systemClickEven(obj.input, obj, e);
                    userDiyClick(obj.input, obj, e);
                };
                //keyup 事件扩展
                //只有设置了下拉菜单时 才能执行 旧内容输入判断。因为input的ajax保存事件blur 也要用到这个旧内容的更新判断
                change_extend = function () {
                    var text_ = obj.input.val();
                    if(text_.length > 0) {
                        //未达到字数要求，不检测
                        if(ajaxIfKeyLenOver > 0 && text_.length < ajaxIfKeyLenOver) return;
                        var postData = {};
                        //post请求url方式获取数据
                        postData[ajaxPostName] = text_;
                        if(inputPostData) postData = core.cloneData(inputPostData, postData);
                        var inputUrl = obj.options['url'];
                        global.rePost(inputUrl, postData, function(response) {
                            var menuData;
                            if(dataKey) {
                                menuData = response[dataKey] || response;
                            } else {
                                menuData = response;
                            }
                            obj.menu['data'] = menuData;
                            obj.menu.show();
                            obj.addClass(menuZindexClass);
                        });
                    } else {
                        obj.menu.hide();
                        obj.removeClass(menuZindexClass);
                    }
                    if(obj.textClearObj) {
                        if(!text_) {
                            obj.textClearObj.addClass('hidden');
                        } else {
                            obj.textClearObj.removeClass('hidden');
                        }
                    }
                };
            }
            if(keyup_extend) {
                optionsEvent['keyup_extend'] = inputKeyUp;
                optionsEvent['keyup'] = keyup_extend;
            } else {
                if(inputKeyUp) optionsEvent['keyup'] = inputKeyUp;
            }
            //propertychange 默认为事件 触发内容更新和同步  //文件input的选择事件无须触发自更新
            var changeDefaultEven = null;
            if(inputType != 'file') changeDefaultEven = function () {
                formatInputContent(obj, option_, addNumObj);//限制内容格式、最大值
            };
            optionsEvent['input propertychange'] = function (e) {
                if(changeDefaultEven) changeDefaultEven(obj, e);
                if(change_extend) change_extend(obj, e);
            };
            if(inputType == 'file') {  //文件上传 如果需要显示预览图 直接生成在input后面
                var fileInput = obj.input;
                //将文件修改事件赋给真实的file
                var loadingUrl = core.getOptVal(option_, ['loadingUrl', 'loading_url'], null);
                optionsEvent['change_extend'] = function () {
                    var inputUrl = obj.options['url'];
                    $.ajaxFileUpload({
                        url: inputUrl,//上传地址
                        loadingUrl: loadingUrl,//loading地址
                        fileInput: fileInput, //文件上传控件的id属性  <input type="file" id="file" name="file" /> 注意，这里一定要有name值
                        secureuri: false,           //一般设置为false
                        dataType: 'json',//返回值类型 一般设置为json
                        //上传超时、成功都会触发此函数
                        finish: function (data)  //服务器成功响应处理函数
                        {
                            //删除自己 重新创造新的input 防止同名文件无法继续上传
                            _createInput(optionCopy);
                            core.strObj.addEvents(obj); //重新绑定input的事件
                            //清空完毕后再执行事件
                            setTimeout(function () {
                                var tmpSuccessVal = core.getOptVal(data, [successKey], '');
                                //此时的newInput已经在临时表单form中，要重新获取 并绑定事件
                                if(strInArray(tmpSuccessVal, successVal) ==-1 ) {
                                    if(errFunc) {
                                        if(typeof errFunc == 'function') {
                                            errFunc(data);
                                        } else {
                                            eval(errFunc);
                                        }
                                    }
                                } else {
                                    if(successFunc) {
                                        if(typeof successFunc == 'function') {
                                            successFunc(obj, data);//暴露对象是父obj 以为要取值和设置值
                                        } else {
                                            eval(successFunc);
                                        }
                                    }
                                }
                            }, 100);
                        }
                    });
                };
                obj.input.css('cursor', 'pointer');//文件类型要加小手提示
                var prevOpt = option_['prev'] || option_['view'] || option_['preview'] || '';//支持近义词
                prevOpt['data'] = core.cloneData(option_['data']||{}, prevOpt['data']);
                if( prevOpt['value']) {
                    prevOpt['src'] =  prevOpt['value'];
                    delProperty(prevOpt, 'value');
                }
                obj.input.wrap('<div class="hide_input_file"></div>');
                if(prevOpt) { //生成预览图
                    if(core.isUndefined(prevOpt['class'])) prevOpt['class'] = 'preview_img';
                    var prevPosition = prevOpt['pos'] || 'left'; //出现的位置 left/right/l/r
                    var prevImgObj = global.makeImg(prevOpt);
                    obj.prev = prevImgObj;
                    if(strInArray(prevPosition, ['right', 'r']) != -1) {
                        obj.append(prevImgObj);
                    } else {
                        obj.prepend(prevImgObj);
                    }
                    //console.log(prevImgObj);
                }
                //input的data更新时，pre子对象也要更新
                obj.renewSonData = function(newData) {
                    //console.log('renew SonData', obj.prev, newData);
                    //console.log(obj.prev['data']);
                    newData = newData || [];
                    //console.log(newData);
                    renewObjData(obj.prev, newData);
                };
                //console.log('is_file2');
                //console.log(optionsEvent);
            }
            //累加事件补充给属性用
            obj.events = core.cloneData(optionsEvent, obj.events);
            //console.log(obj);
            //console.log(obj.events);
            obj.bindEvenObj = obj.input;//定义 绑定事件的对象
            //input清除按钮
            if(input_useClearBtn) {
                obj.textClearObj = global.makeSpan({
                    'class' : 'lrXX', //无内容时要 +hidden 隐藏
                    'click': function (btn_) {
                        btn_.addClass('hidden');
                        obj.setRealInputVal('');
                    }
                });
                obj.append(obj.textClearObj);
            }
            //创建input组件和捆绑事件
            return obj.input;
        };
        //检测当前input的值是否达到最(大/小)值
        obj.checkAddSubBtnOk = function(btn, limitNum, numberOverLimit) {
            var inputKeyUp = '';
            var options = obj['options'] || '';
            if(options) inputKeyUp = options['keyup'] || '';
            limitNum = limitNum || 0;//数字极限值
            numberOverLimit = numberOverLimit || false;//数字是否超过许可
            if (numberOverLimit) {
                if(!btn.hasClass('disabled')) {
                    btn.addClass('disabled');
                }
                obj.setRealInputVal(limitNum);
                if(inputKeyUp) runFunc(inputKeyUp, obj); // 到达最(大/小)值 也触发keyup
                return false;
            } else {
                if(btn.hasClass('disabled')) btn.removeClass('disabled');
            }
            return true;
        };

        //通知更新bind
        function __callRenewSelfVal(text_, setSelf) {
            setSelf = core.isUndefined(setSelf) ? true : setSelf;
            // console.log('__callRenewSelfVal:'+ text_);
            obj.setRealInputVal(text_, true, setSelf);
        }
        //objName 要转成的变量名字
        //格式化内容 数字、浮点、最大最小值
        function formatInputContent(obj_, options_, addNumObj) {
            addNumObj = addNumObj || null;
            var issetMaxVal = !core.isUndefined(options_['max']);//不能用 || 写法 因为0也是设置值
            var maxVal = issetMaxVal ? parseFloat(options_['max']) : '';//数字不能用bool来判断
            var contentLimit = options_['limit'] || '';//内容限制
            var oldText = $.trim(obj_.input.attr('data-old'));
            var text_ = obj_.input.val();
            //console.log('new_text_:'+ text_);
            text_ = $.trim(text_);
            if(contentLimit == 'int') {
                var reg = /[^0-9]/gi;
                if(reg.test(text_)) {
                    text_ = text_.replace(reg,'');
                    __callRenewSelfVal(text_);
                    return;
                }
                if(text_) text_ = parseFloat(text_);
            } else if(contentLimit == 'float') {
                if(isNaN(text_) && text_ != '-') {
                    text_ = text_.replace(/[^0-9.]/gi,'');
                    //最多只能输入1个小数点
                    var result = text_.match(RegExp(/\./, 'g'));
                    var count = !result ? 0 : result.length;
                    if(count>1) {
                        var n = text_.indexOf('.');
                        var text_2 = text_.substr(n+1);
                        var s1 = text_.substr(0, n+1);
                        var n2 = text_2.indexOf('.');
                        var s3 = text_2.slice(0, n2);
                        text_ = (s1+s3);
                    }
                    __callRenewSelfVal(text_);
                    return;
                }
                if(text_) text_ = parseFloat(text_);
            }
            //console.log('text_1:'+ text_);
            //console.log('oldText:'+ oldText);
            //console.log('new_text_:'+ text_);
            if(oldText == text_) return;
            if(issetMaxVal &&text_ > parseFloat(maxVal)) {
                __callRenewSelfVal(maxVal);
                return;
            }
            //最大值 自动限制
            if (issetMaxVal) {
                text_ = parseFloat(text_);
                if (addNumObj) {
                    if (!obj_.checkAddSubBtnOk(addNumObj, maxVal, text_ > maxVal)) {
                        __callRenewSelfVal(maxVal);
                        return;
                    }
                } else {
                    if (text_ > parseFloat(maxVal)) {
                        __callRenewSelfVal(maxVal);
                        return;
                    }
                }
            }
            //console.log('text_2:'+ text_);
            //更新真实val
            //console.log('更新真实val:'+ text_);
            __callRenewSelfVal(text_, false);
            text_ = null;
        }
        //
        //外部更新所有属性
        obj.extend({
            focus: function() {
                obj.input.focus();
            },
            //主动更新数据
            renew: function(options_) {
                //console.log('obj options_:');
                //console.log(obj);
                //console.log(options_);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                //先设定options参数 下面才可以修改options
                options_['type'] =  options_['type'] || 'text';//必须声明类型 因为写入值时要根据类型作判断 
                if(core.isUndefined(options_['value'])) options_['value'] = '';//默认要绑的属性 不声明无法设置值
                var inputVal = options_['value'];
                var objExtendClass = 'diy_input_box';
                if(inputType == 'file') {//file input
                    objExtendClass = 'diy_upload_input';
                }
                var inputSize = options['size'] || '';
                if(core.sizeIsXs(inputSize)) {
                    objExtendClass = 'diy_input_box input-group-xs';
                } else if(core.sizeIsSm(inputSize)) {
                    objExtendClass = 'diy_input_box input-group-sm';
                } else if(core.sizeIsMd(inputSize)) {
                    objExtendClass = 'diy_input_box input-group-md';
                } else if(core.sizeIsBg(inputSize)) {
                    objExtendClass = 'diy_input_box input-group-bg';
                } else if(core.sizeIsLg(inputSize)) {
                    objExtendClass = 'diy_input_box input-group-lg';
                }
 
                options_['class_extend'] = objExtendClass;
                options_['class'] = core.classAddSubClass(options_['class'], objExtendClass, true);
                var hasSetData = !core.isUndefined(options_['data']);
                //console.log(options_['class']);
                core.optionDataFrom(obj, options_);
                //初始化value
                obj.realVal = inputVal;
                //格式化和绑定事件交由内部input 因为每次重新生成input 事件都需要重新绑定
                _createInput(options_);//重新创建一个input
                core.strObj.formatAttr(obj, options_, 0, hasSetData);
            },
            updates: function(dataName, exceptObj) {//数据同步
                exceptObj = exceptObj || [];
                // console.log('updates input dataName:'+ dataName, exceptObj);
                if(inputBind) {
                    if($.inArray(obj, exceptObj) ==-1) {
                        // console.log('exceptObj.push:');
                        exceptObj.push(obj);
                        obj.setRealInputVal(core.getObjData($.trim(inputBind)), false);
                    }
                }
                if(obj[core.objBindAttrsName] && obj[core.objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    //console.log('call renew input');
                    //console.log(this);
                    core.renewObjBindAttr(this, dataName);
                }
                //如果更新了size 父样式要改变
                if(core.strHasKuohao(options['size'])) {
                    if($.inArray(dataName, core.getKuohaoAbc(options['size'], 'public')) !=-1) {//size has change by public data
                        obj.renewSizeClass(core.getObjData(dataName));
                    }
                }
            },
            //克隆当前对象
            cloneSelf: function() {
                var opt = core.cloneData(obj.sor_opt);
                return global.makeInput(opt, true);
            }
        });

        obj.renew(options);
        core.optionGetSet(obj, options);
        core.objBindVal(obj, options);//数据绑定
        core.addCloneName(obj, options);//支持克隆
        return obj;
    };
    return global;
});

