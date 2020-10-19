//Jquery ajax聊天功能
// LR 2016-11-26首次编写 网页聊天

if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
//WebSocket配置
WEB_SOCKET_SWF_LOCATION = "/include/lib/workerman-chat-for-win/Applications/Chat/Web/swf/WebSocketMain.swf";
WEB_SOCKET_DEBUG = true;
var webChat;
var webSocket;//用于判断当前是否连接聊天服务器
//初始化聊天事件
function buildWebChat(msgBtn, moveBox) {
    moveBox = moveBox || false;
    //服务器数据接口
    var dialogBox = null;
    var webChatBoxId = 'webchat_box';

    //聊天页面接口
    var chatModel = '/?s=chat&from_form=1&load_text=1';
    var dialogTalkingUrl = chatModel + '/dialog';
    var friendListUrl = chatModel + '/friends';
    var chatModelCountNoRead = chatModel + '&do=get_my_no_read_msg';
    var chatModelSetMsgRead = chatModel + '&do=send_msg_has_read';
    //配置聊天室的动作文件： workerman-chat-for-win\Applications\Chat\Events.php
    var numWap = msgBtn;//显示未读数量的标签对象 外部初始化时需要传入定义
    //定义聊天对象所有动作
    webChat = {
        //定义对方的uid 用于校验输出聊天内容的是主是客
        talk_to_uid :0,
        roomId : 'sasasui',
        nullLogo : '/resource/system/images/default_cover.jpg',//默认空头像
        getUserInfoUrl : '/?s=chat&do=get_user_info',
        //我的信息
        myUserInfo: {my_uid : 0, u_logo: this.nullLogo},
        //定义表情包路径 仅支持单种表情
        webchatEmoji : {
            'path': '/resource/system/images/webchat/face',
            'type': 'qq2009',
            'total': 96,
            'geshi': 'gif'
        },
        //打开 webSocket 连接
        connectWs: function(cmd)
        {
            // 创建websocket
            webSocket = new WebSocket("ws://"+document.domain+":7272");
            // 当socket连接打开时，输入用户名
            webSocket.onopen = function() {
                webSocket.send(cmd);
            };
            // 当有消息时根据消息类型显示不同信息
            webSocket.onmessage = webChat.onmessage;
            webSocket.onclose = function() {
                //msg("聊天服务器已经关闭，请F5刷新 或 联系客服 或 等待响应");
                //msgTis('聊天服务器已经关闭');
            };
            webSocket.onerror = function() {
                //msg("聊天服务器已经重启，请F5刷新 或 联系客服 或 等待响应");
                //msgTis('聊天服务器已经重启');
                setTimeout(function() {
                    webChat.connectWs();
                }, 5000);
            };
        },
        //创建聊天窗口
        buildBox : function () {
            /*if(!webChat.myUserInfo.my_uid) {
                loginIn();
                return null;
            }*/
            //判断是否有聊天窗口，没有则生成
            dialogBox = $('#'+ webChatBoxId);
            if(dialogBox.length == 0) {
                var width = $(window).width();
                if(width > 520) width = 520;//iphone 6s 最大宽度
                dialogBox = makeABox({
                    err: 0, //ico图标 1v 2- 3! 4i 5x 6") 7"( 8? 9-  0为不需要图标
                    bg: 1,//背景遮挡
                    text: '',
                    'id': 'webChatBoxId',
                    can_move_box: moveBox,
                    btn: '',
                    hide: false,//自动隐藏
                    position_type: 'fixed'//浮动类型
                });
                dialogBox.find('.content').html('<p class="loading_box">努力加载中...</p>');
                dialogBox.css({width: width, left: 'auto', right: 0, top: 0});
            } else {
                dialogBox.show();
            }
            return dialogBox;
        },
        //打开 会话列表
        openDialogList: function () {
            webChat.talk_to_uid = 0; //每次打开或返回列表，要清空当前聊天对象，防止本地误判。
            webChat.buildBox().find('.content').load(chatModel);
        },
        //打开 好友列表
        openFriendList: function () {
            webChat.talk_to_uid = 0; //每次打开或返回列表，要清空当前聊天对象，防止本地误判。
            var box = webChat.buildBox();
            box.find('.content').load(friendListUrl);
        },
        //聊天对话框
        openTalkingBox: function(targetUid) {
            webChat.talk_to_uid = targetUid || '0';
            var box = webChat.buildBox();
            if(box) box.find('.content').load(dialogTalkingUrl + '&to_uid='+ targetUid +'&from_form=1&load_text=1');
        },
        //将未读信息写进聊天小按钮
        //触发条件： 收到别人聊天信息并且 自己当前没有打开聊天窗 或 在和别人聊天时
        pushNoReadNumToBtn : function(fromType)
        {
            fromType = fromType|| 'parent_page';
            //找到所有未读的信息数量
            rePost(chatModelCountNoRead, {}, function(data) {
                if(data.id != '0038') {
                    if(data.info) data.msg += data.info;
                    if(data.id == '0000') {
                        loginIn();
                    }
                } else {
                    var noReadNum = parseInt(data.info);
                    if(fromType == 'parent_page') {//未打开聊天窗口
                        if(!numWap) return; //未定义显示数量的按钮 不执行后面
                        //显示未读数量;
                        var numWapSon = numWap.find('.num');
                        if(noReadNum >0 ) {
                            if(numWapSon.length == 0) {
                                numWap.append('<span class="num"></span>')
                                numWapSon = numWap.find('.num');
                            }
                            numWapSon.html(noReadNum);
                        } else {
                            if(numWapSon.length > 0) {
                                numWapSon.remove();
                            }
                        }
                    } else {//和别人聊天
                        if(noReadNum > 0) {
                            dialogBox.find('#chat_top .no_read_count').html('('+ noReadNum +')');
                        }
                    }
                }
            });
        },
        // 服务端发来消息时
        onmessage : function(e)
        {
            var data = eval("("+e.data+")");
            switch(data['type']) {
                //对方发言
                case 'send_message':
                    //判断是否打开对话框，未打开则显示在列表，打开，则判断我正在和他聊天与否，不聊则显示在头部，聊 则显示在内容中。
                    if(!dialogBox || dialogBox.length ==0) {
                        webChat.pushNoReadNumToBtn('parent_page');
                        return;
                    }
                    var fromUid = parseInt(data['from_uid']);
                    //正在和他聊天
                    if(fromUid == parseInt(webChat.talk_to_uid)) {
                        //服务器在cmd底下无法输出24小时制的小时 故本地直接生成时间
                        var d = new Date();
                        data['time'] = d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate() + ' '+ d.getHours() + ':'+ d.getMinutes() + ':'+ d.getSeconds();
                        webChat.pushContent('his_message', data);
                        //将我们的信息变为已读
                        rePost(chatModelSetMsgRead , {'his_uid':  fromUid, uid: webChat.myUserInfo.my_uid}, function(data) {});
                    } else {
                        //正在别人聊天
                        if(dialogBox.find('#message_list').length == 1) {
                            webChat.pushNoReadNumToBtn('talking');
                        } else if(dialogBox.find('#dialog_list').length == 1) {//在列表页面，刷新列表
                            webChat.openDialogList();
                        }
                    }
                    break;
                //我自己的发言
                case 'i_say':
                    //服务器在cmd底下无法输出24小时制的小时 故本地直接生成时间
                    var d = new Date();
                    data['time'] = d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate() + ' '+ d.getHours() + ':'+ d.getMinutes() + ':'+ d.getSeconds();
                    webChat.pushContent('my_message', data);
                    break;
                // 有人离线了
                case 'some_one_logout':
                    if(parseInt(webChat.talk_to_uid) === parseInt(data['his_uid'])) {
                        webChat.setHisIsOnline(false);
                    }
                    break;
                //他正在输入
                case  'on_write':
                    webChat.setHisWriting();
                    break;
                //对方上线
                case  'his_online':
                    if(data['his_uid']) {
                        //当前聊天对象就是他 设置对方为在线
                        if(parseInt(data['his_uid']) == webChat.talk_to_uid) {
                            webChat.setHisIsOnline(true);
                        }
                    }
                    break;
                //回答方 被通知 更新未读信息
                case  'cmd_refresh_his_no_read_num':
                    if(data['to_uid']) {
                        //当接受对象是我时，更新
                        if(parseInt(data['to_uid']) == webChat.myUserInfo.my_uid && typeof getNoReadAnswerOrApply != 'undefined') {
                            getNoReadAnswerOrApply();
                        }
                    }
                    break;
            }
        },

        //替换表情 ubb转html
        emojiUbbToHtml : function(contentText)
        {
            contentText = contentText || '';
            contentText = contentText.replace(/\[face:([a-z0-9]*)_([0-9+]*)]/g, '<img src="'+ webChat.webchatEmoji.path + '/$1/$2.'+ webChat.webchatEmoji.geshi +'" class="face" />');
            contentText = contentText.replace(/&amp;/g, '&');//所有的非法函数都已经过滤了<> 所以可以复原查看
            contentText = contentText.replace(/&quot;/g, '"');//恢复引号
            contentText = contentText.replace(/&lt;img/g, '<img');//恢复图片标签
            contentText = contentText.replace(/img&gt;/g, 'img>');
            contentText = contentText.replace(/&lt;br&gt;/g, '<br>');//恢复br标签
            //加链接
            var reg = /(http|https):\/\/[\w\-_]+(\.?[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?/gi;
            contentText = contentText.replace(reg, function ($s, $s1) {
                return "<a href=\""+ $s +"\">"+ $s +"</a>";
            });
            return contentText;
        },
        //关闭聊天框
        removeChat: function () {
            hideNewBox();
        }
    };
    window.talkToUser = function(uid) {//与某人发起聊天
        if(!uid || uid==0|| isNaN(uid)) {
            msg('缺少聊天对象的uid');
        }
        webChat.openTalkingBox(uid);
    };
    //打开我的聊天会话列表 必须先登录
    window.openWebChat = function() {
        //生成会话列表窗口
        webChat.openDialogList();
    };
    //更新回答方 未读的信息
    window.notifyHimRefreshNoReadMsg = function(hisUid) {
        webSocket.send('{"type":"notify_him_refresh_no_read_msg", "room_id":"'+ webChat.roomId +'", "to_u_id":"'+ hisUid +'", "from_uid":"'+ webChat.myUserInfo.my_uid +'"}');
    };
    //首次加载 获取之前的未读消息
    if(webChat.myUserInfo.my_uid > 0) webChat.pushNoReadNumToBtn('parent_page');
    //首次加载 获取我的cookies uid
    rePost(webChat.getUserInfoUrl, {uid:  0}, function(data) {
        if(data.id == '0038') {
            webChat.myUserInfo.my_uid = data.info.u_id;
            webChat.myUserInfo.u_logo = data.info.u_logo;
            //首次建立连接
            if(!webSocket || webSocket.readyState != 1) {
                webChat.connectWs('{"type":"join_chat", "room_id": "'+ webChat.roomId +'", "my_uid": "'+ webChat.myUserInfo.my_uid +'"}');
            } else {
                webSocket.send('{"type":"join_chat", "room_id": "'+ webChat.roomId +'", "my_uid": "'+ webChat.myUserInfo.my_uid +'"}');
            }
        }
    });
}

//调取接口：
//1.获取最近聊天会话的列表 openWebChat();
//2.直接与某人聊天 talkToUser(6);

//引入此js的页面 都会自动加载此函数：自动获取其uid 如果已经登录 则提交给聊天室
/*
 $(function() {
 buildWebChat(msgNumWap);//创建聊天室 传入聊天未读信息的按钮对象
 });*/
