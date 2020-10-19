(function (global__, $) {
    $(document).ready(function () {
        var tagBox = $('#tag_box');
        tagBox.find('.selectAll').click(function (e) {
            e.preventDefault();
            var btn = $(this);
            if(btn.hasClass('btn-warning')) {
                tagBox.find("input[type='checkbox']").prop('checked', false);
                btn.removeClass('btn-warning').addClass('btn-default');
            } else {
                tagBox.find("input[type='checkbox']").prop('checked', true);
                btn.removeClass('btn-default').addClass('btn-warning');
            }
        });
        //反选
        tagBox.find('.selectInverse').click(function (e) {
            e.preventDefault();
            var btn = $(this);
            tagBox.find("input[type='checkbox']").each(function () {
                if($(this).prop('checked') == true) {
                    $(this).prop('checked', false);
                } else {
                    $(this).prop('checked', true);
                }
            });
        });
        tagBox.find('.submit').click(function (e) {
            e.preventDefault();
            var selectIdArray = [];
            tagBox.find("input[type='checkbox']").each(function () {
                if($(this).prop('checked') == true) {
                    selectIdArray.push($(this).val())
                }
            });
            if(selectIdArray.length == 0) {
                msgTisf('请选择关键词');
                return;
            }
            parent.location = '/juzi/tag/id/'+ selectIdArray.join(',');
        });
    });
})(this, jQuery);