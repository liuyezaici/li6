var currentPage=1;
//自定义sql查询
// table|admin_power=>a||fields=>id,title||where=>status=1||order=>order_id desc
var getSqlDataApi = '/?s=juzi&do=get_sql_data'; //自定义sql的通用查询接口
var getOneDataApi = '/?s=ujuzi&do=get_one_data&json=true'; //自定义sql的通用查询接口
function getSqlData(whereSql, successFunc) {
    successFunc = successFunc || null;
    whereSql = whereSql || null;
    var getUrl = getSqlDataApi;
    var postData = {};
    if(whereSql) {
        var sqlArray = whereSql.split('||'), array_;
        sqlArray.forEach(function (str_) {
            array_ = str_.split('=>');
            postData[array_[0]] = array_[1];
        });
    }
    // console.log(postData);
    postAndDone({
        post_url: getUrl,
        post_data: postData,
        success_key: 'id',
        success_value: '0038',
        success_func: successFunc,
        err_func: function (response) {
            noLoading();
            msg(response.msg);
        }
    });
}
//加载单个数据
function loadOneData(sid, callBackObj) {
    var getUrl = getOneDataApi + '&id=' + sid;
    postAndDone({
        post_url: getUrl,
        success_key: 'id',
        success_value: '0038',
        success_func: function (response) {
            callBackObj['data'] = response['info']; //数据渲染对象
        },err_func: function (e) {
            if(e.id=='0000') {
                hideNewBox();
                loginIn();
                return;
            }
            hideNewBox();
            hideNewBox();
            msgTis(e.info);
        }
    });
}
$(function () {
    //头部
    var header = makeDiv({
        'id': 'navigation',
        'max_width': '960px',
        'class': 'container',
        value: makeDiv({
            'class': 'navbar navbar-default',
            value: [
                makeBtn({
                    'class': 'navbar-toggle',
                    'data-toggle': 'collapse',
                    'data-target': '#top_menu',
                    value: '<span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>'
                }),
                makeDiv({
                    'class': 'navbar-header',
                    value: makeA({
                        href: '/',
                        target: '_parent',
                        value: makeImg({
                            'class': 'navbar-brand',
                            padding: 0,
                            src: '/resource/front/images/logo.png'
                        })
                    })
                }),
                makeDiv({
                    'id': 'top_menu',
                    'class': 'navbar-collapse collapse navbar-responsive-collapse ',
                    value: [
                        makeList({
                            'margin_left': '20px',
                            'margin_right': '20px',
                            'class': 'nav navbar-nav navbar-right',
                            li: [
                                {
                                    'id': 'status_box1',
                                    value: 'loading'
                                },
                                {
                                    'id': 'status_box2',
                                    value: 'loading2'
                                },
                                {
                                    'id': 'status_box3',
                                    value: ''
                                }
                            ]
                        })
                    ]
                })
            ]
        })
    });

    //获取文章数据
    // uri: keyword=linux
    window.getPageData = function(uri, successFunc) {
        var sql = 'table|juzi=>a||table|c_user=>u|a.uid = u.u_id|l||fields=>a.id,a.title,a.ctime,u.u_nick,u.u_pwd||order=>a.id desc||size=>10';
        uri = uri || '';
        if(uri) {
            uri = $.url.decode(uri);
            sql += "||" + uri;
        }
        // console.log(uri);
        // console.log(sql);
        // return;
        successFunc = successFunc || null;
        if(!successFunc) successFunc = function(response) {
            var data = response['info'];
            // console.log(data);
            articleListBox.data = data;
            var pageData = data['page_data'];
            var page = pageData['pagenow'] || 1;
            var pageSize = pageData['pagesize'] || 10;
            var total = pageData['total'] || 0;
            var whereData = data['where'] || {};
            // console.log(topSearchForm);
            topSearchForm['data'] = whereData;
            currentPage = page;
            var newMenu = makePage({
                page: page,
                pagesize: pageSize,
                total: total,
                click: function (obj, newPage) {
                    var newUri = topSearchForm.serialize();
                    currentPage = newPage;
                    window.location = '#where=>'+ newUri +'||page=>'+ newPage;
                }
            });
            if(topSearchForm.menuObj) {
                topSearchForm.menuObj.remove();
            }
            topSearchForm.menuObj = newMenu;
            leftBox.append(newMenu);
        };
        getSqlData(sql,successFunc);
    };

    $('body').append(header);
    checkLogin();

    //检测url的hash 自动跳转
    var _checkUrlHash = function() {
        var hash = window.gethash();
        if(!hash) {
            return;
        }
        getPageData(hash, null);
    };
    //获取页面hash
    window.gethash = function(){
        return window.location.hash.replace(/^#/,"");
    };
    //页面后退 跟踪url
    var _historyBackEvent = function() {
        window.onhashchange = function() {
            var hash = window.gethash();
            if(!hash) {
                window.location.reload();
                return;
            }
            _checkUrlHash();
        };
    };
    _historyBackEvent(); //页面后退跟踪hash
    _checkUrlHash();//检测url的hash 自动跳转
});

var articleListBox;
var leftBox;
$(function () {
    leftBox = makeDiv({
        'class': 'col col-md-12',
        value: [
            makeDiv({
                'class': 'panel panel-success',
                data_from: {
                    func: function (obj) {
                        articleListBox = obj;
                        if(window.gethash()) return; //有hash 会自动加载文章
                        getPageData('page=>1');
                    }
                },
                value: [
                    makeDiv({
                        'class': 'panel-heading',
                        value: makeH1({
                            'class': 'panel-title',
                            'value': '句子'
                        })
                    }),
                    makeDiv({
                        'data': '{page_data}',
                        'hidden': "{{total}>0}",
                        'class': 'alert alert-warning',
                        'margin': '0',
                        value: "没有找到句子:{total}"
                    }),
                    makeList({
                        'class': 'list-group',
                        'data': '{list_data}',
                        li: {
                            'show': '{id}',
                            'class': 'list-group-item',
                            'value': '[{u_nick}] ' +
                            ' <a href="/juzi/read/{id}">{title}</a>  ' +
                            '<a href="javascript: void(0);" target="_self" onclick="editJuzi(\'{id}\')" class="badge badge-info ">编辑</a> '
                        }
                    })
                ]
            })
        ]
    });
    var middleBox = makeDiv({
        value: [
            makeDiv({
                'class': 'col  col-md-12',
                value:  makeForm({
                    'class': 'well',
                    'name': 'topSearchForm',
                    submit: function(obj, e) {
                        e.preventDefault();
                        currentPage = 1;
                        obj.find("input[name='page']").val(1); //搜索时 页码归1
                        var newUri = obj.serialize();
                        // console.log(newUri);
                        // return;
                        window.location = '#where=>'+newUri;
                        return false;
                    },
                    value: makeDiv({
                        'class': 'input-group',
                        value: [
                            makeBtn({
                                value: '发布',
                                'class': 'btn btn-success',
                                click: function () {
                                    msgView('写句子', makeForm({
                                        'url': '/?s=ujuzi&do=add&json=true',
                                        success_key: 'id',
                                        success_value: ['0043', '0113'],
                                        success_func: function (e) {
                                            hideNewBox();
                                            msgTis(e.msg);
                                            getPageData('page=>1');
                                        },
                                        err_func: function (e) {
                                            if(e.id=='0000') {
                                                loginIn();
                                                return;
                                            }
                                            msgTis(e.msg+e.info);
                                        },
                                        'value': makeTable({
                                            tr_1: [
                                                {
                                                    td: [
                                                        {

                                                            value: makeEditor({
                                                                'class': 'form-control btn-block input-group-lg no_radius',
                                                                'type': 'text',
                                                                'style': 'height: 162px',
                                                                name: 'title',
                                                                place:'句子'
                                                            })
                                                        }
                                                    ]
                                                },
                                                {
                                                    td: [
                                                        {
                                                            value: makeBtn({type:'submit', value:'发布', 'class': 'btn btn-primary btn-block btn-lg no_radius'})
                                                        }
                                                    ]
                                                }
                                            ]
                                        })
                                    }), 600, 430);
                                }
                            }),
                            makeInput({
                            name: 'title|like',
                            'place': '关键词',
                            value: '{title}',
                            'clear': true
                        }), makeBtn({
                            type: 'submit',
                            'class': 'btn btn-success',
                            value: '搜索'
                        })
                        ]
                    })
                })
            })
        ]
    });

    var mainBody = makeDiv({
        'class': 'container',
        'max_width': '960px',
        value: makeDiv({
            'class': 'row',
            value: [middleBox, leftBox]
        })
    });
    $('body').append(mainBody);

    var bottom = makeDiv({
        'class': 'container',
        'max_width': '960px',
        value: [
            makeDiv({
                'class': 'alert text-center',
                value: [
                    makeP({
                        value : "2018.5.11首次尝试纯js首页"
                    }),
                    makeA({
                        href: 'http://www.beian.miit.gov.cn',
                        value: '粤ICP备16054687号'
                    })
                ]
            })
        ]
    });
    $('body').append(bottom);
});
//编辑句子
function editJuzi(id) {
    msgView('编辑句子', makeForm({
        'url': '/?s=ujuzi&do=edit&json=true&id='+ id,
        success_key: 'id',
        success_value: ['0043', '0113'],
        success_func: function (e) {
            hideNewBox();
            msgTis(e.msg);
            getPageData('page=>'+ currentPage);
        },
        err_func: function (e) {
            if(e.id=='0000') {
                loginIn();
                return;
            }
            msgTis(e.msg+e.info);
        },
        data_from:  {
            func: function (obj) {
                loadOneData(id, obj);
            }
        },
        'value': makeTable({
            tr_1: [
                {
                    td: [
                        {

                            value: makeEditor({
                                'class': 'form-control btn-block input-group-lg no_radius',
                                'type': 'text',
                                'style': 'height: 162px',
                                name: 'title',
                                value: '{title}',
                                place:'句子'
                            })
                        }
                    ]
                },
                {
                    td: [
                        {
                            value: makeBtn({type:'submit', value: '编辑', 'class': 'btn btn-primary btn-block btn-lg no_radius'})
                        }
                    ]
                }
            ]
        })
    }), 600, 430);
}