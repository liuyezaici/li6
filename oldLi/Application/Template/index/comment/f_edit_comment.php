<style>
    #modify_comment_form {
        margin: -20px -15px 0 -15px;
    }
    #modify_comment_form .editor {
        padding: 4px;
        line-height: 22px;
    }
</style>
<div id="modify_comment_form"></div>
<script>
    $('#modify_comment_form').append(makeForm({
        'left_width': 65,//左边宽度设置
        'url': '/?s=comment&do=edit_comment&sid=<?=$c_id?>',
        'success': {
            id: ['0043'],
            success_func: 'hideNewBox();refreshThisComment();',
        },
        'elements': [
            {
                em: '评论内容',
                obj: makeEditor({name:'c_content', 'class': 'editor', width: 350, height: 160, content: '<?=urlencode($c_content)?>', full: false})
            },
            {
                em: '',
                obj: [
                    makeBtn({type:'submit', value:'提交保存', 'class': 'btn btn-info'})
                ]
            }
        ]
    }));
</script>
 