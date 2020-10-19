// ajax上传文件插件

jQuery.extend({
    handleError: function( s, xhr, status, e )      {
        // If a local callback was specified, fire it
        if ( s.error ) {
            s.error.call( s.context || s, xhr, status, e );
        }

        // Fire the global callback
        if ( s.global ) {
            (s.context ? jQuery(s.context) : jQuery.event).trigger( "ajaxError", [xhr, s, e] );
        }
    },
    createUploadIframe: function(frameId, uri)
    {

        // if(window.ActiveXObject) {
        //     var io = document.createElement('<iframe id="' + frameId + '" name="' + frameId + '" />');
        //     if(typeof uri== 'boolean'){
        //         io.src = 'javascript:false';
        //     }
        //     else if(typeof uri== 'string'){
        //         io.src = uri;
        //     }
        // }
        //向下兼容
        if(window.ActiveXObject) {
            if(jQuery.browser.version=="9.0" || jQuery.browser.version=="10.0"){
                var io = document.createElement('iframe');
                io.id = frameId;
                io.name = frameId;
            }else if(jQuery.browser.version=="6.0" || jQuery.browser.version=="7.0" || jQuery.browser.version=="8.0"){
                var io = document.createElement('<iframe id="' + frameId + '" name="' + frameId + '" />');
                if(typeof uri== 'boolean'){
                    io.src = 'javascript:false';
                }
                else if(typeof uri== 'string'){
                    io.src = uri;
                }
            }
        }
        else {
            var io = document.createElement('iframe');
            io.id = frameId;
            io.name = frameId;
        }
        io.style.position = 'absolute';
        io.style.top = '-1000px';
        io.style.left = '-1000px';

        document.body.appendChild(io);

        return io
    },

    ajaxFileUpload: function(s) {
        // TODO introduce global settings, allowing the client to modify them for all requests, not only timeout
        s = jQuery.extend({}, jQuery.ajaxSettings, s);
        var id = new Date().getTime();
        var uploadForm = {};
        var tmpLoading = null;
        var frameId = 'jUploadFrame' + id;
        //每次上传成功都会移除form的 防止影响input所在订单表单
        var formId = 'jUploadForm' + id;
        var postData = s.data || null;
        var loadingUrl = s.loadingUrl || '';
        if(loadingUrl) tmpLoading = $('<img class="loading_gif" src="'+ loadingUrl +'">');
        uploadForm = $('<form  action="'+  s.url +'" target="'+  frameId +'" method="POST" ' +
            'name="' + formId + '" style="position: absolute; top: -1000px; left: -1000px;" id="' + formId + '" enctype="multipart/form-data"></form>');
        if(tmpLoading) s.fileInput.after(tmpLoading);//先加个loading
        var inputPrev = s.fileInput.prev();
        var inputParent = s.fileInput.parent();
        $(document.body).append(s.fileInput); //移除input进body后面
        s.fileInput.wrap(uploadForm);
        uploadForm = $('#' + formId); //必须重新获取表单 因为当前的文件input内容才刚发生改变
        if(postData) {
            var tmpInput = '';
            $.each(postData, function (key_, val_) {
                tmpInput = $('<input type="hidden" name="'+ key_+'" value="'+ val_ +'" />');
                uploadForm.append(tmpInput);
            });
        }
        jQuery.createUploadIframe(frameId, s.secureuri);
        // Watch for a new set of requests
        if ( s.global && ! jQuery.active++ )
        {
            jQuery.event.trigger( "ajaxStart" );
        }
        var requestDone = false;
        // Create the request object
        var xml = {};
        if ( s.global )
            jQuery.event.trigger("ajaxSend", [xml, s]);
        // Wait for a response to come back
        var uploadCallback = function(isTimeout)
        {
            var io = document.getElementById(frameId);
            try
            {
                if(io.contentWindow)
                {
                    xml.responseText = io.contentWindow.document.body?io.contentWindow.document.body.innerHTML:null;
                    xml.responseXML = io.contentWindow.document.XMLDocument?io.contentWindow.document.XMLDocument:io.contentWindow.document;

                }else if(io.contentDocument)
                {
                    xml.responseText = io.contentDocument.document.body?io.contentDocument.document.body.innerHTML:null;
                    xml.responseXML = io.contentDocument.document.XMLDocument?io.contentDocument.document.XMLDocument:io.contentDocument.document;
                }
            }catch(e)
            {
                jQuery.handleError(s, xml, null, e);
            }
            //是否完成回调
            var callFinish = false;
            if ( xml || isTimeout == "timeout")
            {
                requestDone = true;
                var status;
                try {
                    status = isTimeout != "timeout" ? "success" : "error";
                    // Make sure that the request was successful or notmodified
                    // console.log('status:'+ status);
                    if ( status != "error" )
                    {
                        // console.log('status2:'+ status);
                        // console.log(xml);
                        // console.log(s.dataType);
                        // process the data (runs the xml through httpData regardless of callback)
                        var data = jQuery.uploadHttpData( xml, s.dataType );
                        // If a local callback was specified, fire it and pass it the data
                        // console.log(s);
                        // console.log(data);
                        // console.log('s.finish');
                        // console.log(s.finish);
                        if ( s.finish ) {
                            s.finish( data, status );
                        } else {
                            console.log('!s.finish');
                            console.log(s);
                        }

                        // Fire the global callback
                        if( s.global )
                            jQuery.event.trigger( "ajaxSuccess", [xml, s] );
                    } else
                        jQuery.handleError(s, xml, status);
                } catch(e)
                {
                    status = "error";
                    jQuery.handleError(s, xml, status, e);
                }

                // The request was completed
                // if( s.global )
                //     jQuery.event.trigger( "ajaxComplete", [xml, s] );
                // // Handle the global AJAX counter
                // if ( s.global && ! --jQuery.active )
                //     jQuery.event.trigger( "ajaxStop" );
                // console.log(s);
                // Process result
                // if(!callFinish) {
                //     if ( s.success ) {
                //         s.success( data, status );
                //     } else if ( s.finish ) {
                //         console.log('call finish');
                //         console.log( s.finish);
                //         s.finish();
                //     } else if ( s.complete ) {
                //         s.complete(xml, status);
                //     }
                // }
                jQuery(io).unbind();
                setTimeout(function()
                {	try {
                    $(io).remove();
                    if(tmpLoading) tmpLoading.remove();//移除loading
                    if(inputPrev.length>0) {
                        inputPrev.after(s.fileInput);
                    } else {
                        inputParent.append(s.fileInput);
                    }
                    $(uploadForm).remove();//再移除表单
                } catch(e)
                {
                    jQuery.handleError(s, xml, null, e);
                }
                }, 100);
                xml = null;
            }
        };
        // Timeout checker
        if ( s.timeout > 0 )
        {
            setTimeout(function(){
                // Check to see if the request is still happening
                if( !requestDone ) uploadCallback( "timeout" );
            }, s.timeout);
        }
        try
        {
            // console.log(s.fileInput.val());
            // console.log(uploadForm);
            $(uploadForm).submit();
        } catch(e)
        {
            jQuery.handleError(s, xml, null, e);
        }
        if(window.attachEvent){
            document.getElementById(frameId).attachEvent('onload', uploadCallback);
        }
        else{
            document.getElementById(frameId).addEventListener('load', uploadCallback, false);
        }
        return {abort: function () {}};

    },

    uploadHttpData: function( r, type ) {
        var data = !type;
        data = type == "xml" || data ? r.responseXML : r.responseText;
        // If the type is "script", eval it in global context
        if ( type == "script" )
            jQuery.globalEval( data );
        // Get the JavaScript object, if JSON is used.
        if ( type == "json" )
        {
            // If you add mimetype in your response,
            // you have to delete the '<pre></pre>' tag.
            // The pre tag in Chrome has attribute, so have to use regex to remove
            var data = r.responseText;
            // console.log(data);
            var reg_ = /^<pre.*?>(.*?)<\/pre>$/i;
            if(reg_.test(data)) {
                // console.log('has pre');
                var am = reg_.exec(data);
                //this is the desired data extracted
                var data = (am) ? am[1] : "";    //the only submatch or empty
                eval( "data = " + data );
            } else {
                // console.log('not has pre');
                eval( "data = " + data );
            }
            // console.log(data);
        }
        // evaluate scripts within html
        if ( type == "html" )
            jQuery("<div>").html(data).evalScripts();
        //alert($('param', data).each(function(){alert($(this).attr('value'));}));
        return data;
    }
});