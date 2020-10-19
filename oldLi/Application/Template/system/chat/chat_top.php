<div id="chat_top">
    <ul class="nav nav-tabs">
        <li class="friend_list"><a href="javascript: void(0);" onclick="webChat.openFriendList();" target="_self">联系人</a></li>
        <li class="message_list"><a href="javascript: void(0);" onclick="webChat.openDialogList();" target="_self">所有会话<span class="no_read_count"></span></a></li>
        <?php
        if(isset($talking_to_u_nick)) {
        ?>
        <li class="dialog"><a href="javascript: void(0);" target="_self"><?=$talking_to_u_nick?> <span id="dialog_status">离线</span> <span id="is_writting"></span> </a></li>
        <?php
        }
        ?>
    </ul>
    <a href="javascript: void(0);" target="_self" class="close glyphicon glyphicon-remove" title="收起" onclick="webChat.removeChat(); "></a>
</div>