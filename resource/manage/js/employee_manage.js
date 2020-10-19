//顶部自定义个人菜单
function editDiyMenu() {
    hideAllbox();
    msgWin('编辑我的常用链接','?m=euser/form&form=edit_diy_menu',800,1);
}
function ajaxOpen(url) {
    var h_ = $(window).height();//浏览器可见高度
    var y_ = $(document).scrollTop() + (h_ / 2);
    var y_ = y_ < 0 ? 0 : y_;
    var x_ = document.body.clientWidth / 2 - 50;
    var url = url.replace(/\s/g,'%20');
    

    //重置和注销  #rightmain_body 新建的util类、注销jQuery事件
    var util = {};
    $('#rightmain_body').unbind();

    $('#rightmain_body').load(url+'&rad='+Math.random(),function() {
        //获取成功 隐藏提示层
        $('#top_load').stop();
        $('#top_load').fadeOut();
    });
    $('#top_load').stop();
    $('#top_load').css({ display:'block',height:h_});
    //setTimeout('hide_top_load()',10000);//无论是否操作成功。10秒后关掉遮罩层
}

//定时隐藏页面弹出的遮罩层
function hide_top_load(){
    $('#top_load').fadeOut();
}
$(document).ready(function(){
    var leftMenu = $('#leftmenu');
    leftMenu.find('dt').click(function(e) {
        var dt = $(this);
        dt.next().slideToggle(300);
        if(dt.find('em').hasClass('mark') ){
            dt.find('em').attr('class','mark2');
        } else {
            dt.find('em').attr('class','mark');
        }
    });
    var url = window.location.toString();
    if (url.indexOf('&goto=') !== -1) {
        var newurl = url.split('&goto=');
        newurl = newurl[1];
        newurl = $.url.decode(newurl);
        if(newurl.substr(0,1) !== '?') newurl = '?'+newurl;
        ajaxOpen(newurl);
    }
    //左边链接改变
    leftMenu.find('.leftmenu_body a').each(function(e){
        var alink = $(this);
        var oldUrl = alink.data('url');
        alink.attr('target','_self').attr('href', "javascript:ajaxOpen('"+ oldUrl +"');");
    });
});