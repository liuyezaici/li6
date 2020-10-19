
//自定义sql查询
// table|admin_power=>a||fields=>id,title||where=>status=1||order=>order_id desc
var getSqlDataApi = '/?do=get_sql_data'; //自定义sql的通用查询接口
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
function getParentSelect(obj) {
    getSqlData('table|admin_power=>a||fields=>id,title||where=>status=1||order=>order_id desc||size=>50',
        function(response) {
            var selectData=response['info'];
            if(!selectData) console.log('获取不到select的data');
            selectData = [{id: 0, title: '全部'}].concat(selectData);
            obj.data = selectData; //数据渲染select
        }
    );
}
$(function () {
    //头部
    var header = makeDiv({
        'id': 'navigation',
        'max-width': '960px',
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
        var sql = 'table|s_articles=>a||table|s_articles_types=>t|a.a_typeid = t.t_id|l||fields=>a.a_id,a.a_typeid,a.a_title,a.a_addtime,t.t_title||order=>a.a_id desc||size=>15||where=>a_tmp=0';
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
            // console.log(articleListBox);
            articleListBox.data = data;
            var pageData = data['page_data'];
            var page = pageData['pagenow'] || 1;
            var pageSize = pageData['pagesize'] || 10;
            var total = pageData['total'] || 0;
            var whereData = data['where'] || {};
            // console.log(topSearchForm);
            topSearchForm['data'] = whereData;
            var newMenu = makePage({
                page: page,
                pagesize: pageSize,
                total: total,
                click: function (obj, newPage) {
                    var newUri = topSearchForm.serialize();
                    window.location = '/#where=>'+ newUri +'||page=>'+ newPage;
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

var articleListBox = {};
var leftBox;
$(function () {
    leftBox = makeDiv({
        'class': 'col col-md-9',
        value: [
            makeDiv({
                'class': 'panel panel-success',
                data_from: {
                    func: function (obj) {
                        // console.log(obj);
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
                            'value': '最新文章'
                        })
                    }),
                    makeDiv({
                        'data': '{page_data}',
                        'hidden': "{{total}>0}",
                        'class': 'alert alert-warning',
                        'margin': '0',
                        value: "没有找到文章:{total}"
                    }),
                    makeList({
                        'class': 'list-group',
                        'data': '{list_data}',
                        li: {
                            'show': '{a_id}',
                            'class': 'list-group-item',
                            'value': '[<a href="/?type={t_id}">{t_title}</a>] <a href="/article/read/{a_id}">{a_title}</a>'
                        }
                    })
                ]
            })
        ]
    });
    var rightBox = makeDiv({
        'class': 'col col-md-3',
        value: [ 
            makeDiv({
                'class': 'well ',
                value:  makeForm({
                    'name': 'topSearchForm',
                    submit: function(obj, e) {
                        e.preventDefault();
                        var newUri = obj.serialize();
                        // console.log(newUri);
                        // return;
                        window.location = '/#where=>'+newUri;
                        return false;
                    },
                    value: [makeInput({
                        type: 'hidden',
                        'class': 'hidden',
                        name: 'a_typeid',
                        value: '{a_typeid}'
                    }), makeDiv({
                        'class': 'input-group',
                        value: [makeInput({
                            name: 'a_title|like',
                            'place': '关键词',
                            value: '{a_title}',
                            'clear': true
                        }), makeBtn({
                            type: 'submit',
                            'class': 'btn btn-success',
                            value: '搜索'
                        })
                        ]
                    })]
                })
            }),
            makeDiv({
                'class': 'panel panel-info',
                data_from: {
                    func: function (obj) {
                        // console.log(obj);
                        getSqlData('table|s_articles_types=>a||fields=>t_id,t_title||where=>t_status=0||order=>t_id desc||size=>15',
                            function(response) {
                                obj.data=response['info']['list_data'];
                            }
                        );
                    }
                },
                value: [
                    makeDiv({
                        'class': 'panel-heading',
                        value: makeH1({
                            'class': 'panel-title',
                            'value': '分类 <a href="/#" target="_parent" class="badge">全部</a>'
                        })
                    }),
                    makeList({
                        'class': 'list-group',
                        li: {
                            'class': 'list-group-item',
                            'value': '<a href="/#where=>a_typeid={t_id}" target="_parent">{t_title}</a>'
                        }
                    })
                ]
            })
        ]
    });

    var mainBody = makeDiv({
        'class': 'container',
        'max-width': '960px',
        value: makeDiv({
            'class': 'row',
            value: [leftBox, rightBox]
        })
    });
    $('body').append(mainBody);

    var bottom = makeDiv({
        'class': 'container',
        'max-width': '960px',
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