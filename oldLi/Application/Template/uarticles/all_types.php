<div id="all_article_type_box"></div>
<script type="text/javascript">
    $('#all_article_type_box').append([
        makeBtn({
            value: "添加分类",
            'class': 'btn btn-success btn-xs',
            'click': "addClass(0)"
        }),
        makeTrees({
            items: {
                'data': <?=$class_list?>,//分类数据
                'value_key': 't_id',//值的下标【要求子数据必须也保持一致】
                'title_key': 't_title',//标题的下标 【要求子数据必须也保持一致】
                'son_key': 'sons',//子数据的字段名
                'li': {
                    //单元格显示内容
                    value: [
                        makeBtn({
                            value: "编辑",
                            'class': 'btn btn-default btn-xs',
                            'style': 'margin-left: 10px;',
                            'click': "editClass('{t_id}')"
                        }),
                        makeBtn({
                            value: "删除",
                            'class': 'btn btn-danger btn-xs',
                            'style': 'margin-left: 10px;',
                            'click': "delClass('{t_id}')"
                        }),

                    ]
                }
            },
        })
    ]);
    //添加分类
    function addClass(parentId) {
        parentId = parentId || 0;
        msgWin('添加分类','[url]/?s=uarticle/add_type&p_id='+ parentId, 360, 100);
    }
    //编辑分类
    function editClass(t_id) {
        msgWin('编辑分类','[url]/?s=uarticle/edit_type&t_id='+ t_id, 500, 160);
    }
    //删除子分类
    function delClass(tid) {
        if (confirm('您确定要删除该分类吗？')){
            postAndDone({
                'post_url': '/?s=uarticles/del_type&json=true',
                'post_data': {tid: tid},
                'success_value': '0039',
                'success_func': function () {
                   hideAllBox();
                }
            });
        }
    }

</script>