// 6rui.com ��:���ضԻ���js 
var QMy = Class.create(); 

/* 6rui�Ż����js */
QMy.Friend = Class.create();
QMy.Friend.prototype = { 
    initialize: function () {},
  /** 
     * @date: 2007-11-25 ����:6rui.com ��2009-4-4 �Ķ�
     * @param: sid ���ڸ���Ԫ��ID   */ 
    _closeDialog: function(){
        if(this.friendSetDialog)
            this.friendSetDialog.hide(this);you_chuang=0;show_gongpin_no()
        if(this.editFriendDialog)
            this.editFriendDialog.hide(this);
        if(this.moveFriendDialog)
            this.moveFriendDialog.hide(this);
    }, 
    

     /**  * �ղ�wy���ֲ��� */
 show_6rui_com: function(sid,tt,fuid,w,h) { //sid�Ǽ���ʱ��id//tt�Ǳ���//ͨ����ȡfuid������,ת����������
                    $('editTitle').innerHTML = tt ;you_chuang=1;
                        $('show_6rui_neirong').innerHTML = $(fuid).innerHTML;
                 if(!this.editFriendDialog){ // ���������,��ֻ����һ��. only create it once
                        this.editFriendDialog = new Ext.BasicDialog("show_6rui_koma_little_liuyezaici_2009_4_24", { 
                            modal:false,
                            resizable:false,
                            width:w, //���
                            height:h,
                            shadow:true,
                            proxyDrag: false,
                            draggable: false,
                            collapsible: false });   
                      //  this.editFriendDialog.addButton('�ر�', this._closeDialog.bind(this), this.editFriendDialog);
                    }

                    this.editFriendDialog.show(Ext.get(sid).dom);
                }
};
var myFriend = new QMy.Friend();


