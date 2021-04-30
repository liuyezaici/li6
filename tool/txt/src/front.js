define(['jquery', 'lrBox'], function ($, lrBox) {
    //格式化ajax post 带上随机数和默认返回json格式
    var rePost = function (url, postData, callBack) {
        if (!url) return;
        $.post(url, postData, callBack, 'json');
    };

    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i].trim();
            if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
        }
        return "";
    }

    var backObj = {
        isUndefined: function (variable) {
            return typeof variable == 'undefined' ? true : false;
        },
        md5: function(a) {
            //md5
            var k = function (a, c) {
                var h, g, k, m;
                k = a & 2147483648;
                m = c & 2147483648;
                h = a & 1073741824;
                g = c & 1073741824;
                a = (a & 1073741823) + (c & 1073741823);
                return h & g ? a ^ 2147483648 ^ k ^ m : h | g ? a & 1073741824 ? a ^ 3221225472 ^ k ^ m : a ^ 1073741824 ^ k ^ m : a ^ k ^ m
            }, l = function (a, c, h, g, l, m, b) {
                a = k(a, k(k(c & h | ~c & g, l), b));
                return k(a << m | a >>> 32 - m, c)
            }, n = function (a, c, h, g, l, m, b) {
                a = k(a, k(k(c & g | h & ~g, l), b));
                return k(a << m | a >>> 32 - m, c)
            }, p = function (a, c, h, g, l, m, b) {
                a = k(a, k(k(c ^ h ^ g, l), b));
                return k(a << m | a >>> 32 - m, c)
            }, q = function (a, c, h, g, l, m, b) {
                a = k(a, k(k(h ^ (c | ~g), l), b));
                return k(a << m | a >>> 32 - m, c)
            }, t = function (a) {
                var c = "", h, g;
                for (g = 0; 3 >= g; g++) h = a >>> 8 * g & 255, h = "0" + h.toString(16), c += h.substr(h.length - 2, 2);
                return c
            };
            var c, h, g, r, m, b, d, e, f;
            a = a.replace(/\x0d\x0a/g, "\n");
            c = "";
            for (h = 0; h < a.length; h++) g = a.charCodeAt(h), 128 > g ? c += String.fromCharCode(g) : (127 < g && 2048 > g ? c += String.fromCharCode(g >> 6 | 192) : (c += String.fromCharCode(g >> 12 | 224), c += String.fromCharCode(g >> 6 & 63 | 128)), c += String.fromCharCode(g & 63 | 128));
            h = c.length;
            a = h + 8;
            r = 16 * ((a - a % 64) / 64 + 1);
            a = Array(r - 1);
            for (b = 0; b < h;) g = (b - b % 4) / 4, m = b % 4 * 8, a[g] |= c.charCodeAt(b) << m, b++;
            g = (b - b % 4) / 4;
            a[g] |= 128 << b % 4 * 8;
            a[r - 2] = h << 3;
            a[r - 1] = h >>> 29;
            b = 1732584193;
            d = 4023233417;
            e = 2562383102;
            f = 271733878;
            for (c = 0; c < a.length; c += 16) h = b, g = d, r = e, m = f, b = l(b, d, e, f, a[c + 0], 7, 3614090360), f = l(f, b, d, e, a[c + 1], 12, 3905402710), e = l(e, f, b, d, a[c + 2], 17, 606105819), d = l(d, e, f, b, a[c + 3], 22, 3250441966), b = l(b, d, e, f, a[c + 4], 7, 4118548399), f = l(f, b, d, e, a[c + 5], 12, 1200080426), e = l(e, f, b, d, a[c + 6], 17, 2821735955), d = l(d, e, f, b, a[c + 7], 22, 4249261313), b = l(b, d, e, f, a[c + 8], 7, 1770035416), f = l(f, b, d, e, a[c + 9], 12, 2336552879), e = l(e, f, b, d, a[c + 10], 17, 4294925233), d = l(d, e, f, b, a[c + 11], 22, 2304563134), b = l(b, d, e, f, a[c + 12], 7, 1804603682), f = l(f, b, d, e, a[c + 13], 12, 4254626195), e = l(e, f, b, d, a[c + 14], 17, 2792965006), d = l(d, e, f, b, a[c + 15], 22, 1236535329), b = n(b, d, e, f, a[c + 1], 5, 4129170786), f = n(f, b, d, e, a[c + 6], 9, 3225465664), e = n(e, f, b, d, a[c + 11], 14, 643717713), d = n(d, e, f, b, a[c + 0], 20, 3921069994), b = n(b, d, e, f, a[c + 5], 5, 3593408605), f = n(f, b, d, e, a[c + 10], 9, 38016083), e = n(e, f, b, d, a[c + 15], 14, 3634488961), d = n(d, e, f, b, a[c + 4], 20, 3889429448), b = n(b, d, e, f, a[c + 9], 5, 568446438), f = n(f, b, d, e, a[c + 14], 9, 3275163606), e = n(e, f, b, d, a[c + 3], 14, 4107603335), d = n(d, e, f, b, a[c + 8], 20, 1163531501), b = n(b, d, e, f, a[c + 13], 5, 2850285829), f = n(f, b, d, e, a[c + 2], 9, 4243563512), e = n(e, f, b, d, a[c + 7], 14, 1735328473), d = n(d, e, f, b, a[c + 12], 20, 2368359562), b = p(b, d, e, f, a[c + 5], 4, 4294588738), f = p(f, b, d, e, a[c + 8], 11, 2272392833), e = p(e, f, b, d, a[c + 11], 16, 1839030562), d = p(d, e, f, b, a[c + 14], 23, 4259657740), b = p(b, d, e, f, a[c + 1], 4, 2763975236), f = p(f, b, d, e, a[c + 4], 11, 1272893353), e = p(e, f, b, d, a[c + 7], 16, 4139469664), d = p(d, e, f, b, a[c + 10], 23, 3200236656), b = p(b, d, e, f, a[c + 13], 4, 681279174), f = p(f, b, d, e, a[c + 0], 11, 3936430074), e = p(e, f, b, d, a[c + 3], 16, 3572445317), d = p(d, e, f, b, a[c + 6], 23, 76029189), b = p(b, d, e, f, a[c + 9], 4, 3654602809), f = p(f, b, d, e, a[c + 12], 11, 3873151461), e = p(e, f, b, d, a[c + 15], 16, 530742520), d = p(d, e, f, b, a[c + 2], 23, 3299628645), b = q(b, d, e, f, a[c + 0], 6, 4096336452), f = q(f, b, d, e, a[c + 7], 10, 1126891415), e = q(e, f, b, d, a[c + 14], 15, 2878612391), d = q(d, e, f, b, a[c + 5], 21, 4237533241), b = q(b, d, e, f, a[c + 12], 6, 1700485571), f = q(f, b, d, e, a[c + 3], 10, 2399980690), e = q(e, f, b, d, a[c + 10], 15, 4293915773), d = q(d, e, f, b, a[c + 1], 21, 2240044497), b = q(b, d, e, f, a[c + 8], 6, 1873313359), f = q(f, b, d, e, a[c + 15], 10, 4264355552), e = q(e, f, b, d, a[c + 6], 15, 2734768916), d = q(d, e, f, b, a[c + 13], 21, 1309151649), b = q(b, d, e, f, a[c + 4], 6, 4149444226), f = q(f, b, d, e, a[c + 11], 10, 3174756917), e = q(e, f, b, d, a[c + 2], 15, 718787259), d = q(d, e, f, b, a[c + 9], 21, 3951481745), b = k(b, h), d = k(d, g), e = k(e, r), f = k(f, m);
            return (t(b) + t(d) + t(e) + t(f)).toLowerCase();
        },
        //获取post.data的成功标识
        getCallData: function (data_) {
            var successKey = backObj.getOptVal(data_, ['successkey', 'success_key', 'successKey'], null);
            var successFunc = backObj.getOptVal(data_, ['successfunc', 'success_func', 'successFunc'], null); //成功回调
            var successVal = backObj.getOptVal(data_, ['successval', 'success_val', 'success_value', 'successVal', 'successValue'], null); //成功的判断值
            var errFunc = backObj.getOptVal(data_, ['failfunc', 'fail_func', 'failFunc', 'errfunc', 'err_func', 'errFunc', 'errorfunc', 'error_func', 'errorFunc'], null);
            if (backObj.isNumber(successVal)) successVal += '';
            return {
                'successKey': successKey,
                'successValue': successVal,
                'successFunc': successFunc,
                'errorFunc': errFunc
            };
        },

        isObj: function (value) {
            return typeof value == 'object';
        },
        isNumber: function (value) {
            return /^[\+\-0-9.]+$/.test(value);
        },
        //字符格式
        isString: function (val) {
            return typeof val == 'string' || typeof val == 'number';
        },
        //获取属性值
        getOptVal: function (obj_, keyname, defaultVal) {
            if (!obj_) return defaultVal;
            if ($.isArray(keyname)) {
                var findKey = false;
                var findVal = false;
                $.each(keyname, function (index_, tmpName) {
                    if (!backObj.isUndefined(obj_[tmpName])) {
                        //console.log('find:'+ tmpName);
                        //console.log(obj_[tmpName]);
                        //对象要克隆 否则会反作用原对象
                        findKey = true;
                        findVal = backObj.isObj(obj_[tmpName]) ? (obj_[tmpName]) : obj_[tmpName];
                        return false;
                    }
                })
                if (findKey) {
                    return findVal;
                } else {
                    return defaultVal;
                }
            } else {
                if (!backObj.isUndefined(obj_[keyname])) {
                    //对象要克隆 否则会反作用原对象
                    return backObj.isObj(obj_[keyname]) ? (obj_[keyname]) : obj_[keyname];
                }
            }
            return defaultVal;
        },
        //检索数组 文本类型：数字和字符都支持 不区分1和'1'
        strInArray: function (str, array_) {
            var exist_ = -1;
            $.each(array_, function (n, item_) {
                if (item_ == str) {
                    exist_ = n;
                    return false; //break
                }
            });
            return exist_;
        },
        postAndDone: function (options, obj) {
            //[属性：success_value,post_url,post_data,msg,msg_hide,func]
            // success_value //成功时的回调值，'0308'/ ['0043', '0113']
            // post_url //post 接口
            // load_bg //load是否加背景
            // post_data //post 数据包
            // success_func //成功时执行的动作
            //err_func://失败时执行的动作
            options = options || {};
            obj = obj || {};
            var callKeys = backObj.getCallData(options);
            var successKey = callKeys['successKey'];
            var successVal = callKeys['successValue'];
            var successFunc = callKeys['successFunc'];
            if (!$.isArray(successVal)) {
                if (!successVal) successVal = '1';
                if (backObj.isString(successVal)) {
                    successVal = successVal.toString().split(',');
                } else {
                    successVal = successVal.toString().split(',');
                }
            }
            var postUrl = options['post_url'] || options['url'] || '';
            var postData = backObj.getOptVal(options, ['post_data', 'postData'], null);
            var errFunc = backObj.getOptVal(options, ['err_func', 'errFunc', 'error_func', 'errorFunc'], null);//失败回调
            return rePost(postUrl, postData, function (data) {
                if (!data) {
                    console.log('post result: no data');
                    return;
                }
                if (successVal && successKey && (backObj.isUndefined(data[successKey]) || backObj.strInArray(data[successKey], successVal) == -1)) {
                    if (errFunc) errFunc(data, obj);
                } else {
                    //可能这里会执行关闭所有（最新）窗口，所以要提前执行，防止将默认的提示语误关。
                    if (successFunc) {
                        if (backObj.isString(successFunc)) {
                            eval(successFunc);
                        } else {
                            successFunc(data, obj);
                        }
                    }
                }
            });
        },
        //传统表单的自定义打包提交方法
        formSubmitEven: function (form, opt) {
            var beforeFunc = backObj.getOptVal(opt, ['before'], null);
            var files = form.find("input[type='file']");
            var fileData = {};
            files.on('change', function () {
                var fileObj = $(this);
                var inputName = $(this).attr('name');
                var fileNode = fileObj[0].files[0];
                var reader = new FileReader();
                reader.onload = function (e) {
                    var base64Data = e.target.result;
                    fileData[inputName] = base64Data;
                };
                reader.readAsDataURL(fileNode);
            });
            form.on('submit', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var data_ = form.serializeArray();
                var pData = {};
                pData = $.extend(pData, fileData);
                data_.map(function (v, n) {
                    if (!backObj.isUndefined(pData[v.name])) {
                        if ($.isArray(pData[v.name])) {
                            pData[v.name].push(v.value);
                        } else {
                            pData[v.name] = [pData[v.name], v.value];
                        }
                    } else {
                        pData[v.name] = v.value;
                    }
                });
                if (!backObj.isUndefined(opt['postData'])) {
                    opt['postData'].map(function (v, k) {
                        pData[k] = v;
                    });
                }
                if (beforeFunc) {
                    var status = beforeFunc(pData);
                    if (status === false) {
                        return;
                    }
                    if (status) {
                        pData = status;
                    }

                }
                var newOpt = {
                    'postData': pData
                };
                var onSubmit = !backObj.isUndefined(opt['submit']) ? opt['submit'] : false;
                if (onSubmit) onSubmit(form);
                newOpt = $.extend({}, newOpt, opt);
                backObj.postAndDone(newOpt);
            });
        },

        //清空表单内容
        cleanForm: function (form) {
            return form.map(function () {
                // Can add propHook for "elements" to filter or add form elements
                var elements = jQuery.prop(this, "elements");
                return elements ? jQuery.makeArray(elements) : this;
            })
                .map(function (i, elem) {
                    if (elem.name) {
                        if (elem.type == 'submit' || elem.type == 'radio' || elem.type == 'checkbox') {

                        } else {
                            jQuery(this).val('');
                        }
                    }

                }).get();
        },

        //滚动条监听事件
        xRoll: function (el, addY, reachCallFunc, leaveCallFunc) {
            var this_ = {
                init: function (_el) {
                    this_.listenScroll(_el);
                    $(window).on("scroll", function () {
                        this_.listenScroll(_el)
                    })
                },
                listenScroll: function (_el) {
                    $(_el).each(function () {
                        var _self = $(this);
                        var scroll_top = $(window).scrollTop();
                        var isWindowHeight = $(window).height();
                        if (scroll_top + isWindowHeight + addY > $(this).offset().top) {
                            if (_self.attr('data-state') == 1) {
                                return;
                            }
                            // console.log('scroll_top:', scroll_top, 'isWindowHeight', isWindowHeight,  $(this), $(this).offset().top);
                            reachCallFunc(_self);
                            _self.attr("data-state", 1);
                        } else {
                            if (_self.attr('data-state') == 1) {
                                if (leaveCallFunc) {
                                    leaveCallFunc(_self);
                                }
                                // console.log(scroll_top + isWindowHeight, $(this).offset().top);
                                _self.attr('data-state', 0);
                            }
                        }
                    })
                }
            };
            this_.init(el);
        },


        deleteAllCookies: function () {
            var _oDomains = [".li6.cc"],
                _oCookies = document.cookie.split("; "),
                _nCookiesLength = _oCookies.length,
                _oArr,
                _sCookie,
                _sUin = "0", _oMailDomain = [],
                _oQQDomain = [];
            if (document.cookie == "") {
                lrBox.msgTsf("成功删除 Cookie!");
                return;
            }

            function handleItem(_asDomain, _asCookie) {
                var _sDetail;
                if (document.cookie != "") {
                    _sDetail = _asCookie.substr(0, _asCookie.indexOf("="));
                    _oArr.push(_asDomain);
                    document.cookie = _oArr.join('');
                    if (document.cookie.split(";").length < _nCookiesLength || document.cookie == "") {
                        _asDomain == _oDomains[0] ? _oMailDomain.push(_sDetail) : _oQQDomain.push(_sDetail);
                        _nCookiesLength--;
                        _oArr.pop();
                        return true;
                    }
                    _oArr.pop();
                }
            }

            for (var i = 0, _nLen = _oCookies.length; i < _nLen; i++) {
                (_oCookies[i].indexOf("=") == -1) && (_oCookies[i] += "=");
                _oArr = [_oCookies[i], ";expires=Thu, 01 Jan 1970 00:00:00 GMT", ";path=/", ";domain="];
                if (_oCookies[i].indexOf("qm_username") != -1) {
                    _sUin = _oCookies[i].substr(_oCookies[i].indexOf("=") + 1, _oCookies[i].length);
                }
                handleItem(_oDomains[0], _oCookies[i]) || handleItem(_oDomains[1], _oCookies[i]);
            }
            lrBox.msgTsf("成功删除 Cookie!");
        }
    };

    //暴露给全局引用
    backObj.$ = $;
    backObj.lrBox = lrBox;
    backObj.isPc = function () {
        var userAgentInfo = navigator.userAgent;
        var Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");
        var flag = true;
        for (var v = 0; v < Agents.length; v++) {
            if (userAgentInfo.indexOf(Agents[v]) > 0) {
                flag = false;
                break;
            }
        }
        return flag;
    };
    return backObj;
});
