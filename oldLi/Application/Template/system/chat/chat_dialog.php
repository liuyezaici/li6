<style>
    /* 聊天对话窗口 */
    /* 聊天输入框 */
    #post_tool_bar {
        width: 100%;
        border-top: 0.1em solid #dedede;
        background-color: #fafafa;
        padding-top: 5px;
    }
    #post_tool_bar .input_box {
        float: left;
        width: 76%;
    }
    #post_tool_bar .input_box .lr_editor {
        height: 5.4em;
        width: 99%;
        border: 1px solid #dedede;
        margin-left: 0.4em;
    }
    #post_tool_bar .submit_box {
        float: left;
        width: 24%;
        overflow: hidden;
    }
    #post_tool_bar .submit_box .submit {
        width: 5em;
        height: 2.5em;
        line-height: 2.5em;
        text-align: center;
        display: block;
        float: left;
        background-color: #a4c3e4;
        border: 0;
        border-radius: 0.2em;
        cursor: pointer;
        margin-left: 0.4em;
        margin-top: 1em;
    }
    #post_tool_bar .submit_box .hiden_submit {
        width: 0;
        height: 0;
        overflow: hidden;
        border: 0;
    }
    #post_tool_bar .submit_box .submit_way {
        display: block;
        float: left;
        color: #999;
        margin-left: 2em;
        margin-top: -0.3em;
        font-size: 0.78em;
    }
    #post_tool_bar .submit_box .count_text_num {
        color: #777;
        margin-left: 10px;
    }
    #post_tool_bar .submit_box .count_text_num #num {
        color: #333;
    }
    #post_tool_bar .submit_box .count_text_num #num.red {
        color: #ff0000;
    }
    #post_tool_bar .face_box {
        float: left;
        width: 22px;
        margin-left: 10px;
    }
    #post_tool_bar .face_box .icon {
        display: block;
        width: 22px;
        height: 24px;
        background: url('/resource/system/images/webchat/enter_face_icon.png') no-repeat;
        background-size: cover;
        cursor: pointer;
    }
    #post_tool_bar .face_box .icon.active {
        background-position: 0 bottom;
    }
    #post_tool_bar .face_box #face_menu {
        border-top: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
        background-color: #fff;
        height: 12em;
        overflow-y: scroll;
        left: 0;
        position: absolute;
        bottom: 0;
        width: 100%;
        display: none;
        z-index: 4;
    }
    #post_tool_bar .face_box #face_menu ul {
        margin-top: 0.2em;
        margin-left: 0.2em;
    }
    #post_tool_bar .face_box #face_menu ul li {
        float: left;
        border: 1px solid #ddd;
        display: block;
        height: 2em;
        margin-right: 0.5em;
        margin-bottom: 0.5em;
        width: 2em;
    }
    #post_tool_bar .face_box #face_menu ul li img {
        width: 2em;
        cursor: pointer;
    }

    /*  聊天记录列表 */
    #message_list {
        background-color: #ebebeb;
        min-height: 1rem; /* 聊天框的最小高度 手机的打字模式下 高度会被小键盘给压缩 */
        max-height: 400px;
        overflow-y: scroll;
        scrollbar-3dlight-color: #D4D0C8; /*- 最外左 -*/
        scrollbar-highlight-color:#fff; /*- 左二 -*/
        scrollbar-face-color: #E4E4E4; /*- 面子 -*/
        scrollbar-arrow-color: #666; /*- 箭头 -*/
        scrollbar-shadow-color: #808080; /*- 右二 -*/
        scrollbar-darkshadow-color: #D7DCE0; /*- 右一 -*/
        scrollbar-base-color: #D7DCE0; /*- 基色 -*/
        scrollbar-track-color: #ddd;/*- 滑道 -*/
    }
    #message_list ul {
        margin: 0;
    }
    #message_list ul li {
        margin-bottom: 2px;
        width: 100%;
        overflow: hidden;
    }
    /* 对方内容 */
    #message_list ul li.his_message .li_left {
        float: left;
        width: 20%;
        overflow: hidden;
    }
    #message_list ul li.his_message .li_left .u_face {
        display: block;
        margin: 0.5em;
    }
    #message_list ul li.his_message .li_left .u_face img {
        width: 100%;
        border: 0.1em solid #ddd;
    }
    #message_list ul li.his_message .li_right {
        float: left;
        width: 80%;
    }
    #message_list ul li.his_message .li_right .r_text {
        border: 0.1em solid #ddd;
        background: #fff;
        display: block;
        min-width: 2.5em;
        margin: 0.5em 0 0 0.3em;
        border-radius: 0.2em;
        position: relative;
        padding: 0.5em;
        float: left;
        word-break:break-all;
        max-width: 14em;
        z-index: 1;
    }
    #message_list ul li.his_message .li_right .r_text .msg_con {
        background: url("/resource/system/images/webchat/webchat_icon.png") no-repeat scroll -0.2em -0.5em / cover ;
        display: block;
        height: 1em;
        left: -0.55em;
        line-height: 0.3em;
        margin: 0.5em 0.5em 0 0.2em;
        min-height: 0.3em;
        overflow: hidden;
        position: absolute;
        top: 0.2em;
        width: 1em;
        z-index: 2;
    }
    /* 我发出的内容 */
    #message_list ul li.my_message .li_left {
        float: left;
        width: 80%;
    }
    #message_list ul li.my_message .li_left .r_text {
        min-width: 2.5em;
        border: 0.1em solid #add09e;
        display: block;
        margin: 0.5em 0.3em 0 0;
        border-radius: 0.2em;
        position: relative;
        background-color: #a0e75a;
        padding: 0.5em;
        float: right;
        word-break:break-all;
        max-width: 14em;
        z-index: 1;
    }
    #message_list ul li.my_message .li_left .r_text .msg_con {
        background: url("/resource/system/images/webchat/webchat_icon.png") no-repeat scroll 0.1em -2.2em / cover ;
        display: block;
        height: 1em;
        right: -0.95em;
        line-height: 0.3em;
        margin: 0.5em 0.5em 0 0.2em;
        min-height: 0.3em;
        overflow: hidden;
        position: absolute;
        top: 0.2em;
        width: 1em;
        z-index: 2;
    }
    #message_list ul li.my_message .li_right {
        float: left;
        width: 20%;
    }
    #message_list ul li.my_message .li_right .u_face {
        display: block;
        margin: 0.5em 1.2em 0.5em 0.5em;
    }
    #message_list ul li.my_message .li_right .u_face img {
        width: 100%;
        border: 0.1em solid #ddd;
    }
    #message_list ul li.no_message {
        padding: 8px 0;
        text-indent: 10px;
    }

</style>
<?=$chatTopHtml?>
<div id="dialog_box">
    <div id="message_list">
        <ul>
            <li class="no_message">loading</li>
        </ul>
    </div>
    <form id="post_tool_bar" class="clearfix">
        <div class="input_box" id="chat_editor"> </div>
        <div class="submit_box">
         <span class="count_text_num">
            <div class="face_box">
                <span class="icon" id="face_btn"></span>
                <div id="face_menu">
                    <ul>
                        <li></li>
                    </ul>
                </div>
            </div>
             <span id="num">0</span>/<span id="max_num">0</span></span>
            <input type="submit" class="submit" value="发送" />
        </div>
    </form>
</div>
<script>
    //lr 编辑器 2017-03-03 17:00 核心借鉴于xheditor
    // appendWhere 编辑器创建的位置 标签对象
    // numTagObj 字数 标签对象
    // maxWordsNum 最多字数
    // keyUpFunc 输入回调函数
    // postBtn 提交按钮对象
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
        var settings  = {localUrlTest:/^https?:\/\/[^\/]*?(s\.com)\//i,remoteImgSaveUrl: '/?s=chat&do=paste_image',clean_paste: 1, urlType: 'rel', urlBase: '/'};
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
        setTimeout(_this.setOpts,300);
        var _Body = $(_doc.body);
        _jText.focus(_this.focus);
        _jWin.focus(function(){if(settings.focus)settings.focus();}).blur(function(){if(settings.blur)settings.blur();});
        if(isWebkit){
            _jWin.click(_this.fixAppleSel);
        }
        this.setCSS = function(css)
        {
            try{_this._exec('styleWithCSS',css,true);}
            catch(e)
            {try{_this._exec('useCSS',!css,true);}catch(e){}}
        };
        _this.setOpts = function()
        {
            setCSS(false);
            try{_this._exec('enableObjectResizing',true,true);}catch(e){}
            //try{_this._exec('enableInlineTableEditing',false,true);}catch(e){}
            if(isIE)try{_this._exec('BackgroundImageCache',true,true);}catch(e){}
        };
        this.fixAppleSel = function(e) {
            e=e.target;
            if(e.tagName.match(/(img|embed)/i))
            {
                var sel=_this.getSel(),rng=_this.getRng(true);
                rng.selectNode(e);
                sel.removeAllRanges();
                sel.addRange(rng);
            }
        };
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
                    if(keyUpFunc) {
                        if(typeof keyUpFunc == 'function') {
                            keyUpFunc();
                        } else {
                            eval(keyUpFunc);
                        }
                    }
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
        };
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
        };
        this.getSel=function()
        {
            return _doc.selection ? _doc.selection : _win.getSelection();
        };
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
        };
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
        };
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

        };
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
        };
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
                if(!tagName) var pos=0;//清空栈
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
        };
        this.domEncode=function(text)
        {
            return text.replace(regEntities,function(c){return arrEntities[c];});
        };
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
        };
        this.returnFalse = function(){ return false; };
        _this.is = function(o,t) {
            var n = typeof(o);
            if (!t)return n != 'undefined';
            if (t === 'array' && (o.hasOwnProperty && o instanceof Array))return true;
            return n === t;
        };
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
        };
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
        };
        //清除粘贴内容
        this.cleanPaste = function(ev){
            var clipboardData,items,item;//for chrome
            if(ev&&(clipboardData=ev.originalEvent.clipboardData)&&(items=clipboardData.items)&&(item=items[0])&&item.kind=='file'&&item.type.match(/^image\//i)){
                var blob = item.getAsFile(),reader = new FileReader();
                reader.onload=function(ev2){
                    var sHtml='<img src="'+ev2.target.result+'">';
                    sHtml=_this.replaceRemoteImg(sHtml);
                    _this.pasteHTML(sHtml);
                };
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
        };
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
        };
        var jBody=$(_doc.documentElement);
        //自动清理粘贴内容
        if(isOpera)jBody.bind('keydown',function(e){if(e.ctrlKey&&e.which===86) _this.cleanPaste();});
        else jBody.bind(isIE?'beforepaste':'paste',_this.cleanPaste);

        //统计字数
        this.countTextLength = function(contentText) {
            contentText = contentText || '';
            contentText = contentText.replace(/<([^>]*)>/ig, '');
            return contentText.length;
        };
        //内容写入
        this.pushContent = function(html) {
            if(!html) {
                _Body.attr('data-oldword', '').html('').focus();
                return;
            }
            _this.pasteHTML(html);
        };
        //获取输入内容
        this.getContent = function() {
            return $.trim(_Body.html());
        };
        return this;
    }
    //编辑器结束
    $(function() {
        var chatTop = $('#chat_top');
        var dialogBox = $('#dialog_box');
        var dialogContentBody = dialogBox.find('#message_list');
        var messageToolBar = dialogBox.find('#post_tool_bar');
        var faceMenu = messageToolBar.find('#face_menu');
        var faceBtn = messageToolBar.find('#face_btn');
        var numBox = messageToolBar.find('#num');
        var chatModelTalkUrl = '/?s=chat&do=talk_to_user';
        var chatModelLoadMessage = '/?s=chat&do=load_last_message';//获取旧的聊天记录接口
        var chatModelPostMessage = '/?s=chat&do=post_message';
        var faceMenuHideTimeId ;//倒计时隐藏表情菜单
        //没有头像时 显示此
        var this_ = this;
        //当前会话ID
        this.dialogId = 0;
        //最后加载的内容id
        this.lastLoadMessageLogid = 0;
        this.hisUserInfo = {};//对方的本地信息
        this.maxWordsNum = 500;  //最多输入字数
        this.needToGetOldMessage = true;  //是否需要获取旧的信息，翻到顶部时不再需要
        this.message_pagesize = 10; //单页聊天记录获取数量
        this.topBottomSpace = 190; //窗口上下预留高度，以限制中间内容高度
        //重置窗口高度
        this.reHeightDialogBox = function () {
            var bodyMaxHeight = $(window).outerHeight(true) - this_.topBottomSpace;
            if(bodyMaxHeight > 600) bodyMaxHeight = 600;
            dialogContentBody.css('height', bodyMaxHeight);
        };

        //附加聊天函数: 设置对方在线/离线
        webChat.setHisIsOnline = function(online)
        {
            chatTop.find('#dialog_status').html((online ? '[在线]' : '[离线]'));
        };
        //附加聊天函数：对方正在输入
        webChat.setHisWriting = function()
        {
            chatTop.find('#is_writting').html('正在输入...');
            setTimeout(function () {
                chatTop.find('#is_writting').html('');
            }, 1200);
        };
        //聊天内容滚动到顶部时自动加载之前的聊天内容
        dialogContentBody.off().on('scroll', function(e) {
            var currentTop = $(this).scrollTop();
            if(currentTop == 0 && this_.needToGetOldMessage) {
                this_.loadLastMessage('top');
            }
         });
        //替换表情 html转ubb
        this.emojiHtmlToUbb =function(contentText)
        {
            contentText = contentText || '';
            contentText = contentText.replace(/\<img([^>^<]*)src="([^"]*)\/include\/lib\/webchat\/images\/face\/([a-z0-9]*)\/([0-9+]*)\.gif"([^>]*)>/ig, '[face:$3_$4]');//将本地表情包替换为ubb
            //contentText = contentText.replace(/\<img([^>]*)>/ig, '');//禁止输入其他外部图片
            contentText = contentText.replace(/\<div([^>]*)>/ig, '');
            contentText = contentText.replace(/\<\/div>/ig, '');
            contentText = contentText.replace(/\<span([^>]*)>/ig, '');
            contentText = contentText.replace(/\<\/span>/ig, '');
            contentText = contentText.replace(/\<ul([^>]*)>/ig, '');
            contentText = contentText.replace(/\<\/ul>/ig, '');
            contentText = contentText.replace(/\<li([^>]*)>/ig, '');
            contentText = contentText.replace(/\<\/li>/ig, '');
            contentText = contentText.replace(/\<p([^>]*)>/ig, '');
            contentText = contentText.replace(/\<\/p>/ig, '');
            return contentText;
        };
        //获取聊天内容模版
        this.getContentTemp = function(flag, data_)
        {
            //替换聊天表情
            var contentText = data_['content'];
            contentText = webChat.emojiUbbToHtml(contentText);
            var dialogTemplate = '';
            if(flag == 'his_message') {
                dialogTemplate = "<li class='his_message'>" +
                    "    <div class='li_left'>" +
                    "<span class='u_face'><img src='"+ (this_.hisUserInfo.u_logo||webChat.nullLogo) +"' onerror=\"this.src='"+ webChat.nullLogo +"'\" /></span>"+
                    "    </div>" +
                    "    <div class='li_right'>" +
                    "       <span class='r_text' title='"+ data_['time'] +"'><span class='msg_con'> </span> "+ contentText +"</span>"+
                    "    </div>" +
                    "</li>";
            } else {
                dialogTemplate = "<li class='my_message'>" +
                    "    <div class='li_left'>" +
                    "       <span class='r_text' title='"+ data_['time'] +"'><span class='msg_con'> </span> "+ contentText +"</span>"+
                    "    </div>" +
                    "    <div class='li_right'>" +
                    "       <span class='u_face'><img src='"+ (webChat.myUserInfo.u_logo||webChat.nullLogo) +"' onerror=\"this.src='"+ webChat.nullLogo +"'\" /></span>"+
                    "    </div>" +
                    "</li>";
            }
            return dialogTemplate;
        };
        //聊天输入框事件
        //点击表情按钮 展开菜单
        faceBtn.off().on({
            'click': function() {
                clearTimeout(faceMenuHideTimeId);//清除残留的移除倒计时
                var btn = $(this);
                btn.toggleClass('active');
                if(btn.hasClass('active')) {
                    if(faceMenu.find('li').length <=1) {
                        var faceHtml = '';
                        for(var i=1; i< webChat.webchatEmoji.total+1; i++) {
                            faceHtml += '<li><img data-type="'+ webChat.webchatEmoji.type +'" data-index="'+ i +'" src="'+ webChat.webchatEmoji.path + '/' + webChat.webchatEmoji.type +'/'+ i +'.'+ webChat.webchatEmoji.geshi +'" /></li>';
                        }
                        faceMenu.html('<ul>'+ faceHtml +'</ul>');
                        //表情点击事件 - 插入表情
                        faceMenu.find('img').off().on('click', function() {
                            var face_ = $(this);
                            var type_ = face_.attr('data-type');
                            var index = face_.attr('data-index');
                            var text = '[face:'+ type_ +'_'+ index +']';
                            editor.pushContent(webChat.emojiUbbToHtml(text));
                            editor.focus();
                            faceMenu.hide();
                            btn.removeClass('active');
                        });
                    }
                    faceMenu.show().css({'bottom': messageToolBar.outerHeight()})
                } else {
                    faceMenu.hide();
                }
            },
            'mouseleave': function() {//鼠标移出按钮 倒计时1秒内如果鼠标不进入表情菜单 则隐藏表情菜单
                faceMenuHideTimeId = setTimeout(function() {
                    faceMenu.hide();
                    faceBtn.removeClass('active');
                }, 1000);

            }
        }).bind('mousedown contextmenu', function() {return false;});
        faceMenu.off().on({
            'mouseleave': function() {
                faceMenu.hide();
                messageToolBar.find('#face_btn').removeClass('active');
            },
            'mouseenter': function() {//鼠标经过，移除按钮的倒计时事件
                clearTimeout(faceMenuHideTimeId);
            }
        });
        //统计字数
        this.countTextLength = function(contentText) {
            contentText = contentText || '';
            contentText = contentText.replace(/<([^>]*)>/ig, '');
            return contentText.length;
        };
        //加载旧的聊天记录
        this.loadLastMessage = function(flag)
        {
            flag = flag || 'top';//加载内容的方式 头部下拉加载、新内容加载时是底部下载
            var postData = {
                dialog_id: this_.dialogId,
                last_logid: this_.lastLoadMessageLogid,
                load_num: this_.message_pagesize //加载数量
            };
            var dialogContentUl = dialogContentBody.children();//ul
            loading(false);
            rePost(chatModelLoadMessage, postData, function(data) {
                noLoading();
                if(data.id != '0038') {
                    if(data.info) data.msg += data.info;
                    if(data.id == '0000') {
                        loginIn();
                        return;
                    }
                    msg(data.msg);
                } else {
                    var lastData = data.info;
                    var newHtml = '';
                    if(lastData.length>0) {
                        for(var i = 0 ; i< lastData.length; i++) {
                            var data_ = lastData[i];
                            this_.lastLoadMessageLogid = data_['l_id'];
                            var l_from_uid = data_['l_from_uid'];
                            var l_to_uid = data_['l_to_uid'];
                            if(parseInt(l_from_uid) == parseInt(webChat.myUserInfo.my_uid)) {
                                newHtml = this_.getContentTemp('my_message', {content: data_['l_content'], time: data_['time'] }) + newHtml;
                            } else {
                                newHtml = this_.getContentTemp('his_message', {content: data_['l_content'], time: data_['time'] }) + newHtml;
                            }
                        }
                    } else {
                        newHtml = "<li class='no_message'>还没有聊天记录</li>";
                        this_.needToGetOldMessage = false;
                    }
                    if(dialogContentUl.find('.no_message').length > 0) { //首次加载内容
                        dialogContentUl.find('.no_message').remove();
                        dialogContentUl.append(newHtml);
                    } else {
                        if(lastData.length>0) {
                            dialogContentUl.append(newHtml);
                        } else {
                            //下拉加载没有内容时 无须提示
                        }
                    }
                    if(flag == 'top') {
                        dialogContentBody.scrollTop(5, 200);
                    } else {
                        //对象要加[0]才能获取新的正确的高度
                        var scrollTop = dialogContentBody[0].scrollHeight;
                        dialogContentBody.scrollTop(scrollTop, 200);
                    }
                    this_.reHeightDialogBox();//每次加载出来好友，要重置最大高度
                }
            });
        };
        //创建编辑器
        var editor = lrEditor(
            messageToolBar.find('#chat_editor'),
            numBox,
            500,
            function() {
                webSocket.send('{"type":"writing", "room_id":"'+ webChat.roomId +'", "to_u_id":"'+ webChat.talk_to_uid +'", "from_uid":"'+ webChat.myUserInfo.my_uid +'"}');
            },
            messageToolBar.find('.submit')
        );
        //编辑器操作
        //提交聊天内容
        messageToolBar.off().on('submit', function(e) {
            e.preventDefault();
            var enterText = editor.getContent();
            enterText = this_.emojiHtmlToUbb(enterText);//过滤图片
            var textLen = this_.countTextLength(enterText);
            if(!enterText) {
                editor.focus();
                return;
            }
            if(textLen > this_.maxWordsNum) {
                msgTis('字数超出限制.请删减');
                return;
            }
            var postData = {
                'my_uid' : webChat.myUserInfo.my_uid,
                'dialog_id' : this_.dialogId,
                'content' : encodeURIComponent(enterText)
            };
            editor.pushContent('');
            rePost(chatModelPostMessage, postData, function(data) {
                if(data.id != '0113') {
                    if(data.info) data.msg += data.info;
                    if(data.id == '0033') {//身份已经切换
                        window.location.reload();
                        return;
                    }
                    if(data.id == '0000') {
                        loginIn();
                        return;
                    }
                    msg(data.msg);
                } else {
                    //php将非法字符过滤后 重新输出个本地
                    enterText = data.info;
                    //发送成功才提交给服务器 如果先是聊天服务器先成功，直接触发对方刷新列表 会导致更新列表的数据未是最新
                    webSocket.send('{"type":"say", "my_uid":"'+ webChat.myUserInfo.my_uid +'", "to_uid":"'+ webChat.talk_to_uid +'","content":"'+ encodeURIComponent(enterText) +'"}');
                    numBox.html(0);
                }
            });
        });
        //接受聊天内容
        //flag 'his_message'
        webChat.pushContent = function(flag, data_)
        {
            var newHtml = this_.getContentTemp(flag, data_);
            //如果当前在聊天对话框中
            if(dialogContentBody.length ==1) {
                var dialogContentUl = dialogContentBody.children();//获取第一个元素 即是ul
                if(dialogContentUl.find('.no_message').length > 0) { //首次加载内容
                    dialogContentUl.find('.no_message').remove();
                    dialogContentUl.append(newHtml);
                } else {
                    dialogContentUl.append(newHtml);
                }
                //聊天内容往下滚动2次，以查看最新信息。 对象要加[0]才能获取新的正确的高度
                setTimeout(function() {
                    var scrollTop = dialogContentBody[0].scrollHeight;
                    dialogContentBody.scrollTop(scrollTop, 200);
                    setTimeout(function() {
                        var scrollTop = dialogContentBody[0].scrollHeight;
                        dialogContentBody.scrollTop(scrollTop, 200);
                    }, 500)
                }, 200)
            }
        };

        //获取对方信息
        rePost(webChat.getUserInfoUrl, {uid:  webChat.talk_to_uid}, function(data) {
            if(data.id != '0038') {
                if(data.info) data.msg += data.info;
                if(data.id == '0000') {
                    loginIn();
                    return;
                }
                msg(data.msg);
            } else {
                if(!data.info.u_logo)  data.info.u_logo = webChat.nullLogo;
                this_.hisUserInfo = data.info;
            }
        });
        //发起聊天请求
        rePost(chatModelTalkUrl, {'to_uid':  webChat.talk_to_uid}, function(data) {
            if(data.id != '0038') {
                if(data.info) data.msg += data.info;
                if(data.id == '0000') {
                    loginIn();
                    return;
                }
                if(data.id == '0562') {//不能和自己聊天
                    webChat.removeChat();
                    msg(data.msg);
                    return;
                }
                msg(data.msg);
            } else {
                var responseData = {};
                eval("responseData = "+data.info);
                this_.dialogId = responseData.dialogNumber;//初始化聊天会话id
                webChat.myUserInfo.my_uid = responseData.my_uid;//初始化聊天会话id
                var login_data = '{"type":"talk_to_user", "room_id": "'+ webChat.roomId +'", "my_uid": "'+ webChat.myUserInfo.my_uid +'", "to_uid": "'+ webChat.talk_to_uid +'"}';
                //首次加载建立 webSocket 聊天连接
                if(!webSocket || webSocket.readyState != 1) {
                    webChat.connectWs(login_data);//首次连接
                } else {
                    webSocket.send(login_data);//发起聊天对话
                }
                webChat.pushNoReadNumToBtn();//更新按钮的未读信息
                //获取最近的10条聊天记录
                this_.loadLastMessage('bottom');
            }
        });
        //显示最大字数
        messageToolBar.find('#max_num').html(this_.maxWordsNum);
    });

</script>