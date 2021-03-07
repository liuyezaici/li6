define(['require'], function (require) {
    var global = {};
    var objBindAttrsName = 'bind_attrs';
    var objAttrHasKh = 'obj_opt_has_kuohao';//obj的属性包含有{} 则可能绑定全局变量
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入

    //创建编辑器[属性：name,width,height,content urlencode , type:'uEditor|xheditor', editorObj: 回调的编辑器对象'
    global.makeEditor = function(sourceOptions, sureSource) {
        var core = require('core');
        sourceOptions = sourceOptions || {};
        sureSource = sureSource || false;
        var editorId =  getOptVal(options, 'id', 'editormd');
        var obj = $('<textarea id="'+ editorId +'"></textarea>');
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            obj.sor_opt = sureSource ?  cloneData(sourceOptions || {}) : cloneData(copySourceOpt(sourceOptions));
        }
        var options = cloneData(sourceOptions);
        var setBind = getOptVal(options, ['bind'], '');
        var sourceVal = getOptVal(options, ['value'], '');

        obj['tag'] = 'editor';
        obj[objValIsNode] = false;
        var valueStrFormatdSuccess = true;//当前value是否渲染完成

        //单独的格式化value的括号
        obj.formatVal = function (opt) {
            opt = opt || [];
            var newData = getOptVal(opt, ['data'], {});
            var newVal = _onFormatVal(obj, newData,  sourceVal);
            var editorOut = !isUndefined(opt['editorObj']) ? opt['editorObj'] : 'editor';
            opt['value'] = newVal; //参数要改变 防止外部取出来的仍是括号
            obj.renewVal(newVal);
            if (sourceVal != newVal) {
                //console.log('value change');
                valueStrFormatdSuccess = true;
            }
            var renewBind = obj[objAttrHasKh]==true;
            if(setBind && renewBind) {//触发数据同步  触发赋值 */
                updateBindObj($.trim(setBind), newVal, [obj]);
            }
            if(valueStrFormatdSuccess) {
                if(obj.lazyCall) {
                    obj.lazyCall(obj, newData, livingObj);
                }
            }
            if(obj[editorOut] && obj[editorOut].setContent) {
                obj[editorOut].setContent(newVal);
            }
            obj.formatPlugs(opt);
        };
        obj.renewVal = function(newVal) {
            obj.val(newVal);
        };
        //渲染插件
        obj.formatPlugs = function(options) {
            var editorType = !isUndefined(options['type']) ? options['type'] : 'uEditor';//text|uEditor|umEditor|xheditor|editormd
            var editorOpt = getOptVal(options, ['editorOpt', 'editorOption'], null);
            var editorOut = !isUndefined(options['editorObj']) ? options['editorObj'] : 'editor';
            var editorId = !isUndefined(options['id']) ? options['id'] : 'editormd';//editormd
            //编辑器扩展 支持插入代码
            var newEditorObj;
            //上面设置了读写属性 下面才能设置
            //先设定options参数 下面才可以修改options
            //id需要在页面种才能渲染编辑器
            setTimeout(function () {
                if(strInArray(editorType, ['xheditor', 'xhEditor']) !=-1) {
                    //这里可以统一设置编辑器样式
                    newEditorObj = obj.xheditor({
                        'plugins':  {
                            Code:{
                                c:'btnCode',t:'插入代码', h:1, e: function() {
                                    var _this=this;
                                    var htmlCode="<div>编程语言<select id='xheCodeType'>" +
                                        "<option value='html'>HTML/XML</option>" +
                                        "<option value='js'>Javascript</option>" +
                                        "<option value='css'>CSS</option>" +
                                        "<option value='php'>PHP</option>" +
                                        "<option value='java'>Java</option>" +
                                        "<option value='py'>Python</option>" +
                                        "<option value='pl'>Perl</option>" +
                                        "<option value='rb'>Ruby</option>" +
                                        "<option value='cs'>C#</option>" +
                                        "<option value='c'>C++/C</option>" +
                                        "<option value='vb'>VB/ASP</option>" +
                                        "<option value=''>其它</option>" +
                                        "</select></div><div>";
                                    htmlCode+="<textarea id='xheCodeValue' wrap='soft' spellcheck='false' style='width:300px;height:100px;' />";
                                    htmlCode+="</div><div style='text-align:right;'><input type='button' id='xheSave' value='确定' /></div>";
                                    var jCode=$(htmlCode),jType=$('#xheCodeType',jCode),jValue=$('#xheCodeValue',jCode),jSave=$('#xheSave',jCode);
                                    jSave.click(function(){
                                        _this.loadBookmark();
                                        _this.pasteHTML('<pre class="prettyprint lang-'+jType.val()+'">'+_this.domEncode(jValue.val())+'</pre> ');
                                        _this.hidePanel();
                                        return false;
                                    });
                                    _this.saveBookmark();
                                    _this.showDialog(jCode);
                                }
                            }
                        },
                        tools: "Link,Unlink,Source,Removeformat,Code,Img,|Fullscreen",
                        skin: 'nostyle'
                    });
                    //支持外部取值
                    if(!obj.hasOwnProperty('value')) {
                        Object.defineProperty(obj, 'value', {
                            get: function () {
                                return obj.val();
                            }
                        });
                    }
                    obj[editorOut] = newEditorObj;
                }
                else if(strInArray(editorType, ['uEditor', 'ueditor']) !=-1) {
                    UE.delEditor(editorId);//防止已经实例化过此编辑器
                    var defToolbars = [[
                        'fullscreen', 'source', '|', 'undo', 'redo', '|',
                        'bold', 'italic', 'underline', 'fontborder', 'strikethrough',  'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', 'insertimage', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
                        'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                        'customstyle', 'paragraph', 'fontfamily', 'fontsize', 'link', 'unlink', 'horizontal', 'spechars', '|',
                        'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
                        'print', 'preview', 'searchreplace', 'drafts', 'help'
                    ]];
                    var toolbars = getOptVal(options, ['toolbars'], defToolbars);
                    newEditorObj = UE.getEditor(editorId, {toolbars: toolbars});
                    //支持外部取值
                    if(!obj.hasOwnProperty('value')) {
                        Object.defineProperty(obj, 'value', {
                            get: function () {
                                return newEditorObj.getContent();
                            }
                        });
                    }
                    obj[editorOut] = newEditorObj;
                    obj[editorOut].pasteHTML = function (newContent) {
                        newEditorObj.setContent(newContent, true);
                    }
                } else if(strInArray(editorType, ['umEditor', 'umeditor']) !=-1) {
                    UM.delEditor(editorId);//防止已经实例化过此编辑器
                    var defToolbars = [
                        'fullscreen', 'source', 'undo', 'redo','bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript',
                        'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor',
                        'backcolor', 'insertorderedlist', 'insertunorderedlist'
                    ];
                    var toolbars = getOptVal(options, ['toolbars', 'toolbar'], defToolbars);
                    newEditorObj = UM.getEditor(editorId, {
                        toolbar: toolbars}
                    );
                    //支持外部取值
                    if(!obj.hasOwnProperty('value')) {
                        Object.defineProperty(obj, 'value', {
                            get: function () {
                                return newEditorObj.getContent();
                            }
                        });
                    }
                    obj[editorOut] = newEditorObj;
                    obj[editorOut].pasteHTML = function (newContent) {
                        newEditorObj.setContent(newContent, true);
                    }
                } else if(strInArray(editorType, ['editormd', 'editorMd']) !=-1) {
                    var opt_ = {
                        width: "100%",
                        height: 540,
                        syncScrolling: "single",
                        toolbarIcons: function () {
                            // Or return editormd.toolbarModes[name]; // full, simple, mini
                            // Using "||" set icons align right.

                            return ["bold", "quote", "h3", "del", "link", "list-ul", "list-ol",
                                "code", "preformatted-text", "code-block", "table", "datetime", "hr", "|", "file", "||", "watch", "preview"]
                        },
                        path: "../lib/"
                    };
                    if (editorOpt) {
                        opt_ = $.extend({}, opt_, editorOpt);
                    }
                    editorId = !isUndefined(editorOpt['id']) ? editorOpt['id'] : 'editormd';//实例化以父div的id为准
                    newEditorObj = editormd(editorId, opt_);
                    //支持外部取值editormd
                    if (!obj.hasOwnProperty('value')) {
                        // console.log('editormd set_val');
                        Object.defineProperty(obj, 'value', {
                            get: function () {
                                return newEditorObj.markdownTextarea.val();
                            },
                            set: function (newVal) {
                                newEditorObj.setValue(newVal);
                            }
                        });
                    } else {
                        // console.log('editormd has_val');
                    }
                    obj[editorOut] = newEditorObj;
                    obj[editorOut].pasteHTML = function (newContent) {
                        newEditorObj.setValue(newContent);
                    }
                }
            }, 500);

        };
        //外部设置val
        obj.extend({
            //主动更新数据
            renew: function(options) {
                options = options || {};
                var hasSetData = !isUndefined(options['data']);
                //console.log('renew exitor');
                //console.log(this);
                //console.log(options);
                options['placeholder'] = getOptVal(options, ['place', 'placeholder'], '');
                var editorOut = !isUndefined(options['editorObj']) ? options['editorObj'] : 'editor';
                var editorType = !isUndefined(options['type']) ? options['type'] : 'uEditor';//text|uEditor|umEditor|xheditor|editormd
                if(isUndefined(options['value'])) options['value'] = '';//支持外部取值，textarea需要用到
                var lastContent = options['value'] || '';
                if(strHasKuohao(lastContent)) {
                    //console.log('set false:');
                    valueStrFormatdSuccess = false;
                }
                obj.val(lastContent);
                optionDataFrom(obj, options);
                //console.log(lastContent);
                if(editorType=='text') {
                    options['class'] = isUndefined(options['class']) ? 'form-control' : options['class'] + ' form-control';
                    //支持外部取值
                    if(!obj.hasOwnProperty('value')) {
                        // console.log('set_text val');
                        Object.defineProperty(obj, 'value', {
                            set: function (n) {
                                return obj.val(n);
                            },
                            get: function () {
                                return obj.val();
                            }
                        });
                    } else {
                        // console.log('has_text val');
                    }
                    obj[editorOut] = obj;
                    obj[editorOut].pasteHTML = function (newContent) {
                        obj.val(newContent);
                    }
                }

                //console.log('options');
                //console.log(options);
                strObj.formatAttr(obj, options, 0, hasSetData); //里面找出事件来绑定
            },
            updates: function(dataName, exceptObj) {//数据同步
                exceptObj = exceptObj || [];
                if(setBind && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(this);
                    this.renewVal(getObjData($.trim(setBind)));
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function() {
                var opt = cloneData(obj.sor_opt);
                return global.makeEditor(opt, true);
            }
        });
        obj.renew(options);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        optionGetSet(obj, options);
        objBindVal(obj, options);//数据绑定
        addCloneName(obj);//支持克隆
        //console.log(obj);
        return obj; //makeEditor
    };

    return global;
});

