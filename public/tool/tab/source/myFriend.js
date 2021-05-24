// 6rui.com 改:隐藏对话框js 
var QMy = Class.create(); 

/* 6rui优化后的js */
QMy.prototype = {
    initialize: function () {},
  /** 
     * @date: 2007-11-25 最新:6rui.com 于2009-4-4 改动
     * @param: sid 窗口浮出元素ID   */ 
    _closeDialog: function(){
        if(this.editFriendDialog)
            this.editFriendDialog.hide(this);
    }, 

     /**  * 收藏wy音乐操作 */
 openWin: function(btnId,showWinId,w,h) { //sid是激活时的id//tt是标题
                 if(!this.editFriendDialog){ // 如果不存在,则只创建一次. only create it once
                        this.editFriendDialog = new Ext.BasicDialog(showWinId, {
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
                    this.editFriendDialog.show(Ext.get(btnId).dom);
                }
};
var myFriend = new QMy();


