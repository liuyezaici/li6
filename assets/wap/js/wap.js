(function (global__, $) {
    //定义路由事件
    global__.wapRouter = {
        loadedOnes: {},
        checkHasLoad: function (routerName) {
            return isUndefined(wapRouter.loadedOnes[routerName]) ? false : true;
        },
        _hashChangeFromUs : false,
        //跳转
        goto: function (router_) {
            var sourceRouter = router_; // abc?a=123
            if(router_.indexOf('?') != -1) router_ = router_.split('?')[0];
            if(router_.indexOf('&') != -1) router_ = router_.split('&')[0];
            if(wapRouter.checkHasLoad(router_)) {
                wapRouter._hashChangeFromUs = true;
                window.location.hash = sourceRouter;
                var cacheObj = wapRouter.loadedOnes[router_];
                cacheObj();
                var needLogin =  (!isUndefined(wapRouter.routers[router_]) && !isUndefined(wapRouter.routers[router_]['needLogin'])) ? wapRouter.routers[router_]['needLogin'] : false;
                if(needLogin) {
                    global__.wapPubFunc.checkLogin();
                    return;
                }
                //释放当前调转 下次onhashchange继续可以侦听
                setTimeout(function () {
                    wapRouter._hashChangeFromUs = false;
                }, 200);
            } else {
                wapRouter._hashChangeFromUs = true;
                window.location.hash = sourceRouter;
                //path 不能取obj_的path 因为每次获取的时候 each已经过期
                var defaultPath = wapRouter.routers.path;
                if(defaultPath.substr(-1, 1) == '/') {
                    defaultPath += router_;
                } else {
                    defaultPath += '/' + router_;
                }
                var path_ = isUndefined(wapRouter.routers[router_]) || isUndefined(wapRouter.routers[router_]['path']) ? defaultPath + '.js' : wapRouter.routers[router_]['path'];
                // console.log('router_:'+ router_);
                // console.log('path_:'+ path_);
                $.ajax({
                    url: urlAddRadom(path_),
                    success: function (string) {
                        var newObj = eval(string)(global__);
                        wapRouter.loadedOnes[router_] = newObj;
                        var needLogin =  (!isUndefined(wapRouter.routers[router_]) && !isUndefined(wapRouter.routers[router_]['needLogin'])) ? wapRouter.routers[router_]['needLogin'] : false;
                        if(needLogin) {
                            global__.wapPubFunc.checkLogin();
                        }
                        wapRouter._hashChangeFromUs = false;
                    },
                    error: function () {
                        console.log('path加载失败:'+ path_);
                    }
                });
                return {};
            }
        },
        //初始化所有接口
        'init': function (defaultRouter) {
            //获取页面hash
            var _gethash = function(){
                var hash = window.location.hash.replace(/^#/,"");
                return hash;
            };
            //检测url的hash 自动跳转
            var _checkUrlHash = function() {
                var router_ = _gethash();
                if(!router_)  {
                    wapRouter.goto(defaultRouter);
                    return;
                }
                // console.log(router_);
                wapRouter.goto(router_); //运行页面
            };
            //页面后退 跟踪url
            window.onhashchange = function() {
                if(wapRouter._hashChangeFromUs == true) return;
                _checkUrlHash();
            };
            _checkUrlHash();//初始化
        },
        //wap页面所有路由
        routers: {
            // 'example_login': {
            //     'path': '/assets/wap/js/login.js'
            // }
            //js内容
            // (function() {
            //     console.log('我要登录');
            // });
        },
    };

    $(document).ready(function () {
        global__.contentObj = new function () {
            this.allBody = $('#'+ global__.wapCfg.wapBodyId);
            this.header = this.allBody.find('#'+ global__.wapCfg.wapTopId);
            this.body = this.allBody.find('#'+ global__.wapCfg.wapMainBodyId);
            this.footer = this.allBody.find('#'+ global__.wapCfg.wapFooterId);
            this.appendHeader = function (newObj) {
                // console.log(contentObj.header);
                contentObj.header.html('').append(newObj);
            };
            this.appendBody = function (newObj) {
                // console.log(contentObj.header);
                contentObj.body.html('').append(newObj);
            };
            this.addBody = function (newObj) {
                // console.log(contentObj.header);
                contentObj.body.append(newObj);
            };
        };

        global__.wapRouter.init(global__.wapCfg.defaultRouter);
    });
})(this, jQuery);