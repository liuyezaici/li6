<style>
    .submit_box {
        text-align: center;
    }
    .submit_box .submit_add_btn {
        margin: 0 auto;
    }
</style>
<script>
    var searchFontForm = makeSearchForm({
        'top_title': '文字管理',
        'url': '/?s=ufonts',
        'items': //筛选传参 [时间、状态、搜索类型等]
            [
                {
                    'obj': makeInput({name: 'searchkey', place: '中文', width: 200, maxlen: 1, value: '<?=$searchkey?>'})
                },
                {
                    'obj': makeBtn({
                        'value': '搜索', left: 2, 'type': 'submit','class': 'btn btn-info'
                    })
                },
                {
                    'obj': makeBtn({
                        'value': '录入文字',left: 122,'type': 'button', 'class': 'btn btn-success', click: 'addFonts();'
                    })
                }
            ]
    });
    var dataHtmlObj = makeDataList({
        form:  searchFontForm, //必须传入头部表单
        datas:  <?=$listResult?>,
        page_info:  <?=$pageInfo?>,
        'top': [
            {title: '编号', width:80, left: 15},
            {title: '字'}
        ],
        'fields': [
            {key: 's_id', left: 15},
            {key: 's_word'}
        ]
    });
    //添加字
    function addFonts() {
        msgWin('添加字', makeForm({
            type: 'li',
            url: '/?s=ufonts&do=add_fonts',
            'success':
            {
                success_value: ['0113'],
                msg: false,
                success_func: function (data) {
                    msgTis('成功录入 '+ data.info +'个字');
                    reFreshFonts();
                }
            },
            elements:
            [
                {em: '文章内容',obj: makeEditor({name:'s_words', width: '100%', height: 500, content: '', full: false})},
                {
                    box: {'class': 'submit_box'},
                    obj: makeBtn({type:'submit', value:'提交录入', 'class': 'btn btn-info submit_add_btn'})
                }
            ]
        }), 900, 500);
    }
    //刷新列表
    function reFreshFonts() {
        searchFontForm.refresh();
    }
    $('#root_right .right_content').append(searchFontForm).append(dataHtmlObj);
    leftEvent('fonts');
</script>
