<style>
    .control_panel .input-group {
        width: 320px;
        max-width: 100%;
        margin-bottom: 25px;
    }
    .control_panel .input-group .form-control{
        padding: 6px 12px;
    }
    #edit_uname_form ,
    #edit_email_form ,
    #edit_tel_form {
        max-width: 372px;
    }
    /* 头像修改 */
    .select_system_face_menu ul li {
        float: left;
        display: inline-block;
        width: 60px;
        height: 60px;
        overflow: hidden;
        margin: 0 8px 8px 0;
    }
    .select_system_face_menu ul li img {
        width: 60px;
    }
</style>
<div class="page-header">
    <h2>
        控制面板
    </h2>
</div>
<div class="form-horizontal control_panel" role="form" style="margin-left: 30px;">
    <div class="input-group">
        <label class="input-group-addon">帐号</label>
        <div class="form-control">
            <?=$user_nick?>
        </div>
        <div class="input-group-btn">
            <span class="btn btn-info" onclick="resetPwd($(this));">修改密码</span>
        </div>
    </div>
    <div class="input-group">
        <label class="input-group-addon">昵称</label>
        <div class="form-control">
            <?=$user_name?>
        </div>
        <div class="input-group-btn">
            <span class="btn btn-info" onclick="editName($(this));">修改昵称</span>
        </div>
    </div>
    <div class="input-group">
        <label class="input-group-addon">邮箱</label>
        <div class="form-control">
            <?=$u_email?>
        </div>
        <div class="input-group-btn">
            <span class="btn btn-info" onclick="editEmail($(this));">修改邮箱</span>
        </div>
    </div>
    <div class="input-group">
        <label class="input-group-addon">手机</label>
        <div class="form-control">
            <?=$u_tel?>
        </div>
        <div class="input-group-btn">
            <span class="btn btn-info" onclick="editTel($(this));">修改手机</span>
        </div>
    </div>

    <div class="input-group">
        <div class="thumbnail">
            <img src="<?=$u_logo?>" title="编辑头像" id="user_head_logo" class="thumbnail" onerror="this.src='<?=\Config::get('cfg_default_face')?>';">
            <div class="caption text-center">
                <p>
                    <a href="javascript:void(0);" class="btn btn-default" onclick="openSystemFacesMenu($(this));" target="_self" id="select_face_btn">系统头像</a>
                    <span id="upload_face_box"></span>

                </p>
            </div>
        </div>
    </div>
</div>
<script>
    //发送验证码剩余时间
    var smsRestTime = '<?=$restTime?>';
    //刷新页面
    function reloadThisPage() {
        window.location.reload();
    }
    //打开系统头像菜单
    function openSystemFacesMenu(opener) {
        var face_htm = '';
        var onepage = 79;
        for( var d = 1; d< (onepage + 1); d++) {
            face_htm += "<li><img src='/resource/system/images/face/"+ d +".jpg' style='cursor:pointer;' alt='选择头像' onclick='choseThisSystemface("+d+");' title='选此头像'></li>";
        }
        var facemenu = '<div class="select_system_face_menu">' +
            '<ul class="clearfix">'+face_htm +"</ul>"+
            '</div>';
        msgView('选择头像',facemenu, 872, 10, false, opener);
    }
    //选择系统头像
    function choseThisSystemface(faceid) {
        rePost('/?s=user/chose_face&json=true',{faceid:faceid},function(data) {
            noLoading();
            hideNewBox();
            if(data.id != '0267') {
                if(data.info) data.msg += data.info;
                if(data.id == '0000') {
                    loginIn();
                    return;
                }
                msgTis(data.msg);
            } else {
                msgTis(data.msg);
                window.location.reload();
            }
        });
        loading();
    }
    //创建上传头像按钮
    $('#upload_face_box').append(makeInput({
        name:'face_url',
        'class': 'diy_upload_input', type:'file',
        url: '/?s=user/upload_face&json=true',
        success_func:'window.location.reload();',
        success_key: 'id',
        success_value: '0388'
    }));

    //修改密码
    function resetPwd( ) {
        msgView('修改密码', makeForm({
            'url': '/?s=user/edit_pwd&json=true',
            success_key: 'id',
            success_value: '0043',
            success_func: function (e) {
                msgTis(e.msg);
                setTimeout(function () {
                    reloadThisPage();
                }, 500)
            },
            err_func: function (e) {
                msgTis(e.info);
            },
            'value': makeTable({
                tr_1: [
                    {
                        td: [
                            {
                                value: makeInput({
                                    'class': 'btn-block no_radius_right',
                                    name: 'old_phone',
                                    place:'当前手机',
                                    limit: 'int', maxlen: 11, null_func: function () {
                                        msgTis('手机呢？');
                                    }
                                })
                            }
                        ]
                    },
                    {
                        td: [
                            {
                                padding_top: '10px',
                                value: makeInput({name:'new_pwd1', 'class': 'btn-block', place:'设置新密码', type: 'password', value: '', maxlen: 25, null_func: function () {
                                        msgTis('新密码呢？');
                                    }
                                })
                            }
                        ]
                    },
                    {
                        td: [
                            {
                                padding_top: '10px',
                                value: makeInput({name:'new_pwd2', 'class': 'btn-block',  place:'重输新密码', type: 'password', value: '', maxlen: 25, null_func: function () {
                                        msgTis('新密码呢？');
                                }})
                            }
                        ]
                    },
                    {
                        td: [
                            {
                                padding_top: '10px',
                                value: makeBtn({type:'submit', value:'提交修改', 'class': 'btn btn-info btn-block'})
                            }
                        ]
                    }
                ]
            })
        }), 420, 100);
    }
    //修改昵称
    function editName() {
        msgView('修改昵称(马甲)', makeForm({
            'url': '/?s=user/change_u_name&json=true',
            success_key: 'id',
            success_value: '0043',
            success_func: function (e) {
                msgTis(e.msg);
                setTimeout(function () {
                    reloadThisPage();
                }, 500)
            },
            err_func: function (e) {
                msgTis(e.info);
            },
            'value': makeTable({
                tr_1: [
                    {
                        td: [
                            {

                                value: makeInput({
                                    'class': 'btn-block no_radius_right',
                                    name: 'u_name',
                                    place:'新的马甲',
                                    url: '/?s=user/form&form=check_new_name',
                                    li:{
                                        value: '{result}'
                                    }
                                })
                            },
                            {
                                value: makeBtn({type:'submit', value:'提交修改', 'class': 'btn btn-info no_radius_left'})
                            }
                        ]
                    }
                ]
            })
        }), 420, 100);

    }

    //修改邮箱
    function editEmail() {
        msgView('修改邮箱', makeForm({
            'url': '/?s=user/edit_email&json=true',
            success_key: 'id',
            success_value: '0043',
            success_func: function (e) {
                msgTis(e.msg);
                setTimeout(function () {
                    reloadThisPage();
                }, 500)
            },
            err_func: function (e) {
                msgTis(e.info);
            },
            'value': makeTable({
                tr_1: [
                    {
                        td: [
                            {

                            value: makeInput({
                                        'class': 'btn-block no_radius_right',
                                        name: 'new_email',
                                        place:'新邮箱',
                                        url:'/?s=user/form&form=check_new_email',
                                        li:{
                                            value: '{result}'
                                        }
                                    })
                            },
                            {
                                value: makeBtn({
                                    'value': '获取邮件',
                                    'class': 'btn btn-default no_radius_left',
                                    'type': 'button',
                                    click: function () {
                                        var newEmail = new_email.value;
                                        if(!newEmail) {
                                            msgTis('请先输入新的邮箱');
                                            return;
                                        }
                                        postAndDone({
                                            'post_url': '/?s=user/send_mail&json=true',
                                            'post_data': {newemail: newEmail.value},
                                            'success_key': 'id',
                                            'success_value': '0023',
                                            'success_func': function () {
                                                msg('发送成功，请登录邮箱查看验证码');
                                            },
                                            err_func: function (re) {
                                                msg(re.msg);
                                            }
                                        });
                                    }
                                })
                            }
                        ]
                    },
                    {
                        td: [
                            {
                                padding_top: '10px',
                                value: makeInput({
                                    'class': 'btn-block no_radius_right',
                                    name: 'email_code',
                                    place: '邮箱验证码'
                                })
                            },
                            {
                                padding_top: '10px',
                                value: makeBtn({type:'submit', value:'提交修改', 'class': 'btn btn-info no_radius_left'})
                            }
                        ]
                    }
                ]
            })
        }), 465, 100);
    }
    //修改手机
    function editTel( ) {
        msgView('修改手机', makeForm({
            'url': '/?s=user/edit_phone&json=true',
            success_key: 'id',
            success_value: '0043',
            success_func: function (e) {
                msgTis(e.msg);
                setTimeout(function () {
                    reloadThisPage();
                }, 500)
            },
            err_func: function (e) {
                msgTis(e.info);
            },
            'value': makeTable({
                tr_1: [
                    {
                        td: [
                            {
                                colspan: '2',
                                value: makeInput({
                                    'class': 'btn-block',
                                    name: 'old_phone',
                                    place:'当前手机',
                                    limit: 'int', maxlen: 11,
                                    null_func: function () {
                                        msgTis('手机呢？');
                                    }
                                })
                            }
                        ]
                    },
                    {
                        td: [
                            {
                                padding_top: '10px',
                                value: makeInput({name:'new_tel', 'class': 'btn-block no_radius_right', place:'新的手机', type: 'text',
                                    limit: 'int', maxlen: 11,
                                    value: '',
                                    null_func: function () {
                                        msgTis('新手机呢？');
                                    }
                                })
                            }
                            ,{
                                padding_top: '10px',
                                value:  makeBtn({value:'获取短信',
                                    'class': 'btn btn-info btn-block no_radius_left',
                                    rest_time: smsRestTime,
                                    click: function (obj) {
                                        var oldTel = old_phone.value;
                                        var newTel = new_tel.value;
                                        if (!oldTel) {
                                            msgTis('请输入您的旧手机');
                                            return;
                                        }
                                        if (!newTel) {
                                            msgTis('请输入您的新手机');
                                            return;
                                        }
                                        postAndDone({
                                            post_url: '/?s=user/get_phone_code_to_change_phone&json=true',
                                            post_data: {
                                                old_phone: oldTel,
                                                new_phone: newTel
                                            },
                                            success_key: 'id',
                                            success_value: '0376',
                                            success_func: function () {
                                                msgTis('发送成功，请勿将手机短信告知他人');
                                                obj.subTime(60);
                                            },
                                            err_func: function (e) {
                                                msgTis(e.info);
                                            }
                                        });
                                    }
                                })
                            }
                        ]
                    },
                    {
                        td: [
                            {
                                padding_top: '10px',
                                value: makeInput({name:'code', 'class': 'btn-block no_radius_right', place:'短信验证码', type: 'text',
                                    limit: 'int', maxlen: 6,
                                    value: '',
                                    null_func: function () {
                                        msgTis('验证码呢？');
                                    }
                                })
                            },
                            {
                                padding_top: '10px',
                                value: makeBtn({type:'submit', value:'提交修改', 'class': 'btn btn-info btn-block no_radius_left'})
                            }
                        ]
                    }
                ]
            })
        }), 420, 100);
    }
</script>
