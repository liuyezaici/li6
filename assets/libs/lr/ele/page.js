define(['require'], function (require) {
    var global = {};
    var objBindAttrsName = 'bind_attrs';
    var objAttrHasKh = 'obj_opt_has_kuohao';//obj的属性包含有{} 则可能绑定全局变量
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入

//创建上一页 下一页的分页功能
    global.makePage = function(sourceOptions, sureSource) {
        var core = require('core');
        sourceOptions = sourceOptions || {};
        sureSource = sureSource || false;
        var options = cloneData(sourceOptions);
        var pageBody = $('<ul></ul>');
        var setBind = getOptVal(options, ['bind'], '');
        if(!pageBody.sor_opt) {
            //必须克隆 否则后面更新会污染sor_opt
            pageBody.sor_opt = sureSource ?  cloneData(sourceOptions || {}) : cloneData(copySourceOpt(sourceOptions));
        }
        pageBody['current_page'] = 1;
        pageBody.totalPage = 0;
        pageBody.fromPage  = 0;
        pageBody.toPage  = 0;
        pageBody.gotoPage  = '';
        pageBody.gotoPageObj  = null;
        pageBody.setPageSize = null;//设置select的单页数量值
        pageBody.noNeedEven  = true;//不需要定义任何的点击事件 防止和系统的点击翻页冲突
        //支持value
        Object.defineProperty(pageBody, 'page', {
            get: function () {
                return parseInt(this['current_page']);
            },
            set: function(newP) {  //支持外部设值
                this.setPage(newP);
            }
        });
        //支持 page_size(单页数量) 的获取
        Object.defineProperty(pageBody, 'page_size', {
            get: function () {
                return parseInt(options['pageSize']);
            },
            set: function(newP) {
                if(pageBody.setPageSize) {
                    pageBody.setPageSize(newP);
                }
            }
        });
        Object.defineProperty(pageBody, 'pageSize', {
            get: function () {
                return parseInt(options['pageSize']);
            },
            set: function(newP) {
                if(pageBody.setPageSize) {
                    pageBody.setPageSize(newP);
                }
            }
        });
        pageBody.extend({
            //设置页面
            setPage: function (newP, exceptObj) {//数据被动同步
                if(newP > pageBody.totalPage) return;
                exceptObj = exceptObj || [];
                if(setBind) {//触发数据同步  触发赋值 */
                    if($.inArray(this, exceptObj) == -1) {
                        exceptObj.push(this);
                    }
                    updateBindObj($.trim(setBind), newP, exceptObj);
                }
                var li = pageBody.find("li[data-page='"+ newP +"']");
                if(li.length == 0 || newP-pageBody.fromPage<=2  || newP-pageBody.fromPage>= pageBody.pageBtnNum-2 ) { //跳度太大 页面不存在 需要重新生成
                    options['page'] = newP;
                    // console.log('options', options);
                    this.renew(options);
                } else {
                    li.addClass('active').siblings('.active').removeClass('active');
                }
                pageBody['current_page'] = newP;
                //不能触发点击事件 因为点击的概念和value更新的意义不是完全吻合
            },
            //主动更新数据
            renew: function (opt) {
                opt = opt || {};
                opt = $.extend({}, sourceOptions, opt);
                var hasSetData = !isUndefined(opt['data']);
                var defaultCfg = {
                    page: 1,
                    pageSize: 10,//单页数量
                    pagenum: 5, //显示页数
                    size: 'md',//分页的外观尺寸
                    total: 1
                };
                //兼容各自语法
                var data_ = opt['data'] ||{};
                var pageSize = getOptVal(opt, ['pagesize','page_size', 'pageSize'], 10);//单页显示数量
                // console.log('pageSize', pageSize, opt);
                var pageBtnNum = getOptVal(opt, ['pagenum', 'pageNum'], 5);//分页按钮显示的数量
                var pageType = getOptVal(opt, ['type'], 'default');//分页样式 default/btn
                var goto = getOptVal(opt, ['goto'], null);
                var selectPageSize = getOptVal(opt, ['selectPageSize', 'select_page_size'], null);//自定义单页的数量
                var pageClass = getOptVal(opt, ['class'], '');
                var diyClick = getOptVal(opt, ['click'], null);
                opt['page'] = opt['page'] || 1;
                opt['btnSize'] = (!opt['size'] || !setSize(opt['size'])) ? 'md' : opt['size'];//过滤size
                pageSize = parseInt(formatIfHasKuohao(pageSize, data_));
                pageBtnNum = parseInt(pageBtnNum);
                if(!pageBtnNum) pageBtnNum = 5;
                opt['btnSize'] = formatIfHasKuohao(getOptVal(opt, ['btnSize'], 'sm'), data_);
                //为兼容自定义页数菜单按钮，强制转两位数
                if(strInArray(opt['btnSize'], ['sm', 'small', 's']) !=-1) {
                    opt['btnSize'] = 'sm';
                } else if(strInArray(opt['btnSize'], ['x', 'xs']) !=-1) {
                    opt['btnSize'] = 'xs';
                } else if(strInArray(opt['btnSize'], ['x', 'xs']) !=-1) {
                    opt['btnSize'] = 'xs';
                } else if(strInArray(opt['btnSize'], ['m', 'md', 'middle', 'normal']) !=-1) {
                    opt['btnSize'] = 'md';
                } else if(strInArray(opt['btnSize'], ['l', 'lg', 'large', 'big']) !=-1) {
                    opt['btnSize'] = 'lg';
                }
                opt['page'] = parseInt(formatIfHasKuohao(getOptVal(opt, ['page'], 1), data_));
                opt['total'] = parseInt(formatIfHasKuohao(getOptVal(opt, ['total'], 0), data_));
                delProperty(opt, ['pagesize', 'page_size']);//统一大小写
                opt['pageSize'] = pageSize;//统一输出
                opt = $.extend({}, defaultCfg, opt);
                var $pageExtClass = 'pagination';
                if(pageClass) $pageExtClass = pageClass;
                if(opt['btnSize'] == 'lg') {
                    $pageExtClass += ' pagination-lg';
                } else if(opt['btnSize'] == 'sm') {
                    $pageExtClass += ' pagination-sm';
                } else if(sizeIsXs(opt['btnSize'])) {
                    $pageExtClass += ' pagination-xs';
                }
                opt['class_extend'] = $pageExtClass;
                var parentOpt = $.extend({}, opt);
                delProperty(parentOpt, ['click']);//父对象不需要点击事件
                //console.log(parentOpt);
                //page只有class无需再修改
                pageBody.attr('class', opt['class_extend']);
                //console.log('page:');
                //console.log(opt);
                optionDataFrom(pageBody, opt);
                var page = parseInt(opt.page);
                var pageSize = parseInt(pageSize);
                if(pageSize < 1 ) pageSize = 1;
                var totalNum = parseInt(opt.total);
                var totalPage = totalNum / pageSize;
                //console.log('totalNum:'+totalNum);
                //console.log('pageSize:'+pageSize);
                if(totalPage.toString().indexOf('.')!=-1) totalPage = parseInt(totalPage) + 1;
                //console.log('totalPage:'+totalPage);
                if(page>totalPage) {
                    page = totalPage;
                }
                pageBody['current_page'] = page;
                pageBody.totalPage = totalPage;
                pageBody.pageBtnNum = pageBtnNum;
                pageBody.empty();
                if(pageType=='default') {
                    var preLi = $('<li><a href="javascript: void(0)" target="_self">&laquo;</a></li>');
                } else if(pageType == 'btn') {
                    var preLi = $('<li><a href="javascript: void(0)" target="_self" class="endPage"> &lt; </a></li>');
                }
                preLi.on('click', function (e) {
                    var nowPage = pageBody['current_page'];
                    var thisToPage = parseInt(nowPage) - 1;
                    if(thisToPage <1) {
                        lrBox.msgTisf('noMorePage');
                    } else {
                        pageBody.gotoPage = '';
                        if(pageBody.gotoPageObj) pageBody.gotoPageObj.val('');
                        pageBody.setPage(thisToPage);
                    }
                });
                pageBody.append(preLi);

                pageBody.fromPage = page - parseInt(pageBtnNum/2);
                var toPage;
                if(pageBody.fromPage<1)  {
                    pageBody.fromPage = 1;
                    toPage = pageBody.fromPage + pageBtnNum;
                } else {
                    toPage = pageBody.fromPage + pageBtnNum ;
                }

                var i,li;
                if(pageBody.fromPage < 1) pageBody.fromPage = 1;
                if(toPage>totalPage) toPage = totalPage;
                if(toPage == totalPage) toPage = totalPage+1; //到达尾部 直接显示全部页码
                if(pageBody.fromPage == toPage) {
                    pageBody.fromPage = 1;
                }
                if(pageBody.fromPage == 1)  {
                    preLi.remove();
                }
                if(page > toPage) {
                    pageBody.fromPage = page-1;
                    toPage = toPage + pageBtnNum;
                    if(pageBody.fromPage < 1) pageBody.fromPage = 1;
                    if(toPage>totalPage) {
                        toPage = totalPage;
                    }
                }
                //console.log(toPage);
                var repeatNum = 0;
                for(i = pageBody.fromPage; i < toPage; i++) {
                    if(repeatNum >= pageBtnNum) {
                        toPage = repeatNum;
                        break;
                    }
                    li = $('<li data-page="'+ i +'"></li>');
                    li.append('<a href="javascript: void(0)" target="_self">'+ i +'</a>');
                    if(page == i) li.addClass('active');
                    li.on('click', function (e) {
                        var clickObj = $(this);
                        var pageNew = clickObj.attr('data-page');
                        // console.log('setPage', pageNew);
                        pageBody.setPage(pageNew);
                        pageBody.gotoPage = '';
                        if(pageBody.gotoPageObj) pageBody.gotoPageObj.val('');
                        if(diyClick) diyClick(li, pageNew, pageBody);
                    });
                    pageBody.append(li);
                    repeatNum ++;
                }
                if(pageType=='default') {
                    var nextLi = $('<li><a href="javascript: void(0)" target="_self"> &raquo; </a></li>');
                    nextLi.off().on('click', function (e) {
                        var nowPage = pageBody['current_page'];
                        var thisToPage = parseInt(nowPage) + 1;
                        if(thisToPage > totalPage) {
                            lrBox.msgTisf('noMorePage');
                        } else {
                            pageBody.gotoPage = '';
                            if(pageBody.gotoPageObj) pageBody.gotoPageObj.val('');
                            pageBody.setPage(thisToPage);
                        }
                    });
                    pageBody.append(nextLi);
                    //设置完所有属性后 再渲染对象属性，因为可能有attr:'{this.totalPage}';
                    strObj.formatAttr(pageBody, opt, 0, hasSetData);
                } else if(pageType == 'btn') {
                    var nowPage = pageBody['current_page'];
                    var senglue = (nowPage == totalPage || toPage>=totalPage )? null: $('<li><a href="javascript: void(0)" target="_self" class="endPage"> ... </a></li>');
                    var totalLi = (nowPage == totalPage || toPage>=totalPage )? null: $('<li><a href="javascript: void(0)" target="_self"> '+ totalPage +' </a></li>');
                    var nextLi = $('<li><a href="javascript: void(0)" target="_self" class="endPage"> &gt; </a></li>');
                    if(totalLi) {
                        totalLi.off().on('click', function (e) {
                            pageBody.setPage(totalPage);
                        });
                    }
                    nextLi.off().on('click', function (e) {
                        var nowPage = pageBody['current_page'];
                        var thisToPage = parseInt(nowPage) + 1;
                        if(thisToPage > totalPage) {
                            lrBox.msgTisf('noMorePage');
                        } else {
                            pageBody.gotoPage = '';
                            if(pageBody.gotoPageObj) pageBody.gotoPageObj.val('');
                            pageBody.setPage(thisToPage);
                        }
                    });
                    if(senglue)pageBody.append(senglue);
                    if(totalLi)pageBody.append(totalLi);
                    if(nextLi)pageBody.append(nextLi);
                }
                if(toPage >= totalPage) {
                    nextLi.remove();
                }
                if(goto) {
                    var gotoLi = $('<li><a><input class="togoPage" placeholder="Goto" /></a></li>');
                    var gotoPageObj = gotoLi.find('.togoPage');
                    if(pageBody.gotoPage) gotoPageObj.val(pageBody.gotoPage);
                    gotoPageObj.off().on('blur', function (e) {
                        var thisPage = parseInt($(this).val());
                        if(!thisPage || thisPage<1) return;
                        if(thisPage > totalPage) {
                            lrBox.msgTisf('noMorePage');
                            return;
                        }
                        pageBody.gotoPage = thisPage;
                        if(thisPage>totalPage) thisPage = totalPage;
                        pageBody.setPage(thisPage);
                    });
                    pageBody.gotoPageObj = gotoPageObj;
                    if(strInArray(goto, ['r', 'right']) !=-1) {
                        pageBody.append(gotoLi);
                    } else if(strInArray(goto, ['l', 'left']) !=-1) {
                        pageBody.prepend(gotoLi);
                    }
                }
                if(selectPageSize) {
                    var onchangeEven = getOptVal(selectPageSize, ['onchange', 'onChange'], null);
                    var defaultText = getOptVal(selectPageSize, ['text', 'defaultText'], 'Num');
                    var className = getOptVal(selectPageSize, ['class'], 'default');
                    var selectMenuObj = $('<li class="selectPageSize"><button type="button" class="btn btn-'+ opt['btnSize'] +' '+ className +'"> <span class="defaultText">' + pageSize + '</span> <span class="caret"></span>\n' +
                        '</button></li>');
                    var listVal = getOptVal(selectPageSize, ['value', 'values', 'val', 'list'], [10, 20, 30, 40, 50, 100, 200]);
                    var dir = getOptVal(selectPageSize, ['dir'], 'down');
                    if(strInArray(dir, ['down', 'up', 'd', 'u']) ==-1) dir = 'down';
                    var sizeMenu = $('<ul class="sizeMenu"></ul>');
                    if(strInArray(dir, ['down', 'd']) !=-1) {
                        sizeMenu.addClass('showDown');
                    } else {
                        sizeMenu.addClass('showUp');
                    }
                    var menuLi = [];
                    $.each(listVal, function (n, v) {
                        menuLi.push('<li><a tabindex="-1" href="javascript: void(0);" target="_self" data-val="'+ v +'">'+ v +'</a></li>');
                    });
                    sizeMenu.append(menuLi);
                    selectMenuObj.append(sizeMenu);
                    var textBtn = selectMenuObj.find('.btn');
                    textBtn.find('.defaultText').attr('title', defaultText);
                    pageBody.setPageSize = function (newSize) {
                        //修改默认配置的参数
                        options['pageSize'] = newSize;
                        textBtn.find('.defaultText').html(newSize);
                    };
                    textBtn.on({
                        'focus': function (e) {
                            sizeMenu.show();
                        },
                        'blur': function (e) {
                            setTimeout(function () {
                                sizeMenu.hide();
                            }, 160);
                        }
                    });
                    sizeMenu.find('li a').on('click', function (e) {
                        var newSize = parseInt($(this).attr('data-val'));
                        if(!isNumber(newSize) || !newSize) newSize = 10;
                        pageBody.setPageSize(newSize);
                        sizeMenu.hide();
                        pageBody.renew(options);
                        if(onchangeEven) {
                            onchangeEven(newSize, e, pageBody);
                        }
                    });
                    pageBody.append(selectMenuObj);
                }
            },
            //data更新时  page更新
            renewPageData: function(data) {
                //console.log('renewPageData self:');
                //console.log(data);
                var optSource = cloneData(options);
                optSource['data'] = data;
                this.renew(optSource);
            },
            //克隆当前对象
            cloneSelf: function() {
                var opt = cloneData(obj.sor_opt);
                return global.makePage(opt, true);
            },
            updates: function(dataName, exceptObj) {//数据被动同步
                //console.log('updates this');
                exceptObj = exceptObj || [];
                if(setBind && $.inArray(this, exceptObj) == -1) {
                    exceptObj.push(this);
                    this.setPage(getObjData($.trim(setBind)), exceptObj);
                }
                if(this[objBindAttrsName] && this[objBindAttrsName][dataName]) {
                    //console.log(getObjData(dataName));
                    if(strInArray('page', this[objBindAttrsName][dataName]) !=-1) this.setPage(getObjData(dataName));
                }
            }
        });
        objBindVal(pageBody, options);//数据绑定
        pageBody.renew(options);
        optionGetSet(pageBody, options); //允许外部修改
        return pageBody;
    }
    return global;
});

