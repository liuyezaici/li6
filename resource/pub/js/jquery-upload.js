//webupload 批量上传封装 需要插件支持
/*
 * $('#append_box').append(batchUploadForm({
 'post': {
 'workPath' : '/include/lib/webuploader',
 'url' : '/include/lib/webuploader/upload.php',
 'save_path': '/upload/images/',
 'pathSafeHash': '/upload/images/',
 'uhash': '/upload/images/'
 }
 }));
 * */
window.batchUploadForm=function(options) {
    options = options ||[];
    var defaultOption = {
        'workPath' : '/include/lib/webuploader',
        'url' : '/include/lib/webuploader/upload.php',
        'id': 'diy_batch_uploader',
        'class': 'diy_batch_uploader',
        'post': {},
        auto: false,
        'one_finish': function (data) {
            if(data.id !=='0388') {
                if(data.info) data.msg += data.info;
                msg(data.msg);
            } else {
                hideNewBox();
                msgTis(data.msg);
            }
        },
        'all_finish': function () {

        },
        'accept': {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png,txt,html',
            mimeTypes: 'image/jpg,image/jpeg,image/png,txt/plain'
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
                            <div class="btn-group btn-group-sm">\
                                <div id="'+ pickBtn2Id +'" class="btn-group btn-group-sm pick_btn"></div>\
                                <button class="uploadBtn btn btn-success">开始上传</button>\
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
        uploader;// WebUploader实例
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

            // 压根就没有安转。
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
    $('body').append(wrapObj);
    //等待按钮渲染完成，再生成准确的点击层
    setTimeout(function () {
        // 实例化
        uploader = WebUploader.create({
            pick: {
                id: '#'+pickBtn1Id,
                'class': 'btn btn-info',
                btnText: '点击选择文件'
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
            fileNumLimit: 300,//最多300个文件
            resize: false,// 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
            fileSizeLimit: 5000000 * 1024 * 1024 * 1024,    // 200 M
            fileSingleSizeLimit: 100000 * 1024 * 1024 * 1024    // 图片无须限制大小  M
        });
        // 添加“添加文件”的按钮，
        uploader.addButton({
            id: '#'+pickBtn2Id,
            'class': 'btn btn-default',
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
                    // use jquery animate to rotation
                    // $({
                    //     rotation: rotation
                    // }).animate({
                    //     rotation: file.rotation
                    // }, {
                    //     easing: 'linear',
                    //     step: function( now ) {
                    //         now = now * Math.PI / 180;

                    //         var cos = Math.cos( now ),
                    //             sin = Math.sin( now );

                    //         wrapObj.css( 'filter', "progid:DXImageTransform.Microsoft.Matrix(M11=" + cos + ",M12=" + (-sin) + ",M21=" + sin + ",M22=" + cos + ",SizingMethod='auto expand')");
                    //     }
                    // });
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
                text = '选中' + fileCount + '个文件，共' +
                    WebUploader.formatSize(fileSize) + '。';
            } else if (state === 'confirm') {
                stats = uploader.getStats();
                if (stats.uploadFailNum) {
                    text = '已成功上传' + stats.successNum + ' ，' +
                        stats.uploadFailNum + ' 上传失败，<a class="retry" href="#">重新上传</a>失败 或<a class="ignore" href="#">忽略</a>'
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
                        // 没有成功的 ，重设
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