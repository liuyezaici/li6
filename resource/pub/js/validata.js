//创建验证码
global.makeValidate=function(btn, direction) {
    direction = direction || 'bottom';
    var boxId = "validate_append_box";
    var validateBox = $('<div></div>');
    validateBox.attr({
        'id': boxId,
        'class': 'validate_append_box'
    });
    validateBox.append('<div class="validate_menu_box"><iframe border="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" framespacing="0" ' +
        'frameborder="0" scrolling="no" width="260" height="150" src="/include/lib/drag_validate/validate.php"></iframe></div>');
    var balidateMenu = validateBox.find('.validate_menu_box');
    $('body').append(validateBox);
    var btnWidth = btn.outerWidth();
    var btnHeight = btn.outerHeight();
    var winWidth_ = $(window).width();//浏览器可见宽度
    var winHeight = $(window).height();//浏览器可见高度
    var winScrolltop = $(document).scrollTop();
    var y_ = winScrolltop + (winHeight / 2);
    validateBox.css({'height': winScrolltop+winHeight, 'width': winWidth_});
    var btnLeft = btn.offset().left;
    var btnTop = btn.offset().top;
    if(direction == 'bottom') {//出现在按钮下方
        balidateMenu.css({'left': btnLeft, 'top': btnTop + btnHeight});
    } else if(direction == 'top') {//出现在按钮上方
        balidateMenu.css({'left': btnLeft, 'top': btnTop - 150});
    } else if(direction == 'right') {//出现在按钮右侧
        balidateMenu.css({'left': btnLeft + btnWidth, 'top': btnTop});
    }
    validateBox.on('click', function () {
        validateBox.remove();
    });
    window.success_drag_validate_push = function (n) {
        btn.attr('data-validate', n);
        validateBox.remove();
        btn.click();
    };
}