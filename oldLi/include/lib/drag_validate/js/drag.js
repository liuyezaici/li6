/*
 * version 1.6
 * lr
 * date 2017-05-20
 * 拖动图片验证码
 */

//判断浏览器是pc还是移动端
function isPc(){
    var userAgentInfo = navigator.userAgent;
    var Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) { flag = false; break; }
    }
    return flag;
}
(function($){
    $.fn.drag = function(dragBarName, moveBtnName){
        var x, box = this, isMove = false;
        var moveBar = box.find(dragBarName);
        var moveImg = box.find(moveBtnName);
        //添加背景，文字，滑块
        var html = '<div class="drag_bg"></div>'+
            '<div class="drag_text" onselectstart="return false;" unselectable="on" default-text="往右拖动图片验证">往右拖动图片验证</div>'+
            '<div class="move_btn"></div>';
        moveBar.append(html);
        var moveBtn = moveBar.find('.move_btn');
        var drag_bg = box.find('.drag_bg');
        var text = box.find('.drag_text');
        var maxWidth = box.width() - moveBtn.width();  //能滑动的最大间距
        //手机端 鼠标在bar上的移动事件
        function doMoveBarEven(e) {
            e.preventDefault(); //阻止默认事件[如：手机显示下载图片]
            var e = e.touches[0];
            var _x = e.clientX -x;
            if(isMove) {
                moveImg.css({'left': _x});
                if(_x > 0 && _x <= maxWidth){
                    moveBtn.css({'left': _x});
                    drag_bg.css({'width': _x});
                }else if(_x > maxWidth){  //鼠标指针移动距离达到最大时清空事件
                    //backToBegin();
                }
            }
        }
        //手机端 移动按钮事件
        function doMoveBtn(e) {
            e.preventDefault(); //阻止默认事件[如：手机显示下载图片]
            moveBar[0].removeEventListener('touchmove', doMoveBarEven);
            moveBar[0].removeEventListener('touchstart', doMoveEndEven);
            moveImg.show();
            isMove = true;
            e = e.touches[0];
            x = e.clientX;
            //鼠标指针在上下文移动时，移动距离大于0小于最大间距，滑块x轴位置等于鼠标移动距离
            moveBar[0].addEventListener('touchmove', doMoveBarEven);
        }
        //手机端 鼠标移出事件
        function doMoveEndEven(e) {
            e.preventDefault(); //阻止默认事件[如：手机显示下载图片]
            isMove = false;
            var _x = e.pageX - x;
            $.ajax({
                type: "POST",
                url: "?do=validate",
                dataType : "JSON",
                async: false,
                data:{point:_x},
                success: function(result){
                    if(result['id'] == '0038' ) {
                        for(var i=1; 3>=i; i++){
                            moveImg.animate({left:_x-(10-5*i)}, 50);
                            moveImg.animate({left:_x+2*(30-10*i)}, 20, function(){
                                moveImg.css({'left': _x});
                            });
                        }
                        moveBtn.css({'left': maxWidth});
                        drag_bg.css({'width': '100%'});
                        moveImg.removeClass('moveImg_bord');
                        dragOk();
                        parent.success_push(_x);
                    } else if(result['id'] == '0022') {
                        text.text(result['info']);
                        backToBegin();
                        window.location.reload();
                    } else {
                        text.text(result['info']);
                        moveImg.css({'left': 0});
                        moveBtn.css({'left': 0});
                        drag_bg.css({'width': 0});
                        setTimeout(function () {
                            text.text(text.attr('default-text'));
                        }, 1500);
                    }
                },
                beforeSend:function(){
                    text.text(text.attr('加载中...'));
                }
            });
        }
        //鼠标按下时候的x轴的位置
        if(!isPc()) {
            moveBtn[0].addEventListener('touchstart', doMoveBtn);
            moveBtn[0].addEventListener('touchend', doMoveEndEven);
        } else {//电脑端
            moveBtn.mousedown(function(e) {
                moveBar.unbind('mousemove');
                moveBar.unbind('mouseup');
                moveImg.show();
                isMove = true;
                x = e.pageX - parseInt(moveBar.css('left'), 10);
                //鼠标指针在上下文移动时，移动距离大于0小于最大间距，滑块x轴位置等于鼠标移动距离
                moveBar.mousemove(function(e){
                    var _x = e.pageX - x;
                    if(isMove){
                        moveImg.css({'left': _x});
                        if(_x > 0 && _x <= maxWidth){
                            moveBtn.css({'left': _x});
                            drag_bg.css({'width': _x});
                        }else if(_x > maxWidth){  //鼠标指针移动距离达到最大时清空事件
                            //backToBegin();
                        }
                    }
                }).mouseup(function(e) {
                    isMove = false;
                    var _x = e.pageX - x;
                    $.ajax({
                        type: "POST",
                        url: "?do=validate",
                        dataType : "JSON",
                        async: false,
                        data:{point:_x},
                        success: function(result){
                            if(result['id'] == '0038' ) {
                                for(var i=1; 3>=i; i++){
                                    moveImg.animate({left:_x-(10-5*i)}, 50);
                                    moveImg.animate({left:_x+2*(30-10*i)}, 20, function(){
                                        moveImg.css({'left': _x});
                                    });
                                }
                                moveBtn.css({'left': maxWidth});
                                drag_bg.css({'width': '100%'});
                                moveImg.removeClass('moveImg_bord');
                                dragOk();
                                parent.success_push(_x);
                            } else if(result['id'] == '0022') {
                                text.text(result['info']);
                                backToBegin();
                                window.location.reload();
                            } else {
                                text.text(result['info']);
                                moveImg.css({'left': 0});
                                moveBtn.css({'left': 0});
                                drag_bg.css({'width': 0});
                                setTimeout(function () {
                                    text.text(text.attr('default-text'));
                                }, 1500);
                            }
                        },
                        beforeSend:function(){
                            text.text(text.attr('加载中...'));
                        }
                    });
                }).mouseleave(function(e) {//鼠标出去
                    backToBegin();
                });
            });
        }
        //回到起点
        function backToBegin() {
            moveImg.css({'left': 0});
            moveBtn.css({'left': 0});
            drag_bg.css({'width': 0});
            clearEven();
        }
        //清空事件
        function clearEven() {
            moveBar.unbind('mousemove');
            moveBar.unbind('mouseup');
            moveBar.unbind('mouseleave');
        }
        //验证通过
        function dragOk(){
            text.html('<img height="20" src="img/success.png"> 验证通过');
            clearEven();
            moveBtn.unbind('mousedown').hide();
        }
    };
})(jQuery);
