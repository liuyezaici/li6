/*
 * @file        lrEle.js
 * @version     v2021.2.22
 * @description makePage by javascript.
 * @license     MIT License
 * @author      LiRui
 * @createTime  2018-08
 */
"use strict";
define(['jquery', 'lrBox', 'table', 'form', 'list', 'input', 'str','h',
        'items', 'select', 'tree', 'img','radio', 'checked', 'switch',  'bar',  'rili',  'page'
    ],
    function ($, lrBox, table, form, list, input, str, hObj,
              items, select, tree, img, radio, checked, switched, bar, rili, page) {
    // VERSION 20210222
    // LR 2018.8
    //$.url.decode('http:%%%'); 实际以下插件中并没有使用 urldecode 此处嵌入只是方便以后的调取
    $.url = function() { function l(a) { for(var b = "", c = 0, f = 0, d = 0;c < a.length;) { f = a.charCodeAt(c); if(f < 128) { b += String.fromCharCode(f); c++ }else if(f > 191 && f < 224) { d = a.charCodeAt(c + 1); b += String.fromCharCode((f & 31) << 6 | d & 63); c += 2 }else { d = a.charCodeAt(c + 1); var c3 = a.charCodeAt(c + 2); b += String.fromCharCode((f & 15) << 12 | (d & 63) << 6 | c3 & 63); c += 3 } }return b } function m(a, b) { var c = {}, f = {"true":true, "false":false, "null":null}; $.each(a.replace(/\+/g, " ").split("&"), function(d, j) { var e = j.split("="); d = k(e[0]); j = c; var i = 0, g = d.split("]["), h = g.length - 1; if(/\[/.test(g[0]) && /\]$/.test(g[h])) { g[h] = g[h].replace(/\]$/, ""); g = g.shift().split("[").concat(g); h = g.length - 1 }else h = 0; if(e.length === 2) { e = k(e[1]); if(b)e = e && !isNaN(e) ? +e : e === "undefined" ? undefined : f[e] !== undefined ? f[e] : e; if(h)for(;i <= h;i++) { d = g[i] === "" ? j.length : g[i]; j = j[d] = i < h ? j[d] || (g[i + 1] && isNaN(g[i + 1]) ? {} : []) : e }else if($.isArray(c[d]))c[d].push(e); else c[d] = c[d] !== undefined ? [c[d], e] : e }else if(d)c[d] = b ? undefined : "" }); return c } function n(a) { a = a || window.location; var b = ["source", "protocol", "authority", "userInfo", "user", "password", "host", "port", "relative", "path", "directory", "file", "query", "anchor"]; a = /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/.exec(a); for(var c = {}, f = b.length;f--;)c[b[f]] = a[f] || ""; if(c.query)c.params = m(c.query, true); return c } function o(a) { if(a.source)return encodeURI(a.source); var b = []; if(a.protocol)if(a.protocol == "file")b.push("file:///"); else a.protocol == "mailto" ? b.push("mailto:") : b.push(a.protocol + "://"); if(a.authority)b.push(a.authority); else { if(a.userInfo)b.push(a.userInfo + "@"); else if(a.user) { b.push(a.user); a.password && b.push(":" + a.password); b.push("@") }if(a.host) { b.push(a.host); a.port && b.push(":" + a.port) } }if(a.path)b.push(a.path); else { a.directory && b.push(a.directory); a.file && b.push(a.file) }if(a.query)b.push("?" + a.query); else a.params && b.push("?" + $.param(a.params)); a.anchor && b.push("#" + a.anchor); return b.join("") } function p(a) { return encodeURIComponent(a) } function k(a) { a = a || window.location.toString(); return l(unescape(a.replace(/\+/g, " "))) } return{encode:p, decode:k, parse:n, build:o} }();

        // ajax上传文件插件
    $.extend({handleError:function(s,xhr,status,e){if(s.error){s.error.call(s.context||s,xhr,status,e)}if(s.global){(s.context?jQuery(s.context):jQuery.event).trigger("ajaxError",[xhr,s,e])}},createUploadIframe:function(frameId,uri){if(window.ActiveXObject){if(jQuery.browser.version=="9.0"||jQuery.browser.version=="10.0"){var io=document.createElement("iframe");io.id=frameId;io.name=frameId}else{if(jQuery.browser.version=="6.0"||jQuery.browser.version=="7.0"||jQuery.browser.version=="8.0"){var io=document.createElement('<iframe id="'+frameId+'" name="'+frameId+'" />');if(typeof uri=="boolean"){io.src="javascript:false"}else{if(typeof uri=="string"){io.src=uri}}}}}else{var io=document.createElement("iframe");io.id=frameId;io.name=frameId}io.style.position="absolute";io.style.top="-1000px";io.style.left="-1000px";document.body.appendChild(io);return io},ajaxFileUpload:function(s){s=jQuery.extend({},jQuery.ajaxSettings,s);var id=new Date().getTime();var uploadForm={};var tmpLoading=null;var frameId="jUploadFrame"+id;var formId="jUploadForm"+id;var postData=s.data||null;var loadingUrl=s.loadingUrl||"";if(loadingUrl){tmpLoading=$('<img class="loading_gif" src="'+loadingUrl+'">')}uploadForm=$('<form  action="'+s.url+'" target="'+frameId+'" method="POST" '+'name="'+formId+'" style="position: absolute; top: -1000px; left: -1000px;" id="'+formId+'" enctype="multipart/form-data"></form>');if(tmpLoading){s.fileInput.after(tmpLoading)}var inputPrev=s.fileInput.prev();var inputParent=s.fileInput.parent();$(document.body).append(s.fileInput);s.fileInput.wrap(uploadForm);uploadForm=$("#"+formId);if(postData){var tmpInput="";$.each(postData,function(key_,val_){tmpInput=$('<input type="hidden" name="'+key_+'" value="'+val_+'" />');uploadForm.append(tmpInput)})}jQuery.createUploadIframe(frameId,s.secureuri);if(s.global&&!jQuery.active++){jQuery.event.trigger("ajaxStart")}var requestDone=false;var xml={};if(s.global){jQuery.event.trigger("ajaxSend",[xml,s])}var uploadCallback=function(isTimeout){var io=document.getElementById(frameId);try{if(io.contentWindow){xml.responseText=io.contentWindow.document.body?io.contentWindow.document.body.innerHTML:null;xml.responseXML=io.contentWindow.document.XMLDocument?io.contentWindow.document.XMLDocument:io.contentWindow.document}else{if(io.contentDocument){xml.responseText=io.contentDocument.document.body?io.contentDocument.document.body.innerHTML:null;xml.responseXML=io.contentDocument.document.XMLDocument?io.contentDocument.document.XMLDocument:io.contentDocument.document}}}catch(e){jQuery.handleError(s,xml,null,e)}var callFinish=false;if(xml||isTimeout=="timeout"){requestDone=true;var status;try{status=isTimeout!="timeout"?"success":"error";if(status!="error"){var data=jQuery.uploadHttpData(xml,s.dataType);if(s.finish){s.finish(data,status)}else{console.log("!s.finish");console.log(s)}if(s.global){jQuery.event.trigger("ajaxSuccess",[xml,s])}}else{jQuery.handleError(s,xml,status)}}catch(e){status="error";jQuery.handleError(s,xml,status,e)}jQuery(io).unbind();setTimeout(function(){try{$(io).remove();if(tmpLoading){tmpLoading.remove()}if(inputPrev.length>0){inputPrev.after(s.fileInput)}else{inputParent.append(s.fileInput)}$(uploadForm).remove()}catch(e){jQuery.handleError(s,xml,null,e)}},100);xml=null}};if(s.timeout>0){setTimeout(function(){if(!requestDone){uploadCallback("timeout")}},s.timeout)}try{$(uploadForm).submit()}catch(e){jQuery.handleError(s,xml,null,e)}if(window.attachEvent){document.getElementById(frameId).attachEvent("onload",uploadCallback)}else{document.getElementById(frameId).addEventListener("load",uploadCallback,false)}return{abort:function(){}}},uploadHttpData:function(r,type){var data=!type;data=type=="xml"||data?r.responseXML:r.responseText;if(type=="script"){jQuery.globalEval(data)}if(type=="json"){var data=r.responseText;var reg_=/^<pre.*?>(.*?)<\/pre>$/i;if(reg_.test(data)){var am=reg_.exec(data);var data=(am)?am[1]:"";eval("data = "+data)}else{eval("data = "+data)}}if(type=="html"){jQuery("<div>").html(data).evalScripts()}return data}});

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
        str = str.replace(/\:/g, '\\\:');
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


    var global = {};
    //定义是否绑定过文档点击事件
    window.bindDocumentHideMenuEven = false;
    // 不允许append的对象: input,img,textArea,select,radio,switch
    // 允许append的对象: span p div 并且value是字符串
    var evenTags = 'mouseover||mouseenter||hover||hover_extend/mouseout||mouseleave||mouseleave_extend/click/dblclick||dbclick/paste/blur||blur_extend/change||change_extend/keyup/keyup_extend/input propertychange||input propertychange_extend/submit||submit_extend'.split('/');


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


    //哪些参数的修改无需触发对象更新
    var optionsChangeNoRenew = ['name'];
    //缩写原生的判断对象是否存在
    function isUndefined(variable) {return typeof variable == 'undefined' ? true : false;}
    //判断对象是不是插件定义的对象
    function isOurObj(obj_) {
        if(!obj_) return false;
        return obj_.hasOwnProperty('sor_opt');
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
                return url += url.split('?lr_radom')[0] + '?lr_radom='+ makeRandomInt(22);
            }
            if(url.indexOf('&lr_radom') !=-1) {
                return url += url.split('&lr_radom')[0] + '&lr_radom='+ makeRandomInt(22);
            }
            return url += '&lr_radom='+ makeRandomInt(22);
        }
        return url += '?r_='+ makeRandomInt(22);
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

    var objValObjKey = 'obj_val_objs';//当前对象包含的obj  每个人对象创建成功后，其val都会保存当前值或dom对象 字符串形式的value除非
    var objAttrHasKh = 'obj_opt_has_kuohao';//obj的属性包含有{} 则可能绑定全局变量
    var objHasKhAttrs = 'obj_has_kuohao_attrs';//obj的包含有{}的属性
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入
    var objBindAttrsName = 'bind_attrs';

    function addObjAttrToBindVal(obj_, attrName, valueKey) {
        console.log('add ObjAttrToBindVal attrName  :', obj_, attrName +','+ valueKey );
        var ignoreThisAttrName = false;

        if(isUndefined(obj_[objBindAttrsName])) obj_[objBindAttrsName]=  {};
        var objBindData = obj_[objBindAttrsName];
        var lastValAttrs = isUndefined(objBindData[valueKey]) ? [] : objBindData[valueKey];
        if($.inArray(attrName, lastValAttrs) ==-1) {
            lastValAttrs.push(attrName);
            obj_[objBindAttrsName][valueKey] = lastValAttrs;
            console.log('objAddListener  :',obj_, attrName);
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

            //!"" 这样的 算是布尔语法 语法：(0-n)个空格!(0-n)个空格"(0-n)个空格"
            if(/\s*!\s*"\s*"\s*/.test(str)) {
                return 'gth';
            }
            //!1  !0 !A 这样的 算是布尔语法 语法：(0-n)个空格!(0-n)个空格0-9/a-z
            if(/\s*!\s*([0-9]+)\s*/.test(str)) {
                return 'gthZm';
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
            var optData;
            optData = options['data'] || makeNullData();
            var newAttr = {};
            var hidden = false;
            var setChecked = undefined;//设置打勾样式
            var setDisabled = false;//设置不可点击
            var has_kuohao = false; //是否含有括号
            // 如果有括号，并且obj的忽略绑定全局设置为null 则将忽略绑定设置为false;如果没有括号 则忽略绑定设为true
            var class_extend_true_val = '';
            var newOpt = {};
            var evenOption = {};//事件参数
            // if(options['tag'] =='div') {
            //     console.log(thisObj);
            // }
            var tmpStyle = [];
            var onFormatEven = {};
            var thisFormatEven = {};
            thisObj.diyClass = '';//最初定义的class 用于后期渲染class
            thisObj[objHasKhAttrs] = {};
            var tmpHasKhAttr = {};

            $.each(options, function (n, v) {
                class_extend_true_val = '';
                //系统参数 无须解析 当参数解析时 执行回调
                if(n.substr(0, 9) =='onFormat_') {
                    // console.log('add_onformat:', n, v);
                    onFormatEven[n] = v;
                    return;
                }
                //支持字符串中输入{公式} value已经在外部更新 这里只针对属性
                // if(options['tag'] =='div') {
                //     console.log('each.options.n', n, v);
                // }
                if(isStrOrNumber(v) ) {
                    if(strHasKuohao(v)) {
                        tmpHasKhAttr[n] = v;
                        if(n != 'data') has_kuohao = true; //data 带括号不算是属性包含括号 因为下次格式化也不是通过format来实现的 是通过 renew ObjData
                        if(strHasKuohao(v, 'data')) {
                            var abcList = getJhksAbc(v);//没有输入变量，也可以格式化纯运算符
                            var canFmatAbcWhenNoData = false;//无data时是否允许渲染参数
                            // console.log('abcList', n, v, optData);
                            if(!abcList) {
                                canFmatAbcWhenNoData = true;
                            } else {
                                //有this.方法 必须强制渲染
                                $.each(abcList, function (n, v) {
                                    if(v.indexOf('this[') !=-1 || v.indexOf('this.') !=-1) {
                                        canFmatAbcWhenNoData = true;
                                    }
                                });
                                if(hasData(optData)) canFmatAbcWhenNoData = true;
                            }
                            if(n !='value' && n !='src' ) {
                                // console.log('before____formatStr',  hasSetData, hasData(optData), canFmatAbcWhenNoData);
                                if(hasSetData || hasData(optData) ||canFmatAbcWhenNoData) {
                                    v = strObj.formatStr(v, optData, index, thisObj, n);
                                    options[n] = v; //参数要改变 防止外部取出来的仍是括号
                                    if(!isUndefined(options['onFormat_'+n])) {
                                        // console.log('add thisFormatEven',  n, v);
                                        thisFormatEven['onFormat_'+n] = {
                                            func: options['onFormat_'+n],
                                            'val': v,
                                            'data': optData
                                        };
                                    }
                                }
                            }
                        }
                        if(strHasKuohao(v, 'public')) {
                            if(n !='value' && n !='src') {
                                v = strObj.formatStr(v, optData, index, thisObj, n);
                                options[n] = v; //参数要改变 防止外部取出来的仍是括号
                                if(!isUndefined(options['onFormat_'+n])) {
                                    thisFormatEven[options['onFormat_'+n]] = {
                                        func: options['onFormat_'+n],
                                        'val': v,
                                        'data': optData,
                                    };
                                }
                            }
                        }
                    } else {
                        if(n=='class') {
                            thisObj.diyClass = v;
                        }
                    }
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
                        newAttr[n] = v;
                    }
                }

                //hide or show
                if(n =='show' || n =='hidden' || n =='hide') {
                    thisObj.setHidden = true;
                    // console.log('setHidden', v);
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
                    if(v == 'false' || !v || v==0 || v =='0') {
                        setChecked = false;
                    } else {
                        setChecked = true;
                    }
                }
                if(n == 'class_extend') {
                    thisObj.classExt = true;
                    if(v) {
                        thisObj.classExtFlag = true;
                        thisObj['class_extend_true_val'] = v; //缓存扩展样式 下次还可以移除
                    } else {
                        thisObj.classExtFlag = false;
                    }
                }
                //扩展属性不需要显示
                if(canAddAttr(n)) {
                    if(isStrOrNumber(v) || typeof v == 'boolean') {
                        // console.log('add newAttr:', v );
                        if(thisObj.attr && thisObj.attr(n) && v  && thisObj.attr(n) == v  && n != 'class') {
                            return; //不变的属性不用设置
                        }
                        newAttr[n] = v;
                    }
                }
            });
            // console.log('thisFormatEven',  onFormatEven, thisObj);

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
            //之前渲染过class的值
            if(thisObj['class_extend_true_val']) {
                lastClassTrueVal = thisObj['class_extend_true_val'];
            }
            if(thisObj.classExt) {
                if(thisObj.classExtFlag) {
                    newAttr['class'] = classAddSubClass(newAttr['class'], lastClassTrueVal, true, ' ');
                } else  {
                    if(lastClassTrueVal) { //之前有生成过扩展样式 要移除
                        newAttr['class'] = classAddSubClass(newAttr['class'], lastClassTrueVal, false, ' ');
                    } else { //如果配置里没有class 并且扩展里也没有 且现在有样式 则要清空样式
                        if(!thisObj.classExtFlag && !options['class'] && thisObj.attr('class')) {
                            newAttr['class'] = '';
                        }
                    }
                }
                options['class'] = newAttr['class'];
            }
            // console.log('setHidden:', thisObj, thisObj.setHidden);
            if(thisObj.setHidden) {
                if(hidden) {
                    newAttr['class'] = classAddSubClass(newAttr['class'], 'hidden', true, ' ');
                } else {
                    newAttr['class'] = classAddSubClass(newAttr['class'], 'hidden', false, ' ');
                    if(thisObj) thisObj.removeClass('hidden');
                }
                options['class'] = newAttr['class'];
            }
            if(!setDisabled) {
                delProperty(newAttr, ['disabled']);
                if(thisObj) thisObj.removeAttr('disabled');
            }
            if(!isUndefined(setChecked)) {
                if(thisObj) {
                    if(!setChecked) {
                        delProperty(newAttr, ['checked']);
                        thisObj.removeAttr('checked');
                    }
                }
            }
            if(hasData(newAttr) && thisObj.attr ) {//更新属性
                //一样的class不需要重写
                if(newAttr['class'] == thisObj.attr('class')) {
                    delProperty(newAttr, ['class']);
                }
                thisObj.attr(newAttr);
            }
            if(has_kuohao) {
                thisObj[objAttrHasKh] = true;
            }
            thisObj.events = cloneData(evenOption, thisObj.events);
            //更新旧的options
            strObj.addEvents(thisObj);
            thisObj[objHasKhAttrs] = tmpHasKhAttr;
            //格式化value
            var val_ = getOptVal(options,  ['value', 'th', 'td', 'src'], null);
            if(val_ !== null && isStrOrNumber(val_) ) {
                if(thisObj.formatVal) {
                    thisObj.formatVal(options);
                }
            }
            if(hasData(thisFormatEven)) {
                $.each(thisFormatEven, function(field, item_) {
                    item_['func'](thisObj, item_['val'], item_['data']);
                });
            }
            thisObj['onFormatEven'] = onFormatEven;
            // console.log('set____', thisObj, onFormatEven);
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
            // console.log('reFormat_________________');
            // console.log(thisObj);
            // console.log(newOpt);
            var optData = newOpt['data'];
            // console.log('reFormat.KhAttr.data');
            // console.log(optData);
            var attrsHasData = thisObj[objHasKhAttrs] || {};
            // console.log(attrsHasData, 'optData:',optData);
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
            var opt = thisObj['sor_opt'];
            var onFormatEven = thisObj['onFormatEven'] || {};
            var thisFormatEven = {};
            // console.log('onFormatEven:' , thisObj['onFormatEven'], thisObj);

            // console.log('attrsHasData', attrsHasData);
            var index = 0;
            $.each(attrsHasData, function (key_, val_) {
                if(key_ =='value' || key_ =='src') {
                    callRewObjStringVal(thisObj, newOpt);
                    return;//continue
                }
                // console.log('format this1:'+ key_ + ':', val_, optData);
                val_ = strObj.formatStr(val_, optData, index, thisObj, key_); //计算v中的公式 {1+2 > 3}
                // console.log('format this2:'+ key_ + ':', val_);
                // console.log('opt', opt);
                thisObj['options'][key_] = val_;//参数更新 直接可以外部获取道新的值 这里不能修改opt 因为opt是sor源数据
                if(attrIsEven(key_)) {
                    evenOption[key_] = val_;
                }
                if(!isUndefined(onFormatEven['onFormat_'+ key_])) {
                    thisFormatEven['onFormat_'+ key_] = {func: onFormatEven['onFormat_'+ key_],'val': val_, data: optData};
                }
                //console.log('format this:');
                //console.log(n + ':'+ v);
                //style的转译属性：position/width/height/left/top/margin_/padding_
                //都要提进style=''里
                if(isStrOrNumber(val_) ) {
                    var hasGangStr = false;
                    styleHengAttrs.forEach(function (n_) {
                        var reg_ = new RegExp('^'+ n_, "gm");
                        if(key_.match(reg_)) {
                            key_ = key_.replace('_', '-');
                            hasGangStr = true;
                            //console.log('hasGangStr '+ n+':'+ v);
                        }
                    });
                    if(strInArray(key_, cantAddCssAttrs) !=-1 || hasGangStr) {
                        //console.log('push '+ n+':'+ v);
                        tmpStyle.push(key_ +':'+v);
                    }
                    //支持data-n
                    //console.log('add newAttr111 '+ key_ +':'+ v);
                    if(key_ != 'data' && key_.substr(0, 4) == 'data' ) {
                        newAttr[key_] = v;
                    }
                }

                //hide or show
                // console.log(key_ + ' is:', val_);
                if(key_ =='show' || key_ =='hidden' || key_ =='hide') {
                    setHidden = true;
                    if(((key_ =='hide' || key_ =='hidden') && (val_ == 'true' ||  val_ == true ||  val_ == 1)) || (key_ =='show' && (val_ == 'false' || val_== false|| val_== 0))) {
                        // console.log('n=='+ key_ +'::::::v='+ val_);
                        hidden = true;
                    }
                }
                // console.log(key_, 'hidden_________________', hidden);
                //disabled
                if(key_ =='disabled') {
                    if(val_ == 'false' || !val_ || v==0 || val_ =='0') {
                        setDisabled = false;
                    } else {
                        setDisabled = true;
                    }
                }
                if(key_ =='checked') {
                    //console.log('checked:'+ v);
                    if(val_ == 'false' || !val_ || v==0 || val_ =='0') {
                        setChecked = false;
                    } else {
                        setChecked = true;
                    }
                }
                //console.log('attr:'+n);
                if(key_ == 'class_extend') {
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
                // if(key_ =='colspan') {
                //console.log(optData);
                //console.log('n:'+ key_ +',v:'+v);
                //console.log('canAddAttr:'+ canAddAttr('colspan'));
                // }
                //扩展属性不需要显示
                if((isStrOrNumber(val_) || typeof val_ == 'boolean') && canAddAttr(key_)) {
                    if(thisObj.attr && thisObj.attr(key_) && val_ && thisObj.attr(key_) == val_  && key_ != 'class') {
                        return; //不变的属性不用设置
                    }
                    newAttr[key_] = val_;
                }

            });
            // console.log('newAttr::');
            // console.log(thisObj);
            // console.log(newAttr);
            if(hasData(tmpStyle)) {
                if(isUndefined(newAttr['style'])) {
                    newAttr['style'] = '';
                }
                tmpStyle.forEach(function (tmp_) {
                    newAttr['style'] = classAddSubClass(trim(newAttr['style'], ';'), tmp_, true, ';');
                });
                //获取旧的class 当class中不包含{}时 需要继续使用
                var oldStyle = getOptVal(newOpt, 'style', '');
                if(oldStyle && !strHasKuohao(oldStyle)) {
                    newAttr['style'] = classAddSubClass(oldStyle, newAttr['style'], ';');
                }
            }
            var lastClassTrueVal = '';
            if(thisObj['class_extend_true_val']) {
                lastClassTrueVal = thisObj['class_extend_true_val'];
            }
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
            //console.log('hidden:'+ hidden);
            if(!isUndefined(setDisabled)) {
                if(!setDisabled) {
                    delProperty(newAttr, ['disabled']);
                    if(thisObj) thisObj.removeAttr('disabled');
                }
            }
            if(!isUndefined(setChecked)) {
                //console.log('setChecked__________________'+ setChecked);
                if(thisObj) {
                    if(!setChecked) {
                        delProperty(newAttr, ['checked']);
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
            if(hasData(newAttr) && thisObj.attr ) {//更新属性
                //console.log('call_____renew obj_attr:');
                //一样的class不需要重写
                //获取用户自定义的class 如果不包含{}时 需要继续保留
                var oldClass = thisObj.diyClass;
                if(oldClass) {
                    newAttr['class'] = classAddSubClass(oldClass, newAttr['class'], ' ');
                }
                var oldExtendClass = getOptVal(opt, 'class_extend', '');
                if(oldExtendClass) {
                    newAttr['class'] = classAddSubClass(newAttr['class'], oldExtendClass, ' ');

                }
                //如果更新了可见，则之前的hidden要去掉
                if(setHidden===true && hidden == false) {
                    // console.log('replace_____Class:');
                    newAttr['class'] = newAttr['class'].replace(/\s*hidden/ig, '');
                }
                if(newAttr['class'] == thisObj.attr('class')) {
                    delProperty(newAttr, ['class']);
                }
                thisObj.attr(newAttr);
            }
            thisObj.events = cloneData(evenOption, thisObj.events);
            // console.log('renew obj_attr___:');
            this.addEvents(thisObj);
            if(hasData(thisFormatEven)) {
                $.each(thisFormatEven, function(field, item_) {
                    item_['func'](thisObj, item_['val'], item_['data']);
                });
            }
        },

        //字符串转变量
        //str 要替换的字符串
        //objName 要转成的变量名字
        formatStr : function(str, data_, index, obj_, attrName) {
            // console.log('formatStr', str, attrName);
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
            //console.log(obj_ );
            //格式单个变量
            function formatOneDateKey(abc, dataPublic) {
                dataPublic = dataPublic || 'data'; // data的来源 要么继承data 要么public里取
                abc = abc || '';
                // console.log('abc', abc);
                // console.log('data_', data_);
                abc = $.trim(abc);
                var resultStr=   '';
                if(!abc) return abc;
                attrName = attrName || '';
                if(dataPublic == 'data') {
                    var regData3 = /^\!*this\.data/; //!this.data
                    var regData4 = /^\!*this\[data\]/;
                    var matchData3 = abc.match(regData3);
                    var matchData4 = abc.match(regData4);
                    // console.log('matchData3', abc, matchData3);
                    if(matchData3 || matchData4) {
                        resultStr = (abc.match(/(^this\.data)/) ? hasData(data_) : !hasData(data_)) ? 1 : 0;
                    } else {
                        var regData1 = /^this\.data\.([a-zA-Z0-9]+)/;
                        var regData2 = /^this\[data\](\[('|")?([a-zA-Z_\[\]]+[a-zA-Z_\d.]+)('|")?\])*/;
                        var matchData1 = abc.match(regData1);
                        var matchData2 = abc.match(regData2);
                        if(matchData1 || matchData2) {
                            var matchKey = matchData1[1] || matchData2[1];
                            matchKey = strObj.urlDecodeLR(matchKey);
                            //允许获取当前data对象
                            if(matchKey =='length') {
                                resultStr = data_.length;
                            } else if(data_[matchKey]) {
                                resultStr = data_[matchKey];
                            } else if(obj_[matchKey]) { //允许获取 obj.diyAttr
                                resultStr = obj_[matchKey];
                            } else {
                                resultStr = '';
                            }
                        } else {
                            resultStr = abc;
                        }
                    }
                    // console.log('resultStr', resultStr);
                    if(resultStr && isString(resultStr)) {
                        var reg1 = /^\!*this\.([a-zA-Z0-9]+)/;
                        var reg2 = /^\!*this\[\d+\]*(\[('|")?([a-zA-Z_\[\]]+[a-zA-Z_\d.]+)('|")?\])*/;
                        var match1 = resultStr.match(reg1);
                        var match2 = resultStr.match(reg2);
                        // console.log('match1', match1);
                        if(match1 || match2) {
                            var matchKey = '';
                            if(match1) matchKey =  match1[1];
                            if(match2) matchKey =  match2[1];
                            matchKey = strObj.urlDecodeLR(matchKey);
                            // console.log('matchKey', matchKey);
                            //允许获取当前data对象
                            if(matchKey =='data') {
                                resultStr = hasData(data_) ? true : false;
                            } else if(!isUndefined(data_[matchKey])) {
                                var val_ = data_[matchKey];
                                //!开头的要加反转
                                // console.log('resultStr1', resultStr);
                                if(resultStr.match(/^\!/)) {
                                    resultStr = !val_;
                                } else {
                                    resultStr = val_;
                                }
                                // console.log('resultStr2', resultStr);
                            } else if(!isUndefined(obj_[matchKey])) { //允许获取 obj.diyAttr
                                resultStr = obj_[matchKey];
                            } else {
                                resultStr = '';
                            }
                            // console.log('resultStr2', resultStr);
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
                                } else {
                                    resultStr = '';//找不到data[abc]对象 返回为空
                                }
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
                            if(attrName) {
                                addObjAttrToBindVal(obj_, attrName, abc);
                            }
                        }
                        resultStr = ''; //返回自定义的绑定对象字符串 <xxx:aaaa>
                    }
                }
                var newAbc;
                if(isBoolean(resultStr)) {
                    // console.log(' isBoolean  ', resultStr);
                    newAbc = resultStr; //可能是data:{info}提取对象 所以不能转json
                } else if(typeof resultStr == 'object' || typeof resultStr == 'array') { // 对象直接替换当前匹配的data,如：data:{son_data}
                    newAbc = resultStr; //可能是data:{info}提取对象 所以不能转json
                } else {
                    newAbc = abc.replace(abc, resultStr);
                }
                // console.log(' format one '+ abc +' resultStr:'+ newAbc, typeof  newAbc);
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
            // console.log('after getSetStr:'+ str);

            //格式化当前public的变量
            function formatPubJkh(s_) {
                //console.log('formatPubJkh Abc:'+ s_);
                if(!isStrOrNumber(s_))  return s_;
                if(isBoolean(s_)) return s_;
                if(!s_) return s_;
                // console.log('s_:::::::::::::::');
                // console.log(s_);
                if(typeof s_ == 'number') s_ += '';
                //console.log('getStr JHK:  '+s_);
                s_ = formatAbc(s_, 'public');
                // console.log('formatAbc public end___:  '+s_);
                if(isObj(s_)) {
                    return s_;
                }
                //console.log('remendErr.Str11:'+ s_);
                s_ = remendErrStr(s_);
                //console.log('remendErr.Str22:'+ s_);
                var has_Yufa = strObj.hasYufa(s_);
                if(has_Yufa ) {
                    // console.log('has__Yufa:'+ has_Yufa);
                    // console.log('s___1:'+ s_);
                    s_ = strObj.runYufa(s_);
                    //console.log('s___2:'+ s_);
                } else {
                    //console.log(s_+ ' no has__Yufa:'+ has_Yufa);
                }
                return s_;
            }
            //格式化当前data的变量
            function formatDataJkh(s_) {
                // console.log('format DataJkh:'+ s_);
                if(!isStrOrNumber(s_))  return s_;
                if(isBoolean(s_)) return s_;
                if(!s_) return s_;
                if(typeof s_ == 'number') s_ += '';
                s_ = formatAbc(s_, 'data');
                // console.log('after format Abc:', s_);
                //提取语法：
                // item {0 % 2==0 ? 'even': 'odd'}
                // {'a'+'b'}
                var replaceFunc = function (s3) {
                    var matchesFunc = __getjkhFunc(s3);
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
            // console.log('替换完public的变量，结果:'+ str);
            str = formatDataJkh(str); //格式化字符串 数据来源于 data
            // console.log('替换完data的变量，结果:'+ str);
            //替换单个尖括号里的变量
            function replaceMatchAbc(str_, match_, dataPub) { //match_: {abc}
                // console.log('replace MatchAbc str_ :', str_);
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
                    str_ = str_.replace(matchVal, '');
                    return; //continue
                }
                if(!str_) return str_;
                if(isBoolean(str_)) return ;//continue
                matchVal = formatOneDateKey(matchVal, dataPub);//格式 {abc}
                // console.log('change matchVal new___ :', matchVal, typeof matchVal);
                //此结果可能是提取data数组 或 对象 或字符串
                if(isStrOrNumber(matchVal)) {
                    //console.log('str_ :'+ str_);
                    //console.log('matchVal :'+ matchVal);
                    //console.log('type :'+ (typeof matchVal));
                    if(!isNumber(matchVal)) {//非纯数字的结果 要替换(abc) 为 ("abc")
                        str_ = str_.replace(/\{([a-zA-Z_]+[a-zA-Z_\d.]*)\}/g, '<j>$1</j>'); //把{a}替换为<j>a</j> 方便后面解析{内包含<j>
                        matchVal = encodeNewHtml(matchVal);//加密引号\\" " ( )
                        var matchValJhkReg = match_.replace(/^\{(.+)\}$/, '<j>$1<\\/j>');
                        var hasJkhOutReg = '\{([^\<]*)' + matchValJhkReg + '([^\}]*)[\'|\"|\}]'
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

                        // console.log('!isNumber  matchVal :'+matchVal);
                        //console.log('change str_ last22222 :'+str_);
                    } else if(isBoolean(matchVal)) {
                        // console.log('isBoolean');
                        str_ = str_.replace(RegExp(regCodeAddGang(match_), 'g'), 0);
                    } else {
                        //console.log('isNumber :'+ matchVal);
                        str_ = str_.replace(RegExp(regCodeAddGang(match_), 'g'), matchVal);
                    }
                    //console.log('change str_ new :'+str_);
                } else { //abc => obj 那么abc直接等于obj
                    // console.log('match_ obj :', match_, typeof matchVal);
                    if(isBoolean(matchVal)) {
                        // console.log('matchVal is isBoolean ______________', matchVal);
                        str_ = str_.replace(RegExp(regCodeAddGang(match_), 'g'), matchVal?1:0);
                        // console.log('result ______________', str_);
                    } else if(typeof matchVal == 'object') {
                        // console.log('matchVal is object ______________::::');
                        // console.log(str_);
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
                // console.log( 'match is abc to decodeNewHtml-------:', str_);
                str_ = decodeNewHtml(str_);
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
                    //提取子对象 {this[0][abc]}  this.abc.abc 因为引号里的内容可能加了[url]  {!this.data}
                    var tmpMatch = s_.match(/{\!*([a-zA-Z_]+[a-zA-Z_\d.]*)(\[\d+\])*(\[('|")([a-zA-Z_\[\]]+[a-zA-Z_\d.]+)('|")\])*}/g);
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
                // console.log('matches:');
                // console.log(matches);
                //没有获取到{abc} 且没有{"a"+"b"}的语法 才能return
                if(!hasData(matches)) return s_;
                matches = uniqueArray(matches);
                matches.forEach(function (match__) {
                    s_ = replaceMatchAbc(s_, match__, dataPub);
                    // console.log('after replace MatchAbc:', s_, typeof s_);
                });
                return s_;
            }
            // console.log(' end resultStr :'+ str);
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
    //获取属性值 是否需要父参数来实现更新数据
    function getOptNeedParentKey(options_) {
        return getOptVal(options_, ['need_parent_key', 'needParentKey'], '');
    }

    //获取post.data的成功标识
    function getCallData(data_) {
        var successKey = getOptVal(data_, ['successkey', 'success_key', 'successKey'], null);
        var successFunc = getOptVal(data_, ['successfunc', 'success_func', 'successFunc'], null); //成功回调
        var successVal = getOptVal(data_, ['successval', 'success_val', 'success_value', 'successVal', 'successValue'], null); //成功的判断值
        var errFunc = getOptVal(data_, ['failfunc', 'fail_func', 'failFunc', 'errfunc', 'err_func', 'errFunc', 'errorfunc', 'error_func', 'errorFunc'], null);
        if(isNumber(successVal)) successVal +='';
        return {
            'successKey': successKey,
            'successValue': successVal,
            'successFunc': successFunc,
            'errorFunc': errFunc
        };
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
        var thisFormatEven = {};
        var opt = thisObj.sor_opt;
        if(dataLive) data = cloneData(dataLive); //data可能来源于living Obj 所以要重新克隆一个新的data 防止index回传
        if(thisObj.nodeObj && thisObj.nodeObj.length >0) {
            $.each(thisObj.nodeObj, function (n, item) {
                var nodeText = item.text;//textNode原始字符串 如{abc}未编译
                var node = item.obj;
                if(strHasKuohao(nodeText)) {
                    thisObj[objAttrHasKh] = true;
                }
                var newStr = nodeText;
                //首次初始化 要么是带data 要么是需要pubdata
                if(hasSetData || strHasKuohao(nodeText, 'public')) {
                    newStr =  strObj.formatStr(nodeText, data, n, thisObj, 'value');
                    if(!isUndefined(opt['onFormat_value'])) {
                        thisFormatEven  = {func: opt['onFormat_value'], val: newStr, data: data};
                    }
                }
                newStr = htmlDecode(newStr);
                //console.log('htmlDecode :'+ newStr , 'nodeText:'+nodeText);
                //只有文本被修改才更新node的文本 防止没必要的操作dom 带<>标记的内容要在此格式化
                if(nodeText !== newStr) {
                    setTextContent(node, newStr);
                }
            });
            if(hasData(thisFormatEven)) {
                thisFormatEven['func'](thisObj, thisFormatEven['val'], thisFormatEven['data']);
            }
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
    //删除属性
    var delProperty = function (obj, propertys) {
        if(!Array.isArray(propertys)) propertys = [propertys];
        propertys.map(function (v, n) {
            Reflect.deleteProperty(obj, v);
        });
    };

    //格式化data参数
    function optionAddData(opt, optData) {
        var optDataString = getOptVal(opt, 'data', {});
        // if(!hasData(optData)) return [opt, false];//无data返回  //2019.3.16 无data也要返回change
        var dataIsChange = false;//数据是否继承父
        var backData = {};
        // console.log('optDataString', optDataString);
        // console.log('optData', optData);
        if(isStrOrNumber(optDataString)) {
            // console.log('optDataString', optDataString);
            // console.log(optData);
            backData = strObj.formatStr(optDataString, optData);
            if(!backData) {
                console.log('!backData', optDataString, optData, backData);
            }
            if(backData && isString(backData)) {
                backData = JSON.parse(backData);
            }
            // console.log('backData', backData);
            dataIsChange = true;
        } else {
            //console.log(opt['data']);
            //console.log(optData);
            if(hasData(optDataString)) {//has set data
                if(!dataIsSame(optDataString, optData)) {
                    backData = optData;
                    dataIsChange = true;
                }
            }  else {
                backData = optData;
                dataIsChange = true;
            }
        }
        return [backData, dataIsChange];
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
        return sizeIsXs(str) || sizeIsSm(str) || sizeIsMd(str) || sizeIsBg(str) || sizeIsLg(str) ;
    }

    //暴露方法
    global.strObj = strObj;

    //格式化ajax post 带上随机数和默认返回json格式
    function rePost(url, postData, callBack) {
        if(!url) return;
        $.post(url, postData, callBack, 'json');
    }

    //封装post之后的动作
    function postAndDone(options, obj) {
        options = options || {};
        obj = obj || {};
        var callKeys = getCallData(options);
        var successKey = callKeys['successKey'];
        var successVal = callKeys['successValue'];
        var successFunc = callKeys['successFunc'];
        var errFunc = callKeys['errorFunc'];
        if(!$.isArray(successVal)) {
            if(!successVal) successVal = '1';
            if(isStrOrNumber(successVal)) {
                successVal = successVal.split(',');
            } else {
                successVal = successVal.toString().split(',');
            }
        }
        var postUrl = getOptVal(options, ['post_url', 'postUrl', 'url'], null);
        var postData = getOptVal(options, ['post_data', 'postData'], null);
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
    }

    function createRadomName(tag, num) {
        num = num ||'';
        var newName = 'lr_'+ tag +'_'+ makeRandomInt(7) + num;
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
    //找出能添加到对象的属性 ，过滤掉css类型的属性
    function canAddAttr(attrName) {
        var isCssAttr = false;
        styleHengAttrs.forEach(function (n_) {
            var reg_ = new RegExp('^'+ n_, "gm");
            if(attrName.match(reg_)) {
                // if(n_ =='colspan') console.log(attrName);
                isCssAttr = true;
            }
        });
        cantAddCssAttrs.forEach(function (n_) {
            var reg_ = new RegExp('^'+ n_, "gm");
            if(attrName.match(reg_)) {
                // if(n_ =='colspan') console.log(attrName);
                //console.log('hasstr:'+ attrName);
                isCssAttr = true;
            }
        });
        //不在常规的可视化属性里
        var inShowStr = $.inArray(attrName,
            ['value', 'text', 'show', 'value_key', 'valueKey', 'title_key', 'titleKey', 'text_key', 'textKey', 'click', 'data', 'hide',
                'obj_val_is_node','need_parent_key', 'needParentKey', 'success_val', 'success_key', 'tag'
            ]) ==-1;
        //console.log('inShowStr:'+ inShowStr);
        return inShowStr && attrName.indexOf('extend') == -1 && !isCssAttr;
    }
    //call renew val
    function callRewObjStringVal(obj_, options) {
        // console.log('callRewObj.StringVal');
        // console.log(obj_, obj_[objValIsNode],options['value']);
        if(obj_[objValIsNode]) {
            //未渲染
            if(strHasKuohao(options['value'])) {//局部的data变了 才格式化
                formatObjNodesVal(obj_, options['data'], !isUndefined(options['data']));
            } else {
                //console.log('已渲染 node');
                domAppendNode(obj_, options);
            }
        } else {
            if(obj_.formatVal) {
                obj_.formatVal(options);
            }
        }
    }
    //更新对象的属性 （假如属性中包含全局变量{{abc}} ）
    function renewObjBindAttr(obj_, renewBindVal) {
        console.log('goto renewObj BindAttr:');
        //console.log(obj_);
        renewBindVal = renewBindVal || '';
        var objBindData = obj_[objBindAttrsName] || [];
        if(!objBindData || !hasData(objBindData)) return;
        var objBindAttrs = objBindData[renewBindVal] || [];
        if(!objBindAttrs || !hasData(objBindAttrs)) return;
        var newAttr = {};
        var options = $.extend({}, obj_['sor_opt']);
        var optData = options['data'] || makeNullData();
        var v;
        var hidden = false;
        var setHidden = false;//设置隐藏样式
        var setDisabled = false;//设置不可点击样式
        var setChecked = false;//设置打勾样式
        var sourceVal;
        var onFormatEven = obj_['onFormatEven'] || {};
        var thisFormatEven = {};
        objBindAttrs.forEach(function (attrName) {
            sourceVal = options[attrName];
            if(attrName=='value') { //value的 {bind_val} 变化  要其自己更新val
                if(isStrOrNumber(sourceVal) || $.isArray(sourceVal)) { //checkbox是数组
                    callRewObjStringVal(obj_, options);
                }
                return;//continue
            }
            // console.log('sourceVal', sourceVal);
            v = strObj.formatStr(sourceVal, optData, 0, obj_, attrName);
            // console.log('v', v);
            if(!isUndefined(onFormatEven['onFormat_'+ attrName])) {
                thisFormatEven['onFormat_'+ attrName] = {func: onFormatEven['onFormat_'+ attrName],val:v, data: optData};
            }

            //console.log('attrName:'+ attrName + ':'+ v);
            //hide or show
            if(attrName =='show' || attrName =='hide') {
                setHidden = true;
                if((attrName =='hide' && (val_ == 'true' ||  val_ == true))
                    || (attrName =='show' && (val_ == 'false' ||  val_ == false))
                ) hidden = true;
            }
            //disabled
            if(attrName =='disabled') {
                if(val_ == 'false' || !val_ || v==0 || val_ =='0') {
                    setDisabled = false;
                } else {
                    setDisabled = true;
                }
            }
            //checked
            if(attrName =='checked') {
                //console.log('checked:'+ v);
                if(val_ == 'false' || !val_ || val_ ==0 || val_ =='0') {
                    setChecked = false;
                } else {
                    setChecked = true;
                }
            }
            //扩展属性不需要显示
            // console.log('canAddAttr(attrName)', attrName, canAddAttr(attrName));
            if(isStrOrNumber(v) && canAddAttr(attrName)) {
                // console.log('newAttr', v);
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
            delProperty(newAttr, 'disabled');
            if(obj_) obj_.removeAttr('disabled');
        }
        if(!setChecked) {
            delProperty(newAttr, 'checked');
            if(obj_) obj_.removeAttr('checked');
        }
        if(hasData(newAttr)) {
            obj_.attr(newAttr);
        }

        if(hasData(thisFormatEven)) {
            $.each(thisFormatEven, function(field, item_) {
                item_['func'](obj_, item_['val'], item_['data']);
            });
        }
    }
    //鼠标事件枚举
    var allMouseEven = [
        'mouseover','mouseleave','hover','click', 'dblclick', 'keydown', 'mousedown', 'mousemove', 'mouseup'
    ];
    //获取参数中的鼠标事件
    function getMouseEven(opt) {
        opt = opt ||{};
        var backEven = {};
        allMouseEven.map(function (s_, n) {
            if(opt[s_]) {
                backEven[s_] = opt[s_];
            }
        });
        return backEven;
    }

    //属性捆绑读写 参数设置 和更新
    function optionGetSet(thisObj, options, bindDataKey) {
        bindDataKey = bindDataKey || 'value';//绑定全局变量的属性key obj设置了bind 那么全局变量的值会同步更新这个属性
        var setOpts = $.extend({}, options);//用于设置的参数
        if(isUndefined(setOpts['class'])) setOpts['class'] = '';//默认要带上class 否则属性无法被外部修改
        var setBind = getOptVal(options, ['bind'], '');
        if(!thisObj.hasOwnProperty('options')) {
            Object.defineProperty(thisObj, 'options', {
                get: function () {
                    return options;
                }
            });
        }
        //强制绑定data
        if(!thisObj.hasOwnProperty('data')) {
            Object.defineProperty(thisObj, 'data', {
                get: function () {
                    var optData = options['data'];
                    if(isStrOrNumber(optData)) return optData;
                    return cloneData(optData);//data要克隆取， 否则同步修改对象，导致不变
                },
                set: function (newVal) {
                    // console.log('@@@@@@@@@@@@@@@@@call obj to renew data:', thisObj, newVal);
                    renewObjData(thisObj, newVal); //直接更新data 里面已经 更新属性  format AttrVals(thisObj, options);
                    options['data'] = newVal; //无同步更新  不能立即更新data，renew ObjData 还需要对比data
                }
            });
        }
        $.each(setOpts, function (opt_, val_) {
            if(thisObj.hasOwnProperty(opt_)) {
                return;
            }
            if(opt_ == 'value' || opt_=='data') {
                //continue value是不需要默认存取的 全部自己定义
                //data在上面自定义
                return;
            }
            // console.log('setOpts!!!!!!!:', opt_);
            Object.defineProperty(thisObj, opt_, {
                get: function () {
                    // console.log('get opt_:', thisObj, opt_, options[opt_]);
                    //如果当前数值已经绑定 读取公共的数据
                    if(setBind && bindDataKey == opt_ ) {
                        //console.log('get::bindDataKey'+opt_ + ':' + val_);
                        return getObjData($.trim(setBind));
                    }
                    return options[opt_];
                },
                set: function (newVal) {
                    console.log('set opt_:', thisObj, opt_, newVal);
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
        // console.log('option DataFrom:', obj_);
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
                    //如果外部提前定义了强制继承父data 这里就不能重置了
                    if(isUndefined(options_['extendParentData'])) {
                        extendParentData = false;
                    }
                }
            }
        } else if(!isUndefined(options_['data_from']) || !isUndefined(options_['dataFrom'])) {
            extendParentData = false;
        }
        if(isUndefined(options_['extendParentData'])) {
            options_['extendParentData'] = extendParentData;
        }
        var data_ = getOptVal(options_, ['data'], {}); //data
        var dataFrom = getOptVal(options_, ['data_from', 'dataFrom'], null); //data来源
        var pageMenu = getOptVal(options_, ['pageMenu', 'pagemenu', 'page_menu'], null); //menudata来源于url
        var dataFromFunc = getOptVal(dataFrom,'func', null);
        var dataFromUrl = getOptVal(dataFrom,'url', '');
        var dataBeforeDecode = getOptVal(dataFrom, 'dataBefore', null);//数据处理前的解密方法
        var postParameter = getOptVal(dataFrom, ['post_data', 'postData'], {});
        var callKeys = getCallData(dataFrom);
        var successKey = callKeys['successKey'];
        var successValue = callKeys['successValue'];
        var successFunc = callKeys['successFunc'];
        var errFunc = callKeys['errorFunc'];
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
                if(successKey) postData['successKey'] = successKey;
                if(successValue) postData['successValue'] = successValue;
                if(errFunc) postData['errorFunc'] = errFunc;
                postAndDone(postData, obj_);
            }
            //允许外部刷新数据
            obj_.renewData = function (callFunc, page) {
                page = page || 1;
                _renewMyUrlData(callFunc, page);
            };
            var needParentSelect = obj_.INeedParentValFlag || false;
            //不需要取父值参数的情况，可直接请求提交
            if(!needParentSelect) {
                //当子select不依赖于父select的value则可以直接请求url
                _renewMyUrlData(successFunc);
            } else {
                //select.son专用延迟更新菜单的方法 :等待父select渲染menu成功再取值,主动请求此接口更新子select的data
                obj_.getDataWithParentVal = function (newParentVal, func, page) {
                    page = page || null;
                    if(page) postParameter[menuDataPageKey] = page;
                    postParameter[(obj_.INeedParentKey||'id')] = newParentVal;
                    var postData = {
                        post_url: dataFromUrl,
                        post_data: postParameter,
                        successFunc: function (response) {
                            //console.log('response');
                            //console.log(response);
                            __formatDataFunc(response);
                        }, errorFunc: function (response) {
                            if(errFunc) errFunc(response, obj_);
                        }
                    };
                    if(successKey) postData['successKey'] = successKey;
                    if(successValue) postData['successValue'] = successValue;
                    if(errFunc) postData['errorFunc'] = errFunc;
                    postAndDone(postData, obj_);
                };
            }
        }  else if(typeof dataFrom == 'string') { //select专用 子select的data为：'sonData'格式
            // 等待父select确认value时 才能确认select的menu菜单数据
            obj_.getDataFromParentData = function (parentObj, newParentVal, sonObj) {
                // console.log(parentObj.sor_opt, parentObj.menu.menu.data);
                var valueKey =  getOptVal(parentObj.sor_opt, ['value_key', 'valueKey'], '');
                var parentData =  parentObj.menu.menu.data;
                var findData = null;
                // console.log('valueKey', valueKey, 'newParentVal', newParentVal);
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
                // console.log('findData', findData);
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
        // console.log('renew  newData', newData, obj);
        if(!isOurObj(obj)) {
            console.log('not isOurObj');
            return;
        }
        if(isStrOrNumber(newData)) return;//非data
        var options = cloneData(obj['sor_opt']) || {};//必须克隆 否则会修改opt
        //options在此赋值data
        var OptBack = optionAddData(options, newData);
        var backData = OptBack[0];
        options['data'] = backData;
        // console.log('renew ------------ObjData', newData, obj.tag,  obj, backData);
        if(obj[objAttrHasKh]) {
            //这个opt是纯字符串的 所以不会更新bj.option 每次获取值的时候,还是会返回的{abc}
            strObj.reFormatKhAttr(obj, options);
        }
        //data更新 裁剪或增加可循环的子对象的长度
        // console.log('obj.renewSonLen ------------ ', obj, obj.renewSonLen);
        if(obj.renewSonLen) { //如果对象支持对象更新的扩展事件，如makeList/makeTable的裁剪数量，要修改list的长度
            obj.renewSonLen(options);
        } else if(obj.renewSonData) { //更新长度和更新子data是上包含下的
            obj.renewSonData(backData);
        }
        //page专用
        if(obj.renewPageData) {
            //console.log(obj);
            //console.log(newData);
            obj.renewPageData(newData);
        }
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
    function addCloneName(thisObj) {
        thisObj.extend({
            clone:  function() {
                return thisObj.cloneSelf();
            },
            addline:  function(afterFlag) {
                if(isUndefined(afterFlag)) afterFlag = 'before';
                var parentObj = thisObj['parent'];//其父
                var newObj = thisObj.cloneSelf();
                // console.log('thisObj', thisObj);
                // console.log('newObj', newObj);
                newObj['parent'] = parentObj;
                parentObj[objValObjKey].push(newObj);
                if(afterFlag == 'after') {//在后面添加对象
                    thisObj.after(newObj);
                    // console.log('afterFlag', afterFlag);
                } else if(afterFlag == 'before') {//在前面添加对象
                    thisObj.before(newObj);
                }
                return newObj;
            },
            removeObj: function () {
                var thisObj = this;
                if(isOurObj(thisObj['parent'])) {
                    var parentObj = thisObj['parent'];
                    var parentSons = parentObj['value'];
                    if(parentSons.length == 1) {
                        lrBox.msgTisf('atLeaseOne');
                        return;
                    }
                    $.each(parentSons, function (n, obj_) {
                        if(obj_ == thisObj) {
                            parentSons.splice(n, 1);
                        }
                    });
                    thisObj.remove();
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
    //绑定定时器
    function addTimer(thisObj) {
        thisObj.timer = function (opt){
            //time: 1000, //时间间隔 单位毫秒 多久执行一次 或 多少秒后执行
            //  until/while/stop: function () {
            //      return (btn.restNum == 0);
            // },
            //   repeat: true, // 一直循环：true,  自定义次数：1+  0/1：表示执行一次
            // func: function (){ //执行命令
            //      btn.value -= 1;
            //      btn.data  = {'rest': btn.restNum};
            //  }
            var time_ = getOptVal(opt, ['time'], 1000);
            time_ = parseInt(time_);
            var while_ = getOptVal(opt, ['while','until', 'stop', 'stopWhen', 'stopIf'], null);
            var repeat = getOptVal(opt, ['repeat'], false);
            var funcEven = getOptVal(opt, ['func'], false);
            var endEven = getOptVal(opt, ['end'], false);
            var timerId = null;
            if(!funcEven || typeof funcEven !=='function') {
                console.log('func参数缺省');
                return;
            }
            if(repeat) {
                var repeatForTime = false;
                var repeatCount = 0;
                if(isNumber(repeat)) {
                    repeatForTime = true;
                }
                timerId = setInterval(function() {
                    funcEven(thisObj);
                    if(while_ && typeof while_ =='function') {
                        var result = while_(thisObj);
                        if(result === true) {
                            clearInterval(timerId);
                            if(endEven) {
                                endEven(thisObj);
                            }
                        }
                    }
                    if(repeatForTime) {
                        repeatCount ++;
                        if(repeatCount >=repeat) {
                            clearInterval(timerId);
                            if(endEven) {
                                endEven(thisObj);
                            }
                        }
                    }
                }, time_);
            } else {
                timerId = setTimeout(function () {
                    funcEven(thisObj);
                    clearTimeout(timerId);
                }, time_);
            }

        };
    }

    //dom批量绑定事件 mouseenter/mouseleave/click/dblclick/blur/change
    function objSetOptEven(bindToObj, options, callBackObj) {
        options = options || {};
        callBackObj = callBackObj || bindToObj;//回调对象 默认为当前对象，如果设置了特别的父对象，则取父对象
        var evenFuncs = {}, evenNameArray;

        //console.log('objSet.OptEven', bindToObj);
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
                                //console.log(bindToObj);
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
                        //console.log(bindToObj);
                        //console.log(thisFunc.toString());
                        runThisFuncs = function(eve){
                            if(callBackObj && callBackObj.attr('disabled')) {
                                //console.log('has_disabled22');
                                return;
                            }
                            //console.log(options['data']);
                            if(isString(thisFunc)) {
                                return eval(thisFunc);
                            } else {
                                //console.log('on submit:'+ evenName);
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

        if(bindToObj.off) {
            var pasteFunc = false;
            if('paste' in evenFuncs) {
                pasteFunc = evenFuncs['paste'];
                delProperty(evenFuncs, 'paste');
            }
            //console.log('addEvent111:',thisObj, pasteFunc);
            bindToObj.off().on(evenFuncs); //防止二次叠加 所以要先off
            // //paste是特殊的事件，jq无法获取粘贴的图片内容 要转换为原生的绑定语法
            if(pasteFunc && !thisObj.bindPaste) {
                //console.log('addEventListener...',bindToObj);
                bindToObj.bindPaste = true;
                bindToObj[0].addEventListener('paste', pasteFunc);
            }
        }
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
    //克隆data
    function cloneData(newData, oldData) {
        if(!isUndefined(oldData)) {
            return $.extend(getCloneType(newData), oldData, newData);
        } else {
            return $.extend(getCloneType(newData), newData);
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

    //创建随机数字
    function makeRandomInt(len) {
        return str.makeRandomInt(len);
    }

    //创建随机字母-数字
    function makeRandomStr(len) {
        return str.makeRandomStr(len);
    }
    //四舍五入
    function formatFloat(float,dec) {
        return str.formatFloat(float, dec);
    }


    //out func 暴露内部方法
    var outFunc = [
        'sizeIsXs', 'sizeIsSm','sizeIsMd','sizeIsBg','sizeIsLg',
        'isObj',   'getOptVal', 'hasData','cloneData','_onFormatVal','strHasKuohao','formatIfHasKuohao',
        'optionGetSet', 'objBindVal', 'addCloneName', 'optionAddData', 'formatFloat','setSize','copyEvens',
        'isUndefined', 'makeRandomInt','makeRandomStr', 'optionDataFrom','strInArray','toNumber','isNumber',
        'copySourceOpt', 'renewObjData', 'getOptNeedParentKey', 'getKuohaoAbc', 'isStrOrNumber','delProperty',
        'renewObjBindAttr', 'getObjData', 'getCallData', 'classAddSubClass', 'objPushVal','getMouseEven',
        'updateBindObj', 'objIsNull', '_getFormData', 'postAndDone', 'rePost','createRadomName','isOurObj',
        'makeDom', 'makeA', 'makeB', 'makeI', 'makeP', 'makeList', 'makeLi',
        'makeH1', 'makeH2', 'makeH3', 'makeH4', 'makeH5','makeH6',
        'makeTable', 'makeForm', 'makeTr', 'makeTh', 'makeTd',
        'makeDiv', 'makeSpan', 'makeBtn', 'makeImg', 'makeInput', 'makeItems', 'makeSelect',
        'makeTree','makeSwitch', 'makeChecked', 'makeRadio', 'makeBar','makeRili','makePage',
    ];

    outFunc.map(function (v, n) {
        global[v] = function () {
            var res = eval(v).apply(this, Array.prototype.slice.call(arguments,0));
            return res;
        };
    });
    global.livingObj = livingObj;//暴露全局变量

    //判断尺寸 小
    function sizeIsXs (str) {
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
    //判断尺寸 中
    function sizeIsBg(str) {
        return strInArray(str, ['b', 'bg', 'big']) !==-1;
    }
    //判断尺寸 超大
    function sizeIsLg(str) {
        return strInArray(str, ['l', 'lg', 'large']) !==-1;
    }
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
        update_ = isUndefined(update_) ? true : update_;
        if(!global.notifyObj[dataName]) {
            if(!livingObj.hasOwnProperty(dataName)) {
                addKeyToListener(dataName, val);  //数据监听器
            }
            var notifyClass = new notifyer();
            notifyClass['data_name'] = dataName;
            notifyClass.addReceivrs(domObj);
            global.notifyObj[dataName] = notifyClass;
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
            if(!global.notifyObj[dataName].hasReceivr(domObj)) {
                global.notifyObj[dataName].addReceivrs(domObj);
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
        if(global.notifyObj[dataName]) {
            // 作为发布者发出通知
            // console.log('update BindObj  :',dataName, newVal,global.notifyObj[dataName]);
            global.notifyObj[dataName].notify(dataName, exceptObj);
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
                    mousedown: [mouseDown, thisObj, parentLi, parentBox],
                    draging: [movingobj, thisObj, parentLi, parentBox],
                    dragup: [movingEnd, thisObj, parentLi, parentBox]
                });
            }, 200);
        }
    }
    //打包form内的数据为对象
    function _getFormData(obj) {
        var backData = {};
        //保存值到name
        function objSaveVal(tmpName, objVal) {
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
        //获取数组
        function getArrayObjVal(array_) {
            $.each(array_, function (i, item_) {
                if(isOurObj(item_)) {
                    // console.log('1.item_', item_);
                    getObjVal(item_);
                }
            });
        }
        //取单个obj的值
        function getObjVal(obj_) {
            var objVal;
            var objName = obj_['name'];
            objVal = obj_['value'];
            // console.log('getObjVal');
            // console.log(obj_);
            // console.log('objVal');
            // console.log(objVal);
            if($.isArray(objVal))  {//[obj, obj] 或 [1,2,3]
                if(isOurObj(objVal[0])) {
                    // console.log('isOurObj 0: ');
                    $.each(objVal, function (i, item_) {
                        getObjVal(item_);
                    });
                } else {
                    if(objName) {
                        // console.log('get objName: ', objName, objVal);
                        objSaveVal(objName, objVal);
                    }
                }
            } else {
                // console.log('objVal not Array', objVal, typeof objVal);
                // return;
                if(isOurObj(objVal)) {
                    getObjVal(objVal);
                } else {
                    if(objName) {
                        // console.log('save objName: ', objName, obj_, objVal);
                        objSaveVal(objName, objVal);
                    }
                }
            }
        }
        //如果当前对象是自定义对象 直接遍历对象获取 无须用dom
        if(isOurObj(obj)) {
            var formVals = obj[objValObjKey];//form的value -> table/list/ [ [ obj{input},obj{input}+.. ]+ obj{input} + obj{btn} ]
            //如果formVals是对象，直接取值；如果是数组，遍历对象，再取值
            if(!$.isArray(formVals)) formVals = [formVals];
            // console.log('formVals', formVals);
            getArrayObjVal(formVals);
        } else {
            console.log('not our obj', obj);
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
                    getArrayObjVal(arrayItem);
                } else {
                    getObjNullVal(arrayItem);
                }
            });
        }
        //取单个obj的值
        function getObjNullVal(obj_) {
            if(isOurObj(obj_)) { // obj{input/div/p}
                // console.log('obj_', obj_, obj_.value);
                var itsVal = obj_.value;
                var objVal = isUndefined(itsVal) ? '': itsVal;
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
                    if(obj_['null_func'] && !errFunc && !objVal) errFunc = [obj_['null_func'], obj_, obj_['parent']];
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
        _renewNodeVal(optValStr);//更新node
        if(obj_[objBindAttrsName]) { //obj bind attrs(如:class) 中含{{dataName} > 2}
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
        valObj['parent'] = obj_;
        obj_.append(valObj);
        // console.log('PUSH:', obj_, obj_['options']);
        // obj_['options']['obj_val_objs'] = [valObj];
        if(!obj_[objValObjKey] || !hasData(obj_[objValObjKey])) {
            obj_[objValObjKey] = [valObj];
        } else {
            obj_[objValObjKey].push(valObj);
        }

    }

    //普通的obj对象写入obj对象
    function domAppendObj(obj_, opt) {
        var optNewVal =  opt['value'] || opt['son'] || '';
        var optData = opt['data'] || []; //空data的form 在 append table时，table有data 则不能覆盖
        if(!hasData(optNewVal)) {
            return;
        }
        //console.log(obj_);
        if(_valIsAy(optNewVal)) {
            $.each(optNewVal, function (n, valObj) {
                __appendOneOurObj(valObj);
            });
        } else {
            __appendOneOurObj(optNewVal);
        }
        //创建对象的儿子对象
        //如果一开始value是obj格式 突然换个html格式，所以要在这里做格式判断
        function __appendOneOurObj(valObj) {
            if(isUndefined(valObj)) {
                return;
            }
            if(valObj) {
                objPushVal(obj_, valObj);
            }
            //console.log(obj_);
            if(hasData(optData) && (typeof optData == 'array' || typeof optData == 'object'))
            { //有数据传入 哪怕是[] 都要判断之前的对象是否有data 有则对比更新
                var extPar = valObj['extendParentData'];
                if(!isUndefined(extPar) && extPar == true) {
                    console.log('domAppendObj renew ObjData', );
                    renewObjData(valObj, optData);
                }
            }
        }
    }

    //检测val是否真实的obj数组 区分[obj,obj]和checkbox的[obj,obj] 就是有option属性
    function _valIsAy(valObj) {
        return $.isArray(valObj) && !isOurObj(valObj);
    }

    //创建文本dom /a/p/span/div/li/td/em/b/i/
    //sureSource 可以提前确定好配置参数 不用递归取子集
    function makeDom(sourceOptions) {
        var opt = cloneData(sourceOptions);
        opt = opt || {};
        var tag = opt['tag'] || 'span';
        var sureSource = opt['sureSource'] || false;
        var defaultOps = cloneData(opt['options'] || {});//必须克隆
        if(tag =='button') {
            var lrBtnSizeClass = '';
            var newSize = getOptVal(defaultOps, ['size'], null);
            if(sizeIsXs(newSize)) {
                lrBtnSizeClass = 'btnLrXs';
            } else if(sizeIsSm(newSize)) {
                lrBtnSizeClass = 'btnLrSm';
            } else if(sizeIsMd(newSize)) {
                lrBtnSizeClass = 'btnLrMd';
            } else if(sizeIsBg(newSize)) {
                lrBtnSizeClass = 'btnLrBg';
            } else if(sizeIsLg(newSize)) {
                lrBtnSizeClass = 'btnLrLg';
            }
            if(lrBtnSizeClass) {
                defaultOps['class_extend'] = lrBtnSizeClass;
            }
            defaultOps[objValIsNode] = false; // 不允许append val
        }
        defaultOps['tag'] = tag;
        var obj = $('<'+ tag +'></'+ tag +'>');
        if(!obj.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            obj.sor_opt = sureSource ?  cloneData(defaultOps || {}) : cloneData(copySourceOpt(defaultOps));
        }
        // if(tag=='li') {
        //     console.log('makeLi.sor_opt', obj.sor_opt);
        // }
        obj[objValObjKey] = [];
        var setBind = getOptVal(defaultOps, ['bind'], '');
        var onReadyEven = getOptVal(defaultOps, ['onload', 'onLoad', 'ready', 'onReady'], null);
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
        obj.whenUpdate = null; //当被更新时触发事件 比如makeBar的update
        obj.nodeObj = []; //初始化dom节点
        obj.htmObj = [];//初始化element节点

        //value是dom对象的时候的value读写操作
        var dimValObj = function() {
            obj[objValIsNode] = false;
            //如果一开始value是html格式，突然换个obj格式，所以要在这里做格式判断
            if(!obj.hasOwnProperty('value')) {
                Object.defineProperty(obj, 'value', {
                    get: function () {
                        return obj[objValObjKey] || [];
                    }
                    ,set: function (newVal) {
                        __clearSons(obj);
                        if(_valIsAy(newVal)) {
                            $.each(newVal, function (n, valObj) {
                                objPushVal(obj, valObj);
                            });
                        } else {
                            objPushVal(obj, newVal);
                        }
                    }
                });
            }
        };
        //当外部修改obj的val时，直接更新
        //when value is changed by outside
        obj.renewVal = function(newV, opt_) {
            if(isUndefined(opt_)) opt_ = obj['sor_opt'];
            // console.log('renew Val', newV);
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
            if(!hasData(obj[objValObjKey])) {
                return;
            }
            $.each(obj[objValObjKey], function (n, son) {
                if(!son)  return;
                //子对象是否继承父data
                var extPar = son['extendParentData'];
                // console.log('extPar',obj,'son:', son, extPar);
                if(!isUndefined(extPar) && extPar == true) {
                    renewObjData(son, newData);
                }
            })
        };
        //append方法扩展
        obj.appendObj = function(newObj) {
            obj.renewVal(newObj, obj['sor_opt']);
        };
        obj.domAppendVal = function(opt, hasSetData) {
            var obj_ = this;
            opt= opt || [];
            if(!hasData(opt)) return '';
            tag = tag || 'span';
            var setExtData = false;//子对象是否继承 在tr有data的情况下 td强制设为继承
            if(!isUndefined(opt['extendParentData'])) {
                setExtData = true;
            }
            var data_ = getOptVal(opt, ['data'], {});
            // console.log('domAppendVal_______', tag, obj,  opt['value'], data_);
            //这里生成的dom都是提前获取好sor_opt的
            var createDom = function (newOpt, sonData) {
                newOpt = newOpt || {};
                // console.log('create Dom************_', newOpt);
                var renew_data = false;
                if(newOpt['tag']) {
                    //格式化data参数
                    if(hasData(data_)) {
                        //这里不能提前设置data 因为可能是克隆来的opt 需要保留原来的sorOpt
                        renew_data = true;
                    }
                    if(setExtData) {// 延续继承data
                        newOpt['extendParentData'] = true;
                    }
                    if(newOpt['tag']=='checked') {
                        valObj = require('checked').makeCheck(newOpt, true);
                    } else if(newOpt['tag']=='radio') {
                        valObj = require('radio').makeRadio(newOpt, true);
                    } else if(newOpt['tag']=='switch') {
                        valObj = require('switch').makeSwitch(newOpt, true);
                    } else if(newOpt['tag']=='input') {
                        valObj = require('input').makeInput(newOpt, true);
                    } else if(newOpt['tag']=='img') {
                        valObj = require('img').makeImg(newOpt, true);
                    }  else if(newOpt['tag']=='list') {
                        valObj = require('list').makeList(newOpt, true);
                    } else {
                        // console.log('newOpt', newOpt['data'], valObj);
                        valObj = makeDom({
                            'tag': newOpt['tag'],
                            'options': newOpt,
                            'sureSource': true,
                        });

                    }
                    objPushVal(obj_, valObj);
                    // console.log('create success _ _ _', valObj);
                    //生产完再更新data 不会污染sor_opt.data
                    // if(renew_data) {
                    //     if(!isUndefined(newOpt['data']) && isString(newOpt['data'])) {
                    //         var dataBack = optionAddData(newOpt, data_);
                    //         var newData = dataBack[0];
                    //         renewObjData(valObj, newData);
                    //     } else {
                    //         renewObjData(valObj, data_);
                    //     }
                    // }
                    obj_.append(valObj);
                }
            };
            //console.log(obj_);
            if(tag == 'tr') {
                var optNewVal = opt['td'] || opt['th'] || {}; //tr的value参数 只能是td th
                // console.log('tr___AppendVal,data', data_, 'optNewVal', optNewVal);
                //所以这里的克隆属性要在maketr时判断是否克隆的TD，如果是，则tr无须再克隆，并且注销这个TD的克隆属性
                var newVal = tdToObj(data_, optNewVal, setExtData, (isUndefined(opt['td']) ? 'th':'td') );//创建新的[TD]
                // console.log('tr newVal_______|||||||||||||||||||||||||||||| :', obj_, newVal);
                newVal.forEach(function(td_) {
                    objPushVal(obj_, td_);
                });
                dimValObj();
            } else if(tag == 'list') {
                console.log('list__AppendVal---------,opt', opt['data']);
                var newVal = makeList(opt);
                console.log('list__AppendVal++++++++++,newVal', newVal);
                // objPushVal(obj_, newVal);
                objPushVal(obj_, newVal);
                if(hasData(data_)) {
                    // console.log('renew list.Data+++++++++++++' , data_);
                    // renewObjData(newVal, data_);
                }
                dimValObj();
            } else if(tag == 'td' || tag == 'li') { // td /li的data是外层被动更新的 不能在此更新
                var valObj = isUndefined(opt['value']) ? '': opt['value'];
                if(isOurObj(valObj)) {
                    dimValObj();
                    objPushVal(obj_, valObj);
                } else if($.isArray(valObj)) {
                    dimValObj();
                    // console.log('isArray +++++++++++++', valObj , data_);
                    $.each(valObj, function (n, val_){
                        if(!val_) return;
                        if(val_ instanceof $) { //jq对象 也是ourObj
                            // console.log('array.jq对象 ', val_);
                            objPushVal(obj_, val_);
                            //li和td 的data由list创建后更新data
                        } else {
                            createDom(val_);
                        }
                    });
                } else if(isObj(valObj)) {
                    // console.log('isObj+++++++++++++', valObj);
                    dimValObj();
                    if(valObj instanceof $) {
                        obj_.append(valObj);
                    } else {
                        // console.log('createDom +++++++++++++', valObj);
                        createDom(valObj);
                    }
                } else if(isStrOrNumber(valObj)) {//td的value可以是字符串
                    // console.log('AppendVal+++++++++++++', valObj);
                    obj[objValIsNode] = true;
                    domAppendNode(obj_, opt, hasSetData);
                }
            } else {
                if(isUndefined(opt['value'])) opt['value'] = ' ';//必须输入空文本只能执行node替换
                var optValStr = opt['value'];
                // console.log('AppendVal value is obj+++++++++++++', optValStr);
                if(isStrOrNumber(optValStr) ) {
                    obj[objValIsNode] = true;
                    domAppendNode(obj_, opt, hasSetData);
                } else if($.isArray(optValStr) ) {
                    dimValObj();
                    // console.log('isArray', optValStr);
                    $.each(optValStr, function (i_, val_) {
                        if(!val_) return;
                        if(val_ instanceof $ || isOurObj(val_)) {
                            // console.log('obj PushVal+++++++++++++', obj_, val_);
                            objPushVal(obj_, val_);

                        } else {
                            // console.log('create Dom ++++++++++++',  val_);
                            createDom(val_);
                        }
                    });
                } else {  //value is obj
                    // console.log('goto obj+++++++++++++', optValStr);
                    if(isOurObj(optValStr)) {
                        dimValObj();
                        domAppendObj(obj_, opt);
                    } else {
                        createDom(optValStr);
                    }
                }
            }
        };
        //外部设置val
        obj.extend({
            //主动更新数据
            renew: function(optionsGet) {
                optionsGet = optionsGet || {};
                // console.log('renew _________:', optionsGet['data'], this);
                var hasSetData = !isUndefined(optionsGet['data']);
                // if(tag == 'li') {
                //     console.log(' li:', optionsGet['data'], hasSetData);
                // }
                obj.domAppendVal(optionsGet, hasSetData);
                optionDataFrom(obj, optionsGet);
                //参数读写绑定 参数可能被外部重置 所以要同步更新参数
                //先设定options参数 下面才可以修改options
                strObj.formatAttr(obj, optionsGet, 0, hasSetData);
            },
            //克隆当前对象 name要重新生成
            cloneSelf: function() {
                var opt = cloneData(obj.sor_opt);
                opt['name'] = createRadomName(opt['tag']);
                // console.log('clone__self：', tag, opt.value, opt.value.data);
                return makeDom({
                    'tag': tag,
                    'options': opt,
                });
            },
            updates: function(dataName, exceptObj) {//数据被动同步
                //console.log('updates this');
                exceptObj = exceptObj || [];
                if(setBind && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(obj);
                    //console.log('updateNodeText this');
                    updateNodeText(this,$.trim(setBind), exceptObj);
                    if(this.whenUpdate) {
                        this.whenUpdate(this, getObjData($.trim(setBind)));
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
        // console.log('afterRenew', JSON.stringify(defaultOps));
        objBindVal(obj, defaultOps);//数据绑定
        addCloneName(obj);//支持克隆
        addTimer(obj);//添加定时器绑定
        //绑定拖拽事件
        callBindDragObj(obj, defaultOps);
        if(onReadyEven) {
            onReadyEven(obj);
        }
        return obj;
    }

    function makeA(options) {
        return makeDom({tag: 'a', 'options':options});
    }
    function makeB(options) {
        return makeDom({tag: 'b', 'options':options});
    }
    function makeI(options) {
        return makeDom({tag: 'i', 'options':options});
    }
    function makeLi(options) {
        return makeDom({tag: 'li', 'options':options});
    }
    function makeSpan(options) {
        return makeDom({tag: 'span', 'options':options});
    }
    function makeP(options) {
        return makeDom({tag: 'p', 'options':options});
    }
    function makeDiv(options) {
        return makeDom({tag: 'div', 'options':options});
    }
    function makeTable(options) {
        return table.makeTable(options);
    }
    function makeForm(options) {
        return form.makeForm(options);
    }
    function makeList(options) {
        return list.makeList(options);
    }
    function makeImg(options) {
        return img.makeImg(options);
    }
    function makeInput(options) {
        return input.makeInput(options);
    }
    function makeItems(options) {
        return items.makeItems(options);
    }
    function makeSelect(options) {
        return select.makeSelect(options);
    }
    function makeRadio(options) {
        return radio.makeRadio(options);
    }
    function makeChecked(options) {
        return checked.makeChecked(options);
    }
    function makeSwitch(options) {
        return switched.makeSwitch(options);
    }
    function makeTd(options) {
        return makeDom({tag: 'td', 'options':options});
    }
    function makeTh(options) {
        return makeDom({tag: 'th', 'options':options});
    }
    function makeTr(options) {
        return makeDom({tag: 'tr', 'options':options});
    }
    function makeH1(options) {
        return hObj.makeH1(options);
    }
    function makeH2(options) {
        return hObj.makeH2(options);
    }
    function makeH3(options) {
        return hObj.makeH3(options);
    }
    function makeH4(options) {
        return hObj.makeH4(options);
    }
    function makeH5(options) {
        return hObj.makeH5(options);
    }
    function makeH6(options) {
        return hObj.makeH6(options);
    }
    function makeTree(options) {
        return tree.makeTree(options);
    }
    function makeBtn(options) {
        return makeDom({tag: 'button', 'options':options});
    }
    function makeBar(options) {
        return bar.makeBar(options);
    }
    function makeRili(options) {
        return rili.makeRili(options);
    }
    function makePage(options) {
        return page.makePage(options);
    }

    //格式化 {td: []} 为 {value: makeTd}
    function tdToObj(trData, TdOpts, setExtData, tdKey) {
        //这里需要继承克隆 因为tr一旦是克隆的，makeTR时会继续克隆这个TD.

        var trVal = [], newTd;
        // console.log('makeTD__before,data:',  TdOpts);
        if (!$.isArray(TdOpts)) {
            TdOpts = [TdOpts];
        }
        TdOpts.forEach(function (opt_) {
            //opt_ 的value可能是提前渲染好的span数组
            var optVal = opt_['value'] || [];
            //强制转数组
            if(!$.isArray(optVal)) {
                //  opt_['value'] = [optVal];
            }
            // console.log('forEach___val', opt_['value']);
            if(setExtData) opt_['extendParentData'] = true;//tr是克隆来的话，会继承data  td必须也要继续
            newTd = makeTD_(opt_);
            if(hasData(trData)) newTd['data'] = trData;
            trVal.push(newTd);
        });
        //创建单个TD
        function makeTD_(opt) {
            var newTd = tdKey =='td' ? makeTd(opt) : makeTh(opt);
            return newTd;
        }
        return trVal;
    }
    //清空子对象
    function __clearSons(obj_) {
        if(obj_[objValObjKey]) {
            obj_[objValObjKey].forEach(function (o) {
                o.remove();
            });
        }
        obj_[objValObjKey] = [];
    }
    //拷贝源配置文件 主要是value为对象的要获取原参数
    function copySourceOpt(opt) {
        // console.log('copySourceOpt_______', opt);
        var getArray = function (array_) {
            var arrayBack = [];
            $.each(array_, function (index_, val2_) {
                if(isOurObj(val2_)) {
                    //如果是提前创建好的
                    // console.log('isOurObj', val2_,  val2_.sor_opt, 'data:', val2_['data']);
                    arrayBack.push(val2_.sor_opt);
                    // console.log('isOurObj_sour', arrayBack);
                } else if($.isArray(val2_)) {
                    arrayBack.push(getArray(val2_));
                }  else if(isObj(val2_)) {
                    if(val2_ instanceof $) {
                        arrayBack = val2_;
                    } else {
                        arrayBack.push(checkAll(val2_));
                    }

                }
            });
            // console.log('objBack', array_, arrayBack);
            return arrayBack;
        };
        var checkAll = function (opt_) {
            // console.log('checkAll', opt_);
            var backData = {};
            $.each(opt_, function (k, val_) {
                if(k=='data') {
                    backData[k] = val_;
                    return;//continue;
                }
                // console.log('k__',k, val_);
                if($.isArray(val_)) {
                    // console.log('isArray', val_, val_[0]);
                    if(isStrOrNumber(val_[0])) {
                        backData[k] = val_;
                    } else {
                        backData[k] = getArray(val_);
                    }
                    // td: [{},{}]
                } else if(isOurObj(val_)) {
                    console.log('isOurObj', val_, val_.sor_opt);
                    backData[k] = val_.sor_opt;
                }  else if(isObj(val_)) {
                    if(val_ instanceof $) {
                        backData[k] = val_;
                    } else {
                        // console.log('isObj', val_);
                        backData[k] = checkAll(val_);
                    }
                } else {
                    backData[k] = val_;
                }
            });
            return backData;
        };
        return checkAll(opt);
    }

    //格式化val 方法
    function _onFormatVal(obj, data_, sourceVal, valKey)  {
        valKey = valKey || 'value';
        var newVal = '';
        var opt = obj['sor_opt'];
        var thisFormatEven = {};
        //每次格式化 优先取格式化前的source value
        if (strHasKuohao(sourceVal)) {
            var formatData = {};
            if (strHasKuohao(sourceVal, 'public')) {
                formatData =  livingObj['data'];
                newVal = strObj.formatStr(sourceVal, formatData, 0, obj, valKey);
            } else {
                formatData =  data_;
                newVal = strObj.formatStr(sourceVal, data_, 0, obj, valKey);
            }
            if(hasData(formatData)) {
                if(!isUndefined(opt['onFormat_'+ valKey])) {
                    thisFormatEven = {func: opt['onFormat_'+ valKey],val: newVal, data: formatData};
                }
            }
            obj[objAttrHasKh] = true;
        }  else {
            newVal = sourceVal;
        }

        if(hasData(thisFormatEven)) {
            thisFormatEven['func'](obj, thisFormatEven['val'], thisFormatEven['data']);
        }
        return newVal;
    }

    //是否pc
    global.isPc = function(){
        var userAgentInfo = navigator.userAgent;
        var Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");
        var flag = true;
        for (var v = 0; v < Agents.length; v++) {
            if (userAgentInfo.indexOf(Agents[v]) > 0) { flag = false; break; }
        }
        return flag;
    };


    //传统表单的自定义打包提交方法
    global.formSubmitEven = function(form, opt) {
        var beforeFunc = getOptVal(opt, ['before'], null);
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
            if(beforeFunc) {
                var status = beforeFunc(pData);
                if(status===false) {
                    return;
                }
            }
            if(!isUndefined(opt['postData'])) {
                opt['postData'].map(function (v, k) {
                    pData[k] = v;
                });
            }
            var newOpt = {
                'postData' : pData
            };
            var onSubmit = !isUndefined(opt['submit']) ? opt['submit'] :  false;
            if(onSubmit) onSubmit(form);
            newOpt = $.extend({}, newOpt, opt);
            postAndDone(newOpt);
        });
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
    return global;
});

