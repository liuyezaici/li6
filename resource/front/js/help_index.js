//搜索框关键字特效
function searchEvent() {
    var defaultKeytext = '请输入关键字';
    var form = $('#search_form');
    form.find('#help_keyword').focus(function(e) {
        if($(this).val() == defaultKeytext) {
            $(this).val('').addClass('focus');
        }
    }).blur(function(e) {
            if($(this).val() == '') {
                $(this).val(defaultKeytext).removeClass('focus');
            }
        }
    );
    form.submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var keywords = form.find('.input').val();
        if(keywords == defaultKeytext) {
            window.location = '/?s=hel';
            return;
        }
        window.location = '/?s=help&keyword='+encodeURIComponent(keywords)+'';
    });
}
searchEvent();