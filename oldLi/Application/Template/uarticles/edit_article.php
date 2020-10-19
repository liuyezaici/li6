<style>
    /* 附件管理 */
    .manage_files {
        background-color: #fff;
    }
    .manage_files ul li {
        padding: 5px 0;
        display: inline-block;
        width: 25%;
    }
    .manage_files ul li .cover {
        margin: 0 5px;
        overflow: hidden;
        border: 1px solid #dedede;
        cursor: pointer;
        display: block;
        height: 56px;
    }
    .manage_files ul li .cover img {
        width: 100%;
        height: 100%;
    }
    .manage_files ul li .title {
        margin: 0 5px;
        padding: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }
    .manage_files ul li .title input {
        width: 100%;
        border: 1px solid #fff;
        text-indent: 2px;
    }
    .manage_files ul li .title input:hover {
        border: 1px solid #ccc;
    }
    .manage_files ul li .operat {
        display: block;
        padding-top: 5px;
    }
    .manage_files ul li .operat .item {
        width: 33.33%;
        display: inline-block;
        text-align: center;
        float: left;
    }
    .manage_files ul li .operat .glyphicon {
        color: #bbb;
    }
    .manage_files ul li .operat .glyphicon:hover {
        color: #999;
    }
    #modify_article_form .lr_editor {
        width: 555px;
        height: 120px;
    }
</style>
<div id="modify_article_form"></div>
<script>
    var editData = <?=$editData?>;
    var uploadUserHash = '<?=$uhash?>';
    var typeSelect = makeSelect({
        name: 'a_typeid',
        'text': '选择分类',
        value_key: 'value',
        title_key: 'title',
        value: '{a_typeid}',
        menu: {
            width: '400px',
            data_from: {
                url: '/?s=uarticles/get_all_types',
                success_key: 'id',
                success_value: '0038',
                data_key: 'info'
            }
        },
        li: {
            value: '{title}'
        }
    }); 
    $('#modify_article_form').append(makeForm({
        'url': '/?s=uarticles/submitEditArticle&modify=<?=$modify?>',
        data: editData,
        name: 'modifyForm',
        success_key: 'id',
        success_value: ['0043', '0113'],
        success_func: function (e) {
            if('<?=$modify?>'=='add') {
                hideAllBox();
                reFreshArticle();
            }
            msgTis(e.msg);
        },
        err_func: function (e) {
            msgTis(e.msg + ':'+ e.info);
        },
        value: [
            makeInput({
                name: 'a_id', value: '{a_id}', type: 'hidden'
            }),
            makeTable({
                tr_1: [
                    {
                        td: [
                            {
                                width: '100px',
                                value: makeSpan({value: '文章标题'})
                            },{
                                value: makeInput({name:'a_title', width: '455px', value: '{a_title}', blur: function (o, e) {
                                        var form = o.findParent('modifyForm');
                                        var aidObj = form.findName("a_id");
                                        var a_id = aidObj.value;
                                        var title = form.find("input[name='a_title']").val();
                                        if(title.length<=1)  return;
                                        if(a_id>0) return; //已经有分配编号 不需要再生成临时问题 比如编辑时
                                        rePost("/?s=uarticles/make_tmp_article", {a_title: title},function(data) {
                                            if(!data || !data.id) return;
                                            if($.inArray(data.id, ['0113']) == -1) {
                                                if(data.info) data.msg += data.info;
                                                if(data.id == '0000') {
                                                    loginIn();
                                                    return;
                                                }
                                                msg(data.msg,4);
                                            } else {
                                                aidObj.value=(data.info);
                                                reLoadThisArticleFujian();
                                            }
                                        });
                                    }})
                            }
                        ]
                    },
                    {
                        td: [
                            {
                                padding_top: '15px',
                                value: makeSpan({value: '文章分类'})
                            },{
                                padding_top: '15px',
                                value:  [
                                    typeSelect,
                                    makeBtn({value: '管理分类', 'class': 'btn btn-default', margin_left: '5px', click: 'viewAllTypes();'})
                                ]
                            }
                        ]
                    },
                    {
                        td: [
                            {
                                padding_top: '15px',
                                valign: 'top',
                                value: makeSpan({value: '正文'})
                            },{
                                padding_top: '15px',
                                value: makeDiv({
                                    id:'article_editor_parent',
                                    value: makeEditor({
                                        name:'a_content',
                                        'class': 'editor',
                                        width: '500px',
                                        height: '450px',
                                        type: 'editormd',
                                        editorObj: 'editArticleEditor',
                                        editorOpt: {
                                            id:'article_editor_parent',
                                            width   : "100%",
                                            height  : 440,
                                            syncScrolling : "single",
                                            toolbarIcons : function() {
                                                // Or return editormd.toolbarModes[name]; // full, simple, mini
                                                // Using "||" set icons align right.
                                                return ["bold", "quote", "h3", "del", "link", "list-ul", "list-ol",
                                                    "code",  "preformatted-text", "code-block", "table", "datetime","hr", "|", "image", "||", "watch", "preview"]
                                            },
                                            path: '/include/lib/editormd/lib/',// 如果加载不了其他扩展，则需要手动定义lib所在路径
                                        },
                                        value: '{a_content}'
                                    })
                                })
                            }
                        ]
                    },
                    {
                        td: [
                            {
                                padding_top: '10px',
                                value: makeSpan({value: '附件'})
                            },{
                                padding_top: '10px',
                                value: makeDiv({id: 'manage_article_fujian', 'class': 'manage_files', value: ''})
                            }
                        ]
                    },
                    {
                        td: [{}, {
                            padding_top: '10px',
                            value: makeBtn({type:'submit', value:'保存', 'class': 'btn btn-info'})
                        }]
                    }
                ]
            })
        ]
    })
    );
    //重新加载分类
    function refreshArticlesTypes() {
        console.log('typeSelect', typeSelect);
        typeSelect.menu.renewData();
    }

    var currentFilePage = 1;
    //附件翻页
    function articleFujianGotoPage(page) {
        reLoadThisArticleFujian(page);
    }
    //加载当前问题的附件
    function reLoadThisArticleFujian(page) {
        page = page || 1;
        currentFilePage = page;
        var form = $('#modify_article_form');
        var a_id = form.find("input[name='a_id']").val();
        if(!a_id || a_id==0) return;
        rePost("/?s=Uarticlefujian/load_article_fujians&page="+ page, {a_id: a_id},function(data) {
            if(!data || !data.id) return;
            if($.inArray(data.id, ['0038']) == -1) {
                if(data.info) data.msg += data.info;
                if(data.id == '0000') {
                    loginIn();
                    return;
                }
                msg(data.msg,4);
            } else {
                var responses = data.info;
                var filesData = responses.fileDatas;
                var filesPageMenu = responses.pageInfo;
                var fileHtml= '', tmpFileFid, tmpFileName, tmpFileSize, tmpFileGeshi, tmpFileOrder;
                fileHtml += "<ul>";
                var fileIcon = '';
                if(filesData.length>0) {
                    $.each(filesData, function (n, v) {
                        tmpFileFid = v['f_id'];
                        tmpFileName = v['f_filename'];
                        tmpFileGeshi = v['f_geshi'];
                        tmpFileSize = v['filesize'];
                        if($.inArray(tmpFileGeshi.toLowerCase(), ['gif','png','jpg', 'jpeg']) !=-1) {
                            fileIcon ="<img src=\""+ v['f_fileurl'] +"\" width='100%' onclick=\"enterThisImage('"+ v['f_fileurl'] +"');\" title='插入使用' />";
                        } else {
                            fileIcon ="<span onclick=\"enterThisFile('"+ v['f_fileurl'] +"');\">插入使用</span>";
                        }
                        fileHtml += "<li>" +
                            "<span class='cover' title='格式:"+ tmpFileGeshi +",大小:"+ tmpFileSize +"'> " +
                             fileIcon +
                            "</span>" +
                            "<span class='operat'> " +
                            "   <span class='item'><a href='javascript: void(0);' onclick=\"moveThisFile('"+ tmpFileFid +"', 'l');\" target='_self' title='向左移动'><i class='glyphicon glyphicon-arrow-left'></i></a></span>" +
                            "   <span class='item'><a href='javascript: void(0);' onclick=\"delThisFile('"+ tmpFileFid +"');\" target='_self' title='删除图片'><i class='glyphicon glyphicon-remove'></i></a></span>" +
                            "   <span class='item'><a href='javascript: void(0);' onclick=\"moveThisFile('"+ tmpFileFid +"', 'r');\" target='_self' title='向左移动'><i class='glyphicon glyphicon-arrow-right'></i></a></span>" +
                            "</span>" +
                            "</li>";
                    });
                } else {
                    fileHtml += "<li> </li>";
                }

                fileHtml += "</ul>" + filesPageMenu;
                var uploadBtn = makeBtn({
                    'class': 'btn alert-info',
                    value: '<em class="glyphicon glyphicon-picture"></em> 上传文件',
                    click: function () {
                        var uploadForm = batchUploadForm({
                            'url' : '/?s=Uarticlefujian/upload_files',
                            'post': {
                                'a_id': a_id,
                                'save_path': '<?=$savePath?>',
                                'path_safe_hash': '<?=\Func\Func::makeSafeUploadCode($savePath, $userId)?>',
                                'uhash': uploadUserHash
                            },
                            'one_finish': function () {

                            } ,
                            'all_finish': function () {
                                hideNewBox();
                                msgTis('上传完成');
                                reLoadThisArticleFujian();
                            },
                            'accept': {
                                title: 'file',
                                extensions: 'gif,jpg,jpeg,bmp,png,txt,html',
                                mimeTypes: 'image/jpg,image/jpeg,image/png,txt/plain'
                            }
                        });
                        msgView('上传文件', uploadForm, 820, 10, false);
                    }
                });
                form.find('#manage_article_fujian').html(fileHtml).append(makeDiv({'class': 'upload_btn_box', value: uploadBtn}));
            }
        });
    }
    //移动附件
    function moveThisFile(f_id, direction) {
        postAndDone({
            success_value: '0043',
            post_url: '/?s=Uarticlefujian&do=move_post_fujian',
            post_data: {f_id: f_id, direction: direction},
            load_bg: false,
            success_func: 'reLoadThisArticleFujian();',
            'msg': false

        });
    }
    //将附件插入到内容
    function enterThisFile(url) {
        var pushHtml = "["+ url +"]("+ url +")";
        a_content.editArticleEditor.insertValue(pushHtml);
    }
    //将图片插入到内容
    function enterThisImage(url) {
        var pushHtml = "![]("+ url +")";
        a_content.editArticleEditor.insertValue(pushHtml);
    }
    //删除附件
    function delThisFile(code) {
        postAndDone({
            success_value: '0039',
            post_url: '/?s=Uarticlefujian&do=remove_article_fujians',
            post_data: {fid: code},
            msg: false,
            success_func: 'reLoadThisArticleFujian();'
        });
    }
    reLoadThisArticleFujian();//加载附件
</script>