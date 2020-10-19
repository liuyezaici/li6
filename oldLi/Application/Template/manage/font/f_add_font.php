<style>
    #modify_article_form .editor {
        padding: 4px;
        line-height: 22px;
    }
    /* 附件管理 */
    #manage_article_fujian {
        border: 1px solid #ddd;
    }
    #manage_article_fujian ul li {
        padding: 5px 0;
    }
    #manage_article_fujian ul li.top_ {
        background-color: #efefef;
    }
    #manage_article_fujian ul li span {
        display: inline-block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
    }
    #manage_article_fujian ul li span.order {
        width: 55px;
        margin-left: 5px;
    }
    #manage_article_fujian ul li span.code {
        width: 65px;
    }
    #manage_article_fujian ul li span.title {
        width: 223px;
    }
    #manage_article_fujian ul li span.title input {
        width: 232px;
        border: 1px solid #fff;
        padding: 2px;
    }
    #manage_article_fujian ul li span.title input:hover {
        border: 1px solid #ccc;
    }
    #manage_article_fujian ul li span.geshi {
        width: 55px;
    }
    #manage_article_fujian ul li span.size {
        width: 80px;
    }
    #manage_article_fujian ul li span.order input {
        width: 42px;
        border: 1px solid #fff;
        padding: 2px;
    }
    #manage_article_fujian ul li span.order input:hover {
        border: 1px solid #ccc;
    }
    #manage_article_fujian ul li span.operat {
        width: 165px;
    }
    #manage_article_fujian ul li span.operat .blue {
        margin-right: 10px;
    }
    #manage_article_fujian ul li span.operat .blue.active {
        color: #666;
    }
    #modify_article_form .lr_editor {
        width: 555px;
        height: 120px;
    }
</style>
<div id="modify_article_form"></div>
<script>
    $('#modify_article_form').append(makeForm({
        'left_width': 80,//左边宽度设置
        'url': '/?s=uarticles/edit_article&modify=<?=$modify?>',
        'hide_inputs': [{name: 'a_id', value: '<?=$a_id?>'}],
        'success': {
            id: ['0043', '0113'],
            success_func: 'hideNewBox();reFreshArticle();'
        },
        'elements': [
            {
                em: '文章标题',
                obj: makeInput({name:'a_title', width: 355, value: '<?=addslashes($a_title)?>', blur: "submitTmpArticle"})
            },
            {
                em: '文章分类',
                obj: makeDiv({id:'tmp_type_box'})
            },
            {
                em: '正文',
                obj: makeEditor({name:'a_content', 'class': 'editor', width:600, height: 450, content: '<?=urlencode($a_content)?>', full: true, xheditor: 'editArticleEditor'})
            },
            {
                em: '图片附件',
                obj: makeDiv({id: 'manage_article_fujian', value: '请先输入资源的标题'})
            },
            {
                em: '',
                obj: [
                    makeBtn({type:'submit', value:'提交发布', 'class': 'btn btn-info'})
                ]
            }
        ]
    }));
    //重新加载分类
    function refreshArticlesTypes() {
        rePost('/?s=uarticles/get_all_types', {},function(data) {
            if(!data || !data.id) return;
            if(data.id != '0038') {
                if(data.info) data.msg += data.info;
                if(data.id == '0000') {
                    loginIn();
                }
            } else {
                var allTypes = data.info;
                var newTypesObj = makeSelectMenu({
                    name: 'a_typeid',
                    'default_text': '选择分类',
                    selected: '<?=$a_typeid?>',
                    menu_data: allTypes
                })[0];
                $('#tmp_type_box').html('').append(newTypesObj).append( makeBtn({value: '管理分类', left: 5, click: 'viewAllTypes();'}) );
            }
        });
    }
    //发布资源时，标题移出，自动添加临时的资源  方便以下的附件上传
    function submitTmpArticle() {
        var form = $('#modify_article_form');
        var aidObj = form.find("input[name='a_id']");
        var a_id = aidObj.val();
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
                aidObj.val(data.info);
                reLoadThisArticleFujian();
            }
        });
    }

    //上传图片
    function uploadArticleFujian() {
        var form = $('#modify_article_form');
        var a_id = form.find("input[name='a_id']").val();
        if(a_id.length<1) {
            msg('缺少文章编号！');
            return;
        }
        var showHtml='<div id="container_article_fujians">' +
            '<div id="ossfile_manage_article_fujians">你的浏览器不支持flash,Silverlight或者HTML5！</div> ' +
            '<a id="selectfiles_manage_article_fujians" href="javascript:void(0);" target="_self" class="upload_btn">选择本地文件</a>' +
            '<a id="postfiles_manage_article_fujians" href="javascript:void(0);" target="_self" class="btn btn-success">开始上传</a></div>';
        messageView('上传图片', showHtml, 480, 200);
        makeUploadArticleFujianEven(a_id, 'container_article_fujian', 'ossfile_manage_article_fujians', 'selectfiles_manage_article_fujians', 'postfiles_manage_article_fujians');
    }
    //附件翻页
    function articleFujianGotoPage(page, nu) {
        reLoadThisArticleFujian(page);
    }
    //加载当前问题的附件
    function reLoadThisArticleFujian(page) {
        page = page || 1;
        var form = $('#modify_article_form');
        var a_id = form.find("input[name='a_id']").val();
        if(!a_id || a_id==0) {
            return;
        }
        rePost("/?s=Uarticlefujian&do=load_article_fujians&page="+ page, {a_id: a_id},function(data) {
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
                var fileHtml= '', tmpFileCode, tmpFileName, tmpFileSize, tmpFileGeshi, tmpFileOrder;
                fileHtml += "<ul>";
                fileHtml += "<li class='top_'>" +
                    "<span class='order'> 排序 </span>" +
                    "<span class='code'> 预览 </span>" +
                    "<span class='title'> 图片名 </span>" +
                    "<span class='geshi'> 格式 </span>" +
                    "<span class='size'> 大小 </span>" +
                    "<span class='operat'> 操作 <a href=\"javascript: void(0);\" class=\"btn upload_files_btn\" target=\"_self\" onclick=\"uploadArticleFujian();\">上传图片</a> </span>" +
                    "</li>";
                if(filesData.length>0) {
                    $.each(filesData, function (n, v) {
                        tmpFileOrder = v['f_order'];
                        f_id = v['f_id'];
                        tmpFileName = v['f_filename'];
                        tmpFileGeshi = v['f_geshi'];
                        tmpFileSize = v['filesize'];
                        fileHtml += "<li>" +
                            "<span class='order'><input type='text' value='"+ tmpFileOrder +"' data-fid='"+ f_id +"' data-old='"+ tmpFileOrder +"' onblur='editFileOrder($(this));' maxlength='4' /></span>" +
                            "<span class='code'> <a href='"+ v['downUrl'] +"'><img src=\""+ v['downUrl'] +"\" height='32' /></a> </span>" +
                            "<span class='title'><input type='text' value='"+ tmpFileName +"' data-fid='"+ f_id +"' data-old='"+ tmpFileName +"' onblur='editFileName($(this));' maxlength='50' /></span>" +
                            "<span class='geshi'>"+ tmpFileGeshi +"</span>" +
                            "<span class='size'>"+ tmpFileSize +"</span>" +
                            "<span class='operat'> <a href='javascript: void(0);' onclick=\"enterThisFile($(this),'"+ tmpFileCode +"',"+ v['is_img'] +",'"+ v['downUrl'] +"');\" target='_self' class='blue'>插入到内容</a>" +
                            "<a href='javascript: void(0);' onclick=\"delThisFile('"+ tmpFileCode +"');\" target='_self' class='red'>删除</a></span>" +
                            "</li>";
                    });
                } else {
                    fileHtml += "<li> &nbsp; 没有附件</li>";
                }

                fileHtml += "</ul>" + filesPageMenu;
                form.find('#manage_article_fujian').html(fileHtml);
            }
        });
    }
    //下载附件
    function downThisFile(fid) {
        msgWin('下载文件', '/?s=Uarticlefujian/form&form=down_manage_article_fujian&fid='+ fid, 480, 200);
    }
    //快速修改附件名字
    function editFileName(input) {
        var fid = input.attr('data-fid');
        var old = input.attr('data-old');
        var val = input.val();
        if(old==val) return;
        input.attr('data-old', val);
        postAndDone({
            'post_url': '/?s=Uarticlefujian&do=edit_article_fujian_title',
            'post_data': {'fid': fid, 'title': val},
            'id': '0043',
            'msg': false
        });
    }
    //将附件插入到内容
    function enterThisFile(obj, code, isImg, url) {
        var pushHtml = "";
        if(!isImg) {
            pushHtml = "\r\n[file:"+ code +"]";
        } else {
            pushHtml = "\r\n<img src='"+ url +"' onload='if(this.width>500) this.width=500;'/>";
        }
        editArticleEditor.pasteHTML(pushHtml);
        obj.addClass('active');
    }
    //快速修改附件名字
    function editFileOrder(input) {
        var fid = input.attr('data-fid');
        var old = input.attr('data-old');
        var val = parseInt(input.val());
        if(old==val) return;
        input.attr('data-old', val);
        postAndDone({
            'post_url': '/?s=Uarticlefujian&do=edit_file_order',
            'post_data': {'fid': fid, 'ord_id': val},
            'success_value': '0043',
            'success_msg': false,
            'success_func': 'reLoadThisArticleFujian();'
        });
    }
    //删除附件
    function delThisFile(code) {
        msgConfirm('您确定要删除此附件吗', '确定', '再考虑考虑', "confirmDelThisShareFujian('"+ code +"');", 'hideNewBox();');
    }
    //确定执行：删除文件
    function confirmDelThisShareFujian(fid) {
        hideNewBox();
        postAndDone({
            id: '0043',
            post_url: '/?s=Uarticlefujian&do=remove_article_fujians',
            post_data: {fid: fid},
            success_func: 'reLoadThisArticleFujian();'
        });
    }

    reLoadThisArticleFujian();//加载附件
    refreshArticlesTypes();
</script>