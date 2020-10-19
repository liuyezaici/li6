//lr 编辑器 2017-03-03 17:00 核心借鉴于xheditor
function lrEditor(appendWhere,numTagObj, maxWordsNum, keyUpFunc, postBtn) {
    //
    // appendWhere 插入编辑器的地方
    // numTagObj 显示输入字数的tag对象
    //maxWordsNum 最多可以输入的字数限制
    //keyUpFunc 输正在入时 执行函数
    //实例一个对象 var editor = lrEditor($('#num_box'), 500);
    if(!appendWhere) return false;
    appendWhere = appendWhere || null;
    numTagObj = numTagObj || null;
    maxWordsNum = maxWordsNum || 0;
    keyUpFunc = keyUpFunc || '';
    postBtn = postBtn || '';
    // 配置
    var enterIframeId = 'lr_editor_'+(parseInt(Math.random() * 100000));
    var _jText = $('<iframe id="'+ enterIframeId +'" class="lr_editor" src="javascript:;" frameborder="0"></iframe>');
    appendWhere.append(_jText);//想要获取iframe的window对象 必须先在doc对象中呈现
    var agent = navigator.userAgent.toLowerCase();
    var bMobile = /mobile/i.test(agent),
        browser = $.browser,browerVer=parseFloat(browser.version),
        isIE=browser.msie,
        isIE11 = /trident\//i.test(agent) && (/rv:/i.test(agent) || /Netscape/i.test(agent.appName)),
        isMozilla = browser.mozilla,
        isWebkit = browser.webkit,
        isOpera = browser.opera,
        isChrome = browser.chrome,
        bAir = agent.indexOf(' adobeair/')>-1,
        bCleanPaste=false;
    var settings  = {localUrlTest:/^https?:\/\/[^\/]*?(s\.com)\//i,remoteImgSaveUrl: '/include/lib/webchat/save_remote_img.php',clean_paste: 1, urlType: 'rel', urlBase: '/'};
    var urlType=settings.urlType;
    var urlBase=settings.urlBase;
    var arrEntities={'<':'&lt;','>':'&gt;','"':'&quot;','®':'&reg;','©':'&copy;'};//实体
    var regEntities=/[<>"®©]/g;
    var _this=this,_win,_jWin,_doc,_jDoc,_Body;
     var iframeHTML = '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml"><head> ';
    iframeHTML+='<style type="text/css"> html{height:100%;background-color:#fff}body,td,th{font-family:Arial,Helvetica,sans-serif;font-size:12px;}' +
        'body{height:100%;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;overflow-wrap:break-word;word-wrap:break-word;margin:0;padding:2px 5px;overflow-y:auto;line-height:20px}' +
        '.xhe-border td,.xhe-border th{border:1px dotted #d3d3d3}body img:-moz-broken{-moz-force-broken-image-icon:1;height:24px;width:24px}</style>';
    iframeHTML+='</head><body spellcheck="0"></body></html>';
    _win=$('#'+enterIframeId)[0].contentWindow;//只能重新获取
    _jWin=$(_win);
    try{
        this.doc=_doc = _win.document;_jDoc=$(_doc);
        _doc.open();
        _doc.write(iframeHTML);
        _doc.close();
        if(isIE)_doc.body.contentEditable='true';
        else _doc.designMode = 'On';
    }catch(e){}
    setTimeout(setOpts,300);
    var _Body = $(_doc.body);
    _jText.focus(_this.focus);
    _jWin.focus(function(){if(settings.focus)settings.focus();}).blur(function(){if(settings.blur)settings.blur();});
    if(isWebkit){
        _jWin.click(fixAppleSel);
    }
    function setCSS(css)
    {
        try{_this._exec('styleWithCSS',css,true);}
        catch(e)
        {try{_this._exec('useCSS',!css,true);}catch(e){}}
    }
    function setOpts()
    {
        setCSS(false);
        try{_this._exec('enableObjectResizing',true,true);}catch(e){}
        //try{_this._exec('enableInlineTableEditing',false,true);}catch(e){}
        if(isIE)try{_this._exec('BackgroundImageCache',true,true);}catch(e){}
    }
    function fixAppleSel(e) {
        e=e.target;
        if(e.tagName.match(/(img|embed)/i))
        {
            var sel=_this.getSel(),rng=_this.getRng(true);
            rng.selectNode(e);
            sel.removeAllRanges();
            sel.addRange(rng);
        }
    }
    //基本控件名
    if(isIE)
    {
        //IE控件上Backspace会导致页面后退
        _jDoc.keydown(function(ev){ var rng=_this.getRng(); if(ev.which===8&&rng.item){$(rng.item(0)).remove();return false;}});
        //修正IE拖动img大小不更新width和height属性值的问题
        function fixResize(ev)
        {
            var jImg=$(ev.target),v;
            if(v=jImg.css('width'))jImg.css('width','').attr('width',v.replace(/[^0-9%]+/g, ''));
            if(v=jImg.css('height'))jImg.css('height','').attr('height',v.replace(/[^0-9%]+/g, ''));
        }
        _jDoc.bind('controlselect',function(ev){
            ev=ev.target;if(!$.nodeName(ev,'IMG'))return;
            $(ev).unbind('resizeend',fixResize).bind('resizeend',fixResize);
        });
    }
    //编辑器事件
    _jDoc.off().on({
        //系统右键菜单屏蔽
        'contextmenu': function() {
            _this.returnFalse();
        },
        //正在输入
        'keyup': function(e) {
            var oldWord = _Body.attr('data-oldword');
            var newWord = _Body.html();
            //只有内容发生变化 才触发格式化内容
            if(oldWord != newWord) {
                //定义要过滤的标签
                var limitTag = ['<ul', '<li','<ol', '<hr', '<s', '<e'];
                var hasTag = false;//是否包含被禁止的标签
                for(var i=0; i< limitTag.length; i++) {
                    if(newWord.indexOf(limitTag[i]) != -1 ) {
                        hasTag = true;
                        break;
                    }
                }
                if(hasTag) {
                    //获取粘贴的内容：除了p,div img (div,p是浏览器默认输入的换行符 )其他全部替换如； css标签
                    newWord = newWord.replace(/\<img([^>]*)>/ig, '[img$1]');
                    newWord = newWord.replace(/\<div([^>]*)>/ig, '[div$1]');
                    newWord = newWord.replace(/\<\/div([^>]*)>/ig, '[\/div]');
                    newWord = newWord.replace(/\<p([^>]*)>/ig, '[p$1]');
                    newWord = newWord.replace(/\<\/p>/ig, '[\/p]');
                    newWord = newWord.replace(/\<br([^>]*)>/ig, '[br]');
                    newWord = newWord.replace(/\<([^>]*)>/ig, '');//移除所有<>
                    //恢复允许的标签 img div p
                    newWord = newWord.replace(/\[img([^\]]*)\]/ig, '<img$1>');
                    newWord = newWord.replace(/\[br\]/ig, '<br>');
                    newWord = newWord.replace(/\[div([^\]]*)\]/ig, '<div$1>');
                    newWord = newWord.replace(/\[p([^\]]*)\]/ig, '<p$1>');
                    newWord = newWord.replace(/\[\/div]/ig, '<\/div>');
                    newWord = newWord.replace(/\[\/p]/ig, '<\/p>');
                    //newWord = newWord.replace(/\<img([^>]*)>/ig, '');//禁止输入其他外部图片
                    _Body.html(newWord);//内容更新 需要重新赋值
                }
                _Body.attr('data-oldword', newWord);
                //如果有侦听函数，执行它。
                if(keyUpFunc) eval(keyUpFunc);
            }
            var textLen = _this.countTextLength(newWord);
            if(numTagObj) {
                numTagObj.html(textLen);
                if(textLen > maxWordsNum) {
                    numTagObj.addClass('red');
                } else {
                    numTagObj.removeClass('red');
                }
            }
        },
        'keydown': function(event) {
            if (event.ctrlKey && event.keyCode == 13) {
                //快捷方式 提交发送内容
                if(postBtn) postBtn.click();
            }
        }
    });
    this.focus = function()
    {
        _win.focus();
        if(isIE) {
            var rng=_this.getRng();
            if(rng.parentElement&&rng.parentElement().ownerDocument!==_doc)_this.setTextCursor();//修正IE初始焦点问题
        }
        return false;
    }
    this.setTextCursor=function(bLast)
    {
        var rng=_this.getRng(true),cursorNode=_doc.body;
        if(isIE || isIE11)rng.moveToElementText(cursorNode);
        else{
            var chileName=bLast?'lastChild':'firstChild';
            while(cursorNode.nodeType!=3&&cursorNode[chileName]){cursorNode=cursorNode[chileName];}
            rng.selectNode(cursorNode);
        }
        rng.collapse(bLast?false:true);
        if(isIE || isIE11)rng.select();
        else{var sel=_this.getSel();sel.removeAllRanges();sel.addRange(rng);}
    }
    this.getSel=function()
    {
        return _doc.selection ? _doc.selection : _win.getSelection();
    }
    this.getRng=function(bNew)
    {
        var sel,rng;
        try{
            if(!bNew){
                sel=_this.getSel();
                rng = sel.createRange ? sel.createRange() : sel.rangeCount > 0?sel.getRangeAt(0):null;
            }
            if(!rng)rng = _doc.body.createTextRange?_doc.body.createTextRange():_doc.createRange();
        }catch (ex){}
        return rng;
    }
    this.getSelect=function(format)
    {
        var sel=_this.getSel(),rng=_this.getRng(),isCollapsed=true;
        if (!rng || rng.item)isCollapsed=false
        else isCollapsed=!sel || rng.boundingWidth === 0 || rng.collapsed;
        if(format==='text') return isCollapsed ? '' : (rng.text || (sel.toString ? sel.toString() : ''));
        var sHtml;
        if(rng.cloneContents)
        {
            var tmp=$('<div></div>'),c;
            c = rng.cloneContents();
            if(c)tmp.append(c);
            sHtml=tmp.html();
        }
        else if(_this.is(rng.item))sHtml=rng.item(0).outerHTML;
        else if(_this.is(rng.htmlText))sHtml=rng.htmlText;
        else sHtml=rng.toString();
        if(isCollapsed)sHtml='';
        sHtml=_this.cleanHTML(sHtml);
        sHtml=_this.formatXHTML(sHtml);
        return sHtml;
    }
    this.cleanHTML=function(sHtml)
    {
        sHtml = sHtml.replace(/<!?\/?(DOCTYPE|html|body|meta)(\s+[^>]*?)?>/ig, '');
        var arrHeadSave;sHtml = sHtml.replace(/<head(?:\s+[^>]*?)?>([\s\S]*?)<\/head>/i, function(all,content){arrHeadSave=content.match(/<(script|style)(\s+[^>]*?)?>[\s\S]*?<\/\1>/ig);return '';});
        if(arrHeadSave)sHtml=arrHeadSave.join('')+sHtml;
        sHtml = sHtml.replace(/<\??xml(:\w+)?(\s+[^>]*?)?>([\s\S]*?<\/xml>)?/ig, '');

        if(!settings.internalScript)sHtml = sHtml.replace(/<script(\s+[^>]*?)?>[\s\S]*?<\/script>/ig, '');
        if(!settings.internalStyle)sHtml = sHtml.replace(/<style(\s+[^>]*?)?>[\s\S]*?<\/style>/ig, '');
        if(!settings.linkTag||!settings.inlineScript||!settings.inlineStyle)sHtml=sHtml.replace(/(<(\w+))((?:\s+[\w-]+\s*=\s*(?:"[^"]*"|'[^']*'|[^>\s]+))*)\s*(\/?>)/ig,function(all,left,tag,attr,right){
            if(!settings.linkTag&&tag.toLowerCase()==='link')return '';
            if(!settings.inlineScript)attr=attr.replace(/\s+on(?:click|dblclick|mouse(down|up|move|over|out|enter|leave|wheel)|key(down|press|up)|change|select|submit|reset|blur|focus|load|unload)\s*=\s*("[^"]*"|'[^']*'|[^>\s]+)/ig,'');
            if(!settings.inlineStyle)attr=attr.replace(/\s+(style|class)\s*=\s*("[^"]*"|'[^']*'|[^>\s]+)/ig,'');
            return left+attr+right;
        });
        sHtml=sHtml.replace(/<\/(strong|b|u|strike|em|i)>((?:\s|<br\/?>|&nbsp;)*?)<\1(\s+[^>]*?)?>/ig,'$2');//连续相同标签

        return sHtml;

    }
    this.cleanWord=function(sHtml)
    {
        var cleanPaste=settings.cleanPaste;
        if(cleanPaste>0&&cleanPaste<3&&/mso(-|normal)|WordDocument|<table\s+[^>]*?x:str|\s+class\s*=\s*"?xl[67]\d"/i.test(sHtml))
        {
            //区块标签清理
            sHtml = sHtml.replace(/<!--[\s\S]*?-->|<!(--)?\[[\s\S]+?\](--)?>|<style(\s+[^>]*?)?>[\s\S]*?<\/style>/ig, '');

            sHtml = sHtml.replace(/\r?\n/ig, '');

            //保留Word图片占位
            if(isIE){
                sHtml = sHtml.replace(/<v:shapetype(\s+[^>]*)?>[\s\S]*<\/v:shapetype>/ig,'');

                sHtml = sHtml.replace(/<v:shape(\s+[^>]+)?>[\s\S]*?<v:imagedata(\s+[^>]+)?>\s*<\/v:imagedata>[\s\S]*?<\/v:shape>/ig,function(all,attr1,attr2){
                    var match;
                    match = attr2.match(/\s+src\s*=\s*("[^"]+"|'[^']+'|[^>\s]+)/i);
                    if(match){
                        match = match[1].match(/^(["']?)(.*)\1/)[2];
                        var sImg ='<img src="/resource/pub/images/box/blank.gif'+'" _xhe_temp="true" class="wordImage"';
                        match = attr1.match(/\s+style\s*=\s*("[^"]+"|'[^']+'|[^>\s]+)/i);
                        if(match){
                            match = match[1].match(/^(["']?)(.*)\1/)[2];
                            sImg += ' style="' + match + '"';
                        }
                        sImg += ' />';
                        return sImg;
                    }
                    return '';
                });
            }
            else{
                sHtml = sHtml.replace(/<img( [^<>]*(v:shapes|msohtmlclip)[^<>]*)\/?>/ig,function(all,attr){
                    var match,str = '<img src="/resource/pub/images/box/blank.gif'+'" _xhe_temp="true" class="wordImage"';
                    match = attr.match(/ width\s*=\s*"([^"]+)"/i);
                    if(match)str += ' width="'+match[1]+'"';
                    match = attr.match(/ height\s*=\s*"([^"]+)"/i);
                    if(match)str += ' height="'+match[1]+'"';
                    return str + ' />';
                });
            }

            sHtml=sHtml.replace(/(<(\/?)([\w\-:]+))((?:\s+[\w\-:]+(?:\s*=\s*(?:"[^"]*"|'[^']*'|[^>\s]+))?)*)\s*(\/?>)/g,function(all,left,end,tag,attr,right){
                tag=tag.toLowerCase();
                if((tag.match(/^(link)$/)&&attr.match(/file:\/\//i))||tag.match(/:/)||(tag==='span'&&cleanPaste===2))return '';
                if(!end){
                    attr=attr.replace(/\s([\w\-:]+)(?:\s*=\s*("[^"]*"|'[^']*'|[^>\s]+))?/ig,function(all,n,v){
                        n=n.toLowerCase();
                        if(/:/.test(n))return '';
                        v=v.match(/^(["']?)(.*)\1/)[2];
                        if(cleanPaste===1){//简单清理
                            switch(tag){
                                case 'p':
                                    if(n === 'style'){
                                        v=v.replace(/"|&quot;/ig,"'").replace(/\s*([^:]+)\s*:\s*(.*?)(;|$)/ig,function(all,n,v){
                                            return /^(text-align)$/i.test(n)?(n+':'+v+';'):'';
                                        }).replace(/^\s+|\s+$/g,'');
                                        return v?(' '+n+'="'+v+'"'):'';
                                    }
                                    break;
                                case 'span':
                                    if(n === 'style'){
                                        v=v.replace(/"|&quot;/ig,"'").replace(/\s*([^:]+)\s*:\s*(.*?)(;|$)/ig,function(all,n,v){
                                            return /^(color|background|font-size|font-family)$/i.test(n)?(n+':'+v+';'):'';
                                        }).replace(/^\s+|\s+$/g,'');
                                        return v?(' '+n+'="'+v+'"'):'';
                                    }
                                    break;
                                case 'table':
                                    if(n.match(/^(cellspacing|cellpadding|border|width)$/i))return all;
                                    break;
                                case 'td':
                                    if(n.match(/^(rowspan|colspan)$/i))return all;
                                    if(n === 'style'){
                                        v=v.replace(/"|&quot;/ig,"'").replace(/\s*([^:]+)\s*:\s*(.*?)(;|$)/ig,function(all,n,v){
                                            return /^(width|height)$/i.test(n)?(n+':'+v+';'):'';
                                        }).replace(/^\s+|\s+$/g,'');
                                        return v?(' '+n+'="'+v+'"'):'';
                                    }
                                    break;
                                case 'a':
                                    if(n.match(/^(href)$/i))return all;
                                    break;
                                case 'font':
                                case 'img':
                                    return all;
                                    break;
                            }
                        }
                        else if(cleanPaste===2){
                            switch(tag){
                                case 'td':
                                    if(n.match(/^(rowspan|colspan)$/i))return all;
                                    break;
                                case 'img':
                                    return all;
                            }
                        }
                        return '';
                    });
                }
                return left+attr+right;
            });
            //空内容的标签
            for(var i=0;i<3;i++)sHtml = sHtml.replace( /<([^\s>]+)(\s+[^>]*)?>\s*<\/\1>/g,'');
            //无属性的无意义标签
            function cleanEmptyTag(all,tag,content){
                return content;
            }
            for(var i=0;i<3;i++)sHtml = sHtml.replace(/<(span|a)>(((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S])*?<\/\1>)*?<\/\1>)*?)<\/\1>/ig,cleanEmptyTag);//第3层
            for(var i=0;i<3;i++)sHtml = sHtml.replace(/<(span|a)>(((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S])*?<\/\1>)*?)<\/\1>/ig,cleanEmptyTag);//第2层
            for(var i=0;i<3;i++)sHtml = sHtml.replace(/<(span|a)>(((?!<\1(\s+[^>]*?)?>)[\s\S])*?)<\/\1>/ig,cleanEmptyTag);//最里层
            //合并多个font
            for(var i=0;i<3;i++)sHtml = sHtml.replace(/<font(\s+[^>]+)><font(\s+[^>]+)>/ig,function(all,attr1,attr2){
                return '<font'+attr1+attr2+'>';
            });
            //清除表格间隙里的空格等特殊字符
            sHtml=sHtml.replace(/(<(\/?)(tr|td)(?:\s+[^>]+)?>)[^<>]+/ig,function(all,left,end,tag){
                if(!end&&/^td$/i.test(tag))return all;
                else return left;
            });
        }
        return sHtml;
    }
    this.formatXHTML=function(sHtml,bFormat){
        var emptyTags = makeMap("area,base,basefont,br,col,frame,hr,img,input,isindex,link,meta,param,embed");//HTML 4.01
        var blockTags = makeMap("address,applet,blockquote,button,center,dd,dir,div,dl,dt,fieldset,form,frameset,h1,h2,h3,h4,h5,h6,hr,iframe,ins,isindex,li,map,menu,noframes,noscript,object,ol,p,pre,table,tbody,td,tfoot,th,thead,tr,ul,script");//HTML 4.01
        var inlineTags = makeMap("a,abbr,acronym,applet,b,basefont,bdo,big,br,button,cite,code,del,dfn,em,font,i,iframe,img,input,ins,kbd,label,map,object,q,s,samp,script,select,small,span,strike,strong,sub,sup,textarea,tt,u,var");//HTML 4.01
        var closeSelfTags = makeMap("colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr");
        var fillAttrsTags = makeMap("checked,compact,declare,defer,disabled,ismap,multiple,nohref,noresize,noshade,nowrap,readonly,selected");
        var cdataTags = makeMap("script,style");
        var tagReplac={'b':'strong','i':'em','s':'del','strike':'del'};

        var regTag=/<(?:\/([^\s>]+)|!([^>]*?)|([\w\-:]+)((?:"[^"]*"|'[^']*'|[^"'<>])*)\s*(\/?))>/g;
        var regAttr = /\s*([\w\-:]+)(?:\s*=\s*(?:"([^"]*)"|'([^']*)'|([^\s]+)))?/g;
        var results=[],stack=[];
        stack.last = function(){return this[ this.length - 1 ];};
        var match,tagIndex,nextIndex=0,tagName,tagCDATA,arrCDATA,text;
        var lvl=-1,lastTag='body',lastTagStart,stopFormat=false;

        while(match=regTag.exec(sHtml)){
            tagIndex = match.index;
            if(tagIndex>nextIndex){//保存前面的文本或者CDATA
                text=sHtml.substring(nextIndex,tagIndex);
                if(tagCDATA)arrCDATA.push(text);
                else onText(text);
            }
            nextIndex = regTag.lastIndex;

            if(tagName=match[1]){//结束标签
                tagName=processTag(tagName);
                if(tagCDATA&&tagName===tagCDATA){//结束标签前输出CDATA
                    onCDATA(arrCDATA.join(''));
                    tagCDATA=null;
                    arrCDATA=null;
                }
                if(!tagCDATA){
                    onEndTag(tagName);
                    continue;
                }
            }

            if(tagCDATA)arrCDATA.push(match[0]);
            else{
                if(tagName=match[3]){//开始标签
                    tagName=processTag(tagName);
                    onStartTag(tagName,match[4],match[5]);
                    if(cdataTags[tagName]){
                        tagCDATA=tagName;
                        arrCDATA=[];
                    }
                }
                else if(match[2])onComment(match[0]);//注释标签
            }

        }
        if(sHtml.length>nextIndex)onText(sHtml.substring(nextIndex,sHtml.length ));//结尾文本
        onEndTag();//封闭未结束的标签
        sHtml=results.join('');
        results=null;

        function makeMap(str)
        {
            var obj = {}, items = str.split(",");
            for ( var i = 0; i < items.length; i++ )obj[ items[i] ] = true;
            return obj;
        }
        function processTag(tagName)
        {
            tagName=tagName.toLowerCase();
            var tag=tagReplac[tagName];
            return tag?tag:tagName;
        }
        function onStartTag(tagName,rest,unary)
        {
            if(blockTags[tagName])while(stack.last()&&inlineTags[stack.last()])onEndTag(stack.last());//块标签
            if(closeSelfTags[tagName]&&stack.last()===tagName)onEndTag(tagName);//自封闭标签
            unary = emptyTags[ tagName ] || !!unary;
            if (!unary)stack.push(tagName);

            var all= [];
            all.push('<' + tagName);
            rest.replace(regAttr, function(match, name)
            {
                name=name.toLowerCase();
                var value = arguments[2] ? arguments[2] :
                    arguments[3] ? arguments[3] :
                        arguments[4] ? arguments[4] :
                            fillAttrsTags[name] ? name : "";
                all.push(' '+name+'="'+value.replace(/"/g,"'")+'"');
            });
            all.push((unary ? " /" : "") + ">");
            addHtmlFrag(all.join(''),tagName,true);
            if(tagName==='pre')stopFormat=true;
        }
        function onEndTag(tagName)
        {
            if(!tagName)var pos=0;//清空栈
            else for(var pos=stack.length-1;pos>=0;pos--)if(stack[pos]===tagName)break;//向上寻找匹配的开始标签
            if(pos>=0)
            {
                for(var i=stack.length-1;i>=pos;i--)addHtmlFrag("</" + stack[i] + ">",stack[i]);
                stack.length=pos;
            }
            if(tagName==='pre'){
                stopFormat=false;
                lvl--;
            }
        }
        function onText(text){
            addHtmlFrag(_this.domEncode(text));
        }
        function onCDATA(text){
            results.push(text.replace(/^[\s\r\n]+|[\s\r\n]+$/g,''));
        }
        function onComment(text){
            results.push(text);
        }
        function addHtmlFrag(html,tagName,bStart)
        {
            if(!stopFormat)html=html.replace(/(\t*\r?\n\t*)+/g,'');//清理换行符和相邻的制表符
            if(!stopFormat&&bFormat===true)
            {
                if(html.match(/^\s*$/)){//不格式化空内容的标签
                    results.push(html);
                    return;
                }
                var bBlock=blockTags[tagName],tag=bBlock?tagName:'';
                if(bBlock)
                {
                    if(bStart)lvl++;//块开始
                    if(lastTag==='')lvl--;//补文本结束
                }
                else if(lastTag)lvl++;//文本开始
                if(tag!==lastTag||bBlock)addIndent();
                results.push(html);
                if(tagName==='br')addIndent();//回车强制换行
                if(bBlock&&(emptyTags[tagName]||!bStart))lvl--;//块结束
                lastTag=bBlock?tagName:'';lastTagStart=bStart;
            }
            else results.push(html);
        }
        function addIndent(){results.push('\r\n');if(lvl>0){var tabs=lvl;while(tabs--)results.push("\t");}}
        //font转style
        var arrFontsize = settings.listFontsize;
        function font2style(all,tag,attrs,content)
        {
            if(!attrs)return content;
            var styles='',f,s,c,style;
            attrs=attrs.replace(/ face\s*=\s*"\s*([^"]*)\s*"/i,function(all,v){
                if(v)styles+='font-family:'+v+';';
                return '';
            });
            attrs=attrs.replace(/ size\s*=\s*"\s*(\d+)\s*"/i,function(all,v){
                styles+='font-size:'+arrFontsize[(v>7?7:(v<1?1:v))-1].s+';';
                return '';
            });
            attrs=attrs.replace(/ color\s*=\s*"\s*([^"]*)\s*"/i,function(all,v){
                if(v)styles+='color:'+v+';';
                return '';
            });
            attrs=attrs.replace(/ style\s*=\s*"\s*([^"]*)\s*"/i,function(all,v){
                if(v)styles+=v;
                return '';
            });
            attrs+=' style="'+styles+'"';
            return attrs?('<span'+attrs+'>'+content+'</span>'):content;
        }
        sHtml = sHtml.replace(/<(font)(\s+[^>]*?)?>(((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S])*?<\/\1>)*?<\/\1>)*?)<\/\1>/ig,font2style);//第3层
        sHtml = sHtml.replace(/<(font)(\s+[^>]*?)?>(((?!<\1(\s+[^>]*?)?>)[\s\S]|<\1(\s+[^>]*?)?>((?!<\1(\s+[^>]*?)?>)[\s\S])*?<\/\1>)*?)<\/\1>/ig,font2style);//第2层
        sHtml = sHtml.replace(/<(font)(\s+[^>]*?)?>(((?!<\1(\s+[^>]*?)?>)[\s\S])*?)<\/\1>/ig,font2style);//最里层
        sHtml = sHtml.replace(/^(\s*\r?\n)+|(\s*\r?\n)+$/g,'');//清理首尾换行
        return sHtml;
    }
    this.domEncode=function(text)
    {
        return text.replace(regEntities,function(c){return arrEntities[c];});
    }
    this.pasteHTML = function(sHtml,bStart)
    {
        _this.focus();
        var sel=_this.getSel(),rng=_this.getRng();
        if(bStart!==undefined)//非覆盖式插入
        {
            if(rng.item)
            {
                var item=rng.item(0);
                rng=_this.getRng(true);
                rng.moveToElementText(item);
                rng.select();
            }
            rng.collapse(bStart);
        }
        sHtml+='<'+(isIE?'img':'span')+' id="_xhe_temp" width="0" height="0" />';
        if(rng.insertNode)
        {
            if($(rng.startContainer).closest('style,script').length>0)return false;//防止粘贴在style和script内部
            rng.deleteContents();
            rng.insertNode(rng.createContextualFragment(sHtml));
        }
        else
        {
            if(sel.type.toLowerCase()==='control'){sel.clear();rng=_this.getRng();}
            rng.pasteHTML(sHtml);
        }
        var jTemp=$('#_xhe_temp',_doc),temp=jTemp[0];
        if(isIE)
        {
            rng.moveToElementText(temp);
            rng.select();
        }
        else
        {
            rng.selectNode(temp);
            sel.removeAllRanges();
            sel.addRange(rng);
        }
        jTemp.remove();
    }
    this.returnFalse = function(){ return false; }
    _this.is = function(o,t) {
        var n = typeof(o);
        if (!t)return n != 'undefined';
        if (t === 'array' && (o.hasOwnProperty && o instanceof Array))return true;
        return n === t;
    }
    this.getLocalUrl = function(url,urlType,urlBase)//绝对地址：abs,根地址：root,相对地址：rel
    {
        if( (url.match(/^(\w+):\/\//i) && !url.match(/^https?:/i)) || /^#/i.test(url) || /^data:/i.test(url) )return url;//非http和https协议，或者页面锚点不转换，或者base64编码的图片等
        var baseUrl=urlBase?$('<a href="'+urlBase+'" />')[0]:location,protocol=baseUrl.protocol,host=baseUrl.host,hostname=baseUrl.hostname,port=baseUrl.port,path=baseUrl.pathname.replace(/\\/g,'/').replace(/[^\/]+$/i,'');
        if(port==='')port='80';
        if(path==='')path='/';
        else if(path.charAt(0)!=='/')path='/'+path;//修正IE path
        url=$.trim(url);
        //删除域路径
        if(urlType!=='abs')url=url.replace(new RegExp(protocol+'\\/\\/'+hostname.replace(/\./g,'\\.')+'(?::'+port+')'+(port==='80'?'?':'')+'(\/|$)','i'),'/');
        //删除根路径
        if(urlType==='rel')url=url.replace(new RegExp('^'+path.replace(/([\/\.\+\[\]\(\)])/g,'\\$1'),'i'),'');
        //加上根路径
        if(urlType!=='rel')
        {
            if(!url.match(/^(https?:\/\/|\/)/i))url=path+url;
            if(url.charAt(0)==='/')//处理根路径中的..
            {
                var arrPath=[],arrFolder = url.split('/'),folder,i,l=arrFolder.length;
                for(i=0;i<l;i++)
                {
                    folder=arrFolder[i];
                    if(folder==='..')arrPath.pop();
                    else if(folder!==''&&folder!=='.')arrPath.push(folder);
                }
                if(arrFolder[l-1]==='')arrPath.push('');
                url='/'+arrPath.join('/');
            }
        }
        //加上域路径
        if(urlType==='abs'&&!url.match(/^https?:\/\//i))url=protocol+'//'+host+url;
        url=url.replace(/(https?:\/\/[^:\/?#]+):80(\/|$)/i,'$1$2');//省略80端口
        return url;
    }
    this.xheAttr = function(jObj,n,v)
    {
        if(!n)return false;
        var kn='_xhe_'+n;
        if(v)//设置属性
        {
            if(urlType)v=_this.getLocalUrl(v,urlType,urlBase);
            jObj.attr(n,urlBase?_this.getLocalUrl(v,'abs',urlBase):v).removeAttr(kn).attr(kn,v);
        }
        return jObj.attr(kn)||jObj.attr(n);
    }
    //清除粘贴内容
    this.cleanPaste = function(ev){
        var clipboardData,items,item;//for chrome
        if(ev&&(clipboardData=ev.originalEvent.clipboardData)&&(items=clipboardData.items)&&(item=items[0])&&item.kind=='file'&&item.type.match(/^image\//i)){
            var blob = item.getAsFile(),reader = new FileReader();
            reader.onload=function(ev2){
                var sHtml='<img src="'+ev2.target.result+'">';
                sHtml=_this.replaceRemoteImg(sHtml);
                _this.pasteHTML(sHtml);
            }
            reader.readAsDataURL(blob);
            return false;
        }

        var clean_paste=settings.clean_paste;
        if(clean_paste===0||bCleanPaste)return true;
        bCleanPaste=true;//解决IE右键粘贴重复产生paste的问题
        //_this.saveBookmark();
        var tag=isIE?'pre':'div',jDiv=$('<'+tag+' class="xhe-paste">\uFEFF\uFEFF</'+tag+'>',_doc).appendTo(_doc.body),div=jDiv[0],sel=_this.getSel(),rng=_this.getRng(true);
        jDiv.css('top',_jWin.scrollTop());
        if(isIE || isIE11){
            rng.moveToElementText(div);
            rng.select();
            //注：调用execommand:paste，会导致IE8,IE9目标路径无法转为绝对路径
        }
        else{
            rng.selectNodeContents(div);
            sel.removeAllRanges();
            sel.addRange(rng);
        }

        setTimeout(function(){
            var bText=(clean_paste===3),sPaste;
            if(bText){
                jDiv.html(jDiv.html().replace(/<br(\s+[^<>]*)?>/ig,'\n'));
                sPaste=jDiv.text();
            }
            else{
                var jTDiv=$('.xhe-paste',_doc.body),arrHtml=[];
                jTDiv.each(function(i,n){if($(n).find('.xhe-paste').length==0)arrHtml.push(n.innerHTML);});
                sPaste=arrHtml.join('<br />');
            }

            jDiv.remove();
            //_this.loadBookmark();
            sPaste=sPaste.replace(/^[\s\uFEFF]+|[\s\uFEFF]+$/g,'');
            if(sPaste){
                if(bText)_this.pasteText(sPaste);
                else{
                    sPaste=_this.cleanHTML(sPaste);
                    sPaste=_this.cleanWord(sPaste);
                    sPaste=_this.formatXHTML(sPaste);
                    if(!settings.onPaste||settings.onPaste&&(sPaste=settings.onPaste(sPaste))!==false){
                        sPaste=_this.replaceRemoteImg(sPaste);
                        _this.pasteHTML(sPaste);
                    }
                }
            }
            bCleanPaste=false;
        },0);
    }
    //远程图片转本地
    this.replaceRemoteImg= function (sHtml){
        var localUrlTest=settings.localUrlTest,remoteImgSaveUrl=settings.remoteImgSaveUrl;
        if(localUrlTest&&remoteImgSaveUrl){
            var arrRemoteImgs=[],count=0;
            sHtml=sHtml.replace(/(<img)((?:\s+[^>]*?)?(?:\s+src="\s*([^"]+)\s*")(?: [^>]*)?)(\/?>)/ig,function(all,left,attr,url,right){
                if(/^(https?|data:image)/i.test(url) && !/_xhe_temp/.test(attr) && !localUrlTest.test(url)){
                    arrRemoteImgs[count]=url;
                    attr=attr.replace(/\s+(width|height)="[^"]*"/ig,'').replace(/\s+src="[^"]*"/ig,' src="/resource/pub/images/loading2.gif" remoteimg="'+(count++)+'"');
                }
                return left+attr+right;
            });
            if(arrRemoteImgs.length>0){
                $.post(remoteImgSaveUrl,{urls:arrRemoteImgs.join('|')},function(data){
                    data=data.split('|');
                    $('img[remoteimg]',_this.doc).each(function(){
                        var $this=$(this);
                        _this.xheAttr($this,'src',data[$this.attr('remoteimg')]);
                        $this.removeAttr('remoteimg');
                    });
                });
            }
        }
        return sHtml;
    }
    var jBody=$(_doc.documentElement);
    //自动清理粘贴内容
    if(isOpera)jBody.bind('keydown',function(e){if(e.ctrlKey&&e.which===86) _this.cleanPaste();});
    else jBody.bind(isIE?'beforepaste':'paste',_this.cleanPaste);

    //统计字数
    this.countTextLength = function(contentText) {
        contentText = contentText || '';
        contentText = contentText.replace(/<([^>]*)>/ig, '');
        return contentText.length;
    }
    //内容写入
    this.pushContent = function(html) {
        if(!html) {
            _Body.attr('data-oldword', '').html('').focus();
            return;
        }
        _this.pasteHTML(html);
    }
    //获取输入内容
    this.getContent = function() {
        return $.trim(_Body.html());
    }
    return this;
};
