<div id="modify_article_type_form"></div>
<script>
    $('#modify_article_type_form').append(makeForm({
        'url': '/?s=uarticles/modify_type&json&modify=<?=$modify?>&t_id=<?=$t_id?>',
        'success_value': ['0043', '0113'],
        'success_key': 'id',
        success_func: function () {
            hideNewBox();
            hideNewBox();
        },
        'value': makeDiv({
                'class': 'input-group',
                value: [
                    makeSpan({'class': 'btn btn-default', value: '分类名'}),
                    makeInput({name:'t_title', width: '150px', value: '<?=$t_title?>'}),
                    makeBtn({type:'submit', value:'保存', 'class': 'btn btn-info'})
                ]
        })
    }));
</script>
