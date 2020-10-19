<script src="/resource/pub/js/jquery-upload.js">
</script>
<script>
    var pageOpt = {
        page: <?=$pageInfo['pagenow']?>,
        total: <?=$pageInfo['total']?>,
        pagesize: <?=$pageInfo['pagesize']?>,
        click: function (obj, newPage) {
            searchForm.findName("page").value=(newPage);
            reFreshArticle();
        }
    };
    var searchForm = makeForm({
        'class': 'diy_search_top',
        submit: function(obj) {
            var uri = obj.serialize();
            ajaxOpen('/?s=uarticles&'+ uri );
        },
        value: [
            makeInput({name: 'page', type: 'hidden', value: '<?=$page?>'}),
            makeSpan({
                padding: '0 22px 0 10px',
                value: '文章管理'
            }),
            makeInput({name: 'searchkey', place: '文章标题', 'class': 'no_radius_right', width: '200px', value: '<?=$searchkey?>'}),
            makeBtn({
                'value': '搜索', 'type': 'submit','class': 'btn btn-info no_radius_left'
            }),
            makeBtn({
                'value': '写文章', margin_left: '122px','type': 'button', 'class': 'btn btn-success', click: function () {
                    msgWin('添加文章','[url]/?s=uarticles/add_article', 800, 50);
                }
            }),
            makeBtn({
                'value': '文章分类', margin_left: '12px','type': 'button', 'class': 'btn btn-info', click: function () {
                    viewAllTypes();
                }
            })
        ]
    });
    var dataHtmlObj = makeTable({
        'class': 'table table-bordered',
        data:  <?=$listResult?>,
        tr_1:
            {
                td: [
                    {
                        width: '120px',
                        'style': 'padding-left: 20px;',
                        value: [
                            makeSpan({
                                value: '编号'
                            })
                        ]
                    },
                    {
                        value: [
                            makeSpan({
                                value: '标题'
                            })
                        ]
                    },
                    {
                        value: [
                            makeSpan({
                                value: '人气'
                            })
                        ]
                    },
                    {
                        value: [
                            makeSpan({
                                value: '操作'
                            })
                        ]
                    }
                ]
            },
        tr:
            {
                td: [
                    {
                        show: '{a_id}',
                        value: [
                            makeSpan({
                                'style': 'padding-left: 20px;',
                                value: '{a_id}'
                            })
                        ]
                    },
                    {
                        show: '{a_id}',
                        value: [
                            makeA({
                                href: '/?s=article/read/&id={a_id}',
                                value: '{a_title}'
                            })
                        ]
                    },
                    {
                        show: '{a_id}',
                        value: [
                            makeSpan({
                                value: '{a_hit}'
                            })
                        ]
                    },
                    {
                        show: '{a_id}',
                        value: [
                            makeA({
                                value: '编辑',
                                target: '_self',
                                'class': 'btn btn-xs btn-info no_radius_right',
                                click: "editArticle({a_id});"
                            }),
                            makeA({
                                value: '删除',
                                target: '_self',
                                'class': 'btn btn-xs btn-warning no_radius_left',
                                click: "delArticle({a_id});"
                            })
                        ]
                    }
                ]
            },
        tr_default: [
            {
                show: '{{this.length}==0}',
                td: [
                    {
                        colspan: '10',
                        value: '没有搜索结果'
                    }
                ]
            }
        ],
    });
    var pageObj = makePage(pageOpt);

    //删除文章
    function delArticle(articleid) {
        msgConfirm('你确定要删除此文章吗？', '确定', '取消', function () {
           hideNewBox();
            postAndDone({
                post_url: '/?s=uarticles/del_article',
                post_data: {a_id: articleid},
                success_key: 'id',
                success_value: '0039',
                success_func: 'hideNewBox();reFreshArticle();msgTis("删除成功");'
            });
        });
    }
    //编辑文章图片
    function editArticlePics(articleid) {
        hideNewBox();
        msgWin('编辑文章图片','[url]/?s=uarticles/article_pic_list&a_id='+ articleid, 1200, 1);
    }
    //编辑文章信息
    function editArticle(articleid) {
        msgWin('编辑文章','[url]/?s=uarticles/edit_article&a_id='+ articleid, 1200, 1);
    }
    //刷新列表
    function reFreshArticle() {
        $(searchForm[0]).submit();
    }

    //文章分类
    function viewAllTypes() {
        msgWin('文章分类管理','[url]/?s=uarticles/all_types', 580, 1);
    }
    $('#root_right .right_content').append(searchForm).append(dataHtmlObj).append(pageObj);
</script>