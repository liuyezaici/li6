// 6rui.com 改:隐藏对话框js 
var QMy = Class.create(); 

/* 6rui优化后的js */
QMy.Friend = Class.create();
QMy.Friend.prototype = { 
    initialize: function () {},
  /** 
     * @date: 2007-11-25 最新:6rui.com 于2009-4-4 改动
     * @param: sid 窗口浮出元素ID   */ 
    _closeDialog: function(){
        if(this.friendSetDialog)
            this.friendSetDialog.hide(this);you_chuang=0;show_gongpin_no()
        if(this.editFriendDialog)
            this.editFriendDialog.hide(this);
        if(this.moveFriendDialog)
            this.moveFriendDialog.hide(this);
    }, 
    

     /**  * 收藏wy音乐操作 */
 show_6rui_com: function(sid,tt,fuid,w,h) { //sid是激活时的id//tt是标题//通过获取fuid的内容,转到新内容里
                    $('editTitle').innerHTML = tt ;you_chuang=1;
                        $('show_6rui_neirong').innerHTML = $(fuid).innerHTML;
                 if(!this.editFriendDialog){ // 如果不存在,则只创建一次. only create it once
                        this.editFriendDialog = new Ext.BasicDialog("show_6rui_koma_little_liuyezaici_2009_4_24", { 
                            modal:false,
                            resizable:false,
                            width:w, //宽度
                            height:h,
                            shadow:true,
                            proxyDrag: false,
                            draggable: false,
                            collapsible: false });   
                      //  this.editFriendDialog.addButton('关闭', this._closeDialog.bind(this), this.editFriendDialog);
                    }

                    this.editFriendDialog.show(Ext.get(sid).dom);
                }
};
var myFriend = new QMy.Friend();


