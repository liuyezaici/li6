<div id="modify_article_type_form"></div>
<script>
    $('#modify_article_type_form').append(makeForm({
        'left_width': 60,//左边宽度设置
        'url': '/?s=uarticles/modify_type&json&modify=<?=$modify?>&t_id=<?=$t_id?>',
        'success': {
            id: ['0043', '0113'],
            success_func: 'hideNewBox();'
        },
        'elements': [
            {
                em: '分类名',
                obj: [
                    makeInput({name:'t_title', width: 120, value: '<?=$t_title?>'}),
                    makeBtn({type:'submit', value:'提交保存', 'class': 'btn btn-info', left: 5})
                ]
            }
        ]
    }));
</script>
