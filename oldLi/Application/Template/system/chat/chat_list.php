<style>
    /* 会话列表 */
    #dialog_list {
        overflow-y: auto;
        border: 1px solid #ddd;
        border-top: 0;
        max-height: 100%;
        min-height: 300px;
    }
    #dialog_list ul li {
        width: 100%;
        overflow: hidden;
        border-bottom: 0.1em solid #ddd;
    }
    #dialog_list ul li:hover {
        background-color: #f3f3f3;
    }
    #dialog_list ul li.no_msg {
        padding: 8px 0;
        text-indent: 10px;
    }
    #dialog_list ul li .d_left {
        float: left;
        display: block;
        width: 20%;
        overflow: hidden;
    }
    #dialog_list ul li .d_left .u_face {
        margin: 10px 20px 10px 15px;
    }
    #dialog_list ul li .d_left .u_face img {
        width: 100%;
        border-radius: 10px;
    }
    #dialog_list ul li .d_main {
        float: left;
        width: 80%;
        position: relative;
        padding: 5px 0;
    }
    #dialog_list ul li .d_main .title {
        position: relative;
        padding-top: 6px;
    }
    #dialog_list ul li .d_main .title .user_nick {
        cursor: pointer;
    }
    #dialog_list ul li .d_main .last_time {
        font-size: 12px;
        color: #aaa;
    }
    #dialog_list ul li .d_main .remove_btn {
        font-size: 16px;
        color: #666;
        cursor: pointer;
        position: absolute;
        right: 10px;
    }
    #dialog_list ul li .d_main .last_words {
        overflow: hidden;
        white-space: nowrap;
        color: #999;
        cursor: pointer;
        width: 95%;
    }
    #dialog_list ul li .d_main .last_words .no_read {
        font-size: 1em;
        color: #ca1900;
    }
</style>
<?=$chatTopHtml?>
<div id="dialog_list">
    <ul>
        <li class="no_msg">loading</li>
    </ul>
</div>
<script>
    $(function() {
        var dialogList = $('#dialog_list');
        var this_= this;
        this.dialog_list_api = '/?s=chat/get_dialog_list'; //ajax接口 获取我的会话列表
        this.topBottomSpace = 100; //窗口上下预留高度，以限制中间内容高度
        //重置窗口高度
        this.reHeightDialogBox = function () {
            var bodyMaxHeight = $(window).outerHeight(true) - this_.topBottomSpace;
            if(bodyMaxHeight > 600) bodyMaxHeight = 600;
            dialogList.css('height', bodyMaxHeight);
        };
        //首次加载建立 webSocket 聊天连接
        if(!webSocket || webSocket.readyState != 1) {
            //首次建立连接
            webChat.connectWs('{"type":"join_chat", "room_id": "'+ webChat.roomId +'", "my_uid": "'+ webChat.myUserInfo.my_uid +'"}');
        }
        //删除会话
        webChat.removeDialog = function(btn, lid)
        {
            rePost(web_api_url + '?do=remove_dialog', {lid:  lid}, function(data) {
                if(data.id != '0043') {
                    if(data.info) data.msg += data.info;
                    if(data.id == '0000') {
                        loginIn();
                        return;
                    }
                    msg(data.msg);
                } else {
                    btn.parents('.d_main').parent().remove();
                }
            });
        };
        //加载所有会话列表
        this.loadDialogList = function () {
            //加载 会话列表
            loading(false);
            rePost(this_.dialog_list_api, {
                my_uid : webChat.myUserInfo.my_uid
            }, function(data) {
                noLoading();
                if(data.id != '0038') {
                    if(data.info) data.msg += data.info;
                    if(data.id == '0000') {
                        loginIn();
                        return;
                    }
                    msg(data.msg);
                } else {
                    var lastTalkObj = eval(data.info);
                    if(lastTalkObj.length == 0) {
                        dialogList.html('<ul><li class="no_msg">还没有聊天对象</li></ul>');
                    } else {
                        var listHtml = '';
                        var data_;
                        var speaker = '';
                        var readYet = true;
                        if(lastTalkObj.length > 0) {
                            for(var i=0; i< lastTalkObj.length; i++) {
                                data_ = lastTalkObj[i];
                                speaker = data_['speaker'] == 'him' ? 'Ta': '我';
                                readYet = data_['read_yet'] ? '' : '<span class="no_read">[未读]</span>';
                                listHtml +=  '<li>' +
                                    '<div class="d_left" onclick="talkToUser(\''+ data_.fromUid +'\');"> '+
                                    '<div class="u_face"><img src="'+ (data_.fromUimg ? data_.fromUimg : webChat.nullLogo) +'" alt="头像" /></div> '+
                                    '</div> '+
                                    '<div class="d_main"> '+
                                        '<div class="title"> '+
                                        '<span class="user_nick" onclick="talkToUser(\''+ data_['fromUid'] +'\');">'+ data_.fromUname +'</span> '+
                                        '<span class="last_time">'+ data_['lastTime'] +'</span> '+
                                        '<span class="remove_btn" onclick="webChat.removeDialog($(this), \''+ data_.lid +'\');" title="移除会话"> <i class="glyphicon glyphicon-remove"></i> \</span> '+
                                        '</div> '+
                                        '<div class="last_words" onclick="talkToUser(\''+ data_.fromUid +'\');">'+
                                            (data_.lastContent ? readYet + speaker + ':' + webChat.emojiUbbToHtml(data_.lastContent) : '' )
                                            +'</div> '+
                                    '</div> '+
                                    '</li>';
                            }
                            dialogList.html('<ul>'+ listHtml +'</ul>');
                        }
                    }
                }
            });
        };
        //列表时接受聊天内容
        //flag 'his_message'
        webChat.pushContent = function(flag, data_)
        {
            //如果是对方发来信息，要更新列表内容
            if(flag == 'his_message') {
                this_.loadDialogList();//更新聊天列表
            }
        };

        webChat.pushNoReadNumToBtn();//打开列表要把未读信息数量更新
        //初始化：加载聊天会话列表
        this_.loadDialogList();

    });
</script>