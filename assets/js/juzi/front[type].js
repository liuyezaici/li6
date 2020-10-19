(function (global__, $) {

    //搜索数据
    function searchFormGo(formObj, listTableObj, pageObj, renewPageFalse) {
        if(isUndefined(renewPageFalse)) renewPageFalse = true;
        var formData = formObj.serialize();
        var ajaxUrl = formObj.url;
        if(ajaxUrl.indexOf('?') ==-1) {
            ajaxUrl += '?' + formData;
        } else {
            ajaxUrl += '&' + formData;
        }
        loading();
        postAndDone({
            url: ajaxUrl,
            success_func: function (responseData) {
                noLoading();
                listTableObj['data'] = responseData['rows'];
                if(renewPageFalse) {//刷新分页与否 主动点击分页无需刷新
                    var pageOpt = pageObj['options'];
                    pageOpt['page'] = responseData['page'];
                    pageOpt['pagesize'] = responseData['page_size'];
                    pageOpt['total'] = responseData['total'];
                    pageObj.renew(pageOpt);
                }
            },
        });
    }

    global__.frontCfg = {
        wapBodyId: 'lr_body'  //定义 页头 页中 页脚 的id
        ,wapTopId: 'top_menu'
        ,checkBindMobile: '/adppp/usercenter/api/user/checkIsbangMobile/'   //检测是否绑定手机
        ,getSmsToLoginApi: '/adppp/sms/api/index/send/'   //获取短信
        ,submitBindMobileUrl: '/adppp/usercenter/api/user/changemobile'   //修改手机
        ,tokenKey: 'token'   //
        ,socketUrl: ''   //socketUrl 连接设备socket的url
        ,secrectAesKey: 'l~!*(){]Uvs<2>.r'   //aes加密密钥
        ,mustBindMobile: true  //是否强制买家绑定手机
        ,submitLoginUrl: '/adppp/usercenter/api/user/login'  //登录的url
        ,submitRegUrl: '/adppp/usercenter/api/user/register'  //注册的url
        ,sendEmailUrl: '/adppp/usercenter/api/user/sendEmail'  //发送邮件
        ,editPwdUrl: '/adppp/usercenter/api/user/editPwd'  //修改密码
        ,checkLoginUrl: '/juzi/System/checklogin'  //检测登录的url
        ,checkLoginUrl: '/juzi/System/checklogin'  //检测登录的url
        ,logoutUrl: '/juzi/System/logout'  //退出登录
        ,submitJuziUrl: '/adppp/juzi/api/index/add'  //发布句子
        ,getMyAllTypesUrl: '/adppp/juzi/api/juzitype/myalltypes'  //我的分类 全部
        ,searchAuthorUrl: '/adppp/juzi/api/juziauthor/search'  //搜索作者
        ,searchFromUrl: '/adppp/juzi/api/juzifrom/search'  //搜索来源
        ,myJuzisUrl: '/adppp/juzi/api/index/index'  //我的句子 分页
        ,delJuziUrl : '/adppp/juzi/api/index/del/id/' //删除句子
        ,editJuziUrl : '/adppp/juzi/api/index/edit/id/' //修改句子
        ,getJuziUrl : '/adppp/juzi/api/index/get/id/' //获取句子
        ,myTypesUrl: '/adppp/juzi/api/juzitype/index/index'  //我的分类 分页
        ,getTypeUrl : '/adppp/juzi/api/juzitype/get/id/' //获取分类
        ,addTypeUrl : '/adppp/juzi/api/juzitype/add' //添加分类
        ,editTypeUrl : '/adppp/juzi/api/juzitype/edit/id/' //修改分类
        ,delTypeUrl : '/adppp/juzi/api/juzitype/del/id/' //删除分类
        ,hasLoginHtml: function(nickname, username) {
            var link = (username == 'admin') ? '/admin/index': 'javascript: void(0);';
            return' <li><a target="_self" href="'+ link +'" class="my_btn">'+ nickname +'</a></li>\n' +
        '                <li><a target="_self" href="javascript: void(0);" class="write_btn">发布</a></li>\n' +
        '                <li><a target="_self" href="javascript: void(0);" class="my_juzi">句子</a></li>\n' +
        // '                <li><a target="_self" href="javascript: void(0);" class="my_types">分类</a></li>\n' +
        '                <li><a target="_self" href="javascript: void(0);" class="logout_btn">退出</a></li>';
        }
        ,notLoginHtml: ' <li><a target="_self" href="javascript: void(0);" class="login_btn">登录</a></li>\n' +
        '                <li><a target="_self" href="javascript: void(0);" class="forget_btn">忘记密码</a></li>\n' +
        '                <li><a target="_self" href="javascript: void(0);" class="reg_btn">注册</a></li>'
    };
    //公共全局方法
    global__.frontFunc = {
        tokenPost: function(options) {
            options = options || {};
            if(!options['post_data']) options['post_data'] = {};
            options['post_data']['token'] = global__.userToken;
            postAndDone(options);
        },
        checkLogin : function () {
            //检测登录
            postAndDone({
                url: frontCfg.checkLoginUrl,
                success_key: 'code',
                success_val: '1',
                success_func: function (response) {
                    var data_ = response.data;
                    var nickname = data_.nickname || data_.username;
                    var topMenu = $('#'+global__.frontCfg.wapTopId).find('ul');
                    topMenu.html(global__.frontCfg.hasLoginHtml(nickname, data_.username));
                    topMenu.find('.write_btn').click(function () {
                        global__.frontFunc.writeJuzi();
                    });
                    topMenu.find('.my_types').click(function () {
                        global__.frontFunc.myTypes();
                    });
                    topMenu.find('.my_juzi').click(function () {
                        global__.frontFunc.myJuzis();
                    });
                    topMenu.find('.my_btn').click(function () {
                        if(data_.local_uid == 1) {
                            window.location = '/admin';
                        }
                    });
                    topMenu.find('.logout_btn').click(function () {
                        global__.frontFunc.logOut();
                    });
                },
                err_func: function (response) {
                    var topMenu = $('#'+global__.frontCfg.wapTopId).find('ul');
                    topMenu.html(global__.frontCfg.notLoginHtml);
                    topMenu.find('.login_btn').click(function () {
                        global__.frontFunc.loginIn();
                    });
                    topMenu.find('.reg_btn').click(function () {
                        global__.frontFunc.reg();
                    });
                    topMenu.find('.forget_btn').click(function () {
                        global__.frontFunc.forget();
                    });
                }
            })
        },
        //检测是否绑定手机
        ifBindMobile : function () {
            global__.frontFunc.tokenPost({
                url: global__.frontCfg.checkBindMobile,
                success_key: 'code',
                success_value: '1',
                success_func: function (e) {

                },
                err_func: function (e) {
                    global__.frontFunc.gotoBindMobile();
                    msgTisf(e.msg);
                },
            })
        },
        loginIn: function () {
            hideAllBox();
            var loginBodyObj = makeForm({
                'name': '',
                'type': 'post',
                url: global__.frontCfg.submitLoginUrl,
                value:  [
                    makeTable({
                        tr_1: [{
                            id: 'account_tr',
                            td: {
                                value: makeInput({
                                    place: '帐号/手机',
                                    name: 'account',
                                    'class': 'no_border no_radius input-group-lg btn-block',
                                    null_func: function (data) {
                                        msgTisf('请输入帐号');
                                    }
                                })
                            }
                        },{
                            td: {
                                value: makeInput({
                                    place: '密码',
                                    'class': 'no_border no_radius input-group-lg btn-block',
                                    type: 'password',
                                    name: 'password',
                                    null_func: function (data) {
                                        msgTisf('请输入密码');
                                    }
                                })
                            }
                        },{
                            td: {
                                colspan: '2',
                                style: "padding-top: 10px;",
                                value: makeBtn({
                                    value: '登 录',
                                    type: 'submit',
                                    'class': 'btn btn-lg btn-info btn-block no_radius'
                                })
                            }
                        },{
                            'class': 'more_tr',
                            td: {
                                colspan: '2',
                                style: "padding: 20px 10px 0 0; text-align: right;",
                                value: [
                                    makeA({
                                        'class': 'btn',
                                        value: '忘记密码',
                                        click: "frontFunc.forget();"
                                    }),
                                    makeA({
                                        'class': 'btn',
                                        value: '注册',
                                        style: "margin-left: 20px;",
                                        click: "frontFunc.reg();"
                                    })]
                            }
                        }]
                    })],
                submit: function (obj, ev) {//提交时回调
                     loading(true);
                },
                success_key: 'code',
                success_val: '1',
                success_func: function (data) {
                    noLoading();
                    hideAllBox();
                    msgTisf(data.msg);
                    global__.frontFunc.checkLogin();
                },
                err_func: function (data) {
                    noLoading();
                    msgTisf(data.msg);
                }
            });
            msgWin('欢迎回来', loginBodyObj, 380, 60);
        }
        ,reg: function () {
            hideAllBox();
            var regBodyObj = makeForm({
                'name': '',
                'type': 'post',
                url: global__.frontCfg.submitRegUrl,
                value:  [
                    makeTable({
                        tr_1: [{
                            id: 'account_tr',
                            td: {
                                value: makeInput({
                                    place: '帐号',
                                    name: 'username',
                                    clear: 'true',
                                    autocomplete: "off",
                                    'class': 'no_border no_radius input-group-lg btn-block',
                                    null_func: function (data) {
                                        msgTisf('请输入帐号');
                                    }
                                })
                            }
                        },{
                            td: {
                                value: makeInput({
                                    place: '设置密码',
                                    'class': 'no_border no_radius input-group-lg btn-block',
                                    type: 'password',
                                    name: 'password1',
                                    clear: 'true',
                                    null_func: function (data) {
                                        msgTisf('请输入密码1');
                                    }
                                })
                            }
                        },{
                            td: {
                                value: makeInput({
                                    place: '重复密码',
                                    'class': 'no_border no_radius input-group-lg btn-block',
                                    type: 'password',
                                    name: 'password2',
                                    clear: 'true',
                                    null_func: function (data) {
                                        msgTisf('请输入密码2');
                                    }
                                })
                            }
                        },{
                            td: {
                                colspan: '2',
                                style: "padding-top: 10px;",
                                value: makeBtn({
                                    value: '注册',
                                    type: 'submit',
                                    'class': 'btn btn-lg btn-success btn-block no_radius'
                                })
                            }
                        },{
                            'class': 'more_tr',
                            td: {
                                colspan: '2',
                                style: "padding: 20px 10px 0 0; text-align: right;",
                                value: [
                                    makeA({
                                        'class': 'btn',
                                        value: '登录',
                                        style: "margin-left: 20px;",
                                        click: "frontFunc.loginIn();"
                                    })]
                            }
                        }]
                    })],
                submit: function (obj, ev) {//提交时回调
                     loading(true);
                },
                success_key: 'code',
                success_val: '1',
                success_func: function (data) {
                    noLoading();
                    hideAllBox();
                    msgTisf(data.msg);
                    global__.frontFunc.checkLogin();
                },
                err_func: function (data) {
                    noLoading();
                    msgTisf(data.msg);
                }
            });
            msgWin('注册', regBodyObj, 380, 60);
        } ,forget: function () {
            hideAllBox();
            var findBodyObj = [
                makeForm({
                    'name': '',
                    'type': 'post',
                    url: global__.frontCfg.sendEmailUrl,
                    value:  [
                        makeTable({
                            tr_1: [
                                {
                                td: {
                                    style: 'padding-bottom: 12px;',
                                    value: [
                                        makeInput({
                                            show: false,
                                            name: 'event',
                                            type: 'hidden',
                                            value: 'event',
                                        }),
                                        makeDiv({
                                            'class': 'input-group',
                                            'value': [makeInput({
                                                place: '您的邮箱',
                                                name: 'email',
                                                clear: 'true',
                                                autocomplete: "off",
                                                'class': 'form-control',
                                                null_func: function (data) {
                                                    msgTisf('请输入邮箱');
                                                }
                                            }),
                                                makeSpan({
                                                    'class': 'input-group-btn',
                                                    value: makeBtn({
                                                        value: '获取邮件',
                                                        type: 'submit',
                                                        'class': 'btn btn-default'
                                                    })
                                                })
                                            ]

                                        })
                                    ]
                                }
                            }]
                        })
                    ],
                    submit: function (obj, ev) {//提交时回调
                        loading(true);
                    },
                    success_key: 'code',
                    success_val: '1',
                    success_func: function (data) {
                        noLoading();
                        hideAllBox();
                        msgTisf(data.msg);
                        global__.frontFunc.checkLogin();
                    },
                    err_func: function (data) {
                        noLoading();
                        msgTisf(data.msg);
                    }
                }),
                makeForm({
                    'name': '',
                    'type': 'post',
                    url: global__.frontCfg.editPwdUrl,
                    value:  [
                        makeTable({
                            tr_1: [{
                                td: {
                                    style: 'padding-bottom: 12px;',
                                    value: [
                                       makeInput({
                                            place: '邮箱验证码',
                                            name: 'emailcode',
                                            clear: 'true',
                                            type: 'password',
                                            autocomplete: "off",
                                            'class': 'input-block',
                                            null_func: function (data) {
                                                msgTisf('请输入新密码');
                                            }
                                        })
                                    ]
                                },
                            },{
                                td: {
                                    style: 'padding-bottom: 12px;',
                                    value: [
                                       makeInput({
                                            place: '新密码',
                                            name: 'newpwd1',
                                            clear: 'true',
                                            type: 'password',
                                            autocomplete: "off",
                                            'class': 'input-block',
                                            null_func: function (data) {
                                                msgTisf('请输入新密码');
                                            }
                                        })
                                    ]
                                },
                            },{
                                id: 'account_tr',
                                td: {
                                    value: [
                                        makeInput({
                                            place: '重复密码',
                                            name: 'newpwd2',
                                            clear: 'true',
                                            type: 'password',
                                            autocomplete: "off",
                                            'class': 'input-block',
                                            null_func: function (data) {
                                                msgTisf('请输入新密码');
                                            }
                                        })
                                    ]
                                }
                            },{
                                td: {
                                    colspan: '2',
                                    style: "padding-top: 10px;",
                                    value: makeBtn({
                                        value: '提交修改',
                                        type: 'submit',
                                        'class': 'btn btn-lg btn-primary btn-block no_radius'
                                    })
                                }
                            },{
                                'class': 'more_tr',
                                td: {
                                    colspan: '2',
                                    style: "padding: 20px 10px 0 0; text-align: right;",
                                    value: [
                                        makeA({
                                            'class': 'btn',
                                            value: '登录',
                                            style: "margin-left: 20px;",
                                            click: "frontFunc.loginIn();"
                                        }),
                                        makeA({
                                            'class': 'btn',
                                            value: '注册',
                                            style: "margin-left: 20px;",
                                            click: "frontFunc.reg();"
                                        }),
                                    ]
                                }
                            }]
                        })],
                    submit: function (obj, ev) {//提交时回调
                        loading(true);
                    },
                    success_key: 'code',
                    success_val: '1',
                    success_func: function (data) {
                        noLoading();
                        hideAllBox();
                        msgTisf(data.msg);
                        global__.frontFunc.checkLogin();
                    },
                    err_func: function (data) {
                        noLoading();
                        msgTisf(data.msg);
                    }
                })
            ];
            msgWin('找回密码', findBodyObj, 380, 60);
        }
        ,writeJuzi: function (id) {
            id = id || 0;
            var dataFrom = null;
            if(id>0) {
                dataFrom = {
                    url: global__.frontCfg.getJuziUrl + id,
                    data_key: 'data'
                };
            }
            var topTitle = id > 0 ? '编辑句子': '写句子';
            var writeObj = makeForm({
                'name': '',
                'type': 'post',
                url: id > 0 ? global__.frontCfg.editJuziUrl + id : global__.frontCfg.submitJuziUrl,
                value:  [
                    makeTable({
                        data_from: dataFrom,
                        tr_1: [{
                            td: {
                                value: makeEditor({
                                    type: 'text',
                                    place: '内容(10-200字)',
                                    name: 'content',
                                    value: '{content}',
                                    style: 'height: 200px;padding-top: 15px;',
                                    'class': 'no_border no_radius input-group-lg btn-block',
                                    null_func: function (data) {
                                        msgTisf('请输入句子内容');
                                    },
                                    keyup: function (this_, e, scope) {
                                        var enterContent = this_.value;
                                        if(enterContent.match(/\n/)) {
                                            enterContent = enterContent.replace(/\n/ig, '');
                                            this_.value = enterContent;
                                        }
                                        var num = enterContent.length;
                                        scope.currentWriteNum = num;
                                    },
                                    lazyCall: function (this_, e, scope) {
                                        var enterContent = this_.value;
                                        if(enterContent.match(/\n/)) {
                                            enterContent = enterContent.replace(/\n/ig, '');
                                            this_.value = enterContent;
                                        }
                                        setTimeout(function () {
                                            var num = enterContent.length;
                                            scope.currentWriteNum = num;
                                        }, 500);
                                    }
                                })
                            }
                        },{
                            td: {
                                colspan: '2',
                                style: "padding-top: 10px;padding-left: 5px;",
                                value: [
                                    makeInput({
                                        name: 'authorStr',
                                        'place': '作者',
                                        url: global__.frontCfg.searchAuthorUrl,
                                        value: '{authorStr}',
                                        'post_min': 1, //至少要一个字符才提交 可以设置为5 表示输入5位数才开始检索
                                        'data_key': 'data', //数据的下标
                                        'value_key': 'id',
                                        clear: true,
                                        li_num: 5,
                                        menu: {
                                            li: {
                                                show: '{id}',
                                                title: '{title}',
                                                value: '{title}',
                                                click: function (li, e, scope) {
                                                    writeObj.findName('authorStr').value = li.attr('title') ;
                                                    li.parent.parent.hide();
                                                }
                                            }
                                        }
                                    })
                                ]
                            }
                        },{
                            td: {
                                colspan: '2',
                                style: "padding-top: 10px;padding-left: 5px;",
                                value: [
                                    makeInput({
                                        name: 'fromStr',
                                        'place': '句子来源',
                                        url: global__.frontCfg.searchFromUrl,
                                        value: '{fromStr}',
                                        'post_min': 1, //至少要一个字符才提交 可以设置为5 表示输入5位数才开始检索
                                        'data_key': 'data', //数据的下标
                                        'value_key': 'id',
                                        clear: true,
                                        li_num: 5,
                                        menu: {
                                            li: {
                                                show: '{id}',
                                                value: '{title}',
                                                title: '{title}',
                                                click: function (li, e, scope) {
                                                    writeObj.findName('fromStr').value = li.attr('title') ;
                                                    li.parent.parent.hide();
                                                }
                                            }
                                        }
                                    }),makeDiv({
                                        value: '格式:歌曲/电影/电视剧/小说/诗歌《xxx》'
                                    })
                                ]
                            }
                        },{
                            td: {
                                colspan: '2',
                                style: "padding-top: 10px;padding-left: 5px;",
                                value: [
                                    makeSpan({
                                        value: '当前字数:'
                                    }),
                                    makeSpan({
                                        'class': '{{{currentWriteNum}}>200?"red":"blue"}',
                                        value: '{{currentWriteNum}}'
                                    })
                                ]
                            }
                        },{
                            td: {
                                colspan: '2',
                                style: "padding-top: 10px;",
                                value: makeBtn({
                                    value: '提交',
                                    type: 'submit',
                                    'class': 'btn btn-lg btn-info btn-block'
                                })
                            }
                        }]
                    })],
                submit: function (obj, ev) {//提交时回调
                     loading(true);
                },
                success_key: 'code',
                success_val: '1',
                success_func: function (data) {
                    noLoading();
                    if(global__.renewJuzis) global__.renewJuzis();
                    msgTisf(data.msg);
                },
                err_func: function (data) {
                    noLoading();
                    msgTisf(data.msg);
                }
            });
            msgWin(topTitle, writeObj, 400, 60);
        }
        // ,modifyType: function (id) {
        //     id = id || 0;
        //     var topTitle = id > 0 ? '编辑分类' : '添加分类';
        //     var box = makeForm({
        //         'class': 'well',
        //         'type': 'post',
        //         url: id > 0 ? global__.frontCfg.editTypeUrl + id : global__.frontCfg.addTypeUrl,
        //         submit: function (obj, ev) {
        //         },
        //         success_key: 'code',
        //         success_value: ['1'],
        //         success_func: function (data) {
        //             hideNewBox();
        //             global__.frontFunc.typeTable.renewData();
        //             msgTisf('保存成功');
        //         },
        //         err_func: function (data) {
        //             msg(data.msg);
        //         },
        //         value: makeTable({
        //             'class': 'edit_table',
        //             data_from: {
        //                 url: global__.frontCfg.getTypeUrl + id,
        //                 data_key: 'data'
        //             },
        //             tr_1: [
        //                 {
        //                     td: [
        //                         {
        //                             'class': 'td_em',
        //                             value: makeSelect({
        //                                 name: 'row[opened]',
        //                                 value_key: 'id',
        //                                 title_key: 'title',
        //                                 menu: {
        //                                     data: [
        //                                         {id: 1, title: '公开'},
        //                                         {id: 0, title: '保密'}
        //                                     ],
        //                                 },
        //                                 li: {
        //                                     value: '{title}'
        //                                 },
        //                                 value:  '{opened}',
        //                             })
        //                         }, {
        //                             'class': 'input-group',
        //                             value: [makeInput({
        //                                 'class': 'form-control',
        //                                 name: 'row[title]',
        //                                 value: '{title}',
        //                                 place: '分类名',
        //                                 null_func: function () {
        //                                     msgTisf('分类名不能为空');
        //                                 }
        //                             }),
        //                                 makeDiv({
        //                                     'class': 'input-group-btn',
        //                                     value: makeBtn({
        //                                         'class': 'btn btn-success',
        //                                         'value': '保存',
        //                                         'type': 'submit'
        //                                     })
        //                                 })]
        //                         }]
        //                 }
        //             ]
        //         })
        //     });
        //     msgWin(topTitle, box, 520, 1);
        // }
        ,typeTable: null
        // ,myTypes: function () {
        //     hideAllBox();
        //     //搜索框
        //     var topSearchTypesForm = makeForm({
        //         url: global__.frontCfg.myTypesUrl,
        //         submit: function(form_, e) {
        //             e.preventDefault();
        //             form_.findName("page").val(1);
        //             searchFormGo(form_, global__.frontFunc.typeTable, myTypesPageObj);
        //             return false;
        //         },
        //         value: makeDiv({
        //             'class': 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
        //             'style': 'padding-bottom: 5px;',
        //             value: [
        //                 makeInput({
        //                     value: 1,
        //                     type: 'hidden',
        //                     name: 'page'
        //                 }),
        //                 makeSpan({
        //                     'class': 'input-group',
        //                     value: [
        //                          makeSpan({
        //                             'class': 'input-group',
        //                             value: [
        //                                 makeInput({
        //                                     name: 'typename',
        //                                     'clear': true,
        //                                     'place': '分类名'
        //                                 }),
        //                                 makeSpan({
        //                                     'class': 'input-group-btn',
        //                                     value:[
        //                                         makeBtn({
        //                                         'class': 'btn btn-success',
        //                                         value: '<i class="glyphicon glyphicon-search"></i> 搜索',
        //                                         type: 'submit'
        //                                     }),makeBtn({
        //                                             'class': 'btn btn-info',
        //                                             'value': '添加',
        //                                             click: function () {
        //                                                 global__.frontFunc.modifyType();
        //                                             }
        //                                         })
        //                                     ]
        //                                 }),
        //                             ]
        //                         })
        //                     ]
        //                 })
        //             ]
        //         })
        //     });
        //     //句子分类 列表
        //     this.typeTable = makeTable({
        //         data_from: {
        //             func: function (tableObj) {
        //                 searchFormGo(topSearchTypesForm, tableObj, myTypesPageObj);
        //             }
        //         },
        //         'style': 'margin-bottom: 0;',
        //         'class': 'table table-striped table-bordered table-hover',
        //         tr_top: [
        //             {
        //                 th: [
        //                     {
        //                         value: '分类'
        //                     },
        //                     {
        //                         value: '公开'
        //                     },
        //                     {
        //                         value: '操作'
        //                     }
        //                 ]
        //             }
        //         ],
        //         tr_default: [
        //             {
        //                 show: '{{this.length}==0}',
        //                 td: [
        //                     {
        //                         colspan: '3',
        //                         value: '没有分类'
        //                     }
        //                 ]
        //             }
        //         ],
        //         tr: [
        //             {
        //                 show: '{"{id}" !=""}',
        //                 td:
        //                     [
        //                         {
        //                             value: '{title}'
        //                         },
        //                         {
        //                             value: '{{opened}==1 ? "公开":"保密" }'
        //                         },
        //                         {
        //                             value: [
        //                                 makeBtn({
        //                                     'show': '{ {title}!="默认分类" }',
        //                                     'class': 'btn btn-xs btn-info',
        //                                     value: '编辑',
        //                                     data_id: '{id}',
        //                                     click: function (obj_) {
        //                                         var id = obj_.data_id;
        //                                         global__.frontFunc.modifyType(id);
        //                                     }
        //                                 }),
        //                                 makeBtn({
        //                                     'show': '{ {title}!="默认分类" }',
        //                                     'class': 'btn btn-xs btn-danger',
        //                                     value: '删除',
        //                                     id: '{id}',
        //                                     margin_left: '5px',
        //                                     click: function (obj_) {
        //                                         var id = obj_.id;
        //                                         msgConfirm('您是否确认要删除此分类？', '确定', '取消', function () {
        //                                             hideNewBox();
        //                                             postAndDone({
        //                                                 url: global__.frontCfg.delTypeUrl + id,
        //                                                 success_key: 'code',
        //                                                 success_value: '1',
        //                                                 success_func: function () {
        //                                                     global__.frontFunc.typeTable.renewData();
        //                                                     msgTisf('删除成功');
        //                                                 },
        //                                                 err_func: function (data) {
        //                                                     msgTisf(data.msg);
        //                                                 }
        //                                             })
        //                                         });
        //                                     }
        //                                 })
        //                             ]
        //                         }
        //                     ]
        //             }
        //         ]
        //     });
        //     //句子分类 分页框
        //     var myTypesPageObj = makePage({
        //         click: function (obj, newPage) {
        //             topSearchTypesForm.findName("page").val(newPage);
        //             searchFormGo(topSearchTypesForm,  global__.frontFunc.typeTable, myTypesPageObj, false);
        //         }
        //     });
        //     var juziTypeBox = makeDiv({
        //         value: [
        //             topSearchTypesForm, global__.frontFunc.typeTable, myTypesPageObj
        //         ]
        //     });
        //     msgWin('句子分类', juziTypeBox, 650, 1, {bg: false});
        // }
        ,juziTable: null
        ,myJuzis: function () {
            hideAllBox();
            //句子 分页框
            var myJuzisPageObj = makePage({
                click: function (obj, newPage) {
                    topSearchJuziForm.findName("page").val(newPage);
                    searchFormGo(topSearchJuziForm,  global__.frontFunc.juziTable, myJuzisPageObj, false);
                }
            });
            //搜索框
            var topSearchJuziForm = makeForm({
                url: global__.frontCfg.myJuzisUrl,
                submit: function(form_, e) {
                    console.log(e);
                    e.preventDefault();
                    form_.findName("page").val(1);
                    searchFormGo(form_, global__.frontFunc.juziTable, myJuzisPageObj);
                    return false;
                },
                value: makeDiv({
                    'class': 'col-xs-12 col-sm-12 col-md-12 col-lg-12',
                    'style': 'padding-bottom: 5px;',
                    value: [
                        makeInput({
                            value: 1,
                            type: 'hidden',
                            name: 'page'
                        }),
                        makeSpan({
                            'class': 'input-group',
                            value: [
                                makeSpan({
                                    'class': 'input-group',
                                    value: [
                                        makeInput({
                                            name: 'keyword',
                                            'clear': true,
                                            'place': '关键词'
                                        }),
                                        makeSpan({
                                            'class': 'input-group-btn',
                                            value:[
                                                makeBtn({
                                                    'class': 'btn btn-success',
                                                    value: '<i class="glyphicon glyphicon-search"></i> 搜索',
                                                    type: 'submit'
                                                }),makeBtn({
                                                    'class': 'btn btn-info',
                                                    'value': '发布',
                                                    click: function () {
                                                        global__.frontFunc.writeJuzi();
                                                    }
                                                })
                                            ]
                                        }),
                                    ]
                                })
                            ]
                        })
                    ]
                })
            });
            //句子 列表
            this.juziTable = makeTable({
                    data_from: {
                        func: function (tableObj) {
                            searchFormGo(topSearchJuziForm, tableObj, myJuzisPageObj);
                        }
                    },
                    'style': 'margin-bottom: 0;',
                    'class': 'table table-striped table-bordered table-hover',
                    tr_top: [
                        {
                            th: [
                                // {
                                //     value: '分类'
                                // },
                                {
                                    value: '内容'
                                },
                                {
                                    value: '操作'
                                }
                            ]
                        }
                    ],
                    tr_default: [
                        {
                            show: '{{this.length}==0}',
                            td: [
                                {
                                    colspan: '4',
                                    value: '没有句子'
                                }
                            ]
                        }
                    ],
                    tr: [
                        {
                            show: '{"{id}" !=""}',
                            td:
                                [
                                    // {
                                    //     value: [
                                    //         makeSpan({
                                    //             'class':  '{{opened}==1 ? "glyphicon glyphicon-eye-open":"glyphicon glyphicon-eye-close"}',
                                    //             'color':  '{{opened}==1 ? "#82afc0;":"#ccc;"}',
                                    //         }),
                                    //         makeSpan({
                                    //             style: 'margin-left: 5px;',
                                    //             value: '{typename}',
                                    //         })]
                                    // },
                                    {
                                        value: '<a href="/juzi/read/uri/{uri}">{content}</a>'
                                    },
                                    {
                                        value: [
                                            makeBtn({
                                                'class': 'btn btn-xs btn-info',
                                                value: '编辑',
                                                data_id: '{id}',
                                                click: function (obj_) {
                                                    var id = obj_.data_id;
                                                    global__.frontFunc.writeJuzi(id);
                                                }
                                            }),
                                            makeBtn({
                                                'class': 'btn btn-xs btn-danger',
                                                value: '删除',
                                                id: '{id}',
                                                margin_left: '5px',
                                                click: function (obj_) {
                                                    var id = obj_.id;
                                                    msgConfirm('您是否确认要删除此句子？', '确定', '取消', function () {
                                                        hideNewBox();
                                                        postAndDone({
                                                            url: global__.frontCfg.delJuziUrl + id,
                                                            success_key: 'code',
                                                            success_value: '1',
                                                            success_func: function () {
                                                                global__.frontFunc.juziTable.renewData();
                                                                msgTisf('删除成功');
                                                            },
                                                            err_func: function (data) {
                                                                msgTisf(data.msg);
                                                            }
                                                        })
                                                    });
                                                }
                                            })
                                        ]
                                    }
                                ]
                        }
                    ]
                });
            var juziTypeBox = makeDiv({
                value: [
                    topSearchJuziForm, global__.frontFunc.juziTable, myJuzisPageObj
                ]
            });
            msgWin('句子', juziTypeBox, 650, 1, {bg: false});
            global__.renewJuzis = function () {
                global__.frontFunc.juziTable.renewData();
            }
        }
        //检测是否登录
        ,isLogOut: function(code) {
            return global__.frontCfg.loginTimeOutCode == code;
        }
        //退出登录
        ,logOut: function() {
            postAndDone({
                url: global__.frontCfg.logoutUrl,
                success_key: 'code',
                success_val: '1',
                success_func: function () {
                    msgTisf('退出成功');
                    global__.frontFunc.checkLogin();
                },
            });
        }
        //提示绑定手机
        ,gotoBindMobile: function () {
            var diyForm = makeForm({
                'style': 'width: 90%;margin:0 auto 0.5rem auto;',
                'type': 'post',
                'url' : frontCfg.submitBindMobileUrl,
                value:  [
                    makeInput({type: 'hidden', name: 'event', value: 'changemobile'}),
                    makeTable({
                        tr_1: [{
                            id: 'account_tr',
                            td: [
                                {
                                    padding_top: '10px',
                                    value: [
                                        makeInput({
                                            name: 'new_mobile',
                                            size: 'lg',
                                            'class': 'btn-block no_radius_right' ,
                                            place: '您的手机',
                                            type: 'text',
                                            limit: 'int',
                                            maxlen: 11,
                                            value: '',
                                            null_func: function () {
                                                msgTisf('请输入您的手机');
                                            }
                                        })
                                    ]
                                }
                                ,{
                                    padding_top: '10px',
                                    value:  makeBtn({value:'获取短信',
                                        size: 'lg',
                                        'class': 'btn btn-default btn-block no_radius_left',
                                        rest_time: 0,
                                        click: function (obj) {
                                            var newTel = new_mobile.value;
                                            if (!newTel) {
                                                msgTisf('请输入您的手机');
                                                return;
                                            }
                                            global__.frontFunc.tokenPost({
                                                post_url: global__.frontCfg.getSmsToLoginApi,
                                                post_data: {
                                                    mobile: newTel,
                                                    'event': 'changemobile',
                                                },
                                                success_key: 'code',
                                                success_value: '1',
                                                success_func: function () {
                                                msgTisf('发送成功，请勿将手机短信告知他人');
                                                console.log(obj);
                                                obj.subTime(60);
                                            },
                                            err_func: function (e) {
                                                msgTisf(e.msg);
                                            }
                                        });
                                        }
                                    })
                                }
                            ]
                        },{
                            td: [
                                {
                                    padding_top: '10px',
                                    value: makeInput({
                                        name:'code',
                                        size: 'lg',
                                        'class': 'btn-block no_radius_right',
                                        place:'短信验证码', type: 'text',
                                        limit: 'int', maxlen: 6,
                                        value: '',
                                        null_func: function () {
                                            msgTisf('验证码呢？');
                                        }
                                    })
                                },
                                {
                                    padding_top: '10px',
                                    value: makeBtn({
                                        type:'submit',
                                        size: 'lg',
                                        value:'绑定',
                                        'class': 'btn btn-info btn-block no_radius_left'
                                    })
                                }
                            ]
                        } ]
                    })],
                success_key: 'code',
                success_value: '1',
                success_func: function (data) {
                    hideNewBox();
                    msgTisf(data.msg);
                },
                err_func: function (data) {
                    msgTisf(data.msg);
                }
            });
            msgWin('请先绑定手机', diyForm, 400, 1,{
                bg: true,//背景遮挡
                'class': 'new_loginbox',
                'canDrag': false,
                'closeBtn': false
            });
        }
    };
    global__.frontFunc.checkLogin();
})(this, jQuery);