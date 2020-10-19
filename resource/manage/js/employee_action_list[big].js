//防止内容部直接被打开
if( document.getElementsByClassName('leftmenu').length == 0) {
    parent.window.location = '/?s=employee';
}
//搜索事件
var topSearchForm = $('#top_search');
topSearchForm.find('.search_form1').submit(function(e){
    e.preventDefault();
    var form = $(this);
    var sdatepicker = form.find('#sdatepicker').val();
    var edatepicker = form.find('#edatepicker').val();
    var actiontype = form.find('#actiontype').val();
    var goodstype = form.find('#goodstype').val();
    var actionstate = form.find('#actionstate').val();
    var step = form.find('.step').val();
    var model = form.find('.model').val();
    var new_url = model+"&step="+step+
        "&goodstype="+goodstype+
        "&actionstate="+actionstate+
        "&actiontype="+actiontype+
        "&stime="+sdatepicker+
        "&etime="+edatepicker+
        "&searchkey="+sdatepicker+
        "&stime="+sdatepicker+
        "&searchkey=";
    ajaxOpen(new_url);
});
//搜索事件
topSearchForm.find('.search_form2').submit(function(e){
    e.preventDefault();
    var form = $(this);
    var searchtype = form.find('.searchtype').val();
    var keyValue = form.find('#searchkey').val();
    var model = form.find('.model').val();
    var step = form.find('.step').val();
    if(!keyValue || keyValue == '') {
        msg('请输入关键字',4);
        return false;
    }
    var new_url = model+"&step="+step+
        "&searchtype="+ searchtype +
        "&searchkey="+ encodeURIComponent(keyValue) +
        "&step="+step;
    ajaxOpen(new_url);
});
var timeBegin = topSearchForm.find("#sdatepicker" );
timeBegin.datepicker();
timeBegin.change(function() {
    timeBegin.datepicker("option", "dateFormat", 'yy-mm-dd' );
});

var timeEnd = topSearchForm.find("#edatepicker" );
timeEnd.datepicker();
timeEnd.change(function() {
    timeEnd.datepicker("option", "dateFormat", 'yy-mm-dd' );
});
//编辑活动信息
function editAction(a_id) {
    hideAllbox();
    msgWin('编辑活动信息','/?s=eaction/form&form=edit_action&aid='+a_id,900,1);
}
//删除活动
function deleteAction(a_id) {
    if (confirm('您确定要删除该活动吗？')){
        var postData = {
            gid: a_id
        };
        post('/?s=eaction&do=del&json=true',postData,function(data) {
            if(data.id != '0039') {
                if(data.info) data.msg += data.info;
                if(data.id == '0000') {
                    loginIn();
                    return;
                }
                msg(data.msg,4);
            } else {
                msg(data.msg,1);
                if(data.id == '0039') removeRecord(a_id);
                return;
            }
        });
    }
}
function removeRecord(aid) {
    $('#td_'+aid).remove();
}

// fid:1 通过 0 不通过
function action_agree(fid,aid) {
    var pass = fid==1?"pass":"notpass";
    hideAllbox();
    msgWin('商品审核','/?s=eaction/form&form='+ pass +'&step='+shenhe_step+'&aid='+aid,900,1);
}
//批量审核
function submitAudit(){
        var ids = '';
        var postlink;
        $("#list_action").find(".checkbox").each(function(){
            var box = $(this);
            if(box.attr('checked')) {
                ids += ','+$(this).val();
            }
            ids = trim(ids,",");
        });
        if( !ids ){
            msg('请选择一个活动',4);
            return false;
        }
        var form = $('#submit_box');
        var reason;
        if( form.find('#all_agree_sel').val() == '2' ){
            postlink = '/?s=eaction&do=pass_some&step='+shenhe_step;
            reason = '';
        }else{
            postlink = '/?s=eaction&do=notpass&step='+shenhe_step;
            reason = form.find('#all_reason_list').val();
        }
        post( postlink, { aid: ids,memo:reason }, function(data){
            switch(data.id){
                case '0041':
                    msg('修改活动状态失败，请重试！',4);
                    break;
                case '0043':
                    msgTis('操作成功');
                    audit_action_page(currentPage);
                    break;
                default :
                    msgTis(data.msg);

            }
        });
}
function no_pass(aid) {
    var this_tr = $('#td_'+aid);
    if(this_tr.length > 0) {
        this_tr.find('.list_state').html('未通过').addClass('warning').removeClass('green').removeClass('blue').removeClass('gray');
    }
}
var hasSelectAll = false;
//全选 反选
function selectAll() {
    if(!hasSelectAll){
        $("#list_action").find(".checkbox").attr('checked','checked');
        hasSelectAll = true;
    }else{
        hasSelectAll = false;
        $("#list_action").find(".checkbox").removeAttr('checked');
    }
}
//记录翻页
function audit_action_page(page) {
    currentPage = page;
    var form = $('.top_search').find('.search_form1');
    var dateStart = encodeURIComponent(form.find('#sdatepicker').val());
    var dateEnd = encodeURIComponent(form.find('#edatepicker').val());
    var modelUrl = form.find('.model').val();
    var step = form.find('.step').val();
    var actiontype = form.find('#actiontype').val();
    var goodstype = form.find('#goodstype').val();
    var actionstate = form.find('#actionstate').val();
    var pageUrl = modelUrl +"&step="+ step +"&stime="+ dateStart +"&etime="+ dateEnd +"&actiontype="+ actiontype +"&goodstype="+ goodstype +"&actionstate="+ actionstate;
    ajaxOpen(pageUrl+"&page="+page);
}
$('#all_agree_sel').change(function(){
    if($(this).val() ==3 ){
        $('#all_reason_list').show();
    } else {
        $('#all_reason_list').hide();
    }
});