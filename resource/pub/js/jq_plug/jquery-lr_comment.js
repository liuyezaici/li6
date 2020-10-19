$.fn.makeCommentBox = function(options) {
    var this_ = $(this);
    this_.page = 1;
    this_.pageSize = 10;
    var obj = $(this);
    options = options || {};
    options['id'] = isUndefined(options['id']) ? 'lr_comment' : options['id'];
    options['my_uid'] = isUndefined(options['my_uid']) ? 0 : options['my_uid'];
    options['max_num'] = isUndefined(options['max_num']) ? 200 : options['max_num']; //最多输入字数
    options['name'] = isUndefined(options['name']) ? 'content' : options['name'];
    options['submit_url'] = isUndefined(options['submit_url']) ? '/?' : options['submit_url'];
    options['get_comment_url'] = isUndefined(options['get_comment_url']) ? '/?' : options['get_comment_url'];
    options['edit_comment_url'] = isUndefined(options['edit_comment_url']) ? '/?' : options['edit_comment_url'];
    options['del_comment_url'] = isUndefined(options['del_comment_url']) ? '/?' : options['del_comment_url'];
    options['success_value'] = isUndefined(options['success_value']) ? '0113' : options['success_value'];
    options['top_text'] = isUndefined(options['top_text']) ? '发表评论' : options['top_text'];
    options['default_text'] = isUndefined(options['default_text']) ? '还没有评论' : options['default_text'];
    options['msg_hide'] = isUndefined(options['msg_hide']) ? '' : options['msg_hide'];
    //自定义评论数据的输出字段
    options['fields'] = isUndefined(options['fields']) ? {
        'sid': 'c_id', //数据id
        'time': 'c_addtime',
        'content': 'c_content',
        'sons': 'sons', //子评论的下标
        'u_id': 'c_uid',
        'u_nick': 'u_nick',
        'u_logo': 'u_logo'
    } : options['fields'];
    this_.options = options;
    var box = $('<div id="'+ options['id'] +'">' +
        '<div class="comment_list"></div>' +
        '<div class="comment_post_bar"> '+ options['top_text'] +' <div class="editor"></div> <div class="num_box"> 字数:<span class="num">0</span>/'+ options['max_num'] +' </div> <input type="button" value="提交内容" class="btn btn-info post_btn" /></div>' +
        '</div>');
    this_.commentListObj = box.find('.comment_list');
    //评论翻页

    //评论内容转json格式
    this_.commentJsonToHtml = function(jsonData) {
        var commentData = jsonData[0];
        var pageData = jsonData[1];
        var commentLens = pageData.total;
        var totalPage = commentLens % this_.pageSize > 0 ? parseInt(commentLens / this_.pageSize) +1 : parseInt(commentLens / this_.pageSize);
        var commentHTML = '<ul>';
        var editHtml = '';//编辑按钮
        var dField = this_.options['fields'];
        for (var i = 0; i < commentData.length; i++) {
            if(parseInt(commentData[i][dField['u_id']]) == options['my_uid']) {
                editHtml = '<span class="edit_btn" data-id="'+ commentData[i][dField['sid']] +'">[编辑]</span> ' +
                    '<span class="del_btn" data-id="'+ commentData[i][dField['sid']] +'">[删除]</span> ';
            }
            commentHTML += '<li>'+
                '<div class="user_cover"><img src="'+ commentData[i][dField['u_logo']] +'" height="35" /> </div>'+
                '<div class="right_u_content"> ' +
                '   <div class="u_top"> ' +
                '       <span class="u_nick">'+ commentData[i][dField['u_nick']] +'</span> '+
                '       <span class="time">'+ commentData[i][dField['time']] +'</span> '+
                         editHtml +
                '   </div>'+
                '   <div class="u_content"> ' +
                '       '+ commentData[i][dField['content']] +
                '   </div>'+
                '</div>'+
                '</li>'
        }
        commentHTML += '</ul>';
        this_.commentListObj.html(commentHTML);
        //绑定编辑事件
        this_.commentListObj.find('.edit_btn').click(function() {
            var btn = $(this);
            msgWin('编辑评论', options['edit_comment_url'] + btn.attr('data-id'), 1)
        });
        //绑定删除事件
        this_.commentListObj.find('.del_btn').click(function() {
            var btn = $(this);
            postAndDone({
                'post_url': options['del_comment_url'] + btn.attr('data-id'),
                'success_value': '0039',
                'success_func': 'refreshThisComment();'
            });
        });
        var pageObj = $('#comments_pages');
        if(pageObj.length > 0) {
            pageObj.remove();//如果已经存在 要清空所有子内容
        }
        this_.commentListObj.after('<div class="pages" id="comments_pages"></div>');
        pageObj = $('#comments_pages');
        //计算分页导航
        var fromPage = this_.page-5;
        if(fromPage<1) fromPage = 1;
        for (var i_=fromPage; i_ < Math.min((fromPage + 10), totalPage+1); i_++){
            if (i_ == this_.page){
                pageObj.append("<span class='page current' data-page=\""+ i_ +"\">"+ i_ +"</span>");
            }else{
                pageObj.append("<span class='page' data-page=\""+ i_ +"\">"+ i_ +"</span>");
            }
        }
        if (i_ < totalPage){
            pageObj.append("<span class='page' data-page=\""+ totalPage +"\">尾页</span>");
        }
        pageObj.append("<span class='total'>共"+ commentLens +"个评论</span>");
        pageObj.find('.page').on('click', function() {
            var page_ = $(this).attr('data-page');
            this_.commentGotoPage(page_);
        });
        return pageObj;
    };
    //翻页
    this_.commentGotoPage =function(page) {
        this_.page = page;
        rePost(this_.options['get_comment_url'], {'page': page},function(data) {
            if(!data || !data.id) return;
            if($.inArray(data.id, ['0038']) == -1) {
                if(data.info) data.msg += data.info;
            } else {
                this_.commentJsonToHtml(data.info);
            }
        });
    };
    //刷新评论
    this_.refreshComment = function() {
        this_.commentGotoPage(this_.page);
    };
    //父页面 编辑评论 调取刷新评论
    window.refreshThisComment = this_.refreshComment;
    if(isUndefined(options['get_comment_url'])) {
        this_.commentListObj.append('<li class="no_data">'+ options['default_text'] +'</li>');
    } else {
        this_.commentGotoPage(1);
    }
    obj.append(box);
    var numBox = box.find('.num');
    var postBtn = box.find('.post_btn');
    var editor = lrEditor(box.find('.editor'), numBox, options['max_num'], '', postBtn);
    var postData = {};
    postBtn.click(function() {
        postData[options['name']] = editor.getContent();
        rePost(options['submit_url'], postData,function(data) {
            if(!data || !data.id) return;
            if(!$.isArray(options['success_value'])) options['success_value'] = [options['success_value']];
            if($.inArray(data.id, options['success_value']) == -1) {
                if(data.info) data.msg += data.info;
                msg(data.msg);
            } else {
                this_.refreshComment();
                editor.pushContent('');
            }
        });
    });
};