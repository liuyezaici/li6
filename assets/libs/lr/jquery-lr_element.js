// VERSION 20180823
// LR 2018.8
// 支持字符串的数据三元算法
//$.url.decode('http:%%%'); 实际以下插件中并没有使用 urldecode 此处嵌入只是方便以后的调取
jQuery.url = function() { function l(a) { for(var b = "", c = 0, f = 0, d = 0;c < a.length;) { f = a.charCodeAt(c); if(f < 128) { b += String.fromCharCode(f); c++ }else if(f > 191 && f < 224) { d = a.charCodeAt(c + 1); b += String.fromCharCode((f & 31) << 6 | d & 63); c += 2 }else { d = a.charCodeAt(c + 1); c3 = a.charCodeAt(c + 2); b += String.fromCharCode((f & 15) << 12 | (d & 63) << 6 | c3 & 63); c += 3 } }return b } function m(a, b) { var c = {}, f = {"true":true, "false":false, "null":null}; $.each(a.replace(/\+/g, " ").split("&"), function(d, j) { var e = j.split("="); d = k(e[0]); j = c; var i = 0, g = d.split("]["), h = g.length - 1; if(/\[/.test(g[0]) && /\]$/.test(g[h])) { g[h] = g[h].replace(/\]$/, ""); g = g.shift().split("[").concat(g); h = g.length - 1 }else h = 0; if(e.length === 2) { e = k(e[1]); if(b)e = e && !isNaN(e) ? +e : e === "undefined" ? undefined : f[e] !== undefined ? f[e] : e; if(h)for(;i <= h;i++) { d = g[i] === "" ? j.length : g[i]; j = j[d] = i < h ? j[d] || (g[i + 1] && isNaN(g[i + 1]) ? {} : []) : e }else if($.isArray(c[d]))c[d].push(e); else c[d] = c[d] !== undefined ? [c[d], e] : e }else if(d)c[d] = b ? undefined : "" }); return c } function n(a) { a = a || window.location; var b = ["source", "protocol", "authority", "userInfo", "user", "password", "host", "port", "relative", "path", "directory", "file", "query", "anchor"]; a = /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/.exec(a); for(var c = {}, f = b.length;f--;)c[b[f]] = a[f] || ""; if(c.query)c.params = m(c.query, true); return c } function o(a) { if(a.source)return encodeURI(a.source); var b = []; if(a.protocol)if(a.protocol == "file")b.push("file:///"); else a.protocol == "mailto" ? b.push("mailto:") : b.push(a.protocol + "://"); if(a.authority)b.push(a.authority); else { if(a.userInfo)b.push(a.userInfo + "@"); else if(a.user) { b.push(a.user); a.password && b.push(":" + a.password); b.push("@") }if(a.host) { b.push(a.host); a.port && b.push(":" + a.port) } }if(a.path)b.push(a.path); else { a.directory && b.push(a.directory); a.file && b.push(a.file) }if(a.query)b.push("?" + a.query); else a.params && b.push("?" + $.param(a.params)); a.anchor && b.push("#" + a.anchor); return b.join("") } function p(a) { return encodeURIComponent(a) } function k(a) { a = a || window.location.toString(); return l(unescape(a.replace(/\+/g, " "))) } return{encode:p, decode:k, parse:n, build:o} }();

// ajax上传文件插件
jQuery.extend({handleError:function(s,xhr,status,e){if(s.error){s.error.call(s.context||s,xhr,status,e)}if(s.global){(s.context?jQuery(s.context):jQuery.event).trigger("ajaxError",[xhr,s,e])}},createUploadIframe:function(frameId,uri){if(window.ActiveXObject){if(jQuery.browser.version=="9.0"||jQuery.browser.version=="10.0"){var io=document.createElement("iframe");io.id=frameId;io.name=frameId}else{if(jQuery.browser.version=="6.0"||jQuery.browser.version=="7.0"||jQuery.browser.version=="8.0"){var io=document.createElement('<iframe id="'+frameId+'" name="'+frameId+'" />');if(typeof uri=="boolean"){io.src="javascript:false"}else{if(typeof uri=="string"){io.src=uri}}}}}else{var io=document.createElement("iframe");io.id=frameId;io.name=frameId}io.style.position="absolute";io.style.top="-1000px";io.style.left="-1000px";document.body.appendChild(io);return io},ajaxFileUpload:function(s){s=jQuery.extend({},jQuery.ajaxSettings,s);var id=new Date().getTime();var uploadForm={};var tmpLoading=null;var frameId="jUploadFrame"+id;var formId="jUploadForm"+id;var postData=s.data||null;var loadingUrl=s.loadingUrl||"";if(loadingUrl){tmpLoading=$('<img class="loading_gif" src="'+loadingUrl+'">')}uploadForm=$('<form  action="'+s.url+'" target="'+frameId+'" method="POST" '+'name="'+formId+'" style="position: absolute; top: -1000px; left: -1000px;" id="'+formId+'" enctype="multipart/form-data"></form>');if(tmpLoading){s.fileInput.after(tmpLoading)}var inputPrev=s.fileInput.prev();var inputParent=s.fileInput.parent();$(document.body).append(s.fileInput);s.fileInput.wrap(uploadForm);uploadForm=$("#"+formId);if(postData){var tmpInput="";$.each(postData,function(key_,val_){tmpInput=$('<input type="hidden" name="'+key_+'" value="'+val_+'" />');uploadForm.append(tmpInput)})}jQuery.createUploadIframe(frameId,s.secureuri);if(s.global&&!jQuery.active++){jQuery.event.trigger("ajaxStart")}var requestDone=false;var xml={};if(s.global){jQuery.event.trigger("ajaxSend",[xml,s])}var uploadCallback=function(isTimeout){var io=document.getElementById(frameId);try{if(io.contentWindow){xml.responseText=io.contentWindow.document.body?io.contentWindow.document.body.innerHTML:null;xml.responseXML=io.contentWindow.document.XMLDocument?io.contentWindow.document.XMLDocument:io.contentWindow.document}else{if(io.contentDocument){xml.responseText=io.contentDocument.document.body?io.contentDocument.document.body.innerHTML:null;xml.responseXML=io.contentDocument.document.XMLDocument?io.contentDocument.document.XMLDocument:io.contentDocument.document}}}catch(e){jQuery.handleError(s,xml,null,e)}var callFinish=false;if(xml||isTimeout=="timeout"){requestDone=true;var status;try{status=isTimeout!="timeout"?"success":"error";if(status!="error"){var data=jQuery.uploadHttpData(xml,s.dataType);if(s.finish){s.finish(data,status)}else{console.log("!s.finish");console.log(s)}if(s.global){jQuery.event.trigger("ajaxSuccess",[xml,s])}}else{jQuery.handleError(s,xml,status)}}catch(e){status="error";jQuery.handleError(s,xml,status,e)}jQuery(io).unbind();setTimeout(function(){try{$(io).remove();if(tmpLoading){tmpLoading.remove()}if(inputPrev.length>0){inputPrev.after(s.fileInput)}else{inputParent.append(s.fileInput)}$(uploadForm).remove()}catch(e){jQuery.handleError(s,xml,null,e)}},100);xml=null}};if(s.timeout>0){setTimeout(function(){if(!requestDone){uploadCallback("timeout")}},s.timeout)}try{$(uploadForm).submit()}catch(e){jQuery.handleError(s,xml,null,e)}if(window.attachEvent){document.getElementById(frameId).attachEvent("onload",uploadCallback)}else{document.getElementById(frameId).addEventListener("load",uploadCallback,false)}return{abort:function(){}}},uploadHttpData:function(r,type){var data=!type;data=type=="xml"||data?r.responseXML:r.responseText;if(type=="script"){jQuery.globalEval(data)}if(type=="json"){var data=r.responseText;var reg_=/^<pre.*?>(.*?)<\/pre>$/i;if(reg_.test(data)){var am=reg_.exec(data);var data=(am)?am[1]:"";eval("data = "+data)}else{eval("data = "+data)}}if(type=="html"){jQuery("<div>").html(data).evalScripts()}return data}});

//md5
    (function(u){var k=function(a,c){var h,g,k,m;k=a&2147483648;m=c&2147483648;h=a&1073741824;g=c&1073741824;a=(a&1073741823)+(c&1073741823);return h&g?a^2147483648^k^m:h|g?a&1073741824?a^3221225472^k^m:a^1073741824^k^m:a^k^m},l=function(a,c,h,g,l,m,b){a=k(a,k(k(c&h|~c&g,l),b));return k(a<<m|a>>>32-m,c)},n=function(a,c,h,g,l,m,b){a=k(a,k(k(c&g|h&~g,l),b));return k(a<<m|a>>>32-m,c)},p=function(a,c,h,g,l,m,b){a=k(a,k(k(c^h^g,l),b));return k(a<<m|a>>>32-m,c)},q=function(a,c,h,g,l,m,b){a=k(a,k(k(h^(c|~g), l),b));return k(a<<m|a>>>32-m,c)},t=function(a){var c="",h,g;for(g=0;3>=g;g++)h=a>>>8*g&255,h="0"+h.toString(16),c+=h.substr(h.length-2,2);return c};u.extend({md5:function(a){var c,h,g,r,m,b,d,e,f;a=a.replace(/\x0d\x0a/g,"\n");c="";for(h=0;h<a.length;h++)g=a.charCodeAt(h),128>g?c+=String.fromCharCode(g):(127<g&&2048>g?c+=String.fromCharCode(g>>6|192):(c+=String.fromCharCode(g>>12|224),c+=String.fromCharCode(g>>6&63|128)),c+=String.fromCharCode(g&63|128));h=c.length;a=h+8;r=16*((a-a%64)/64+1);a=Array(r-1);for(b=0;b<h;)g=(b-b%4)/4,m=b%4*8,a[g]|=c.charCodeAt(b)<<m,b++;g=(b-b%4)/4;a[g]|=128<<b%4*8;a[r-2]=h<<3;a[r-1]=h>>>29;b=1732584193;d=4023233417;e=2562383102;f=271733878;for(c=0;c<a.length;c+=16)h=b,g=d,r=e,m=f,b=l(b,d,e,f,a[c+0],7,3614090360),f=l(f,b,d,e,a[c+1],12,3905402710),e=l(e,f,b,d,a[c+2],17,606105819),d=l(d,e,f,b,a[c+3],22,3250441966),b=l(b,d,e,f,a[c+4],7,4118548399),f=l(f,b,d,e,a[c+5],12,1200080426),e=l(e,f,b,d,a[c+6],17,2821735955),d=l(d,e,f,b,a[c+7],22,4249261313),b=l(b,d,e,f,a[c+8],7,1770035416),f=l(f,b,d,e,a[c+9],12,2336552879),e=l(e,f,b,d,a[c+10],17,4294925233),d=l(d,e,f,b,a[c+11],22,2304563134),b=l(b,d,e,f,a[c+12],7,1804603682),f=l(f,b,d,e,a[c+13],12,4254626195),e=l(e,f,b,d,a[c+14],17,2792965006),d=l(d,e,f,b,a[c+15],22,1236535329),b=n(b,d,e,f,a[c+1],5,4129170786),f=n(f,b,d,e,a[c+6],9,3225465664),e=n(e,f,b,d,a[c+11],14,643717713),d=n(d,e,f,b,a[c+0],20,3921069994),b=n(b,d,e,f,a[c+5],5,3593408605),f=n(f,b,d,e,a[c+10],9,38016083),e=n(e,f,b,d,a[c+15],14,3634488961),d=n(d,e,f,b,a[c+4],20,3889429448),b=n(b,d,e,f,a[c+9],5,568446438),f=n(f,b,d,e,a[c+14],9,3275163606),e=n(e,f,b,d,a[c+3],14,4107603335),d=n(d,e,f,b,a[c+8],20,1163531501),b=n(b,d,e,f,a[c+13],5,2850285829),f=n(f,b,d,e,a[c+2],9,4243563512),e=n(e,f,b,d,a[c+7],14,1735328473),d=n(d,e,f,b,a[c+12],20,2368359562),b=p(b,d,e,f,a[c+5],4,4294588738),f=p(f,b,d,e,a[c+8],11,2272392833),e=p(e,f,b,d,a[c+11],16,1839030562),d=p(d,e,f,b,a[c+14],23,4259657740),b=p(b,d,e,f,a[c+1],4,2763975236),f=p(f,b,d,e,a[c+4],11,1272893353),e=p(e,f,b,d,a[c+7],16,4139469664),d=p(d,e,f,b,a[c+10],23,3200236656),b=p(b,d,e,f,a[c+13],4,681279174),f=p(f,b,d,e,a[c+0],11,3936430074),e=p(e,f,b,d,a[c+3],16,3572445317),d=p(d,e,f,b,a[c+6],23,76029189),b=p(b,d,e,f,a[c+9],4,3654602809),f=p(f,b,d,e,a[c+12],11,3873151461),e=p(e,f,b,d,a[c+15],16,530742520),d=p(d,e,f,b,a[c+2],23,3299628645),b=q(b,d,e,f,a[c+0],6,4096336452),f=q(f,b,d,e,a[c+7],10,1126891415),e=q(e,f,b,d,a[c+14],15,2878612391),d=q(d,e,f,b,a[c+5],21,4237533241),b=q(b,d,e,f,a[c+12],6,1700485571),f=q(f,b,d,e,a[c+3],10,2399980690),e=q(e,f,b,d,a[c+10],15,4293915773),d=q(d,e,f,b,a[c+1],21,2240044497),b=q(b,d,e,f,a[c+8],6,1873313359),f=q(f,b,d,e,a[c+15],10,4264355552),e=q(e,f,b,d,a[c+6],15,2734768916),d=q(d,e,f,b,a[c+13],21,1309151649),b=q(b,d,e,f,a[c+4],6,4149444226),f=q(f,b,d,e,a[c+11],10,3174756917),e=q(e,f,b,d,a[c+2],15,718787259),d=q(d,e,f,b,a[c+9],21,3951481745),b=k(b,h),d=k(d,g),e=k(e,r),f=k(f,m);return(t(b)+t(d)+t(e)+t(f)).toLowerCase()}})})(jQuery);
//js base64
    (function($){var b64="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",a256="",r64=[256],r256=[256],i=0;var UTF8={encode:function(strUni){var strUtf=strUni.replace(/[\u0080-\u07ff]/g,function(c){var cc=c.charCodeAt(0);return String.fromCharCode(192|cc>>6,128|cc&63)}).replace(/[\u0800-\uffff]/g,function(c){var cc=c.charCodeAt(0);return String.fromCharCode(224|cc>>12,128|cc>>6&63,128|cc&63)});return strUtf},decode:function(strUtf){var strUni=strUtf.replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g,function(c){var cc=((c.charCodeAt(0)&15)<<12)|((c.charCodeAt(1)&63)<<6)|(c.charCodeAt(2)&63);return String.fromCharCode(cc)}).replace(/[\u00c0-\u00df][\u0080-\u00bf]/g,function(c){var cc=(c.charCodeAt(0)&31)<<6|c.charCodeAt(1)&63;return String.fromCharCode(cc)});return strUni}};while(i<256){var c=String.fromCharCode(i);a256+=c;r256[i]=i;r64[i]=b64.indexOf(c);++i}function code(s,discard,alpha,beta,w1,w2){s=String(s);var buffer=0,i=0,length=s.length,result="",bitsInBuffer=0;while(i<length){var c=s.charCodeAt(i);c=c<256?alpha[c]:-1;buffer=(buffer<<w1)+c;bitsInBuffer+=w1;while(bitsInBuffer>=w2){bitsInBuffer-=w2;var tmp=buffer>>bitsInBuffer;result+=beta.charAt(tmp);buffer^=tmp<<bitsInBuffer}++i}if(!discard&&bitsInBuffer>0){result+=beta.charAt(buffer<<(w2-bitsInBuffer))}return result}var Plugin=$.base64=function(dir,input,encode){return input?Plugin[dir](input,encode):dir?null:this};Plugin.btoa=Plugin.encode=function(plain,utf8encode){plain=Plugin.raw===false||Plugin.utf8encode||utf8encode?UTF8.encode(plain):plain;plain=code(plain,false,r256,b64,8,6);return plain+"====".slice((plain.length%4)||4)};Plugin.atob=Plugin.decode=function(coded,utf8decode){coded=String(coded).split("=");var i=coded.length;do{--i;coded[i]=code(coded[i],true,r64,a256,6,8)}while(i>0);coded=coded.join("");return Plugin.raw===false||Plugin.utf8decode||utf8decode?UTF8.decode(coded):coded}}(jQuery));

    function isAbcJhk( value ) {
        return /^{\s*[\.a-zA-Z0-9_]+\s*}$/.test( value );
    }
    function isNumber( value ) {
        return /^[\+\-0-9.]+$/.test( value );
    }
    function isObj( value ) {
        return typeof value == 'object';
    }
    function isString( value ) {
        return typeof value == 'string';
    }
    function isStrOrNumber( value ) {
        return isString(value) || isNumber(value);
    }
    function trim(str, node) {
        if(isBoolean(str)) return str;
        str = str || " ";
        node = node || " ";
        if(isObj(str)) return str;
        if(isNumber(str)) return str;
        var len = node.length;
        if (str.substr(0, len) == node) str = str.substr(len);
        if (str.substr(str.length - len, len) == node) str = str.substr(0, str.length - len);
        return str;
    }
    function toNumber (val) {
        var n = parseFloat(val);
        return isNaN(n) ? val : n
    }
    function isBoolean (s) {
        return typeof s =='boolean';
    }
    function htmlDecode(str){
        if(isObj(str)) return str;
        var s = "";
        if(!str || str.length == 0) return "";
        if(isBoolean(str)) return str;
        if(isNumber(str)) return str;
        if(!str.replace) {
            console.log('str no suppose replace:');
            console.log(str);
        }
        s = str.replace(/&amp;/g,"&");
        s = s.replace(/&lt;/g,"<");
        s = s.replace(/&gt;/g,">");
        s = s.replace(/&nbsp;/g," ");
        s = s.replace(/&#39;/g,"\'");
        s = s.replace(/&quot;/g,"\"");
        return s;
    }
    //数组去重
    function uniqueArray(array_) {
        array_ = array_ || [];
        //console.log(array_);
        return jQuery.unique(array_.sort());
    }
    //转义正则敏感符 str_.replace(new RegExp(match,"gm"), matchVal); 否则当字符串match中含有以下符号时，无法替换
    function regCodeAddGang(str) {
    if(!str) return '';
        str = str.replace(/\?/g, '\\\?');
        str = str.replace(/\+/g, '\\\+');
        str = str.replace(/\^/g, '\\\^');
        str = str.replace(/\(/g, '\\\(');
        str = str.replace(/\)/g, '\\\)');
        str = str.replace(/\*/g, '\\\*');
        str = str.replace(/\$/g, '\\\$');
        str = str.replace(/\[/g, '\\\[');
        str = str.replace(/\]/g, '\\\]');
        str = str.replace(/\{/g, '\\\{');
        str = str.replace(/\}/g, '\\\}');
        str = str.replace(/\|/g, '\\\|');
        str = str.replace(/\=/g, '\\\=');
        str = str.replace(/\\\\/g, '\\\\\\');
        return str;
    }

(function (global, $) {
    //定义是否绑定过文档点击事件
    window.bindDocumentHideMenuEven = false;
    var menu_pub_class_name = 'my_diy_menu'; //全部的option菜单样式 用于统一鼠标点击body自动隐藏
    //对象加zindex
    var menuZindexClass = 'menu_add_zindex';
    var parentObjKey = 'parent';//给所有对象加一个父亲 设置键名
    var optionCallCloneKey = 'is_clone';//当前对象键名：对象被更新时是否需要重新克隆
    //哪些参数的修改无需触发对象更新
    var objValObjKey = 'obj_val_objs';//当前对象包含的obj  每个人对象创建成功后，其val都会保存当前值或dom对象 字符串形式的value除非
    var objLastValKey = 'obj_last_val_objs';//当前对象之前保存的value包含的obj或字符串
    var tableWithDataTrKey = 'trs_data';//表格内带有data循环的tr的下标
    var tableNoDataTrKey = 'trs_nodata';//表格内不带有data循环的tr的下标
    var ignoreBindValsKeyname = 'ignore_bind_str';//忽略绑定的字符串 如select菜单的li其data-value和value都需要被强制忽略绑定全局变量的
    var objAttrHasKh = 'obj_opt_has_kuohao';//obj的属性包含有{} 则可能绑定全局变量
    var objHasKhAttrs = 'obj_has_kuohao_attrs';//obj的包含有{}的属性
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入
    // 不允许append的对象: input,img,textArea,select,radio,switch
    // 允许append的对象: span p div 并且value是字符串
    var evenTags = 'mouseover||mouseenter||hover||hover_extend/mouseout||mouseleave||mouseleave_extend/click||click_extend/dblclick||dbclick/paste/blur||blur_extend/change||change_extend/keyup/keyup_extend/input propertychange||input propertychange_extend/submit||submit_extend'.split('/');


    //检测是否事件
    function attrIsEven(attr) {
        var isEven = false;
        evenTags.forEach(function(evenNames) {
            var evenNameArray = evenNames.split('||');
            evenNameArray.forEach(function(evenName) {
                evenName = $.trim(evenName);
                if(evenName == attr) {
                    isEven = true;
                    return false;
                }
            });
        });
        return isEven;
    }
    //拷贝options所有事件
    function copyEvens(opt, newOpt) {
        $.each(opt, function (k, v) {
            if(attrIsEven(k)) newOpt[k] = opt[k];
        });
        return newOpt;
    }

    //拷贝options原本未解析的属性
    function getSourceOpt(opt) {
        $.each(opt, function (k, v) {
            if(!isUndefined(opt['source_'+ k])) {
                opt[k] = opt['source_'+ k];
            }
        });
        return opt;
    }

    //移除options所有事件
    function removeAllEven(opt) {
        $.each(opt, function (k, v) {
            if(attrIsEven(k)) delete opt[k];
        });
    }

    //去掉引号里的内容
    function clearYinhao(str_) {
        if(isObj(str_)) return str_;
        if(isBoolean(str_)) return str_;
        if(isNumber(str_)) return str_;
        str_ = str_.replace(/'([^']*)'/g, '\'\'');
        return str_.replace(/"([^"]*)"/g, '""');
    }
    //定义解析符
    var kuohaoNewhasl = '[cd_kuohao_l]'; //(
    var kuohaoNewhasr = '[cd_kuohao_r]'; //)
    var yinhaoNewhasd = '[cd_yinhaod]';//单引号
    var yinhaoNewhass = '[cd_yinhaos]';//双引号
    var yinhaofNewhasd = '[cd_tmpZy_Yinhd]';//\\单引号
    var yinhaofNewhass = '[cd_tmpZy_Yinhs]';//\\双引号
    //替换新解析到的内容 解析掉\\" 和 " 和 ( 和 )
    function encodeNewHtml(str_) {
        if(isObj(str_)) return str_;
        if(isBoolean(str_)) return str_;
        if(isNumber(str_)) return str_;
        str_ = str_.replace(/\\\"/g, yinhaofNewhass);
        str_ = str_.replace(/\\\'/g, yinhaofNewhasd);
        str_ = str_.replace(/'/g, yinhaoNewhasd);
        str_ = str_.replace(/"/g, yinhaoNewhass);
        str_ = str_.replace(/\(/g, kuohaoNewhasl);//()在引号里不会加密url 会混淆 所以要作转译
        str_ = str_.replace(/\)/g, kuohaoNewhasr);//()在引号里不会加密url 会混淆 所以要作转译
        return str_;
    }
    //替换原解析内容中的反引号
    function yinhaogTH(str_) {
        if(isObj(str_)) return str_;
        if(isBoolean(str_)) return str_;
        if(isNumber(str_)) return str_;
        str_ = str_.replace(/\\\'/g, yinhaofNewhasd);
        str_ = str_.replace(/\\\"/g, yinhaofNewhass);
        return str_;
    }
    //加密的字符恢复
    function decodeNewHtml(str_) {
        if(isObj(str_)) return str_;
        if(isBoolean(str_)) return str_;
        if(isNumber(str_)) return str_;
        str_ = str_.replace(RegExp(regCodeAddGang(yinhaoNewhasd), 'g'), "'");
        str_ = str_.replace(RegExp(regCodeAddGang(yinhaoNewhass), 'g'), '"');
        str_ = str_.replace(RegExp(regCodeAddGang(yinhaofNewhass), 'g'), '\\\"');
        str_ = str_.replace(RegExp(regCodeAddGang(yinhaofNewhasd), 'g'), "\\\'");
        str_ = str_.replace(RegExp(regCodeAddGang(kuohaoNewhasl), 'g'), "(");
        str_ = str_.replace(RegExp(regCodeAddGang(kuohaoNewhasr), 'g'), ')');
        return str_;
    }
    //克隆的类型
    function getCloneType(newData) {
        if($.isArray(newData)) {
            return [];
        } else {
            return {};
        }
    }
    //克隆data
    function cloneData(newData, oldData) {
        if(!isUndefined(oldData)) {
            return $.extend(getCloneType(newData), oldData, newData);
        } else {
            return $.extend(getCloneType(newData), newData);
        }
    }

    //哪些参数的修改无需触发对象更新
    var optionsChangeNoRenew = ['name', optionCallCloneKey];
    //缩写原生的判断对象是否存在
    function isUndefined(variable) {return typeof variable == 'undefined' ? true : false;}
    //判断对象是不是插件定义的对象
    function isOurObj(obj_) {
        if(!obj_) return false;
        return obj_.hasOwnProperty('options');
    }
    //判断对象为空
    function objIsNull(obj) {
        if(!obj) return false;
        return Object.keys(obj).length == 0;
    }
    //检索数组 文本类型：数字和字符都支持 不区分1和'1'
    function strInArray(str, array_) {
        var exist_ = -1;
        $.each(array_, function (n, item_) {
            if(item_ == str) {
                exist_ = n;
                return false; //break
            }
        });
        return exist_;
    }
    //检索是否包含以下任意一个字符串
    function hasStrs(str, array_) {
        var exist_ = -1;
        $.each(array_, function (n, item_) {
            if(str.indexOf(item_) !=-1) {
                exist_ = n;
                return false; //break
            }
        });
        return exist_;
    }
    //判断数据为空
    function hasData(obj) {
        if(obj == null || !obj) return false;
        if(typeof obj != 'object') return false;
        if(Array.isArray(obj)) return obj.length > 0;
        return !objIsNull(obj);
    }
    //获取字符串里的尖括号变量 结果为数组,单元素左右无{}
    function getJhksAbc(str_) {
        if(!str_) return '';
        var jkhMatchs = str_.match(/{([a-zA-Z_]+[a-zA-Z_\d.]*)}/g);
        if(jkhMatchs) {
            var backMatch = [];
            jkhMatchs.forEach(function (match_) {
                backMatch.push(match_.replace(/\{|\}/g, ''));
            });
            return backMatch;
        }
        return '';
    }

    // > 2 要转为 0>2 //运算公式 m>n
    //图片去缓存
    function noCacheImg(url) {
        if(!url) return '';
        if(url.indexOf('?') != -1) {
            if(url.indexOf('?lr_radom') !=-1) {
                return url += url.split('?lr_radom')[0] + '?lr_radom='+ global.makeRadom(22);
            }
            if(url.indexOf('&lr_radom') !=-1) {
                return url += url.split('&lr_radom')[0] + '&lr_radom='+ global.makeRadom(22);
            }
            return url += '&lr_radom='+ global.makeRadom(22);
        }
        return url += '?r_='+ global.makeRadom(22);
    }
    //检测是否有非法运算字符 > 123 或 d =='ddd'

    // 异常()开头
    var regErrNullGongshiSet1 = /^\(\s*\)/;
    //  纯 (asdasdasd) 或 = (abc) 或 (abc) >
    var regErrNullGongshiSet2 = /^\(([a-zA-Z_]+[a-zA-Z_\d.]*)\)$|([>|<|==|!|&|\|])\s*\(([a-zA-Z_]+[a-zA-Z_\d.]*)\)|\(([a-zA-Z_]+[a-zA-Z_\d.]*)\)\s*([>|<|==|!|&|\|])/;
    // 异常格式 一个或多个!  {!} {!!}
    var regErrNullGongshiSet3 = /^!+$/;
    // !&|<>=开头或结尾
    var regErrFrontEnd = /^\s*(=|\|\||&|<|>|!)|(=|\|\||&|<|>)\s*$/;
    //  &&||   中间有空
    var regErrBoolenGongshiSetMiddle = /(&&|\|\|)\s*(&&|\|\|)/; //
    //  && 或 ||的 左边或右边 直接跟了比较符 如：||> ||< ||! ||=
    var regErrNullGongshiSet4 = /([\&\&|\|\|]+)\s*(>|<|=|!)|(>|<|=|!)\s*([\&\&|\|\|]+)/;
    // () 的里面直接跟了比较符 ><!=&| ) 注意：)外面是可以跟|&的
    var regErrNullGongshiSet5 = /\(\s*([>|<|=|!|&|\|])|([>|<|=|!|&|\|])\s*\)/;
    //字母和数字直接跟比较符号 注意 class="" 注意的不能过滤 所以要考虑是字母开头 或 前面跟&&||
    var regErrNullGongshiSet6 = /^([a-zA-Z_]+[a-zA-Z_\d.]*)([>|<|==|!|\||&])| ([\&\&|\|\|]+)\s*([a-zA-Z_]+[a-zA-Z_\d.]*)\s*([>|<|==|!|\||&])|([>|<|==|!|\||&])\s*([a-zA-Z_]+[a-zA-Z_\d.]*)\s*([\&\&|\|\|]+)|([>|<|==|!|\||&])\s*([a-zA-Z_]+[a-zA-Z_\d.]*)\s*([>|<|==|!|\||&])|([>|<|=|!])([a-zA-Z_]+[a-zA-Z_\d.]*)$/;
    //运算符跟空的()
    var regErrNullGongshiSet7 = /([>|<|=|!|&|\|])\s*\(\s*\)|\(\s*\)\s*([>|<|=|!|&|\|])/;

    //与非运算符
    var yufeiReg = /^\s*\(*(('|")([^'"]*)('|")|\s*([\+|\-|\*|\/]*)\s*([0-9.]+)|\s*(true|false)\s*)\)*\s*((\s*\(*\s*([\+\-*\/%]+)\s*\(*\s*([0-9.]+)\s*\)*\s*\)*)|(\s*([\+|\&\&|\|\|]+)\s*\(*('|")([^'"]*)('|")\)*\s*))*(\s*([>|<|!=|==]+)\s*\(*(('|")([^'"]*)('|")|([0-9.]+))\)*\s*((\s*\(*\s*([\+\-*\/%]+)\s*\(*\s*([0-9.]+)\s*\)*\s*\)*)|(\s*\+\s*\(*('|")([^'"]*)('|")\)*\s*))*)*(\s*([\&\&|\|\|]+)\s*\(*(('|")([^'"]*)('|")|\s*([\+|\-|\*|\/]*)\s*([0-9.]+)|\s*(true|false)\s*)\)*\s*((\s*\(*\s*([\+\-*\/%]+)\s*\(*\s*([0-9.]+)\s*\)*\s*\)*)|(\s*([\+|\&\&|\|\|]+)\s*\(*('|")([^'"]*)('|")\)*\s*))*(\s*([>|<|!=|==]+)\s*\(*(('|")([^'"]*)('|")|([0-9.]+))\)*\s*((\s*\(*\s*([\+\-*\/%]+)\s*\(*\s*([0-9.]+)\s*\)*\s*\)*)|(\s*\+\s*\(*('|")([^'"]*)('|")\)*\s*))*)*)*$/;

    //3元运算符判断
    var regYufa3YuanSet = /^\s*\(*(('|")([^'"]*)('|")|\s*([\+|\-|\*|\/]*)\s*([0-9.]+)|\s*(true|false)\s*)\)*\s*((\s*\(*\s*([\+\-*\/%]+)\s*\(*\s*([0-9.]+)\s*\)*\s*\)*)|(\s*([\+|\&\&|\|\|]+)\s*\(*('|")([^'"]*)('|")\)*\s*))*(\s*([>|<|!|=]+)\s*\(*(('|")([^'"]*)('|")|([0-9.]+))\)*\s*((\s*\(*\s*([\+\-*\/%]+)\s*\(*\s*([0-9.]+)\s*\)*\s*\)*)|(\s*\+\s*\(*('|")([^'"]*)('|")\)*\s*))*)*\?\s*(('|")([^'"]*)('|")|([0-9.]+))\s*:\s*(('|")([^'"]*)('|")|([0-9.]+))/;
    //3元运算符里的引号
    //{"aaaa" ? "<i class="fa fa-cloud-download"></i> btn1":"<i class="fa fa-times"></i> btn2"}
    //: {"aaaa" ? "<i class="fa fa-cloud-download"></i> btn1":"<i class="fa fa-times"></i> btn2"}
    function replace3yuanYinhao(str) {
        if(regYufa3YuanSet.test(str)) {
            var matchs = str.match(regYufa3YuanSet);
            matchs.splice(0, 1);//去掉原文
            var newArray = [];
            $.each(matchs, function(n, match_) {
                if(n==0) {//continue
                    newArray[n] = match_;
                    return;
                }
                var tmpMatch = trim(match_, ' ');
                tmpMatch = trim(tmpMatch, '"');
                tmpMatch = trim(tmpMatch, "'");
                //console.log('match_:'+n);
                //console.log(match_);
                newArray[n] = tmpMatch.replace(/"/g, '\\"');
            });
            //console.log('newArray:');
            //console.log(newArray);
            return newArray[0] +' ? "'+ newArray[1] +'" : "'+ newArray[2] +'"' ;
        };
        return str;
    }


    //给将对象需要同步的属性写入同步变量中
    var objBindAttrsName = 'bind_attrs';
    function addObjAttrToBindVal(obj_, attrName, valueKey) {
        // console.log('attrName  :'+attrName +','+ valueKey );
        if(attrName == ignoreBindValsKeyname) return;//定义用来忽略字符串的属性 不能被误判加入绑定
        var ignoreThisAttrName = false;

        if(isUndefined(obj_[objBindAttrsName])) obj_[objBindAttrsName]=  {};
        var objBindData = obj_[objBindAttrsName];
        var lastValAttrs = isUndefined(objBindData[valueKey]) ? [] : objBindData[valueKey];
        if($.inArray(attrName, lastValAttrs) ==-1) {
            lastValAttrs.push(attrName);
            obj_[objBindAttrsName][valueKey] = lastValAttrs;
            objAddListener(obj_, valueKey, ''); //当前对象加入到监控中
        }
    }


    //字符串方法 集合
    var strObj =  {
        runYufaReg :  /^{*([a-zA-Z_]+[a-zA-Z_\d.]*)\[run\]\((.*)\)\s*\;*\s*\}*$/, //一定要英文或{开头
        //运行系统语法
        runYufa : function(str_) {
            //console.log('runYufa Yufa  :'+ str_ );
            str_ = $.trim(str_);
            str_ = trim(str_,'{');
            str_ = trim(str_,'}');
            str_ = str_.replace(RegExp(this.runYufaReg, 'g'), '$1($2)');//去掉[run]标记
            //console.log('runYufa Yufa  :'+ str_ );
            //run之前要把引号里的内容解析不然会误判，
            // 如： {11 +"[url_hash_code]%E7%9A%84bb" == "[url_hash_code]11%E7%9A%84bb"?"[url_hash_code]11_bb":"[url_hash_code]22_bb" }
            str_ = this.urlDecodeLR(str_);
            //console.log('result  :'+ str_ );
            str_ = eval(str_);
            //console.log('result  :'+ str_ );
            return str_;
        },
        //检测{}内的字符串是否有运算语法
        hasYufa: function(str) {
            str = str || '';
            if(!str) return false;
            //必须以{开头 和 }结尾 才算语法
            if(!/^\s*{(.+)}\s*$/.test(str)) {
                return false;
            }
            str = $.trim(str);
            str = trim(str, '{');
            str = trim(str, '}');
            //console.log('check has Yufa  :'+ str);
            // 如果str里含有{abc}则不能算是正常的语法
            if(/([^{]*)\{([a-zA-Z_]+[a-zA-Z_\d.]*)\}([^\}]*)/.test(str)) {
                //console.log('yinhao in has {ab_c}  :'+ str);
                return false;
            } else {
                //console.log(str);
            }
            //console.log('check has Yufa  :'+ str);

            //!"" 这样的 算是布尔语法
            if(/\s*!\s*"\s*"\s*/.test(str)) {
                //console.log('yinhao in has { or }  :'+ str);
                return 'gth';
            }
            //("") 这样的 算是布尔语法
            if(/^\s*\(*\s*"\s*"\s*\)*\s*$/.test(str)) {
                //console.log('yinhao in has { or }  :'+ str);
                return 'emp_kh';
            }
            //{""&&""||""} 这样的 算是与非语法
            if(yufeiReg.test(str)) {
                //console.log('yinhao in has { or }  :'+ str);
                return 'yufei';
            }

            //console.log('check has Yufa  :'+ str);
            //要先将引号清空
            str = clearYinhao(str);
            // 如去掉引号里后，如果含有{或}则不能算是正常的语法
            if(/({|})/.test(str)) {
                //console.log('yinhao in has { or }  :'+ str);
                return false;
            }
            if(hasStrs(str, ['inArray', 'indexOf', 'typeof']) !=-1) {
                return 'hasInarray'
            }
            if(hasStrs(str, ['eval']) !=-1) {
                return 'has_eval'
            }

            //四则混合运算解析 (数字后面必须跟至少一个+-*/的运算，否则就是纯数字 不能算为运算。)
            //console.log('sizehunheRegMul     :'+ str );
            var sizehunheRegMul = /^\s*\(*(('|")([^'"]*)('|")|([\+|\-|\*|\/]*)\s*([0-9.]+))\)*\s*((\s*([\+\-*\/%]+)\s*\(*([0-9.]+)\s*\)*\s*)|(\s*\+\s*\(*('|")([^'"]*)('|")\)*\s*))+/;
            if(sizehunheRegMul.test(str)) return 'sizehunheRegMul';

            // 3元运算符
            if (regYufa3YuanSet.test(str))  {
                var matchArray = regYufa3YuanSet.exec(str);
                var match1 = matchArray[1];//''? 问号左边部分 必须是'或"或123或!"开头
                if(!/^!*'|^!*"|^\s*([0-9.]+)|^\s*(true)|^\s*(false)/g.test(match1)) {
                    //console.log('not match1:'+ match1);
                    return false;
                }
                return 'regYufa3YuanSet';
            }
            if (this.runYufaReg.test(str)) {
                //console.log('check has Yufa  runReg');
                return 'runReg';
            }

            //匹配字符串拼接后对比： "a" + "a" +2 !== "aa" +"dd" +2 -123/2 + 3*5
            //匹配字符串拼接后对比： "a" + "a" +2 && "aa" +"dd" +2 -123/2 + 3*5
            //匹配字符串拼接后对比： "a" + "a" +2 || "aa" +"dd" +2 -123/2 + 3*5
            //对比运算符
            var compareRegMul = /^\s*\(*(('|")([^'"]*)('|")|([\+|\-|\*|\/]*)\s*([0-9.]+))\)*\s*(((\s*\(*([\+\-*\/%]+)\s*\(*([0-9.]+)\s*\)*)|(\s*\+\s*\(*('|")([^'"]*)('|")\)*))*)\s*(([>|<|!|=]+)|(\&\&|\|\|))\s*\(*(('|")([^'"]*)('|")|([0-9.]+))\)*\s*(((\s*\(*([\+\-*\/%]+)\s*\(*([0-9.]+)\s*\)*)|(\s*\+\s*\(*('|")([^'"]*)('|")\)*))*)/;
            if(compareRegMul.test(str)) return 'compareYufaMul';

            //console.log('check has Yufa end false :'+ str);
            return false;
        },

        //自带的url加密标识 防止无法区分是否需要解密
        urlHashCode : '[url_hash_code]',
        urlencodeLR : function(s_) {
            if(isBoolean(s_)) return s_;
            if(isNumber(s_)) return s_;
            if(!s_) return s_;
            return this.urlHashCode + $.url.encode(s_);
        },
        //解密url
        urlDecodeLR : function(s_) {
            if(isBoolean(s_)) return s_;
            if(isNumber(s_)) return s_;
            if(!s_) return s_;
            //console.log('urldecode s_:'+ s_);
            if(s_.indexOf(this.urlHashCode) ==-1) return s_;
            var urlReg = this.urlHashCode.replace(/\[/, '\\[');
            urlReg = urlReg.replace(/\]/, '\\]');
            var reg_ = urlReg + '([^"\']+)';

            s_ = s_.replace(RegExp(reg_, 'g'), function (tmpMatch, tmpMatch1, tmpMatch2) {
                var jkhYinhRegOur1 = '(\'|")'+  regCodeAddGang(strObj.urlHashCode) + tmpMatch1 + '(\'|")';
                // "[url_hash_code] 其他中文 {a}  " 在url里不需要给{a}加引号
                var newHtml = tmpMatch1 ? $.url.decode(tmpMatch1) : '';
                newHtml = decodeNewHtml(newHtml);
                //判断当前解析的url内容是否在引号里面 如在则需要在引号前\\
                if(RegExp(jkhYinhRegOur1, 'g').test(s_)) {
                    //console.log('in our yin :'+ s_);
                    //console.log('in yin tmpMatch :'+ tmpMatch);
                    newHtml = newHtml.replace(/\\\"/g, '[tmp_._yh]');
                    newHtml = newHtml.replace(/"/g, '\\\"');
                    newHtml = newHtml.replace(/\[tmp_\._yh\]/g, '\\\"');
                    //console.log('newHtml :'+ newHtml);
                }
                return newHtml;
            });
            return s_;
        },

        //当属性中有公式绑定了同步变量时，同步变量的更新要更新属性
        formatAttr : function(thisObj, options, index, hasSetData) {
            index = index || 0;
            //如果之前继承过父亲的data 则后面自身渲染时 如果自身不作data声明 是要继续使用last_options的data的
            var hasLastOpt = thisObj && thisObj['last_options'] && thisObj['last_options']['data'] ? true : false;
            var optData;
            if((isUndefined(options['data']) || !hasData(options['data'])) && hasLastOpt ) {
                optData = thisObj['last_options']['data'];
                options['data'] = optData; //更新option的data
            } else {
                optData = options['data'] || makeNullData();
            }
            var newAttr = {};
            var hidden = false;
            var setHidden = false;//设置隐藏样式
            var setChecked = undefined;//设置打勾样式
            var setDisabled = false;//设置不可点击
            var classExt = false;//包含扩展样式
            var classExtFlag = false;//true 添加 false移除 扩展样式
            var has_kuohao = false; //是否含有括号
            // 如果有括号，并且obj的忽略绑定全局设置为null 则将忽略绑定设置为false;如果没有括号 则忽略绑定设为true
            var class_extend_true_val = '';
            var newOpt = {};
            var evenOption = {};//事件参数
            // console.log('formatAttr');
            // console.log(thisObj);
            //console.log(options['click']);
            var tmpStyle = [];
            $.each(options, function (n, v) {
                class_extend_true_val = '';
                //系统参数 无须解析
                if(n.substr(0, 7) =='source_') {
                    return;
                }
                //支持字符串中输入{公式} value已经在外部更新 这里只针对属性
                if(isStrOrNumber(v) ) {
                    if(strHasKuohao(v)) {
                        options['source_' + n] = v;
                        if(n != 'data') has_kuohao = true; //data 带括号不算是属性包含括号 因为下次格式化也不是通过format来实现的 是通过 renew ObjData
                        if(n !='value' && hasSetData) {
                            v = strObj.formatStr(v, optData, index, thisObj, n);
                            options[n] = v; //参数要改变 防止外部取出来的仍是括号
                        }
                    }
                    if(strInArray(n, ['value', 'th', 'td']) !=-1 && isString(v)) {
                        if(thisObj.formatVal) {
                            thisObj.formatVal(options);
                            return;
                        }
                    }
                }
                //除了style class喜欢变来变去 其他文本属性不含括号 并且未改变 则不作更新
                if(optionIsSame(thisObj, options, n)) {
                    console.log('optionIsSame this', n + ':'+ v);
                    // console.log(thisObj);
                    return;
                }
                if(n == 'type') {
                    if(isStrOrNumber(v)) {
                        //sanjiao在data更新不能更新obj的type  只能属性更新时通知其改变
                        if(thisObj.renewType) {
                            thisObj.renewType(options);
                        }
                    }
                    //return;// 按钮是有type的 如: type='button' 所以不能跳过
                }
                if(has_kuohao && strInArray(n, ['readonly', 'readOnly']) !=-1) {
                    if(thisObj.renewReadonly) {
                        thisObj.renewReadonly(options);
                    }
                }

                //style支持对象数组格式 {'color': '', 'font-size': '12px'}
                if(n == 'style' && typeof options[n] == 'object') {
                    var newStyleVal = [];
                    var styleVal = options['style'];
                    $.each(styleVal, function (k_, v_) {
                        v_ = $.trim(v_, ';');
                        newStyleVal.push(k_ + ":" + v_);
                    });
                    newAttr['style'] = newStyleVal.join(';');
                }
                //console.log('tmpStyle:'+n +'_____:'+ v);
                //console.log(tmpStyle);
                if(attrIsEven(n)) {
                    //console.log('attrIsEven:'+n );
                    evenOption[n] = v;
                }
                //style的转译属性：position/width/height/left/top/margin_/padding_
                //都要提进style=''里
                if(isStrOrNumber(v) ) {
                   if(n.indexOf('_') !=-1 || n.indexOf('-') !=-1) {
                       var hasGangStr = false;
                       styleHengAttrs.forEach(function (n_) {
                           var reg_ = new RegExp('^'+ n_, "gm");
                           if(n.match(reg_) && $.inArray(n,styleIgnore) == -1) {
                               n = n.replace('_', '-');
                               hasGangStr = true;
                           }
                       });
                   }
                   //属性转为style
                    if(strInArray(n, cantAddCssAttrs) !=-1 || hasGangStr) {
                        tmpStyle.push(n+':'+v);
                    }
                    //支持 data data-n
                    if(n.substr(0, 4) == 'data' ) {
                        //console.log('add newAttr222 '+ n +':'+ v);
                        if(!optionIsSame(thisObj, options, n)) {
                            //console.log('add newAttr '+ n +':'+ v);
                            newAttr[n] = v;
                        }
                    }
                }

                //hide or show
                if(n =='show' || n =='hidden' || n =='hide') {
                    setHidden = true;
                    // console.log('show newAttr '+ n +':', v);
                    if(((n =='hide' || n =='hidden') && (v == 'true' ||  v == true ||  v == 1)) || (n =='show' && (v == 'false' ||  v == false ||  v == 0))) {
                        hidden = true;
                    }
                }
                //disabled
                if(n =='disabled') {
                    if(v == 'false' || !v || v==0 || v =='0') {
                        setDisabled = false;
                    } else {
                        setDisabled = true;
                    }
                }
                if(n =='checked') {
                    //console.log('checked:'+ v);
                    if(v == 'false' || !v || v==0 || v =='0') {
                        setChecked = false;
                    } else {
                        setChecked = true;
                    }
                }
                //console.log('attr:'+n);
                if(n == 'class_extend') {
                    classExt = true;
                    //console.log('class_extend');
                    //console.log(thisObj);
                    //console.log(options['class_extend']);
                    if(v) {
                        classExtFlag = true;
                        thisObj['class_extend_true_val'] = v; //缓存扩展样式 下次还可以移除
                    } else {
                        classExtFlag = false;
                    }
                }
                // console.log('formatAttr.....each:'+ n , v);
                //扩展属性不需要显示
                if(canAddAttr(n)) {
                    if(isStrOrNumber(v) || typeof v == 'boolean') {
                        if(thisObj.attr && thisObj.attr(n) && v  && thisObj.attr(n) == v  && n != 'class') {
                            return; //不变的属性不用设置
                        }
                        newAttr[n] = v;
                    }
                }
            });
            //console.log(newAttr);
            if(hasData(tmpStyle)) {
                if(isUndefined(newAttr['style'])) {
                    newAttr['style'] = '';
                }
                tmpStyle.forEach(function (tmp_) {
                    newAttr['style'] = classAddSubClass(trim(newAttr['style'], ';'), tmp_, true, ';');
                });
            }
            var lastClassTrueVal = '';
            if(thisObj['class_extend_true_val']) {
                lastClassTrueVal = thisObj['class_extend_true_val'];
            }
            if(classExt) {
                if(classExtFlag) {
                    newAttr['class'] = classAddSubClass(newAttr['class'], lastClassTrueVal, true, ' ');
                } else  {
                    if(lastClassTrueVal) { //之前有生成过扩展样式 要移除
                        newAttr['class'] = classAddSubClass(newAttr['class'], lastClassTrueVal, false, ' ');
                    } else { //如果配置里没有class 并且扩展里也没有 且现在有样式 则要清空样式
                        if(!classExtFlag && !options['class'] && thisObj.attr('class')) {
                            newAttr['class'] = '';
                        }
                    }
                }
                options['class'] = newAttr['class'];
            }
            if(setHidden) {
                if(hidden) {
                    newAttr['class'] = classAddSubClass(newAttr['class'], 'hidden', true, ' ');
                } else {
                    newAttr['class'] = classAddSubClass(newAttr['class'], 'hidden', false, ' ');
                    if(thisObj) thisObj.removeClass('hidden');
                    //console.log(newAttr['class']);
                }
                options['class'] = newAttr['class'];
            }
            //console.log(thisObj);
            if(!setDisabled) {
                delete newAttr['disabled'];
                if(thisObj) thisObj.removeAttr('disabled');
            }
            if(!isUndefined(setChecked)) {
                if(thisObj) {
                    if(!setChecked) {
                        delete newAttr['checked'];
                        thisObj.removeAttr('checked');
                    }
                    //如果checked绑定了bind属性 要更新其他打勾状态
                    if(options['bind'] == 'checked') {
                        updateBindObj('checked', setChecked, [thisObj]);
                    }
                }
            }
            //console.log(thisObj);
            if(hasData(newAttr) && thisObj.attr ) {//更新属性
                //一样的class不需要重写
                if(newAttr['class'] == thisObj.attr('class')) {
                    delete newAttr['class'];
                }
                thisObj.attr(newAttr);
            }
            if(has_kuohao) {
                thisObj[objAttrHasKh] = true;
            }
            thisObj.events = cloneData(evenOption, thisObj.events);
            //更新旧的options
            options = $.extend({}, options, thisObj['options']);
            strObj.addEvents(thisObj);
            return newOpt;
        },
        //绑定属性
        addEvents : function(thisObj) {
            //console.log('add.Events', thisObj);
            //console.log(thisObj);
            var evenOption = thisObj.events || {};
            if(thisObj.noNeedEven) return;
            if(!hasData(evenOption)) return;
            var bindObj = isUndefined(thisObj.bindEvenObj) ? thisObj: thisObj.bindEvenObj;
            //特殊例子：makePage 对象不需要绑定事件
            objSetOptEven(bindObj, evenOption, thisObj);
        },
        //当属性中有公式绑定了同步变量时，同步变量的更新要更新属性
        reFormatKhAttr: function(thisObj, newOpt) {
            var optData = newOpt['data'];
            // console.log('reFormat.KhAttr.data');
            // console.log(optData);
            var attrsHasData = thisObj[objHasKhAttrs] || [];
            // console.log('reFormat__________________.KhAttr Attr');
            // console.log(thisObj);
            // console.log(attrsHasData);
            if(!hasData(attrsHasData)) return;
            var v;
            var setHidden = undefined;
            var hidden = false;
            var setDisabled = undefined;
            var setChecked = undefined;
            var classExtFlag = false;
            var classExt = false;
            var tmpStyle = [];
            var newAttr = {};
            var evenOption = {};
            var opt = thisObj['options'];
            // console.log('attrsHasData', attrsHasData);
            $.each(attrsHasData, function (index, n) {
                v = getOptVal(opt, ['source_'+n, n], null);//优先取source_n
                if(n =='value') {
                    // console.log('callRewObjStringVal__________________');
                    callRewObjStringVal(thisObj, newOpt);
                    return;//continue
                }
                // console.log('reformat this1:'+ n + ':', v);
                // console.log(optData);
                v = strObj.formatStr(v, optData, index, thisObj, n); //计算v中的公式 {1+2 > 3}
                // console.log('format this2:'+ n + ':', v);
                thisObj['options'][n]= v;//参数更新 直接可以外部获取道新的值
                if(attrIsEven(n)) {
                    //console.log('attrIsEven this2:'+ n );
                    evenOption[n] = v;
                }
                //console.log('format this:');
                //console.log(n + ':'+ v);
                //style的转译属性：position/width/height/left/top/margin_/padding_
                //都要提进style=''里
                if(isStrOrNumber(v) ) {
                    var hasGangStr = false;
                    styleHengAttrs.forEach(function (n_) {
                        var reg_ = new RegExp('^'+ n_, "gm");
                        if(n.match(reg_) && $.inArray(n,styleIgnore) == -1) {
                            n = n.replace('_', '-');
                            hasGangStr = true;
                            //console.log('hasGangStr '+ n+':'+ v);
                        }
                    });
                    if(strInArray(n, cantAddCssAttrs) !=-1 || hasGangStr) {
                        //console.log('push '+ n+':'+ v);
                        tmpStyle.push(n+':'+v);
                    }
                    //支持data-n
                    //console.log('add newAttr111 '+ n +':'+ v);
                    if(n != 'data' && n.substr(0, 4) == 'data' ) {
                        newAttr[n] = v;
                    }
                }

                //hide or show
                //console.log(n + ' is:'+ v);
                if(n =='show' || n =='hidden' || n =='hide') {
                    setHidden = true;
                    if(((n =='hide' || n =='hidden') && (v == 'true' ||  v == true ||  v == 1)) || (n =='show' && (v == 'false' || v == false|| v == 0))) {
                        // console.log('n=='+ n +'::::::v='+ v);
                        hidden = true;
                    }
                }
                // console.log(n, 'hidden_________________', hidden);
                //disabled
                if(n =='disabled') {
                    if(v == 'false' || !v || v==0 || v =='0') {
                        setDisabled = false;
                    } else {
                        setDisabled = true;
                    }
                }
                if(n =='checked') {
                    //console.log('checked:'+ v);
                    if(v == 'false' || !v || v==0 || v =='0') {
                        setChecked = false;
                    } else {
                        setChecked = true;
                    }
                }
                //console.log('attr:'+n);
                if(n == 'class_extend') {
                    classExt = true;
                    //console.log('class_extend');
                    //console.log(thisObj);
                    //console.log(options['class_extend']);
                    if(v) {
                        classExtFlag = true;
                        thisObj['class_extend_true_val'] = v; //缓存扩展样式 下次还可以移除
                    } else {
                        classExtFlag = false;
                    }
                }
                // if(n =='colspan') {
                //console.log(optData);
                //console.log('n:'+ n +',v:'+v);
                //console.log('canAddAttr:'+ canAddAttr('colspan'));
                // }
                //扩展属性不需要显示
                if((isStrOrNumber(v) || typeof v == 'boolean') && canAddAttr(n)) {
                    if(thisObj.attr && thisObj.attr(n) && v  && thisObj.attr(n) == v  && n != 'class') {
                        return; //不变的属性不用设置
                    }
                    newAttr[n] = v;
                }
            });
            //console.log('newAttr::');
            //console.log(thisObj);
            //console.log(newAttr);
            if(hasData(tmpStyle)) {
                if(isUndefined(newAttr['style'])) {
                    newAttr['style'] = '';
                }
                tmpStyle.forEach(function (tmp_) {
                    //console.log("newAttr['style']");
                    //console.log(newAttr['style']);
                    newAttr['style'] = classAddSubClass(trim(newAttr['style'], ';'), tmp_, true, ';');
                });
                //获取旧的class 当class中不包含{}时 需要继续使用
                var oldStyle = getOptVal(newOpt, 'style', '');
                if(oldStyle && !strHasKuohao(oldStyle)) {
                    newAttr['style'] = classAddSubClass(oldStyle, newAttr['style'], ';');
                }
            }
            //console.log('options3__________::');
            //console.log('newAttr::');
            //console.log(thisObj);
            //console.log(newAttr);
            //console.log('setDisabled::'+ setDisabled);
            var lastClassTrueVal = '';
            if(thisObj['class_extend_true_val']) {
                lastClassTrueVal = thisObj['class_extend_true_val'];
            }
            //console.log('lastClassTrueVal::'+ lastClassTrueVal);
            //console.log('options4::');
            if(classExt) {
                //console.log('has classExt:'+ classExt);
                if(classExtFlag) {
                    newAttr['class'] = classAddSubClass(newAttr['class'], lastClassTrueVal, true, ' ');
                } else  {
                    //console.log('has lastClassTrueVal');
                    //console.log(thisObj);
                    //console.log(lastClassTrueVal);
                    if(lastClassTrueVal) { //之前有生成过扩展样式 要移除
                        newAttr['class'] = classAddSubClass(newAttr['class'], lastClassTrueVal, false, ' ');
                    } else { //如果配置里没有class 并且扩展里也没有 且现在有样式 则要清空样式
                        if(!classExtFlag && !newOpt['class'] && thisObj.attr('class')) {
                            //console.log('remove class');
                            //console.log(thisObj);
                            newAttr['class'] = '';
                        }
                    }
                }
            }

            //console.log(thisObj);
            //console.log(newAttr);
            //console.log('setHidden:'+ setHidden);
            //console.log('hidden:'+ hidden);
            if(!isUndefined(setDisabled)) {
                if(!setDisabled) {
                    //console.log('del__________________disabled');
                    delete newAttr['disabled'];
                    if(thisObj) thisObj.removeAttr('disabled');
                }
            }
            if(!isUndefined(setChecked)) {
                //console.log('setChecked__________________'+ setChecked);
                if(thisObj) {
                    if(!setChecked) {
                        delete newAttr['checked'];
                        thisObj.removeAttr('checked');
                    }
                    //如果checked绑定了bind属性 要更新其他打勾状态
                    if(newOpt['bind'] == 'checked') {
                        updateBindObj('checked', setChecked, [thisObj]);
                    }
                }
            }

            if(!isUndefined(setHidden)) {
                //console.log(thisObj);
                if(hidden) {
                    newAttr['class'] = classAddSubClass(newAttr['class'], 'hidden', true, ' ');
                } else {
                    // console.log('remove hidden');
                    // console.log(newAttr['class']);
                    newAttr['class'] = classAddSubClass(newAttr['class'], 'hidden', false, ' ');
                    if(thisObj) {
                        // console.log('removeClass hidden', thisObj);
                        thisObj.removeClass('hidden');
                    }
                    // console.log(newAttr['class']);
                }
            } else {
                var lastShow = getOptVal(opt, ['show'], '');
                var lastHide = getOptVal(opt, ['hide'], '');
                if(lastShow !== '') {
                    if(!lastShow || lastShow ==0) {
                        newAttr['class'] = classAddSubClass(newAttr['class'], 'hidden', true, ' ');
                    } else {
                        newAttr['class'] = classAddSubClass(newAttr['class'], 'hidden', false, ' ');
                    }
                } else if(lastHide !== '') {
                    if(lastHide) {
                        newAttr['class'] = classAddSubClass(newAttr['class'], 'hidden', true, ' ');
                    } else {
                        newAttr['class'] = classAddSubClass(newAttr['class'], 'hidden', false, ' ');
                    }
                }
            }
            // console.log('renew obj_attr:');
            // console.log(thisObj);
            // console.log(newAttr);
            //console.log(hasData(newAttr));
            //console.log(thisObj.attr);
            //console.log(thisObj);
            //console.log('attr_class:'+ newAttr['class']);
            if(hasData(newAttr) && thisObj.attr ) {//更新属性
                //console.log('call_____renew obj_attr:');
                //一样的class不需要重写
                //获取用户自定义的class 如果不包含{}时 需要继续保留
                var oldClass = thisObj.diyClass;
                //console.log(thisObj);
                // console.log('oldClass :'+ oldClass);
                if(oldClass) {
                    newAttr['class'] = classAddSubClass(oldClass, newAttr['class'], ' ');
                }
                //console.log('add_____________');
                //console.log(newAttr);
                var oldExtendClass = getOptVal(opt, 'class_extend', '');
                if(oldExtendClass) {
                    newAttr['class'] = classAddSubClass(newAttr['class'], oldExtendClass, ' ');

                }
                //如果更新了可见，则之前的hidden要去掉
                if(setHidden===true && hidden == false) {
                    // console.log('replace_____Class:');
                    newAttr['class'] = newAttr['class'].replace(/\s*hidden/ig, '');
                }
                // console.log('attr_class2:'+ newAttr['class']);
                //console.log('attr_class2:'+ thisObj.attr('class'));
                if(newAttr['class'] == thisObj.attr('class')) {
                    // console.log('delete class');
                    //console.log(thisObj);
                    //console.log(newAttr['class']);
                    delete newAttr['class'];
                }
                // console.log('renew obj_attr___:');
                //console.log(oldExtendClass);
                //console.log(thisObj);
                // console.log(newAttr);
                thisObj.attr(newAttr);
            }
            thisObj.events = cloneData(evenOption, thisObj.events);
            this.addEvents(thisObj);
        },

        //字符串转变量
        //str 要替换的字符串
        //objName 要转成的变量名字
        formatStr : function(str, data_, index, obj_, attrName) {
            if(isUndefined(data_) || !isObj(data_)) data_ = {};
            str = str || '';
            obj_ = obj_ || null;
            attrName = attrName || '';
            if(str=='' || !isStrOrNumber(str) || ( !strHasKuohao(str, 'data') &&  !strHasKuohao(str, 'public'))) {
                return str;
            }
            var abcAlbReg = '\{([a-zA-Z_]+[a-zA-Z_\d.]*)\}';
            str = yinhaogTH(str);//所有\\"都要保护起来
            str = changeYinhaoIn(str);// 防止解析{func}里的func时 "==?"这样的字符串被误解析
            // console.log('format Str1:'+ str);
            //转译引号里的内容 防止检测纠正语句时对替换的干扰
            //{12 === item ?"a= item2":"b"} 加密 "a= item2"
            function changeYinhaoIn(s_) {
                if(isObj(s_)) return s_;
                if(isBoolean(s_)) return s_;
                if(isNumber(s_)) return s_;
                //console.log('changeYinhaoIn:'+ s_);
                //console.log(s_);
                s_ = s_.replace(/'([^']*)'/g, function (match_) {
                    match_ = trim(match_, "'");
                    if(RegExp(abcAlbReg, 'g').test(match_)) {//将引号里的标量提前格式化
                        //console.log('formats_ before match_1:'+ match_);
                        match_ = formatAbc(match_, 'public'); //格式化字符串 数据来源于 public
                        //console.log('formats_ before match_2:'+ match_);
                        match_ = formatAbc(match_, 'data'); //格式化字符串 数据来源于 data
                        //console.log('formats_ after match_3:'+ match_);
                    }
                    return "'"+ (match_ ? strObj.urlencodeLR(encodeNewHtml(match_)) : '') +"'";
                });
                s_ = s_.replace(/"([^"]*)"/g, function (match_) {
                    match_ = trim(match_, '"');
                    if(RegExp(abcAlbReg, 'g').test(match_)) {//将引号里的标量提前格式化
                        //console.log('formats_ public match_1:'+ match_);
                        match_ = formatAbc(match_, 'public'); //格式化字符串 数据来源于 public
                        //console.log('formats_ before match_2:'+ match_);
                        match_ = formatAbc(match_, 'data'); //格式化字符串 数据来源于 data
                        //console.log('formats_ after match_3:'+ match_);
                    }
                    match_ = match_.replace(/\(/g, kuohaoNewhasl);//()在引号里不会加密url 会混淆 所以要作转译
                    match_ = match_.replace(/\)/g, kuohaoNewhasr);//()在引号里不会加密url 会混淆 所以要作转译
                    return '"'+ (match_ ? strObj.urlencodeLR(encodeNewHtml(match_)) : '') +'"';
                });
                //console.log('new s_:'+ s_);
                return s_;
            }

            //console.log('format Str:'+ str);
            //console.log('data_:' );
            //console.log(data_ );
            //console.log(obj_ );
            //格式单个变量
            function formatOneDateKey(abc, dataPublic) {
                dataPublic = dataPublic || 'data'; // data的来源 要么继承data 要么public里取
                abc = abc || '';
                abc = $.trim(abc);
                var resultStr=   '';
                if(!abc) return abc;
                attrName = attrName || '';
                if(dataPublic == 'data') {
                    var match1 = abc.match(/^this\.([a-zA-Z0-9]+)/);
                    var match2 = abc.match(/^this\[\d+\]*(\[('|")([a-zA-Z_\[\]]+[a-zA-Z_\d.]+)('|")\])*/);
                    if(match1 || match2) {
                        if(match1 != null) {
                            //console.log('replace 1_________________ :'+abc);
                            //允许获取当前data对象
                            var abc2 = abc.replace('this.', 'data_.');
                            //console.log(abc2);
                            try {
                                resultStr = eval(abc2);
                            } catch(err){
                                resultStr = '';
                            }
                        }
                        if(match2 !=null) {
                            //允许获取当前data对象
                            var abc2 = abc.replace(/^this/, 'data_');
                            abc2 = strObj.urlDecodeLR(abc2);
                            try {
                                resultStr = eval(abc2);
                            } catch(err){
                                resultStr = '';
                            }
                        }
                    } else {
                        //{a.b}
                        if(abc.indexOf('.') !=-1) {
                            var array_ = abc.split('.');
                            var getData = $.extend({}, data_);
                            $.each(array_, function (n, key_) {
                                if(isUndefined(getData[key_])) {
                                    return;
                                }
                                getData = !isUndefined(getData[key_]) ? getData[key_] : {};
                            });
                            resultStr = getData.toString();
                        } else {
                            if(!isUndefined(data_[abc])) {
                                resultStr = data_[abc]; //这里调取的数据不能再进行格式化算法 要当作纯字符串输出。如：“我的>123” 格式化会报错。
                            }
                        }
                    }
                } else {
                    if(!isUndefined(livingObj['data'][abc])) {
                        resultStr = getObjData(abc);
                        // console.log('resultStr', abc, resultStr);
                        if(obj_) {//当前对象的字符串调取了全局变量，则要加入对象
                            if(attrName) {
                                addObjAttrToBindVal(obj_, attrName, abc);
                            }
                        }
                        //console.log(abc);
                    } else {
                        if(obj_) {
                            //当前对象的字符串调取了全局变量，则要加入被监听对象
                            if(attrName) addObjAttrToBindVal(obj_, attrName, abc);
                        }
                        resultStr = ''; //返回自定义的绑定对象字符串 <xxx:aaaa>
                    }
                }
                var newAbc;
                if(typeof resultStr == 'object' || typeof resultStr == 'array') { // 对象直接替换当前匹配的data,如：data:{son_data}
                    newAbc = resultStr; //可能是data:{info}提取对象 所以不能转json
                } else {
                    newAbc = abc.replace(abc, resultStr);
                }
                //console.log(' format one '+ abc +' resultStr:'+ newAbc);
                return newAbc;
            }
            //提取赋值的等式 {a=3}
            function getSetStr(setSourcsStr) {
                setSourcsStr = setSourcsStr || '';
                if(!setSourcsStr) return '';
                var matches = setSourcsStr.match(/{(.+)}/g);
                function replaceOneVal(str_, matchOne) {
                    //赋值操作要替换 将绑定的变量存储 {aaa} = 66 或 abc += 77
                    var regSet = /((;\s*|^{\s*)([a-zA-Z_]+[a-zA-Z_\d.]*))(\s*)([\+\-*\/%]*)\=(\s*)('([^']*)'|"([^"]*)"|([^;}\s]+))\s*;*\s*}*/;
                    if(regSet.test(matchOne)) {
                        var findSetArray =  matchOne.match(RegExp(regSet,"g"));
                        //console.log('findSetArray');
                        //console.log(findSetArray);
                        var bindKey,bindSetVal;
                        findSetArray.forEach(function (setStr) {
                            var yufa = /([\+\-*\/%]*)\=(\s*)/.exec(setStr)[0]; // =或 +=
                            bindKey = setStr.substr(0, setStr.indexOf(yufa));
                            bindSetVal = setStr.substr(setStr.indexOf(yufa) +1  + (yufa.length -1));
                            bindKey = $.trim(bindKey);
                            bindKey = trim(bindKey, '{');
                            bindKey = trim(bindKey, "}");
                            bindSetVal = $.trim(bindSetVal);
                            bindSetVal = trim(bindSetVal, "}");
                            bindSetVal = $.trim(bindSetVal);
                            bindSetVal = trim(bindSetVal, ';');
                            bindSetVal = $.trim(bindSetVal);
                            bindSetVal = trim(bindSetVal, '"');
                            bindSetVal = trim(bindSetVal, "'");

                            bindSetVal = this.urlDecodeLR(bindSetVal);
                            //console.log('bindSetVal:'+ bindSetVal);
                            //console.log('replace setStr:'+ setStr);
                            //console.log('bindKey:'+ bindKey);
                            //如果定义过全局变量 不允许再重定义  后面叠加运算 如 += , *= 会在更新时重复计算，所以不考虑二次叠加全局变量.
                            if(!livingObj.hasOwnProperty(bindKey)) {
                                // console.log('addKey.ToListener:'+ bindKey +',val:'+ bindSetVal + ',yufa:'+ yufa);
                                addKeyToListener(bindKey, bindSetVal, yufa);
                            }
                            //去掉 aa=6, {aa=c要转为 { 防止其他剩下的{aa=c;d=5;f=7}丢失{
                            str_ = str_.replace(setStr, function (val) {
                                if(val.substr(0, 1) =='{' && val.substr(val.length-1, 1) =='}') return '';
                                if(val.substr(0, 1) =='{') return '{';
                                if(val.substr(val.length-1, 1) =='}') return '}';
                                return '';
                            });
                        });
                        //继续替换剩下的字符串
                        str_ = getSetStr(str_);
                        //console.log('matchOne:'+ matchOne);
                    }
                    return str_;
                }
                //console.log('matches');
                //console.log(matches);
                matches && matches.forEach(function (matchOne) { //match: {abc}
                    if(/{([^\{\}]+)}/g.test(matchOne)) {
                        //console.log('matchOne');
                        //console.log(matchOne);
                        var matches2 = matchOne.match(/{([^\}]+)}/g);
                        matches2 && matches2.forEach(function (matchOne_) {
                            //console.log('match_:'+ matchOne);
                            setSourcsStr = replaceOneVal(setSourcsStr, matchOne_);
                        });
                    }
                });
                //console.log('setSourcsStr:'+ setSourcsStr);
                return setSourcsStr;
            }
            str = getSetStr(str); // 提取 aaa = 33; 必须放在第一步 全局变量赋值
            //console.log('after getSetStr:'+ str);

            //格式化当前public的变量
            function formatPubJkh(s_) {
                //console.log('formatPubJkh Abc:'+ s_);
                if(!isStrOrNumber(s_))  return s_;
                if(isBoolean(s_)) return s_;
                if(!s_) return s_;
                //console.log('s_:::::::::::::::');
                //console.log(s_);
                if(typeof s_ == 'number') s_ += '';
                //console.log('getStr JHK:  '+s_);
                s_ = formatAbc(s_, 'public');
                //console.log('formatAbc public end___:  '+s_);
                if(isObj(s_)) {
                    return s_;
                }
                //console.log('remendErr.Str11:'+ s_);
                s_ = remendErrStr(s_);
                //console.log('remendErr.Str22:'+ s_);
                var has_Yufa = strObj.hasYufa(s_);
                if(has_Yufa ) {
                    //console.log(s_);
                    //console.log('has__Yufa:'+ has_Yufa);
                    //console.log('s___1:'+ s_);
                    s_ = strObj.runYufa(s_);
                    //console.log('s___2:'+ s_);
                } else {
                    //console.log(s_+ ' no has__Yufa:'+ has_Yufa);
                }
                return s_;
            }
            //格式化当前data的变量
            function formatDataJkh(s_) {
                //console.log('format DataJkh ———————————————:'+ s_);
                if(!isStrOrNumber(s_))  return s_;
                if(isBoolean(s_)) return s_;
                if(!s_) return s_;
                if(typeof s_ == 'number') s_ += '';
                //console.log('getStr JHK_________________:  '+s_);
                s_ = formatAbc(s_, 'data');
                //console.log('after format Abc:', s_);
                //提取语法：
                // item {0 % 2==0 ? 'even': 'odd'}
                // {'a'+'b'}
                var replaceFunc = function (s3) {
                    //console.log('s::::::::::: '+ s3);
                    var matchesFunc = __getjkhFunc(s3);
                    //console.log('__get jkh__Func:');
                    //console.log(matchesFunc);
                    var yufaNum =0;
                    if(hasData(matchesFunc)) {
                        matchesFunc.forEach(function (func_) {
                            var macthNew = '';
                            //console.log(' check_Yufa:'+ func_);
                            var checkFunc = remendErrStr(func_);
                            //console.log('checkFunc2:'+ checkFunc);
                            var has3Yufa = strObj.hasYufa(checkFunc);
                            //console.log(checkFunc+ ' has3_Yufa:'+ has3Yufa);
                            if(has3Yufa) {
                                checkFunc = strObj.urlDecodeLR(checkFunc);
                                //console.log('after urlDecodeLR :'+ checkFunc);
                                macthNew= strObj.runYufa(checkFunc)
                                //console.log('s3:'+ s3);
                                //console.log('checkFunc:'+ checkFunc);
                                //console.log('macthNew:'+ macthNew);
                                //console.log(regCodeAddGang(func_));
                                //console.log('after replace:'+ s3url);
                                var s3url = $.url.encode(s3);
                                var func_url = $.url.encode(func_);
                                var macthNew_url = $.url.encode(macthNew);
                                //console.log('s3url:'+ s3url);
                                //console.log('func_url1:'+ s3url);
                                s3url = s3url.replace(RegExp(regCodeAddGang(func_url), 'g'), macthNew_url);
                                //console.log('func_url2:'+ s3url);
                                yufaNum ++;
                                s3 = s3url ? $.url.decode(s3url) : '';
                                //console.log('decode:'+ s3);
                            } else {
                                s3 = decodeNewHtml(s3);
                            }
                            //console.log('after replace:'+ s3);
                        });
                    } else {
                        //console.log('有语法 但没有{函数}:'+ s3);
                        //console.log(matches2);
                    }
                    if(isObj(s3)) {
                        //console.log('is obj:!!!!!!!!!');
                        //console.log(s3);
                        return s3;
                    }
                    //替换错误的语法 再检测是否含语法
                    s3 = remendErrStr(s3);
                    var has3Yufa = strObj.hasYufa(s3);
                    if(has3Yufa) {
                        // 纠正错误的语法 如 ? aa
                        //console.log(s3+ ' has3_Yufa:'+ has3Yufa);
                        //console.log(s3+ ' has3_Yufa:'+ has3Yufa);
                        s3 = strObj.runYufa(s3);
                    } else {
                        //console.log(s3+ ' no has3_Yufa:'+ has3Yufa);
                    }
                    //解析完data的数据再恢复引号
                    s3 = strObj.urlDecodeLR(s3);
                    //console.log(' ———————————————:'+ s3);
                    if(strHasKuohao(s3, 'data') && yufaNum >0) { //必须之前有语法 才能继续检测 否则没有语法不需要再循环
                        s3 = replaceFunc(s3);
                    }
                    return s3;
                };
                //click('[url_hash_code]%E5%95%8A%E5%A5%BD')
                //有没有{}语法 应该是在解析url之前判断 不能在解析后判断语法，因为url里的符合内容太丰富了 无法做到解析时不受干扰
                if(strHasKuohao(s_, 'data')) {
                    //console.log('still has func ———————————————:'+ s_);
                    s_ = replaceFunc(s_);
                } else {
                    //直到没有语法才可以解析
                    if(isStrOrNumber(s_)) {
                        s_ = strObj.urlDecodeLR(s_);
                    }
                }
                return s_;
            }

            //console.log('替换public的变量 :'+ str);
            str = formatPubJkh(str); //格式化全局字符串变量 数据来源于 public
            //console.log(obj_);
            //console.log('替换完public的变量，结果:'+ str);
            str = formatDataJkh(str); //格式化字符串 数据来源于 data
            //console.log('替换完data的变量，结果:'+ str);
            //替换单个尖括号里的变量
            function replaceMatchAbc(str_, match_, dataPub) { //match_: {abc}

                //console.log('replace MatchAbc str_ :'+str_ + ',dataPub:'+ dataPub);
                var matchVal;
                if(dataPub=='public') {
                    matchVal = trim(match_, '{{');
                    matchVal = trim(matchVal, '}}');
                } else {
                    matchVal = trim(match_, '{');
                    matchVal = trim(matchVal, '}');
                }
                matchVal = $.trim(matchVal);
                if(!matchVal) {
                    //console.log(str_);
                    //console.log(matchVal);
                    str_ = str_.replace(matchVal, '');
                    return; //continue
                }
                if(!str_) return str_;
                //console.log(str_);
                //console.log('matchVal:'+ matchVal);
                if(isBoolean(str_)) return ;//continue
                //console.log('change str old :'+str_);
                //console.log('change str match :'+match_);
                //console.log('change matchVal old :'+matchVal);
                //console.log('dataPub :'+dataPub);
                //console.log(data_);
                //console.log('change matchVal new :'+matchVal);
                matchVal = formatOneDateKey(matchVal, dataPub);//格式 {abc}
                //console.log('change matchVal new :'+matchVal);
                //此结果可能是提取data数组 或 对象 或字符串
                if(isStrOrNumber(matchVal)) {
                    //console.log('str_ :'+ str_);
                    //console.log('matchVal :'+ matchVal);
                    //console.log('type :'+ (typeof matchVal));
                    if(!isNumber(matchVal)) {//非纯数字的结果 要替换(abc) 为 ("abc")
                        //console.log('!isNumber :'+ matchVal);
                        str_ = str_.replace(/\{([a-zA-Z_]+[a-zA-Z_\d.]*)\}/g, '<j>$1</j>'); //把{a}替换为<j>a</j> 方便后面解析{内包含<j>
                        //console.log('str_ :'+ str_);
                        matchVal = encodeNewHtml(matchVal);//加密引号\\" " ( )
                        var matchValJhkReg = match_.replace(/^\{(.+)\}$/, '<j>$1<\\/j>');
                        var hasJkhOutReg = '\{([^\<]*)' + matchValJhkReg + '([^\}]*)[\'|\"|\}]'
                        //console.log(hasJkhOutReg);
                        var jkhYinhRegOur = '(\'|")'+  regCodeAddGang(strObj.urlHashCode)
                            +'([^\'"]*)\s*' + matchValJhkReg + 's*([^\'"]*)(\'|")'; // "[url_hash_code]  {a}  " 在url里不需要给{a}加引号
                        //变量外部含有尖括号 并且外部无引号
                        if(RegExp(hasJkhOutReg, 'g').test(str_)
                            && !RegExp(jkhYinhRegOur, 'g').test(str_)
                        ) {
                            //console.log('matchVal kuo by (<<<<<<<<<<<<:'+matchVal);
                            // { {a} =='a' } 变为 { "{a}" =='a' }
                            var matches = str_.match(RegExp(hasJkhOutReg, 'g'));
                            //console.log('str_ :'+ str_);
                            //console.log(matches);
                            matches.forEach(function (matchTmp) {
                                //console.log('matchTmp :'+ matchTmp);
                                //console.log('match_ :'+ match_);
                                var newMac = matchTmp.replace(RegExp(regCodeAddGang(matchValJhkReg), 'g'), '"'+ strObj.urlencodeLR(encodeNewHtml(matchVal)) +'"');
                                //console.log('newMac:'+ newMac);
                                str_ = str_.replace(RegExp(regCodeAddGang(matchTmp), 'g'), newMac);
                            });
                        }
                        //console.log('change str_ last111111 :'+str_);
                        str_ = str_.replace(/<j>([a-zA-Z_]+[a-zA-Z_\d.]*)<\/j>/g, '{$1}'); //把<j>a</j> 还原为{a} 再格式化一边
                        str_ = str_.replace(RegExp(regCodeAddGang(match_), 'g'), matchVal); //{}外部的变量格式化不需要加引号

                        //console.log('!isNumber  matchVal :'+matchVal);
                        //console.log('change str_ last22222 :'+str_);
                    } else {
                        //console.log('isNumber :'+ matchVal);
                        str_ = str_.replace(RegExp(regCodeAddGang(match_), 'g'), matchVal);
                    }
                    //console.log('change str_ new :'+str_);
                } else { //abc => obj 那么abc直接等于obj
                    //console.log('match_ obj :'+ match_);
                    if(typeof matchVal == 'object') {
                        //console.log('matchVal is object ______________::::');
                        //console.log(str_);
                        //console.log('match_:'+ match_);
                        //console.log(matchVal);
                        //如果匹配的关键词返回是object 则：如果自身原文就是匹配的变量，则替换自身变量
                        if(match_ == str_) {
                            str_ = matchVal; //可能是data:{info}提取对象 所以不能转json
                        } else {
                            str_ = str_.replace(RegExp(regCodeAddGang(match_), 'g'), matchVal);
                        }
                    } else {
                        //如果匹配的关键词返回是null 则清空
                        str_ = str_.replace(RegExp(regCodeAddGang(match_), 'g'), '');
                    }
                }
                str_ = decodeNewHtml(str_);
                //console.log( 'match is abc to:'+ str_);
                //console.log('dataPub:'+dataPub);
                //console.log('matchVal:'+ matchVal);
                //console.log('change str_ last 233333333333333333333 :'+str_);
                return str_;
            };
            //获取尖括号变量
            function __getjkh(s_, dataType) {
                var jhkArray = [];
                if(dataType=='data') {
                    //提取对象
                    var tmpMatch = s_.match(/{([a-zA-Z_]+[a-zA-Z_\d.]*)}/g);
                    if(tmpMatch) {
                        jhkArray = jhkArray.concat(tmpMatch);
                    }
                    //提取子对象 {this[0][abc]}  this.abc.abc 因为引号里的内容可能加了[url]
                    var tmpMatch = s_.match(/{([a-zA-Z_]+[a-zA-Z_\d.]*)(\[\d+\])*(\[('|")([a-zA-Z_\[\]]+[a-zA-Z_\d.]+)('|")\])*}/g);
                    if(tmpMatch) {
                        jhkArray = jhkArray.concat(tmpMatch);
                    }
                } else {
                    tmpMatch = s_.match(/{{([a-zA-Z_]+[a-zA-Z_\d.]*)}}/g);
                    if(tmpMatch) {
                        jhkArray = jhkArray.concat(tmpMatch);
                    }
                }
                if(jhkArray) jhkArray = uniqueArray(jhkArray);
                return jhkArray;
            }

            //修复字符串
            //如：abc {== 12 ?'a':'b'} 或 {!==12 && 1}或 {1> &&} 或 {?"a":"b"} 或 {&&"a"} 或 {1<||} 或 {1<||"1<|"=="&&>2"}
            // {跟比较符>|=|<|!=|?|&&|\|\|
            // &&跟比较符>|=|<|!=|?|&&|\|\|
            // == 跟运算符 ==
            function remendErrStr(checkSstr_) {
                // if(isUndefined(encode_)) encode_ = true;
                //console.log('remend ErrStr encode_:'+ encode_);
                var reg1 = /{(.+)}/;
                //console.log('remendErr__Str:'+ checkSstr_);
                var regErrGth = /^{\s*\(*!\)*\s*}$/; // {!}或 {(!)}
                var regErrEmpty = /^{\s*\(*\s*\)*\s*}$/;//{(())}
                var regFront = /(({|&|\|\|)|\(\s*\))\s*(\|\||\&\&|==|!=|>=|<=|>|<|\?|\+)/;
                var regEnd = /(\|\||\&\&|==|!=|>=|<=|>|<|\+)\s*((}|&|\)|\|\|)|\(\s*\))/;
                var regMiddleF = /(([\|\||\&\&|\(]+)|\(\s*\))\s*(\|\||\&\&|==|===|!\=|!\=\=|>|>=|<=|\?|\+)/; //&& >= 或 () >=
                var regMiddleB = /(==|===|!=|!==|>|>=|<=|\?|\+)\s*(([\|\||\&\&|\)|\?]+)|\(\s*\))/;  //==|| 或 ==?
                var reg3yuan1 = /\?\s*:/;// ?:
                var reg3yuan2 = /:\s*}$/; // :}
                var reg3yuan3 = /([0-9.]*)\s*([a-zA-Z_]+[a-zA-Z_\d.]*)\s*\?/; // 2a?
                //abc|| 或 &&asc 或 "addas" == "asdsdaas"
                var letterCompare = /(([a-zA-Z_]+[a-zA-Z_\d.]*)\s*(\+|\?|\==|!\=\=|\:|>|<|\&\&|\|\|)|(\+|\?|\==|!\=\=|\:|>|<|\&\&|\|\|)\s*([a-zA-Z_]+[a-zA-Z_\d.]*))/;

                var jkhMatchs = checkSstr_.match(RegExp(reg1, 'g')); //找到所有的尖括号 进行语法校验和补充""
                if(!hasData(jkhMatchs)) {
                    return  checkSstr_;
                }
                //检测单独的感叹号 即单变量的与非判断
                var doCheckGth = function(match__) {
                    if(!regErrGth.test(match__) ) {
                        return match__;
                    }
                    //console.log('has err regErrGth');
                    //console.log(match__);
                    match__ = checkSstr_ = '{!""}';
                    return match__;
                };
                //检测为空的括号
                var doCheckEpyKh = function(match__) {
                    if(!regErrEmpty.test(match__) ) {
                        return match__;
                    }
                    //console.log('has err regErrEmpty');
                    //console.log(match__);
                    match__ = checkSstr_ = '{("")}';
                    return match__;
                };
                //flag：截止符在哪边  左 &==  ==}右
                var __replaceAddSpace = function(func, matchesArray, match__, flag) {
                    var newMatch__ = match__;
                    matchesArray.forEach(function (tmpVal) {
                        tmpVal = $.trim(tmpVal);
                        var newTmpVal;
                        //console.log('func: '+ func +', tmpVal:'+ tmpVal + ',flag:'+ flag);
                        if(/\(\s*\)/g.test(tmpVal)) {
                            //console.log('has_kh:'+ tmpVal + ',flag:'+ flag);
                            if(flag == 'right') {//==()
                                newTmpVal = tmpVal.substr(0, tmpVal.length-2) + '("")';
                            } else if(flag == 'left') {//() ==
                                newTmpVal = '("")'+ $.trim(tmpVal.substr(2));
                            }
                            //console.log('newTmpVal:'+ newTmpVal);
                        } else {
                            if(flag == 'right') {//==&
                                if(strInArray(tmpVal.substr(0, 2), ['==', '>=', '<=']) !=-1) {
                                    newTmpVal = tmpVal.substr(0, 2) + '""'+ $.trim(tmpVal.substr(2));
                                    //console.log('in:'+newTmpVal);
                                } else {
                                    newTmpVal = tmpVal.substr(0, tmpVal.length-1) + '""'+ $.trim(tmpVal.substr(-1, 1));
                                    //console.log('noin:'+newTmpVal);
                                }
                            } else if(flag == 'left') {//&==
                                if(strInArray(tmpVal.substr(-2, 2), ['==', '>=', '<=']) !=-1) {
                                    newTmpVal = tmpVal.substr(0, tmpVal.length-2) + '""'+ $.trim(tmpVal.substr(-2, 2));
                                    //console.log('in:'+newTmpVal);
                                } else {
                                    newTmpVal = tmpVal.substr(0, tmpVal.length-1) + '""'+ $.trim(tmpVal.substr(-1, 1));
                                    //console.log('noin:'+newTmpVal);
                                }
                            } else {
                                newTmpVal = tmpVal.substr(0, tmpVal.length-1) + '""'+ $.trim(tmpVal.substr(-1, 1));
                            }
                            //console.log('newTmpVal:'+ newTmpVal);
                        }

                        //console.log('tmpVal:'+ tmpVal + ',newTmpVal:'+ newTmpVal);
                        newMatch__ = newMatch__.replace(RegExp(regCodeAddGang(tmpVal), 'g'), newTmpVal);
                    });
                    //console.log('match__:'+ match__ + 'newMatch__:'+ newMatch__);
                    checkSstr_ = checkSstr_.replace(RegExp(regCodeAddGang(match__), 'g'), newMatch__);
                    return newMatch__;
                };
                //检测运算符直接跟}
                var doCheckEnd = function(match__) {
                    if(!regEnd.test(match__) ) {
                        return match__;
                    }
                    //console.log('has err regEnd');
                    //console.log(match__);//{1==}
                    var matchesEnd = match__.match(RegExp(regEnd, 'g'));
                    matchesEnd = uniqueArray(matchesEnd);
                    //console.log('check_before_:'+ match__);
                    var newMatch__ = __replaceAddSpace('doCheckEnd', matchesEnd, match__, 'right');
                    //console.log('check_after_:'+ newMatch__);
                    return newMatch__;
                };
                //检测前面的字符串
                var doCheckFront = function(match__) {
                    if(!regFront.test(match__) ) {
                        return match__;
                    }
                    //console.log('has err regFront');
                    //console.log(match__);
                    var matchesFront = match__.match(RegExp(regFront, 'g'));
                    matchesFront = uniqueArray(matchesFront);
                    var newMatch__ = __replaceAddSpace('doCheckFront', matchesFront, match__, 'left');
                    //console.log('check_after_:'+ checkSstr_);
                    return newMatch__;
                };
                //检测中间的字符串
                var doCheckMid = function(match__) {
                    if(!regMiddleF.test(match__) && !regMiddleB.test(match__) ) {
                        return match__;
                    }
                    if(regMiddleF.test(match__) ) {
                        //console.log('has err regMiddleF');
                        //console.log(match__);
                        //console.log('checkSstr_:'+ checkSstr_);
                        //console.log('match__:'+ match__);
                        var matchesMid = match__.match(RegExp(regMiddleF, 'g'));
                        matchesMid = uniqueArray(matchesMid);
                        match__ = __replaceAddSpace('regMiddleF', matchesMid, match__, 'left');
                    }
                    if(regMiddleB.test(match__) ) {
                        //console.log('has err regMiddleB');
                        //console.log(match__);
                        //console.log('checkSstr_:'+ checkSstr_);
                        //console.log('match__:'+ match__);
                        var matchesMid = match__.match(RegExp(regMiddleB, 'g'));
                        //console.log('matchesMid');
                        //console.log(matchesMid);
                        matchesMid = uniqueArray(matchesMid);
                        match__ = __replaceAddSpace('regMiddleB', matchesMid, match__, 'right');
                    }
                    match__ = doCheckMid(match__);//可能剩下的字符串也包含异常 {""+==?:1}
                    return match__;
                };
                //检测3元的字符串
                var doCheck3Yuan = function(match__) {
                    if((!reg3yuan1.test(match__) && !reg3yuan2.test(match__)  && !reg3yuan3.test(match__)) ) {
                        return match__;
                    }
                    //console.log('doCheck3Yuan:'+ match__);
                    if(reg3yuan1.test(match__)) {
                        //console.log('has err reg3yuan1');
                        //console.log(match__);
                        var matchesMid = match__.match(RegExp(reg3yuan1, 'g'));
                        //console.log('matchesMid');
                        //console.log(matchesMid);
                        matchesMid= matchesMid[0]; // && == 或 && >将空格替换为""即可
                        var newMatch = $.trim(matchesMid);
                        newMatch = matchesMid.replace(RegExp(reg3yuan1, 'g'), '?"":');
                        //console.log('newMatch:'+ newMatch);
                        newMatch = match__.replace(RegExp(reg3yuan1, 'g'), newMatch);
                        checkSstr_ = checkSstr_.replace(match__, newMatch);
                        match__ = newMatch;
                        //console.log('checkSstr_:'+ checkSstr_);
                    }
                    if(reg3yuan2.test(match__)) {
                        //console.log('has err reg3yuan2');
                        //console.log(match__);
                        var matchesMid = match__.match(RegExp(reg3yuan2, 'g'));
                        //console.log('matchesMid');
                        //console.log(matchesMid);
                        matchesMid= matchesMid[0]; // :}
                        var newMatch = $.trim(matchesMid);
                        newMatch = matchesMid.replace(RegExp(reg3yuan2, 'g'), ':""}');
                        //console.log('newMatch:'+ newMatch);
                        newMatch = match__.replace(RegExp(reg3yuan2, 'g'), newMatch);
                        checkSstr_ = checkSstr_.replace(match__, newMatch);
                        match__ = newMatch;
                        //console.log('checkSstr_:'+ checkSstr_);
                    }
                    if(reg3yuan3.test(match__)) {
                        //console.log('has err reg3yuan3');
                        //console.log(match__);
                        var matchesMid = match__.match(RegExp(reg3yuan3, 'g'));
                        //console.log('matchesMid');
                        //console.log(matchesMid);
                        matchesMid= matchesMid[0]; // aa?
                        var newMatch = $.trim(matchesMid);
                        //console.log('newMatch:'+ newMatch);
                        newMatch = match__.replace(RegExp(reg3yuan3, 'g'), function (tmpMac) {
                            return '"'+ strObj.urlencodeLR(encodeNewHtml(tmpMac.substr(0, tmpMac.length-1 ))) + '"?';
                        });
                        checkSstr_ = checkSstr_.replace(match__, newMatch);
                        match__ = newMatch;
                        //console.log('checkSstr_:'+ checkSstr_);
                    }
                    match__ = doCheck3Yuan(match__);//可能剩下的字符串也包含异常 {""+""==""?"":}
                    return match__;
                };
                //检测字母的+法 如：a+ 或 +c
                var doCheckLetter = function(match__) {
                    if(!letterCompare.test(match__)) {
                        //console.log('!has err letterCompare');
                        //console.log(match__);
                        return match__;
                    }
                    //console.log('doCheck Letter:'+ match__);
                    //match__  abc?
                    if(letterCompare.test(match__)) {
                        //console.log('has err letterCompare');
                        //console.log(match__);
                        var matchesLetter = match__.match(RegExp(letterCompare, 'g'));
                        //console.log('matchesLetter');
                        //console.log(matchesLetter);
                        var newMatch2 = match__;
                        matchesLetter = uniqueArray(matchesLetter);
                        matchesLetter.forEach(function (macTmp) {
                            var newMatch;
                            ///(([a-zA-Z_]+[a-zA-Z_\d.]*)\s*(\+|\?|\==|!\=\=|\:|\&\&|\|\|)|(\+|\?|\==|!\=\=|\:|\&\&|\|\|)\s*([a-zA-Z_]+[a-zA-Z_\d.]*))/;
                            var frontReg = /([a-zA-Z_]+[a-zA-Z_\d.]*)\s*(\+|\?|\==|!\=\=|\:|>|<|\&\&|\|\|)/;
                            if(frontReg.test(macTmp)) {
                                //console.log('front_________');
                                newMatch = macTmp.replace(RegExp(frontReg, 'g'), function (match1, match2) {
                                    return match1.replace(match2, '"'+ strObj.urlencodeLR(encodeNewHtml(match2)) + '"');//没有引号的要统一urlencode 因为后面要统一解密
                                });
                                //console.log('newMatch11:'+ newMatch);
                            } else {
                                var backReg = /(\+|\?|\==|!\=\=|\:|>|<|\&\&|\|\|)\s*([a-zA-Z_]+[a-zA-Z_\d.]*)/;
                                //console.log('back_________');
                                newMatch = macTmp.replace(RegExp(backReg, 'g'), function (match1, match2, match3) {
                                    //console.log('match1:'+ match1);
                                    //console.log('match2:'+ match2);
                                    //console.log('match3:'+ match3);
                                    return match1.replace(match3, '"'+  strObj.urlencodeLR(encodeNewHtml(match3)) + '"');//没有引号的要统一urlencode 因为后面要统一解密
                                });
                                //console.log('newMatch22:'+ newMatch);
                            }
                            //console.log('macTmp:'+ macTmp);//abc+
                            newMatch2 = newMatch2.replace(macTmp, newMatch);
                            // match__ = newMatch2;
                            //console.log('newMatch2:'+ newMatch2);// {}
                        });
                        //console.log('checkSstr11_:'+ checkSstr_);
                        checkSstr_ = checkSstr_.replace(match__, newMatch2);
                        match__ = newMatch2;
                        //console.log('checkSstr22_:'+ checkSstr_);
                    }

                    //console.log('doCheck Letter:'+ match__);
                    match__ = doCheckLetter(match__);//可能剩下的字符串也包含异常 {a+b} 替换为 {"a"+ b}
                    return match__;
                };
                //括号里的中文加引号
                var doKuohaoAddYinhao = function(match__) {
                    var kuohaoHasZh = /\(([^\}\{\'\"\)\(]+)\)/; //(这里有中文)不能截止于)因为可能是这样的(中文())
                    if(!kuohaoHasZh.test(match__)) {
                        return match__;
                    }
                    var matchesZh = match__.match(RegExp(kuohaoHasZh, 'g'));
                    matchesZh = uniqueArray(matchesZh);
                    //match__  abc?
                    //console.log(match__);
                    var newMatch2 = match__;
                    matchesZh.forEach(function (chTmp) {
                        if(/^\(([0-9.\-\+]+)\)$/.test(chTmp)) {
                            return; //continue;
                        }
                        //console.log('ch_________');
                        //console.log(chTmp);
                        var newMatch = chTmp.replace(RegExp(/^\((.+)\)$/, 'g'), function (match1, match2) {
                            //把纯数字和 四则混合运算都去掉 如果还剩下的内容 则是字母或中文
                            var checkIfNumber = match2.replace(/([0-9\+\-\*\/\%\^]+)/g, '');
                            //console.log('match1:'+ match1);
                            //console.log('checkIfNumber:'+ checkIfNumber);
                            if(checkIfNumber) {
                                return match1.replace(checkIfNumber, '"'+ strObj.urlencodeLR(encodeNewHtml(checkIfNumber)) + '"');//没有引号的要统一urlencode 因为后面要统一解密
                            } else {
                                return match2;
                            }
                        });
                        //console.log('newMatch:'+ newMatch);
                        //console.log('macTmp:'+ macTmp);//abc+
                        newMatch2 = newMatch2.replace(chTmp, newMatch);
                        // match__ = newMatch2;
                        //console.log('newMatch2:'+ newMatch2);// {}
                    });
                    checkSstr_ = checkSstr_.replace(match__, newMatch2);
                    match__ = newMatch2;
                    //console.log('checkSstr_:'+ checkSstr_);
                    return match__;
                };

                //console.log(jkhMatchs);
                //console.log('checkSstr_:'+ checkSstr_);
                jkhMatchs.forEach(function (match__) {
                    //纯变量不算异常
                    if(/{\s*([a-zA-Z_]+[a-zA-Z_\d.]*)\s*}/g.test(match__)) return;
                    //console.log('match__:'+ match__);
                    match__ = doCheckGth(match__);
                    match__ = doCheckEpyKh(match__);
                    //console.log('doCheckFront_11:'+ checkSstr_);
                    match__ = doCheckFront(match__);
                    //console.log('doCheckFront_22:'+ checkSstr_);
                    match__ = doCheckEnd(match__);
                    //console.log('doCheckEnd:'+ match__);
                    match__ = doCheckMid(match__);
                    match__ = doCheckLetter(match__);
                    //console.log('checkStr2:'+ match__);
                    match__ = doKuohaoAddYinhao(match__);
                    //console.log('checkStr_yh:'+ match__);
                    //console.log('match__:'+ match__);
                    match__ = doCheck3Yuan(match__);
                });
                //console.log('checkSstr_:'+ checkSstr_);
                return checkSstr_;
            }

            //获取尖括号里的运算方法
            function __getjkhFunc(s_) {
                //console.log('__getjkh__Func');
                //console.log('__getjkh Func s_');
                //console.log(s_);
                //提取函数
                var tmpMatch = s_.match(/{([^\{\}]+)}/g);
                tmpMatch = uniqueArray(tmpMatch);
                var jhkArray = [];
                $.each(tmpMatch, function (n, match_) {
                    if (isNumber(match_) || isAbcJhk(match_)) return;//不提取纯abc或纯数字
                    jhkArray.push(match_);
                });
                jhkArray = uniqueArray(jhkArray);
                return jhkArray;
            }
            //格式纯abc123变量
            function formatAbc(s_, dataPub) {
                dataPub = dataPub || 'data';
                var matches;
                if(!isStrOrNumber(s_))  return s_;
                if(isBoolean(s_)) return s_;
                if(!s_) return s_;
                if(typeof s_ == 'number') s_ += '';
                matches = __getjkh(s_, dataPub);//获取字符串里的尖括号，如果{}在引号""里 则只保留纯英文的{aa}
                //console.log('dataPub:'+ dataPub);
                //console.log('matches:');
                //console.log(matches);
                //没有获取到{abc} 且没有{"a"+"b"}的语法 才能return
                if(!hasData(matches)) return s_;
                matches = uniqueArray(matches);
                //console.log('matches 222__________________:'+ dataPub);
                //console.log(matches);
                matches.forEach(function (match__) {
                    //console.log('match__:'+ match__);
                    //console.log('s_:'+ s_);
                    s_ = replaceMatchAbc(s_, match__, dataPub);
                    //console.log('after replace MatchAbc:'+ s_);
                });
                return s_;
            }
            //console.log(' end resultStr :'+ str);
            return str;
        }
    };


    //如果属性含有花括号 替换
    function formatIfHasKuohao(str, data_) {
        if(strHasKuohao(str)) {
            return strObj.formatStr(str, data_);
        } else {
            return str;
        }
    }
    //获取属性值
    function getOptVal(obj_, keyname, defaultVal) {
        if(!obj_) return defaultVal;
        if($.isArray(keyname)) {
            var findKey = false;
            var findVal = false;
            $.each(keyname, function (index_, tmpName) {
                if(!isUndefined(obj_[tmpName])) {
                    //console.log('find:'+ tmpName);
                    //console.log(obj_[tmpName]);
                    //对象要克隆 否则会反作用原对象
                    findKey = true;
                    findVal = isObj(obj_[tmpName]) ? cloneData(obj_[tmpName]) : obj_[tmpName];
                    return false;
                }
            })
            if(findKey) {
                return findVal;
            } else {
                return defaultVal;
            }
        } else {
            if(!isUndefined(obj_[keyname])) {
                //对象要克隆 否则会反作用原对象
                return isObj(obj_[keyname]) ? cloneData(obj_[keyname]) : obj_[keyname];
            }
        }
        return defaultVal;
    }

    //获取post.data的成功标识
    function getCallData(data_) {
        var successKey = getOptVal(data_, ['succ_key', 'succKey', 'success_key', 'successKey'], null);
        var successFunc = getOptVal(data_, ['succ_func', 'succFunc', 'success_func', 'successFunc'], null); //成功回调
        var successVal = getOptVal(data_, ['succ_val', 'succ_value', 'success_val', 'success_value', 'successVal', 'successValue'], null); //成功的判断值
        var errFunc = getOptVal(data_, ['fail_func', 'failFunc', 'err_func', 'errFunc', 'error_func', 'errorFunc'], null);
        if(isNumber(successVal)) successVal +='';
        return {
            'success_key': successKey,
            'success_value': successVal,
            'success_func': successFunc,
            'err_func': errFunc
        };
    }

    //判断对象的属性是否改变 未作任何改动时返回true
    function optionIsSame(obj, option, attrName, index) {
        if(isUndefined(option[attrName])) {
            return true;
        }
        //该参数未设置 判为不作修改
        var lastOption = obj['last_options'] || '';
        var lastVal = lastOption[attrName] || '';
        if(isUndefined(index)) {
            var same =  !isUndefined(lastOption[attrName]) && lastVal === option[attrName];
            // if(attrName=='value') {
            //     console.log('same:',lastOption, option);
            // }
            return same;
        } else {
            return lastVal[index] === option[attrName][index];
        }
    }
    //对旧的样式进行加减新样式
    function classAddSubClass(oldClass, newClass, add, splitStr) {
        splitStr = splitStr || ' ';
        if(strHasKuohao(oldClass)) oldClass = '';
        if(strHasKuohao(newClass)) newClass = '';
        add = add || false;
        oldClass = oldClass || '';
        newClass = newClass || '';
        if(!newClass) return oldClass;
        var oldClassArray = [];
        if(oldClass) {
            oldClassArray = oldClass.split(splitStr);
        }
        if(newClass.indexOf(splitStr) !=-1) {
            newClass.split(splitStr).forEach(function (cla_) {
                var index_ = $.inArray(cla_, oldClassArray);
                if(add) {
                    if(index_ ==-1)  oldClassArray.push(cla_);
                } else {
                    if(index_ !=-1) oldClassArray.splice(index_, 1);
                }
            })
        } else {
            var index_ = $.inArray(newClass, oldClassArray);
            if(add) {
                if(index_ ==-1)  oldClassArray.push(newClass);
            } else {
                if(index_ !=-1) oldClassArray.splice(index_, 1);
            }
        }
        var result = $.trim(oldClassArray.join(splitStr));
        result = trim(result, splitStr);
        return result;
    }
    //判断文本是否包含html
    function isHtml(text) {
        var  reg = /<[^>]+>/g;
        return reg.test(text);
    }
    //判断文本是否包含{}
    function strHasKuohao(text, dataPub) {
        if(typeof text != 'string') return false;
        dataPub=  dataPub || null;
        var  reg1, reg2;
        var hasKh1, hasKh2;
        reg1 = /{[^\}]+}/g;
        reg2 = /{{[^\}]+}}/g;
        if(dataPub) {
            if(dataPub == 'data') {
                return reg1.test(text);
            } else {
                return reg2.test(text);
            }
        } else {
            hasKh1 = reg1.test(text);
            hasKh2 = reg2.test(text);
            return (hasKh1 || hasKh2);
        }
    }
    //创建文本节点
    function makeTextNodu(text) {
        return document.createTextNode(text)
    }
    //设置文本节点
    function setTextContent (node, text) {
        //console.log('node_:::::');
        //console.log(node);
        //插入内容 当内容包含htm的dom节点时 比如<i></i> 需要启动html置换
        function insertHtmlToNode(node_, html_) {
            if(!html_) html_ = '';
            var htmlString = html_.toString();
            node_.textContent = '';
            if(node_.htmlObj) {
                $(node_.htmlObj).remove();
            }
            if(isHtml(htmlString)) {
                //console.log('htmlString');
                var tmpDom = $('<span></span>');
                tmpDom.append(html_);
                node_.htmlObj = tmpDom;
                $(node_).after(tmpDom);
            } else {//纯文本直接设置内容
                //console.log('纯文本直接设置内容',node_, html_);
                node_.textContent = html_;
            }
        }

        if($.isArray(node)) { //div的node是数组
            //console.log('array');
            //console.log(node);
            node.forEach(function (v) {
                insertHtmlToNode(v.obj, text);
            });
        } else {
            if(text == null) {
                text == '';
            }
            //console.log('insertHtmlToNode', node, text);
            insertHtmlToNode(node, text);
        }
    }
    //同步节点数据
    function updateNodeText(obj, bindName) {
        setTextContent(obj.nodeObj, getObjData(bindName));
    }
    //获取对象的html的所有节点 并且给当前对象提取textNode
    function getObjHtmlNode(obj_, node, data) {
        var flag = document.createDocumentFragment();
        var child;
        // 循环时，node.firstChild 一直是被剪切后的下一个
        //console.log(node);
        //console.log('node.firstChild');
        //console.log(node.firstChild);

        // 123a<i>asdasd</i>这样的格式
        // if(node.firstChild && node.firstChild.nodeType==3) {
        //     return;
        // }
        while (child = node.firstChild) {
            compile(child, data);
            flag.appendChild(child); // 将子节点劫持到文档片段中
        }
        function compile (node, data) {
            // 节点类型为元素 div/p/li/ul/ <input>
            if (node.nodeType === 1) {
                //console.log('node.nodeType is 1' );
                var attr = node.attributes;
                // 遍历属性 如果有定义｛｝要存储 下次编译
                var addElement = false;
                var tmpObj = {'obj': node, 'attrs': []};
                for (var i = 0; i < attr.length; i++) {
                    var valueStr = attr[i].nodeValue;
                    //console.log('valueStr:'+ valueStr );
                    if(strHasKuohao(valueStr)) {
                        addElement = true;
                        tmpObj['attrs'].push({
                            'name': attr[i].nodeName,
                            'text': attr[i].nodeValue
                        });
                    }
                }
                if(addElement) {
                    obj_.htmObj.push(tmpObj);
                }
                // obj_.htmObj.push(tmpObj);
                //解析子内容
                node.appendChild(getObjHtmlNode(obj_, node, data));
            }
            //console.log(node);
            //console.log('node.nodeType:'+ node.nodeType);
            // 节点类型为 纯文本 text
            if (node.nodeType === 3) {
                obj_.nodeObj.push({
                    'text': node.nodeValue,
                    'obj': node
                });
            }
        }
        return flag;
    }
    //更新对象的所有文本节点
    function formatObjNodesVal(thisObj,  dataLive, hasSetData) {
        var data = [];
        if(dataLive) data = cloneData(dataLive); //data可能来源于living Obj 所以要重新克隆一个新的data 防止index回传
        if(thisObj.nodeObj && thisObj.nodeObj.length >0) {
            $.each(thisObj.nodeObj, function (n, item) {
                var nodeText = item.text;//textNode原始字符串 如{abc}未编译
                var node = item.obj;
                if(strHasKuohao(nodeText)) {
                    thisObj[objAttrHasKh] = true;
                }
                var newStr = nodeText;
                if(hasSetData) {
                    newStr =  strObj.formatStr(nodeText, data, n, thisObj, 'value');
                }
                newStr = htmlDecode(newStr);
                //console.log('htmlDecode :'+ newStr , 'nodeText:'+nodeText);
                //只有文本被修改才更新node的文本 防止没必要的操作dom 带<>标记的内容要在此格式化
                if(nodeText !== newStr) {
                    setTextContent(node, newStr);
                }
            });
        }

        //格式化标签，如: <a >
        thisObj.htmObj.forEach(function (objItem) {
            var node = objItem.obj;
            //console.log('objItem :');
            //console.log(node);
            var attrs = objItem.attrs;
            for (var i = 0; i < attrs.length; i++) {
                var valueStr = attrs[i].text;
                //console.log('here : valueStr:'+ valueStr);
                if(strHasKuohao(valueStr)) {
                    thisObj[objAttrHasKh] = true;
                }
                var newStr = strObj.formatStr(valueStr, data);
                if(valueStr != newStr) node.setAttribute(attrs[i].name, newStr);
            }
        });
    }
    //对比data数组和对象是否一致
    var dataIsSame= function( x, y ) {
        // If both x and y are null or undefined and exactly the same
        if ( x === y ) {
            return true;
        }
        if(isUndefined(x) && JSON.stringify(y) === '[]') return true;
        if(isUndefined(y) && JSON.stringify(x) === '[]') return true;
        // If they are not strictly equal, they both need to be Objects
        if ( ! ( x instanceof Object ) || ! ( y instanceof Object ) ) {
            return false;
        }
        //They must have the exact same prototype chain,the closest we can do is
        //test the constructor.
        if ( x.constructor !== y.constructor ) {
            return false;
        }
        for ( var p in x ) {
            //Inherited properties were tested using x.constructor === y.constructor
            if ( x.hasOwnProperty( p ) ) {
                // Allows comparing x[ p ] and y[ p ] when set to undefined
                if ( ! y.hasOwnProperty( p ) ) {
                    return false;
                }

                // If they have the same strict value or identity then they are equal
                if ( x[ p ] === y[ p ] ) {
                    continue;
                }

                // Numbers, Strings, Functions, Booleans must be strictly equal
                if ( typeof( x[ p ] ) !== "object" ) {
                    return false;
                }

                // Objects and Arrays must be tested recursively
                if ( ! dataIsSame( x[ p ], y[ p ] ) ) {
                    return false;
                }
            }
        }
        for ( p in y ) {
            // allows x[ p ] to be set to undefined
            if ( y.hasOwnProperty( p ) && ! x.hasOwnProperty( p ) ) {
                return false;
            }
        }
        return true;
    };

    //强制给Obj加data参数
    function optionAddData(opt, optData) {
        // if(!hasData(optData)) return [opt, false];//无data返回  //2019.3.16 无data也要返回change
        var dataIsChange = false;//数据是否继承父
        var optDataString = opt['source_data']||opt['data'];
        //console.log('optDataString', optDataString);
        if(isStrOrNumber(optDataString)) {
            //console.log(optDataString);
            //console.log(optData);
            opt['data'] = strObj.formatStr(optDataString, optData);
            //console.log(opt['data']);
            dataIsChange = true;
        } else {
            //console.log(opt['data']);
            //console.log(optData);
            if(optDataString) {//has set data
                if((hasData(optDataString) && !dataIsSame(optDataString, optData))
                    || (!hasData(optDataString) && !dataIsSame(optData, makeNullData()))
                ) {
                    //console.log('!==');
                    opt['data'] = optData;
                    dataIsChange = true;
                }
            }  else {
                //console.log('set___optData');
                //console.log(optData);
                opt['data'] = optData;
                dataIsChange = true;
            }
        }
        return [opt, dataIsChange];
    }
    //序号操作类
    var indexClass = {
        regs: /\[\d+\]$/,
        //name+序号
        nameAddNum: function (objName) {
            var newName;
            //console.log('objName:'+ objName);
            if(this.regs.test(objName)) {
                //console.log('has_index:'+ objName);
                newName = objName.replace(this.regs, function() {
                    return '['+ (parseInt((arguments[0]).replace(/\[|\]/g, ''))+1) +']';
                });
            } else {
                //console.log('nohas_index:'+ objName);
                newName = objName + '[0]';
            }
            if(global[newName]) {
                //console.log('exist_name:'+ newName);
                newName = this.nameAddNum(newName);
                //console.log('adddd:'+ newName);
            }
            return newName;
        },
        //name去括号
        nameRemoveNum: function(objName) {
            var newName;
            //console.log('objName:'+ objName);
            if(this.regs.test(objName)) {
                newName = objName.replace(this.regs, '');
            } else {
                newName = objName;
            }
            return newName;
        },
        //name取序号
        nameGetNum: function(objName) {
            var newName;
            //console.log('objName:'+ objName);
            if(this.regs.test(objName)) {
                newName = parseInt(objName.match(this.regs)[0].replace(/\[|\]/g, ''));
            } else {
                newName = 0;
            }
            return newName;
        },
        //name包含序号
        nameHasNum: function(objName) {
            return this.regs.test(objName);
        }
    };



    //判断是否定义尺寸
    function setSize(str) {
        return sizeIsXs(str) || sizeIsSm(str) || sizeIsMd(str) || sizeIsLg(str) ;
    }
    //判断尺寸 小
    function sizeIsXs(str) {
        return strInArray(str, ['x', 'xs']) !==-1;
    }
    //判断尺寸 小
    function sizeIsSm(str) {
        return strInArray(str, ['s', 'sm', 'small']) !==-1;
    }
    //判断尺寸 中
    function sizeIsMd(str) {
        return strInArray(str, ['m', 'md', 'middle', 'normal']) !==-1;
    }
    //判断尺寸 大
    function sizeIsLg(str) {
        return strInArray(str, ['l', 'lg', 'large', 'big']) !==-1;
    }
    //格式化ajax post 带上随机数和默认返回json格式
    global.rePost= function(url, postData, callBack) {
        if(!url) return;
        $.post(url, postData, callBack, 'json');
    };
    //封装post之后的动作
    global.postAndDone =function(options, obj) {
        //[属性：success_value,post_url,post_data,msg,msg_hide,func]
        // success_value //成功时的回调值，'0308'/ ['0043', '0113']
        // post_url //post 接口
        // load_bg //load是否加背景
        // post_data //post 数据包
        // success_func //成功时执行的动作
        //err_func://失败时执行的动作
        options = options || {};
        obj = obj || {};
        var callKeys = getCallData(options);
        var successKey = callKeys['success_key'];
        var successVal = callKeys['success_value'];
        var successFunc = callKeys['success_func'];
        if(!$.isArray(successVal)) {
            if(!successVal) successVal = '1';
            if(isStrOrNumber(successVal)) {
                successVal = successVal.split(',');
            } else {
                successVal = successVal.toString().split(',');
            }
        }
        var postUrl = options['post_url'] || options['url'] || '';
        var postData = getOptVal(options, ['post_data', 'postData'], null);
        var errFunc = getOptVal(options, ['err_func', 'errFunc', 'error_func', 'errorFunc'], null);//失败回调
        return global.rePost(postUrl, postData,function(data) {
            if(!data) {
                console.log('post result: no data');
                return;
            }
            if(successVal && successKey && (isUndefined(data[successKey]) || strInArray(data[successKey], successVal) == -1)) {
                if(errFunc) errFunc(data, obj, livingObj);
            } else {
                //可能这里会执行关闭所有（最新）窗口，所以要提前执行，防止将默认的提示语误关。
                if(successFunc) {
                    if(isString(successFunc)) {
                        eval(successFunc);
                    } else {
                        successFunc(data, obj, livingObj);
                    }
                }
            }
        });
    };
    //生成随机字符
    global.makeRadom = function(len) {
        len = len || 10;
        return (Math.random()*1000000000).toString().substr(0, len).replace(/\./g, '');
    };

    function createRadomName(tag, num) {
        num = num ||'';
        var newName = 'lr_'+ tag +'_'+ global.makeRadom(7) + num;
        //名字已经存在，并且在页面中 （如果层被关闭，可能有gloal对象 但不存在页面中了，这时候可以继续覆盖原对象name）
        if(global[newName]) {
            //console.log('exist:'+ global[newName]);
            return createRadomName(tag);
        }
        return newName;
    }
    var styleHengAttrs = "z_index|z-index|border_|border-|margin_|margin-|padding_|padding-|max_|max-|" +
        "transition_|transition-|grid_|grid-|font_|font-|box_|box-|letter_|letter-|line_|line-|min_|" +
        "column_|column-|counter_|counter-|page_|page-|word_|word-|perspective_|perspective-|nav_|nav-|" +
        "min-|list_|list-|target_|target-|rotation_|rotation-|overflow_|overflow-|vertical_|vertical-|white_|white-|text_|text-|background_|background-|animation_|animation-";
    styleHengAttrs = styleHengAttrs.split('|');
    var styleIgnore = ['text_key'];//系统的文本键名 不是style来的 要忽略
    //attr属性中不能添加为obj.attr的属性 它们会自动转入style
    var cantAddCssAttrs = [
        'display', 'position', 'width', 'height', 'left', 'top', 'margin', 'padding',
        'color', 'zindex', 'background','border',
        'overflow',
        'resize',
        'animation',
        'rotation',
    ];
    //找出能添加到对象的属性 css类不可以
    function canAddAttr(attrName) {
        var hasStr = false;
        styleHengAttrs.forEach(function (n_) {
            var reg_ = new RegExp('^'+ n_, "gm");
            if(attrName.match(reg_)) {
                // if(n_ =='colspan') console.log(attrName);
                hasStr = true;
            }
        });
        cantAddCssAttrs.forEach(function (n_) {
            var reg_ = new RegExp('^'+ n_, "gm");
            if(attrName.match(reg_)) {
                // if(n_ =='colspan') console.log(attrName);
                //console.log('hasstr:'+ attrName);
                hasStr = true;
            }
        });
        // if(attrName =='colspan') console.log(hasStr);
        var inShowStr = $.inArray(attrName,
            ['value', 'text', 'show', 'value_key', 'valueKey', 'title_key', 'titleKey', 'text_key', 'textKey', 'click', 'is_clone', 'data', 'hide', ignoreBindValsKeyname,
                'source_data_text', 'obj_val_is_node', 'success_key', 'tag'
            ]) ==-1;
        //console.log('inShowStr:'+ inShowStr);
        return inShowStr && attrName.indexOf('extend') == -1 && !hasStr;
    }
    //call renew val
    function callRewObjStringVal(obj_, options) {
        // console.log('callRewObj.StringVal');
        // console.log(obj_, obj_[objValIsNode]);
        if(obj_[objValIsNode]) {
            //未渲染
            if(strHasKuohao(options['value'], 'data') && !dataIsSame(obj_['last_options']['data'], options['data'])) {//局部的data变了 才格式化
                // console.log('formatObj.NodesVal');
                // console.log(options['data']);
                formatObjNodesVal(obj_, options['data'], !isUndefined(options['data']));
            } else if(strHasKuohao(options['value'], 'public')) {//全局的data不变化 无须格式化
                formatObjNodesVal(obj_, options['data'], !isUndefined(options['data']));
            } else {
                //console.log('已渲染 node');
                domAppendNode(obj_, options);
            }

        } else {
            // console.log('callRew ObjStringVal');
            // console.log(obj_);
            // console.log(obj_.formatVal);
            if(obj_.formatVal) {
                obj_.formatVal(options);
            }
        }
    }
    //更新对象的属性 （假如属性中包含全局变量{{abc}} ）
    function renewObjBindAttr(obj_, renewBindVal) {
        //console.log('goto renewObjBindAttr:');
        //console.log(obj_);
        renewBindVal = renewBindVal || '';
        var objBindData = obj_[objBindAttrsName] || [];
        if(!objBindData || !hasData(objBindData)) return;
        var objBindAttrs = objBindData[renewBindVal] || [];
        if(!objBindAttrs || !hasData(objBindAttrs)) return;
        var newAttr = {};
        var options = $.extend({}, obj_['options']);
        var optData = options['data'] || makeNullData();
        var v;
        var hidden = false;
        var setHidden = false;//设置隐藏样式
        var setDisabled = false;//设置不可点击样式
        var setChecked = false;//设置打勾样式
        var sourceVal;
        objBindAttrs.forEach(function (attrName) {
            sourceVal = options['source_'+ attrName];
            if(attrName=='value') { //value的 {bind_val} 变化  要其自己更新val
                if(isStrOrNumber(sourceVal) || $.isArray(sourceVal)) { //checkbox是数组
                    callRewObjStringVal(obj_, options);
                }
                return;//continue
            }
            v = strObj.formatStr(sourceVal, optData, 0, obj_, attrName);
            //console.log('attrName:'+ attrName + ':'+ v);
            //hide or show
            if(attrName =='show' || attrName =='hide') {
                console.log('show v', v);
                setHidden = true;
                if((attrName =='hide' && (v == 'true' ||  v == true))
                    || (attrName =='show' && (v == 'false' ||  v == false))
                ) hidden = true;
            }
            //disabled
            if(attrName =='disabled') {
                if(v == 'false' || !v || v==0 || v =='0') {
                    setDisabled = false;
                } else {
                    setDisabled = true;
                }
            }
            //checked
            if(attrName =='checked') {
                //console.log('checked:'+ v);
                if(v == 'false' || !v || v==0 || v=='0') {
                    setChecked = false;
                } else {
                    setChecked = true;
                }
            }
            //扩展属性不需要显示
            if(isStrOrNumber(v) && canAddAttr(attrName)) {
                newAttr[attrName] = v;
                obj_['options'][attrName] = v; //允许外部重新获取属性的值
            }
        });
        //刷新属性时 如果没有刷新class 则要补充之前的class
        if( !('class' in objBindAttrs) ) {
            var lastClass = options['class'];
            //console.log('lastClass:'+ lastClass);
            if(lastClass) newAttr['class'] = classAddSubClass(newAttr['class'], lastClass, true, ' ');
        }
        var lastClassTrueVal = '';
        if(obj_['class_extend_true_val']) {
            lastClassTrueVal = obj_['class_extend_true_val'];
            newAttr['class'] = classAddSubClass(newAttr['class'], lastClassTrueVal, true, ' ');
        }
        if(setHidden) {
            if(hidden) {
                newAttr['class'] = classAddSubClass(newAttr['class'], 'hidden', true, ' ');
            } else {
                newAttr['class'] = classAddSubClass(newAttr['class'], 'hidden', false, ' ');
            }
        }
        if(!setDisabled) {
            delete newAttr['disabled'];
            if(obj_) obj_.removeAttr('disabled');
        }
        if(!setChecked) {
            delete newAttr['checked'];
            if(obj_) obj_.removeAttr('checked');
        }
        if(hasData(newAttr)) {
            obj_.attr(newAttr);
        }
    }


    //属性捆绑读写 参数设置 和更新
    function optionGetSet(thisObj, options, bindDataKey) {
        bindDataKey = bindDataKey || 'value';//绑定全局变量的属性key obj设置了bind 那么全局变量的值会同步更新这个属性
        var setOptins = $.extend({}, options);//用于设置的参数
        if(isUndefined(setOptins['class'])) setOptins['class'] = '';//默认要带上class 否则属性无法被外部修改
        //补充source_n
        thisObj[objHasKhAttrs] = [];
        thisObj.diyClass = '';//最初定义的class 用于后期渲染class
        var tmpAttr = [];
        $.each(setOptins, function (opt_, val_) {
            if (strHasKuohao(val_)) {
                if(opt_.substr(0, 7) !== 'source_') {
                    tmpAttr.push(opt_);
                } else {
                    tmpAttr.push(opt_.replace(/^source_/g, ''));
                }
            } else {
                if(opt_=='class') {
                    thisObj.diyClass = val_;
                }
            }
        });
        tmpAttr = uniqueArray(tmpAttr);
        thisObj[objHasKhAttrs] = tmpAttr;
        if(thisObj.hasOwnProperty('options')) {
            var oldOptions = $.extend({}, thisObj['options']);
            var newOptions = $.extend({}, options);
            //更新旧版options
            var changeOptions = [];
            $.each(newOptions, function (keyName, val_) {
                //name 等自动更新的参数无需触发更新
                if($.inArray(keyName, optionsChangeNoRenew) !=-1) return;
                //属性未变
                if(oldOptions.hasOwnProperty(keyName) && oldOptions[keyName] === val_) {
                    return;
                }
                changeOptions[keyName] = val_;
            });
            //console.log(changeOptions);
            if(changeOptions.length>0) {
                oldOptions = $.extend(oldOptions, changeOptions);
                thisObj['options'] = oldOptions;
            }
        } else {
            Object.defineProperty(thisObj, 'options', {
                get: function () {
                    return options;
                }
                ,set: function (newOption) {
                    //console.log('newOption:: ');
                    //console.log(newOption);
                    thisObj.renew(newOption);//参数修改，统一全部更新
                }
            });
        }
        //强制绑定data
        if(!thisObj.hasOwnProperty('data')) {
            // console.log('bindData-----');
            // console.log(thisObj);
            Object.defineProperty(thisObj, 'data', {
                get: function () {
                    var optData = options['data'];
                    if(isStrOrNumber(optData)) return optData;
                    return cloneData(optData);//data要克隆取， 否则同步修改对象，导致不变
                },
                set: function (newVal) {
                    // console.log('call obj to renew data:', thisObj, newVal);
                    renewObjData(thisObj, newVal); //直接更新data 里面已经 更新属性  format AttrVals(thisObj, options);
                    options['data'] = newVal; //无同步更新  不能立即更新data，renew ObjData 还需要对比data
                }
            });
        }
        $.each(setOptins, function (opt_, val_) {
            if(thisObj.hasOwnProperty(opt_)) {
                return;
            }
            if(opt_ == 'value' || opt_=='data') {
                //continue value是不需要默认存取的 全部自己定义
                //data在上面自定义
                return;
            }
            Object.defineProperty(thisObj, opt_, {
                get: function () {
                    //如果当前数值已经绑定 读取公共的数据
                    if(options['bind'] && bindDataKey == opt_ ) {
                        //console.log('get::bindDataKey'+opt_ + ':' + val_);
                        return getObjData($.trim(options['bind']));
                    }
                    return options[opt_];
                },
                set: function (newVal) {
                    options[opt_] = newVal; //无同步更新  不能立即更新data，renew ObjData 还需要对比data
                    //name 等自动更新的参数无需触发更新
                    if($.inArray(opt_, optionsChangeNoRenew) != -1) { //系统属性改变 无须触发更新
                        return;
                    }
                }
            });
        });
    }

    //dom的data来源方式封装
    function optionDataFrom(obj_, options_) {
        //如果对象设置有对象格式的data，则给它一个标识：不需要继承父data
        var extendParentData = true; //是否需要继承父data
        if(!isUndefined(options_['data'])) {//has:data
            if(typeof options_['data'] != 'string') {
                //console.log('no_str');
                if(hasData(options_['data'])) extendParentData = false; //data为[]的话也要算继承父data的 因为默认TD的data就是[]
            } else {
                if(strHasKuohao(options_['data'])) {
                    extendParentData = true; //{abc}
                } else {
                    extendParentData = false;
                }
            }
        } else if(!isUndefined(options_['data_from']) || !isUndefined(options_['dataFrom'])) {
            extendParentData = false;
        }
        if(isUndefined(options_['extendParentData'])) {
            // console.log('set.extendParentData', extendParentData);
            options_['extendParentData'] = extendParentData;
        }
        // if(isUndefined(options_['data'])) options_['data'] = []; //强制加data,允许外部修改data
        var data_ = getOptVal(options_, ['data'], {}); //data
        //console.log('obj.optionDataFrom');
        //console.log(obj_);
        var dataFrom = getOptVal(options_, ['data_from', 'dataFrom'], null); //data来源
        var pageMenu = getOptVal(options_, ['pageMenu', 'pagemenu', 'page_menu'], null); //menudata来源于url
        var dataFromFunc = getOptVal(dataFrom,'func', null);
        var dataFromUrl = getOptVal(dataFrom,'url', '');
        var dataBeforeDecode = getOptVal(dataFrom, 'dataBefore', null);//数据处理前的解密方法
        var postParameter = getOptVal(dataFrom, ['post_data', 'postData'], {});
        var callKeys = getCallData(dataFrom);
        var successKey = callKeys['success_key'];
        var successValue = callKeys['success_value'];
        var successFunc = callKeys['success_func'];
        var errFunc = callKeys['err_func'];

        //console.log(JSON.stringify(options_));
        var dataFromDataKey = getOptVal(dataFrom, ['data_key', 'dataKey'], null);
        var menuDataFromKey = getOptVal(pageMenu, ['data_key', 'dataKey'], null);
        var menuDataPageKey = getOptVal(pageMenu, 'page_post_key', 'page');
        //渲染data
        var __formatDataFunc = function (response, callFunc) {
            var getFromData;
            var getPMenuFromData;
            if(dataFromDataKey) {
                //console.log('dataFromDataKey:'+ dataFromDataKey);
                if(dataFromDataKey.indexOf('.') !=-1) {
                    var array_ = dataFromDataKey.split('.');
                    getFromData = response;
                    $.each(array_, function (n, key_) {
                        getFromData = !isUndefined(getFromData[key_]) ? getFromData[key_] : {};
                    });
                } else {
                    getFromData = hasData(response[dataFromDataKey]) ? response[dataFromDataKey] : {};
                }
            } else {
                getFromData = response;
            }
            // console.log('getFromData:');
            // console.log(getFromData);
            if(pageMenu) {
                if(menuDataFromKey.indexOf('.') !=-1) {
                    var array_ = menuDataFromKey.split('.');
                    getPMenuFromData = response;
                    $.each(array_, function (n, key_) {
                        getPMenuFromData = getPMenuFromData[key_];
                    });
                } else {
                    getPMenuFromData = hasData(response[menuDataFromKey]) ? response[menuDataFromKey] : {};
                }
            }

            var responseSet = cloneData(getFromData);
            //console.log('renew_obj_data_____________');
            //console.log(obj_);
            //console.log(getFromData);
            obj_['data'] = responseSet;
            if(pageMenu) {
                //console.log(obj_);
                if(obj_['pageMenu']) {
                    obj_['pageMenu']['data'] = getPMenuFromData;
                } else {
                    //console.log('no_menu_____________obj');
                }
            }
            if(callFunc) {
                callFunc(responseSet, obj_);
            }
            if(obj_.lazyCall) {
                obj_.lazyCall(obj_, responseSet, livingObj);
            }
        };
        if(dataFromFunc) {
            //console.log('dataFromFunc');
            //console.log(dataFromFunc);
            //允许外部刷新数据
            obj_.renewData = function (callFunc) {
                var getFromData = dataFromFunc(obj_);
                //console.log('getFromData');
                //console.log(getFromData);
                if(!isUndefined(getFromData)) __formatDataFunc(getFromData, callFunc);
                //console.log('renewData');
                //console.log(obj_);
            };
            //要等父出现才能执行
            setTimeout(function () {
                //console.log('renewData');
                //console.log(obj_);
                obj_.renewData();
            }, 10);

        } else if(dataFromUrl) {
            dataFromUrl = formatIfHasKuohao(dataFromUrl, data_);
            function _renewMyUrlData(callFunc, page) {
                //console.log('_renewMyUrlData ______________');
                //console.log(obj_);
                //console.log(dataFromUrl);
                page = page || null;
                if(page) postParameter[menuDataPageKey] = page;
                var postData = {
                    post_url: dataFromUrl,
                    post_data: postParameter,
                    successFunc: function (response) {
                        if(dataBeforeDecode) {
                            response = dataBeforeDecode(response);
                        }
                        // console.log('response', response);
                        __formatDataFunc(response, callFunc);

                    }, errFunc: function (response) {
                        if(errFunc) errFunc(response, obj_);
                    }
                };
                if(successKey) postData['success_key'] = successKey;
                if(successValue) postData['success_value'] = successValue;
                if(errFunc) postData['err_func'] = errFunc;
                global.postAndDone(postData, obj_);
            }
            //允许外部刷新数据
            obj_.renewData = function (callFunc, page) {
                page = page || 1;
                _renewMyUrlData(callFunc, page);
            };
            var needParentVal = obj_.INeedParentValFlag || false;
            //不需要取父值参数的情况，可直接请求提交
            if(!needParentVal) {
                _renewMyUrlData(successFunc);
            } else {
                //select.son专用延迟更新菜单的方法
                //否则等待父值渲染成功再取值,触发此接口即可更新此对象的data
                obj_.getDataWithParentVal = function (newParentVal, func, page) {

                    page = page || null;
                    if(page) postParameter[menuDataPageKey] = page;
                    postParameter[(obj_.INeedParentKey||'id')] = newParentVal;
                    var postData = {
                        post_url: dataFromUrl,
                        post_data: postParameter,
                        success_func: function (response) {
                            //console.log('response');
                            //console.log(response);
                            __formatDataFunc(response);
                        }, err_func: function (response) {
                            if(errFunc) errFunc(response, obj_);
                        }
                    };
                    if(successKey) postData['success_key'] = successKey;
                    if(successValue) postData['success_value'] = successValue;
                    if(errFunc) postData['err_func'] = errFunc;
                    //console.log(postData);
                    global.postAndDone(postData, obj_);
                };
            }
        }  else if(typeof dataFrom == 'string') { //select专用
            //console.log('dataFrom', obj_, dataFrom);
            //value不需要渲染时才可以执行延迟事件 否则要待渲染完成才可以执行此方法
            //否则等待父值渲染成功再取值,触发此接口即可更新此对象的data
            obj_.getDataFromParentData = function (parentObj, newParentVal, sonObj) {
                var valueKey =  getOptVal(parentObj.options, ['value_key', 'valueKey']);
                var parentData =  parentObj.menu.menu.data;
                var findData = null;
                //console.log('get DataFrom ParentData', obj_, parentData);
                if(hasData(parentData)) {
                    $.each(parentData, function(key, tmp) {
                        if(tmp[valueKey] == newParentVal) {
                            if(!isUndefined(tmp[dataFrom])) {
                                findData = tmp[dataFrom];
                            }
                            return false;
                        }
                    });
                }
                if(findData) {
                    //console.log('findData', sonObj, findData);
                    sonObj.menu_data = findData;
                    sonObj.menu.menuXuanranSuccess = true;
                } else {
                    //console.log('no_findData', parentObj,parentData, valueKey, newParentVal);
                }
            };

        }
    }

    //更新对象的data时 重新渲染对象的{}
    function renewObjData(obj, newData) {
        if(!isOurObj(obj)) return;//非自定义的对象不能更data
        if(isStrOrNumber(newData)) return;//非data
        var options = cloneData(obj['options']);
        var newPushData = cloneData(newData);
        // console.log('renew-ObjData', obj, options, JSON.stringify(newPushData));
        // console.log(obj[objAttrHasKh]);
        //options在此赋值data
        var OptBack = optionAddData(options, newPushData);
        var newOpt = OptBack[0];
        newPushData = newOpt['data'];
        if(obj[objAttrHasKh]) {
            strObj.reFormatKhAttr(obj, newOpt);
        }
        //data更新 裁剪或增加可循环的子对象的长度
        if(obj.renewSonLen) { //如果对象支持对象更新的扩展事件，如makeList/makeTable的裁剪数量，要修改list的长度
            //console.log('renew.SonLen');
            //console.log(obj);
            obj.renewSonLen(options);
        } else if(obj.renewSonData) { //更新长度和更新子data是上包含下的
            obj.renewSonData(newPushData);
        }
        //page专用
        if(obj.renewPageData) {
            //console.log(obj);
            //console.log(newData);
            obj.renewPageData(newData);
        }
        //更新最后的opt
        obj['last_options']['data'] = cloneData(newPushData);//设置完所有属性 要更新旧的option
    }
    //重置obj底下所有son的name
    function changeChildSonsName(tmpObj, newI) {
        var changeTmpNames = [];
        var sons = tmpObj[objValObjKey];
        var tmpNewName;
        if(hasData(sons)) {
            //console.log(sons);
            hasData(sons) && sons.forEach(function (sonObj) {
                if(!sonObj['name']) {//span
                    changeChildSonsName(sonObj, newI);
                    return; //continue
                } else {
                    var objNameFront = indexClass.nameRemoveNum(sonObj['name']);
                    tmpNewName = (objNameFront + '['+ newI +']');
                    changeTmpNames.push({
                        'name': tmpNewName,
                        'obj': sonObj
                    });
                    sonObj['name'] = tmpNewName;
                    sonObj.attr('name', tmpNewName);
                    if(sonObj.hasClass('diy_input_box') || sonObj.hasClass('diy_upload_input') ) {
                        sonObj.find('.diy_input').each(function (n, tmpTag) {
                            var tmpInput = $(tmpTag);
                            if(tmpInput.attr('name').match(/(\[\d+\])$/)) {
                                tmpInput.attr('name', tmpNewName);
                            }
                        })
                    }
                }
            });
            //重置全局对象name=>obj
            changeTmpNames && changeTmpNames.forEach(function (datum) {
                global[datum['name']] = datum['obj'];
            });
            //console.log(tmpObj);
        }
    }
    //dom补充克隆功能
    function addCloneName(thisObj, defaultOps) {
        defaultOps = defaultOps || {};
        //全局对象支持引用
        if(defaultOps['name']) {
            global[defaultOps['name']] = thisObj;
            //同名为何要累加？如果是二次创建同名的元素呢？
        }
        thisObj.extend({
            //调整后面的兄弟的排序 flag true加1 false减1
            renewRestObjIndex: function(oldIndex, flag) {
                var parentObj = thisObj['parent'];//其父
                if(!isOurObj(parentObj)) return;
                var objName = thisObj['name'];
                var sonsLen = parentObj[objValObjKey].length;
                var i_, tmpObj, tmpNewName;
                var objNameFront = indexClass.nameRemoveNum(objName);
                //console.log(objNameFront);
                var changeNames = [];
                if(flag == true) {//插队
                    for (i_ = parseInt(oldIndex)+1; i_ < parseInt(sonsLen); i_ ++) {
                        tmpObj =  global[(objNameFront+'['+ i_ +']')];
                        tmpNewName = (objNameFront+'['+ (i_+1) +']');
                        //console.log('find tmpNewName:'+ tmpNewName);
                        //console.log(tmpObj);
                        if(tmpObj) {// 1-2-3-4 前面插入 5 后面排序全部+1
                            changeNames.push({
                                'name': tmpNewName,
                                'obj': tmpObj
                            });
                            tmpObj['name'] = tmpNewName;
                            tmpObj.attr('name', tmpNewName);
                            changeChildSonsName(tmpObj, (i_+1));
                        }
                    }
                    //重置全局对象name=>obj
                    changeNames.forEach(function (datum) {
                        global[datum['name']] = datum['obj'];
                        //console.log('set globalname:'+ datum['name']);
                        //console.log(datum['obj']);
                    });
                } else {//减队
                    //console.log('sonsLen:'+ sonsLen);
                    for (i_ = parseInt(oldIndex)+1; i_ <= parseInt(sonsLen); i_ ++) {
                        tmpObj =  global[(objNameFront+'['+ i_ +']')];
                        tmpNewName = (objNameFront+'['+ (i_-1) +']');
                        if(tmpObj) {// 1-2-3-4 前面插入 5 后面排序全部+1
                            changeNames.push({
                                'name': tmpNewName,
                                'obj': tmpObj
                            });
                            tmpObj['name'] = tmpNewName;
                            tmpObj.attr('name', tmpNewName);
                            changeChildSonsName(tmpObj, (i_-1));
                        }
                    }
                    //重置全局对象name=>obj
                    changeNames.forEach(function (datum) {
                        global[datum['name']] = datum['obj'];
                    });
                    //最新的一个name要废除 因为全部都减1了
                    global[(objNameFront + '['+ sonsLen +']')] = null;
                }

            },
            clone:  function() {
                //console.log('clone obj:');
                //console.log(thisObj);
                //console.log(newOpt);
                //console.log('newOpt:'+ (objToJson(newOpt)));
                var  newOpt = cloneData(defaultOps);
                newOpt = getSourceOpt(newOpt);
                if(!isUndefined(newOpt['name'])) {
                    //tr li的name由用户自己定义区分，因为有时候用户不需要自动加下标
                    // var oldName = newOption['name'];
                    // newOption['name'] = indexClass.nameAddNum(oldName);
                } else {
                    //td span 不需要强加name
                    var tag = newOpt['tag'];
                    if(tag =='li' && tag =='tr') {
                        newOpt['name'] = createRadomName(newOpt['tag']); //必须设置name 否则拖动换排序时无法切换对象的子name
                    }
                }
                var newObj = thisObj.cloneSelf(newOpt);
                //console.log('newObj obj:');
                //console.log(newObj);
                return newObj;
            },
            addline:  function(afterFlag) {
                if(isUndefined(afterFlag)) afterFlag = true;
                var newOpt = $.extend({}, defaultOps);
                newOpt = getSourceOpt(newOpt);
                var newName;
                newName = createRadomName(defaultOps['tag']); //为当前对象补充name
                var parentObj = thisObj['parent'];//其父
                var hasParent = isOurObj(parentObj);

                newOpt['name'] = newName;
                var newObj = thisObj.cloneSelf(newOpt);
                if(hasParent) {
                    newObj['parent'] = parentObj;//设置其父
                    var parentSons = parentObj['value'];
                    var findIndex = -1;
                    parentSons.map(function (obj_, n) {
                        if(obj_ == thisObj) {
                            findIndex = n+1;//第一个是(0,1)
                            return false;
                        }
                    });
                }
                if(afterFlag == 'after') {//在后面添加对象
                    if(hasParent) {
                        //在中间插入
                        var array1 = parentSons.slice(0, findIndex);
                        var array2 = parentSons.slice(findIndex);
                        array1.push(newObj);
                        parentSons = array1.concat(array2);
                        parentObj['value'] = parentSons; //写入value 不然提交表单时无法获取其值
                    }
                    thisObj.after(newObj);
                } else if(afterFlag == 'before') {//在前面添加对象
                    if(hasParent) {
                        //在前面插入
                        var array1 = parentSons.slice(0, findIndex);
                        var array2 = parentSons.slice(findIndex);
                        var newArray = [newObj];
                        newArray = newArray.concat(array1);
                        parentSons = newArray.concat(array2);
                        parentObj['value'] = parentSons; //写入value 不然提交表单时无法获取其值
                    }
                    thisObj.before(newObj);
                }
                //如果是tr 要补充父对象
                if(newOpt['tag'] == 'tr') {
                    parentObj[objValObjKey].push(newObj);
                    if(parentObj['tr_clone_demo']) {
                        parentObj[tableWithDataTrKey].push(newObj);
                    } else {
                        parentObj[tableNoDataTrKey].push(newObj);
                    }
                }
                return newObj;
            },
            removeObj: function () {
                var thisObj = this;
                if(isOurObj(thisObj['parent'])) {
                    var parentObj = thisObj['parent'];
                    var parentSons = parentObj['value'];
                    if(parentSons.length == 1) {
                        msgTisf('至少要保留一行');
                        return;
                    }
                    $.each(parentSons, function (n, obj_) {
                        if(obj_ == thisObj) {
                            if(obj_.name) {
                                delete global[obj_.name];
                            }
                            parentSons.splice(n, 1);
                        }
                    });
                    var objName = thisObj['name'] || '';
                    global[objName] = null;
                    delete global[objName];
                    removeObjName(thisObj);
                    //后面的所有排序都要-1
                    if(objName) {
                        // var oldIndex = indexClass.nameGetNum(objName);
                        // thisObj.renewRestObjIndex(oldIndex, false);
                    }
                    parentObj['value'] = parentSons; //写入value 不然提交表单时无法获取其值
                    //console.log(parentSons);
                } else {
                    thisObj.remove();
                }
                //console.log('remove obj');
                //console.log(thisObj);
            },
            //通过name找到子对象
            findName : function(names) {
                names = names || '';
                names = $.trim(names);
                var findResultObj = null;
                var ___find = function (findObj) {
                    //console.log('findObj___',findObj);
                    //console.log(findObj.name);
                    if(findObj.name && findObj.name == names ) {
                        //console.log('findObj.name:'+ findObj.name);
                        findResultObj = findObj;
                        return ;
                    }
                    findObjVal(findObj);
                };
                var __findArray = function (valueObjs) {
                    $.each(valueObjs, function (index, tmpObj) {
                        //value仍是数组 继续递归查找
                        if($.isArray(tmpObj)) {
                            __findArray(tmpObj);
                        } else {
                            ___find(tmpObj);
                        }
                        if(findResultObj !== null) {
                            return false;
                        }
                    });
                };
                var findObjVal = function (__obj) {
                    var valueObjs = __obj.value;
                    if(!valueObjs || isStrOrNumber(valueObjs)  ) {
                        //console.log('no_val_Obj___', __obj, 'valueObjs', valueObjs);
                        return '';
                    }
                    if($.isArray(valueObjs)) {
                        __findArray(valueObjs);
                    } else {
                        ___find(valueObjs);
                    }
                };
                findObjVal(thisObj);
                return findResultObj;
            },
            //通过class找到子对象
            findClass : function(names) {
                names = names || '';
                names = $.trim(names);
                var findResultObj = [];
                var ___find = function (findObj) {
                    //console.log(findObj);
                    //console.log(findObj.name);
                    if(findObj.hasClass(names) ) {
                        //console.log('findObj.name:'+ findObj.name);
                        findResultObj.push(findObj);
                    }
                    findObjVal(findObj);
                };
                var findObjVal = function (__obj) {
                    var valueObjs = __obj.value;
                    if(!valueObjs || isStrOrNumber(valueObjs)  ) {
                        return;
                    }
                    if($.isArray(valueObjs)) {
                        //console.log('is_array');
                        //console.log(valueObjs);
                        $.each(valueObjs, function (index, tmpObj) {
                            ___find(tmpObj);
                        });
                    } else {
                        ___find(valueObjs);
                    }
                };
                findObjVal(thisObj);
                return findResultObj;
            },
            //通过name往上查找父对象
            findParent: function (name_) {
                var findObjPar = function (__obj) {
                    var parObj = __obj.parent;
                    if(!parObj || !isOurObj(parObj)  ) {
                        return '';
                    }
                    if(parObj.name=== name_) return parObj;
                    return findObjPar(parObj);
                };
                return findObjPar(thisObj);
            }
        });

    }
    //移除global对象 并且删除obj
    function removeObjName(thisObj) {
        if(!isOurObj(thisObj)) return;
        var sons = thisObj['value'] || thisObj['sons'] || [];
        if(sons.length >0 && $.isArray(sons)) {
            sons.forEach(function (obj_) {
                removeObjName(obj_);
            });
        }
        delete global[thisObj['name']];
        thisObj.remove();
    }

    //dom批量绑定事件 mouseenter/mouseleave/click/dblclick/blur/change
    function objSetOptEven(thisObj, options, callBackObj) {
        options = options || {};
        callBackObj = callBackObj || thisObj;//回调对象 默认为当前对象，如果设置了特别的父对象，则取父对象
        var evenFuncs = {}, evenNameArray;

        //console.log('objSet.OptEven', thisObj);
        //console.log('evenTags:'+ evenTags);
        evenTags.forEach(function(evenNames) {
            evenNameArray = evenNames.split('||');
            var evenNameMain = evenNameArray[0];
            evenNameArray.forEach(function(evenName) {
                //console.log('evenName:'+ evenName);
                evenName = $.trim(evenName);
                if(!isUndefined(options[evenName])) {
                    //console.log('!isUndefined:'+ evenName);
                    var runThisFuncs,thisFunc = options[evenName];
                    if(evenFuncs[evenNameMain]) { //如果已经有主要方法 ，补充扩展方法,如：submit_extend
                        var lastFunc = evenFuncs[evenNameMain];
                        runThisFuncs = function(eve) {
                            if(callBackObj && callBackObj.attr('disabled')) {
                                //console.log(thisObj);
                                return;
                            }
                            //先执行主要事件，再执行扩展事件，如果是核心的控件事件 ，要置换用户的核心事件为扩展事件 如 Select的li的click
                            var returnBack = lastFunc(eve);// 执行默认事件
                            if(returnBack === false) { //之前的函数被return 则不再执行
                                //console.log('no more');
                                return;
                            }
                            thisFunc(callBackObj, eve, livingObj);
                        };
                    }  else {
                        //console.log('no_'+ evenNameMain);
                        //console.log(thisObj);
                        //console.log(thisFunc.toString());
                        runThisFuncs = function(eve){
                            if(callBackObj && callBackObj.attr('disabled')) {
                                //console.log('has_disabled22');
                                return;
                            }
                            //console.log('thisObj');
                            //console.log(thisObj);
                            //console.log(options['data']);
                            if(isString(thisFunc)) {
                                return eval(thisFunc);
                            } else {
                                //console.log('on submit:'+ evenName);
                                //console.log(thisObj);
                                //console.log(thisFunc.toString());
                                return thisFunc(callBackObj, eve, livingObj); //回调给上面的扩展方法使用
                            }
                        };
                    }
                    //console.log(runThisFuncs.toString());
                    evenFuncs[evenNameMain] = runThisFuncs;
                }
            });
        });
        if(thisObj.off) {
            var pasteFunc = false;
            if('paste' in evenFuncs) {
                pasteFunc = evenFuncs['paste'];
                delete evenFuncs['paste'];
            }
            //console.log('addEvent111:',thisObj, pasteFunc);
            thisObj.off().on(evenFuncs); //防止二次叠加 所以要先off
            // //paste是特殊的事件，jq无法获取粘贴的图片内容
            if(pasteFunc && !thisObj.bindPaste) {
                //console.log('addEventListener...',thisObj);
                thisObj.bindPaste = true;
                thisObj[0].addEventListener('paste', pasteFunc);
            }
        }
    }
    function addOptionNullFunc(thisObj, defaultOps) {
        defaultOps = defaultOps || {};
        if(optionIsSame(thisObj, defaultOps, 'null_func')) return; //属性未改变 无需重置
        var nullFunc = defaultOps['null_func']; //为空时执行
        if(!nullFunc) return ;
        // thisObj['null_func'] = nullFunc; //会触发刷新
        var nullFuncString = $.trim(nullFunc.toString());
        if(nullFuncString.substr(-1, 1) != '}') nullFuncString = "function() { " + nullFuncString + "}"; //字符方法要用function包裹
        if(thisObj.attr) thisObj.attr('null_func', nullFuncString); //radio和checkbox是[] 不能加attr
    }
    //触发function事件
    function runFunc(doFunc, obj) {
        if(doFunc) {
            if(isString(doFunc)) {
                eval(doFunc);
            } else {
                doFunc(obj);
            }
        }
    }
    //同步原理：
    //dom首次获取值时 会声明一个 内容更新器，利用全局变量：临时内容更新器  把它传给 数据监听器
    // 数据监听器在遍历每个数据时，会一一分配一个空的订阅器
    // 数据监听器在被取出数据时，根据是否有 临时内容更新器 来判断是否要写入订阅通知器
    // （上行提到 临时内容更新器 只在dom初始化时生成 并且在dom赋值即是数据取出后，立刻注销“临时内容更新器”的这个指向）
    // 数据监听器会在数据被设置内容时 会触发当前数据绑定的订阅通知器 让它给旗下的订阅者(dom)发送通知。
    //数据同步
    var livingObj = {data: {}};
    //公共的通知对象列表
    /*
     *  {
     *   'bind_val': notifyClass
     *  }
     * */
    //var notifyObj = {};
    global.notifyObj = {};
    //订阅通知器 用于通知订阅者
    var notifyer = function() {
        this.receivrs= [];
        this.data_name= '';
        this.addReceivrs = function(newReceiver) {
            if($.inArray(newReceiver, this.receivrs) ==-1) {
                this.receivrs.push(newReceiver);
            }
        };
        this.hasReceivr = function(newReceiver) {
            return $.inArray(newReceiver, this.receivrs) ==-1 ? false : true;
        };
        this.notify = function(dataName, exceptObj) {
            // console.log('notify');
            exceptObj = exceptObj || [];
            //console.log('this.receivrs');
            //console.log(this.receivrs);
            //console.log('notify.exceptObj');
            //console.log(exceptObj);
            this.receivrs.forEach(function(tmpReceiver) {
                //console.log(dataName);
                //console.log('tmpReceiver');
                //console.log(tmpReceiver);
                if($.inArray(tmpReceiver, exceptObj) != -1) { //continue;
                    return;
                }
                if(!tmpReceiver.updates) console.log(tmpReceiver);
                tmpReceiver.updates(dataName, exceptObj);
            });
        };
    };
    //dom数据绑定
    function objBindVal(thisObj, options, bindData) {
        options = options || {};
        bindData = bindData || [{'key_':'bind', 'val_':'value'}];
        var bindKeys,bindKeyArray, valFrom, valString;
        $.each(bindData, function (n, v) {
            bindKeys = v.key_;
            valFrom = v.val_;
            bindKeyArray = bindKeys.split('/');
            $.each(bindKeyArray, function (n, bindKey) {
                valString = (options[valFrom]||'') || (getObjData(options[bindKey]) || '');
                //bindKey: bind/setText
                if(options[bindKey]) { //数据绑定
                    var bingStr = $.trim(options[bindKey]);
                    // console.log('bindKeyArray', bindKey, bingStr, valString);
                    //只有这个obj属性中未定义全局绑定变量，才能加入全局绑定
                    if(!thisObj[objBindAttrsName] || !thisObj[objBindAttrsName][bingStr]) {
                        objAddListener(thisObj, bingStr, valString, valString!=='' ); //当前对象加入到监控中
                    }
                }
            });
        });
    }
    //数据监听器
    function objAddListener(domObj, dataName, val, update_) {
        // console.log('obj.AddListener', domObj, dataName, val, update_);
        update_ = isUndefined(update_) ? true : update_;
        if(!notifyObj[dataName]) {
            if(!livingObj.hasOwnProperty(dataName)) {
                addKeyToListener(dataName, val);  //数据监听器
            }
            var notifyClass = new notifyer();
            notifyClass['data_name'] = dataName;
            notifyClass.addReceivrs(domObj);
            notifyObj[dataName] = notifyClass;
        } else {
            //如果之前没有设置数据，而我现在有，那么要同步更新绑定数据为我这个数据
            var lastVal = isUndefined(livingObj['data'][dataName]) ? null : livingObj['data'][dataName];
            if(!lastVal) {
                if(val) {
                    //之前未设置，后期补上，则主动更新
                    // console.log('之前未设置,补上', dataName, val);
                    setObjData(dataName, val);
                }
            } else {
                //之前有，现在的值又有了 则要更新它们
                if(val) {
                    // console.log('之前有,现在的值要被动更新'+ val);
                    if(domObj.updates && update_) domObj.updates(dataName);
                }
            }
            if(!notifyObj[dataName].hasReceivr(domObj)) {
                notifyObj[dataName].addReceivrs(domObj);
            }
        }
    }
    //属性添加到监听器
    function addKeyToListener(dataName, defaultVal, yufa) {
        defaultVal = defaultVal || '';
        yufa = yufa || '=';
        var lastVal = isUndefined(livingObj['data'][dataName]) ? null : livingObj['data'][dataName];
        if(!livingObj.hasOwnProperty(dataName) || !livingObj['data'].hasOwnProperty(dataName)) {
            //console.log('add dim obj:'+ dataName + ',val:'+ defaultVal);
            Object.defineProperty(livingObj, dataName, {
                get: function () {
                    return this['data'][dataName];
                },
                set: function (newVal) {
                    //console.log('update BindObj  1:'+ newVal);
                    if (newVal === lastVal) return;
                    // console.log('update__  BindObj  :'+ dataName);
                    updateBindObj(dataName, newVal);
                }
            });
            livingObj['data'][dataName] = defaultVal;
        } else {
            if(lastVal && yufa != '=') {
                //执行 ss +=2
                eval('var ss=' + lastVal +'; ss'+ yufa + defaultVal);
                defaultVal = ss;
                livingObj['data'][dataName] = defaultVal;
            }
        }
    }
    //指定数据更新
    function updateBindObj(dataName, newVal, exceptObj) {
        exceptObj = isUndefined(exceptObj) ? [] : exceptObj;
        livingObj['data'][dataName] = newVal;
        // 作为发布者发出通知
        if(notifyObj[dataName]) {
            notifyObj[dataName].notify(dataName, exceptObj);
        }
    }
    //公共监听对象设置新值
    function setObjData(dataName, val) {
        //必须先定义数据绑定 才能触发数据同步
        if(livingObj.hasOwnProperty(dataName)) livingObj[dataName] = val;
    }
    //公共监听对象 取值
    function getObjData(dataName) {
        dataName = $.trim(dataName);
        return isUndefined(livingObj['data'][dataName]) ? '' : livingObj['data'][dataName] ;//不能用|| 因为0也是值
    }
    //获取字符串中的所有{}中的变量 返回数组
    function getKuohaoAbc(str, dataPublic) {
        dataPublic = dataPublic || 'data'; //取的字符是全局的 还是局部的
        var matches = str.match(/{([^}^{]*?)}/g);// ["{id}", "{name}", "{1+2...}"]
        var strReg = /^[0-9a-zA-Z_]{1,}$/;
        if(!matches) return [];
        var matchKey =  [];
        matches.forEach(function (matchVal) {
            matchVal = matchVal.replace(/\{|\}/g, '');
            matchVal = $.trim(matchVal);
            if(!matchVal)  return;
            if(strReg.test(matchVal)) {
                matchKey.push(matchVal);
            }
        });
        if(hasData(matchKey)) matchKey = $.unique(matchKey);
        return matchKey;
    }
    //获取父对象
    function getParentObj(thisObj, parentStr) {
        var getTimes = 0;
        if(parentStr.substr(0, 6) =='parent') {
            getTimes = parentStr.split('parent').length - 1;
            //4
        } else {
            return thisObj.closest(parentStr);
        }
        //递归获取子对象
        function __getParent(o_, num) {
            if(num > 0) {
                num --;
                return __getParent(o_.parent,num);
            } else {
                return o_;
            }
        }
        return __getParent(thisObj, getTimes);
    }

    //dom绑定拖拽事件
    function callBindDragObj(thisObj, option_) {
        var orderKeyname = getOptVal(option_, ['order_key', 'orderKey', 'order_keyname', 'orderKeyname', 'order_field', 'orderField'], null);
        var orderType = getOptVal(option_, ['order_type', 'orderType', 'order_value', 'orderValue'], null);
        var moveLi = getOptVal(option_, ['move_li', 'moveLi'], null);
        var moveBox = getOptVal(option_, ['move_box', 'moveBox'], null);
        var movingFunc = getOptVal(option_, ['moving_func','movingFunc','moving'], null);
        var moveEndFunc = getOptVal(option_, ['move_end_func','move_end', 'moveEndFunc', 'moveEnd'], null);
        var moveBeforeFunc = getOptVal(option_, ['move_before_func','move_before', 'moveBeforeFunc', 'moveBefore'], null);
        if(moveLi && moveBox) {
            var emptyClass = 'emptyDragLi';
            var emptyLi;
            var hasMakeEmpLi = false;
            var moveTopLi = option_['move_top_li'] || 0;//头部多余的li个数 方便防止超出顶部
            var moveBottomLi = option_['move_bottom_li'] || 0;//尾部多余的li个数 方便防止超出底部
            var makeEmptyLi = function(par, tmpLi) {
                //console.log(par);
                if(tmpLi.css('position') !=='absolute') tmpLi.css('position', 'absolute');
                if(!hasMakeEmpLi) {
                    var tagName = (tmpLi[0].tagName).toLocaleLowerCase();
                    emptyLi = $(document.createElement(tagName));
                    var emptyCss = {
                        'display': 'block',
                        'width': tmpLi.outerWidth(),
                        'height': tmpLi.outerHeight()
                    };
                    if(tagName=='tr') {
                        var trNum = tmpLi.find('td').length;
                        var tdHtml = (new Array(trNum + 1)).join('<td></td>');
                        emptyLi.append(tdHtml);
                        emptyCss = {
                            'height': tmpLi.outerHeight()
                        };
                        //console.log(trNum);
                    }
                    emptyLi.attr('class', emptyClass);
                    emptyLi.css(emptyCss);
                    //console.log(tagName);
                    tmpLi.after(emptyLi);
                    thisObj.css('cursor', 'move');
                    hasMakeEmpLi = true;
                }
                //超过上界，则切换空白li的位置
                if(emptyLi.prev().length> 0) {
                    var hasPrevLi = true;
                    var prevLi = emptyLi.prev();
                    if(prevLi.hasClass(emptyClass)) {
                        if(prevLi.prev().length == 0) {
                            hasPrevLi = false;
                        } else {
                            prevLi = prevLi.prev();
                        }
                    }
                    var upLine = parseFloat(emptyLi.position().top) - parseFloat(prevLi.outerHeight());
                    upLine = Math.round(upLine);
                    if(hasPrevLi && parseFloat(tmpLi.css('top')) <= upLine ) {
                        //console.log('prevLi.before:');
                        //console.log(prevLi);
                        prevLi.before(emptyLi);
                    }
                }
                //超过下界，则切换空白li的位置
                if(emptyLi.next().length> 0) {
                    var hasNextLi = true;
                    var nextLi = emptyLi.next();
                    if(nextLi.hasClass(emptyClass)) {
                        if(nextLi.prev().length == 0) {
                            hasNextLi = false;
                        } else {
                            nextLi = nextLi.next();
                        }
                    }
                    var upLine = parseFloat(parseFloat(nextLi.outerHeight()) + parseFloat(emptyLi.position().top));
                    upLine = Math.round(upLine);
                    if(hasNextLi && parseFloat(tmpLi.css('top'))+5 >= upLine) {
                        nextLi.after(emptyLi);
                        // window.location.hash = 'next:'+Math.random(10);
                    }
                }
            };
            var lastCursor = null;
            var beforeDrag = function (par, tmpLi) {
                lastCursor = thisObj.css('cursor');
            }
            var stopDrag = function(par, tmpLi) {
                tmpLi.css({
                    'position': '',
                    'left': '',
                    'z-index': '',
                    'top': ''
                });
                if(!lastCursor) thisObj.css('cursor', '');
                //切换li到真实的位置
                par.find('.'+ emptyClass).after(tmpLi);
                par.find('.'+ emptyClass).remove();
                hasMakeEmpLi = false;
                //重置所有的li的name 不然form无法提交
                //console.log(par);
                var thisLiName = tmpLi.attr('name');
                var preLi = tmpLi.prev();
                var nextLi = tmpLi.next();
                var siblingLi, siblingsName, siblingsIndex,
                    nextLiName,  preLiName, preLiIndex,
                    nextLiIndex, currentIndex;
                if(preLi.length == 0) {//挪到最上面时
                    siblingLi = nextLi;
                    if(orderKeyname) {
                        var newOrder;
                        if(orderType=='desc') {
                            newOrder = parseFloat(siblingLi.attr(orderKeyname)) +1;
                        } else {
                            newOrder = parseFloat(siblingLi.attr(orderKeyname)) -1;
                        }
                        tmpLi.attr(orderKeyname, newOrder);
                    }
                } else {
                    siblingLi = preLi;
                    if(orderKeyname) {
                        var newOrder;
                        if(orderType=='desc') {
                            newOrder = parseFloat(siblingLi.attr(orderKeyname)) -1;
                        } else {
                            newOrder = parseFloat(siblingLi.attr(orderKeyname)) +1;
                        }
                        tmpLi.attr(orderKeyname, newOrder);
                    }
                }
                siblingsName = siblingLi.attr('name');
                var objNameFront = indexClass.nameRemoveNum(thisLiName);
                if(!objNameFront) {
                    objNameFront = 'lr_index_li';
                    global[objNameFront] = tmpLi;
                    tmpLi.attr('name', objNameFront);
                }
                preLiName = preLi.attr('name');
                nextLiName = nextLi.attr('name');
                if(!indexClass.nameHasNum(siblingsName)) {//不包含[123]这样的li 不需要重置name
                    return;
                }
                siblingsIndex = indexClass.nameGetNum(siblingsName);
                preLiIndex = indexClass.nameGetNum(preLiName);
                nextLiIndex = indexClass.nameGetNum(nextLiName);
                currentIndex = indexClass.nameGetNum(thisLiName);
                //console.log('siblingsIndex:'+ siblingsIndex);
                //console.log('currentIndex:'+ currentIndex);
                if(siblingsIndex < currentIndex-1 ) {//向上拖动
                    var tmpName = createRadomName(tmpLi['tag']); //为当前旧对象补充name
                    if(thisLiName) global[thisLiName] = null;
                    tmpLi.attr('name', tmpName);
                    changeChildSonsName(tmpLi, 1000000);//将子对象的所有name都临时放大 防止冲突
                    var tmpObj, tmpNewName, changeNames=[];
                    for(var i_ = nextLiIndex; i_ <= currentIndex; i_ ++) {//i_要算上自己 一起重置
                        tmpObj =  global[(objNameFront+'['+ i_ +']')];
                        tmpNewName = (objNameFront+'['+ (i_+1) +']');
                        if(tmpObj) {// 1-2-3-4 前面插入 5 后面排序全部+1
                            changeNames.push({
                                'name': tmpNewName,
                                'obj': tmpObj
                            });
                            tmpObj['name'] = tmpNewName;
                            tmpObj.attr('name', tmpNewName);
                            changeChildSonsName(tmpObj, (i_+1));
                        }
                    }
                    //重置全局对象name=>obj
                    changeNames.forEach(function (datum) {
                        global[datum['name']] = datum['obj'];
                    });
                    //将当前li恢复name为i_-1;
                    var newTmpName = objNameFront+'['+ (nextLiIndex) +']';
                    global[newTmpName] = tmpLi;
                    tmpLi['name'] = newTmpName;
                    tmpLi.attr('name', newTmpName);
                    changeChildSonsName(tmpLi, nextLiIndex);//将子对象的所有name都临时放大 防止冲突
                } else if(siblingsIndex > currentIndex ) {//向下拖动
                    //console.log('向下拖动');
                    //console.log('currentIndex'+ currentIndex);
                    //console.log('nextLiIndex'+ nextLiIndex);
                    //console.log('preLiIndex'+ preLiIndex);
                    //console.log('thisLiName'+ thisLiName);
                    //现将当前li拷贝 清空，再将所有i_递增1 再将当前li恢复name为i_-1;
                    var tmpName = createRadomName(tmpLi['tag']); //为当前旧对象补充name
                    global[thisLiName] = null;
                    tmpLi.attr('name', tmpName);
                    changeChildSonsName(tmpLi, 1000000);//将子对象的所有name都临时放大 防止冲突

                    var tmpObj, tmpNewName, changeNames=[];
                    for(var i_ = currentIndex+1 ; i_ <= preLiIndex; i_ ++) {
                        //console.log('change i '+ i_);
                        //console.log(objNameFront+'['+ i_ +']');
                        tmpObj =  global[(objNameFront+'['+ i_ +']')];
                        //console.log(tmpObj);
                        tmpNewName = (objNameFront+'['+ (i_-1) +']');
                        if(tmpObj) {// 1-2-3-4 前面插入 5 后面排序全部+1
                            //console.log('change tmpObj tmpNewName: '+ tmpNewName);
                            changeNames.push({
                                'name': tmpNewName,
                                'obj': tmpObj
                            });
                            tmpObj['name'] = tmpNewName;
                            tmpObj.attr('name', tmpNewName);
                            changeChildSonsName(tmpObj, (i_-1));
                        }
                    }
                    //重置全局对象name=>obj
                    changeNames.forEach(function (datum) {
                        global[datum['name']] = datum['obj'];
                    });
                    //将当前li恢复name为i_+1;
                    var newTmpName = objNameFront+'['+ (preLiIndex) +']';
                    global[newTmpName] = tmpLi;
                    tmpLi['name'] = newTmpName;
                    tmpLi.attr('name', newTmpName);
                    changeChildSonsName(tmpLi, preLiIndex);//将子对象的所有name都临时放大 防止冲突
                }
            };
            setTimeout(function () {
                var parentLi,parentBox;
                parentLi = getParentObj(thisObj, moveLi);
                parentBox = getParentObj(thisObj, moveBox);
                // console.log('thisObj');
                // console.log(thisObj);
                // console.log('parentBox');
                // console.log(parentBox);
                if(isOurObj(parentBox) && parentBox && parentBox.css('position') !=='relative') parentBox.css('position', 'relative');
                var minTop = -1;
                if(moveTopLi > 0) {
                    var tmpLi = 0;
                    $.each(parentLi.siblings(), function (n, sibli_) {
                        sibli_ = $(sibli_);
                        if(tmpLi<moveTopLi) {
                            minTop += sibli_.outerHeight();
                            //console.log('tmpTop:'+ sibli_.outerHeight());
                        } else {
                            return;
                        }
                        tmpLi ++;
                    });
                }
                if(moveBottomLi > 0) {
                    var siblings = parentLi.siblings();
                    var bottomDistance = 0; //底部距离
                    var sibli_;
                    for(var tmpLi =0; tmpLi < moveBottomLi; tmpLi++) {
                        if(tmpLi<moveBottomLi) {
                            sibli_ = siblings[siblings.length - 1 - tmpLi];
                            sibli_ = $(sibli_);
                            bottomDistance += sibli_.outerHeight();
                        } else {
                            return;
                        }
                    }
                }
                //console.log('bottomDistance:'+bottomDistance);
                //console.log('minTop:'+minTop);
                // var minLeft = firstLi.offset().left;
                var minLeft = 0;
                var liHeight = parentLi.outerHeight();
                //console.log('maxHeight:'+ maxHeight);
                // parentBox.css('height', maxHeight);
                var mouseDown = function(xy, btn, parentLi_, parentBox_, pubobj) {
                    beforeDrag(parentBox_, parentLi_);
                    if(moveBeforeFunc) moveBeforeFunc(btn, parentBox_, parentLi_, xy);
                };
                var movingBar = function(xy, btn, parentLi_, parentBox_, pubobj) {
                    makeEmptyLi(parentBox_, parentLi_);
                    if(movingFunc) movingFunc(btn, parentLi_, parentBox_, xy);
                    //console.log(xy);
                };
                var movingEnd = function(xy, btn, parentLi_, parentBox_, pubobj) {
                    stopDrag(parentBox_, parentLi_);
                    if(moveEndFunc) moveEndFunc(btn, parentLi_, parentBox_, xy);
                };
                //当li的数量添加或减少时，max_top会改变，所以应该是keydown时更新这个最大高度
                parentLi.Drag(thisObj, '', {
                    min_top: minTop,
                    bottom_distance: bottomDistance,
                    min_left: minLeft,
                    max_left: minLeft,
                    parent_box: parentBox,
                    li_height: liHeight,
                    mousedown_data: [mouseDown, thisObj, parentLi, parentBox],
                    draging_data: [movingBar, thisObj, parentLi, parentBox],
                    drag_up_data: [movingEnd, thisObj, parentLi, parentBox]
                });
            }, 200);
        }
    }
    //打包form内的数据为对象
    $.fn.getFormData = $.fn.getFormDatas = function () {
        var backData = {};
        //保存值到name
        function objSaveVal(obj_, tmpName, objVal) {
            //console.log(obj_);
            //console.log(tmpName);
            //console.log(backData[tmpName]);
            if(isUndefined(backData[tmpName])) {
                backData[tmpName] = objVal;
            } else {
                if($.isArray(backData[tmpName])) {
                    backData[tmpName] = backData[tmpName].concat(objVal);
                } else {
                    backData[tmpName] = [backData[tmpName], objVal];
                }
            }
        }
        //取单个obj的值
        function getObjVal(obj_) {
            if(isOurObj(obj_)) { // obj{input/div/p}
                var objVal;
                var objName = obj_['name'] || obj_['options']['name'] || '';
                objVal = obj_.value;
                //console.log('get obj name: '+ objName +',val');
                //console.log(obj_);
                //console.log(objVal);
                if(typeof objVal == 'object')  {//[obj, obj] 或 [1,2,3]
                    //console.log(objVal);
                    //console.log('is obj');
                    if(_valIsAy(objVal)) {//将所有数组都递归 转交给单个对象的方法
                        //console.log('is array and not our obj ooo');
                        if(!hasData(objVal)) {// []
                            //console.log('objVal no has data:'+ objName +' val:'+ objVal );
                            if(objName) {
                                //console.log('obj_ add objName:'+ objName +' val:'+ objVal );
                                objSaveVal(obj_, objName, '');
                            }
                        } else {
                            objVal.forEach(function (tmpObj) {
                                if(isStrOrNumber(tmpObj)) {
                                    //console.log(tmpObj);
                                    //console.log('name:'+ objName);
                                    if(objName) {
                                        //console.log('obj_ add objName:'+ objName +' val:'+ tmpObj );
                                        objSaveVal(obj_, objName, tmpObj);
                                    }
                                } else if(isOurObj(tmpObj)) {
                                    //console.log('tmpObj is isOurObj');
                                    //console.log(tmpObj);
                                    getObjVal(tmpObj);
                                } else {
                                    //console.log('what is this????');
                                    //console.log(v);
                                }
                            });
                        }
                    } else if(isOurObj(objVal)) {
                        //console.log('getObjVal');
                        //console.log(obj_);
                        //console.log(objVal);
                        getObjVal(objVal);
                    } else {
                        //console.log('eeeeeeeeeeeeeeeeeeeeee');
                        //console.log(obj_);
                        //console.log('objName');
                        //console.log(objVal);
                        if(objName) objSaveVal(obj_, objName, objVal);
                    }
                } else {
                    //console.log(obj_);
                    //console.log('objName');
                    //console.log(objVal);
                    if(objName) objSaveVal(obj_, objName, objVal);
                }
            } else { //checkbox的 value = [1,2,3]
                //console.log(obj_);
            }
        }
        //取数组的单个对象
        function getArrayObjVal(array_) {
            //console.log('getArray ObjVal');
            //console.log(array_);
            array_.forEach(function (arrayItem) {
                if(_valIsAy(arrayItem)) {//将所有数组都递归 转交给单个对象的方法
                    //console.log('is array and not our obj 1');
                    //console.log(formVals);
                    getArrayObjVal(arrayItem);
                } else {
                    //console.log('getObjVal2');
                    //console.log(arrayItem);
                    getObjVal(arrayItem);
                }
            });
        }
        //如果当前对象是自定义对象 直接遍历对象获取 无须用dom
        if(isOurObj(this)) {
            var formVals = this.value;//form的value -> table/list/ [ [ obj{input},obj{input}+.. ]+ obj{input} + obj{btn} ]
            //如果formVals是对象，直接取值；如果是数组，遍历对象，再取值
            if(_valIsAy(formVals)) {
                //console.log('is array and not our obj 2');
                //console.log(formVals);
                getArrayObjVal(formVals);
            } else { // obj{table} / obj{list}
                getObjVal(formVals);
            }
        } else {//普通的dom取值
            var formData = this.serializeArray();
            $.each(formData, function (n, datum) {
                objSaveVal(datum.name, datum.value);
            })
        }
        //将拖动排序后的name重新按0-10排列
        var newListNames = Object.keys(backData).sort();
        var newObj = {};
        newListNames.forEach(function (name_) {
            newObj[name_] = backData[name_];
        });
        return newObj;
    };
//遍历form内的数据 找到禁止留空的对象 如果有则执行通知函数 返回 ['err];
    $.fn.getFormNullErr = function () {
        var errFunc = false;
        //取数组的值
        function getArrayObjVal(array_) {
            array_.forEach(function (arrayItem) {
                if(_valIsAy(arrayItem)) {//将所有数组都递归 转交给单个对象的方法
                    //console.log(arrayItem);
                    //console.log('is array');
                    getArrayObjVal(arrayItem);
                } else {
                    //console.log('getObjNullVal:');
                    //console.log(arrayItem);
                    getObjNullVal(arrayItem);
                }
            });
        }
        //取单个obj的值
        function getObjNullVal(obj_) {
            if(isOurObj(obj_)) { // obj{input/div/p}
                var itsVal = obj_.value;
                var objVal = isUndefined(itsVal) ? '': itsVal;
                //console.log(obj_);
                //console.log(objVal);
                //console.log(obj_['null_func']);
                //console.log(errFunc);
                //console.log(objVal);
                if(typeof objVal == 'object')  {
                    if(_valIsAy(objVal) ) {//将所有数组都递归 转交给单个对象的方法
                        //console.log('val obj is array:');
                        //console.log(objVal);
                        getArrayObjVal(objVal);
                    } else {
                        //console.log('val is our obj:');
                        //console.log(objVal);
                        getObjNullVal(objVal);
                    }
                } else {
                    //console.log('val is not obj:');
                    //console.log(objVal);
                    if(obj_['null_func'] && !errFunc && !objVal) errFunc = [obj_['null_func'], obj_, obj_[parentObjKey]];
                }
            }
        }
        //如果当前对象是自定义对象 直接遍历对象获取 无须用dom
        if(isOurObj(this)) {
            //console.log(this);
            var formVals = this.value;//form的value -> table/list/ [ [ obj{input},obj{input}+.. ]+ obj{input} + obj{btn} ]
            //如果formVals是对象，直接取值；如果是数组，遍历对象，再取值
            if(_valIsAy(formVals)) {
                getArrayObjVal(formVals);
            } else { // obj{table} / obj{list}
                //console.log('getObjNullVal');
                //console.log(formVals);
                getObjNullVal(formVals);
            }
        }
        return errFunc;
    };
    //默认空data
    function makeNullData() {
        return {};
    }

    //对象字符串node类型的写入和刷新
    function domAppendNode(obj_, opt, hasSetData) {
        var optValStr = opt['value'] || opt['son'] || '';
        var optData = opt['data'] || makeNullData();
        if(isStrOrNumber(optData) && hasSetData) { //optData : {son_v}
            optData = strObj.formatStr(optData, [], 0, obj_, 'data');
        }
        //更新node
        var _renewNodeVal = function(newV) {
            //如果 newV 是三元运算符，并且引号里带<> 直接html会勿将三元语法给打乱，所以应该一开始就将<>转译一遍
            if(isHtml(newV) ) {
                //匹配例子： ..." <a href="#{id}"></a> <i class="glyphicon glyphicon-fire"></i>  <i class="glyphicon glyphicon-fire"></i>" ...
                var  regInYin = /('|")([^<'"]*)((\s*<([a-z]+)\s*[^>]+>([^<]*)<\/([a-z]+)>)+)([^'"<]*)('|")/g;
                var inYinStr = newV.match(regInYin);
                if(inYinStr) {
                    var  regReplaceYin = /<[^>]+>/g;
                    //console.log('is inYinStr:');
                    //console.log(inYinStr);
                    //console.log('is html111111:'+ newV);
                    $.each(inYinStr, function (n, in_Str) {
                        var newMatchVal = in_Str;
                        var inJKHStr = in_Str.match(regReplaceYin);//找到的尖括号
                        //console.log(inJKHStr);
                        $.each(inJKHStr, function (n, jkh_tag) {
                            var newS_ = jkh_tag.replace(/</g, '&lt;');
                            newS_ = newS_.replace(/>/g, '&gt;');
                            //console.log('console html111111:'+ in_Str);
                            //console.log('console newMatchVal:'+ newMatchVal);
                            newMatchVal = newMatchVal.replace(RegExp(regCodeAddGang(jkh_tag), 'g'), newS_);
                            //console.log('console html222222:'+ newMatchVal);
                        });
                        newV = newV.replace(RegExp(regCodeAddGang(in_Str), 'g'), newMatchVal);
                    });
                    //console.log('is html222222:'+ newV);
                }
            }
            obj_.html(newV);//先写入html再提取dom
            obj_.nodeObj = [];//刷新时要更新之前保存的node
            obj_.htmObj = [];//刷新时要更新之前保存的nodeHtm
            //提取dom的node
            var thisNewVal = getObjHtmlNode(obj_, obj_[0], optData);
            obj_.append(thisNewVal);
            formatObjNodesVal(obj_, optData, hasSetData); //value的改变 也要重新格式化
        };
        //当值的文本发生变化时 要一起更新静态节点内容
        if(!optionIsSame(obj_, opt, 'value') || obj_.nodeObj.length == 0 )
        {
            _renewNodeVal(optValStr);//更新node
        } else if(!dataIsSame(obj_['last_options']['data'], optData)) { //更新子对象或子字符串
            formatObjNodesVal(obj_, optData, hasSetData);
        } else if(obj_[objBindAttrsName]) { //obj bind attrs(如:class) 中含{{dataName} > 2}
            $.each(obj_[objBindAttrsName], function (bindName, attrNames) {
                // 只格式化value
                $.each(attrNames, function (n, attrKey) {
                    if(attrKey =='value')  {
                        formatObjNodesVal(obj_, livingObj, hasSetData);
                    }
                });
            });
        }
        //如果一开始value是html格式，突然换个obj格式，所以要在这里做格式判断
        if(!obj_.hasOwnProperty('value')) {
            Object.defineProperty(obj_, 'value', {
                get: function () {
                    //console.log('get val');
                    //console.log(obj_[objValIsNode]);
                    if(obj_[objValIsNode]) {
                        return obj_.html();
                    }
                    return this[objValObjKey];
                }
                ,set: function (newVal) {
                    //value修改，统一全部更新
                    _renewNodeVal(newVal);//更新node
                }
            });
        }
    }
    //对象写入son val
    function objPushVal(obj_, valObj) {
        valObj[parentObjKey] = obj_;
        obj_.append(valObj);
        obj_[objValObjKey].push(valObj);
        obj_[objLastValKey].push(valObj);
    }
    //tr对象写入(更新)Td对象
    function domAppendTrObj(obj_, opt) {
        //console.log(opt);
        var optNewVal = opt['value'] || opt['son'] || '';
        var valForCompare = $.isArray(optNewVal) ? optNewVal : [optNewVal];
        //console.log(valForCompare);
        //console.log(dataIsSame(obj_[objLastValKey], valForCompare));
        if(hasData(obj_[objLastValKey]) && dataIsSame(obj_[objLastValKey], valForCompare)) { // val未变 不用append
            //console.log('tr val no change ::::::::');
        } else {
            var optData = opt['data'] || [];
            var trNewOpt = tdToObj(optData, opt);//创建新的TD
            var newVal = trNewOpt['value'] || [];
            if(!$.isArray(newVal)) newVal = [newVal];
            var newTd,tdOpt;
            var callClone = isUndefined(opt[optionCallCloneKey]) ? false : opt[optionCallCloneKey];
            newVal.forEach(function(td_) {
                if(callClone) {
                    //console.log('callClone');
                    //如果这个TD已经是克隆的 就不需要再克隆了，直接拿
                    if(td_['options'][optionCallCloneKey]) {
                        //console.log('callClone success:');
                        //console.log(td_);
                        //console.log(td_['options']);
                        objPushVal(obj_, td_);
                        //console.log('callClone but just get cloneTD success:');
                    } else {
                        tdOpt = cloneData(td_['options']);
                        tdOpt = getSourceOpt(tdOpt);
                        newTd = td_.cloneSelf(tdOpt);
                        objPushVal(obj_, newTd);
                        //console.log('callClone success:');
                        //console.log(newTd);
                    }
                } else {
                    //console.log('not clone td val ::::::::');
                    //console.log(td_);
                    var valForCompare = $.isArray(td_) ? td_ : [td_];
                    if(!dataIsSame(obj_[objLastValKey], valForCompare)) { //内容obj已变
                        //console.log('append obj val ::::::::');
                        //console.log(obj_);
                        //console.log(td_);
                        objPushVal(obj_, td_);
                    }
                }
            });
            if(!obj_.hasOwnProperty('value')) {
                Object.defineProperty(obj_, 'value', {
                    get: function () {
                        //console.log(this.html());
                        return newVal;
                    }
                    ,set: function (V) {
                        newVal.remove();
                        //console.log('SET OBJ PATENT::::::::');
                        //console.log('SON::::::');
                        //console.log(V);
                        //console.log(obj_);
                        V[parentObjKey] = obj_;
                        obj_.append(newVal);
                    }
                });
            }
        }
    }
    //普通的obj对象写入obj对象
    function domAppendObj(obj_, opt) {
        var optNewVal =  opt['value'] || opt['son'] || '';
        var optData = opt['data'] || []; //空data的form 在 append table时，table有data 则不能覆盖
        var callClone = isUndefined(opt[optionCallCloneKey]) ? false : opt[optionCallCloneKey];
        // console.log(opt);
        //console.log(hasData(optNewVal));
        if(!hasData(optNewVal)) {
            return;
        }
        //console.log(obj_);
        //console.log(optNewVal);
        //console.log(optData);
        if(_valIsAy(optNewVal)) {
            //console.log('_valIsAy optNewVal');
            if(hasData(obj_[objValObjKey])) {
            } else {
                $.each(optNewVal, function (n, valObj) {
                    __appendOneOurObj(valObj, n);
                });
            }
        } else {
            __appendOneOurObj(optNewVal, 0);
        }
        //创建对象的儿子对象
        //如果一开始value是obj格式 突然换个html格式，所以要在这里做格式判断
        function __appendOneOurObj(valObj) {
            //console.log('__appendOneOurObj');
            if(isUndefined(valObj)) {
                return;
            }
            var sonOpt,newObj;
            if(callClone) {//当前准备写入的的obj需要先克隆
                sonOpt = cloneData(valObj['options']);
                //console.log(objToJson(valObj['options']));
                //console.log(objToJson(sonOpt));
                //准备data 如果是克隆的子对象 data不应该提前修改 否则子对象的data就默认不需要继承父了
                //console.log('clone sonOpt', sonOpt);
                sonOpt = getSourceOpt(sonOpt);
                newObj = valObj.cloneSelf(sonOpt);
                if(valObj['extendParentData'] && isStrOrNumber(sonOpt['data']) && hasData(optData)) {
                    //console.log('callClone set_data ::::::::');
                    //console.log(optData);
                    newObj['data'] = optData;
                }
                if(isOurObj(newObj)) {//设置其父obj
                    //console.log('callClone append val ::::::::');
                    //console.log(obj_);
                    //console.log(newObj);
                    objPushVal(obj_, newObj);
                }
            } else {
                //console.log('no clone val');
                //console.log(obj_);
                var valForCompare = $.isArray(valObj) ? valObj : [valObj];
                if(!dataIsSame(obj_[objLastValKey], valForCompare)) { //内容obj已变
                    // console.log('val change: append new val ::::::::');
                    if(valObj) {
                        objPushVal(obj_, valObj);
                    }
                }
                //console.log(obj_);
                if(hasData(optData) && (typeof optData == 'array' || typeof optData == 'object'))
                { //有数据传入 哪怕是[] 都要判断之前的对象是否有data 有则对比更新
                    if(!isUndefined(valObj['extendParentData']) && valObj['extendParentData'] == true) {
                        renewObjData(valObj, optData);
                    }
                }
            }
        }
        if(!obj_.hasOwnProperty('value')) {
            Object.defineProperty(obj_, 'value', {
                configurable: true,//允许删除
                get: function () {
                    if(obj_[objValIsNode]) {
                        return obj_.html();
                    }
                    return this[objValObjKey];
                }
                ,set: function (V) {
                    this[objValObjKey] = V;
                    V[parentObjKey] = this;
                }
            });
        }
    }

    //检测val是否真实的obj数组 区分[obj,obj]和checkbox的[obj,obj] 就是有option属性
    function _valIsAy(valObj) {
        return $.isArray(valObj) && !isOurObj(valObj);
    }

    //格式化 {td: []} 为 {value: makeTd}
    function tdToObj(trData, trOpts) {
        //console.log('tdToObj');
        //console.log(trData);
        //这里需要继承克隆 因为tr一旦是克隆的，makeTR时会继续克隆这个TD.
        //所以这里的克隆属性要在maketr时判断是否克隆的TD，如果是，则tr无须再克隆，并且注销这个TD的克隆属性
        var tdKey = !isUndefined(trOpts['th']) ? 'th' : 'td';
        var TdOpts = trOpts[tdKey] || {};
        var callClone = trOpts[optionCallCloneKey];
        var trVal, newTd;
        if (Array.isArray(TdOpts)) {
            trVal = [];
            //console.log(TdOpts);
            TdOpts.forEach(function (opt_) {
                if(callClone) opt_[optionCallCloneKey] = true;//tr是克隆来的话  td必须也要继续克隆
                newTd = makeTD_(opt_);
                //console.log('newTd_obj');
                //console.log(newTd);
                trVal.push(newTd);
            })
        } else {
            if(callClone) TdOpts[optionCallCloneKey] = true;//tr是克隆来的话  td必须也要继续克隆
            trVal = makeTD_(TdOpts);
        }
        trOpts['value'] = trVal;
        //console.log(trVal);

        //创建单个TD
        function makeTD_(opt__) {
            //console.log('trData');
            //console.log(trData);
            //console.log('makeTD_____________________', opt__['data']);
            //console.log('trdata',trData);
            var newTd = tdKey =='td' ? global.makeTd(opt__) : global.makeTh(opt__);
            //console.log('hasData trData _____________________', hasData(trData));
            if(isStrOrNumber(opt__['data']) && hasData(trData)) {
                //console.log('makeTD2222_____________________');
                //console.log(opt__['data']);
                newTd['data'] = trData;
            }
            //console.log('opt2__');
            //console.log(opt__);
            //console.log(opt__[optionCallCloneKey]);
            return newTd;
        }
        return trOpts;
    }
    //清空子对象
    function __clearSons(obj_) {
        if(obj_[objValObjKey]) {
            obj_[objValObjKey].forEach(function (o) {
                o.remove();
            });
        }
        obj_[objValObjKey] = [];
        obj_[objLastValKey] = [];
    }
    //创建文本dom /a/p/span/div/li/td/em/b/i/
    function makeDom(sourceOptions) {
        var opt = cloneData(sourceOptions);
        opt = opt || {};
        var tag = opt['tag'] || 'span';
        var defaultOps = opt['options'] || {};
        defaultOps['tag'] = tag;
        //必须设置name 否则拖动换排序时无法切换对象的子name
        if(isUndefined(defaultOps['name'])) {
            //td span 不需要加name 因为它们不参与循环
            if(tag == 'tr' || tag == 'li') {
                defaultOps['name'] = createRadomName(tag);
            } else if(tag =='li' && tag =='tr') {
                var newname = createRadomName(tag);
                defaultOps['name'] = newname;
            }
        }
        var extendAttr = opt['extend_attr'] || {};
        var afterCreate = getOptVal(opt, ['afterCreate', 'after_create'], false);
        if(isUndefined(defaultOps['value'])) defaultOps['value'] = ' ';//必须输入空文本只能执行node替换
        var options = $.extend({}, defaultOps);
        options = $.extend({}, options, extendAttr);//支持外部扩展属性 如 a 的 href
        var obj = $('<'+ tag +'></'+ tag +'>');
        obj.whenUpdate = null; //当被更新时触发事件 比如makeBar的update
        obj.nodeObj = []; //初始化dom节点
        obj.htmObj = [];//初始化element节点
        obj[objValObjKey] = [];//初始化对象的子对象
        obj[objLastValKey] = [];//初始化对象最后更新的子对象
        obj['last_options'] = getOptVal(options, 'last_options', {});
        obj[objValIsNode] = getOptVal(opt, [objValIsNode], true);
        //允许在外部定义 如btn
        if(!isUndefined(opt[objValIsNode])) {
            obj[objValIsNode] = opt[objValIsNode];
        } else {
            if(!isStrOrNumber(options['value'])) {
                obj[objValIsNode] = false;
            }
        }
        //当外部修改obj的val时，直接更新
        //when value is changed by outside
        obj.renewVal = function(newV, opt_) {
            if(isUndefined(opt_)) opt_ = obj['options'];
            //console.log(obj);
            opt_['value'] = newV;
            if(isStrOrNumber(newV)) {
                obj[objValIsNode] = true; //修改obj的内容类型
                domAppendNode(obj, opt_);
            } else {  //value is obj
                obj[objValIsNode] = false;
                obj.html('');
                __clearSons(obj); //如果是对象的val被修改，提前清空sons
                domAppendObj(obj, opt_);
            }
        };
        //触发子数据更新
        obj.renewSonData = function(newData) {
            newData = newData || [];
            if(!hasData(obj[objValObjKey])) return;
            // console.log('renew SonData.sons:', obj[objValObjKey], newData);
            $.each(obj[objValObjKey], function (n, son) {
                if(!son)  return;
                //子对象是否继承父data
                if(!isUndefined(son['extendParentData']) && son['extendParentData'] == true) {
                    renewObjData(son, newData);
                } else {
                    // console.log('!extendParentData', son);
                }
            })
        };
        //append方法扩展
        obj.appendObj = function(newObj) {
            obj.renewVal(newObj, obj['options']);
        };
        obj.domAppendVal = function(opt, hasSetData) {
            var obj_ = this;
            if(isUndefined(opt['value']) || opt['value']=='') opt['value'] = ' ';//必须输入空文本只能执行node替换
            opt= opt || [];
            if(!hasData(opt)) return '';
            // console.log('dom.AppendVal');
            // console.log(this);
            // console.log(opt['value']);
            tag = tag || 'span';
            //console.log(obj_);
            if(tag == 'tr') { //value is obj
                domAppendTrObj(obj_, opt);
            } else {
                var optValStr = opt['value'] || opt['son'] || '';
                if(isStrOrNumber(optValStr) ) {
                    domAppendNode(obj_, opt, hasSetData);
                } else {  //value is obj
                    // __clearSons(obj_); //如果是对象的val被修改，提前清空sons
                    domAppendObj(obj_, opt);
                }
            }
        }
        //外部设置val
        obj.extend({
            //主动更新数据
            renew: function(optionsGet) {
                optionsGet = optionsGet || {};
                // if(tag == 'a') {
                //     console.log('renew a_______________:', this, optionsGet['data']);
                // }
                optionDataFrom(this, optionsGet);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                //先设定options参数 下面才可以修改options
                var hasSetData = !isUndefined(optionsGet['data']);
                strObj.formatAttr(obj, optionsGet, 0, hasSetData);
                obj.domAppendVal(optionsGet, hasSetData);
                obj['last_options'] = $.extend({}, optionsGet);//设置完所有属性 要更新旧的option
                obj[objLastValKey] = obj[objValObjKey];//设置完所有属性 要更新旧的val
                //console.log('finish');
                //console.log(this);
            },
            //克隆当前对象 name要重新生成
            cloneSelf: function(optionsGet) {
                optionsGet[optionCallCloneKey] = true;
                optionsGet['name'] = createRadomName(optionsGet['tag']);
                return makeDom({
                    'tag': tag,
                    'options': optionsGet,
                    'extend_attr': extendAttr,
                    'after_create': afterCreate
                });
            },
            updates: function(dataName, exceptObj) {//数据被动同步
                //console.log('updates this');
                exceptObj = exceptObj || [];
                if(options['bind'] && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(obj);
                    //console.log('updateNodeText this');
                    updateNodeText(this,$.trim(options['bind']), exceptObj);
                    if(this.whenUpdate) {
                        this.whenUpdate(this, getObjData($.trim(options['bind'])));
                    }
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) {
                    //console.log('renew ObjAttr this');
                    //console.log(this);
                    renewObjBindAttr(this, dataName);
                }
            }
        });
        obj.renew(defaultOps);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        optionGetSet(obj, defaultOps);
        objBindVal(obj, defaultOps);//数据绑定
        addCloneName(obj, defaultOps);//支持克隆
        if(afterCreate) afterCreate(obj, defaultOps); //初始化内容再写入内容
        return obj;
    }

    //创建简单的标签对象
    global.makeA = function(defaultOps) {
        var funcAfterCreate = function (thisObj, option_) {
            //绑定拖拽事件
            callBindDragObj(thisObj, option_);
        };
        var extendAttr = {};
        if(isUndefined(defaultOps['href'])) extendAttr = {'href' : 'javascript: void(0);'};
        return makeDom({
            'tag': 'a',
            'options': defaultOps,
            'extend_attr': extendAttr,
            'after_create': funcAfterCreate
        });
    };
    global.makeDiv = function(defaultOps) {
        var funcAfterCreate = function (thisObj, option_) {
            //绑定拖拽事件
            callBindDragObj(thisObj, option_);
        };
        return makeDom({
            'tag': 'div',
            'options': defaultOps,
            'after_create': funcAfterCreate
        });
    };
    global.makeUl = function(defaultOps) {
        return makeDom({
            'tag': 'ul',
            'options': defaultOps
        });
    };
    global.makeLi = function(defaultOps) {
        if(!isUndefined(defaultOps['data'])) {
            if(isUndefined(defaultOps['data']['index'])) {
                defaultOps['data']['index'] = 0;
            }
        }
        return makeDom({
            'tag': 'li',
            'options': defaultOps
        });
    };
    global.makeTd = function(defaultOps) {
        //console.log('make td');
        return makeDom({
            'tag': 'td',
            'options': defaultOps
        });
    };
    global.makeTh = function(defaultOps) {
        return makeDom({
            'tag': 'th',
            'options': defaultOps
        });
    };
    global.makeTr = function(defaultOps) {
        return makeDom({
            'tag': 'tr',
            'options': defaultOps
        });
    };
    global.makeP = function(defaultOps) {
        return makeDom({
            'tag': 'p',
            'options': defaultOps
        });
    };
    global.makeB = function(defaultOps) {
        return makeDom({
            'tag': 'b',
            'options': defaultOps
        });
    };
    global.makeI = function(defaultOps) {
        return makeDom({
            'tag': 'i',
            'options': defaultOps
        });
    };
    global.makeSpan = function(defaultOps) {
        var funcAfterCreate = function (thisObj, option_) {
            //绑定拖拽事件
            callBindDragObj(thisObj, option_);
        };
        return makeDom({
            'tag': 'span',
            'options': defaultOps,
            'after_create': funcAfterCreate
        });
    };
    global.makeLabel = function(defaultOps) {
        var funcAfterCreate = function (thisObj, option_) {
            //绑定拖拽事件
            callBindDragObj(thisObj, option_);
        };
        return makeDom({
            'tag': 'label',
            'options': defaultOps,
            'after_create': funcAfterCreate
        });
    };
    global.makeH1 = function(defaultOps) {
        var funcAfterCreate = function (thisObj, option_) {
            //绑定拖拽事件
            callBindDragObj(thisObj, option_);
        };
        return makeDom({
            'tag': 'h1',
            'options': defaultOps,
            'after_create': funcAfterCreate
        });
    };
    global.makeH2 = function(defaultOps) {
        return makeDom({
            'tag': 'h2',
            'options': defaultOps
        });
    };
    global.makeH3 = function(defaultOps) {
        return makeDom({
            'tag': 'h3',
            'options': defaultOps
        });
    };
    global.makeH4 = function(defaultOps) {
        return makeDom({
            'tag': 'h4',
            'options': defaultOps
        });
    };
    global.makeH5 = function(defaultOps) {
        return makeDom({
            'tag': 'h5',
            'options': defaultOps
        });
    };
    global.makeH6 = function(defaultOps) {
        return makeDom({
            'tag': 'h6',
            'options': defaultOps
        });
    };

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
    global.makeList = function(sourceOptions) {
        //console.log('makeList.:');
        var options = cloneData(sourceOptions);
        options = options || {};
        var obj = $('<ul></ul>');
        obj[objValObjKey] = [];//初始化对象的子对象
        obj[objLastValKey] = [];//初始化对象的子对象
        obj['last_options'] = getOptVal(options, 'last_options', {});
        obj[objValIsNode] = false;
        obj['default_li'] = null;
        if(isUndefined(options['value'])) options['value'] = []; //为了外部可以统一输出 要配置value(其自身可能是用li来设置值的)
        var liIsArray = false;
        if($.isArray(options['li'])) liIsArray = true ;//数组格式的li不需要执行渲染
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
        obj.lazyCall = getOptVal(options, ['lazy_call', 'lazyCall'], null);

        //更新list.data
        obj.renewSonLen = function (opt) {
            if(liIsArray) {
                return ;//数组格式的li不需要执行渲染
            }
            var sons = this[objValObjKey];
            var sonFirst = sons[0];
            if(isUndefined(opt['data'])) return;
            var maxNum = getOptVal(opt, ['maxNum', 'max_num', 'maxLen', 'max_len'], false);
            var newData = cloneData(opt['data']);
            if(hasData(newData) && maxNum && isNumber(maxNum)) {
                newData = newData.slice(0, maxNum);
            }
            //console.log('renew.SonLen');
            //console.log('sons');
            //console.log(newData);
            //如果之前产生过多的儿子而新数量变少要剔除
            var lastValLen = sons.length;
            var nowValLen = newData.length;
            var tmpIndex;
            //console.log('lastValLen:'+ lastValLen);
            //console.log('nowValLen:'+ nowValLen);
            if(lastValLen > nowValLen) { //多出来 裁掉
                //console.log('remove more');
                //如果没有data，要copy一个临时的
                if(nowValLen ==0 && !obj['default_li'] ) {
                    //保留之前的li的value 继续复制一个li 不能从源opt开始克隆，会丢失之后渲染的li.value
                    var newOpt = cloneData(sonFirst['options']);
                    newOpt = getSourceOpt(newOpt);
                    obj['default_li'] = sonFirst.cloneSelf(newOpt);
                }
                sons.splice(nowValLen, lastValLen-nowValLen).forEach(function (o) {
                    if(o.name) {
                        delete global[o.name];
                        //console.log('remove_name:'+ o.name, o);
                    }
                    o.remove();
                });
                for(tmpIndex = 0; tmpIndex < nowValLen ; tmpIndex++) {
                    newTmpData = newData[tmpIndex];
                    newTmpData['index'] = tmpIndex;
                    renewObjData(sons[tmpIndex], newData[tmpIndex]);
                }
                this[objLastValKey] = [];
            } else if(lastValLen < nowValLen) { //数据累加 要克隆第一个li
                //console.log('lastValLen:'+ lastValLen);
                //console.log('nowValLen:'+ nowValLen);
                //console.log(sons);
                //console.log(sonFirst);
                var tmpIndex,newLi, newTmpData;
                //console.log(newData);
                for(tmpIndex = 0; tmpIndex < nowValLen ; tmpIndex++) {
                    newTmpData = newData[tmpIndex];
                    if(!isUndefined(sons[tmpIndex])) {
                        renewObjData(sons[tmpIndex], newData[tmpIndex]);
                    } else {
                        if(!hasData(sons)) {
                            //保留之前的li的value 继续复制一个li 不能从源opt开始克隆，会丢失之后渲染的li.value
                            var newOpt = cloneData(obj['default_li']['options']);
                            newOpt = getSourceOpt(newOpt);
                            newLi = obj['default_li'].cloneSelf(newOpt);
                            sonFirst = newLi;
                        } else {
                            //console.log('sonFirst___is___:');
                            //console.log(sonFirst);
                            //console.log(sonFirst['options']['value']);
                            //保留之前的li的value 继续复制一个li 不能从源opt开始克隆，会丢失之后渲染的li.value
                            var newOpt = cloneData(sonFirst['options']);
                            newOpt = getSourceOpt(newOpt);
                            newLi = sonFirst.cloneSelf(newOpt);
                            //console.log('sonFirst.clone');
                            //console.log(newLi);
                        }
                        //console.log('cloneSonLi');
                        //console.log(newLi);
                        newLi[parentObjKey] = obj;
                        sons[sons.length] = newLi;
                        //等克隆完li的属性才能更新data 不然提早渲染的data可能无法再次刷新
                        newData[tmpIndex]['index'] = tmpIndex;
                        newLi['data'] = newData[tmpIndex];
                        this.append(newLi);
                    }

                }
                this[objLastValKey] = sons;
            } else {
                //长度未变 只更新子data
                obj.renewSonData(newData);
            }

            //console.log(sons);
        };
        //更新list子对象的数据
        obj.renewSonData = function(newData){
            // console.log('renew.SonData');
            // console.log(obj);
            // console.log(newData);
            // return;
            newData = newData || [];
            if(!hasData(obj[objValObjKey])) return;
            //数组格式的li不需要执行循环
            if(liIsArray) {
                //console.log('liIsArray:');
                if ($.isArray(newData)) {
                    newData = newData[0];
                }
                //console.log(obj[objValObjKey]);
                $.each(obj[objValObjKey], function (n, son) {
                    renewObjData(son, newData);
                });
            } else {
                //console.log('li no Array:');
                //console.log('sons');
                var sonData;
                $.each(obj[objValObjKey], function (n, son) {
                    //console.log('son__'+ n);
                    //console.log(son);
                    sonData = newData[n]||[];
                    if(isUndefined(sonData['index'])) sonData['index'] = n;
                    renewObjData(son, sonData);
                });
            }
        };
        //克隆li的可循环数据
        function cloneListSon(obj, options) {
            //console.log('clone.ListSon:');
            //console.log(obj);
            var sonKey = 'li';
            var optionsData = options['data'] || {}; // data: {son_data}
            var liOptions = $.extend({}, options[sonKey] || {});//子的公共配置 li: {}
            //如果父list是克隆的，子li也要集成克隆
            if(!isUndefined(options[optionCallCloneKey])) {
                liOptions[optionCallCloneKey] = options[optionCallCloneKey];
            }
            //创建单个子对象
            function makeOneSon(index, tmpNewData_) {
                var liOpt = cloneData(options[sonKey] ,{});//子的公共配置 li: {}
                var newTestOpt = $.extend({}, options[sonKey] || {});//子的公共配置 li: {}
                index = index || 0;
                //console.log('liOpt_________________:');
                //console.log(liOpt);
                //console.log('index:'+ index);
                if(!isUndefined(obj[objValObjKey][index])) {//exist li
                    //console.log('exist li:'+ index);
                    var lastLi = obj[objValObjKey][index];
                    liOpt['data'] = tmpNewData_;
                    lastLi.renew(liOpt);
                } else {
                    var liObj;
                    //console.log('index:'+ index);
                    var checkDataChange = optionAddData(newTestOpt, tmpNewData_);
                    var dataHashChange = checkDataChange[1];
                    //console.log('liOpt');
                    //console.log('liOptWidthNewData');
                    //console.log(liOptWidthNewData);
                    liOpt = getSourceOpt(liOpt);
                    // //首次空时直接用第一份参数创建li
                    if(obj[objValObjKey].length == 0) {
                        //创建li时 其data可能是字符串 不能给格式化后的data它 否则后面无法再被父data更新
                        //从第2个开始的li的所有子内容都必须是克隆的，如：value之前是一个obj 则必须克隆新的value
                        if(index>0) {
                            liOpt[optionCallCloneKey] = true;
                        }
                        //console.log('make__________SonLi',index);
                        //console.log(liOpt);
                        liObj = obj.makeSonLi(liOpt);
                        //console.log(liObj);
                    } else {
                        liObj = obj[objValObjKey][0].cloneSelf(liOpt);//克隆要拿原数据 带括号的

                    }
                    if(dataHashChange){
                        liObj['data'] = tmpNewData_;
                    }
                    //console.log(liObj['value']);
                    objPushVal(obj, liObj);
                }
            }
            var index = 0,tmpNewData;
            //数组格式的li不需要执行数据循环
            if(liIsArray) {
                //console.log('here');
                $.each(liOptions, function (n, liOpt) {//data循环数据
                    //console.log(optionsData);
                    if ($.isArray(optionsData)) {
                        optionsData = optionsData[0];
                    }
                    //console.log(liOpt);
                    var OptBack = optionAddData(liOpt, optionsData);
                    liOpt = OptBack[0];
                    var liObj = obj.makeSonLi(liOpt);
                    //console.log(liObj);
                    index ++;
                    objPushVal(obj, liObj);
                });
            } else {
                //有数据才循环
                if(optionsData && hasData(optionsData) ) {
                    //console.log('repeat_data makeli');
                    //console.log(obj);
                    //console.log(optionsData);
                    $.each(optionsData, function (key_, val_) {//data循环数据
                        //console.log('key_:'+ key_ +',val_:');
                        //console.log(val_);
                        if (Array.isArray(optionsData)) {
                            //console.log('isArray optionsData');
                            tmpNewData = val_;
                            tmpNewData['index'] = index;
                        } else {
                            //console.log('isObj optionsData');
                            tmpNewData = {};
                            tmpNewData[key_] = val_;
                        }
                        //console.log('tmpNewData');
                        //console.log(tmpNewData);
                        //console.log('tmpO');
                        //console.log(tmpO);
                        makeOneSon(index, tmpNewData);
                        index ++;
                    });
                } else {//nodata  li
                    //console.log('no data');
                    //console.log(obj);
                    //默认继承父的data 可以用来防止li属性的{}获取不到data而产生多余的全局绑定变量
                    if(isUndefined(liOptions['data'] && hasData(optionsData))) liOptions['data'] = [];
                    //console.log('first_li_new___:');
                    //console.log(liOptions);
                    var liObj = obj.makeSonLi(liOptions);
                    objPushVal(obj, liObj);
                    //console.log(obj);
                    //console.log(liObj);
                }
            }

        }
        obj.extend({
            //主动更新数据
            renew: function(options_) {
                obj.INeedParentValFlag = getOptVal(options_, ['need_parent_val', 'needParentVal'], false);//需要父参数渲染好才能请求url
                obj.INeedParentKey = getOptVal(options_, ['need_parent_key', 'needParentKey', 'need_parent_name', 'needParentName'], 'demo_name');
                // console.log('renew list');
                // console.log(obj);
                // console.log(options_);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                //先设定options参数 下面才可以修改options
                optionDataFrom(this, options_);
                strObj.formatAttr(this, options_);//其内容 已经在clone li里全部生成过了 只差数据来格式化了
                //console.log('end formatAttr');
                this['last_options'] = $.extend({}, options_);//设置完所有属性 要更新旧的option
                this[objLastValKey] = obj[objValObjKey];//设置完所有属性 要更新旧的option
                //console.log('end ');
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return makeList(optionsGet);
            },
            //创建子对象 外部可以触发此方法
            makeSonLi: function(optionsGet) {
                // list的value如果写{}则数据来源于data，反之数据来源于全局
                return makeLi(optionsGet);
            }
        });
        cloneListSon(obj, options);
        obj.renew(options);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        optionGetSet(obj, options);
        objBindVal(obj, options);//数据绑定
        addCloneName(obj, options);//支持克隆
        //对象直接设置了data 可以触发 延迟执行
        var lazyCall = getOptVal(options, ['lazy_call', 'lazyCall'], null);
        var dataFrom = getOptVal(options, ['data_from', 'dataFrom'], null);
        if(lazyCall) {
            //设置了data 可以立刻延迟执行
            if(hasData(getOptVal(options, ['data']))) {
                lazyCall(obj, livingObj);
            } else {
                //没有设置 data_from 可以立刻延迟执行
                if(!dataFrom) {
                    lazyCall(obj, livingObj);
                }
            }
        }
        //console.log(obj);
        return obj;
    };
    //生成自定义表格
    /* 属性：
     {
     'class': 'xxx_table',
     'id': '',
     tr: [
     {id: 'tr1', 'class',
     td: [{ value: '', 'class':''}, {} ]
     ]
     }
     //全选
     obj.selectAll(idname)
     //已选id
     obj.selected(idname)
     */
    global.makeTable = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        var obj = $('<table width="100%" border="0"></table>');
        var tbody = $('<tbody></tbody>');
        obj.append(tbody);
        obj.tag = 'table';
        obj.tBody = tbody;
        obj[objValObjKey] = [];//记录对象的子 tr 无须每次更新数据都重新生成 tr
        obj[objLastValKey] = [];
        obj['last_options'] = getOptVal(options, 'last_options', {});
        obj[tableWithDataTrKey] = [];//带data数据的tr对象 作循环打印的本体
        obj[tableNoDataTrKey] = [];//不带data数据的tr对象 不作循环
        obj[objValIsNode] = false;
        obj['tr_clone_demo'] = null; //循环数据的克隆母版
        var tableIsClone = options[optionCallCloneKey] === true;
        //获取tr的循环个数
        var _getTrNum = function(opt) {
            var trOpt = opt['tr'] || opt['tr_repeat'];
            var trOptLen;
            if(!$.isArray(trOpt)) {
                trOptLen = 1;
            } else {
                trOptLen = trOpt.length;
            }
            return trOptLen;
        };

        //更新table.data 如果含有带循环的tr 则只更新data的tr；反之更新全部tr
        var cloneDefaultRepeatTr = function(index, tmpData) {
            var cloneTr = obj['tr_clone_demo'][index];
            var cloneOpt = cloneData(cloneTr['options']);
            cloneOpt = getSourceOpt(cloneOpt);
            cloneOpt['tag'] = 'tr';
            var newTr = cloneTr.cloneSelf(cloneOpt);
            newTr['data'] = tmpData; //必须克隆完再更新data
            newTr[parentObjKey] = obj;//分配父对象
            obj[objValObjKey].push(newTr); //带数据的tr 缓存obj的子对象
            obj[tableWithDataTrKey].push(newTr); //带数据的tr 缓存obj的子对象
            obj.tBody.append(newTr);
            return newTr;
        };
        obj.renewSonLen = function (opt) {
            var sons;
            var demoData = opt['data'];
            var trOptLen = _getTrNum(opt);
            if(hasData(this[tableWithDataTrKey]) || obj['tr_clone_demo']) {
                sons = this[tableWithDataTrKey] ;
                var newData = cloneData(demoData);
                if(!$.isArray(newData)) newData = [newData];
                //如果之前产生过多的儿子而新数量变少要剔除
                var lastValLen = sons.length;
                var nowValLen = newData.length * trOptLen;
                //更新data
                var dataLines = newData.length;
                var tmpTrGroupid; //遍历在第N行data
                var tmpTrGroupFromIndex;
                var tmpIndex;
                var tmpData;
                if(lastValLen > nowValLen) { //多出来 裁掉
                    sons.splice(nowValLen, lastValLen-nowValLen).forEach(function (o) {
                        if(o.name) {
                            delete global[o.name];
                        }
                        o.remove();
                    });
                    //不能直接赋值sons 会等同两个对象
                    obj[objValObjKey] = [];
                    sons.forEach(function(tmpSon_) {
                        obj[objValObjKey].push(tmpSon_);
                    });
                    //遍历data数据
                    for(tmpTrGroupid = 0; tmpTrGroupid < dataLines; tmpTrGroupid++) {
                        tmpTrGroupFromIndex = tmpTrGroupid * trOptLen;
                        tmpData =  newData[tmpTrGroupid];
                        tmpData['index'] = tmpTrGroupid;
                        //一组组更新(创建)tr
                        for(var i2 = 0 ; i2 < trOptLen; i2 ++) {
                            tmpIndex =  tmpTrGroupFromIndex ++;
                            if(!isUndefined(sons[tmpIndex])) {
                                sons[tmpIndex]['data'] = tmpData;
                            }
                        }
                    }
                } else if(lastValLen < nowValLen) { //数据累加 要克隆第一个tr 并且累加到最后一个循环的对象背后
                    for(tmpTrGroupid = 0; tmpTrGroupid < dataLines; tmpTrGroupid++) {
                        tmpTrGroupFromIndex = tmpTrGroupid * trOptLen;
                        tmpData =  newData[tmpTrGroupid];
                        //一组组更新/创建tr
                        for(var i2 = 0 ; i2 < trOptLen; i2 ++) {
                            tmpIndex =  tmpTrGroupFromIndex ++; //在所有son里面 当前是第几个tr
                            if(!isUndefined(sons[tmpIndex])) {
                                sons[tmpIndex]['data'] = tmpData;
                                // console.log('renew_tmpIndex, i2:', i2, 'tmpIndex', tmpIndex);
                            } else {
                                //直接用虚拟tr进行克隆
                                cloneDefaultRepeatTr(i2, tmpData);
                                tmpIndex ++;
                            }
                        }
                    }
                } else {
                    obj.renewOldRepeatTr(newData);
                }
            }

            //更新单独无data的tr1
            __renrewNoDataTrs(cloneData(demoData));
        };
        //更新不循环的tr1
        var __renrewNoDataTrs = function(newData) {
            var sons;
            if(hasData(obj[tableNoDataTrKey])) {//只更新循环部分的tr
                sons = obj[tableNoDataTrKey];
                //不循环的tr也可以读取数组的长度
                // if($.isArray(newData)) newData = newData[0];
                $.each(sons, function (n, son) {
                    //console.log('__renrewNoDataTrs son');
                    //console.log(son);
                    renewObjData(son, newData);
                });
            }
        };
        //更新循环的tr的date
        obj.renewOldRepeatTr = function(newData) {
            var sons;
            // console.log('renew renewOld RepeatTr');
            // console.log(newData);
            if(hasData(this[tableWithDataTrKey])) {//只更新循环部分的tr
                sons = this[tableWithDataTrKey];
                //console.log(sons);
                if(!$.isArray(newData)) newData = [newData];
                var sonData;
                var trOptLen = _getTrNum(obj['options']||[]);
                $.each(sons, function (n, son) {
                    sonData = newData[Math.ceil((n+1)/trOptLen)-1]; //数据要每隔一组tr再更新
                    if(!sonData) sonData = []; //数据突然为空
                    renewObjData(son, sonData);
                })
            }
        };
        //创建多个子对象
        var lastTrNoData = null;//上一个无data的tr 用于确定有data的tr的产生位置
        function makeRepeatTrs(trOpts, trData, cloneIf) {
            var sons = obj[tableWithDataTrKey];
            //new trs
            var trObj;
            //创建第一组tr 不用克隆 并且存储默认的tr虚拟对象 防止tr渲染清空后无法再次克隆tr
            if(!cloneIf) {
                // console.log('!cloneIf.each:___', trOpts);
                var addDefaultTr = false;
                if(!obj['tr_clone_demo']) {
                    obj['tr_clone_demo'] = [];
                    addDefaultTr = true;
                }
                var trOpt_;
                $.each(trOpts, function (n, tmpOpt_) {
                    trOpt_ = cloneData(tmpOpt_);
                    trObj = global.makeTr(trOpt_);
                    trObj[parentObjKey] = obj;//分配父对象
                    //有data则写入table，无data则只写入虚拟tr_default
                    if(hasData(trData)) {
                        trObj['data'] = trData; //必须克隆完再更新data
                        obj[objValObjKey].push(trObj); //带数据的tr 缓存obj的子对象
                        obj[tableWithDataTrKey].push(trObj); //带数据的tr 缓存obj的子对象
                        if(lastTrNoData) {
                            lastTrNoData.after(trObj);
                            lastTrNoData = trObj;
                            // console.log('lastTrNoData.after',trObj);
                        } else {
                            obj.tBody.append(trObj);
                        }
                        if(addDefaultTr) {
                            //保留之前的li的value 继续复制一个li 不能从源opt开始克隆，会丢失之后渲染的li.value
                            var newOpt = cloneData(trObj['options']);
                            newOpt = getSourceOpt(newOpt);
                            newOpt['tag'] = 'tr';
                            obj['tr_clone_demo'].push(trObj.cloneSelf(newOpt));
                        }
                    } else if(addDefaultTr) {
                        obj['tr_clone_demo'].push(trObj);
                    }
                });
            } else {
                $.each(trOpts, function (n, trOpt_) {
                    trOpt_['tag'] = 'tr';
                    trObj = sons[n].cloneSelf(trOpt_);
                    trObj['data'] = trData; //必须克隆完再更新data
                    trObj[parentObjKey] = obj;//分配父对象
                    obj[objValObjKey].push(trObj); //带数据的tr 缓存obj的子对象
                    obj[tableWithDataTrKey].push(trObj); //带数据的tr 缓存obj的子对象
                    if(lastTrNoData) {
                        lastTrNoData.after(trObj);
                        lastTrNoData = trObj;
                    } else {
                        obj.tBody.append(trObj);
                    }
                });
            }
        }
        //克隆多行的可数据循环的tr
        function createRepeatDataTrs(options) {
            var optionsData = options['data'] || null; // data: {son_data}
            var trOptions = $.extend({}, options['tr'] || {});//子的公共配置 tr: {}
            var trGroupLen = 0; //循环的tr内部数量
            $.each(trOptions, function () {
                trGroupLen ++;
            });
            //有数组数据才循环
            if($.isArray(optionsData) && hasData(optionsData)) {
                //console.log(optionsData);
                var cloneIf = false;
                //console.log('createRepeat.DataTrs ______________________________');
                $.each(optionsData, function (key_, tmpData) {//data循环数据
                    if(key_ > 0) cloneIf = true;
                    makeRepeatTrs(cloneData(trOptions), tmpData, cloneIf);
                });
            } else {
                makeRepeatTrs(trOptions, optionsData, false);
            }
        }

        //写入TR
        obj.appendTrs = function(options_) {
            //提取所有的tr_ ,tr
            var tabData = options_['data'] || [];
            var i_ = 0;//计算tr出现的位置
            var findRepeatTr = false;//是否找到循环的tr
            $.each(options_, function (key_, val_) {
                if(key_.substr(0,2) == 'tr' || key_.substr(0,3) == 'tr_') {
                    if(key_ == 'tr' || key_ == 'tr_repeat') {
                        if(!$.isArray(val_)) { //tr: {td: {}}
                            options_['tr'] = [val_];
                        }
                        //console.log('create____RepeatDataTrs_:::::::::::::', key_);
                        createRepeatDataTrs(options_);
                        findRepeatTr = true;
                    } else if(key_.substr(0,2) == 'tr') {//生成不循环的tr_n 也是可以配置无循环的data的
                        //console.log(val_);
                        if(!$.isArray(val_)) { //tr_2:{td: {}}  => tr_2:[{td: {}},{td: {}}]
                            val_ = [val_];
                        }
                        val_.forEach(function (trOpt) {
                            trOpt[optionCallCloneKey] = tableIsClone;
                            delete trOpt['data'];//如果是克隆的会带上之前的data 导致无法更新自身的data 所以要提前删除
                            var trObj = global.makeTr(trOpt);
                            var tabDataFrom = getOptVal(options_, ['data_from', 'dataFrom'], null);
                            if(!tabDataFrom && hasData(tabData)) {
                                var sonOptBack = optionAddData(trOpt, tabData);
                                var tmpData = sonOptBack[0]['data'];
                                trObj['data'] = tmpData; //data不能提前赋予  否则会导致无法继承父data
                            }
                            trObj[parentObjKey] = obj; //分配父对象
                            obj[objValObjKey].push(trObj);
                            obj[tableNoDataTrKey].push(trObj);
                            lastTrNoData = trObj;
                            obj.tBody.append(trObj);
                        });
                    }
                }
                i_ ++ ;
            });
        } ;
        obj.extend({
            renew: function(options_) {
                optionDataFrom(obj, options_);
                console.log('renew table');
                //console.log(this);
                //console.log(options_);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                //先设定options参数 下面才可以修改options
                strObj.formatAttr(this, options_);
                this['last_options'] = $.extend({}, options_);//设置完所有属性 要更新旧的option
                this[objLastValKey] = obj[objValObjKey];//设置完所有属性 要更新旧的option
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                if(!isUndefined(optionsGet['name'])) {
                    var oldName = optionsGet['name'];
                    //console.log('clone objName:'+ oldName);
                    optionsGet['name'] = indexClass.nameAddNum(oldName);
                } else {
                    optionsGet['name'] = createRadomName('table'); //必须设置name 否则拖动换排序时无法切换对象的子name
                }
                return makeTable(optionsGet);
            }
        });
        Object.defineProperty(obj, 'value', {
            get: function () {
                //读取值 用于外部表单打包数据
                return this[objValObjKey];
            }
        });
        //全选
        obj.selectAll = function(inputName) {
            inputName = inputName || '';
            if(!inputName) {
                console.log('no set inputName');
                return;
            }
            //有选中 则反选
            var selectAllFlag = true;
            if(obj.find("input[name='"+ inputName +"']").first().prop('checked') == true) {
                selectAllFlag = false;
            }
            $.each(obj[objValObjKey], function (k, tr) {
                tr.find("input[name='"+ inputName +"']").prop('checked', selectAllFlag);
            });
        };
        //已选
        obj.selected = function(inputName) {
            inputName = inputName || '';
            if(!inputName) {
                console.log('no set inputName');
                return;
            }
            //有选中 则反选
            var selectIds = [], tmpTrInput_;
            $.each(obj[objValObjKey], function (k, tr) {
                tmpTrInput_ = tr.find("input[name='"+ inputName +"']");
                if(!tmpTrInput_) return;//continue
                if(tmpTrInput_.prop('checked')) {
                    selectIds.push(tmpTrInput_.val());
                }
            });
            return selectIds;
        };
        obj.appendTrs(options);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        obj.renew(options);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        optionGetSet(obj, options);
        addCloneName(obj, options);//支持克隆
        return obj;
    };

    /*创建 输入框
     属性：
     name,value,width,place,limit: 'int/float', bind: 'some_value', null_func: ''  //为空时 提交会执行
     ,readonly,title,
     ,clear,maxlen,min,max
     ,size: 'sm/lg'
     //lr_btn 添加左右控制数量的按钮 lr_btn_type: middle/left/right
     ajax:true
     修改/文件/菜单]统一参数
     url:'/?s=',
     success_key: 'id',
     success_value:'0388',
     success_func: '',
     快速修改数值   ajax: { }
     创建ajax下拉菜单
     data_key: 'data',
     menu_text:"", // 菜单显示数据
     value_key: "" //值的下标
     li: {
     })
     */
    global.makeInput = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        //input不能直接带form-control样式 会被外层div引起弯角变化
        var obj = $('<span></span>');
        var inputBind = $.trim(getOptVal(options, ['bind'], '')); //数据绑定
        var inputType = $.trim(getOptVal(options, ['type'], 'text')); //数据绑定
        obj.textClearObj  = null; //input右侧清除内容的小x
        obj.menu = null;
        obj.input = null;
        obj.realVal = null;//对象真实的value
        obj.prev = null;
        obj.hasCreateLrBtn = false; //是否已实例化左右按钮 防止二次生成
        obj['last_options'] = getOptVal(options, 'last_options', {});
        obj[objValIsNode] = false;
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
            if(isUndefined(newVal)) {
                return obj.value;
            } else {
                obj.value = newVal;
            }
        };
        //更新input的可读状态
        obj.renewReadonly = function(opt) {
            var sourceReadonly = opt['source_readonly'] || opt['readonly'] || opt['readOnly'];
            var newVal;
            var newData = opt['data'];
            if(strHasKuohao(sourceReadonly, 'public')) {
                newVal = strObj.formatStr(sourceReadonly, livingObj['data'], 0, obj, 'value');
                obj[objAttrHasKh] = true;
            } else if(strHasKuohao(sourceReadonly, 'data')) {
                newVal = strObj.formatStr(sourceReadonly, newData||{}, 0, obj, 'value');
                obj[objAttrHasKh] = true;
            } else {
                newVal = sourceReadonly;
            }
            if(strInArray(newVal, [true, 'true', 'readonly', 'readOnly']) !==-1) {
                obj.input.attr('readonly', true);
            } else {
                obj.input.removeAttr('readonly');
            }
        };
        //更新input插件val
        obj.formatVal = function (opt) {
            var sourceVal = opt['source_value'] || opt['value'];
            var newVal;
            var newData = opt['data'] || {};
            // console.log('format.InputVal:', newData, sourceVal);
            var sourceValIsPub = strHasKuohao(sourceVal, 'public');
            var sourceValIsData = strHasKuohao(sourceVal, 'data');
            var renewBind = true;
            if(sourceValIsPub ||sourceValIsData) {
                if(!opt['source_value']) opt['source_value'] = sourceVal;
                if(sourceValIsPub) {
                    newVal = strObj.formatStr(sourceVal, livingObj['data'], 0, obj, 'value');
                } else if(sourceValIsData) {
                    newVal = strObj.formatStr(sourceVal, newData||{}, 0, obj, 'value');
                }
                obj[objAttrHasKh] = true;
            } else {
                newVal = sourceVal;
            }
            obj[objAttrHasKh] = true;
            obj.setRealInputVal(newVal, renewBind, true, [obj]);
            if(obj.setLrBtnDisable) obj.setLrBtnDisable();
            if(obj.lazyCall) {
                obj.lazyCall(obj, opt['data'] || {}, livingObj);
            }
        };
        //更新size的样式
        obj.renewSizeClass = function (newSize) {
            var obj_ = this;
            var lrBtnSizeClass = 'input-group';
            var opt = obj['options'];
            if(sizeIsXs(newSize)) {
                lrBtnSizeClass = 'diy_input_box input-group-xs';
            } else if(sizeIsSm(newSize)) {
                lrBtnSizeClass = 'diy_input_box input-group-sm';
            } else if(sizeIsMd(newSize)) {
                lrBtnSizeClass = 'diy_input_box input-group-md';
            } else if(sizeIsLg(newSize)) {
                lrBtnSizeClass = 'diy_input_box input-group-lg';
            }
            var lr_btn = getOptVal(opt, ['lr_btn'], null);
            if(lr_btn) {
                var lastClass = opt['class'];
                var lastClassExt = opt['class_extend'];
                if(lastClassExt) lastClass = classAddSubClass(lastClass, lastClassExt, true);
                if(lrBtnSizeClass) lastClass = classAddSubClass(lastClass, lrBtnSizeClass, true);
                obj_.attr('class', lastClass);
            } else {
                obj_.find('.input-group').attr('class', lrBtnSizeClass);
            }
        };
        //input赋值 更新数据绑定
        obj.setRealInputVal = function(newVal, callRenewBindData, resetDomVal, exceptObj) {
            // console.log('set.RealInputVal:'+ newVal);
            exceptObj = exceptObj || [];
            obj.realVal = newVal;
            resetDomVal = isUndefined(resetDomVal) ? true : resetDomVal;//更新自己内容
            callRenewBindData = isUndefined(callRenewBindData) ? true : callRenewBindData;//召唤同步数据
            if(callRenewBindData && inputBind) {
                if($.inArray(obj, exceptObj) ==-1)  exceptObj.push(obj);
                // console.log('update.BindObj:', inputBind, newVal);
                updateBindObj($.trim(inputBind), newVal, exceptObj);
                //console.log('sss', obj[objBindAttrsName]);
                if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][inputBind])) {
                    renewObjBindAttr(obj, inputBind);
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
        obj.createInput = function(optionCopy) {
            var option_ = cloneData(optionCopy);
            //console.log('create.Input:');
            var valueKey = getOptVal(option_, ['value_key', 'valueKey'], '');
            var li_num = option_['li_num'] ? parseInt(option_['li_num']) : false;
            var input_useClearBtn = !isUndefined(options['clear']);//使用清空内容的按钮
            var searchMenuOpt = !isUndefined(option_['menu']) ? option_['menu'] : null;
            var searchLiOpt = getOptVal(searchMenuOpt, ['li'], null);
            var inputKeyUp = option_['keyup'] || '';
            var inputChange = option_['change'] || '';
            var dataKey = option_['data_key'] || 'data'; //menu的post回调的数据来源
            var issetMaxVal = !isUndefined(option_['max']);//不能用 || 写法 因为0也是设置值
            var issetMinVal = !isUndefined(option_['min']);
            var maxVal = issetMaxVal ? parseFloat(option_['max']) : '';//数字不能用bool来判断
            var minVal = issetMinVal ? parseFloat(option_['min']) : '';
            var inputTypeAuto =  option_['type'] || 'text';
            var useLrBtn = getOptVal(option_, ['lr_btn'], null);//使用左右数量按钮
            var readonly = getOptVal(option_, ['readonly', 'readOnly'], null);//readonly
            var autocomplete = getOptVal(option_, ['autocomplete'], 'off');//off
            var lrBtnStep = parseFloat(option_['lr_btn_step']) || 1;//左右-+按钮 增减的跳度
            var lrBtnType = option_['lr_btn_type'] || 'middle';//左右-+按钮默认样式 middle right left
            var callKeys = getCallData(option_);
            var ajaxPostName = getOptVal(option_, ['post_name', 'postName'], (option_['name']||'noname'));// ajax 提交key 默认是 name|post_name
            var ajaxEdit = !isUndefined(option_['ajax']) || false;
            var ajaxEditData = option_['post_data'] ||  option_['postData'] || false;
            var ajaxIfKeyLenOver = !isUndefined(option_['post_min']) ? option_['post_min'] : 0;//ajax触发请求需要输入的最少字数
            var successKey = callKeys['success_key'];
            var successVal = callKeys['success_value'];
            var successFunc = callKeys['success_func'];
            var errFunc = callKeys['err_func'];
            var inputData = option_['data'] || null;//  data
            var inputPostData = getOptVal(option_, ['post_data', 'postData'], null);//  ajax_post_data
            if(successVal && !$.isArray(successVal)) successVal = successVal.toString().split(',');
            var keyup_extend = null;
            var change_extend = null;//输入框的 change 事件
            var parentName = option_['name'] || '';
            // row[filename][1] 这样的name给到文件上传是在后台是获取不到的 所以要换成file
            if(/([a-zA-Z_]+[a-zA-Z_\d.]*)(\[([a-zA-Z_]+[a-zA-Z_\d.]*)\])*\[([a-zA-Z_]+[a-zA-Z_\d.]*)\]$/.test(parentName) && inputTypeAuto  == 'file') {
                parentName = createRadomName('file');
            }
            obj.input =  $('<input class="diy_input" type="'+ inputTypeAuto +'" autocomplete="'+ autocomplete +'" name="'+ parentName +'" />');
            if(readonly !== null) {
                obj.input.attr('readOnly', true);
            }
            //文件上传还要用到name 外部设置表单的值 也需要用到name直接修改 这个体验还是要保留的好
            obj.empty().append(obj.input);
            if(!isUndefined(option_['place'])) obj.input.attr('placeholder', option_['place']);//input 默认背景文字  placeholder
            if(!isUndefined(option_['maxlen'])) obj.input.attr('maxlength', option_['maxlen']);//input 最多输入内容
            if(!isUndefined(option_['accept'])) {//input 允许的文件类型
                obj.input.attr('accept', option_['accept']);
                delete option_['accept'];
            }
            //添加 -+ 的左右按钮
            var subNumObj, addNumObj;
            //左右按钮 只实例化1次
            obj.setLrBtnDisable = false;
            if(useLrBtn && !obj.hasCreateLrBtn) {
                if(strHasKuohao(maxVal)) {
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
                            postData = cloneData(ajaxEditData, postData);//支持自定义打包额外的数据
                        }
                        var newUrl = obj.attr('url');
                        postData[ajaxPostName] = $.trim(thisVal);//name必须重新获取 因为上面的是临时变量
                        if(inputPostData) postData = cloneData(inputPostData, postData);
                        global.postAndDone({
                            post_url: newUrl,
                            post_data: postData,
                            success_value: successVal,
                            success_key: successKey,
                            success_func: successFunc,
                            err_func: errFunc
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
                var menuOpt = {'class': menu_pub_class_name +' ajax_menu', value: makeList(ulListOpt)};
                var searchMenu = makeDiv(menuOpt);
                obj.menu = searchMenu;
                searchMenu[parentObjKey] = obj;//设置父亲
                obj.menu['input'] = obj;//设置input 暴露给外部调取
                obj.append(obj.menu);
                //click 事件扩展
                var click_extend = function () {
                    var inputVal = obj.input.val();
                    if (inputVal ) {
                        if(obj.input.attr('data-old') != inputVal) {
                            obj.menu.show();
                            obj.addClass(menuZindexClass);
                        }
                    }
                };
                if(option_['click']) {
                    optionsEvent['click_extend'] = option_['click'];
                    optionsEvent['click'] = click_extend;
                }
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
                        if(inputPostData) postData = cloneData(inputPostData, postData);
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
                if(changeDefaultEven) changeDefaultEven(obj, e, livingObj);
                if(change_extend) change_extend(obj, e, livingObj);
            };
            if(inputType == 'file') {  //文件上传 如果需要显示预览图 直接生成在input后面
                var fileInput = obj.input;
                //将文件修改事件赋给真实的file
                var loadingUrl = getOptVal(option_, ['loadingUrl', 'loading_url'], null);
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
                            obj.createInput(optionCopy);
                            strObj.addEvents(obj); //重新绑定input的事件
                            //清空完毕后再执行事件
                            setTimeout(function () {
                                var tmpSuccessVal = getOptVal(data, [successKey], '');
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
                prevOpt['data'] = cloneData(option_['data']||{}, prevOpt['data']);
                if( prevOpt['value']) {
                    prevOpt['src'] =  prevOpt['value'];
                    delete prevOpt['value'];
                }
                obj.input.wrap('<div class="hide_input_file"></div>');
                if(prevOpt) { //生成预览图
                    if(isUndefined(prevOpt['class'])) prevOpt['class'] = 'preview_img';
                    var prevPosition = prevOpt['pos'] || 'left'; //出现的位置 left/right/l/r
                    var prevImgObj = makeImg(prevOpt);
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
            obj.events = cloneData(optionsEvent, obj.events);
            //console.log(obj);
            //console.log(obj.events);
            obj.bindEvenObj = obj.input;
            //input清除按钮
            if(input_useClearBtn) {
                obj.textClearObj = makeSpan({
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
            setSelf = isUndefined(setSelf) ? true : setSelf;
            // console.log('__callRenewSelfVal:'+ text_);
            obj.setRealInputVal(text_, true, setSelf);
        }
        //objName 要转成的变量名字
        //格式化内容 数字、浮点、最大最小值
        function formatInputContent(obj_, options_, addNumObj) {
            addNumObj = addNumObj || null;
            var issetMaxVal = !isUndefined(options_['max']);//不能用 || 写法 因为0也是设置值
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
            //主动更新数据
            renew: function(options_) {
                //console.log('obj options_:');
                //console.log(obj);
                //console.log(options_);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                //先设定options参数 下面才可以修改options
                options_['type'] =  options_['type'] || 'text';//必须声明类型 因为写入值时要根据类型作判断
                if(!optionIsSame(obj, options_, 'size')) {
                    obj.renewSizeClass(options_['size']||'');//初始化尺寸大小
                }
                if(isUndefined(options_['value'])) options_['value'] = '';//默认要绑的属性 不声明无法设置值
                var inputVal = options_['value'];
                var objExtendClass = 'diy_input_box';
                if(inputType == 'file') {//file input
                    objExtendClass = 'diy_upload_input';
                }
                var inputSize = options['size'] || '';
                if(sizeIsXs(inputSize)) {
                    objExtendClass = 'diy_input_box input-group-xs';
                } else if(sizeIsSm(inputSize)) {
                    objExtendClass = 'diy_input_box input-group-sm';
                } else if(sizeIsMd(inputSize)) {
                    objExtendClass = 'diy_input_box input-group-md';
                } else if(sizeIsLg(inputSize)) {
                    objExtendClass = 'diy_input_box input-group-lg';
                }
                options_['class_extend'] = objExtendClass;
                options_['class'] = classAddSubClass(options_['class'], objExtendClass, true);
                //console.log(options_['class']);
                optionDataFrom(obj, options_);
                //初始化value
                obj.realVal = inputVal;
                //console.log('formatAttr');
                //格式化和绑定事件交由内部input 因为每次重新生成input 事件都需要重新绑定
                obj.createInput(options_);//重新创建一个input
                strObj.formatAttr(obj, options_);
                // console.log('makeInput_end1', options_['value']);
                addOptionNullFunc(obj, options_);//加null_func
                this['last_options'] = $.extend({}, options_);//设置完所有属性 要更新旧的option
            },
            updates: function(dataName, exceptObj) {//数据同步
                exceptObj = exceptObj || [];
                // console.log('updates input dataName:'+ dataName, exceptObj);
                if(inputBind) {
                    if($.inArray(obj, exceptObj) ==-1) {
                        // console.log('exceptObj.push:');
                        exceptObj.push(obj);
                        obj.setRealInputVal(getObjData($.trim(options['bind'])), false);
                    }
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    //console.log('call renew input');
                    //console.log(this);
                    renewObjBindAttr(this, dataName);
                }
                //如果更新了size 父样式要改变
                if(strHasKuohao(options['size'])) {
                    if($.inArray(dataName, getKuohaoAbc(options['size'], 'public')) !=-1) {//size has change by public data
                        //console.log('updates input dataName:'+ getObjData(dataName));
                        obj.renewSizeClass(getObjData(dataName));
                    }
                }
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return makeInput(optionsGet);
            }
        });

        obj.renew(options);
        optionGetSet(obj, options);
        objBindVal(obj, options);//数据绑定
        addCloneName(obj, options);//支持克隆
        //console.log(obj);
        return obj;
    };
    //创建图片
    global.makeImg = function (sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        var obj = $('<img />');
        obj['last_options'] = getOptVal(options, 'last_options', {});
        obj[objValIsNode] = false;
        //外部设置val
        obj.extend({
            //主动更新数据
            renew: function(options_) {
                optionDataFrom(this, options_);
                //console.log('renew img');
                //console.log(options_);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                //先设定options参数 下面才可以修改options
                options_['src'] = options_['src'] || options_['value'];
                var loadFunc = options_['load'] || null;
                var loadError = options_['error'] || null;
                var src = options_['src'] || null;
                strObj.formatAttr(this, options_);
                if(src && !strHasKuohao(src)) this.attr('src', src);//格式化src后再赋值
                obj['last_options'] = $.extend({}, options_);//设置完所有属性 要更新旧的option
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
                if(options['bind'] && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(this);
                    this.attr('src', getObjData($.trim(options['bind'])));
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return makeImg(optionsGet);
            }
        });
        //支持value
        Object.defineProperty(obj, 'value', {
            get: function () {
                return this.attr('src');
            },
            set: function(n) {     //支持外部设值
                //console.log('set img val:'+ n);
                if (!isUndefined(options['bind'])) {
                    updateBindObj($.trim(options['bind']), n, [obj]);//同步更新
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
                if (!isUndefined(options['bind'])) {
                    updateBindObj($.trim(options['bind']), n, [obj]);//同步更新
                }
                this.attr('src', n);
            }
        });
        obj.renew(options);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        optionGetSet(obj, options, 'src'); //参数读写绑定 参数可能被外部重置 所以要同步更新参数
        objBindVal(obj, options, [{'key_':'bind', 'val_':'src'}]);
        addCloneName(obj, options);//支持克隆
        return obj;
    };
    //创建按钮 [属性：name,type,value,class,left,func 按钮事件, rest_time]
    //rest_time 按钮可添加 剩余时间倒计时
    //num 后面跟随数值
    global.makeBtn = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        var extendAttr = {};
        if(isUndefined(options['type'])) extendAttr['type'] = 'button';
        var optData = !isUndefined(options['data']) ? options['data'] : {};
        var valueStr = !isUndefined(options['value']) ? options['value'] : "";
        var restNum = !isUndefined(options['rest_time']) ? options['rest_time'] : 0;
        var minNum = !isUndefined(options['min_num']) ? options['min_num'] : 0;
        if(!isUndefined(options['rest_time'])) {
            var showTimer = (restNum <= minNum) ? '': '('+ restNum +')';
            optData['rest_time'] = showTimer;
            valueStr += '{rest_time}';
            options['data'] = optData;
            options['value'] = valueStr;
        }
        var lrBtnSizeClass = '';
        var newSize = getOptVal(options, ['size'], null);
        if(sizeIsXs(newSize)) {
            lrBtnSizeClass = 'btnLrXs';
        } else if(sizeIsSm(newSize)) {
            lrBtnSizeClass = 'btnLrSm';
        } else if(sizeIsMd(newSize)) {
            lrBtnSizeClass = 'btnLrMd';
        } else if(sizeIsLg(newSize)) {
            lrBtnSizeClass = 'btnLrLg';
        }
        if(lrBtnSizeClass) {
            options['class_extend'] = lrBtnSizeClass;
        }
        var sourceVal = options['value'];
        //console.log(optData);
        var funcAfterCreate = function (thisObj, option_) {
            //设置倒计时初始化剩余数字
            thisObj.setRestNum = function (restTime) { //倒计时
                if(restTime <= minNum) {
                    thisObj.removeAttr('disabled');
                } else {
                    thisObj.attr('disabled', true);
                }
                var optData = !isUndefined(option_['data']) ? cloneData(option_['data']) : {};
                //console.log(optData);
                //console.log(thisObj);
                optData['rest_time'] = restTime;
                //console.log(optData);
                thisObj['data'] = optData;
                option_.value = sourceVal + '{{rest_time}>0?"({rest_time})":""}';
                thisObj['last_options']['value'] = option_.value;
                var newStr = strObj.formatStr(option_.value, optData, 0, thisObj, 'value');
                thisObj.renewVal(newStr);
            };
            //外部使用的倒计时触发方法
            thisObj.subTime = function (restTime) { //倒计时
                thisObj.setRestNum(restTime);
                thisObj.__inSubTime(restTime);
            };
            //内部使用的倒计时事件
            thisObj.__inSubTime = function (rest_time) { //倒计时
                if(isUndefined(option_.step)) option_.step = 1;
                var optData = !isUndefined(option_['data']) ? cloneData(option_['data']) : {};
                if(rest_time > minNum) {
                    //1秒后触发btn内容更新
                    setTimeout(function () {
                        rest_time -= parseFloat(option_.step);
                        optData['rest_time'] = rest_time;
                        var newStr = strObj.formatStr(option_.value, optData, 0, thisObj, 'value');
                        thisObj.renewVal(newStr);
                        if(rest_time == 0) {
                            optData['rest_time'] = 0;
                            thisObj['last_options']['value'] = sourceVal;//恢复默认的value
                            thisObj.removeAttr('disabled');
                        } else {
                            thisObj.__inSubTime(rest_time);
                        }
                    }, 1000);
                    thisObj.attr('disabled', true);
                } else {
                    //要等按钮渲染好data属性 才能重置它的data
                    setTimeout(function() {
                        thisObj['data'] = optData;
                        thisObj.removeAttr('disabled');
                    }, 50);
                }
            };
            //带倒计时的按钮
            if(!isUndefined(option_['rest_time']) ) {
                thisObj.__inSubTime(parseFloat(option_['rest_time']));
            }
            //绑定拖拽事件
            callBindDragObj(thisObj, option_);
        };
        options[objValIsNode] = false; //不允许再append val
        var newBtn = makeDom({tag:'button', 'options':options, 'extend_attr':extendAttr, 'after_create': funcAfterCreate});
        //console.log('newBtn');
        //console.log(newBtn);
        return newBtn;
    };
//生成表单
    global.makeForm = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        var extendAttr = {};
        if(isUndefined(options['type'])) options['type'] = 'post';//post/get//upload
        var url = options['url'] || '#';//post url
        extendAttr['class_extend'] = 'form-horizontal';
        var formType = options['type'];
        var defaultSubmit = options['submit'] || null;
        var replaceDataFunc = getOptVal(options, ['replaceData', 'replace_data'], null);
        var postData = getOptVal(options, ['post_data', 'postData'], null);
        var afterCreate = getOptVal(options, ['afterCreate', 'after_create'], false);
        if(formType == 'upload') extendAttr['enctype'] = 'multipart/form-data'; //文件上传表单
        var autoFunc = function (thisObj, options_) {
            //支持打包uri
            thisObj.extend({
                serialize: function() {
                    return jQuery.param( this.getFormDatas() );
                }
            });
            if(afterCreate) afterCreate(thisObj);
        };

        var successObj =  $.extend({}, options);
        var submitSys = function (thisObj, e) {
            e.preventDefault();
            var postDataForm = thisObj.getFormDatas();
            var nullFunc = thisObj.getFormNullErr();//找到是否有禁止留空的拦截
            if(postData) postDataForm = cloneData(postData, postDataForm);
            //console.log(postDataForm);
            //console.log(nullFunc);
            if(nullFunc && nullFunc.length>0) {
                if(typeof nullFunc[0] != 'string') nullFunc[0](nullFunc[1], nullFunc[2]);
                return false;
            }
            //系统函数前置则会注定提交 该由用户来决定是否要提交
            if(defaultSubmit) {
                var response = defaultSubmit(thisObj, e);
                if(response === false) return false;
            }
            if(replaceDataFunc) {
                postDataForm = replaceDataFunc(postDataForm, thisObj, e);
            }
            //console.log(formType);
            if(formType == 'post') {//执行post以及post成功之后的回调动作
                successObj['post_data'] = postDataForm;
                successObj['post_url'] = url;
                global.postAndDone(successObj, thisObj);
            } else if(formType == 'func') {
                //这里的扩展submit可以为空 只执行用户自己定义的submit事件 如：
                //  submit: function(obj, e) {
                //      e.preventDefault();
                //      formSearch(obj); //定义事件
                //      return false;
                //  }
            } else if(formType == 'get') {//执行get 需要用到 target
                window.open(url + $.serialize(postDataForm),'_blank');
            }
        };
        options['submit'] = submitSys;
        options[objValIsNode] = false; //不允许再append val
        return makeDom({tag:'form', 'options': options, 'extend_attr':extendAttr, 'afterCreate':autoFunc});
    };
//生成快速编辑的表单
    global.makeFormEdit = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        var topTitle = options['top_title'] ||  options['topTitle'] || null;
        var optionsBox = $.extend({}, options);
        var editVal = options['value'] || [];
        if(topTitle) {
            editVal = [makeDiv({'class':'text-left', 'margin':'-10px 0 10px 0', value: topTitle}), editVal];
        }
        options['value'] = editVal;
        //删除form不需要的属性
        delete options['width']; //宽度属性已经给box
        delete options['top'];
        delete options['top_title'];
        delete options['topTitle'];
        delete options['class'];
        var form = makeForm(options);
        msgConfirm(form, '修改', '取消',
            function () {
                form.trigger('submit');
            },
            function () {hideNewBox();},
            optionsBox);
        return form;
    };
    //创建开关1 移动的圆球 [属性：name,value,width,
    /*
        value_key: 'value', //默认data的值的键名
        text_key: 'text', //默认data的文本的键名
        item: [{
            value: 1,
            text: 'boy'
        },{
            value: 0,
            text: 'girl'
        }],
        */
    global.makeSwitch = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        if(isUndefined(options['name'])) options['name'] = 'no_name';
        if(isUndefined(options['value'])) options['value'] = '';
        var selectVal = options['value'];
        var obj = $('<span></span>');
        options['class_extend'] = 'diy_switch';
        obj['last_options'] = getOptVal(options, 'last_options', {});
        obj[objValIsNode] = false;
        obj['switchVal'] = selectVal;
        obj['switchText'] = '';
        var iconObj = makeSpan({
            'class': 'icon_box',
            'value': '<span class="icon_par"><i class="icon"></i></span><span class="text1"></span><span class="text2"></span>'
        });
        var innerText1 = iconObj.find('.text1');
        var innerText2 = iconObj.find('.text2');
        obj.append(iconObj);
        //单独的格式化value的括号
        obj.formatVal = function (opt) {
            opt = opt || [];
            var sourceVal = opt['source_value'] || opt['value'];
            var newData = opt['data']||{};
            var newVal ;
            if(strHasKuohao(sourceVal, 'public')) {
                newVal = strObj.formatStr(sourceVal, livingObj['data'], 0, obj, 'value');
                obj[objAttrHasKh] = true;
            } else if(strHasKuohao(sourceVal, 'data')) {
                newVal = strObj.formatStr(sourceVal, newData, 0, obj, 'value');
                obj[objAttrHasKh] = true;
            } else {
                newVal = sourceVal;
            }
            selectVal = newVal;
            opt['value'] = newVal; //参数要改变 防止外部取出来的仍是括号
            obj.valChange(newVal, [obj], false);//自身格式化 不能更新自己的bind 会导致死循环
            if(obj.lazyCall) {
                obj.lazyCall(obj, opt['data'] || {}, livingObj);
            }
        };

        //支持外部取值 data-value
        Object.defineProperty(obj, 'value', {
            get: function() {
                return obj['switchVal'];
            },
            set: function(V) {
                obj.valChange(V, [this], true);
            }
        });
        //支持外部取值
        Object.defineProperty(obj, 'text', {
            get: function() {
                return obj['switchText'];
            }
        });

        //外部设置属性
        obj.extend({
            //值的修改
            valChange: function (newVal, exceptObj, renewBind) {
                // console.log('val Change', newVal);
                exceptObj = exceptObj || [];
                renewBind = isUndefined(renewBind) ? true : renewBind;
                if(newVal != obj.attr('data-value')) {//obj['value']可能已经提前被同步修改 所以要用attr对比
                    obj.attr('data-value', newVal);
                    if(newVal == innerText1.attr('data-val')) {
                        iconObj.addClass('active');
                        obj['switchText'] = innerText1.data('text') ;
                        obj.attr('title', innerText1.data('text')) ;
                        if(obj.activeColor || obj.active_color) {
                            iconObj.css('backgroundColor', (obj.activeColor||obj.active_color));
                        }
                        innerText1.addClass('activeSw');
                        innerText2.removeClass('activeSw');
                    } else {
                        iconObj.removeClass('active');
                        obj['switchText'] = innerText2.data('text') ;
                        obj.attr('title', innerText2.data('text') ) ;
                        if(obj.inActiveColor || obj.inactive_color) {
                            iconObj.css('backgroundColor', (obj.inActiveColor||obj.inactive_color));
                        }
                        innerText1.removeClass('activeSw');
                        innerText2.addClass('activeSw');
                    }
                }
                obj['switchVal'] = newVal;
                var setText = getOptVal(options, ['setText', 'set_text'], null);
                var newText = obj.text;
                // console.log('newText', newVal, setText, renewBind, newText);
                if($.inArray(obj, exceptObj) == -1) exceptObj.push(obj);
                if(renewBind) {
                    if(newVal.length && options['bind'] && renewBind) {
                        updateBindObj($.trim(options['bind']), newVal, exceptObj);
                    } else {
                        var lastVal = isUndefined(livingObj['data'][options['bind']]) ? null : livingObj['data'][options['bind']];
                        if(lastVal) {
                            obj.value = lastVal;
                        }
                    }
                    if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][options['bind']])) {
                        renewObjBindAttr(obj, options['bind']);
                    }
                }
                if(setText && newText !=='') {
                    updateBindObj($.trim(setText), newText, exceptObj);
                }
            },
            //主动更新数据
            renew: function(options_) {
                var selectItem = options_['item']|| [{'value': 1}, {'value': 0}];
                var valueKey = !isUndefined(options_['value_key']) ? options_['value_key'] : 'value'; //没有下标则取value
                var textKey = !isUndefined(options_['text_key']) ? options_['text_key'] : 'text'; //没有下标则取value
                var type_ = !isUndefined(options_['type']) ? options_['type'] : ''; //1,2,3,4,5样式
                var disabled_ = getOptVal(options_, ['disabled', 'disable'], ''); //boolean
                var showText = getOptVal(options_, ['show_text', 'showText'], false); //显示文本
                var readonly = getOptVal(options_, ['readonly', 'readOnly'], false); //只读
                var size_ = options_['size']||''; //xs/sm/md/lg
                var objExtendClass = '';
                if(sizeIsXs(size_)) {
                    objExtendClass = 'switch-xs';
                } else if(sizeIsSm(size_)) {
                    objExtendClass = 'switch-sm';
                } else if(sizeIsMd(size_)) {
                    objExtendClass = 'switch-md';
                } else if(sizeIsLg(size_)) {
                    objExtendClass = 'switch-lg';
                }
                innerText1.attr('data-val', selectItem[0][valueKey]);
                innerText2.attr('data-val', selectItem[1][valueKey]);
                if(!isUndefined(selectItem[0][textKey])) {
                    innerText1.attr('data-text', selectItem[0][textKey]);
                    if(showText!==false && showText!==0) {
                        innerText1.html(selectItem[0][textKey]);
                    }
                }
                if(!isUndefined(selectItem[1][textKey])) {
                    innerText2.attr('data-text', selectItem[1][textKey]);
                    if(showText!==false && showText !==0) {
                        innerText2.html(selectItem[1][textKey]);
                    }
                }
                if(disabled_) {//纠正disable
                    if(options_['disable'] && !options_['disabled']) {
                        options_['disabled'] = disabled_;
                        delete options_['disable'];
                    }
                }
                options_['class_extend'] = 'diy_switch'+ (type_ && type_!=1? type_: '') +
                    (disabled_==true ? ' isDisable' : '') +
                    (objExtendClass?' '+objExtendClass : '');
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                optionDataFrom(this, options_);
                var click_extend = function (obj_) {
                    if(readonly) return false;
                    var newVal = (obj_['switchVal'] == innerText1.attr('data-val')) ? innerText2.attr('data-val') : innerText1.attr('data-val');
                    obj.valChange(newVal); //单纯的改变样式 赋值
                };
                if(options_['click']) {
                    options_['click_extend'] = options_['click'];
                }
                options_['click'] = click_extend;
                //先设定options参数 下面才可以修改options
                strObj.formatAttr(this, options_);
                this['last_options'] = $.extend({}, options_);//设置完所有属性 要更新旧的option
            },
            updates: function(dataName, exceptObj) {//数据同步
                exceptObj = exceptObj || [];
                console.log('updates.switch');
                if(options['bind'] && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(this);
                    this.valChange(getObjData($.trim(options['bind'])), exceptObj, false)
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return makeSwitch(optionsGet);
            }
        });
        obj.renew(options);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        objBindVal(obj, options, [{'key_':'bind', 'val_':'value'}, {'key_':'set_text/setText', 'val_':'text'}]);//数据绑定
        addCloneName(obj, options);//支持克隆
        obj.valChange(selectVal);//首次赋值
        return obj; //makeSwitch
    };
    //创建Nav选项卡
    global.makeNav = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        if(isUndefined(options['value'])) options['value'] = '';
        var navVal = options['value'];
        var navUl = options['ul']||[];
        var navData = options['data']||[];
        var navContent = options['content']||[];
        var navSize = setSize(options['size']) ? options['size'] : 'md'; //xs/sm/md/lg
        var objExtendClass = '';
        if(sizeIsXs(navSize)) {
            objExtendClass = 'btnGLrXs';
        } else if(sizeIsSm(navSize)) {
            objExtendClass = 'btnGLrSm';
        } else if(sizeIsMd(navSize)) {
            objExtendClass = 'btnGLrMd';
        } else if(sizeIsLg(navSize)) {
            objExtendClass = 'btnGLrLg';
        }
        if(isUndefined(options['class'])) options['class'] = 'default';
        options['class'] = classAddSubClass(options['class'], 'diy_nav', 'add');
        if(objExtendClass) options['class'] = classAddSubClass(options['class'], objExtendClass, 'add');
        if(navVal) delete options['value'];
        options['data-value'] = navVal;
        var obj = makeDiv(options);
        obj['last_options'] = getOptVal(options, 'last_options', {});
        obj[objValIsNode] = false;
        obj['nav_obj'] = [];
        obj['content_obj'] = [];
        obj['content_val'] = [];
        var navliData = [];
        var navObj = [];
        //菜单点击事件
        obj['navClick'] = function(newVal, exceptObj, renewBind) {
            exceptObj = exceptObj || [obj];
            renewBind = isUndefined(renewBind) ? true : renewBind;
            //不能跳过data来更新obj的显示 因为单个内容可能自带data
            var liList = obj['nav_obj']['value'];
            var activeN = 0;
            $.each(liList, function (n, li_) {
                if(li_.attr('data-value') == newVal) {
                    activeN = n;
                    li_.addClass('active');
                } else {
                    li_.removeClass('active');
                }
            });
            $.each(obj['content_val'], function (n, ct_) {
                if(n == activeN) {
                    if(!isUndefined(ct_['click_renew']) && ct_['click_renew']) {
                        ct_.renewData(function () {
                            ct_.addClass('active in');
                        });
                    } else {
                        ct_.addClass('active in');
                    }
                } else {
                    ct_.removeClass('active in');
                }
            });
            obj.attr('data-value', newVal);
            //触发数据同步  触发赋值 */
            if(renewBind && options['bind']) {
                if($.inArray(obj, exceptObj) == -1) exceptObj.push(obj);
                if(newVal.length) {
                    updateBindObj($.trim(options['bind']), newVal, exceptObj);
                } else {
                    var lastVal = isUndefined(livingObj['data'][options['bind']]) ? null : livingObj['data'][options['bind']];
                    if(lastVal) {
                        obj['navClick'](lastVal, [obj]);
                    }
                }
                if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][options['bind']])) {
                    renewObjBindAttr(obj, options['bind']);
                }

            }
        };
        //点击时触发内容改变的事件
        function clickEven(obj_, e_) {
            e_.preventDefault();
            var val = obj_.attr('data-value');
            obj['navClick'](val, [obj_]);
        }
        //ul is repeat obj
        if(!$.isArray(navUl) && hasData(navData)) {
            navUl['data-value'] = isUndefined(navUl['value']) ? '': navUl['value'];
            navUl['value'] = isUndefined(navUl['title']) ? '': navUl['title'];
            if(!isUndefined(navUl['click'])) {
                navUl['click_extend'] = navUl['click'];
                navUl['click'] = function (obj_, e_) {
                    clickEven(obj_, e_);
                };
            } else {
                navUl['click'] = function (obj_, e_) {
                    clickEven(obj_, e_);
                };
            }
            if(!isUndefined(navUl['class'])) {
                navUl['class_extend'] = navUl['class'];
                navUl['class'] = "{'"+ navVal +"'== '{value}' ? 'active':''}";
            } else {
                navUl['class'] = "{'"+ navVal +"'== '{value}' ? 'active':''}";
            }
            navObj = makeList({
                'data': navData,
                'class': 'nav nav-tabs',
                'li': navUl
            });
            $.each(navData, function (n, tmpData) {
                var tmpD_ = cloneData(tmpData); //data赋值操作必须要克隆
                tmpD_['data-value'] = isUndefined(tmpD_['value']) ? n: tmpD_['value'];
                tmpD_['value'] = isUndefined(tmpData['title']) ? n: tmpD_['title'];
                navliData.push(tmpD_);
            });
        }
        //console.log(navData);
        //ul is array
        if($.isArray(navUl)) {
            var liArray = [];
            $.each(navUl, function (n, tmpLi) {
                tmpLi['data-value'] = isUndefined(tmpLi['value']) ? n: tmpLi['value'];
                tmpLi['value'] = isUndefined(tmpLi['title']) ? n: tmpLi['title'];
                if(!isUndefined(tmpLi['click'])) {
                    tmpLi['click_extend'] = tmpLi['click'];
                    tmpLi['click'] = function (obj_, e_) {
                        clickEven(obj_, e_);
                    };
                } else {
                    tmpLi['click'] = function (obj_, e_) {
                        clickEven(obj_, e_);
                    };
                }
                if(!isUndefined(tmpLi['class'])) {
                    tmpLi['class_extend'] = tmpLi['class'];
                    tmpLi['class'] = navVal == tmpLi['data-value'] ? 'active':'';
                } else {
                    tmpLi['class'] = navVal == tmpLi['data-value'] ? 'active':'';
                }
                navliData.push(tmpLi);
                liArray.push(makeLi(tmpLi));
            });
            navObj = makeUl({
                'data': navData,
                'class': 'nav nav-tabs',
                'value': liArray
            });
        }
        var contentObjOpt = {
            'class': 'tab-content'
        };
        //console.log(navliData);
        if($.isArray(navContent)) {
            $.each(navContent, function (n, tmpContentOpt) {
                tmpContentOpt['data-value'] = isUndefined(navliData[n]['data-value']) ? n : navliData[n]['data-value'];
                if (!isUndefined(tmpContentOpt['class'])) {
                    tmpContentOpt['class'] = classAddSubClass(tmpContentOpt['class'], 'tab-pane', 'add');
                    tmpContentOpt['class_extend'] = tmpContentOpt['class'];
                    tmpContentOpt['class'] = navVal == tmpContentOpt['data-value'] ? 'active in' : '';
                } else {
                    tmpContentOpt['class_extend'] = 'tab-pane';
                    tmpContentOpt['class'] = navVal == tmpContentOpt['data-value'] ? 'active in' : '';
                }
                obj['content_val'].push(makeDiv(tmpContentOpt));
            });
        } else {//当content的配置是对象
            $.each(navData, function (n, tmpData) {
                var tmpCntData = cloneData(tmpData);
                var tmpCntOpt = $.extend({}, navContent);
                tmpCntOpt['data'] = tmpCntData;
                if(!isUndefined(tmpCntOpt['class'])) {
                    tmpCntOpt['class'] = classAddSubClass(tmpCntOpt['class'], 'tab-pane', 'add');
                    tmpCntOpt['class_extend'] = tmpCntOpt['class'];
                    tmpCntOpt['class'] = "{"+ navVal +"== '{value}' ? 'active in':''}";
                } else {
                    tmpCntOpt['class_extend'] = 'tab-pane';
                    tmpCntOpt['class'] = "{"+ navVal +"== '{value}' ? 'active in':''}";
                    //console.log(tmpCntOpt);
                }
                obj['content_val'].push(makeDiv(tmpCntOpt));
            });
        }
        contentObjOpt['value'] = obj['content_val'];
        // contentObjOpt['data'] = options['data']; //内容不能设置data 因为可能自带data
        var contentObj = makeDiv(contentObjOpt);
        obj['content_obj'] = contentObj;
        obj['nav_obj'] = navObj;
        obj.append(navObj);
        obj.append(contentObj);

        //外部设置属性
        obj.extend({
            //值的修改
            valChange: function (newVal, exceptObj, renewBind) {
                exceptObj = exceptObj || [];
                renewBind = isUndefined(renewBind) ? true : renewBind;
                obj['navClick'](newVal, exceptObj, renewBind);
            },
            updates: function(dataName, exceptObj) {//数据同步
                exceptObj = exceptObj || [];
                if(options['bind'] && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(obj);
                    this.valChange(getObjData($.trim(options['bind'])), exceptObj, false)
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    //console.log('updates');
                    //console.log(options);
                    renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return makeNav(optionsGet);
            }
        });
        objBindVal(obj, options);//数据绑定
        addCloneName(obj, options);//支持克隆
        return obj; //makeSwitch
    };
    //创建items
    global.makeItems = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        var obj = $('<div></div>');
        obj['last_options'] = getOptVal(options, 'last_options', {});
        obj[objValIsNode] = false;
        obj['createItem'] = false;
        var itemObjName = 'items';
        obj[itemObjName] = false;
        obj['multi'] = undefined;
        obj['itemValArray'] = [];
        var itemClassName = 'item_container';//带data的ul的classname
        obj.valueSeted = false;//当前对象的value是否设置完成
        obj.menuXuanranSuccess = false;//当前对象的menu是否渲染完成 [menu需要渲染 才会用到]
        var autoRenewMenuText = false;//当前对象的menu的text和val是否同时渲染完成
        //单独的格式化value的括号
        obj.formatVal = function (opt) {
            opt = opt || [];
            var sourceVal = opt['source_value'] || opt['value'];
            var newVal;
            //每次格式化 优先取格式化前的source value
            if(strHasKuohao(sourceVal, 'public')) {
                newVal = strObj.formatStr(sourceVal, livingObj['data'], 0, obj, 'value');
                obj[objAttrHasKh] = true;
            }else if(strHasKuohao(sourceVal, 'data')) {
                newVal = strObj.formatStr(sourceVal, opt['data']||{}, 0, obj, 'value');
                obj[objAttrHasKh] = true;
            } else {
                newVal = sourceVal;
            }
            opt['value'] = newVal; //参数要改变 防止外部取出来的仍是括号
            obj.valueSeted = true;
            obj.setItemVal(newVal);
            if(!autoRenewMenuText) renewMenuTextByVal();
            //console.log('format_val');
            //如果值是数组 并且多个值 并且未定义是否多选，则默认支持多选
            if($.isArray(newVal) && newVal.length>0 && obj['multi'] == undefined) {
                obj['multi'] = true;
            }
            if(obj.lazyCall) {
                obj.lazyCall(obj, opt['data'] || {}, livingObj);
            }
        };

        //支持外部设置 取值
        Object.defineProperty(obj, 'value', {
            set: function (newVal) {
                // console.log('set newVal:' ,newVal);
                obj.valueSeted = true;
                obj.setItemVal(newVal);
                renewMenuTextByVal();
            },
            get: function () {
                return obj.itemValArray.join(',');
            }
        });
        //支持外部取选中的文本 返回数组格式
        Object.defineProperty(obj, 'text', {
            get: function () {
                //多选时，才返回数组
                return obj['multi'] ? obj.itemTxtArray : ($.isArray(obj.itemTxtArray) ? obj.itemTxtArray.join('') : obj.itemTxtArray) ;
            }
        });
        //支持外部取选中的文本 返回数组格式
        Object.defineProperty(obj, 'title', {
            get: function () {
                //多选时，才返回数组
                return obj['multi'] ? obj.itemTxtArray : ($.isArray(obj.itemTxtArray) ? obj.itemTxtArray.join('') : obj.itemTxtArray) ;
            }
        });
        //获取当前选中的文本
        obj.reGetValAndText = function () {
            var valArray_ = [];
            var textArray_ = [];
            var ulLis = obj[itemObjName];
            var liVal, liTitle;
            $.each(ulLis, function(n, tmpItem) {
                liVal = tmpItem.attr('data-value');
                liTitle = tmpItem.attr('data-title');
                if(tmpItem.hasClass('active')) {
                    valArray_.push(liVal);
                    textArray_.push(liTitle);
                }
            });
            obj.itemValArray = valArray_;
            obj.itemTxtArray = textArray_;
            obj.attr('data-value', valArray_.join(','));
            obj.attr('data-text', textArray_.join(','));
            autoRenewMenuText = true;
        };
        //公共的初始化触发渲染菜单和值的方法
        //当菜单渲染完成，并且item的值渲染完成 才能获取菜单的文本
        function renewMenuTextByVal() {
            //console.log(obj);
            if(obj.menuXuanranSuccess && obj.valueSeted) {
                var textArray = obj.getItemTextByVal();//获取当前li选中的内容
                var selectText = $.isArray(textArray) ? textArray.join(',') : textArray;
                obj.attr('data-text', selectText);
                //当前没有选中的值 要清空子select的菜单 让选中值再加载子select菜单
                autoRenewMenuText = true;
                var setText = getOptVal(options, ['set_text', 'setText'], null);
                //通知更新text
                if(setText) {
                    var selectText = $.isArray(textArray) ? textArray.join(',') : textArray;
                    updateBindObj($.trim(setText), selectText, [obj]);
                    if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][setText])) {
                        renewObjBindAttr(obj, setText);
                    }
                }
            }
        }
        //通过当前val取text
        obj.getItemTextByVal = function () {
            var newValAy = obj.itemValArray;
            //console.log('getItem.TextByVal :');
            //console.log(newValAy);
            if(!newValAy || !hasData(newValAy)) {
                //console.log('!newValAy :');
                return '';
            }
            var textStr = '';
            newValAy = newValAy || [];
            if(!$.isArray(newValAy)) newValAy = newValAy.toString().split(',');
            //设置(更新)select的text
            var valArray_ = [];
            var textArray_ = [];
            var ulLis = obj[itemObjName];
            $.each(ulLis, function(n, tmpItem) {
                var liVal = tmpItem.attr('data-value');
                var liTitle = tmpItem.attr('data-title');
                //console.log('liVal:'+ liVal);
                //console.log(newValAy);
                if(newValAy) {
                    if(strInArray(liVal, newValAy) !=-1) {
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
            obj.itemValArray = valArray_;
            obj.attr('data-value', valArray_.join(','));
            obj.attr('data-text', textArray_.join(','));
            return textArray_;
        };
        //更新选中的值和文本
        obj.setItemVal = function(newVal, exceptObj, renewBind) {
            exceptObj = exceptObj || [];
            renewBind = isUndefined(renewBind) ? true : renewBind;
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
            //console.log(newValArray);
            if(valStr !== obj.attr('data-value')) {
                obj.attr('data-value', valStr);
            }
            obj.itemValArray = newValArray;
            if(renewBind && options['bind']) {
                console.log('items.renewBind', renewBind, options['bind']);
                //触发数据同步  触发赋值 */
                if($.inArray(obj, exceptObj) == -1) exceptObj.push(obj);
                if(valStr.length) {
                    updateBindObj($.trim(options['bind']), valStr, exceptObj);
                } else {
                    var lastVal = isUndefined(livingObj['data'][options['bind']]) ? null : livingObj['data'][options['bind']];
                    if(lastVal) {
                        obj.value = lastVal;
                    }
                }
                if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][options['bind']])) {
                    renewObjBindAttr(obj, options['bind']);
                }
            }
        };
        obj.extend({
            //主动更新数据
            renew: function(options_) {
                if(isUndefined(options_['value'])) options_['value'] = ''; //强制加value 否则外部无法取
                if(isUndefined(options_['text'])) options_['text'] = ''; //强制加text 否则外部无法取
                var sValueStr = !isUndefined(options_['value']) ? options_['value'] : [] ;
                obj['multi'] = getOptVal(options_, ['mul', 'multi', 'multity'], undefined); //是否支持多选
                var lazyCall = getOptVal(options_, ['lazy_call', 'lazyCall'], null);
                var itemsOpt = getOptVal(options_, ['items'], {});
                var liOpt = cloneData(itemsOpt);
                // console.log('items:');
                // console.log(JSON.stringify(itemsOpt));
                var valueKey = getOptVal(liOpt, ['value_key', 'valueKey'], '');
                var titleKey = getOptVal(liOpt, ['title_key', 'titleKey', 'text_key', 'textKey'], '');
                //如果值是数组 并且多个值 并且未定义是否多选，则默认多选
                if($.isArray(sValueStr) && sValueStr.length>0 && obj['multi'] == undefined) {
                    obj['multi'] = true;
                }
                options_['class'] = classAddSubClass(options_['class'], 'diy_items', true);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                //先设定options参数 下面才可以修改options
                var itemValueArray = !isUndefined(options_['value']) ? options_['value'] : [] ;
                if(!itemValueArray) itemValueArray = [];
                if(!$.isArray(itemValueArray)) {
                    if(typeof itemValueArray == 'number') {
                        itemValueArray = itemValueArray + '';
                    }
                    if( isStrOrNumber(itemValueArray)   && !strHasKuohao(itemValueArray)) {
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
                    delete liOpt['value_key'];
                    delete liOpt['valueKey'];
                    delete liOpt['need_parent_val'];
                    delete liOpt['need_parent_key'];
                    var liTitleKey = 'data-title';
                    if(titleKey) {//li中输出标题
                        liOpt[liTitleKey] = '{'+ titleKey +'}';
                    }
                    delete liOpt['title_key'];
                    delete liOpt['titleKey'];
                    delete liOpt['text_key'];
                    delete liOpt['textKey'];
                    delete liOpt['need_parent_val'];
                    delete liOpt['needParentVal'];
                    delete liOpt['need_parent_key'];
                    delete liOpt['needParentKey'];
                    delete liOpt['need_parent_name'];
                    delete liOpt['needParentName'];
                    liOpt['value'] = liOpt['text'];
                    delete liOpt['text'];
                    // console.log('liOpt ______:');
                    // console.log(JSON.stringify(liOpt));
                    //console.log('makeList');
                    var lastClickExt = getOptVal(liOpt, ['click_extend', 'clickExtend'], null);
                    var lastClick = getOptVal(liOpt, ['click'], null);
                    if(lastClickExt) {
                        liOpt['click_extend'] = function (obj_, e_, scope) {
                            lastClickExt(obj_, obj_.parent.parent.parent, e_, scope);
                            //console.log('click___');
                            //console.log(obj_);
                            if(lastClick) lastClick(obj_, obj_.parent.parent.parent, e_, scope);//后置用户设置的事件
                        }
                    } else {
                        if(lastClick) {
                            liOpt['click_extend'] = function (obj_, e_, scope) {
                                lastClick(obj_, obj_.parent.parent.parent, e_, scope);//后置用户设置的事件
                            }
                        }
                    }
                    //console.log('liOpt');
                    //console.log(liOpt);
                    liOpt['click'] = function (clickObj, even_, score_) {//支持点击事件扩展
                        var liVal = clickObj.attr(liDataKey);
                        //console.log('click:', obj['multi']);
                        if(obj['multi']) {//多选
                            clickObj.toggleClass('active');
                            obj.reGetValAndText();
                        } else {//单选
                            clickObj.addClass('active').siblings('.active').removeClass('active');
                            //获取子菜单的事件
                            //单纯的改变样式 赋值
                            obj.setItemVal([liVal]);
                            renewMenuTextByVal();
                        }
                    };
                    liOpt['disabled'] = "{{this.disabled}==true || {this.disabled}=='true' || {this.disabled}==1}";
                    // console.log('liOpt');
                    // console.log(liOpt);
                    //items参数里的data要给makeList,自己不需要
                    var ulOpt = {};
                    if(!isUndefined(liOpt['data'])) ulOpt['data'] = liOpt['data'];
                    if(!isUndefined(liOpt['data_from'])) ulOpt['data_from'] = liOpt['data_from'];
                    if(!isUndefined(liOpt['dataFrom'])) ulOpt['dataFrom'] = liOpt['dataFrom'];
                    delete liOpt['data'];
                    delete liOpt['data_from'];
                    delete liOpt['dataFrom'];
                    ulOpt['li'] = liOpt;
                    ulOpt['need_parent_val'] = getOptVal(itemsOpt, ['need_parent_val', 'needParentVal'], false);//需要父参数渲染好才能请求url
                    ulOpt['need_parent_key'] = getOptVal(itemsOpt, ['need_parent_key', 'needParentKey', 'need_parent_name', 'needParentName'], null);

                    //console.log(JSON.stringify(ulOpt));
                    //console.log(JSON.stringify(options_));
                    ulOpt['lazyCall'] = function () {
                        obj.menuXuanranSuccess = true;
                        if(!autoRenewMenuText) {
                            renewMenuTextByVal();
                        }
                        setTimeout(function () {
                            //延迟执行父绑定的延迟事件
                            if(lazyCall) lazyCall(obj, itemValueArray, livingObj);
                        }, 100);
                    };
                    // console.log('ulOpt', JSON.stringify(ulOpt));
                    ulListObj = makeList(ulOpt);
                    var sons = ulListObj.value;
                    var disableSons = [];
                    var disableVals = [];
                    sons.map(function (v, n) {
                        if(v.disabled == 'true') {
                            disableSons.push(v);
                            disableVals.push(v.attr('data-value'));
                        }
                    });
                    obj['disableSons'] = disableSons;
                    obj['disableVals'] = disableVals;
                    optionDataFrom(obj, options_);//
                    var divOpt = {
                        'class_extend': itemClassName,
                        value: ulListObj
                    };
                    var itemObj = makeDiv(divOpt);
                    itemObj[parentObjKey] = obj;//设置其父对象
                    obj['itemObj'] = itemObj;
                    obj['menu'] = ulListObj;
                    obj[itemObjName] = ulListObj.value;
                    obj.append(itemObj);
                    obj['createItem'] = true;
                }
                addOptionNullFunc(this, options_);//加null_func
                strObj.formatAttr(obj, options_);
                obj['last_options'] = cloneData(options_);//设置完所有属性 要更新旧的option
            },
            updates: function(dataName, exceptObj) {//数据同步
                //console.log(dataName);
                exceptObj = exceptObj || [];
                if(options['bind'] && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(obj);
                    this.setItemVal(getObjData($.trim(options['bind'])), exceptObj, false);
                    renewMenuTextByVal();
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return makeItems(optionsGet);
            }
        });
        objBindVal(obj, options, [{'key_':'bind', 'val_':'value'}, {'key_':'set_text/setText', 'val_':'text'}]);//数据绑定
        obj.renew(options);
        optionGetSet(obj, options); // format AttrVals 先获取options遍历更新 再设置读写
        addCloneName(obj, options);//支持克隆
        //console.log('item_obj');
        //console.log(obj);
        return obj; //makeItems
    };
//生成 下拉菜单
    global.makeSelect = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        //div + contenteditable="true" 可输入 tabindex 用于触发丢焦
        var objExtendClass = 'btnGLr';
        var inputSize = options['size'] || '';
        if(sizeIsXs(inputSize)) {
            objExtendClass = 'btnGLr btnGLrXs';
        } else if(sizeIsSm(inputSize)) {
            objExtendClass = 'btnGLr btnGLrSm';
        } else if(sizeIsMd(inputSize)) {
            objExtendClass = 'btnGLr btnGLrMd';
        } else if(sizeIsLg(inputSize)) {
            objExtendClass = 'btnGLr btnGLrLg';
        }
        var selectDefaultText = getOptVal(options, ['default_text', 'defaultText'], '请选择');
        if(!isUndefined(options['defaultText']) && !isUndefined(options['default_text']))  {
            options['default_text'] = selectDefaultText;
            delete options['defaultText'];
        }
        var obj = $('<div>\
            <div class="inner"> \
                <div class="title_wrap '+ objExtendClass +' ">\
                    <div class="select_text btnLr btnLrDefault" tabindex="1">'+ selectDefaultText +'</div>\
                    <span class="btnLr btnLrDefault" type="button"><span class="caret"></span></> \
                </div> \
             </div>\
         </div>');
        var objInner = obj.find('.inner');
        obj['multi'] = undefined;
        obj['last_options'] = getOptVal(options, 'last_options', {});
        obj[objValIsNode] = false;
        obj['createMenu'] = false;
        obj['menu'] = false;
        obj['clear_btn'] = null;
        obj['selectValArray'] = [];
        obj['selectTxtArray'] = [];
        var sonSelectKey = 'son'; //子下拉菜单的键名 外部调取就用son 不能改的
        obj.textObj = obj.find('.select_text');
        obj.INeedParentValFlag = false;  //当前select对象需要父的value去取menu的data
        var autoRenewSelectMenu = false; //当前select的menu和val是否同时设置完成
        var clearBtn = getOptVal(options, ['clear'], false);
        obj.hasRenewSonObj = false;//定义是否已更新子菜单
        //检测子对象是否跟随父value改变
        function __checkIfNeedParent() {
            if(!isUndefined(obj[sonSelectKey])) {
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
                }
                if(itemUlList['getDataFromParentData']) {
                    itemUlList['getDataFromParentData'](obj, val, sonSelect);
                }
            } else {
                //console.log('no need');
            }
        }
        //更新显示文本
        obj.renewText = function(newTextArray) {
            if(!newTextArray || isUndefined(newTextArray)) newTextArray = obj['menu'].text;
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
            obj.attr('data-text', newTextStr);
            obj.textObj.setSelectMenuText(newTextArray, newTextStr);
            var setText = getOptVal(options, ['set_text', 'setText'], null);
            if(setText) {//触发数据同步  触发赋值 */
                updateBindObj($.trim(setText), newTextStr, [obj]);
                if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][setText])) {
                    renewObjBindAttr(obj, setText);
                }
            }
            //清除内容的按钮
            if(clearBtn) {
                if(!obj['clear_btn']) {
                    obj['clear_btn'] = makeSpan({
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
            var sourceVal = opt['source_value'] || opt['value'];
            var newVal;
            //每次格式化 优先取格式化前的source value
            var newData = {};
            if(strHasKuohao(sourceVal, 'public')) {
                newData = livingObj['data'];
                newVal = strObj.formatStr(sourceVal, newData, 0, obj, 'value');
            } else if(strHasKuohao(sourceVal, 'data')) {
                newData = opt['data'] || {};
                newVal = strObj.formatStr(sourceVal, newData, 0, obj, 'value');
            } else {
                newVal = sourceVal;
            }
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
                obj.lazyCall(obj, opt['data'] || {}, livingObj);
            }
        };

        //更新选中的值和文本
        obj.setSelectVal = function(newVal, exceptObj, renewBind) {
            exceptObj = exceptObj || [];
            renewBind = isUndefined(renewBind) ? true : renewBind;
            if(obj['menu'].value != newVal) obj['menu'].value = newVal;
            // console.log('setSelectVal', newVal, renewBind);
            if(renewBind && options['bind']) {
                //触发数据同步  触发赋值 */
                if($.inArray(obj, exceptObj) == -1) exceptObj.push(obj);
                if(newVal !== '') {
                    updateBindObj($.trim(options['bind']), newVal, exceptObj);
                } else {
                    var lastVal = isUndefined(livingObj['data'][options['bind']]) ? null : livingObj['data'][options['bind']];
                    if(lastVal !== '') {
                        obj.setSelectVal(lastVal, [obj]);
                    }
                }
                if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][options['bind']])) {
                    renewObjBindAttr(obj, options['bind']);
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
        Object.defineProperty(obj, 'menu_data', {
            set: function (newVal) {
                obj['menu']['menu']['data'] = newVal;
                obj['menu'].getItemTextByVal();
                obj.renewText();
                //console.log('renew_son_menu_data', obj);
                __checkIfRenewSonObj(obj.value);
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
            set: function (newText) {
                var texts = newText||selectDefaultText;
                texts = $.isArray(texts) ? texts.join(',') : texts;
                obj.textObj.html(texts);
            },
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
        //div支持input的读写  因为format.Content里调用了 input,val();
        obj.textObj.val = function(newText) {
            if(isUndefined(newText)) newText = this.text();
            var newTextStr = $.isArray(newText) ? newText.join(',') : newText;
            this.setSelectMenuText(newText, newTextStr);
        };
        obj.extend({
            //主动更新数据
            renew: function(options_) {
                if(isUndefined(options_['value'])) options_['value'] = ''; //强制加value 否则外部无法取
                var sValueStr = getOptVal(options_, ['value'], []) ;
                var itemValueArray = sValueStr;
                obj.INeedParentValFlag = getOptVal(options_, ['need_parent_val', 'needParentVal'], false);//需要父参数渲染好才能请求url
                var needParentKey = getOptVal(options_, ['need_parent_key', 'needParentKey', 'need_parent_name', 'needParentName'], null);
                var itemsMenuOpt = getOptVal(options_, ['li'], {});
                var pageOpt = getOptVal(options_, ['pagemenu','pageMenu','page_menu'], null);
                var optData = options_['data'] || {};
                var lazyCall = getOptVal(options_, ['lazy_call', 'lazyCall'], null);
                obj['multi'] = getOptVal(options_, ['mul', 'multi', 'multity'], undefined); //是否支持多选
                //如果值是数组 并且多个值 并且未定义是否多选，则默认多选
                if($.isArray(sValueStr) && sValueStr.length>0 && obj['multi'] == undefined) {
                    obj['multi'] = true;
                }
                var sourceVal = options_['value']||'';
                //初始化 确认是否val需要渲染
                if(strHasKuohao(sourceVal, 'public')) {
                    obj[objAttrHasKh] = true;
                } else if(strHasKuohao(sourceVal, 'data')) {
                    obj[objAttrHasKh] = true;
                } else {
                    obj[objAttrHasKh] = false;
                }

                //多级子菜单
                var selectSonOpt = options_['son'] || false;
                //生成子对象
                var sonObj = null;
                if(selectSonOpt) {
                    // 缺省则沿用父属性
                    var sonExtendOptNames = 'default_text/value_key/title_key/text_key/li/url/post_name/data_key/success_key/success_value/success_value/success_func'.split('/');
                    var needParentVal = getOptVal(selectSonOpt, ['need_parent_val'], '');
                    sonExtendOptNames.forEach(function(opt_) {
                        if(isUndefined(selectSonOpt[opt_]) && !isUndefined(options_[opt_])) {
                            selectSonOpt[opt_] = options_[opt_];
                        }
                    });
                    //console.log(obj);
                    //console.log('son needParentVal:'+ needParentVal);
                    if(needParentVal) {
                        selectSonOpt['need_parent_val'] = true; //定义子对象属性:需要父值去取值
                    }
                    sonObj = makeSelect(selectSonOpt);
                    sonObj['parent'] = obj;
                    obj[sonSelectKey] = sonObj;
                }
                //只生成一次下拉菜单
                if(!obj['createMenu']) {
                    //select自身可以用data和data_from 但是前提是menu参数里必须要设定data或data_from
                    var parentDataFrom = getOptVal(options_, ['data_from', "dataFrom"], null);
                    var menuOpt = getOptVal(options_, ['menu'], {});
                    var menuDataFrom = getOptVal(menuOpt, ['data_from', "dataFrom"], null);
                    var menuSetData = getOptVal(menuOpt, ['data'], null);
                    //继承当前select属性 是否需要父value来更新
                    if(!menuDataFrom && !menuSetData) {
                        //console.log('son___no_data');
                        //console.log(obj);
                        if(parentDataFrom) {
                            itemsMenuOpt['data_from'] = cloneData(parentDataFrom);
                            delete options_['data_from'];
                            //console.log('del data_from');
                        } else if(optData) {
                            itemsMenuOpt['data'] = cloneData(optData);
                            delete options_['data'];
                            //console.log('del data');
                        }
                    } else {
                        //console.log('menuDataFrom');
                        //console.log(obj);
                        //console.log(menuDataFrom);
                        if(menuDataFrom) {
                            itemsMenuOpt['data_from'] = menuDataFrom;
                        } else {
                            itemsMenuOpt['data'] = cloneData(menuSetData);
                        }
                        //console.log('delete menuOpt data');
                        //console.log(obj);
                        delete menuOpt['data']; //item对象不需要渲染data
                        //渲染select自己的data 和son菜单无关
                        optionDataFrom(obj, options_);
                    }
                    if(!isUndefined(itemsMenuOpt['value']) && isUndefined(itemsMenuOpt['text'])) {
                        itemsMenuOpt['text'] = itemsMenuOpt['value'];
                        delete itemsMenuOpt['value'];
                    }
                    //旧版会把这两个配置写在opt里 也支持读取覆盖
                    var optValKey = getOptVal(options_, ['value_key', 'valueKey'], 'value');
                    var optTitKey = getOptVal(options_, ['title_key', 'titleKey', 'text_key', 'textKey'], null);
                    if(!optTitKey && !itemsMenuOpt['title_key']) {
                        console.log('select未定义title_key ');
                        return;
                    }
                    if(optValKey) itemsMenuOpt['value_key'] = optValKey;
                    if(optTitKey) itemsMenuOpt['title_key'] = optTitKey;
                    var lastClick = getOptVal(itemsMenuOpt, ['click'], '');
                    itemsMenuOpt['click'] = function (o_, ev_, scope_) {
                        var itemObj = o_.parent.parent.parent;
                        if(!itemObj['multi']) {
                            itemObj.hide();
                        }
                        obj.renewText();
                        obj.setSelectVal(obj.value, [obj]);
                        __checkIfRenewSonObj(obj.value, 1); //检测是否需要触发子对象刷新data
                        if(lastClick) lastClick(o_, ev_, scope_);
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
                                lazyCall(obj, itemValueArray, livingObj);
                            }
                        }, 50);
                    };
                    //刷新data的事件交由list去做 先把属性给Items
                    itemsMenuOpt['need_parent_val'] = obj.INeedParentValFlag;
                    itemsMenuOpt['need_parent_key'] = needParentKey;
                    // itemsMenuOpt['bind'] = options_['bind'] || '';
                    menuOpt = $.extend({}, menuOpt, {
                        'items': itemsMenuOpt,
                        'multi': obj['multi'],
                        'value': itemValueArray
                    });
                    //item自身不能继承data菜单
                    delete menuOpt['data_from'];
                    delete menuOpt['dataFrom'];
                    // console.log('itemsMenuOpt', JSON.stringify(itemsMenuOpt));
                    var menu_obj = makeItems(menuOpt);
                    //console.log('menu_obj',menu_obj);
                    menu_obj[parentObjKey] = obj;//设置其父对象
                    if(!menu_obj.hasClass(menu_pub_class_name)) menu_obj.addClass(menu_pub_class_name);//带公共菜单样式
                    obj['menu'] = menu_obj;//对外方便更新和获取菜单
                    obj.append(menu_obj);
                    objInner.click(function (even_, obj_) {
                        even_.stopPropagation();
                        var clickTag = $(even_.target);
                        if(clickTag.hasClass('lrXX')) return; //clear
                        menu_obj.show();
                        obj.addClass(menuZindexClass);
                    });
                    if(pageOpt) {
                        //console.log('create_page');
                        var pageNowKey = pageOpt['page_now_key'] || pageOpt['current_page_key'] || 'page';
                        var pageObj = global.makePage({
                            data: '{'+ pageOpt['data_key'] +'}',
                            page: pageNowKey ? '{'+ pageNowKey +'}' : 'page',
                            total: pageOpt['result_total_key'] ? '{'+ pageOpt['result_total_key'] +'}' : 'total',
                            pagesize: pageOpt['page_size_key'] ? '{'+ pageOpt['page_size_key'] +'}' : 5,
                            size: pageOpt['menu_size'] ? pageOpt['menu_size'] : 'sm',
                            pagenum: pageOpt['page_btn_num'] ? pageOpt['page_btn_num'] : 5,
                            click: function (tmpObj, page) {
                                obj['menu'].renewData(null, page);
                            }
                        });
                        obj['menu'].append(pageObj);
                    }
                    obj['createMenu'] = true;
                }
                //console.log(obj);
                //添加数据
                var selectAdd = options_['add'] || {};
                var selectAddUrl = selectAdd['url'] || '';
                var postAddName = selectAdd['post_name'] || options_['name'];
                var canEnter = false;
                if(selectAddUrl && successAddVal) {//可填写
                    obj.textObj.attr('contenteditable', true);
                    this.attr('url', selectAddUrl);
                    canEnter = true;
                }
                //单独给input分配的事件
                var newInputEven = {};
                //keyup 事件扩展
                if(canEnter) {
                    //keyup 默认为事件 触发内容更新和同步
                    newInputEven['keyup_extend'] = function () {
                        formatInputContent(obj, options_, false);//限制内容格式、最大值
                    };
                }
                //blur事件扩展
                if(canEnter) {
                    newInputEven['blur_extend'] = function () {
                        var oldVal = obj.textObj.attr('data-old');
                        var thisVal = obj.textObj.text();
                        obj.textObj.attr('data-old', thisVal);//丢焦时才更新旧值
                        //ajax添加
                        if(canEnter && thisVal && oldVal !=thisVal ) {
                            var postAddData = {};
                            postAddData[postAddName] = $.trim(thisVal);//name必须重新获取 因为上面的是临时变量
                            global.postAndDone({
                                post_url: selectAddUrl,
                                post_data: postAddData,
                                success_value: successAddVal,
                                success_key: successAddKey,
                                success_func: successAddFunc
                            }, obj.textObj);
                        }
                    };
                }
                options_['class_extend'] = 'select_box';
                addOptionNullFunc(this, options_);//加null_func
                delete newInputEven['value'];
                //console.log(options_);
                obj.bindEvenObj = obj.textObj;
                strObj.formatAttr(obj.textObj, newInputEven);/// 给input分配的事件 如 blur
                //强制加value参数 否则无法触发初始化渲染value事件：format Val
                var formatOpt = cloneData(options_);
                if(isUndefined(formatOpt['value'])) formatOpt['value'] = '';
                strObj.formatAttr(obj, formatOpt);
                this['last_options'] = $.extend({}, options_);//设置完所有属性 要更新旧的option

            },
            updates: function(dataName, exceptObj) {//数据同步
                //console.log('updates:'+dataName);
                exceptObj = exceptObj || [];
                if(options['bind'] && $.inArray(this, exceptObj) == -1) {
                    this.value = (getObjData($.trim(options['bind'])));
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return makeSelect(optionsGet);
            }
        });
        objBindVal(obj, options, [{'key_':'bind', 'val_':'value'}, {'key_':'set_text/setText', 'val_':'text'}]);//数据绑定
        obj.renew(options);
        optionGetSet(obj, options);
        addCloneName(obj, options);//支持克隆
        //console.log('select_obj');
        //console.log(obj);
        return obj; //makeSelect
    };
    //创建check
    var onlyCheckeds = {};
    global.makeCheck = global.makeChecked = global.makeCheckbox = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        var obj = $('<div></div>');
        if(isUndefined(options['name'])) {
            var newname = createRadomName('check');
            options['name'] = newname;
        }
        obj['tag'] = 'checkbox';
        obj['last_options'] = getOptVal(options, 'last_options', {});
        obj[objValIsNode] = false;
        obj['createCheck'] = false;
        var onlyName = getOptVal(options, ['only', 'single', 'one'], null);
        if(onlyName) {
            if(isUndefined(onlyCheckeds[onlyName])) {
                onlyCheckeds[onlyName] = [];
            }
            onlyCheckeds[onlyName].push(obj);
        }
        //select:单独的格式化value的括号 更新data时会触发
        obj.formatVal = function (opt) {
            opt = opt || [];
            var sourceVal = opt['source_value'] || opt['value'];
            var newVal;
            //每次格式化 优先取格式化前的source value
            if (strHasKuohao(sourceVal, 'public')) {
                newVal = strObj.formatStr(sourceVal, livingObj['data'], 0, obj, 'value');
                obj[objAttrHasKh] = true;
            } else if (strHasKuohao(sourceVal, 'data')) {
                newVal = strObj.formatStr(sourceVal, opt['data'] || {}, 0, obj, 'value');
                obj[objAttrHasKh] = true;
            } else {
                newVal = sourceVal;
            }
            if ($.isArray(newVal)) newVal = newVal.join(',');
            var renewBind = strHasKuohao(newVal, 'public');
            obj.callRenewBind(newVal, [obj], renewBind);
            if(obj.lazyCall) {
                obj.lazyCall(obj, opt['data'] || {}, livingObj);
            }
        };

        //更像绑定的值
        obj.callRenewBind = function(newVal, exceptObj, renewBind) {
            exceptObj = exceptObj || [];
            renewBind = isUndefined(renewBind) ? true : renewBind;
            if(isUndefined(newVal)) {
                newVal = obj.checked_value;
            } else {
                obj.checked_value = newVal;
            }
            if (options['bind'] && renewBind) {
                if($.inArray(obj, exceptObj) == -1) {
                    exceptObj.push(obj);
                    updateBindObj(options['bind'], newVal, exceptObj);
                }
                if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][options['bind']])) {
                    renewObjBindAttr(obj, options['bind']);
                }
            }
            var setText = getOptVal(options, ['set_text', 'setText'], null);
            if (setText) {
                updateBindObj(setText, obj.text, [obj]);
                if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][setText])) {
                    renewObjBindAttr(obj, setText);
                }
            }
        };
        //检测是否选中
        var hasChecked = function() {
            var checked = obj.attr('checked');
            if(!isUndefined(checked)) {
                if(checked==0 || checked=='0' || checked=='false' || !checked) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        };
        //支持外部设置 取值
        Object.defineProperty(obj, 'value', {
            set: function (newVal) {
                obj.checked_value = newVal;
            },
            get: function () {
                if(hasChecked()) return obj.checked_value;
                return '';
            }
        });
        //支持外部取选中的文本 返回数组格式
        Object.defineProperty(obj, 'text', {
            set: function (newVal) {
                obj.checked_title = newVal;
            },
            get: function () {
                if(hasChecked()) return obj.checked_title;
                return '';
            }
        });
        //支持外部设置 取值
        Object.defineProperty(obj, 'checked', {
            set: function (newVal) {
                if(newVal==0 || newVal == 'false' || !newVal) {
                    obj.removeAttr('checked');
                } else {
                    obj.attr('checked', newVal);
                }
            },
            get: function () {
                return hasChecked();
            }
        });
        //自身更新data时 触发更新data
        obj.renewSonData = function(newData) {
            obj.checked = false;//当data更新时 check状态要恢复为默认值
            obj.removeAttr('checked');
            obj[objValObjKey]['data'] = newData;
        };
        obj.extend({
            //主动更新数据
            renew: function(options_) {
                var size_ = options_['size']||''; //xs/sm/md/lg
                var objExtendClass = '';
                if(sizeIsXs(size_)) {
                    objExtendClass = 'checked-xs';
                } else if(sizeIsSm(size_)) {
                    objExtendClass = 'checked-sm';
                } else if(sizeIsMd(size_)) {
                    objExtendClass = 'checked-md';
                } else if(sizeIsLg(size_)) {
                    objExtendClass = 'checked-lg';
                }
                var type_ = !isUndefined(options_['type']) ? options_['type'] : ''; //1,2,3,4,5样式
                options_['class_extend'] = 'diy_checked' + (type_ && type_!=1? ' checkStyle'+type_: '')+ (objExtendClass? ' '+ objExtendClass :'');

                var disabled = getOptVal(options_, ['disable','disabled'], '');
                if(!isUndefined(options_['disable']) && isUndefined(options_['disabled'])) {
                    options_['disabled'] = disabled;
                    delete options_['disable'];
                }
                //重置value和title/text
                options_['checked_value'] = getOptVal(options_, ['value'], '');
                options_['checked_title'] = getOptVal(options_, ['text'], '');
                var dataTitle = getOptVal(options_, ['text'], '');
                delete options_['value'];
                delete options_['text'];
                if(disabled == 1) options_['disabled'] = true;
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                optionDataFrom(obj, options_);//
                var data_ = getOptVal(options_, 'data', {});
                //console.log('options11_');
                //console.log(options_);
                //只生成一次子对象
                if(!obj['createCheck']) {
                    var sonOpt = {
                        'class': '_inner',
                        value: [
                            makeSpan({
                                'class': '_icon',
                                'value': ''
                            }),makeSpan({
                                'class': '_title',
                                'value': dataTitle
                            })]
                    };
                    var sonObj = makeSpan(sonOpt);
                    sonObj[parentObjKey] = obj;//设置其父对象
                    obj[objValObjKey] = sonObj;//设置其子对象
                    if(isObj(data_) && hasData(data_)) sonObj['data'] = data_;
                    obj.append(sonObj);
                    obj['createCheck'] = true;
                    //console.log('sonObj');
                    //console.log(sonObj);
                    var defaultClickFunc = function(obj_, e) {
                        if(obj_.attr('disabled')) return;
                        var lastChecked = hasChecked();
                        if(lastChecked) {
                            obj_.removeAttr('checked');
                        } else {
                            obj_.attr('checked', 'true');
                        }
                        var newVal = !lastChecked;
                        newVal = newVal ? 1 : 0;
                        obj_['options']['checked'] = newVal;
                        if(options_['bind']) {
                            updateBindObj($.trim(options_['bind']), newVal, [obj_]);
                            if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][options_['bind']])) {
                                renewObjBindAttr(obj, options_['bind']);
                            }
                        }
                        var setText = getOptVal(options, ['set_text', 'setText'], null);
                        if (setText) {
                            updateBindObj(setText, obj.text, [obj]);
                            if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][setText])) {
                                renewObjBindAttr(obj, setText);
                            }
                        }
                        //之前未选中，现在选中，判断是否设置单选
                        if(!lastChecked && onlyName && onlyCheckeds[onlyName]) {
                            $.each(onlyCheckeds[onlyName], function (index,o_) {
                                if(o_!==obj) {
                                    o_.checked = false;
                                }
                            });
                        }
                    };
                    if(isUndefined(options_['click'])) {
                        options_['click'] = defaultClickFunc;
                    } else {
                        options_['click_extend'] = options_['click'];
                        options_['click'] = defaultClickFunc;
                    }
                }
                //console.log('options_');
                //console.log(obj);
                //console.log(options_);
                addOptionNullFunc(obj, options_);//加null_func
                strObj.formatAttr(obj, options_);
                optionGetSet(obj, options_); // format AttrVals 先获取options遍历更新 再设置读写
                obj['last_options'] = cloneData(options_);//设置完所有属性 要更新旧的option
            },
            updates: function(dataName, exceptObj) {//数据同步
                //console.log('updates');
                //console.log(dataName);
                //console.log(obj[objBindAttrsName]);
                exceptObj = exceptObj || [];
                if(options['bind'] && $.inArray(this, exceptObj) == -1) {
                    var checked = (getObjData($.trim(options['bind'])));
                    if(!checked || checked==0 || checked=='false') {
                        this.removeAttr('checked');
                    } else {
                        this.attr('checked', 1);
                    }
                }
                var setText = getOptVal(options, ['set_text', 'setText'], null);
                if(setText) {
                    this.attr('data_text', getObjData($.trim(setText)));
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return makeChecked(optionsGet);
            }
        });
        objBindVal(obj, options, [{'key_':'bind', 'val_':'value'}, {'key_':'set_text/setText', 'val_':'text'}]);//数据绑定
        obj.renew(options);
        addCloneName(obj, options);//支持克隆
        return obj;
    };
    //单选框
    global.makeRadio = global.makeRadios = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        var obj = $('<div>\
            <div class="inner"> \
             </div>\
         </div>');
        var objInner = obj.find('.inner');
        obj['last_options'] = getOptVal(options, 'last_options', {});
        obj[objValIsNode] = false;
        obj['createItem'] = false;
        obj['itemsObj'] = null;
        var valueStrFormatdSuccess = true;//当前value是否渲染完成
        //select:单独的格式化value的括号 更新data时会触发
        obj.formatVal = function (opt) {
            opt = opt || [];
            var sourceVal = opt['source_value'] || opt['value'];
            var newVal;
            //每次格式化 优先取格式化前的source value
            if (strHasKuohao(sourceVal, 'public')) {
                newVal = strObj.formatStr(sourceVal, livingObj['data'], 0, obj, 'value');
                obj[objAttrHasKh] = true;
            } else if (strHasKuohao(sourceVal, 'data')) {
                newVal = strObj.formatStr(sourceVal, opt['data'] || {}, 0, obj, 'value');
                obj[objAttrHasKh] = true;
            } else {
                newVal = sourceVal;
            }

            if (!optionIsSame(obj, opt, 'value')) {
                valueStrFormatdSuccess = true;
            }
            if ($.isArray(newVal)) newVal = newVal.join(',');
            if(valueStrFormatdSuccess) {
                if(obj.lazyCall) {
                    obj.lazyCall(obj, opt['data'] || {}, livingObj);
                }
            }
            //console.log(obj);
            var renewBind = obj[objAttrHasKh] == true;
            // console.log('radio renewBind', renewBind);
            obj.callRenewBind(newVal, [obj], renewBind);
            if(obj.lazyCall) {
                obj.lazyCall(obj, opt['data'] || {}, livingObj);
            }
        };
        //更像绑定的值
        obj.callRenewBind = function(newVal, exceptObj, renewBind) {
            exceptObj = exceptObj || [];
            renewBind = isUndefined(renewBind) ? true : renewBind;
            if(isUndefined(newVal)) {
                newVal = obj.value;
            } else {
                obj.value = newVal;
            }
            if (options['bind'] && renewBind) {
                if($.inArray(obj, exceptObj) == -1) {
                    exceptObj.push(obj);
                    updateBindObj(options['bind'], newVal, exceptObj);
                }
                if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][options['bind']])) {
                    renewObjBindAttr(obj, options['bind']);
                }
            }
            var setText = getOptVal(options, ['set_text', 'setText'], null);
            if (setText) {
                updateBindObj(setText, obj.text, [obj]);
                if(obj[objBindAttrsName] && !objIsNull(obj[objBindAttrsName]) && !isUndefined(obj[objBindAttrsName][setText])) {
                    renewObjBindAttr(obj, setText);
                }
            }
        };
        //支持外部设置 取值
        Object.defineProperty(obj, 'value', {
            set: function (newVal) {
                obj['itemsObj'].value = newVal;
            },
            get: function () {
                return obj['itemsObj'].value;
            }
        });
        //支持外部取选中的文本 返回数组格式
        Object.defineProperty(obj, 'text', {
            get: function () {
                var texts = obj['itemsObj'].text;
                return $.isArray(texts) ? texts.join(',') : texts;
            }
        });
        obj.extend({
            //主动更新数据
            renew: function (optionsGet) {
                optionsGet = optionsGet || {};
                var options_ = cloneData(optionsGet);//保留默认的配置 用于克隆
                // console.log('renew radio::::::::::::::');
                //console.log(this);
                //console.log(options_['data']);
                if (isUndefined(options_['value'])) options_['value'] = ''; //强制加value 否则外部无法取
                var sValueStr = getOptVal(options_, ['value'], []);
                var itemsOpt = getOptVal(options_, ['items'], {});
                var type_ = !isUndefined(options_['type']) ? options_['type'] : ''; //1,2,3,4,5样式
                var objExtendClass = '';//默认class
                var radioSize = options_['size'] || '';
                //console.log('size:'+ radioSize);
                if (sizeIsXs(radioSize)) {
                    objExtendClass = 'radios-xs';
                } else if (sizeIsSm(radioSize)) {
                    objExtendClass = 'radios-sm';
                } else if (sizeIsMd(radioSize)) {
                    objExtendClass = 'radios-md';
                } else if (sizeIsLg(radioSize)) {
                    objExtendClass = 'radios-lg';
                }
                if(type_ && type_!=1) objExtendClass += ' radioType'+ type_;
                if(strHasKuohao(options_['value'])) {
                    //console.log('set false:');
                    valueStrFormatdSuccess = false;
                }
                //console.log('size:'+ objExtendClass);
                options_['class_extend'] = 'diy_radio '+objExtendClass;
                optionDataFrom(obj, options_);
                //只生成一次下拉菜单
                if (!obj['createItem']) {
                    //旧版会把这两个配置写在opt里 也支持读取覆盖
                    var itemsTitleKey = getOptVal(itemsOpt, ['title_key', 'titleKey', 'text_key', 'textKey'], null);
                    var itemsValKey = getOptVal(itemsOpt, ['value_key', 'valueKey'], null);
                    if(!itemsTitleKey) {
                        var optTitKey = getOptVal(options_, ['title_key', 'titleKey', 'text_key', 'textKey'], 'title');
                        if (optTitKey) {
                            itemsTitleKey = itemsOpt['title_key'] = optTitKey;
                        }
                    }
                    if(!itemsValKey) {
                        var optValKey = getOptVal(options_, ['value_key', 'valueKey'], 'value');
                        if (optValKey) {
                            itemsValKey = itemsOpt['value_key'] = optValKey;
                        }
                    }
                    itemsOpt['click_extend'] = function (o_, e_, s_) {
                        obj.callRenewBind();
                    };
                    itemsOpt['lazy_call'] = function (item_, newVal) {

                    };
                    // console.log('itemsTitleKey:', itemsTitleKey);
                    itemsOpt['text'] = "<span class='_icon'></span><span class='text'>{"+ itemsTitleKey +"}</span>";
                    itemsOpt['disabled'] = "{disabled}";
                    var menuOpt =  {
                        'items': itemsOpt,
                        'value': sValueStr
                    };
                    //item自身不能继承data菜单
                    var menu_obj = makeItems(menuOpt);
                    menu_obj[parentObjKey] = obj;//设置其父对象
                    obj['itemsObj'] = menu_obj;
                    obj['menu'] = menu_obj;//对外方便更新和获取菜单
                    obj['items'] = menu_obj;//对外方便更新和获取菜单
                    objInner.append(menu_obj);
                }
                //console.log('options_');
                //console.log(this);
                removeAllEven(options_);
                //添加数据
                addOptionNullFunc(this, options_);//加null_func
                //console.log(options_);
                strObj.formatAttr(obj, options_);//无需再设置value //给input分配的事件 如 blur
                optionGetSet(this, options_); // format AttrVals 先获取options遍历更新 再设置读写
                this['last_options'] = $.extend({}, options_);//设置完所有属性 要更新旧的option
                //如果值是确定的 需要检测是否刷新子对象data
            },
            updates: function (dataName, exceptObj) {//数据同步
                exceptObj = exceptObj || [];
                var newVal = getObjData($.trim(options['bind']));
                if( $.inArray(obj, exceptObj) == -1 && strInArray(newVal, obj['menu']['disableVals']) == -1) {
                    // console.log('newVal', newVal, obj['menu']['disableVals']);
                    exceptObj.push(obj);
                    if(options['bind']) {
                        obj.callRenewBind(newVal, exceptObj, false);
                    }
                    if (obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                        renewObjBindAttr(obj, dataName);
                    }
                }
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return makeRadio(optionsGet);
            }
        });
        objBindVal(obj, options, [{'key_':'bind', 'val_':'value'}, {'key_':'setText/set_text', 'val_': 'text'}]);//数据绑定
        obj.renew(options);
        addCloneName(obj, options);//支持克隆
        //console.log('select_obj');
        //console.log(obj);
        return obj; //makeSelect
    };

//创建日历 makeRili
    global.makeRili = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        /* options :
         {
         var rili = makeRili({
            name: 'year,month,date',
            width: '100px,
            year_menu_width: '400px,//下拉年的菜单宽度
            month_menu_width: '156px,//下拉月的菜单宽度
            ym_size: 'small', //年月日尺寸
            value: '2012-12-24',
            from_year: 1998,
            to_year: 2018,
            chose: "$('#search_sms_form').submit()"
         });
         } */
        options = options || {};
        var riliVal = isUndefined(options['value']) ? '' : options['value'];
        var yearMenuWidth = isUndefined(options['year_menu_width']) ? '228px' : options['year_menu_width'];
        var monthMenuWidth = isUndefined(options['month_menu_width']) ? '113px' : options['month_menu_width'];
        var ymSize = isUndefined(options['ym_size']) ? 'normal' : options['ym_size'];//年月的尺寸
        options.year_menu_width = isUndefined(options.year_menu_width) ? 390 : options.year_menu_width;//年份菜单宽度
        options.month_menu_width = isUndefined(options.month_menu_width) ? 180 : options.month_menu_width;//年份菜单宽度
        var now =  new Date();
        var nowYear =  now.getFullYear();//今年
        var currentDay =  now.getDay();//今天
        var fromYear = isUndefined(options.from_year) ? nowYear -40 : options.from_year;//开始年份
        var toYear = isUndefined(options.to_year) ? nowYear + 10 : options.to_year;//截止年份
        var yearMoneyDaySelect;
        var splitStr = '-';//日期分割符号
        var allYears = [];//定义所有可选的年份，防止当前年份不存
        //构建年月下拉框
        var yearData = [];
        for(var i = fromYear; i<=toYear; i++){
            yearData.push({'value':i, 'title':i});
            allYears.push(i);
        }
        //获取年月日
        function getStrYMD(riliVal) {
            riliVal = riliVal || '';  //riliVal //当前输入的年月
            riliVal = $.trim(riliVal);
            var year,month,day=0;
            if(!riliVal || riliVal==0 || !/\d{4}-\d{1,2}-\d{1,2}/.test(riliVal)) {
                year =  now.getFullYear();//今年
                month = now.getMonth()+1;//本月
                day = now.getDay(); //默认为0 防止每次去掉日期时闪回到今天日期
                if(strInArray(year, allYears) ==-1) {
                    year = allYears[0];
                }
            } else {
                riliVal = riliVal.replace(/\/|\./g, splitStr);
                riliVal = riliVal ? riliVal.split(' ')[0] : '';
                var dateArray = riliVal.split(splitStr);
                year = dateArray[0];
                // year = year < 1200 ? 1200 : year;
                month = dateArray[1];
                month = month > 12 ? 12 : month;
                if(!month) month = 1;
                day = dateArray[2];
                day = day > 31 ? 31 : day;
            }
            //console.log('month:'+ month);
            //console.log('day:'+ day);
            return [year, month, day];
        }
        //
        options['click'] = function (clickObj, even_, score_) {
            if(clickObj.hasClass('lrXX')) return;//点击clear 无须弹窗
            //每次点击输入框 重新创建日历
            var ymd_ = getStrYMD(clickObj.value);
            makeDays_(ymd_[0], ymd_[1], ymd_[2]);
        };
        options['change'] = function (keyupObj, even_, score_) {//支持点击事件扩展
            //每次重新输入 重新创建日历
            var ymd_ = getStrYMD(keyupObj.value);
            makeDays_(ymd_[0], ymd_[1], ymd_[2]);
        };
        var obj = makeInput(options);
        var menuName = 'calendar_menu_'+ parseInt(global.makeRadom(10));
        obj._rili = $('<div class="calendar_menu '+ menu_pub_class_name +'" id="'+ menuName +'"></div>');
        $('body').append(obj._rili);
        //创建日历菜单
        function makeRiliMenu() {
            var tableHtml = '<table class="calendar_table" cellspacing="0" cellpadding="0" border="0">' +
                '<tr class="tr_"><td>' +
                '<span class="last_month_btn pre_next_btn"> <span class="icon"> </span>  </span>' +
                '</td>' +
                '<td colspan="5" class="show_year_month_box"> </td>' +
                '<td>' +
                '<span class="next_month_btn pre_next_btn" style="cursor:hand;"> <span class="icon"> </span> </span>' +
                '</td>' +
                '</tr>' +
                '<tr class="week_tr">';
            //日期选择
            var weekDays = ["日","一", "二", "三", "四", "五", "六"];
            for(var i = 0 ; i<weekDays.length; i++){
                tableHtml+='<td>'+weekDays[i]+'</td>';
            }
            tableHtml+="</tr>";
            '</table>';
            obj._rili.append($(tableHtml));
            var currentYear,currentMonth;
            currentYear = nowYear;
            currentMonth = now.getMonth();
            if(riliVal) {
                var ymd_ = getStrYMD(riliVal);
                currentYear = ymd_[0];
                currentMonth = ymd_[1];
                currentDay = ymd_[2];
            }
            //console.log(allYears[0]);
            if(strInArray(currentYear, allYears) ==-1) {
                currentYear = allYears[0];
            }
            //console.log('currentYear:'+ currentYear);
            //console.log('currentMonth:'+ currentMonth);
            yearMoneyDaySelect = makeSelect({
                'value_key': 'value',
                'title_key': 'title',
                menu: {
                    width: yearMenuWidth,
                    'data': yearData
                },
                'default_text': currentYear,
                'value': currentYear +'',
                'size': ymSize,
                click: function(o) {
                    yearMoneyDaySelect['son']['menu'].hide();
                },
                li: {
                    value: "{title}",
                    //修改月份时 要重新载入日期
                    click: function (li_, eve, pubData) {
                        var yearMonth = getCurrentYM();
                        makeDays_(li_.attr('data-value'), yearMonth[1], currentDay);
                    }
                },
                son: {
                    menu: {
                        width: monthMenuWidth,
                        'data': monthData
                    },
                    click: function() {
                        console.log('click_son');
                        yearMoneyDaySelect['menu'].hide();
                    },
                    'value_key': 'value',
                    'title_key': 'title',
                    li: {
                        value: "{title}",
                        width: '25px',
                        //修改月份时 要重新载入日期
                        click: function (li_, eve, pubData) {
                            var yearMonth = getCurrentYM();
                            makeDays_(yearMonth[0], li_.attr('data-value'), currentDay);
                        }
                    },
                    'size': ymSize,
                    'default_text': currentMonth,
                    'value': currentMonth+''
                }
            });
            obj._rili.find('.show_year_month_box')
                .append(yearMoneyDaySelect).append('&nbsp;年 &nbsp;')
                .append(yearMoneyDaySelect['son']).append('&nbsp;月');
            obj._rili.find('.tr_').click(function (o) {
                yearMoneyDaySelect['menu'].hide();
                yearMoneyDaySelect['son']['menu'].hide();
            });
        }
        //计算某天是星期几
        function thisWeekDay (year,month,date) {
            var d = new Date(year,month-1,date);
            return d.getDay();
        }
        //console.log(yearData);
        //构建 月的下拉框
        var monthData = [];
        for(var i = 1;i<=12;i++){
            monthData.push({'value':i,'title':i});
        }
        makeRiliMenu();
        //获取当前选择的年月日
        function getCurrentYM() {
            var data_ = [];
            //console.log('vv:'+ yearMoneyDaySelect.value);
            data_.push(yearMoneyDaySelect.value);
            data_.push(yearMoneyDaySelect['son'].value);
            return data_;
        }
        //构建日历框 并显示
        function makeDays_(thisYear, thisMonth, thisDay) {
            //每次构建日历 要更新下拉框的年和月
            yearMoneyDaySelect.value = thisYear;
            yearMoneyDaySelect['son'].value = thisMonth;
            //判断是否为闰年
            function isBissextile(year){
                var isBis = false;
                if (0==year%4 && ((year%100!=0) || (year%400==0))) {
                    isBis = true;
                }
                return isBis;
            }
            //计算某月的总天数，闰年二月为29天
            function getMonthDays(year_,month_) {
                var days = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month_-1];
                if((month_==2) && isBissextile(year_)){
                    days++;
                }
                return days;
            }

            //输出天数
            var cHtml ="<tr class='day_tr'>";
            //算出当前年月1号是星期几
            var thisWeek = thisWeekDay(thisYear,thisMonth,1);
            if(thisWeek !=7){
                for (var sw = 0;sw<thisWeek;sw++){
                    cHtml+='<td></td>';
                }
            }
            //开始循环输出当月天数
            var css_;
            for (var i = 1; i < getMonthDays(thisYear,thisMonth)+1; i++) {
                if(thisDay == thisDay && i == thisDay && i == thisDay) {
                    css_ = ' current';
                } else {
                    css_ = '';
                }
                $(this).addClass('current');
                cHtml+='<td data-value="'+i+'"> <span class="day'+ css_ +'">'+ i +'</span></td>';
                //星期六换行
                if(thisWeekDay(thisYear, thisMonth, i)==6 ){
                    cHtml+="</tr>";
                    cHtml+="<tr class='day_tr'>";
                }
            }
            cHtml += '</tr>';
            cHtml += '</table>';
            if(obj._rili.find('.day_tr').length>0) obj._rili.find('.day_tr').remove();
            obj._rili.find('.week_tr').after($(cHtml));
            //选择当前日期
            var dataArray = [];
            obj._rili.find('.day').off().on('click', function() {
                dataArray = [];
                var day_ = $(this).text();
                $(this).addClass('current');
                var yearMonth = getCurrentYM();
                yearMonth.push(day_);
                var valStr = yearMonth.join(splitStr);
                obj.value = valStr;
                obj._rili.hide();
                //设置选中时的命令
                if(!isUndefined(options['chose'])) {
                    var choseFunc = options['chose'];
                    if(isString(choseFunc)) {
                        if(choseFunc.indexOf('(') == -1) choseFunc += "(valStr, obj, livingObj)";
                        eval(choseFunc);
                    } else {
                        choseFunc(valStr, obj, livingObj);
                    }
                }
            });
            //上一个月
            obj._rili.find('.last_month_btn').off().on('click', function() {
                var yearMonth = getCurrentYM();
                var year_ = yearMonth[0];
                var month_ = yearMonth[1];
                if(month_ ==1){
                    year_ = year_-1;
                    month_ = 12;
                } else {
                    month_ = month_-1;
                }
                makeDays_(year_, month_, thisDay);
            });
            //下一个月
            obj._rili.find('.next_month_btn').off().on('click', function() {
                var yearMonth = getCurrentYM();
                var year_ = yearMonth[0];
                var month_ = yearMonth[1];
                if(month_ ==12){
                    ++year_;
                    month_ = 1;
                }else{
                    ++month_;
                }
                makeDays_(year_, month_, thisDay);
            });
            //显示控件
            var pos = obj.offset();
            obj._rili.css({'display': 'block', 'left': pos.left, 'top': (pos.top + obj.outerHeight() ) });
        }
        obj.extend({
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return makeRili(optionsGet);
            }
        });
        addCloneName(obj, options);//支持克隆
        return obj;
    };

//在某个对象上定位内容
    $.fn.fixContent = function(content) {
        var target = $(this);
        var objPosition = target.offset();
        var newObj = makeDiv({
            style: {
                'position': 'absolute',
                'width': target.outerWidth() + 'px',
                'height': target.outerHeight() + 'px',
                'left': objPosition.left + 'px',
                'top': objPosition.top + 'px'
            },
            value: content
        });
        $('body').append(newObj);
        return newObj;
    };

//批量上传的表单
    global.makeUploadForm = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        var previewId = !isUndefined(options.id) ? options.id : 'multip_upload_prev_images_'+ parseInt(global.makeRadom(22));
        var uploadInput = makeInput({type: 'file', multi: 'true'});
        var submitUploadBtn = makeBtn({value: '上传',  'type': 'button', 'class': 'btnLr btnLrInfo'});
        var previewObj = makeDiv({
            'class': 'multip_upload_prev_images'
        });
        var formObj = makeForm({
            'type': 'upload',
            'id': previewId,
            elements: [
                {
                    obj: previewObj
                },
                {
                    obj: [
                        uploadInput, submitUploadBtn
                    ]
                }]
        });
        var params = {
            fileInput: uploadInput.find('input')[0],
            upButton: submitUploadBtn[0],
            url: '',
            filter: function(files) {
                var arrFiles = [];
                for (var i = 0, file; file = files[i]; i++) {
                    if (file.type.indexOf("image") == 0) {
                        if (file.size >= 512000) {
                            alert('您这张"'+ file.name +'"图片大小过大，应小于500k');
                        } else {
                            arrFiles.push(file);
                        }
                    } else {
                        alert('文件"' + file.name + '"不是图片。');
                    }
                }
                return arrFiles;
            },
            onSelect: function(files) {
                var html = '', i = 0;
                previewObj.html('<div class="upload_loading"></div>');
                var funAppendImage = function() {
                    file = files[i];
                    if (file) {
                        var reader = new FileReader()
                        reader.onload = function(e) {
                            html = html + '<div id="uploadList_'+ i +'" class="upload_append_list"><p><strong>' + file.name + '</strong>'+
                                '<a href="javascript:" class="upload_delete" title="删除" data-index="'+ i +'">删除</a><br />' +
                                '<img id="uploadImage_' + i + '" src="' + e.target.result + '" class="upload_image" /></p>'+
                                '<span id="uploadProgress_' + i + '" class="upload_progress"></span>' +
                                '</div>';

                            i++;
                            funAppendImage();
                        }
                        reader.readAsDataURL(file);
                    } else {
                        previewObj.html(html);
                        if (html) {
                            //删除方法
                            $(".upload_delete").click(function() {
                                ZXXFILE.funDeleteFile(files[parseInt($(this).attr("data-index"))]);
                                return false;
                            });
                            //提交按钮显示
                            submitUploadBtn.show();
                        } else {
                            //提交按钮隐藏
                            submitUploadBtn.hide();
                        }
                    }
                };
                funAppendImage();
            },
            onDelete: function(file) {
                $("#uploadList_" + file.index).fadeOut();
                formObj[0].reset();
            },
            onDragOver: function() {
                $(this).addClass("upload_drag_hover");
            },
            onDragLeave: function() {
                $(this).removeClass("upload_drag_hover");
            },
            onProgress: function(file, loaded, total) {
                var eleProgress = $("#uploadProgress_" + file.index), percent = (loaded / total * 100).toFixed(2) + '%';
                eleProgress.show().html(percent);
            },
            onSuccess: function(file, response) {
                $("#uploadInf").append("<p>上传成功，图片地址是：" + response + "</p>");
            },
            onFailure: function(file) {
                $("#uploadInf").append("<p>图片" + file.name + "上传失败！</p>");
                $("#uploadImage_" + file.index).css("opacity", 0.2);
            },
            onComplete: function() {
                //提交按钮隐藏
                submitUploadBtn.hide();
                //file控件value置空
                formObj[0].reset();
                // 成功提示
                $("#uploadInf").append("<p>当前图片全部上传完毕，可继续添加上传。</p>");
            }
        };
        ZXXFILE = $.extend(ZXXFILE, params);
        ZXXFILE.init();
        return formObj;
    };

    //创建编辑器[属性：name,width,height,content urlencode , type:'uEditor|xheditor', editorObj: 回调的编辑器对象', 'remote':{ url: '/upload.php',success_val: '0308'}]
    global.makeEditor = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        var editorId = !isUndefined(options['id']) ? options['id'] : 'editormd';
        var obj = $('<textarea id="'+ editorId +'"></textarea>');
        obj['last_options'] = getOptVal(options, 'last_options', {});
        obj['tag'] = 'editor';
        obj[objValIsNode] = false;
        var valueStrFormatdSuccess = true;//当前value是否渲染完成

        //单独的格式化value的括号
        obj.formatVal = function (opt) {
            opt = opt || [];
            var editorOut = !isUndefined(opt['editorObj']) ? opt['editorObj'] : 'editor';
            var sourceVal = opt['source_value'] || opt['value'];
            var newVal;
            if(strHasKuohao(sourceVal, 'public')) {
                newVal = strObj.formatStr(sourceVal, livingObj['data'], 0, obj, 'value');
                obj[objAttrHasKh] = true;
            }else if(strHasKuohao(sourceVal, 'data')) {
                newVal = strObj.formatStr(sourceVal, opt['data']||{}, 0, obj, 'value');
                obj[objAttrHasKh] = true;
            } else {
                newVal = sourceVal;
            }
            opt['value'] = newVal; //参数要改变 防止外部取出来的仍是括号
            obj.renewVal(newVal);
            if (!optionIsSame(obj, opt, 'value')) {
                //console.log('value change');
                valueStrFormatdSuccess = true;
            }
            var renewBind = obj[objAttrHasKh]==true;
            if(options['bind'] && renewBind) {//触发数据同步  触发赋值 */
                updateBindObj($.trim(options['bind']), newVal, [obj]);
            }
            if(valueStrFormatdSuccess) {
                if(obj.lazyCall) {
                    obj.lazyCall(obj, opt['data'] || {}, livingObj);
                }
            }
            if(obj[editorOut] && obj[editorOut].setContent) {
                obj[editorOut].setContent(newVal);
            }
            obj.formatPlugs(opt);
        };
        obj.renewVal = function(newVal) {
            // console.log('renewVal:', newVal);
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
                strObj.formatAttr(obj, options); //里面找出事件来绑定
                optionGetSet(this, options);
                obj[objLastValKey] = obj[objValObjKey];//设置完所有属性 要更新旧的val
                obj['last_options'] = $.extend({}, options);//设置完所有属性 要更新旧的option
            },
            updates: function(dataName, exceptObj) {//数据同步
                //console.log(dataName);
                exceptObj = exceptObj || [];
                if(options['bind'] && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(this);
                    this.renewVal(getObjData($.trim(options['bind'])));
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    //console.log('updates datas');
                    //console.log(this);
                    renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return makeEditor(optionsGet);
            }
        });
        obj.renew(options);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        objBindVal(obj, options);//数据绑定
        addCloneName(obj, options);//支持克隆
        //console.log(obj);
        return obj; //makeEditor
    };

    //创建一个浮动层
    /*
     * options {left:12,top:66,'class':'',id:''}
     * */
    global.appendMenu = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        if(isUndefined(options.left)) options.left = 0;
        if(isUndefined(options.top)) {
            options.y = 0;
        } else {
            options.y = options.top;
        }
        makeBox(options);
    };
//创建浮动菜单
    global.makeMenu = function(btn, menuId, diyOptions, contendObj, appendType) {
        var menu = $('#'+ menuId);
        if(menu.length > 0) return menu;
        appendType = appendType || 'btn';//btn: append after to the btn; body: append to the body
        var btnMarginLeft = btn.css('marginLeft') ? parseFloat(btn.css('marginLeft')) : 0;
        var btnMarginTop = btn.css('marginTop') ? parseFloat(btn.css('marginTop')) : 0;
        var btnPositionTop = btn.position().top;
        //获取按钮父页面的所有left
        var maxParentLeft = 0;
        var tmpLeft;
        btn.parents().each(function () {
            tmpLeft = parseInt($(this).css('left'));
            if(!isNaN(tmpLeft) && tmpLeft>0)  {
                maxParentLeft = tmpLeft;
            }
        });
        var offsets = btn.offset();
        var btnLeft = offsets.left;
        var btnTop = offsets.top;
        btnLeft -= btnMarginLeft;
        btnLeft -= maxParentLeft;
        btnTop -= btnMarginTop;
        var btnHeight = parseFloat(btn.outerHeight());
        var positionOption = {
            'position': 'absolute',
            'z-index': '10000',
            'display': 'none',
            'top': btnTop + btnHeight,
            'left': btnLeft
        };
        var menuOption = {id: menuId};
        menuOption = jQuery.extend(menuOption, positionOption);
        if(diyOptions) {
            if(!isUndefined(diyOptions['class'])) {
                diyOptions['class'] = $.trim(diyOptions['class']) + ' '+ menu_pub_class_name;
            } else {
                diyOptions['class'] = menu_pub_class_name;
            }
            menuOption = jQuery.extend(menuOption, diyOptions);
        } else {
            menuOption['class'] = menu_pub_class_name; //默认加系统指定菜单名
        }
        menu = makeDiv(menuOption);
        menu.append(contendObj);
        if(appendType == 'body') {
            $('body').append(menu);
        } else {
            btn.after(menu);
        }
        return menu;
    }
//创建上一页 下一页的分页功能
    global.makePage = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        var pageBody = $('<ul></ul>');
        options['size'] = (!options['size'] || !setSize(options['size'])) ? 'md' : options['size'];//过滤size
        var $pageExtClass = 'pagination';
        if(sizeIsLg(options['size'])) {
            $pageExtClass += ' pagination-lg';
        } else if(sizeIsSm(options['size'])) {
            $pageExtClass += ' pagination-sm';
        } else if(sizeIsXs(options['size'])) {
            $pageExtClass += ' pagination-xs';
        }
        options['class_extend'] = $pageExtClass;
        pageBody['current_page'] = 1;
        pageBody.totalPage = 0;
        pageBody.fromPage  = 0;
        pageBody.toPage  = 0;
        pageBody.gotoPage  = '';
        pageBody.gotoPageObj  = null;
        pageBody.noNeedEven  = true;//不需要定义任何的点击事件 防止和系统的点击翻页冲突
        //支持value
        Object.defineProperty(pageBody, 'page', {
            get: function () {
                return parseInt(this['current_page']);
            },
            set: function(newP) {  //支持外部设值
                this.setPage(newP);
            }
        });
        pageBody.extend({
            //设置页面
            setPage: function (newP, exceptObj) {//数据被动同步
                if(newP > pageBody.totalPage) return;
                exceptObj = exceptObj || [];
                if(options['bind']) {//触发数据同步  触发赋值 */
                    if($.inArray(this, exceptObj) == -1) {
                        exceptObj.push(this);
                    }
                    updateBindObj($.trim(options['bind']), newP, exceptObj);
                }
                var opt = options;
                var li = pageBody.find("li[data-page='"+ newP +"']");
                if(li.length == 0 || newP-pageBody.fromPage<=2  || newP-pageBody.fromPage>= pageBody.pageBtnNum-2 ) { //跳度太大 页面不存在 需要重新生成
                    opt['page'] = newP;
                    this.renew(opt);
                } else {
                    li.addClass('active').siblings('.active').removeClass('active');
                }
                pageBody['current_page'] = newP;
                //console.log(this);
                //为何要出发点击事件 如果是外部data赋值page数 这个方法会再次出发自身循环
                if(opt['click']) opt['click'](li, newP, livingObj);
            },
            //主动更新数据
            renew: function (options) {
                options = options || {};
                var defaultCfg = {
                    page: 1,
                    pageSize: 10,//单页数量
                    pagenum: 5, //显示页数
                    size: 'md',//分页的外观尺寸
                    total: 1
                };
                //兼容各自语法
                var data_ = options['data'] ||{};
                //console.log(JSON.stringify(options));
                //console.log(options['total']);
                //console.log(data_);
                var pageSize = getOptVal(options, ['pagesize','page_size', 'pageSize'], 10);//单页显示数量
                // console.log('pageSize1:');
                // console.log(pageSize);
                var pageBtnNum = getOptVal(options, ['pagenum', 'pageNum'], 5);//分页按钮显示的数量
                var pageType = getOptVal(options, ['type'], 'default');//分页样式 default/btn
                var goto = getOptVal(options, ['goto'], null);
                var pageClass = getOptVal(options, ['class'], '');
                options['page'] = options['page'] || 1;
                options['btnSize'] = (!options['size'] || !setSize(options['size'])) ? 'md' : options['size'];//过滤size
                pageSize = parseInt(formatIfHasKuohao(pageSize, data_));
                pageBtnNum = formatIfHasKuohao(pageBtnNum, data_);
                options['btnSize'] = formatIfHasKuohao(getOptVal(options, ['btnSize'], 1), data_);
                options['page'] = parseInt(formatIfHasKuohao(getOptVal(options, ['page'], 1), data_));
                options['total'] = parseInt(formatIfHasKuohao(getOptVal(options, ['total'], 0), data_));
                if(!isUndefined(options['pagesize'])) delete options['pagesize'];//统一大小写
                if(!isUndefined(options['page_size'])) delete options['page_size'];//统一大小写
                options['pageSize'] = pageSize;//统一输出
                options = $.extend({}, defaultCfg, options);
                var $pageExtClass = 'pagination';
                if(pageClass) $pageExtClass = pageClass;
                if(options['btnSize'] == 'lg') {
                    $pageExtClass += ' pagination-lg';
                } else if(options['btnSize'] == 'sm') {
                    $pageExtClass += ' pagination-sm';
                } else if(sizeIsXs(options['btnSize'])) {
                    $pageExtClass += ' pagination-xs';
                }
                options['class_extend'] = $pageExtClass;
                var parentOpt = $.extend({}, options);
                delete parentOpt['click'];//父对象不需要点击事件
                //console.log(parentOpt);
                //page只有class无需再修改
                pageBody.attr('class', options['class_extend']);
                //console.log('page:');
                //console.log(options);
                optionDataFrom(pageBody, options);
                strObj.formatAttr(pageBody, options); //里面找出事件来绑定
                optionGetSet(pageBody, options);
                var page = parseInt(options.page);
                var pageSize = parseInt(pageSize);
                if(pageSize < 1 ) pageSize = 1;
                var totalNum = parseInt(options.total);
                var totalPage = totalNum / pageSize;
                //console.log('totalNum:'+totalNum);
                //console.log('pageSize:'+pageSize);
                if(totalPage.toString().indexOf('.')!=-1) totalPage = parseInt(totalPage) + 1;
                //console.log('totalPage:'+totalPage);
                if(page>totalPage) {
                    page = totalPage;
                }
                //console.log(pageBody);
                pageBody['current_page'] = page;
                pageBody.totalPage = totalPage;
                pageBody.pageBtnNum = pageBtnNum;
                pageBody.empty();
                if(pageType=='default') {
                    var preLi = $('<li><a href="javascript: void(0)" target="_self">&laquo;</a></li>');
                } else if(pageType == 'btn') {
                    var preLi = $('<li><a href="javascript: void(0)" target="_self" class="endPage"> &lt; </a></li>');
                }
                preLi.off().on('click', function (e) {
                    var nowPage = pageBody['current_page'];
                    var thisToPage = parseInt(nowPage) - 1;
                    if(thisToPage <1) {
                        msgTis('已经是第一页');
                    } else {
                        pageBody.gotoPage = '';
                        if(pageBody.gotoPageObj) pageBody.gotoPageObj.val('');
                        pageBody.setPage(thisToPage);
                    }
                });
                pageBody.append(preLi);

                pageBody.fromPage = page - parseInt(pageBtnNum/2);
                var toPage;
                if(pageBody.fromPage<1)  {
                    pageBody.fromPage = 1;
                    toPage = pageBody.fromPage + pageBtnNum;
                } else {
                    toPage = pageBody.fromPage + pageBtnNum ;
                }

                var i,li;
                if(pageBody.fromPage < 1) pageBody.fromPage = 1;
                if(toPage>totalPage) toPage = totalPage;
                if(toPage == totalPage) toPage = totalPage+1; //到达尾部 直接显示全部页码
                if(pageBody.fromPage == toPage) {
                    pageBody.fromPage = 1;
                }
                if(page > toPage) {
                    pageBody.fromPage = page-1;
                    toPage = toPage + pageBtnNum;
                    if(pageBody.fromPage < 1) pageBody.fromPage = 1;
                    if(toPage>totalPage) toPage = totalPage;
                }
                //console.log(toPage);
                var repeatNum = 0;
                for(i = pageBody.fromPage; i < toPage; i++) {
                    if(repeatNum >= pageBtnNum) break;//break;
                    li = $('<li data-page="'+ i +'"></li>');
                    li.append('<a href="javascript: void(0)" target="_self">'+ i +'</a>');
                    if(page == i) li.addClass('active');
                    li.off().on('click', function (e) {
                        // e.stopPropagation();
                        var clickObj = $(this);
                        var pageNew = clickObj.attr('data-page');
                        pageBody.setPage(pageNew);
                        pageBody.gotoPage = '';
                        if(pageBody.gotoPageObj) pageBody.gotoPageObj.val('');
                    });
                    pageBody.append(li);
                    repeatNum ++;
                }
                if(pageType=='default') {
                    var nextLi = $('<li><a href="javascript: void(0)" target="_self"> &raquo; </a></li>');
                    nextLi.off().on('click', function (e) {
                        var nowPage = pageBody['current_page'];
                        var thisToPage = parseInt(nowPage) + 1;
                        if(thisToPage > totalPage) {
                            msgTis('已经是最后一页');
                        } else {
                            pageBody.gotoPage = '';
                            if(pageBody.gotoPageObj) pageBody.gotoPageObj.val('');
                            pageBody.setPage(thisToPage);
                        }
                    });
                    pageBody.append(nextLi);
                } else if(pageType == 'btn') {
                    var nowPage = pageBody['current_page'];
                    var senglue = (nowPage == totalPage || toPage>=totalPage )? null: $('<li><a href="javascript: void(0)" target="_self" class="endPage"> ... </a></li>');
                    var totalLi = (nowPage == totalPage || toPage>=totalPage )? null: $('<li><a href="javascript: void(0)" target="_self"> '+ totalPage +' </a></li>');
                    var nextLi = $('<li><a href="javascript: void(0)" target="_self" class="endPage"> &gt; </a></li>');
                    if(totalLi) {
                        totalLi.off().on('click', function (e) {
                            pageBody.setPage(totalPage);
                        });
                    }
                    nextLi.off().on('click', function (e) {
                        var nowPage = pageBody['current_page'];
                        var thisToPage = parseInt(nowPage) + 1;
                        if(thisToPage > totalPage) {
                            msgTis('已经是最后一页');
                        } else {
                            pageBody.gotoPage = '';
                            if(pageBody.gotoPageObj) pageBody.gotoPageObj.val('');
                            pageBody.setPage(thisToPage);
                        }
                    });
                    if(senglue)pageBody.append(senglue);
                    if(totalLi)pageBody.append(totalLi);
                    if(nextLi)pageBody.append(nextLi);
                }
                if(goto) {
                    var gotoLi = $('<li><a><input class="togoPage" placeholder="Goto" /></a></li>');
                    var gotoPageObj = gotoLi.find('.togoPage');
                    if(pageBody.gotoPage) gotoPageObj.val(pageBody.gotoPage);
                    gotoPageObj.off().on('blur', function (e) {
                        var thisPage = parseInt($(this).val());
                        if(!thisPage || thisPage<1) return;
                        if(thisPage > totalPage) {
                            msgTisf('noMorePage');
                            return;
                        }
                        pageBody.gotoPage = thisPage;
                        if(thisPage>totalPage) thisPage = totalPage;
                        pageBody.setPage(thisPage);
                    });
                    pageBody.gotoPageObj = gotoPageObj;
                    if(strInArray(goto, ['r', 'right']) !=-1) {
                        pageBody.append(gotoLi);
                    } else if(strInArray(goto, ['l', 'left']) !=-1) {
                        pageBody.prepend(gotoLi);
                    }
                }

                pageBody['last_options'] = $.extend({}, options);//设置完所有属性 要更新旧的option

            },
            //data更新时  page更新
            renewPageData: function(data) {
                //console.log('renewPageData self:');
                //console.log(data);
                var optSource = cloneData(options);
                optSource['data'] = data;
                this.renew(optSource);
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return global.makePage(optionsGet);
            },
            updates: function(dataName, exceptObj) {//数据被动同步
                //console.log('updates this');
                exceptObj = exceptObj || [];
                if(options['bind'] && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(this);
                    this.setPage(getObjData($.trim(options['bind'])), exceptObj);
                }
                if(this[objBindAttrsName] && this[objBindAttrsName][dataName]) {
                    //console.log(getObjData(dataName));
                    if(strInArray('page', this[objBindAttrsName][dataName]) !=-1) this.setPage(getObjData(dataName));
                }
            }
        });
        objBindVal(pageBody, options);//数据绑定
        optionGetSet(pageBody, options); //允许外部修改
        pageBody.renew(options);
        return pageBody;
    }

    //创建黑三角
    global.makeSanjiao = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        var sanjiaoBody = $('<span><em class="icon"></em></span>');
        var sanjiaoIcon = sanjiaoBody.children();
        sanjiaoBody['current_type'] = 's'; //上s 右y 下x 左z 上下sx ♦ &#9830;
        var allowType = ['s','shang','u','up','t','top', 'y','you','r','right', 'x','xia','d','down','b','bottom', 'z','zuo','l','left','sx'];
        options['class_extend'] = 'diy_sanjiao';
        function formatType(type_) {
            if(strInArray(type_, ['s','shang', 'u','up','t','top']) !=-1) {
                return 's';
            }
            if(strInArray(type_, ['y','you','r','right']) !=-1) {
                return 'y';
            }
            if(strInArray(type_, ['x','xia','d','down','b','bottom']) !=-1) {
                return 'x';
            }
            if(strInArray(type_, ['z','zuo','l','left']) !=-1) {
                return 'z';
            }
        }
        //外部data 变化时更新type
        sanjiaoBody.renewType = function (opt) {
            opt = opt || [];
            var type_ = opt['source_type'] || opt['type'] || ''; //每次格式化 优先取格式化前的value
            if(strHasKuohao(type_, 'data')) {
                opt['source_type'] = type_;
                //console.log('has kuohao');
                //console.log(val);
                //console.log(opt['data']);
                type_ = strObj.formatStr(type_, opt['data']||{}, 0, sanjiaoBody, 'type');
                //console.log(type_);
                sanjiaoBody[objAttrHasKh] = true;
            } else if(strHasKuohao(type_, 'public')) {
                opt['source_type'] = type_;
                type_ = strObj.formatStr(type_, livingObj['data'], 0, sanjiaoBody, 'type');
                sanjiaoBody[objAttrHasKh] = true;
            }
            opt['type'] = type_; //参数要改变 防止外部取出来的仍是括号
            sanjiaoBody.setType(type_);
        };

        //支持 type 定义
        Object.defineProperty(sanjiaoBody, 'type', {
            get: function () {
                return this['current_type'];
            },
            set: function(newType) {  //支持外部设值
                this.setType(newType);
            }
        });
        sanjiaoBody.extend({
            //设置页面
            setType: function (newType, exceptObj) {//数据被动同步
                if(strInArray(newType, allowType) ==-1) newType = 's';
                newType = formatType(newType);
                exceptObj = exceptObj || [];
                if(options['bind']) {//触发数据同步  触发赋值 */
                    if($.inArray(this, exceptObj) == -1) exceptObj.push(this);
                    updateBindObj($.trim(options['bind']), newType, exceptObj);
                }
                sanjiaoBody['current_type'] = newType;
                sanjiaoIcon.attr('class', newType);
            },
            //主动更新数据
            renew: function (options) {
                options = options || {};
                var defaultCfg = {
                    type: 's'
                };
                options = $.extend({}, defaultCfg, options);
                options['class_extend'] = 'diy_sanjiao';
                if(isUndefined(options['type'])) options['type'] = 's';
                if(!strHasKuohao(options['type'])) {
                    if(strInArray(options['type'], allowType) ==-1) options['type'] = 's';
                    options['type'] = formatType(options['type']);
                }
                strObj.formatAttr(sanjiaoBody, options);//基本属性 无需再修改value
                sanjiaoBody['last_options'] = $.extend({}, options);//设置完所有属性 要更新旧的option
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return global.makePage(optionsGet);
            },
            updates: function(dataName, exceptObj) {//数据被动同步
                dataName = $.trim(dataName);
                //console.log('updates this');
                exceptObj = exceptObj || [];
                if(options['bind'] && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(this);
                    this.setType(getObjData($.trim(options['bind'])), exceptObj);
                }
                if(this[objBindAttrsName] && this[objBindAttrsName][dataName]) {
                    //console.log(getObjData(dataName));
                    if(strInArray('page', this[objBindAttrsName][dataName]) !=-1) this.setType(getObjData(dataName));
                }
            }
        });
        objBindVal(sanjiaoBody, options, 'type');//数据绑定
        optionGetSet(sanjiaoBody, options); //允许外部修改
        sanjiaoBody.renew(options);
        return sanjiaoBody;
    };

//创建验证码
    global.makeValidate = function(btn, direction) {
        direction = direction || 'bottom';
        var boxId = "validate_append_box";
        var validateBox = $('<div></div>');
        validateBox.attr({
            'id': boxId,
            'class': 'validate_append_box'
        });
        validateBox.append('<div class="validate_menu_box"><iframe border="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" framespacing="0" ' +
            'frameborder="0" scrolling="no" width="260" height="150" src="/any/validate"></iframe></div>');
        var balidateMenu = validateBox.find('.validate_menu_box');
        $('body').append(validateBox);
        var btnWidth = btn.outerWidth();
        var btnHeight = btn.outerHeight();
        var winWidth_ = $(window).width();//浏览器可见宽度
        var winHeight = $(window).height();//浏览器可见高度
        var winScrolltop = $(document).scrollTop();
        var y_ = winScrolltop + (winHeight / 2);
        validateBox.css({'height': winScrolltop+winHeight, 'width': winWidth_});
        var btnLeft = btn.offset().left;
        var btnTop = btn.offset().top;
        if(direction == 'bottom') {//出现在按钮下方
            balidateMenu.css({'left': btnLeft, 'top': btnTop + btnHeight});
        } else if(direction == 'top') {//出现在按钮上方
            balidateMenu.css({'left': btnLeft, 'top': btnTop - 150});
        } else if(direction == 'right') {//出现在按钮右侧
            balidateMenu.css({'left': btnLeft + btnWidth, 'top': btnTop});
        }
        validateBox.on('click', function () {
            validateBox.remove();
        });
        return validateBox;
        // window.success_drag_validate_push = function (n) {
        //     btn.attr('data-validate', n);
        //     validateBox.remove();
        //     btn.click();
        // };
    }

    //创建拖动条
    //创建文本dom /a/p/span/div/li/td/em/i////
    global.makeBar = function (sourceOptions) {
        var opt = cloneData(sourceOptions);
        var defaultOps = opt || {};
        var extendAttr = opt['extend_attr'] || {};
        var afterCreate = getOptVal(opt, ['afterCreate', 'after_create'], false);
        var options = $.extend({}, defaultOps);
        options = $.extend({}, options, extendAttr);//支持外部扩展属性 如 a 的 href
        var bar = $('<div></div>');
        bar.returnVal = opt['value'] || '';
        bar.htmObj = [];//初始化element节点
        if(isUndefined(options['value'])) options['value'] = '';
        bar['last_options'] = [];
        bar[objValIsNode] = false;
        bar[objAttrHasKh] = false;
        var valueStrFormatdSuccess = false;
        // //支持外部设置值
        Object.defineProperty(bar, 'value', {
            get: function () {
                return bar.returnVal;
            },
            set: function (newVal) {
                if($.isArray(newVal)) newVal = newVal.join(',');
                bar.returnVal = newVal;
                bar.moveBtnByVal(newVal);
            }
        });
        //更新val
        bar.formatVal = function (opt) {
            var sourceVal = opt['source_value'] || opt['value'];
            var newVal;
            if(strHasKuohao(sourceVal, 'public')) {
                newVal = strObj.formatStr(sourceVal, livingObj['data'], 0, bar, 'value');
                bar[objAttrHasKh] = true;
            }else if(strHasKuohao(sourceVal, 'data')) {
                newVal = strObj.formatStr(sourceVal, opt['data']||{}, 0, bar, 'value');
                bar[objAttrHasKh] = true;
            } else {
                newVal = sourceVal;
            }
            bar.returnVal = newVal; //参数要改变 防止外部取出来的仍是括号
            if (!optionIsSame(bar, opt, 'value')) {
                valueStrFormatdSuccess = true;
            }
            var renewBind = bar[objAttrHasKh]==true;
            if(options['bind'] && renewBind) {//触发数据同步  触发赋值 */
                updateBindObj($.trim(options['bind']), newVal, [bar]);
            }
            if(valueStrFormatdSuccess) {
                if(bar.lazyCall) {
                    bar.lazyCall(bar, opt['data'] || {}, livingObj);
                }
            }
            bar.moveBtnByVal(newVal);
        };

        //外部设置val
        bar.extend({
            //主动更新数据
            renew: function(options_) {
                options_ = $.extend({}, options_, extendAttr);//支持外部扩展属性 如 a 的 href
                if(!options_)  return;
                var barVal = options_['value'] || '';
                optionDataFrom(bar, options_);
                //console.log(dataFrom);
                //console.log(data_);
                var iconOpt = options_['icon']|| {};
                var movingFunc = options_['moving']|| null;
                var direction = options_['direction']|| 'x';
                var mouseUpFunc = options_['mouse_up'] || options_['mouseup'] || options_['mouseUp'] || null;
                var maxVal = isUndefined(options_['max']) ? null : options_['max'];
                var minVal = isUndefined(options_['min']) ? null : options_['min'];
                var decNum = isUndefined(options_['dec']) ? null : options_['dec'];//保留几位小数
                maxVal = toNumber(maxVal);
                minVal = toNumber(minVal);
                iconOpt['class_extend'] = 'icon';
                var iconMinLeft = iconOpt['min-left']||  iconOpt['min_left']|| iconOpt['minLeft']|| 0;
                var iconMinTop = iconOpt['min-top']||  iconOpt['min_top']|| iconOpt['minTop'] || (direction=='x'?-2:-1);
                iconOpt['left'] = toNumber(iconMinLeft) + 'px';
                iconOpt['top'] = parseFloat(iconMinTop) + 'px';
                //console.log('min_left:'+ iconMinLeft);
                iconOpt['width'] = isUndefined(iconOpt['width']) ? 30 : iconOpt['width'];
                //console.log('iconOpt');
                //console.log(iconOpt);
                var iconObj = makeSpan(iconOpt);
                options_['class_extend'] = 'diy_bar';
                options_['style'] = 'border:1px solid #ddd';
                bar.html('').append(iconObj);
                //根据value定位拖动按钮
                bar.moveBtnByVal = function(newVal) {
                    var iconWidth = toNumber(iconObj.outerWidth());
                    var barWidth = toNumber(bar.width);
                    var maxLeft = barWidth - iconWidth -2;
                    var iconWidth = toNumber(iconObj.outerWidth());
                    var maxTop = bar.outerHeight() - iconHeight -2;
                    var iconHeight = toNumber(iconObj.outerHeight());
                    if(direction=='x') {
                        var newLeft = (newVal / maxVal) * barWidth;
                        if(iconMinLeft) newLeft = Math.max(newLeft, iconMinLeft);
                        if(maxLeft) newLeft = Math.min(newLeft, maxLeft);
                        iconObj.css('left', newLeft);
                    } else {
                        var newTop = (newVal / maxVal) * barHeight;
                        if(iconMinTop) newTop = Math.max(newTop, iconMinTop);
                        if(maxTop) newTop = Math.min(newTop, maxTop);
                        iconObj.css('top', newTop);
                    }
                };
                //点击定位滚动条
                options_['click_extend'] = function(obj_, e) {
                    if(e.target !== obj_[0]) return; //not click bar
                    var clientPos = '',xy;
                    if(direction=='x') {
                        var btnWidth = toNumber(iconObj.outerWidth(true));
                        var barWidth = toNumber(bar.width);
                        clientPos = e.clientX - obj_.offset().left;
                        clientPos -= btnWidth/2; //居中按钮
                        if(clientPos + btnWidth > barWidth -2) clientPos = barWidth - btnWidth -2;
                        if(clientPos < iconMinLeft) clientPos = iconMinLeft;
                        //console.log('clientPos:'+ clientPos);
                        //console.log('btnWidth:'+ (btnWidth/2) );
                        //console.log('barWidth:'+ barWidth);
                        //console.log(bar);
                        xy = [clientPos, 0];
                        iconObj.css('left', clientPos);
                    } else {
                        var btnHeight = toNumber(iconObj.outerHeight(true));
                        var barHeight = toNumber(bar.height);
                        clientPos = e.clientY - obj_.offset().top;
                        clientPos -= btnHeight/2; //居中按钮
                        if(clientPos + btnHeight > barHeight -2) clientPos = barHeight - btnHeight -2;
                        if(clientPos < iconMinTop) clientPos = iconMinTop;
                        xy = [0, clientPos];
                        iconObj.css('top', clientPos);
                    }
                    xy = bar.countVal(xy);
                    var newVal = direction =='x' ? xy[0] : xy[1];
                    if(!isUndefined(options_['bind']) && options_['bind']) {
                        updateBindObj($.trim(options_['bind']), newVal, [bar]);
                    }
                    bar.returnVal = newVal;
                };
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                //先设定options_参数 下面才可以修改options_
                //console.log('renew call_formatAttr:');
                strObj.formatAttr(this, options_); //里面找出事件来绑定
                optionGetSet(this, options_);
                this['last_options'] = $.extend({}, options_);//设置完所有属性 要更新旧的option
                //console.log('finish');
                //console.log(this);
                //等创建好对象再初始化所有高度
                setTimeout(function () {
                    if(strInArray(direction, ['x', 'y']) ==-1) direction = 'x';
                    var iconWidth = iconOpt['width'] || iconObj.outerWidth();
                    iconWidth = toNumber(iconWidth);
                    var iconHeight = iconOpt['height'] || iconObj.outerHeight();
                    iconHeight = toNumber(iconHeight);
                    var barHeight;
                    if(direction=='x') {
                        barHeight = iconOpt['height'] || iconHeight;
                    } else {
                        barHeight = options['height'] || bar.outerHeight();
                    }
                    barHeight = toNumber(barHeight);
                    var barWidth = options['width'] ||  bar.outerWidth();
                    barWidth = toNumber(barWidth);
                    //计算刻度尺 当前位置应该得到的值
                    bar.countVal = function (xy) {
                        var distance_,newVal;
                        if(!maxVal) maxVal = barWidth;
                        if(direction=='x') {
                            distance_ = formatFloat(xy[0]);
                            newVal = maxVal * (distance_ - iconMinLeft) / (barWidth - iconWidth-2);
                            newVal = formatFloat(newVal, decNum);
                            if(newVal < minVal) newVal = minVal;
                            if(newVal > maxVal) newVal = maxVal;
                            xy[0] = newVal;
                        } else {
                            distance_ = formatFloat(xy[1]);
                            newVal = maxVal * (distance_ - iconMinLeft) / (barHeight - iconHeight-2);
                            newVal = formatFloat(newVal, decNum);
                            if(newVal < minVal) newVal = minVal;
                            if(newVal > maxVal) newVal = maxVal;
                            xy[1] = newVal;
                        }
                        return xy;
                    };
                    var movingBackFunc = function (xy, icon, bar_) {
                        //console.log(xy);
                        xy = bar.countVal(xy);
                        var newVal = direction =='x' ? xy[0] : xy[1];
                        if(!isUndefined(options_['bind']) && options_['bind']) {
                            updateBindObj($.trim(options_['bind']), newVal, [bar]);
                        }
                        bar.returnVal = newVal;
                        if(movingFunc) movingFunc(xy, icon, bar_);
                    };
                    var movingUpBackFunc = function (xy, icon, bar_) {
                        //console.log(xy);
                        xy = bar.countVal(xy);
                        var newVal = direction =='x' ? xy[0] : xy[1];
                        if(!isUndefined(options_['bind']) && options_['bind']) {
                            updateBindObj($.trim(options_['bind']), newVal, [bar]);
                        }
                        bar.returnVal = newVal;
                        //console.log(xy);
                        if(mouseUpFunc) mouseUpFunc(xy, icon, bar_);
                    };
                    var maxTop = bar.outerHeight() - iconHeight -2;
                    var maxLeft = barWidth - iconWidth -2;
                    if(maxLeft <iconMinLeft) maxLeft = iconMinLeft;
                    //console.log('iconWidth:'+ iconWidth);
                    //console.log('min_left:'+ iconMinLeft);
                    //console.log('maxLeft:'+ maxLeft);
                    var opt = {
                        min_top: iconMinTop,
                        min_left: iconMinLeft,
                        max_left: maxLeft,
                        max_top: maxTop,
                        draging_data: [movingBackFunc, iconObj, bar],
                        drag_up_data: [movingUpBackFunc, iconObj, bar]
                    };
                    iconObj.Drag('', '', opt);
                    //被更时 触发按钮移动
                    if(!isUndefined(options_['bind']) && options_['bind']) {
                        var pubVal = getObjData($.trim(options_['bind']));
                        if(pubVal ==='') {
                            pubVal = barVal;
                        }
                        bar.moveBtnByVal(pubVal);
                    } else if(barVal) {
                        if(isNumber(barVal)){
                            bar.moveBtnByVal(barVal);
                        }
                    }
                }, 20);
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return makeBar(optionsGet);
            },
            updates: function(dataName, exceptObj) {//数据被动同步
                //console.log('updates this:'+ dataName);
                //console.log(exceptObj);
                exceptObj = exceptObj || [];
                if(options['bind']) {
                    if($.inArray(this, exceptObj) == -1) {
                        exceptObj.push(this);
                        //console.log('update this');
                        //console.log(exceptObj);
                        var pubVal = getObjData($.trim(defaultOps['bind']));
                        //console.log('updateNodeText this：'+ pubVal);
                        bar.returnVal = pubVal;
                        bar.moveBtnByVal(pubVal);
                    }
                }
                if(bar[objBindAttrsName] && bar[objBindAttrsName][dataName]) {
                    //attrs(如:class) 中含{公式 {dataName} > 2}
                    //如果value中含{}也会由此处开始更新
                    //console.log('renew ObjAttr this');
                    //console.log(this);
                    renewObjBindAttr(this, dataName);
                }
            }
        });
        // if(tag == 'form') console.log('here form op:');
        //console.log('herea:');
        //console.log(bar);
        //console.log(defaultOps['value']);
        bar.renew(defaultOps);//首次赋值 赋值完才能作数据绑定 同步绑定的数据
        objBindVal(bar, defaultOps);//数据绑定
        addCloneName(bar, defaultOps);//支持克隆
        if(afterCreate) afterCreate(bar, defaultOps); //初始化内容再写入内容
        return bar;
    };
    //创建树菜单对象 只能更新、修改起data的长度 data不能设置对象
    var __makeTreeInnerObj = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        var obj = makeDiv(options);
        //更新循环的tree的date
        obj.renewOldTree = function(newData) {
            //console.log('renew.SonData');
            //console.log(newData);
            var sons;
            if(hasData(this['treeLines'])) {//只更新循环部分的tr
                sons = this['treeLines'];
                if(!$.isArray(newData)) newData = [newData];
                var sonData;
                $.each(sons, function (n, son) {
                    sonData = newData[n];
                    if(!sonData) sonData = []; //数据突然为空
                    if(isUndefined(sonData['index'])) sonData['index'] = n;
                    renewObjData(son, sonData);
                })
            }
        };
        //更新tree.data 如果含有带循环的tree 则只更新data的tr；反之更新全部tr
        obj.renewSonLen = function(opt) {
            var demoData = opt['data'];
            //console.log(obj);
            //console.log(demoData);
            //console.log(JSON.stringify(demoData));
            var nowValLen = demoData.length;
            var sons;
            if(hasData(this['treeLines'])) {
                //console.log('hasData _____o');
                sons = this['treeLines'];
                var newData = cloneData(demoData);
                if(!$.isArray(newData)) newData = [newData];
                //如果之前产生过多的儿子而新数量变少要剔除
                var lastValLen = sons.length;
                //console.log('lastValLen:'+ lastValLen);
                //console.log('nowValLen:'+ nowValLen);
                //console.log('nowValLen:'+ nowValLen);
                if(lastValLen > nowValLen) { //多出来 裁掉
                    sons.splice(nowValLen, lastValLen-nowValLen).forEach(function (o) {
                        //console.log('remove _____o');
                        //console.log(o);
                        if(o.name) {
                            delete global[o.name];
                            //console.log('remove_name:'+ o.name);
                        }
                        o.remove();
                    });
                    obj['treeLines'] = sons; //移除son
                    //console.log('remove more td,now:');
                    //console.log(sons);
                    obj[objLastValKey] = obj['treeLines'];
                    //更新data
                    var tmpTreeCheckDataId; //第几行
                    var tmpData;
                    for(tmpTreeCheckDataId = 0; tmpTreeCheckDataId < nowValLen; tmpTreeCheckDataId++) {
                        tmpData =  newData[tmpTreeCheckDataId];
                        tmpData['index'] = tmpTreeCheckDataId;
                        //console.log('tmpTreeCheckDataId:'+ tmpTreeCheckDataId);
                        if(!isUndefined(sons[tmpTreeCheckDataId])) {
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
                        tmpData['index'] = tmpTreeCheckDataId;
                        //console.log('tmpTreeCheckDataId:'+ tmpTreeCheckDataId);
                        //console.log('tmpTreeCheckDataId:'+ tmpTreeCheckDataId);
                        //console.log('tmpData');
                        //console.log(tmpData);
                        if(!isUndefined(sons[tmpTreeCheckDataId])) {
                            //console.log('renew_tmpIndex:'+ tmpIndex);
                            sons[tmpTreeCheckDataId]['data'] = tmpData;
                        } else {
                            //console.log('clone_tmpIndex:'+ tmpIndex);
                            //保留之前的li的value 继续复制一个li 不能从源opt开始克隆，会丢失之后渲染的li.value
                            var newOpt = cloneData(sons[0]['options']);
                            newOpt = getSourceOpt(newOpt);
                            newChecked = sons[0].cloneSelf(newOpt);
                            //console.log('cloneOpt newChecked');
                            //console.log(newChecked);
                            //console.log(tmpData);
                            newChecked[parentObjKey] = this;
                            sons[sons.length-1].after(newChecked);
                            sons[sons.length] = newChecked;
                            //等克隆完tr的属性才能更新data 不然提早渲染的data可能无法再次刷新
                            newChecked['data'] = tmpData;
                        }
                    }
                    this[objLastValKey] = sons;
                } else {
                    //刷新循环的tr
                    obj.renewOldTree(newData);
                }
            }
        };
        return obj;
    };
    //创建树形分类 用于快速管理树形分类
    global.makeTree = global.makeTrees = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        //console.log('makeItems:');
        //console.log(options);
        options = options || {};
        var obj = $('<div></div>');
        obj['last_options'] = getOptVal(options, 'last_options', {});
        obj[objValIsNode] = false;
        obj['createTree'] = false;
        obj['checked_value'] = 0;
        obj['checked_title'] = '';
        obj['multi'] = undefined;
        obj['treeValArray'] = [];
        obj[objValObjKey] = [];//子checked对象
        obj['treeLines'] = [];  //一共有多少个父分类
        var treeOpt = getOptVal(options, ['items'], {});
        var valueKey = getOptVal(treeOpt, ['value_key', 'valueKey'], '');
        var titleKey = getOptVal(treeOpt, ['title_key', 'titleKey', 'text_key', 'textKey'], '');
        var sonDataKey = getOptVal(treeOpt, ['son_key', 'sonKey', 'son_data_key', 'sonDataKey'], null);//data子数据
        var itemVal = getOptVal(treeOpt, ['li'], null);//单元格附加显示内容
        var valueHasSeted = true;//当前对象的value是否渲染完成
        var xuanranTreemenuSuccess = true;//当前对象的menu是否渲染完成
        var parentCheckedsKey = 'parent_checkeds';
        var sonCheckedsKey = 'son_checkeds';
        //单独的格式化value的括号
        obj.formatVal = function (opt) {
            opt = opt || [];
            var sourceVal = opt['source_value'] || opt['value'];
            //每次格式化 优先取格式化前的source value
            var newVal;
            if(strHasKuohao(sourceVal, 'public')) {
                newVal = strObj.formatStr(sourceVal, livingObj['data'], 0, obj, 'value');
                obj[objAttrHasKh] = true;
            } else if(strHasKuohao(sourceVal, 'data')) {
                newVal = strObj.formatStr(sourceVal, opt['data']||{}, 0, obj, 'value');
                obj[objAttrHasKh] = true;
            } else {
                newVal = sourceVal;
            }
            opt['value'] = newVal; //参数要改变 防止外部取出来的仍是括号
            if(strHasKuohao(sourceVal)) {
                if(!optionIsSame(obj, opt, 'value')) {
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
                    obj.lazyCall(obj, opt['data'] || {}, livingObj);
                }
            }
        };

        //外部设置tree选中项
        obj.setTreeVal = function(newVal, notifyOther, exceptObj) {
            //console.log('exceptObj');
            //console.log(exceptObj);
            notifyOther = notifyOther || false;
            if(!Array.isArray(newVal)) newVal = newVal.toString().split(',');
            //console.log('exceptObj2');
            //console.log(exceptObj);
            //console.log("obj[objValObjKey]");
            //console.log(obj[objValObjKey]);
            $.each(obj[objValObjKey], function (i, obj_) {
                if(obj_.checked_value !=='' && strInArray(obj_.checked_value, newVal) !=-1) {
                    obj_.checked = 1;
                } else {
                    obj_.checked = 0;
                }
            });
            if(options['bind'] && notifyOther) {
                if($.inArray(obj, exceptObj) ==-1) {
                    exceptObj.push(obj);
                }
                //console.log('tree.updateBindObj:'+ newVal);
                updateBindObj($.trim(options['bind']), newVal, exceptObj);
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
                return newVal.join(',');
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
        //支持外部取选中的文本 返回数组格式
        Object.defineProperty(obj, 'title', {
            get: function () {
                var newVal = [];
                $.each(obj[objValObjKey], function (i, obj_) {
                    if(obj_.value !=='') newVal.push(obj_.title);
                });
                return ($.isArray(newVal) ? newVal.join(',') : newVal) ;
            }
        });
        //支持外部取子对象列表
        Object.defineProperty(obj, 'sons', {
            get: function () {
                return obj[objValObjKey];
            }
        });

        //移除所有子checked的选中状态
        function __sonRemoveChecked(obj_) {
            if(obj_[sonCheckedsKey]) {
                //console.log('__sonRemoveChecked:');
                $.each(obj_[sonCheckedsKey], function (index, tmpObj) {
                    if(tmpObj.checked==true) tmpObj.checked = false;
                    __sonRemoveChecked(tmpObj);
                });
            }
        }
        //给所有子checked的添加选中状态
        function __sonAddChecked(obj_) {
            if(obj_[sonCheckedsKey]) {
                //console.log('__sonAddChecked:');
                $.each(obj_[sonCheckedsKey], function (index, tmpObj) {
                    if(tmpObj.checked == false) tmpObj.checked = true;
                    __sonAddChecked(tmpObj);
                });
            }
        }
        //给所有父checked的添加选中状态
        function __parAddChecked(obj_) {
            //console.log(obj_[parentCheckedsKey]);
            if(obj_[parentCheckedsKey]) {
                //console.log(tmpObj.checked);
                var tmpParObj = obj_[parentCheckedsKey];
                if(tmpParObj.checked == false) tmpParObj.checked = true;
                __parAddChecked(tmpParObj);
            }
        }
        //克隆多行的可数据循环的tr
        function createRepeatDataTree(optionsData, liOpt_, checkOpt, appendTo, parentObj) {
            //console.log('create.RepeatDataTree');
            //console.log(JSON.stringify(optionsData));
            var tmpLiOpt = cloneData(liOpt_);
            var tmpCheckOpt = cloneData(checkOpt);
            var dataLen = 0; //循环的tr内部数量
            $.each(tmpCheckOpt, function () {
                dataLen ++;
            });
            //创建多个子对象
            function makeTreeSons(_liOpt, _checkOpt, _treeData, dataParentIndex) {
                //new trs
                var liObj;
                //console.log('_liOpt __________:');
                //console.log(_liOpt);
                var copyLiOpt = cloneData(_liOpt);
                var checkOpt,diyClick,
                    sonMenuPbj = null,  //子层data留空
                    checkObj;
                $.each(_treeData, function (n, tmpData) {
                    diyClick = _checkOpt['click'];
                    checkOpt = cloneData(_checkOpt);
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
                        if(options['bind']) {
                            updateBindObj($.trim(options['bind']), obj.value, [obj]);
                        }
                    };

                    checkObj = makeCheck(checkOpt);
                    if(parentObj) {
                        checkObj[parentCheckedsKey] = parentObj;
                        if(parentObj[sonCheckedsKey]) {
                            parentObj[sonCheckedsKey].push(checkObj);
                        } else {
                            parentObj[sonCheckedsKey] = [checkObj];
                        }
                    }
                    if(!isUndefined(tmpData[sonDataKey])) {
                        sonMenuPbj = makeDiv({
                            'class': 'son_menu'
                        });
                        copyLiOpt['value'] = [
                            checkObj,
                            sonMenuPbj
                        ];
                        if(itemVal) {
                            var sonVal = [];
                            if(!$.isArray(itemVal.value)) {
                                itemVal.value = [itemVal.value];
                            }
                            itemVal.value.forEach(function (tmpObj) {
                                var  newSon ;
                                if(isOurObj(tmpObj)) {
                                    //保留之前的li的value 继续复制一个li 不能从源opt开始克隆，会丢失之后渲染的li.value
                                    var newOpt = cloneData(tmpObj['options']);
                                    newOpt = getSourceOpt(newOpt);
                                    newSon = tmpObj.cloneSelf(newOpt);

                                } else {
                                    newSon = tmpObj.clone();
                                }
                                sonVal.push(newSon);
                            });
                            itemVal.value = sonVal;
                            copyLiOpt['value'].push(makeSpan(itemVal));
                        }
                    } else {
                        if(itemVal) {
                            var sonVal = [];
                            if(!$.isArray(itemVal.value)) {
                                itemVal.value = [itemVal.value];
                            }
                            itemVal.value.forEach(function (tmpObj) {
                                var  newSon ;
                                if(isOurObj(tmpObj)) {
                                    //保留之前的li的value 继续复制一个li 不能从源opt开始克隆，会丢失之后渲染的li.value
                                    var newOpt = cloneData(tmpObj['options']);
                                    newOpt = getSourceOpt(newOpt);
                                    newSon = tmpObj.cloneSelf(newOpt);
                                } else {
                                    newSon = tmpObj.clone();
                                }
                                sonVal.push(newSon);
                            });
                            itemVal.value = sonVal;
                            copyLiOpt['value'] = [checkObj, makeSpan(itemVal)];
                        } else {
                            copyLiOpt['value'] = checkObj;
                        }
                    }
                    liObj = makeLi(cloneData(copyLiOpt));
                    liObj[parentObjKey] = obj;//分配父对象
                    obj[objValObjKey].push(checkObj);//累计子对象li
                    _treeData['index'] = n;
                    liObj['data'] = tmpData; //必须克隆完再更新data
                    //console.log('append li :');
                    //console.log(liObj);
                    //console.log('_treeData');
                    //console.log(tmpData);
                    //console.log(tmpData[sonDataKey]);
                    appendTo.append(liObj);
                    if(dataParentIndex==0) obj['treeLines'].push(liObj); //带数据的tr 缓存obj的子对象
                    //子层data渲染
                    if(!isUndefined(tmpData[sonDataKey])) {
                        //console.log('append_son');
                        createRepeatDataTree(tmpData[sonDataKey], liOpt_, _checkOpt, sonMenuPbj, checkObj)
                    }
                });
            }
            //有数组数据才循环
            var cloneIf = false;
            //console.log('optionsData______________________________');
            //console.log(optionsData);
            makeTreeSons(tmpLiOpt, tmpCheckOpt, optionsData, cloneIf, 0);
        }
        obj.extend({
            //主动更新数据
            renew: function(options_) {
                if(isUndefined(options_['value'])) options_['value'] = ''; //强制加value 否则外部无法取
                var sValueStr = !isUndefined(options_['value']) ? options_['value'] : [] ;
                obj['multi'] = getOptVal(options, ['mul', 'multi', 'multity'], undefined); //是否支持多选
                var selectValueArray = sValueStr;
                //如果值是数组 并且多个值 并且未定义是否多选，则默认多选
                if($.isArray(sValueStr) && sValueStr.length>0 && obj['multi'] == undefined) {
                    obj['multi'] = true;
                }
                options_['class'] = classAddSubClass(options_['class'], 'diy_trees', true);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                if(isStrOrNumber(selectValueArray) && strHasKuohao(selectValueArray)) {
                    valueHasSeted = false; //设为未渲染完成
                }

                //只生成一次子对象
                var liDataKey = 'data-value';
                if(valueKey) {//li中输出值
                    treeOpt[liDataKey] = '{'+ valueKey +'}';
                }
                if(titleKey) {//li中输出title值
                    treeOpt['data-title'] = '{'+ titleKey +'}';
                }
                //console.log('treeOpt ______:');
                //console.log(JSON.stringify(treeOpt));
                //console.log('makeList');
                treeOpt['disabled'] = "{{this.disabled}==true || {this.disabled}=='true' || {this.disabled}==1}";
                //console.log('treeOpt');
                //console.log(treeOpt);
                if(getOptVal(treeOpt, ['data_from', "dataFrom"], null)) {
                    xuanranTreemenuSuccess = false;
                }
                //console.log('ulOpt');
                //console.log(obj);
                //console.log(JSON.stringify(ulOpt));
                //console.log(JSON.stringify(options_));
                var liOpt = cloneData(treeOpt);
                var checkOpt = {
                    value: "{"+ valueKey +"}",
                    text: "{"+ titleKey +"}"
                };
                treeOpt['class_extend'] = 'tree_inner';
                var objInner = __makeTreeInnerObj(treeOpt);
                obj.append(objInner);
                //console.log('tree.options_');
                //console.log(JSON.stringify(options_));
                optionDataFrom(objInner, options_);
                delete liOpt['data'];
                delete liOpt['son_key'];
                delete liOpt['sonKey'];
                copyEvens(liOpt, checkOpt);
                removeAllEven(liOpt);
                createRepeatDataTree(treeOpt['data'], liOpt, checkOpt, objInner, null);
                obj['son'] = objInner;
                objInner[parentObjKey] = obj; //设置其父对象
                addOptionNullFunc(obj, options_);//加null_func
                strObj.formatAttr(obj, options_);
                optionGetSet(obj, options_); // format AttrVals 先获取options遍历更新 再设置读写
                obj['last_options'] = cloneData(options_);//设置完所有属性 要更新旧的option
            },
            updates: function(dataName, exceptObj) {//数据同步
                //console.log('uptree.dates______:'+dataName);
                //console.log(exceptObj);
                //console.log(getObjData($.trim(options['bind'])));
                //console.log(getObjData(dataName));
                exceptObj = exceptObj || [];
                if(options['bind'] && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(obj);
                    this.setTreeVal(getObjData($.trim(options['bind'])), false, exceptObj);
                }
                if(obj[objBindAttrsName] && obj[objBindAttrsName][dataName]) { //attrs(如:class) 中含{公式 {dataName} > 2}
                    //console.log('renewObjBindAttr');
                    renewObjBindAttr(this, dataName);
                }
            },
            //克隆当前对象
            cloneSelf: function(optionsGet) {
                optionsGet = optionsGet || cloneData(sourceOptions);
                optionsGet[optionCallCloneKey] = true;//此对象创建时 标记强制克隆其自身和所有子obj
                return makeTree(optionsGet);
            }
        });
        objBindVal(obj, options, [{'key_':'bind', 'val_':'value'}, {'key_':'set_text/setText', 'val_':'text'}]);//数据text绑定
        obj.renew(options);
        addCloneName(obj, options);//支持克隆
        //console.log('item_obj');
        //console.log(obj);
        return obj; //makeTree
    };
    //是否pc
    global.isPc = function(){
        var userAgentInfo = navigator.userAgent;
        var Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");
        var flag = true;
        for (var v = 0; v < Agents.length; v++) {
            if (userAgentInfo.indexOf(Agents[v]) > 0) { flag = false; break; }
        }
        return flag;
    }
    //创建幻灯片
    global.makePPT =function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options['class_extend'] = 'diy_ppt';
        var pptWidth = options['width'] || '200px';
        var pptHeight = options['height'] || '100px';
        var pptType = options['type'] || 'move';//fade
        var pptDirection = options['direction'] || 'left';//left right up down
        var speed = options['speed'] || 500; //移动速度
        var auto = options['auto'] || 0; //自动轮播
        var arrow = options['arrow'] || false; //箭头参数
        var innerClass = 'ppt_inner x_';
        var direcBoth = 'x';
        var currentFadeIndex = 0;//fade特效需要记录当前图片index
        if(pptDirection == 'up' || pptDirection == 'down') direcBoth = 'y';
        if(direcBoth == 'y') innerClass = 'ppt_inner';
        if(!options['li']) return 'no set li';
        pptWidth = parseFloat(pptWidth);
        pptHeight = parseFloat(pptHeight);
        auto = parseFloat(auto);
        speed = parseFloat(speed);
        var moveItemWH = pptWidth;
        var wapLimitWidth = false; //wap限制宽度
        options['li'].forEach(function (li_, i) {
            if(isUndefined(li_['data-ppt-index'])) {
                options['li'][i]['data-ppt-index'] = i;
                if(pptType == 'fade') options['li'][i]['style'] = 'z-index:1;opacity:0;position: absolute;';
            }
        });


        if(direcBoth == 'y') moveItemWH = pptHeight;
        var liOpt = {li: options['li'], 'class': innerClass};
        var list = makeList(liOpt);
        options['value'] = list;
        //鼠标经过事件
        var hoverFunc = function (o, e) {
            var hiddenArrow = o.find('.arrow.hidden');
            if(hiddenArrow.length > 0) hiddenArrow.removeClass('hidden').attr('data-set_hidden', 1);
        };
        var leaveFunc = function (o, e) {
            var hiddenArrow = o.find('.arrow');
            var arrow_;
            $.each(hiddenArrow, function (k, v) {
                arrow_ = $(this);
                if(arrow_.attr('data-set_hidden')  == 1) arrow_.addClass('hidden');
            });
        };
        if(isUndefined(options['hover'])) {
            options['hover'] = hoverFunc;
        } else {
            options['hover_extend'] = hoverFunc;
        }
        if(isUndefined(options['mouseleave'])) {
            options['mouseleave'] = leaveFunc;
        } else {
            options['mouseleave_extend'] = leaveFunc;
        }
        delete options['li'];
        var obj = makeDiv(options);
        var listSons = list[objValObjKey];
        var maxImages = listSons.length;
        var currentDistance = 0;
        var autoPosition = function () {
            //console.log('moveItemWH:'+moveItemWH);
            if(pptType == 'move') {
                //li最后一张放到最前面
                if(pptDirection =='left' || pptDirection =='up') {//向左走 向上走
                    if(listSons.length > 3) {
                        var lastLi = listSons[maxImages-1];
                        listSons[0].before(lastLi);
                        currentDistance = - moveItemWH;
                    }
                } else if(pptDirection =='right' || pptDirection =='down') { //向右走 向下走
                    //li的html反向排序
                    var i,firstLi,lastLi;
                    lastLi = obj.find('li').last();
                    for(i=0; i<maxImages-1; i++) {
                        firstLi = obj.find('li').first();
                        lastLi.after(firstLi);
                    }
                    if(listSons.length > 3) {
                        var lastLi = listSons[maxImages-1];
                        listSons[0].after(lastLi);
                    }
                    currentDistance = - moveItemWH * (maxImages-2);
                }
                if(direcBoth == 'x') {
                    list.attr('style',"margin-left: "+ currentDistance +"px");
                } else {
                    list.attr('style',"margin-top: "+ currentDistance +"px");
                }
            } else if(pptType == 'fade') {
                listSons[0].css({'opacity': 1, 'zIndex': 2});
            }
        };
        //向右移动时 li第一张放到最后面
        function moveLeftLiToRight(newLeft) {
            if(listSons.length > 3) {
                var firstLi = obj.find('li').first();
                var lastLi = obj.find('li').last();
                lastLi.after(firstLi);
            }
            //li切换 父的位置即可归0
            currentDistance = newLeft;
            if(direcBoth == 'x') {
                list.attr('style',"margin-left: "+ currentDistance +"px");
            } else {
                list.attr('style',"margin-top: "+ currentDistance +"px");
            }
        }
        //向左移动时 li最后张放到最前面
        function moveRightLiToLeft(newLeft) {
            if(listSons.length > 3) {
                var firstLi = obj.find('li').first();
                var lastLi = obj.find('li').last();
                firstLi.before(lastLi);
            }
            currentDistance = newLeft;
            //li切换 父的位置即可归0
            if(direcBoth == 'x') {
                list.attr('style',"margin-left: "+ currentDistance +"px");
            } else {
                list.attr('style',"margin-top: "+ currentDistance +"px");
            }
        }
        //获取移动距离
        function getMoveDistance() {
            var moveDistance = moveItemWH;
            if(direcBoth == 'x') {
                if(pptDirection =='left') {
                    moveDistance = moveItemWH;
                } else {
                    moveDistance = moveItemWH * (maxImages-2);
                }
            } else {
                if(pptDirection =='up') {
                    moveDistance = moveItemWH;
                } else {
                    moveDistance = moveItemWH * (maxImages-2);
                }
            }
            return moveDistance;
        }
        function swipeStatus(event, phase, direction, distance) {
            //console.log('event:');
            //console.log(event);
            //console.log('phase:'+ phase);
            //If we are moving before swipe, and we are going L or R in X mode, or U or D in Y mode then drag.
            if (phase == "move" && (direction == "left" || direction == "right" || direction == "up" || direction == "down")) {
                var duration = 0;
                var moveDistance = getMoveDistance();
                //console.log('moveDistance:'+ moveDistance);
                currentDistance = moveDistance;
                if (
                    (direcBoth == 'x' && direction == "left")
                    || (direcBoth == 'y' && direction == "up") ) {
                    dragImg(moveDistance + distance , duration);
                } else if (
                    (direcBoth == 'x' && direction == "right")
                    || (direcBoth == 'y' && direction == "down")
                ) {
                    dragImg(moveDistance - distance, duration);
                }
            } else if (phase == "cancel") {
                scrollImages(- getMoveDistance());
            } else if (phase == "end") {
                if (direction == "right" || direction == "down") {
                    nextImage();
                } else if ( direction == "left"  ||  direction == "up") {
                    previousImage();
                }
            }
        }
        //缓慢移动图片
        function scrollImages(distance, func) {
            //console.log('currentDistance:'+ currentDistance);
            //console.log('distance:'+ distance);
            if(currentDistance == distance) return ;
            //console.log('animate:'+ distance);
            currentDistance = distance;
            if(direcBoth == 'x') {
                list.animate({"margin-left": distance}, speed,  function() {
                    if(func) func();
                });
            } else {
                list.animate({"margin-top": distance}, speed,  function() {
                    if(func) func();
                });
            }
        }
        //图片拖动
        function dragImg(distance) {
            //console.log('dragImg:'+ distance);
            var value = (distance < 0 ? "" : "-") + Math.abs(distance).toString();
            if(direcBoth == 'x') {
                list.css({"margin-left": value + 'px'});
            } else {
                list.css({"margin-top": value + 'px'});
            }
        }

        function previousImage() {
            if(pptType == 'move') {
                var moveDistance = -getMoveDistance() ;
                var scrollDistance = moveDistance - moveItemWH;
                scrollImages(scrollDistance, function () {
                    moveLeftLiToRight(moveDistance);
                });
            } else if(pptType == 'fade') {
                var preImgIndex = currentFadeIndex + 1;
                if(preImgIndex>=maxImages) preImgIndex = 0;//最右边时要跑到最后一张
                console.log(preImgIndex);
                listSons[preImgIndex].css({'opacity': 0, 'zIndex': 2}).animate({'opacity': 1}, speed);
                listSons[currentFadeIndex].animate({'opacity': 0}, speed, function () {
                    $(this).css({'zIndex': 1});
                });
                currentFadeIndex = preImgIndex;
            }
        }
        function nextImage() {
            if(pptType == 'move') {
                var moveDistance = -getMoveDistance();
                var scrollDistance = moveDistance + moveItemWH;
                scrollImages(scrollDistance, function () {
                    moveRightLiToLeft(moveDistance);
                });
            } else if(pptType == 'fade') {
                var nextImgIndex = currentFadeIndex -1;
                if(nextImgIndex<0) nextImgIndex = maxImages-1;//最左边时要跑到最后一张
                listSons[nextImgIndex].css({'opacity': 0, 'zIndex': 2}).animate({'opacity': 1}, speed);
                listSons[currentFadeIndex].animate({'opacity': 0}, speed, function () {
                    $(this).css({'zIndex': 1});
                });
                currentFadeIndex = nextImgIndex;
            }
        }
        //箭头事件
        if(arrow) {
            if(arrow.left) {
                var leftArrowOpt = arrow.left;
                leftArrowOpt['class_extend'] = 'arrow left_arrow';
                leftArrowOpt['type'] = leftArrowOpt['type'] || 'show';
                leftArrowOpt['value'] = !isUndefined(leftArrowOpt['value'])  ? leftArrowOpt['value'] : '&laquo;';
                if(leftArrowOpt['type'] == 'hover') leftArrowOpt['show'] = false;
                leftArrowOpt['click_extend'] = function (obj, e) {
                    if(pptDirection =='right' || pptDirection =='up') nextImage();
                    if(pptDirection =='left' || pptDirection =='down') previousImage();
                };
                var arrowLeft = makeSpan(leftArrowOpt);
                obj.append(arrowLeft);
            }
            if(arrow.right) {
                var rightArrowOpt = arrow.right;
                rightArrowOpt['class_extend'] = 'arrow right_arrow';
                rightArrowOpt['type'] = rightArrowOpt['type'] || 'show';
                rightArrowOpt['value'] = !isUndefined(rightArrowOpt['value'])  ? rightArrowOpt['value'] : '&laquo;';
                if(rightArrowOpt['type'] == 'hover') rightArrowOpt['show'] = false;
                rightArrowOpt['click_extend'] = function (obj, e) {
                    if(pptDirection =='right' || pptDirection =='up') previousImage();
                    if(pptDirection =='left' || pptDirection =='down') nextImage();
                };
                var rightArrowOpt = makeSpan(rightArrowOpt);
                obj.append(rightArrowOpt);
            }
            if(arrow.top) {
                var topArrowOpt = arrow.top;
                topArrowOpt['class_extend'] = 'arrow top_arrow';
                topArrowOpt['type'] = topArrowOpt['type'] || 'show';
                topArrowOpt['value'] = !isUndefined(topArrowOpt['value'])  ? topArrowOpt['value'] : '&laquo;';
                if(topArrowOpt['type'] == 'hover') topArrowOpt['show'] = false;
                topArrowOpt['click_extend'] = function (obj, e) {
                    previousImage();
                };
                var arrowTop = makeSpan(topArrowOpt);
                obj.append(arrowTop);
            }
            if(arrow.bottom) {
                var bottomArrowOpt = arrow.bottom;
                bottomArrowOpt['class_extend'] = 'arrow bottom_arrow';
                bottomArrowOpt['type'] = bottomArrowOpt['type'] || 'show';
                bottomArrowOpt['value'] = !isUndefined(bottomArrowOpt['value'])  ? bottomArrowOpt['value'] : '&laquo;';
                if(bottomArrowOpt['type'] == 'hover') bottomArrowOpt['show'] = false;
                bottomArrowOpt['click_extend'] = function (obj, e) {
                    nextImage();
                };
                var bottomArrowOpt = makeSpan(bottomArrowOpt);
                obj.append(bottomArrowOpt);
            }
        }
        if(pptType == 'move') {
            obj.swipe({
                triggerOnTouchEnd: true,
                triggerOnTouchLeave: true,//鼠标出去则注销
                swipeStatus: swipeStatus,
                allowPageScroll: (direcBoth == 'x' ? 'vertical' : "horizontal"),
                threshold: direcBoth == 'x' ? (moveItemWH/4).toFixed(1) : (pptHeight/3).toFixed(1) //拖拽距离多少则判为翻页 默认75
            });
        }
        if(auto > 0) {
            var timerForAuto = setInterval(function () {
                if(pptType == 'move') {
                    if(direcBoth=='x') {
                        if(pptDirection =='left') {
                            previousImage();
                        } else {
                            nextImage();
                        }
                    } else {
                        if(pptDirection =='up') {
                            previousImage();
                        } else {
                            nextImage();
                        }
                    }
                } else {
                    nextImage();
                }
            }, auto);
        }
        if(!isPc()) {//wap端
            var autoFixPPtSize = function () {
                var winWidth = $(window).outerWidth();
                console.log('winWidth:'+ winWidth);
                console.log('pptWidth:'+ pptWidth);
                pptWidth = winWidth;
                options['width'] = pptWidth + 'px';//压缩最大宽
                wapLimitWidth = pptWidth;
                moveItemWH = pptWidth;
                var img_;
                $.each(obj.find('ul li img'), function () {
                    img_ = $(this);
                    img_.css('width', wapLimitWidth);
                });
                obj.css('width', wapLimitWidth);
            };
            $(window).resize(function () {
                autoFixPPtSize();
                autoPosition(); //初始化图片位置
            });
            autoFixPPtSize();
            autoPosition(); //初始化图片位置
        } else {
            var img_;
            $.each(obj.find('ul li img'), function () {
                img_ = $(this);
                img_.css({'width': pptWidth, 'height': pptHeight});
            });
            obj.css('width', wapLimitWidth);
            autoPosition();
        }
        return obj;
    };
    //创建嵌入的窗口
    global.makeIframe = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        if(!isUndefined(options['url']) && isUndefined(options['src'])) {
            options['src'] = options['url'];
        }
        if(!isUndefined(options['width']) ) {
            var width = options['width'];
            if(width.toString().substr(-1, 1) != '%' &&  width.toString().substr(-2, 2) != 'px') {
                width += 'px';
                options['width'] = width;
            }
        }
        if(!isUndefined(options['height']) ) {
            var height = options['height'];
            if(height.toString().substr(-1, 1) != '%' && height.toString().substr(-2, 2) != 'px') {
                height += 'px';
                options['height'] = height;
            }
        }
        if(!isUndefined(options['border']) && isUndefined(options['frameborder']) ) {
            var hasBorder = options['border'] ? 1:0; //frameborder: 1/0
            options['frameborder'] = hasBorder;
        }
        if(isUndefined(options['frameborder'])) {
            options['frameborder'] = 0;
        }
        if(!isUndefined(options['scroll']) && !isUndefined(options['scrolling']) ) {
            options['scrolling'] = options['scroll'];
        }
        var obj = makeDom({
            'tag': 'iframe',
            'options': options
        });
        if(!isUndefined(options['resize'])) {
            var resizeFunc = options['resize'];
            $(window).resize(function (e) {
                //console.log('resize');
                resizeFunc(obj, e);
            });

        }
        return obj;
    };
    //创建批量上传文件框
    global.batchUploadForm = function(options) {
        options = options || {};
        var defaultOption = {
            'workPath' : '/include/lib/webuploader',
            'url' : '/include/lib/webuploader/upload.php',
            'id': 'diy_batch_uploader',
            'class': 'diy_batch_uploader',
            'post': {},
            auto: true,
            'one_finish': function (data) {
                if(data.id !=='0388') {
                    if(data.info) data.msg += data.info;
                    msg(data.msg);
                } else {
                    hideNewBox();
                    msgHide(data.msg);
                }
            },
            'all_finish': function () {

            },
            'accept': {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png,avi,mp4',
                mimeTypes: 'image/jpg,image/jpeg,image/png'
            }
        };
        options = $.extend({}, defaultOption, options);
        var wrapObj = $('<div></div>'),
            queueList = $('<div class="queue_list"></div>'),
            pickBtn1Id = 'batch_upload_btn_pick_1',
            pickBtn2Id = 'batch_upload_btn_pick_2';
        wrapObj.attr({
            'id': options['id'],
            'class': options['class']
        });
        var placeHolder= $('<div class="placeholder">\
                            <div id="'+ pickBtn1Id +'" class="pick_btn"> </div>\
                            </div>');
        var fileListClassName = isPc() ? 'is_pc' : 'is_wap';
        var fileList= $('<ul class="filelist '+ fileListClassName +'"></ul>');
        // 状态栏，包括进度和控制按钮
        var statusBar = $('<div class="statusBar" style="display:none;">\
                            <div class="progress" style="display: none;">\
                                <span class="text">0%</span>\
                                <span class="percentage" style="width: 0%;"></span>\
                            </div>\
                            <div class="info">共0张（0B），已上传0张</div>\
                            <div class="btnGLr btnGLrSm">\
                                <div id="'+ pickBtn2Id +'" class="btnGLr btnGLrSm pick_btn"></div>\
                                <button class="uploadBtn btnLr btnLrSuccess">开始上传</button>\
                            </div>\
                    </div>');

        // 上传按钮
        var uploadObj = statusBar.find('.uploadBtn'),
            infoObj = statusBar.find('.info'), // 文件总体选择信息
            progressObj = statusBar.find('.progress').hide(),
            fileCount = 0,// 添加的文件数量
            fileSize = 0, // 添加的文件总大小
            // 优化retina, 在retina下这个值是2
            ratio = window.devicePixelRatio || 1,
            // 缩略图大小
            thumbnailWidth = 110 * ratio,
            thumbnailHeight = 110 * ratio,
            // 可能有pedding, success, ready, uploading, confirm, done.
            state = 'pedding',
            // 所有文件的进度信息，key为file id
            percentages = {},
            supportTransition = (function () {
                var s = document.createElement('p').style,
                    r = 'transition' in s ||
                        'WebkitTransition' in s ||
                        'MozTransition' in s ||
                        'msTransition' in s ||
                        'OTransition' in s;
                s = null;
                return r;
            })(),
            uploader;
        // WebUploader实例
        if ( !WebUploader.Uploader.support('flash') && WebUploader.browser.ie ) {
            // flash 安装了但是版本过低。
            if (flashVersion) {
                (function(container) {
                    window['expressinstallcallback'] = function( state ) {
                        switch(state) {
                            case 'Download.Cancelled':
                                alert('您取消了更新！')
                                break;

                            case 'Download.Failed':
                                alert('安装失败')
                                break;

                            default:
                                alert('安装已成功，请刷新！');
                                break;
                        }
                        delete window['expressinstallcallback'];
                    };

                    var swf = './expressInstall.swf';
                    // insert flash object
                    var html = '<object type="application/' +
                        'x-shockwave-flash" data="' +  swf + '" ';

                    if (WebUploader.browser.ie) {
                        html += 'classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ';
                    }

                    html += 'width="100%" height="100%" style="outline:0">'  +
                        '<param name="movie" value="' + swf + '" />' +
                        '<param name="wmode" value="transparent" />' +
                        '<param name="allowscriptaccess" value="always" />' +
                        '</object>';

                    container.html(html);

                })($wrap);
                // 没有安转。
            } else {
                $wrap.html('<a href="http://www.adobe.com/go/getflashplayer" target="_blank" border="0"><img alt="get flash player" src="http://www.adobe.com/macromedia/style_guide/images/160x41_Get_Flash_Player.jpg" /></a>');
            }

            return;
        } else if (!WebUploader.Uploader.support()) {
            alert( 'Web Uploader 不支持您的浏览器！');
            return;
        }
        queueList.append(placeHolder).append(fileList).append(statusBar);
        wrapObj.append(queueList);
        //等待按钮渲染完成，再生成准确的点击层
        setTimeout(function () {
            // 实例化
            uploader = WebUploader.create({
                pick: {
                    id: '#'+pickBtn1Id,
                    'class': 'btnLr btnLrInfo',
                    btnText: '浏览本地图片'
                },
                dnd: queueList,
                paste: document.body,
                accept: options.accept, //允许的格式
                // swf文件路径
                swf: options.workPath + '/js/Uploader.swf',

                disableGlobalDnd: true,
                auto: options.auto, //是否自动上传
                chunked: true,
                // server: 'http://webuploader.duapp.com/server/fileupload.php',
                server: options.url,
                formData: options['post'],
                fileNumLimit: 3000,//最多3000个文件
                resize: false,// 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
                fileSizeLimit: 2000 * 1024 * 1024,    // 200 M
                fileSingleSizeLimit: 100 * 1024 * 1024    // 单张图片限制大小  100M
            });
            // 添加“添加文件”的按钮，
            uploader.addButton({
                id: '#'+pickBtn2Id,
                'class': 'btnLr btnLrDefault',
                btnText: '继续添加'
            });
            // 当有文件添加进来时执行，负责view的创建
            function addFile(file) {
                var liObj = $('<li id="' + file.id + '">' +
                    '<p class="imgWrap"></p>' +
                    '<p class="progress"><span></span></p>' +
                    '</li>'),
                    btns = $('<div class="file-panel">' +
                        '<span class="cancel">删除</span>' +
                        '<span class="rotateRight">向右旋转</span>' +
                        '<span class="rotateLeft">向左旋转</span></div>').appendTo(liObj),
                    prgressObj = liObj.find('p.progress span'),
                    wrapObj = liObj.find('.imgWrap'),
                    infoObj = $('<p class="error"></p>'),
                    text,
                    showError = function (code) {
                        switch (code) {
                            case 'exceed_size':
                                text = '文件大小超出';
                                break;

                            case 'interrupt':
                                text = '上传暂停';
                                break;

                            default:
                                text = '上传失败，请重试';
                                break;
                        }
                        infoObj.text(text).appendTo(liObj);
                    };

                if (file.getStatus() === 'invalid') {
                    showError(file.statusText);
                } else {
                    // @todo lazyload
                    wrapObj.text('预览中');
                    uploader.makeThumb(file, function (error, src) {
                        if (error) {
                            wrapObj.text('不能预览');
                            return;
                        }
                        wrapObj.empty().append($('<img src="' + src + '">'));
                    }, thumbnailWidth, thumbnailHeight);

                    percentages[file.id] = [file.size, 0];
                    file.rotation = 0;
                }
                file.on('statuschange', function (cur, prev) {
                    if (prev === 'progress') {
                        prgressObj.hide().width(0);
                    } else if (prev === 'queued') {
                        liObj.off('mouseenter mouseleave');
                        btns.remove();
                    }

                    // 成功
                    if (cur === 'error' || cur === 'invalid') {
                        showError(file.statusText);
                        percentages[file.id][1] = 1;
                    } else if (cur === 'interrupt') {
                        showError('interrupt');
                    } else if (cur === 'queued') {
                        percentages[file.id][1] = 0;
                    } else if (cur === 'progress') {
                        infoObj.remove();
                        prgressObj.css('display', 'block');
                    } else if (cur === 'complete') {
                        liObj.append('<span class="success"></span>');
                    }

                    liObj.removeClass('state-' + prev).addClass('state-' + cur);
                });
                liObj.on('mouseenter', function () {
                    btns.stop().animate({height: 30});
                });
                liObj.on('mouseleave', function () {
                    btns.stop().animate({height: 0});
                });
                btns.on('click', 'span', function () {
                    var btn = $(this);
                    var className = btn.attr('class'),
                        deg;
                    switch (className) {
                        case 'cancel':
                            uploader.removeFile(file);
                            return;
                        case 'rotateRight':
                            file.rotation += 90;
                            break;

                        case 'rotateLeft':
                            file.rotation -= 90;
                            break;
                    }

                    if (supportTransition) {
                        deg = 'rotate(' + file.rotation + 'deg)';
                        wrapObj.css({
                            '-webkit-transform': deg,
                            '-mos-transform': deg,
                            '-o-transform': deg,
                            'transform': deg
                        });
                    } else {
                        wrapObj.css('filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation=' + (~~((file.rotation / 90) % 4 + 4) % 4) + ')');
                    }
                });
                liObj.appendTo(fileList);
            }
            // 负责view的销毁
            function removeFile(file) {
                var liObj = wrapObj.find('#'+ file.id);
                delete percentages[file.id];
                updateTotalProgress();
                liObj.off().find('.file-panel').off().end().remove();
            }
            //更新图片总数
            function updateTotalProgress() {
                var loaded = 0,
                    total = 0,
                    spans = progressObj.children(),
                    percent;

                $.each(percentages, function (k, v) {
                    total += v[0];
                    loaded += v[0] * v[1];
                });

                percent = total ? loaded / total : 0;

                spans.eq(0).text(Math.round(percent * 100) + '%');
                spans.eq(1).css('width', Math.round(percent * 100) + '%');
                updateStatus();
            }
            function updateStatus() {
                var text = '', stats;
                if (state === 'ready') {
                    text = '选中' + fileCount + '张图片，共' +
                        WebUploader.formatSize(fileSize) + '。';
                } else if (state === 'confirm') {
                    stats = uploader.getStats();
                    if (stats.uploadFailNum) {
                        text = '已成功上传' + stats.successNum + '张照片至XX相册，' +
                            stats.uploadFailNum + '张照片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
                    }

                } else {
                    stats = uploader.getStats();
                    text = '共' + fileCount + '张（' +
                        WebUploader.formatSize(fileSize) +
                        '），已上传' + stats.successNum + '张';

                    if (stats.uploadFailNum) {
                        text += '，失败' + stats.uploadFailNum + '张';
                    }
                }

                infoObj.html(text);
            }
            function setState(val, arg1, arg2) {
                var file, stats;
                if (val === state) {
                    return;
                }

                uploadObj.removeClass('state-' + state);
                uploadObj.addClass('state-' + val);
                state = val;

                switch (state) {
                    case 'pedding':
                        placeHolder.removeClass('element-invisible');
                        fileList.parent().removeClass('filled');
                        fileList.hide();
                        statusBar.addClass('element-invisible');
                        uploader.refresh();
                        break;

                    case 'ready':
                        placeHolder.addClass('element-invisible');
                        wrapObj.find('#'+ pickBtn1Id).removeClass('element-invisible');
                        fileList.parent().addClass('filled');
                        fileList.show();
                        statusBar.removeClass('element-invisible');
                        uploader.refresh();
                        break;

                    case 'uploading':
                        wrapObj.find('#'+ pickBtn2Id).addClass('element-invisible');
                        progressObj.show();
                        uploadObj.text('暂停上传');
                        break;

                    case 'paused':
                        progressObj.show();
                        uploadObj.text('继续上传');
                        break;

                    case 'confirm':
                        progressObj.hide();
                        uploadObj.text('开始上传').addClass('disabled');

                        stats = uploader.getStats();
                        if (stats.successNum && !stats.uploadFailNum) {
                            setState('finish');
                            return;
                        }
                        break;
                    case 'complete'://完成一次上传时提示
                        if(typeof arg2['_raw'] != 'undefined') {
                            var data={};
                            eval("data = "+arg2['_raw']);
                            if(typeof options.one_finish != 'undefined') options.one_finish(data);
                        }
                        break;
                    case 'finish':// 完成全部文件上传时
                        stats = uploader.getStats();
                        if (stats.successNum) {
                            if(typeof options.all_finish != 'undefined') options.all_finish(stats);
                        } else {
                            // 没有成功的图片，重设
                            state = 'done';
                            location.reload();
                        }
                        break;
                }
                updateStatus();
            }
            uploader.onUploadProgress = function (file, percentage) {
                var liObj = wrapObj.find('#'+ file.id),
                    $percent = liObj.find('.progress span');
                $percent.css('width', percentage * 100 + '%');
                percentages[file.id][1] = percentage;
                updateTotalProgress();
            };
            uploader.onFileQueued = function (file) {
                fileCount++;
                fileSize += file.size;

                if (fileCount === 1) {
                    placeHolder.addClass('element-invisible');
                    statusBar.show();
                }
                addFile(file);
                setState('ready');
                updateTotalProgress();
            };
            uploader.onFileDequeued = function (file) {
                fileCount--;
                fileSize -= file.size;
                if (!fileCount) {
                    setState('pedding');
                }
                removeFile(file);
                updateTotalProgress();
            };
            uploader.on('all', function (type, arg1, arg2) {
                var stats;
                switch (type) {
                    case 'uploadSuccess':
                        setState('complete', arg1, arg2);
                        break;
                    case 'uploadFinished':
                        setState('confirm', arg1, arg2);
                        break;

                    case 'startUpload':
                        setState('uploading', arg1, arg2);
                        break;

                    case 'stopUpload':
                        setState('paused', arg1, arg2);
                        break;

                }
            });
            uploader.onError = function (code) {
                alert('Eroor: ' + code);
            };
            uploadObj.on('click', function () {
                if ($(this).hasClass('disabled')) return false;
                if (state === 'ready') {
                    uploader.upload();
                } else if (state === 'paused') {
                    uploader.upload();
                } else if (state === 'uploading') {
                    uploader.stop();
                }
            });
            infoObj.on('click', '.retry', function () {
                uploader.retry();
            });
            infoObj.on('click', '.ignore', function () {
                alert('todo');
            });
            uploadObj.addClass('state-' + state);
            updateTotalProgress();
        }, 300);
        return wrapObj;
    };
    //创建js头
    global.makeScript = function(sourceOptions) {
        var options = cloneData(sourceOptions);
        options = options || {};
        if(!isUndefined(options['url']) && isUndefined(options['src'])) {
            options['src'] = options['url'];
        }
        var charset = isUndefined(options['charset']) ? 'utf-8': options['charset'];
        var jsType = isUndefined(options['type']) ? 'text/javascript': options['type'];
        return $('<script charset="'+ charset +'" type="'+ jsType +'" src="'+ options['src'] +'"></script>');
    };

    //传统表单的自定义打包提交方法
    global.formSubmitEven = function(form, opt) {
        form.on('submit', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var data_ = form.serializeArray();
            var pData = {};
            data_.map(function (v, n) {
                if(!isUndefined(pData[v.name])) {
                    if($.isArray(pData[v.name])) {
                        pData[v.name].push(v.value);
                    } else {
                        pData[v.name] = [pData[v.name], v.value];
                    }
                } else {
                    pData[v.name] = v.value;
                }
            });
            if(!isUndefined(opt['postData'])) {
                opt['postData'].map(function (v, k) {
                    pData[k] = v;
                });
            }
            var newOpt = {
                'postData' : pData
            };
            var onSubmit = !isUndefined(opt['submit']) ? opt['submit'] :  false;
            if(onSubmit) onSubmit();
            newOpt = $.extend({}, newOpt, opt);
            global.postAndDone(newOpt);
        });
    };
    //弹窗的搜索打包
    $.fn.formBoxSearch = function(fn) {
        var form = $(this);
        form.on('submit', function (e) {
            e.preventDefault();
            var postData = $(this).getFormDatas();
            var uri = $(this).serialize();
            fn(uri, postData);
        })
    };
    //direction string 拖拽方向
    //loadFunc function 加载数据
    $.fn.dragToLoadData = function (direction, loadFunc) {
        direction = direction || 'up'; //默认上拖加载,其他 up|down|left|right
        var dragObj = $(this);
        dragObj.on('touchstart', function(e) {
            var touch = e.originalEvent, startX = touch.changedTouches[0].pageX;
            var startY = touch.changedTouches[0].pageY;
            dragObj.on('touchmove', function(e) {
                //e.preventDefault();安卓底下 会引起拖拽失效
                touch = e.originalEvent.touches[0] ||
                    e.originalEvent.changedTouches[0];
                if (touch.pageX - startX > 10) {//右划
                    if(direction == 'right') {
                        loadFunc();
                    }
                } else if (touch.pageX - startX < -10) {//左划
                    if(direction == 'left') {}
                }
                if (touch.pageY - startY > 10) {//下划
                    if(direction == 'down') {
                        var scrollTop = $(document).scrollTop();
                        if(scrollTop == 0) loadFunc();
                    }
                } else if (touch.pageY - startY < -10) {//上划
                    if(direction == 'up') {
                        var scrollTop = $(document).scrollTop();
                        var docHeight = $(document).height();
                        var winHeight = $(window).height();
                        if(scrollTop + winHeight >= docHeight - 1 ) {
                            loadFunc();
                        }
                    }
                }
            });
            //return false;
        }).on('touchend', function() {
            dragObj.off('touchmove');
        });
    };
//此js只能加载一次 不能用于ajax内置模板多次加载
//因为：document绑定事件只绑定一次 多次加载会导致多次绑定
    if(!window.bindDocumentHideMenuEven) {
        $(document).mousedown(function(event) {
            var $target = $(event.target);
            var thisClass = $target.attr('class') ? $target.attr('class') : '';
            if ($target.parents("."+menu_pub_class_name).length === 0
                && thisClass.indexOf(menu_pub_class_name) == -1) {
                //关闭所有的菜单 [目前已有点击关闭的公共菜单：日历、树形菜单]
                $('.'+ menu_pub_class_name).each(function () {
                    var tmpMenu = $(this);
                    var tmpParentInput = tmpMenu.closest('.'+menuZindexClass);
                    if(tmpParentInput) tmpParentInput.removeClass(menuZindexClass);
                    if(tmpMenu.css('display') == 'block') tmpMenu.hide();
                });
            }
            //当前父的邻居的菜单 要隐藏 (日历时用)
            setTimeout(function () {
                //console.log($target);
                //console.log($target.parent().parent().parent());
                //console.log($target.parents('.'+menuZindexClass));
                //console.log($target.closest('.'+menuZindexClass).siblings('.'+menuZindexClass).find("."+menu_pub_class_name));
                if($target.closest('.'+menuZindexClass).length>0
                    && $target.closest('.'+menuZindexClass).siblings('.'+menuZindexClass).find("."+menu_pub_class_name).length > 0 ){
                    var tmpMenu = $target.closest('.'+menuZindexClass).siblings('.'+menuZindexClass).find("."+menu_pub_class_name);
                    var tmpParentInput =tmpMenu.parents('.'+menuZindexClass);
                    if(tmpParentInput) tmpParentInput.removeClass(menuZindexClass);
                    if(tmpMenu.css('display') == 'block') tmpMenu.hide();
                }
            }, 20);
        });
        window.bindDocumentHideMenuEven = true;
    }
    return global;
})(this, jQuery);

