(function (global__, $) {
    $(document).ready(function () {
        var searchForm = $('#searchForm');
        var currentText = searchForm.find('#current_text');
        var appendInput = searchForm.find('#append_input');
        var typeInput = searchForm.find(".hide_flag");
        var searchUrl = appendInput.attr('data-url');
        var openLink1 = appendInput.attr('data-link1');
        var openLink2 = appendInput.attr('data-link2');
        var inputHolder = appendInput.attr('data-holder');
        var liAuthor = appendInput.attr('data-liauther');
        var liName = appendInput.attr('data-liname');
        var searchVal = appendInput.attr('data-value');
        var searchInput = makeInput({
            'name': 'keyword',
            'class': 'form-control',
            place: inputHolder,
            url: searchUrl,
            value: searchVal,
            'post_min': 1, //至少要一个字符才提交 可以设置为5 表示输入5位数才开始检索
            'data_key': 'data', //数据的下标
            'value_key': 'uri',
            clear: true,
            li_num: 10,
            menu: {
                li: {
                    title: '{content}',
                    value: '{text_li}'
                }
            }
        });
        appendInput.html('').append(searchInput);
        searchForm.find('.dropdown-menu a').click(function (e) {
            e.preventDefault();
            var btn = $(this);
            searchUrl = searchUrl.split('?t=')[0];
            var t_ = btn.attr('data-value');
            searchInput.url  = searchUrl +'?t='+ t_;
            typeInput.val(t_);
            currentText.html(btn.html());
        });
    });
})(this, jQuery);