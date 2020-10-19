<style>

    /* 好友列表 */
    #friends_list {
        overflow-y: auto;
        border: 1px solid #ddd;
        border-top: 0;
        max-height: 100%;
        min-height: 300px;
    }
    #friends_list ul {
        margin: 0;
    }
    #friends_list ul li {
        width: 100%;
        overflow: hidden;
        border-bottom: 1px solid #ddd;
    }
    #friends_list ul li:hover {
        background-color: #f3f3f3;
    }
    #friends_list ul li.no_friends {
        padding: 8px 0;
        text-indent: 10px;
    }
    #friends_list ul li .d_left {
        float: left;
        display: block;
        width: 20%;
        overflow: hidden;
    }
    #friends_list ul li .d_left .u_face {
        margin: 10px 20px 10px 15px;
    }
    #friends_list ul li .d_left .u_face img {
        width: 100%;
        border-radius: 8px;
        border: 1px dashed #ccc;
        cursor: pointer;
    }
    #friends_list ul li .d_main {
        float: left;
        width: 80%;
        position: relative;
    }
    #friends_list ul li .d_main .title {
        padding-top: 6px;
    }
    #friends_list ul li .d_main .title .user_nick {
        cursor: pointer;
    }
    #friends_list ul li .d_main .msn_btn {
        position: absolute;
        right: 10px;
        top: 12px;
    }
    #friends_list ul li .d_main .add_time {
        position: absolute;
        right: 10px;
        bottom: -30px;
        color: #999;
        font-size: 11px;
    }
    #friends_list .pages {
        margin-top: 0;
        background-color: #f9f9f9;
    }
    #friends_list .pages .page {
        background-color: #fff;
        border-radius: 5px;
        cursor: pointer;
        display: inline-block;
        line-height: 18px;
        margin-right: 2px;
        padding: 2px 6px;
    }
    #friends_list .pages .page.current {
        background-color: #dedede;
        color: #fff;
    }
    #friends_list .pages .total {
        display: inline-block;
        padding: 2px 5px;
        margin-right: 8px;
    }
</style>
<?=$chatTopHtml?>
<div id="friends_list">
    <ul>
        <li class="no_friends">loading</li>
    </ul>
</div>
<form id="search_frineds" class="clearfix">
</form>
<script>
    $(function() {
        var friendBox = $('#friends_list');
        var this_ = this;
        this.friend_list_api = '/?s=chat&do=get_friend_list'; //ajax获取我的好友列表 接口
        this.friend_pagesize = 10; //单页好友数量
        this.topBottomSpace = 100; //窗口上下预留高度，以限制中间内容高度
        //重置窗口高度
        this.reHeightDialogBox = function () {
            var bodyMaxHeight = $(window).outerHeight(true) - this_.topBottomSpace;
            if(bodyMaxHeight > 600) bodyMaxHeight = 600;
            friendBox.css('height', bodyMaxHeight);
        };
        //加载 好友列表
        this.loadFriendsList = function(page)
        {
            page = page || 1;
            loading(false);
            rePost(this_.friend_list_api, {
                my_uid : webChat.myUserInfo.my_uid,
                page: page
            }, function(data) {
                noLoading();
                if(data.id != '0038') {
                    if(data.info) data.msg += data.info;
                    if(data.id == '0000') {
                        loginIn();
                        return;
                    }
                    if(data.id == '0033') {//身份已经切换
                        window.location.reload();
                        return;
                    }
                    msg(data.msg);
                } else {
                    var returnObj = eval(data.info);
                    var friendsObj = returnObj[0];
                    var pageInfo = returnObj[1];
                    var pageSize = this_.friend_pagesize;//单页好友数量
                    var totalFriends = pageInfo['total'];//好友数量
                    if(friendsObj.length == 0) {
                        friendBox.html('<ul><li class="no_friends">还没有好友</li></ul>');
                    } else {
                        var listHtml = '';
                        var data_;
                        if(friendsObj.length > 0) {
                            var totalPage = totalFriends % pageSize > 0 ? parseInt(totalFriends / pageSize) +1 : parseInt(totalFriends / pageSize);
                            var fromId = 0;//因为接口已分页，所以这里数据也只有一页。(page-1) * pageSize;
                            var endId = pageSize-1;//(page) * pageSize - 1;
                            if(endId > friendsObj.length) endId = friendsObj.length;
                            var newIndex = 0;
                            var newPageData = [];
                            for(var i_ = fromId; i_ < endId; i_ ++) {
                                newPageData[newIndex] = friendsObj[i_];
                                newIndex ++;
                            }
                            var pageObj = $("<div class='pages'></div>");
                            pageObj.append("<span class='total'>"+ totalFriends +"个好友</span>");
                            //计算分页导航
                            var fromPage = page-5;
                            if(fromPage<1) fromPage = 1;
                            for (i_=fromPage; i_ < Math.min((fromPage + 10), totalPage+1); i_++){
                                if (i_ == page){
                                    pageObj.append("<span class='page current' data-page='"+ i_ +"'>"+ i_ +"</span>");
                                }else{
                                    pageObj.append("<span class='page' data-page='"+ i_ +"'>"+ i_ +"</span>");
                                }
                            }
                            if (i_ < totalPage){
                                pageObj.append("<span class=page data-page='"+ totalPage +"'>尾页</span>");
                            }
                            pageObj.find('.page').on('click', function() {
                                var page_ = $(this).attr('data-page');
                                this_.loadFriendsList(page_);
                            });
                            for(var i=0; i< newPageData.length; i++) {
                                data_ = newPageData[i];
                                listHtml +=  '<li> '+
                                    '<div class="d_left"> '+
                                        '<div class="u_face" onclick="talkToUser(\''+ data_.his_uid +'\');"><img src="'+ (data_.his_ulogo ? data_.his_ulogo : webChat.nullLogo) +'" alt="头像"/></div>'+
                                    '</div> '+
                                    '<div class="d_main">'+
                                        '<p class="title"> '+
                                            '<span class="user_nick" onclick="talkToUser(\''+ data_.his_uid +'\');">'+ data_.his_uname +'</span> '+
                                        '</p> '+
                                        '<a href="javascript: void(0);" target="_self" class="msn_btn btn btn-info btn-xs" onclick="talkToUser(\''+ data_.his_uid +'\');">发送信息</a> '+
                                        '<span class="add_time">添加时间: '+ data_.f_add_time +'</span> '+
                                    '</div> '+
                                '</li>';
                            }
                            friendBox.html('<ul>'+ listHtml +'</ul>').append(pageObj);
                            this_.reHeightDialogBox();//每次加载出来好友，要重置最大高度
                        }
                    }
                }
            });
        };
        //默认加载
        this_.loadFriendsList(1);
    });

</script>