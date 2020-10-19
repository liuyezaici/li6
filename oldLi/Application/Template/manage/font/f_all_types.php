<div id="all_article_type_box"></div>
<script type="text/javascript">
    $('#all_article_type_box').append(makeClassTrees({
        'data': <?=$class_list?>,//分类数据
        'key_index': 't_id',//值的下标【要求子数据必须也保持一致】
        'title_index': 't_title',//标题的下标 【要求子数据必须也保持一致】
        'level': 1,//共有层级 最后一层不显示添加按钮。
        'click': {
            'add': 'addClass',
            'edit': 'editClass',
            'del': 'delClass'
        }
    }));
    //删除子分类
    function delClass(tid) {
        if (confirm('您确定要删除该分类吗？')){
            postAndDone({
                'post_url': '/?s=uarticles/del_type&json=true',
                'post_data': {tid: tid},
                'id': '0039',
                'success_func': 'hideAllbox();viewAllTypes()'
            });
        }
    }

</script>