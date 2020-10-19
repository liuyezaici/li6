//检测访客是否登录
function checkLogin() {
    var topMenu = $('#header');
    var header_state = topMenu.find('#user_menu');
    post('/?s=system&do=userstate_info&t='+Math.random(),{}, function(data){
        if( data.nick && data.nick!= '' ){
            if(data.u_hash) {
                window.local_userid = data.u_hash;
            }
            var message = '';
            if(data.messages && data.messages > 0) {
                message = "("+data.messages+")";
            }
            var usermodel = data.usertype == 3 ? 'employee' : 'user';
            if(data.sid) {
                usermodel += "&sid=" + data.sid;
            }
            if(header_state.length > 0 ) {
                var my_menu = " 欢迎您 <a href='/?s="+ usermodel +"'>"+ data.nick  + message +"</a> [ <a  href=\"javascript: void(0);\" onclick=\"logOut();\" target=\"_self\">退出</a> ] " +
                    "<em>|</em><a  href=\"javascript: void(0);\" onclick=\"qiandao();\" target=\"_self\" style='color: #0000ff'>签到</a> " +
                    "<em>|</em><a href='/?s="+ usermodel +"' style='color: #ee1a04'>进入后台</a>" +
                    "<em>|</em><a  target=\"_blank\" href=\"/\">联系客服</a> ";
                header_state.html( my_menu ).attr({'title': data.nick + ', 金币: '+data.score });
            }
            var url = window.location.toString();
            if(url.indexOf('system/login') !=-1) {
                parent.window.location = '/?s='+usermodel;
            }
        } else {
            if(header_state.length > 0 ) {
                var my_menu = '欢迎来到返利试用！请<a class="login" style="color: #fe337b;" onclick="loginIn();" target="_self" href="javascript: void(0);">登录</a>' +
                    '[<a class="login" style="color: #fe337b;" href="/qq_login.php" target="_parent">QQ登录</a>]'+
                    '<em>|</em><a style="color: #fe337b;" href="/reg">免费注册</a>' +
                    ' <em>|</em> <a href="/forget">忘记密码</a>' +
                    '<em>|</em> <a  target=\"_blank\" href=\"/\">联系客服</a> ';
                header_state.html( my_menu );
            }
        }
    });
}
//提交签到
function qiandao(){
    post('/?s=sign&do=sign&json=true',function(data){
        if(data.id == '0000') {
            loginIn();
            return;
        }
        if(data.id != '0103') {
            msg(data.msg,4);
            return;
        } else {
            msg(data.msg,1);
        }
    });
}
checkLogin();