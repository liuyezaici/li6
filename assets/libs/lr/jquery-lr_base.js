//按钮事件
define(['require','jquery', 'lrBox'], function (require, $, lrBox) {
    var this_post = function (opt) {
        var frontFunc = require('frontFunc');
        frontFunc.curl.callPost(opt);
    };
    var backObj = {
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
        dataURLtoBlob: function(dataurl) {
            var lrBase = require('lrBase');
            var arr = dataurl.split(',');
            var mimeArray = arr[0].match(/:(.*?);/);
            if(!mimeArray || lrBase.isUndefined(mimeArray[1])) {
                console.log(mimeArray);
                console.log('no find 1');
                return;
            }
            var mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new Blob([u8arr], {type: mime});
        },
        rePost: function (url, postData, callBack) {
            return $.post(url, postData, callBack, 'json');
        },
        //单纯的ajax post请求
        justAjax: function(url, successFunc, opt) {
            opt = opt || {};
            var cfg = {
                url: url,
                type: "GET",
                contentType: false,
                dataType: 'json',
                processData: false,
                success: function (data) {
                    successFunc(data);
                }
            };
            cfg = $.extend({}, cfg, opt);
            $.ajax(cfg);
        },
        //js trim去左右字符(空格) 去4次左右两边符号
        trim: function (str, node) {
            node = node || " ";
            var len = node.length;
            if (str.substr(0, len) == node) str = str.substr(len);
            if (str.substr(0, len) == node) str = str.substr(len);
            if (str.substr(0, len) == node) str = str.substr(len);
            if (str.substr(0, len) == node) str = str.substr(len);
            if (str.substr(str.length - len, len) == node) str = str.substr(0, str.length - len);
            if (str.substr(str.length - len, len) == node) str = str.substr(0, str.length - len);
            if (str.substr(str.length - len, len) == node) str = str.substr(0, str.length - len);
            if (str.substr(str.length - len, len) == node) str = str.substr(0, str.length - len);
            return str;
        },
        //缩写原生的判断对象是否存在
        isUndefined: function (variable) {return typeof variable == 'undefined' ? true : false;},
        //重复字符串
        repeat:function (html, n) {
            return (new Array(n + 1)).join(html);
        },
        //obj转json字符串
        objToJson:function (obj) {
            try{
                var seen = [];
                var json = JSON.stringify(obj, function(key, val) {
                    if (typeof val == "object") {
                        if (seen.indexOf(val) >= 0) return;
                        seen.push(val)
                    }
                    return val;
                });
                return json;
            }catch(e){
                return e;
            }
        },
        //四舍五入 保留2位小数
        formatFloat:function (src, pos) {
            pos = backObj.isUndefined(pos) ? 2 : pos;
            src = Math.round(src*Math.pow(10, pos))/Math.pow(10, pos);//先四舍五入
            //补齐后面的0
            src=Math.round(parseFloat(src)*100)/100;
            var xsd= src.toString().split(".");
            if(xsd.length==1){
                src=src.toString();
                return src;
            }
            if(xsd.length>1){
                if(xsd[1].length<2){
                    src=src.toString();
                }
                return src;
            }
        },
        //判断手机格式是否正确
        checkPhoneFormat: function (phone) {
            phone = phone || '';
            var patrn = /(^0{0,1}1[3|4|5|6|7|8|9][0-9]{9}$)/;
            return patrn.exec(phone);
        },
        //判断邮箱格式是否正确
        checkEmailFormat: function (email) {
            email = email || '';
            var email_zhengzhe = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return email_zhengzhe.exec(email);
        },
        //判断对象为空
        objIsNull: function (obj) {
            return JSON.stringify(obj) == '{}';
        },

        //模拟php isset
        isset: function(obj) {
            return typeof(obj) !="undefined";
        },
        //检索数组 文本类型：数字和字符都支持 不区分1和'1'
        strInArray:function (str, array_) {
            var exist_ = -1;
            $.each(array_, function (n, item_) {
                if(item_ == str) {
                    exist_ = n;
                    return false; //break
                }
            });
            return exist_;
        },
        //字符格式
        isString: function (val) {
            return typeof val == 'string' || typeof val == 'number';
        },
//获取url参数
        getUrlParam: function (name, explode, url) {
            var param = window.location.href;
            if (url) {
                if (explode) {
                    param = url.substr(url.indexOf(explode) + 1);
                } else {
                    param = url.substr(url.indexOf('?') + 1);
                }
            } else {
                if (explode) {
                    param = window.location.href.substr(window.location.href.indexOf(explode) + 1);
                }
            }
            var reg = new RegExp("(^|&\?)" + name + "=([^&]*)(&|$)");
            var r = param.match(reg);
            if (r != null) return unescape(r[2]);
            return '';
        },
// 给网址加随机参数
        urlAddRadom:function (url, key) {
            key = key || 'v';
            url = url.toString();
            var joinStr = '?';
            if(url.indexOf('?') !=-1 ) {
                joinStr = '&';
            } else {
                joinStr = '?';
            }
            return url + joinStr + key + '='+ Math.random();
        },
        //粘贴图片事件
        bindPasteImageEven: function (dom, func) {
            func = func || null;
            dom.addEventListener('paste', function (e) {
                // console.log('pastingeeeeeeeeee');
                var cbd = e.clipboardData;
                var ua = window.navigator.userAgent;
                // 如果是 Safari 直接 return
                if (!(e.clipboardData && e.clipboardData.items)) {
                    // console.log('!(e.clipboardData && e.clipboardData.items)');
                    return;
                }
                // Mac平台下Chrome49版本以下 复制Finder中的文件的Bug Hack掉
                if (cbd.items && cbd.items.length === 2 && cbd.items[0].kind === "string" && cbd.items[1].kind === "file" &&
                    cbd.types && cbd.types.length === 2 && cbd.types[0] === "text/plain" && cbd.types[1] === "Files" &&
                    ua.match(/Macintosh/i) && Number(ua.match(/Chrome\/(\d{2})/i)[1]) < 49) {
                    // console.log('Mac平台下Chrome49版本以下 复制Finder中的文件的Bug Hack掉');
                    return;
                }
                var blob;
                for (var i = 0; i < cbd.items.length; i++) {
                    var item = cbd.items[i];
                    if (item.kind == "file") {
                        e.preventDefault(); //火狐62.0.3下 自带插入图片功能 要return掉
                        blob = item.getAsFile();
                        if (blob.size === 0) {
                            return '';
                        }
                        // blob 就是从剪切板获得的文件 可以进行上传或其他操作
                        /*-----------------------不与后台进行交互 直接预览start-----------------------*/
                        var reader = new FileReader();
                        var imgs = new Image();
                        imgs.file = blob;
                        reader.onload = (function (aImg) {
                            return function (e) {
                                aImg.src = e.target.result;
                            };
                        })(imgs);
                        reader.readAsDataURL(blob);
                    }
                }
                // console.log('pasting!!!!!!!!!!!!2');
                // console.log(imgs);
                if (func) func(imgs);
            }, false);
        },
        //aes加密解密
        aesObj: new (function() {
            /*globals window, global, require*/
            /**
             * CryptoJS core components.
             */
            var CryptoJS = CryptoJS || (function (Math, undefined) {

                var crypto;

                // Native crypto from window (Browser)
                if (typeof window !== 'undefined' && window.crypto) {
                    crypto = window.crypto;
                }

                // Native (experimental IE 11) crypto from window (Browser)
                if (!crypto && typeof window !== 'undefined' && window.msCrypto) {
                    crypto = window.msCrypto;
                }

                // Native crypto from global (NodeJS)
                if (!crypto && typeof global !== 'undefined' && global.crypto) {
                    crypto = global.crypto;
                }

                // Native crypto import via require (NodeJS)
                if (!crypto && typeof require === 'function') {
                    try {
                        crypto = require('crypto');
                    } catch (err) {}
                }

                /*
	     * Cryptographically secure pseudorandom number generator
	     *
	     * As Math.random() is cryptographically not safe to use
	     */
                var cryptoSecureRandomInt = function () {
                    if (crypto) {
                        // Use getRandomValues method (Browser)
                        if (typeof crypto.getRandomValues === 'function') {
                            try {
                                return crypto.getRandomValues(new Uint32Array(1))[0];
                            } catch (err) {}
                        }

                        // Use randomBytes method (NodeJS)
                        if (typeof crypto.randomBytes === 'function') {
                            try {
                                return crypto.randomBytes(4).readInt32LE();
                            } catch (err) {}
                        }
                    }

                    throw new Error('Native crypto module could not be used to get secure random number.');
                };

                /*
	     * Local polyfill of Object.create

	     */
                var create = Object.create || (function () {
                    function F() {}

                    return function (obj) {
                        var subtype;

                        F.prototype = obj;

                        subtype = new F();

                        F.prototype = null;

                        return subtype;
                    };
                }())

                /**
                 * CryptoJS namespace.
                 */
                var C = {};

                /**
                 * Library namespace.
                 */
                var C_lib = C.lib = {};

                /**
                 * Base object for prototypal inheritance.
                 */
                var Base = C_lib.Base = (function () {


                    return {
                        /**
                         * Creates a new object that inherits from this object.
                         *
                         * @param {Object} overrides Properties to copy into the new object.
                         *
                         * @return {Object} The new object.
                         *
                         * @static
                         *
                         * @example
                         *
                         *     var MyType = CryptoJS.lib.Base.extend({
                         *         field: 'value',
                         *
                         *         method: function () {
                         *         }
                         *     });
                         */
                        extend: function (overrides) {
                            // Spawn
                            var subtype = create(this);

                            // Augment
                            if (overrides) {
                                subtype.mixIn(overrides);
                            }

                            // Create default initializer
                            if (!subtype.hasOwnProperty('init') || this.init === subtype.init) {
                                subtype.init = function () {
                                    subtype.$super.init.apply(this, arguments);
                                };
                            }

                            // Initializer's prototype is the subtype object
                            subtype.init.prototype = subtype;

                            // Reference supertype
                            subtype.$super = this;

                            return subtype;
                        },

                        /**
                         * Extends this object and runs the init method.
                         * Arguments to create() will be passed to init().
                         *
                         * @return {Object} The new object.
                         *
                         * @static
                         *
                         * @example
                         *
                         *     var instance = MyType.create();
                         */
                        create: function () {
                            var instance = this.extend();
                            instance.init.apply(instance, arguments);

                            return instance;
                        },

                        /**
                         * Initializes a newly created object.
                         * Override this method to add some logic when your objects are created.
                         *
                         * @example
                         *
                         *     var MyType = CryptoJS.lib.Base.extend({
                         *         init: function () {
                         *             // ...
                         *         }
                         *     });
                         */
                        init: function () {
                        },

                        /**
                         * Copies properties into this object.
                         *
                         * @param {Object} properties The properties to mix in.
                         *
                         * @example
                         *
                         *     MyType.mixIn({
                         *         field: 'value'
                         *     });
                         */
                        mixIn: function (properties) {
                            for (var propertyName in properties) {
                                if (properties.hasOwnProperty(propertyName)) {
                                    this[propertyName] = properties[propertyName];
                                }
                            }

                            // IE won't copy toString using the loop above
                            if (properties.hasOwnProperty('toString')) {
                                this.toString = properties.toString;
                            }
                        },

                        /**
                         * Creates a copy of this object.
                         *
                         * @return {Object} The clone.
                         *
                         * @example
                         *
                         *     var clone = instance.clone();
                         */
                        clone: function () {
                            return this.init.prototype.extend(this);
                        }
                    };
                }());

                /**
                 * An array of 32-bit words.
                 *
                 * @property {Array} words The array of 32-bit words.
                 * @property {number} sigBytes The number of significant bytes in this word array.
                 */
                var WordArray = C_lib.WordArray = Base.extend({
                    /**
                     * Initializes a newly created word array.
                     *
                     * @param {Array} words (Optional) An array of 32-bit words.
                     * @param {number} sigBytes (Optional) The number of significant bytes in the words.
                     *
                     * @example
                     *
                     *     var wordArray = CryptoJS.lib.WordArray.create();
                     *     var wordArray = CryptoJS.lib.WordArray.create([0x00010203, 0x04050607]);
                     *     var wordArray = CryptoJS.lib.WordArray.create([0x00010203, 0x04050607], 6);
                     */
                    init: function (words, sigBytes) {
                        words = this.words = words || [];

                        if (sigBytes != undefined) {
                            this.sigBytes = sigBytes;
                        } else {
                            this.sigBytes = words.length * 4;
                        }
                    },

                    /**
                     * Converts this word array to a string.
                     *
                     * @param {Encoder} encoder (Optional) The encoding strategy to use. Default: CryptoJS.enc.Hex
                     *
                     * @return {string} The stringified word array.
                     *
                     * @example
                     *
                     *     var string = wordArray + '';
                     *     var string = wordArray.toString();
                     *     var string = wordArray.toString(CryptoJS.enc.Utf8);
                     */
                    toString: function (encoder) {
                        return (encoder || Hex).stringify(this);
                    },

                    /**
                     * Concatenates a word array to this word array.
                     *
                     * @param {WordArray} wordArray The word array to append.
                     *
                     * @return {WordArray} This word array.
                     *
                     * @example
                     *
                     *     wordArray1.concat(wordArray2);
                     */
                    concat: function (wordArray) {
                        // Shortcuts
                        var thisWords = this.words;
                        var thatWords = wordArray.words;
                        var thisSigBytes = this.sigBytes;
                        var thatSigBytes = wordArray.sigBytes;

                        // Clamp excess bits
                        this.clamp();

                        // Concat
                        if (thisSigBytes % 4) {
                            // Copy one byte at a time
                            for (var i = 0; i < thatSigBytes; i++) {
                                var thatByte = (thatWords[i >>> 2] >>> (24 - (i % 4) * 8)) & 0xff;
                                thisWords[(thisSigBytes + i) >>> 2] |= thatByte << (24 - ((thisSigBytes + i) % 4) * 8);
                            }
                        } else {
                            // Copy one word at a time
                            for (var i = 0; i < thatSigBytes; i += 4) {
                                thisWords[(thisSigBytes + i) >>> 2] = thatWords[i >>> 2];
                            }
                        }
                        this.sigBytes += thatSigBytes;

                        // Chainable
                        return this;
                    },

                    /**
                     * Removes insignificant bits.
                     *
                     * @example
                     *
                     *     wordArray.clamp();
                     */
                    clamp: function () {
                        // Shortcuts
                        var words = this.words;
                        var sigBytes = this.sigBytes;

                        // Clamp
                        words[sigBytes >>> 2] &= 0xffffffff << (32 - (sigBytes % 4) * 8);
                        words.length = Math.ceil(sigBytes / 4);
                    },

                    /**
                     * Creates a copy of this word array.
                     *
                     * @return {WordArray} The clone.
                     *
                     * @example
                     *
                     *     var clone = wordArray.clone();
                     */
                    clone: function () {
                        var clone = Base.clone.call(this);
                        clone.words = this.words.slice(0);

                        return clone;
                    },

                    /**
                     * Creates a word array filled with random bytes.
                     *
                     * @param {number} nBytes The number of random bytes to generate.
                     *
                     * @return {WordArray} The random word array.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var wordArray = CryptoJS.lib.WordArray.random(16);
                     */
                    random: function (nBytes) {
                        var words = [];

                        for (var i = 0; i < nBytes; i += 4) {
                            words.push(cryptoSecureRandomInt());
                        }

                        return new WordArray.init(words, nBytes);
                    }
                });

                /**
                 * Encoder namespace.
                 */
                var C_enc = C.enc = {};

                /**
                 * Hex encoding strategy.
                 */
                var Hex = C_enc.Hex = {
                    /**
                     * Converts a word array to a hex string.
                     *
                     * @param {WordArray} wordArray The word array.
                     *
                     * @return {string} The hex string.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var hexString = CryptoJS.enc.Hex.stringify(wordArray);
                     */
                    stringify: function (wordArray) {
                        // Shortcuts
                        var words = wordArray.words;
                        var sigBytes = wordArray.sigBytes;

                        // Convert
                        var hexChars = [];
                        for (var i = 0; i < sigBytes; i++) {
                            var bite = (words[i >>> 2] >>> (24 - (i % 4) * 8)) & 0xff;
                            hexChars.push((bite >>> 4).toString(16));
                            hexChars.push((bite & 0x0f).toString(16));
                        }

                        return hexChars.join('');
                    },

                    /**
                     * Converts a hex string to a word array.
                     *
                     * @param {string} hexStr The hex string.
                     *
                     * @return {WordArray} The word array.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var wordArray = CryptoJS.enc.Hex.parse(hexString);
                     */
                    parse: function (hexStr) {
                        // Shortcut
                        var hexStrLength = hexStr.length;

                        // Convert
                        var words = [];
                        for (var i = 0; i < hexStrLength; i += 2) {
                            words[i >>> 3] |= parseInt(hexStr.substr(i, 2), 16) << (24 - (i % 8) * 4);
                        }

                        return new WordArray.init(words, hexStrLength / 2);
                    }
                };

                /**
                 * Latin1 encoding strategy.
                 */
                var Latin1 = C_enc.Latin1 = {
                    /**
                     * Converts a word array to a Latin1 string.
                     *
                     * @param {WordArray} wordArray The word array.
                     *
                     * @return {string} The Latin1 string.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var latin1String = CryptoJS.enc.Latin1.stringify(wordArray);
                     */
                    stringify: function (wordArray) {
                        // Shortcuts
                        var words = wordArray.words;
                        var sigBytes = wordArray.sigBytes;

                        // Convert
                        var latin1Chars = [];
                        for (var i = 0; i < sigBytes; i++) {
                            var bite = (words[i >>> 2] >>> (24 - (i % 4) * 8)) & 0xff;
                            latin1Chars.push(String.fromCharCode(bite));
                        }

                        return latin1Chars.join('');
                    },

                    /**
                     * Converts a Latin1 string to a word array.
                     *
                     * @param {string} latin1Str The Latin1 string.
                     *
                     * @return {WordArray} The word array.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var wordArray = CryptoJS.enc.Latin1.parse(latin1String);
                     */
                    parse: function (latin1Str) {
                        // Shortcut
                        var latin1StrLength = latin1Str.length;

                        // Convert
                        var words = [];
                        for (var i = 0; i < latin1StrLength; i++) {
                            words[i >>> 2] |= (latin1Str.charCodeAt(i) & 0xff) << (24 - (i % 4) * 8);
                        }

                        return new WordArray.init(words, latin1StrLength);
                    }
                };

                /**
                 * UTF-8 encoding strategy.
                 */
                var Utf8 = C_enc.Utf8 = {
                    /**
                     * Converts a word array to a UTF-8 string.
                     *
                     * @param {WordArray} wordArray The word array.
                     *
                     * @return {string} The UTF-8 string.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var utf8String = CryptoJS.enc.Utf8.stringify(wordArray);
                     */
                    stringify: function (wordArray) {
                        try {
                            return decodeURIComponent(escape(Latin1.stringify(wordArray)));
                        } catch (e) {
                            throw new Error('Malformed UTF-8 data');
                        }
                    },

                    /**
                     * Converts a UTF-8 string to a word array.
                     *
                     * @param {string} utf8Str The UTF-8 string.
                     *
                     * @return {WordArray} The word array.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var wordArray = CryptoJS.enc.Utf8.parse(utf8String);
                     */
                    parse: function (utf8Str) {
                        return Latin1.parse(unescape(encodeURIComponent(utf8Str)));
                    }
                };

                /**
                 * Abstract buffered block algorithm template.
                 *
                 * The property blockSize must be implemented in a concrete subtype.
                 *
                 * @property {number} _minBufferSize The number of blocks that should be kept unprocessed in the buffer. Default: 0
                 */
                var BufferedBlockAlgorithm = C_lib.BufferedBlockAlgorithm = Base.extend({
                    /**
                     * Resets this block algorithm's data buffer to its initial state.
                     *
                     * @example
                     *
                     *     bufferedBlockAlgorithm.reset();
                     */
                    reset: function () {
                        // Initial values
                        this._data = new WordArray.init();
                        this._nDataBytes = 0;
                    },

                    /**
                     * Adds new data to this block algorithm's buffer.
                     *
                     * @param {WordArray|string} data The data to append. Strings are converted to a WordArray using UTF-8.
                     *
                     * @example
                     *
                     *     bufferedBlockAlgorithm._append('data');
                     *     bufferedBlockAlgorithm._append(wordArray);
                     */
                    _append: function (data) {
                        // Convert string to WordArray, else assume WordArray already
                        if (typeof data == 'string') {
                            data = Utf8.parse(data);
                        }

                        // Append
                        this._data.concat(data);
                        this._nDataBytes += data.sigBytes;
                    },

                    /**
                     * Processes available data blocks.
                     *
                     * This method invokes _doProcessBlock(offset), which must be implemented by a concrete subtype.
                     *
                     * @param {boolean} doFlush Whether all blocks and partial blocks should be processed.
                     *
                     * @return {WordArray} The processed data.
                     *
                     * @example
                     *
                     *     var processedData = bufferedBlockAlgorithm._process();
                     *     var processedData = bufferedBlockAlgorithm._process(!!'flush');
                     */
                    _process: function (doFlush) {
                        var processedWords;

                        // Shortcuts
                        var data = this._data;
                        var dataWords = data.words;
                        var dataSigBytes = data.sigBytes;
                        var blockSize = this.blockSize;
                        var blockSizeBytes = blockSize * 4;

                        // Count blocks ready
                        var nBlocksReady = dataSigBytes / blockSizeBytes;
                        if (doFlush) {
                            // Round up to include partial blocks
                            nBlocksReady = Math.ceil(nBlocksReady);
                        } else {
                            // Round down to include only full blocks,
                            // less the number of blocks that must remain in the buffer
                            nBlocksReady = Math.max((nBlocksReady | 0) - this._minBufferSize, 0);
                        }

                        // Count words ready
                        var nWordsReady = nBlocksReady * blockSize;

                        // Count bytes ready
                        var nBytesReady = Math.min(nWordsReady * 4, dataSigBytes);

                        // Process blocks
                        if (nWordsReady) {
                            for (var offset = 0; offset < nWordsReady; offset += blockSize) {
                                // Perform concrete-algorithm logic
                                this._doProcessBlock(dataWords, offset);
                            }

                            // Remove processed words
                            processedWords = dataWords.splice(0, nWordsReady);
                            data.sigBytes -= nBytesReady;
                        }

                        // Return processed words
                        return new WordArray.init(processedWords, nBytesReady);
                    },

                    /**
                     * Creates a copy of this object.
                     *
                     * @return {Object} The clone.
                     *
                     * @example
                     *
                     *     var clone = bufferedBlockAlgorithm.clone();
                     */
                    clone: function () {
                        var clone = Base.clone.call(this);
                        clone._data = this._data.clone();

                        return clone;
                    },

                    _minBufferSize: 0
                });

                /**
                 * Abstract hasher template.
                 *
                 * @property {number} blockSize The number of 32-bit words this hasher operates on. Default: 16 (512 bits)
                 */
                var Hasher = C_lib.Hasher = BufferedBlockAlgorithm.extend({
                    /**
                     * Configuration options.
                     */
                    cfg: Base.extend(),

                    /**
                     * Initializes a newly created hasher.
                     *
                     * @param {Object} cfg (Optional) The configuration options to use for this hash computation.
                     *
                     * @example
                     *
                     *     var hasher = CryptoJS.algo.SHA256.create();
                     */
                    init: function (cfg) {
                        // Apply config defaults
                        this.cfg = this.cfg.extend(cfg);

                        // Set initial values
                        this.reset();
                    },

                    /**
                     * Resets this hasher to its initial state.
                     *
                     * @example
                     *
                     *     hasher.reset();
                     */
                    reset: function () {
                        // Reset data buffer
                        BufferedBlockAlgorithm.reset.call(this);

                        // Perform concrete-hasher logic
                        this._doReset();
                    },

                    /**
                     * Updates this hasher with a message.
                     *
                     * @param {WordArray|string} messageUpdate The message to append.
                     *
                     * @return {Hasher} This hasher.
                     *
                     * @example
                     *
                     *     hasher.update('message');
                     *     hasher.update(wordArray);
                     */
                    update: function (messageUpdate) {
                        // Append
                        this._append(messageUpdate);

                        // Update the hash
                        this._process();

                        // Chainable
                        return this;
                    },

                    /**
                     * Finalizes the hash computation.
                     * Note that the finalize operation is effectively a destructive, read-once operation.
                     *
                     * @param {WordArray|string} messageUpdate (Optional) A final message update.
                     *
                     * @return {WordArray} The hash.
                     *
                     * @example
                     *
                     *     var hash = hasher.finalize();
                     *     var hash = hasher.finalize('message');
                     *     var hash = hasher.finalize(wordArray);
                     */
                    finalize: function (messageUpdate) {
                        // Final message update
                        if (messageUpdate) {
                            this._append(messageUpdate);
                        }

                        // Perform concrete-hasher logic
                        var hash = this._doFinalize();

                        return hash;
                    },

                    blockSize: 512/32,

                    /**
                     * Creates a shortcut function to a hasher's object interface.
                     *
                     * @param {Hasher} hasher The hasher to create a helper for.
                     *
                     * @return {Function} The shortcut function.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var SHA256 = CryptoJS.lib.Hasher._createHelper(CryptoJS.algo.SHA256);
                     */
                    _createHelper: function (hasher) {
                        return function (message, cfg) {
                            return new hasher.init(cfg).finalize(message);
                        };
                    },

                    /**
                     * Creates a shortcut function to the HMAC's object interface.
                     *
                     * @param {Hasher} hasher The hasher to use in this HMAC helper.
                     *
                     * @return {Function} The shortcut function.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var HmacSHA256 = CryptoJS.lib.Hasher._createHmacHelper(CryptoJS.algo.SHA256);
                     */
                    _createHmacHelper: function (hasher) {
                        return function (message, key) {
                            return new C_algo.HMAC.init(hasher, key).finalize(message);
                        };
                    }
                });

                /**
                 * Algorithm namespace.
                 */
                var C_algo = C.algo = {};

                return C;
            }(Math));

            (function () {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var WordArray = C_lib.WordArray;
                var C_enc = C.enc;

                /**
                 * Base64 encoding strategy.
                 */
                var Base64 = C_enc.Base64 = {
                    /**
                     * Converts a word array to a Base64 string.
                     *
                     * @param {WordArray} wordArray The word array.
                     *
                     * @return {string} The Base64 string.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var base64String = CryptoJS.enc.Base64.stringify(wordArray);
                     */
                    stringify: function (wordArray) {
                        // Shortcuts
                        var words = wordArray.words;
                        var sigBytes = wordArray.sigBytes;
                        var map = this._map;

                        // Clamp excess bits
                        wordArray.clamp();

                        // Convert
                        var base64Chars = [];
                        for (var i = 0; i < sigBytes; i += 3) {
                            var byte1 = (words[i >>> 2]       >>> (24 - (i % 4) * 8))       & 0xff;
                            var byte2 = (words[(i + 1) >>> 2] >>> (24 - ((i + 1) % 4) * 8)) & 0xff;
                            var byte3 = (words[(i + 2) >>> 2] >>> (24 - ((i + 2) % 4) * 8)) & 0xff;

                            var triplet = (byte1 << 16) | (byte2 << 8) | byte3;

                            for (var j = 0; (j < 4) && (i + j * 0.75 < sigBytes); j++) {
                                base64Chars.push(map.charAt((triplet >>> (6 * (3 - j))) & 0x3f));
                            }
                        }

                        // Add padding
                        var paddingChar = map.charAt(64);
                        if (paddingChar) {
                            while (base64Chars.length % 4) {
                                base64Chars.push(paddingChar);
                            }
                        }

                        return base64Chars.join('');
                    },

                    /**
                     * Converts a Base64 string to a word array.
                     *
                     * @param {string} base64Str The Base64 string.
                     *
                     * @return {WordArray} The word array.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var wordArray = CryptoJS.enc.Base64.parse(base64String);
                     */
                    parse: function (base64Str) {
                        // Shortcuts
                        var base64StrLength = base64Str.length;
                        var map = this._map;
                        var reverseMap = this._reverseMap;

                        if (!reverseMap) {
                            reverseMap = this._reverseMap = [];
                            for (var j = 0; j < map.length; j++) {
                                reverseMap[map.charCodeAt(j)] = j;
                            }
                        }

                        // Ignore padding
                        var paddingChar = map.charAt(64);
                        if (paddingChar) {
                            var paddingIndex = base64Str.indexOf(paddingChar);
                            if (paddingIndex !== -1) {
                                base64StrLength = paddingIndex;
                            }
                        }

                        // Convert
                        return parseLoop(base64Str, base64StrLength, reverseMap);

                    },

                    _map: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/='
                };

                function parseLoop(base64Str, base64StrLength, reverseMap) {
                    var words = [];
                    var nBytes = 0;
                    for (var i = 0; i < base64StrLength; i++) {
                        if (i % 4) {
                            var bits1 = reverseMap[base64Str.charCodeAt(i - 1)] << ((i % 4) * 2);
                            var bits2 = reverseMap[base64Str.charCodeAt(i)] >>> (6 - (i % 4) * 2);
                            var bitsCombined = bits1 | bits2;
                            words[nBytes >>> 2] |= bitsCombined << (24 - (nBytes % 4) * 8);
                            nBytes++;
                        }
                    }
                    return WordArray.create(words, nBytes);
                }
            }());


            (function (Math) {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var WordArray = C_lib.WordArray;
                var Hasher = C_lib.Hasher;
                var C_algo = C.algo;

                // Constants table
                var T = [];

                // Compute constants
                (function () {
                    for (var i = 0; i < 64; i++) {
                        T[i] = (Math.abs(Math.sin(i + 1)) * 0x100000000) | 0;
                    }
                }());

                /**
                 * MD5 hash algorithm.
                 */
                var MD5 = C_algo.MD5 = Hasher.extend({
                    _doReset: function () {
                        this._hash = new WordArray.init([
                            0x67452301, 0xefcdab89,
                            0x98badcfe, 0x10325476
                        ]);
                    },

                    _doProcessBlock: function (M, offset) {
                        // Swap endian
                        for (var i = 0; i < 16; i++) {
                            // Shortcuts
                            var offset_i = offset + i;
                            var M_offset_i = M[offset_i];

                            M[offset_i] = (
                                (((M_offset_i << 8)  | (M_offset_i >>> 24)) & 0x00ff00ff) |
                                (((M_offset_i << 24) | (M_offset_i >>> 8))  & 0xff00ff00)
                            );
                        }

                        // Shortcuts
                        var H = this._hash.words;

                        var M_offset_0  = M[offset + 0];
                        var M_offset_1  = M[offset + 1];
                        var M_offset_2  = M[offset + 2];
                        var M_offset_3  = M[offset + 3];
                        var M_offset_4  = M[offset + 4];
                        var M_offset_5  = M[offset + 5];
                        var M_offset_6  = M[offset + 6];
                        var M_offset_7  = M[offset + 7];
                        var M_offset_8  = M[offset + 8];
                        var M_offset_9  = M[offset + 9];
                        var M_offset_10 = M[offset + 10];
                        var M_offset_11 = M[offset + 11];
                        var M_offset_12 = M[offset + 12];
                        var M_offset_13 = M[offset + 13];
                        var M_offset_14 = M[offset + 14];
                        var M_offset_15 = M[offset + 15];

                        // Working varialbes
                        var a = H[0];
                        var b = H[1];
                        var c = H[2];
                        var d = H[3];

                        // Computation
                        a = FF(a, b, c, d, M_offset_0,  7,  T[0]);
                        d = FF(d, a, b, c, M_offset_1,  12, T[1]);
                        c = FF(c, d, a, b, M_offset_2,  17, T[2]);
                        b = FF(b, c, d, a, M_offset_3,  22, T[3]);
                        a = FF(a, b, c, d, M_offset_4,  7,  T[4]);
                        d = FF(d, a, b, c, M_offset_5,  12, T[5]);
                        c = FF(c, d, a, b, M_offset_6,  17, T[6]);
                        b = FF(b, c, d, a, M_offset_7,  22, T[7]);
                        a = FF(a, b, c, d, M_offset_8,  7,  T[8]);
                        d = FF(d, a, b, c, M_offset_9,  12, T[9]);
                        c = FF(c, d, a, b, M_offset_10, 17, T[10]);
                        b = FF(b, c, d, a, M_offset_11, 22, T[11]);
                        a = FF(a, b, c, d, M_offset_12, 7,  T[12]);
                        d = FF(d, a, b, c, M_offset_13, 12, T[13]);
                        c = FF(c, d, a, b, M_offset_14, 17, T[14]);
                        b = FF(b, c, d, a, M_offset_15, 22, T[15]);

                        a = GG(a, b, c, d, M_offset_1,  5,  T[16]);
                        d = GG(d, a, b, c, M_offset_6,  9,  T[17]);
                        c = GG(c, d, a, b, M_offset_11, 14, T[18]);
                        b = GG(b, c, d, a, M_offset_0,  20, T[19]);
                        a = GG(a, b, c, d, M_offset_5,  5,  T[20]);
                        d = GG(d, a, b, c, M_offset_10, 9,  T[21]);
                        c = GG(c, d, a, b, M_offset_15, 14, T[22]);
                        b = GG(b, c, d, a, M_offset_4,  20, T[23]);
                        a = GG(a, b, c, d, M_offset_9,  5,  T[24]);
                        d = GG(d, a, b, c, M_offset_14, 9,  T[25]);
                        c = GG(c, d, a, b, M_offset_3,  14, T[26]);
                        b = GG(b, c, d, a, M_offset_8,  20, T[27]);
                        a = GG(a, b, c, d, M_offset_13, 5,  T[28]);
                        d = GG(d, a, b, c, M_offset_2,  9,  T[29]);
                        c = GG(c, d, a, b, M_offset_7,  14, T[30]);
                        b = GG(b, c, d, a, M_offset_12, 20, T[31]);

                        a = HH(a, b, c, d, M_offset_5,  4,  T[32]);
                        d = HH(d, a, b, c, M_offset_8,  11, T[33]);
                        c = HH(c, d, a, b, M_offset_11, 16, T[34]);
                        b = HH(b, c, d, a, M_offset_14, 23, T[35]);
                        a = HH(a, b, c, d, M_offset_1,  4,  T[36]);
                        d = HH(d, a, b, c, M_offset_4,  11, T[37]);
                        c = HH(c, d, a, b, M_offset_7,  16, T[38]);
                        b = HH(b, c, d, a, M_offset_10, 23, T[39]);
                        a = HH(a, b, c, d, M_offset_13, 4,  T[40]);
                        d = HH(d, a, b, c, M_offset_0,  11, T[41]);
                        c = HH(c, d, a, b, M_offset_3,  16, T[42]);
                        b = HH(b, c, d, a, M_offset_6,  23, T[43]);
                        a = HH(a, b, c, d, M_offset_9,  4,  T[44]);
                        d = HH(d, a, b, c, M_offset_12, 11, T[45]);
                        c = HH(c, d, a, b, M_offset_15, 16, T[46]);
                        b = HH(b, c, d, a, M_offset_2,  23, T[47]);

                        a = II(a, b, c, d, M_offset_0,  6,  T[48]);
                        d = II(d, a, b, c, M_offset_7,  10, T[49]);
                        c = II(c, d, a, b, M_offset_14, 15, T[50]);
                        b = II(b, c, d, a, M_offset_5,  21, T[51]);
                        a = II(a, b, c, d, M_offset_12, 6,  T[52]);
                        d = II(d, a, b, c, M_offset_3,  10, T[53]);
                        c = II(c, d, a, b, M_offset_10, 15, T[54]);
                        b = II(b, c, d, a, M_offset_1,  21, T[55]);
                        a = II(a, b, c, d, M_offset_8,  6,  T[56]);
                        d = II(d, a, b, c, M_offset_15, 10, T[57]);
                        c = II(c, d, a, b, M_offset_6,  15, T[58]);
                        b = II(b, c, d, a, M_offset_13, 21, T[59]);
                        a = II(a, b, c, d, M_offset_4,  6,  T[60]);
                        d = II(d, a, b, c, M_offset_11, 10, T[61]);
                        c = II(c, d, a, b, M_offset_2,  15, T[62]);
                        b = II(b, c, d, a, M_offset_9,  21, T[63]);

                        // Intermediate hash value
                        H[0] = (H[0] + a) | 0;
                        H[1] = (H[1] + b) | 0;
                        H[2] = (H[2] + c) | 0;
                        H[3] = (H[3] + d) | 0;
                    },

                    _doFinalize: function () {
                        // Shortcuts
                        var data = this._data;
                        var dataWords = data.words;

                        var nBitsTotal = this._nDataBytes * 8;
                        var nBitsLeft = data.sigBytes * 8;

                        // Add padding
                        dataWords[nBitsLeft >>> 5] |= 0x80 << (24 - nBitsLeft % 32);

                        var nBitsTotalH = Math.floor(nBitsTotal / 0x100000000);
                        var nBitsTotalL = nBitsTotal;
                        dataWords[(((nBitsLeft + 64) >>> 9) << 4) + 15] = (
                            (((nBitsTotalH << 8)  | (nBitsTotalH >>> 24)) & 0x00ff00ff) |
                            (((nBitsTotalH << 24) | (nBitsTotalH >>> 8))  & 0xff00ff00)
                        );
                        dataWords[(((nBitsLeft + 64) >>> 9) << 4) + 14] = (
                            (((nBitsTotalL << 8)  | (nBitsTotalL >>> 24)) & 0x00ff00ff) |
                            (((nBitsTotalL << 24) | (nBitsTotalL >>> 8))  & 0xff00ff00)
                        );

                        data.sigBytes = (dataWords.length + 1) * 4;

                        // Hash final blocks
                        this._process();

                        // Shortcuts
                        var hash = this._hash;
                        var H = hash.words;

                        // Swap endian
                        for (var i = 0; i < 4; i++) {
                            // Shortcut
                            var H_i = H[i];

                            H[i] = (((H_i << 8)  | (H_i >>> 24)) & 0x00ff00ff) |
                                (((H_i << 24) | (H_i >>> 8))  & 0xff00ff00);
                        }

                        // Return final computed hash
                        return hash;
                    },

                    clone: function () {
                        var clone = Hasher.clone.call(this);
                        clone._hash = this._hash.clone();

                        return clone;
                    }
                });

                function FF(a, b, c, d, x, s, t) {
                    var n = a + ((b & c) | (~b & d)) + x + t;
                    return ((n << s) | (n >>> (32 - s))) + b;
                }

                function GG(a, b, c, d, x, s, t) {
                    var n = a + ((b & d) | (c & ~d)) + x + t;
                    return ((n << s) | (n >>> (32 - s))) + b;
                }

                function HH(a, b, c, d, x, s, t) {
                    var n = a + (b ^ c ^ d) + x + t;
                    return ((n << s) | (n >>> (32 - s))) + b;
                }

                function II(a, b, c, d, x, s, t) {
                    var n = a + (c ^ (b | ~d)) + x + t;
                    return ((n << s) | (n >>> (32 - s))) + b;
                }

                /**
                 * Shortcut function to the hasher's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 *
                 * @return {WordArray} The hash.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hash = CryptoJS.MD5('message');
                 *     var hash = CryptoJS.MD5(wordArray);
                 */
                C.MD5 = Hasher._createHelper(MD5);

                /**
                 * Shortcut function to the HMAC's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 * @param {WordArray|string} key The secret key.
                 *
                 * @return {WordArray} The HMAC.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hmac = CryptoJS.HmacMD5(message, key);
                 */
                C.HmacMD5 = Hasher._createHmacHelper(MD5);
            }(Math));


            (function () {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var WordArray = C_lib.WordArray;
                var Hasher = C_lib.Hasher;
                var C_algo = C.algo;

                // Reusable object
                var W = [];

                /**
                 * SHA-1 hash algorithm.
                 */
                var SHA1 = C_algo.SHA1 = Hasher.extend({
                    _doReset: function () {
                        this._hash = new WordArray.init([
                            0x67452301, 0xefcdab89,
                            0x98badcfe, 0x10325476,
                            0xc3d2e1f0
                        ]);
                    },

                    _doProcessBlock: function (M, offset) {
                        // Shortcut
                        var H = this._hash.words;

                        // Working variables
                        var a = H[0];
                        var b = H[1];
                        var c = H[2];
                        var d = H[3];
                        var e = H[4];

                        // Computation
                        for (var i = 0; i < 80; i++) {
                            if (i < 16) {
                                W[i] = M[offset + i] | 0;
                            } else {
                                var n = W[i - 3] ^ W[i - 8] ^ W[i - 14] ^ W[i - 16];
                                W[i] = (n << 1) | (n >>> 31);
                            }

                            var t = ((a << 5) | (a >>> 27)) + e + W[i];
                            if (i < 20) {
                                t += ((b & c) | (~b & d)) + 0x5a827999;
                            } else if (i < 40) {
                                t += (b ^ c ^ d) + 0x6ed9eba1;
                            } else if (i < 60) {
                                t += ((b & c) | (b & d) | (c & d)) - 0x70e44324;
                            } else /* if (i < 80) */ {
                                t += (b ^ c ^ d) - 0x359d3e2a;
                            }

                            e = d;
                            d = c;
                            c = (b << 30) | (b >>> 2);
                            b = a;
                            a = t;
                        }

                        // Intermediate hash value
                        H[0] = (H[0] + a) | 0;
                        H[1] = (H[1] + b) | 0;
                        H[2] = (H[2] + c) | 0;
                        H[3] = (H[3] + d) | 0;
                        H[4] = (H[4] + e) | 0;
                    },

                    _doFinalize: function () {
                        // Shortcuts
                        var data = this._data;
                        var dataWords = data.words;

                        var nBitsTotal = this._nDataBytes * 8;
                        var nBitsLeft = data.sigBytes * 8;

                        // Add padding
                        dataWords[nBitsLeft >>> 5] |= 0x80 << (24 - nBitsLeft % 32);
                        dataWords[(((nBitsLeft + 64) >>> 9) << 4) + 14] = Math.floor(nBitsTotal / 0x100000000);
                        dataWords[(((nBitsLeft + 64) >>> 9) << 4) + 15] = nBitsTotal;
                        data.sigBytes = dataWords.length * 4;

                        // Hash final blocks
                        this._process();

                        // Return final computed hash
                        return this._hash;
                    },

                    clone: function () {
                        var clone = Hasher.clone.call(this);
                        clone._hash = this._hash.clone();

                        return clone;
                    }
                });

                /**
                 * Shortcut function to the hasher's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 *
                 * @return {WordArray} The hash.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hash = CryptoJS.SHA1('message');
                 *     var hash = CryptoJS.SHA1(wordArray);
                 */
                C.SHA1 = Hasher._createHelper(SHA1);

                /**
                 * Shortcut function to the HMAC's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 * @param {WordArray|string} key The secret key.
                 *
                 * @return {WordArray} The HMAC.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hmac = CryptoJS.HmacSHA1(message, key);
                 */
                C.HmacSHA1 = Hasher._createHmacHelper(SHA1);
            }());


            (function (Math) {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var WordArray = C_lib.WordArray;
                var Hasher = C_lib.Hasher;
                var C_algo = C.algo;

                // Initialization and round constants tables
                var H = [];
                var K = [];

                // Compute constants
                (function () {
                    function isPrime(n) {
                        var sqrtN = Math.sqrt(n);
                        for (var factor = 2; factor <= sqrtN; factor++) {
                            if (!(n % factor)) {
                                return false;
                            }
                        }

                        return true;
                    }

                    function getFractionalBits(n) {
                        return ((n - (n | 0)) * 0x100000000) | 0;
                    }

                    var n = 2;
                    var nPrime = 0;
                    while (nPrime < 64) {
                        if (isPrime(n)) {
                            if (nPrime < 8) {
                                H[nPrime] = getFractionalBits(Math.pow(n, 1 / 2));
                            }
                            K[nPrime] = getFractionalBits(Math.pow(n, 1 / 3));

                            nPrime++;
                        }

                        n++;
                    }
                }());

                // Reusable object
                var W = [];

                /**
                 * SHA-256 hash algorithm.
                 */
                var SHA256 = C_algo.SHA256 = Hasher.extend({
                    _doReset: function () {
                        this._hash = new WordArray.init(H.slice(0));
                    },

                    _doProcessBlock: function (M, offset) {
                        // Shortcut
                        var H = this._hash.words;

                        // Working variables
                        var a = H[0];
                        var b = H[1];
                        var c = H[2];
                        var d = H[3];
                        var e = H[4];
                        var f = H[5];
                        var g = H[6];
                        var h = H[7];

                        // Computation
                        for (var i = 0; i < 64; i++) {
                            if (i < 16) {
                                W[i] = M[offset + i] | 0;
                            } else {
                                var gamma0x = W[i - 15];
                                var gamma0  = ((gamma0x << 25) | (gamma0x >>> 7))  ^
                                    ((gamma0x << 14) | (gamma0x >>> 18)) ^
                                    (gamma0x >>> 3);

                                var gamma1x = W[i - 2];
                                var gamma1  = ((gamma1x << 15) | (gamma1x >>> 17)) ^
                                    ((gamma1x << 13) | (gamma1x >>> 19)) ^
                                    (gamma1x >>> 10);

                                W[i] = gamma0 + W[i - 7] + gamma1 + W[i - 16];
                            }

                            var ch  = (e & f) ^ (~e & g);
                            var maj = (a & b) ^ (a & c) ^ (b & c);

                            var sigma0 = ((a << 30) | (a >>> 2)) ^ ((a << 19) | (a >>> 13)) ^ ((a << 10) | (a >>> 22));
                            var sigma1 = ((e << 26) | (e >>> 6)) ^ ((e << 21) | (e >>> 11)) ^ ((e << 7)  | (e >>> 25));

                            var t1 = h + sigma1 + ch + K[i] + W[i];
                            var t2 = sigma0 + maj;

                            h = g;
                            g = f;
                            f = e;
                            e = (d + t1) | 0;
                            d = c;
                            c = b;
                            b = a;
                            a = (t1 + t2) | 0;
                        }

                        // Intermediate hash value
                        H[0] = (H[0] + a) | 0;
                        H[1] = (H[1] + b) | 0;
                        H[2] = (H[2] + c) | 0;
                        H[3] = (H[3] + d) | 0;
                        H[4] = (H[4] + e) | 0;
                        H[5] = (H[5] + f) | 0;
                        H[6] = (H[6] + g) | 0;
                        H[7] = (H[7] + h) | 0;
                    },

                    _doFinalize: function () {
                        // Shortcuts
                        var data = this._data;
                        var dataWords = data.words;

                        var nBitsTotal = this._nDataBytes * 8;
                        var nBitsLeft = data.sigBytes * 8;

                        // Add padding
                        dataWords[nBitsLeft >>> 5] |= 0x80 << (24 - nBitsLeft % 32);
                        dataWords[(((nBitsLeft + 64) >>> 9) << 4) + 14] = Math.floor(nBitsTotal / 0x100000000);
                        dataWords[(((nBitsLeft + 64) >>> 9) << 4) + 15] = nBitsTotal;
                        data.sigBytes = dataWords.length * 4;

                        // Hash final blocks
                        this._process();

                        // Return final computed hash
                        return this._hash;
                    },

                    clone: function () {
                        var clone = Hasher.clone.call(this);
                        clone._hash = this._hash.clone();

                        return clone;
                    }
                });

                /**
                 * Shortcut function to the hasher's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 *
                 * @return {WordArray} The hash.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hash = CryptoJS.SHA256('message');
                 *     var hash = CryptoJS.SHA256(wordArray);
                 */
                C.SHA256 = Hasher._createHelper(SHA256);

                /**
                 * Shortcut function to the HMAC's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 * @param {WordArray|string} key The secret key.
                 *
                 * @return {WordArray} The HMAC.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hmac = CryptoJS.HmacSHA256(message, key);
                 */
                C.HmacSHA256 = Hasher._createHmacHelper(SHA256);
            }(Math));


            (function () {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var WordArray = C_lib.WordArray;
                var C_enc = C.enc;

                /**
                 * UTF-16 BE encoding strategy.
                 */
                var Utf16BE = C_enc.Utf16 = C_enc.Utf16BE = {
                    /**
                     * Converts a word array to a UTF-16 BE string.
                     *
                     * @param {WordArray} wordArray The word array.
                     *
                     * @return {string} The UTF-16 BE string.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var utf16String = CryptoJS.enc.Utf16.stringify(wordArray);
                     */
                    stringify: function (wordArray) {
                        // Shortcuts
                        var words = wordArray.words;
                        var sigBytes = wordArray.sigBytes;

                        // Convert
                        var utf16Chars = [];
                        for (var i = 0; i < sigBytes; i += 2) {
                            var codePoint = (words[i >>> 2] >>> (16 - (i % 4) * 8)) & 0xffff;
                            utf16Chars.push(String.fromCharCode(codePoint));
                        }

                        return utf16Chars.join('');
                    },

                    /**
                     * Converts a UTF-16 BE string to a word array.
                     *
                     * @param {string} utf16Str The UTF-16 BE string.
                     *
                     * @return {WordArray} The word array.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var wordArray = CryptoJS.enc.Utf16.parse(utf16String);
                     */
                    parse: function (utf16Str) {
                        // Shortcut
                        var utf16StrLength = utf16Str.length;

                        // Convert
                        var words = [];
                        for (var i = 0; i < utf16StrLength; i++) {
                            words[i >>> 1] |= utf16Str.charCodeAt(i) << (16 - (i % 2) * 16);
                        }

                        return WordArray.create(words, utf16StrLength * 2);
                    }
                };

                /**
                 * UTF-16 LE encoding strategy.
                 */
                C_enc.Utf16LE = {
                    /**
                     * Converts a word array to a UTF-16 LE string.
                     *
                     * @param {WordArray} wordArray The word array.
                     *
                     * @return {string} The UTF-16 LE string.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var utf16Str = CryptoJS.enc.Utf16LE.stringify(wordArray);
                     */
                    stringify: function (wordArray) {
                        // Shortcuts
                        var words = wordArray.words;
                        var sigBytes = wordArray.sigBytes;

                        // Convert
                        var utf16Chars = [];
                        for (var i = 0; i < sigBytes; i += 2) {
                            var codePoint = swapEndian((words[i >>> 2] >>> (16 - (i % 4) * 8)) & 0xffff);
                            utf16Chars.push(String.fromCharCode(codePoint));
                        }

                        return utf16Chars.join('');
                    },

                    /**
                     * Converts a UTF-16 LE string to a word array.
                     *
                     * @param {string} utf16Str The UTF-16 LE string.
                     *
                     * @return {WordArray} The word array.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var wordArray = CryptoJS.enc.Utf16LE.parse(utf16Str);
                     */
                    parse: function (utf16Str) {
                        // Shortcut
                        var utf16StrLength = utf16Str.length;

                        // Convert
                        var words = [];
                        for (var i = 0; i < utf16StrLength; i++) {
                            words[i >>> 1] |= swapEndian(utf16Str.charCodeAt(i) << (16 - (i % 2) * 16));
                        }

                        return WordArray.create(words, utf16StrLength * 2);
                    }
                };

                function swapEndian(word) {
                    return ((word << 8) & 0xff00ff00) | ((word >>> 8) & 0x00ff00ff);
                }
            }());


            (function () {
                // Check if typed arrays are supported
                if (typeof ArrayBuffer != 'function') {
                    return;
                }

                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var WordArray = C_lib.WordArray;

                // Reference original init
                var superInit = WordArray.init;

                // Augment WordArray.init to handle typed arrays
                var subInit = WordArray.init = function (typedArray) {
                    // Convert buffers to uint8
                    if (typedArray instanceof ArrayBuffer) {
                        typedArray = new Uint8Array(typedArray);
                    }

                    // Convert other array views to uint8
                    if (
                        typedArray instanceof Int8Array ||
                        (typeof Uint8ClampedArray !== "undefined" && typedArray instanceof Uint8ClampedArray) ||
                        typedArray instanceof Int16Array ||
                        typedArray instanceof Uint16Array ||
                        typedArray instanceof Int32Array ||
                        typedArray instanceof Uint32Array ||
                        typedArray instanceof Float32Array ||
                        typedArray instanceof Float64Array
                    ) {
                        typedArray = new Uint8Array(typedArray.buffer, typedArray.byteOffset, typedArray.byteLength);
                    }

                    // Handle Uint8Array
                    if (typedArray instanceof Uint8Array) {
                        // Shortcut
                        var typedArrayByteLength = typedArray.byteLength;

                        // Extract bytes
                        var words = [];
                        for (var i = 0; i < typedArrayByteLength; i++) {
                            words[i >>> 2] |= typedArray[i] << (24 - (i % 4) * 8);
                        }

                        // Initialize this word array
                        superInit.call(this, words, typedArrayByteLength);
                    } else {
                        // Else call normal init
                        superInit.apply(this, arguments);
                    }
                };

                subInit.prototype = WordArray;
            }());


            /** @preserve
             (c) 2012 by Cédric Mesnil. All rights reserved.

             Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

             - Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
             - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

             THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
             */

            (function (Math) {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var WordArray = C_lib.WordArray;
                var Hasher = C_lib.Hasher;
                var C_algo = C.algo;

                // Constants table
                var _zl = WordArray.create([
                    0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10, 11, 12, 13, 14, 15,
                    7,  4, 13,  1, 10,  6, 15,  3, 12,  0,  9,  5,  2, 14, 11,  8,
                    3, 10, 14,  4,  9, 15,  8,  1,  2,  7,  0,  6, 13, 11,  5, 12,
                    1,  9, 11, 10,  0,  8, 12,  4, 13,  3,  7, 15, 14,  5,  6,  2,
                    4,  0,  5,  9,  7, 12,  2, 10, 14,  1,  3,  8, 11,  6, 15, 13]);
                var _zr = WordArray.create([
                    5, 14,  7,  0,  9,  2, 11,  4, 13,  6, 15,  8,  1, 10,  3, 12,
                    6, 11,  3,  7,  0, 13,  5, 10, 14, 15,  8, 12,  4,  9,  1,  2,
                    15,  5,  1,  3,  7, 14,  6,  9, 11,  8, 12,  2, 10,  0,  4, 13,
                    8,  6,  4,  1,  3, 11, 15,  0,  5, 12,  2, 13,  9,  7, 10, 14,
                    12, 15, 10,  4,  1,  5,  8,  7,  6,  2, 13, 14,  0,  3,  9, 11]);
                var _sl = WordArray.create([
                    11, 14, 15, 12,  5,  8,  7,  9, 11, 13, 14, 15,  6,  7,  9,  8,
                    7, 6,   8, 13, 11,  9,  7, 15,  7, 12, 15,  9, 11,  7, 13, 12,
                    11, 13,  6,  7, 14,  9, 13, 15, 14,  8, 13,  6,  5, 12,  7,  5,
                    11, 12, 14, 15, 14, 15,  9,  8,  9, 14,  5,  6,  8,  6,  5, 12,
                    9, 15,  5, 11,  6,  8, 13, 12,  5, 12, 13, 14, 11,  8,  5,  6 ]);
                var _sr = WordArray.create([
                    8,  9,  9, 11, 13, 15, 15,  5,  7,  7,  8, 11, 14, 14, 12,  6,
                    9, 13, 15,  7, 12,  8,  9, 11,  7,  7, 12,  7,  6, 15, 13, 11,
                    9,  7, 15, 11,  8,  6,  6, 14, 12, 13,  5, 14, 13, 13,  7,  5,
                    15,  5,  8, 11, 14, 14,  6, 14,  6,  9, 12,  9, 12,  5, 15,  8,
                    8,  5, 12,  9, 12,  5, 14,  6,  8, 13,  6,  5, 15, 13, 11, 11 ]);

                var _hl =  WordArray.create([ 0x00000000, 0x5A827999, 0x6ED9EBA1, 0x8F1BBCDC, 0xA953FD4E]);
                var _hr =  WordArray.create([ 0x50A28BE6, 0x5C4DD124, 0x6D703EF3, 0x7A6D76E9, 0x00000000]);

                /**
                 * RIPEMD160 hash algorithm.
                 */
                var RIPEMD160 = C_algo.RIPEMD160 = Hasher.extend({
                    _doReset: function () {
                        this._hash  = WordArray.create([0x67452301, 0xEFCDAB89, 0x98BADCFE, 0x10325476, 0xC3D2E1F0]);
                    },

                    _doProcessBlock: function (M, offset) {

                        // Swap endian
                        for (var i = 0; i < 16; i++) {
                            // Shortcuts
                            var offset_i = offset + i;
                            var M_offset_i = M[offset_i];

                            // Swap
                            M[offset_i] = (
                                (((M_offset_i << 8)  | (M_offset_i >>> 24)) & 0x00ff00ff) |
                                (((M_offset_i << 24) | (M_offset_i >>> 8))  & 0xff00ff00)
                            );
                        }
                        // Shortcut
                        var H  = this._hash.words;
                        var hl = _hl.words;
                        var hr = _hr.words;
                        var zl = _zl.words;
                        var zr = _zr.words;
                        var sl = _sl.words;
                        var sr = _sr.words;

                        // Working variables
                        var al, bl, cl, dl, el;
                        var ar, br, cr, dr, er;

                        ar = al = H[0];
                        br = bl = H[1];
                        cr = cl = H[2];
                        dr = dl = H[3];
                        er = el = H[4];
                        // Computation
                        var t;
                        for (var i = 0; i < 80; i += 1) {
                            t = (al +  M[offset+zl[i]])|0;
                            if (i<16){
                                t +=  f1(bl,cl,dl) + hl[0];
                            } else if (i<32) {
                                t +=  f2(bl,cl,dl) + hl[1];
                            } else if (i<48) {
                                t +=  f3(bl,cl,dl) + hl[2];
                            } else if (i<64) {
                                t +=  f4(bl,cl,dl) + hl[3];
                            } else {// if (i<80) {
                                t +=  f5(bl,cl,dl) + hl[4];
                            }
                            t = t|0;
                            t =  rotl(t,sl[i]);
                            t = (t+el)|0;
                            al = el;
                            el = dl;
                            dl = rotl(cl, 10);
                            cl = bl;
                            bl = t;

                            t = (ar + M[offset+zr[i]])|0;
                            if (i<16){
                                t +=  f5(br,cr,dr) + hr[0];
                            } else if (i<32) {
                                t +=  f4(br,cr,dr) + hr[1];
                            } else if (i<48) {
                                t +=  f3(br,cr,dr) + hr[2];
                            } else if (i<64) {
                                t +=  f2(br,cr,dr) + hr[3];
                            } else {// if (i<80) {
                                t +=  f1(br,cr,dr) + hr[4];
                            }
                            t = t|0;
                            t =  rotl(t,sr[i]) ;
                            t = (t+er)|0;
                            ar = er;
                            er = dr;
                            dr = rotl(cr, 10);
                            cr = br;
                            br = t;
                        }
                        // Intermediate hash value
                        t    = (H[1] + cl + dr)|0;
                        H[1] = (H[2] + dl + er)|0;
                        H[2] = (H[3] + el + ar)|0;
                        H[3] = (H[4] + al + br)|0;
                        H[4] = (H[0] + bl + cr)|0;
                        H[0] =  t;
                    },

                    _doFinalize: function () {
                        // Shortcuts
                        var data = this._data;
                        var dataWords = data.words;

                        var nBitsTotal = this._nDataBytes * 8;
                        var nBitsLeft = data.sigBytes * 8;

                        // Add padding
                        dataWords[nBitsLeft >>> 5] |= 0x80 << (24 - nBitsLeft % 32);
                        dataWords[(((nBitsLeft + 64) >>> 9) << 4) + 14] = (
                            (((nBitsTotal << 8)  | (nBitsTotal >>> 24)) & 0x00ff00ff) |
                            (((nBitsTotal << 24) | (nBitsTotal >>> 8))  & 0xff00ff00)
                        );
                        data.sigBytes = (dataWords.length + 1) * 4;

                        // Hash final blocks
                        this._process();

                        // Shortcuts
                        var hash = this._hash;
                        var H = hash.words;

                        // Swap endian
                        for (var i = 0; i < 5; i++) {
                            // Shortcut
                            var H_i = H[i];

                            // Swap
                            H[i] = (((H_i << 8)  | (H_i >>> 24)) & 0x00ff00ff) |
                                (((H_i << 24) | (H_i >>> 8))  & 0xff00ff00);
                        }

                        // Return final computed hash
                        return hash;
                    },

                    clone: function () {
                        var clone = Hasher.clone.call(this);
                        clone._hash = this._hash.clone();

                        return clone;
                    }
                });


                function f1(x, y, z) {
                    return ((x) ^ (y) ^ (z));

                }

                function f2(x, y, z) {
                    return (((x)&(y)) | ((~x)&(z)));
                }

                function f3(x, y, z) {
                    return (((x) | (~(y))) ^ (z));
                }

                function f4(x, y, z) {
                    return (((x) & (z)) | ((y)&(~(z))));
                }

                function f5(x, y, z) {
                    return ((x) ^ ((y) |(~(z))));

                }

                function rotl(x,n) {
                    return (x<<n) | (x>>>(32-n));
                }


                /**
                 * Shortcut function to the hasher's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 *
                 * @return {WordArray} The hash.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hash = CryptoJS.RIPEMD160('message');
                 *     var hash = CryptoJS.RIPEMD160(wordArray);
                 */
                C.RIPEMD160 = Hasher._createHelper(RIPEMD160);

                /**
                 * Shortcut function to the HMAC's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 * @param {WordArray|string} key The secret key.
                 *
                 * @return {WordArray} The HMAC.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hmac = CryptoJS.HmacRIPEMD160(message, key);
                 */
                C.HmacRIPEMD160 = Hasher._createHmacHelper(RIPEMD160);
            }(Math));


            (function () {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var Base = C_lib.Base;
                var C_enc = C.enc;
                var Utf8 = C_enc.Utf8;
                var C_algo = C.algo;

                /**
                 * HMAC algorithm.
                 */
                var HMAC = C_algo.HMAC = Base.extend({
                    /**
                     * Initializes a newly created HMAC.
                     *
                     * @param {Hasher} hasher The hash algorithm to use.
                     * @param {WordArray|string} key The secret key.
                     *
                     * @example
                     *
                     *     var hmacHasher = CryptoJS.algo.HMAC.create(CryptoJS.algo.SHA256, key);
                     */
                    init: function (hasher, key) {
                        // Init hasher
                        hasher = this._hasher = new hasher.init();

                        // Convert string to WordArray, else assume WordArray already
                        if (typeof key == 'string') {
                            key = Utf8.parse(key);
                        }

                        // Shortcuts
                        var hasherBlockSize = hasher.blockSize;
                        var hasherBlockSizeBytes = hasherBlockSize * 4;

                        // Allow arbitrary length keys
                        if (key.sigBytes > hasherBlockSizeBytes) {
                            key = hasher.finalize(key);
                        }

                        // Clamp excess bits
                        key.clamp();

                        // Clone key for inner and outer pads
                        var oKey = this._oKey = key.clone();
                        var iKey = this._iKey = key.clone();

                        // Shortcuts
                        var oKeyWords = oKey.words;
                        var iKeyWords = iKey.words;

                        // XOR keys with pad constants
                        for (var i = 0; i < hasherBlockSize; i++) {
                            oKeyWords[i] ^= 0x5c5c5c5c;
                            iKeyWords[i] ^= 0x36363636;
                        }
                        oKey.sigBytes = iKey.sigBytes = hasherBlockSizeBytes;

                        // Set initial values
                        this.reset();
                    },

                    /**
                     * Resets this HMAC to its initial state.
                     *
                     * @example
                     *
                     *     hmacHasher.reset();
                     */
                    reset: function () {
                        // Shortcut
                        var hasher = this._hasher;

                        // Reset
                        hasher.reset();
                        hasher.update(this._iKey);
                    },

                    /**
                     * Updates this HMAC with a message.
                     *
                     * @param {WordArray|string} messageUpdate The message to append.
                     *
                     * @return {HMAC} This HMAC instance.
                     *
                     * @example
                     *
                     *     hmacHasher.update('message');
                     *     hmacHasher.update(wordArray);
                     */
                    update: function (messageUpdate) {
                        this._hasher.update(messageUpdate);

                        // Chainable
                        return this;
                    },

                    /**
                     * Finalizes the HMAC computation.
                     * Note that the finalize operation is effectively a destructive, read-once operation.
                     *
                     * @param {WordArray|string} messageUpdate (Optional) A final message update.
                     *
                     * @return {WordArray} The HMAC.
                     *
                     * @example
                     *
                     *     var hmac = hmacHasher.finalize();
                     *     var hmac = hmacHasher.finalize('message');
                     *     var hmac = hmacHasher.finalize(wordArray);
                     */
                    finalize: function (messageUpdate) {
                        // Shortcut
                        var hasher = this._hasher;

                        // Compute HMAC
                        var innerHash = hasher.finalize(messageUpdate);
                        hasher.reset();
                        var hmac = hasher.finalize(this._oKey.clone().concat(innerHash));

                        return hmac;
                    }
                });
            }());


            (function () {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var Base = C_lib.Base;
                var WordArray = C_lib.WordArray;
                var C_algo = C.algo;
                var SHA1 = C_algo.SHA1;
                var HMAC = C_algo.HMAC;

                /**
                 * Password-Based Key Derivation Function 2 algorithm.
                 */
                var PBKDF2 = C_algo.PBKDF2 = Base.extend({
                    /**
                     * Configuration options.
                     *
                     * @property {number} keySize The key size in words to generate. Default: 4 (128 bits)
                     * @property {Hasher} hasher The hasher to use. Default: SHA1
                     * @property {number} iterations The number of iterations to perform. Default: 1
                     */
                    cfg: Base.extend({
                        keySize: 128/32,
                        hasher: SHA1,
                        iterations: 1
                    }),

                    /**
                     * Initializes a newly created key derivation function.
                     *
                     * @param {Object} cfg (Optional) The configuration options to use for the derivation.
                     *
                     * @example
                     *
                     *     var kdf = CryptoJS.algo.PBKDF2.create();
                     *     var kdf = CryptoJS.algo.PBKDF2.create({ keySize: 8 });
                     *     var kdf = CryptoJS.algo.PBKDF2.create({ keySize: 8, iterations: 1000 });
                     */
                    init: function (cfg) {
                        this.cfg = this.cfg.extend(cfg);
                    },

                    /**
                     * Computes the Password-Based Key Derivation Function 2.
                     *
                     * @param {WordArray|string} password The password.
                     * @param {WordArray|string} salt A salt.
                     *
                     * @return {WordArray} The derived key.
                     *
                     * @example
                     *
                     *     var key = kdf.compute(password, salt);
                     */
                    compute: function (password, salt) {
                        // Shortcut
                        var cfg = this.cfg;

                        // Init HMAC
                        var hmac = HMAC.create(cfg.hasher, password);

                        // Initial values
                        var derivedKey = WordArray.create();
                        var blockIndex = WordArray.create([0x00000001]);

                        // Shortcuts
                        var derivedKeyWords = derivedKey.words;
                        var blockIndexWords = blockIndex.words;
                        var keySize = cfg.keySize;
                        var iterations = cfg.iterations;

                        // Generate key
                        while (derivedKeyWords.length < keySize) {
                            var block = hmac.update(salt).finalize(blockIndex);
                            hmac.reset();

                            // Shortcuts
                            var blockWords = block.words;
                            var blockWordsLength = blockWords.length;

                            // Iterations
                            var intermediate = block;
                            for (var i = 1; i < iterations; i++) {
                                intermediate = hmac.finalize(intermediate);
                                hmac.reset();

                                // Shortcut
                                var intermediateWords = intermediate.words;

                                // XOR intermediate with block
                                for (var j = 0; j < blockWordsLength; j++) {
                                    blockWords[j] ^= intermediateWords[j];
                                }
                            }

                            derivedKey.concat(block);
                            blockIndexWords[0]++;
                        }
                        derivedKey.sigBytes = keySize * 4;

                        return derivedKey;
                    }
                });

                /**
                 * Computes the Password-Based Key Derivation Function 2.
                 *
                 * @param {WordArray|string} password The password.
                 * @param {WordArray|string} salt A salt.
                 * @param {Object} cfg (Optional) The configuration options to use for this computation.
                 *
                 * @return {WordArray} The derived key.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var key = CryptoJS.PBKDF2(password, salt);
                 *     var key = CryptoJS.PBKDF2(password, salt, { keySize: 8 });
                 *     var key = CryptoJS.PBKDF2(password, salt, { keySize: 8, iterations: 1000 });
                 */
                C.PBKDF2 = function (password, salt, cfg) {
                    return PBKDF2.create(cfg).compute(password, salt);
                };
            }());


            (function () {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var Base = C_lib.Base;
                var WordArray = C_lib.WordArray;
                var C_algo = C.algo;
                var MD5 = C_algo.MD5;

                /**
                 * This key derivation function is meant to conform with EVP_BytesToKey.
                 * www.openssl.org/docs/crypto/EVP_BytesToKey.html
                 */
                var EvpKDF = C_algo.EvpKDF = Base.extend({
                    /**
                     * Configuration options.
                     *
                     * @property {number} keySize The key size in words to generate. Default: 4 (128 bits)
                     * @property {Hasher} hasher The hash algorithm to use. Default: MD5
                     * @property {number} iterations The number of iterations to perform. Default: 1
                     */
                    cfg: Base.extend({
                        keySize: 128/32,
                        hasher: MD5,
                        iterations: 1
                    }),

                    /**
                     * Initializes a newly created key derivation function.
                     *
                     * @param {Object} cfg (Optional) The configuration options to use for the derivation.
                     *
                     * @example
                     *
                     *     var kdf = CryptoJS.algo.EvpKDF.create();
                     *     var kdf = CryptoJS.algo.EvpKDF.create({ keySize: 8 });
                     *     var kdf = CryptoJS.algo.EvpKDF.create({ keySize: 8, iterations: 1000 });
                     */
                    init: function (cfg) {
                        this.cfg = this.cfg.extend(cfg);
                    },

                    /**
                     * Derives a key from a password.
                     *
                     * @param {WordArray|string} password The password.
                     * @param {WordArray|string} salt A salt.
                     *
                     * @return {WordArray} The derived key.
                     *
                     * @example
                     *
                     *     var key = kdf.compute(password, salt);
                     */
                    compute: function (password, salt) {
                        var block;

                        // Shortcut
                        var cfg = this.cfg;

                        // Init hasher
                        var hasher = cfg.hasher.create();

                        // Initial values
                        var derivedKey = WordArray.create();

                        // Shortcuts
                        var derivedKeyWords = derivedKey.words;
                        var keySize = cfg.keySize;
                        var iterations = cfg.iterations;

                        // Generate key
                        while (derivedKeyWords.length < keySize) {
                            if (block) {
                                hasher.update(block);
                            }
                            block = hasher.update(password).finalize(salt);
                            hasher.reset();

                            // Iterations
                            for (var i = 1; i < iterations; i++) {
                                block = hasher.finalize(block);
                                hasher.reset();
                            }

                            derivedKey.concat(block);
                        }
                        derivedKey.sigBytes = keySize * 4;

                        return derivedKey;
                    }
                });

                /**
                 * Derives a key from a password.
                 *
                 * @param {WordArray|string} password The password.
                 * @param {WordArray|string} salt A salt.
                 * @param {Object} cfg (Optional) The configuration options to use for this computation.
                 *
                 * @return {WordArray} The derived key.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var key = CryptoJS.EvpKDF(password, salt);
                 *     var key = CryptoJS.EvpKDF(password, salt, { keySize: 8 });
                 *     var key = CryptoJS.EvpKDF(password, salt, { keySize: 8, iterations: 1000 });
                 */
                C.EvpKDF = function (password, salt, cfg) {
                    return EvpKDF.create(cfg).compute(password, salt);
                };
            }());


            (function () {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var WordArray = C_lib.WordArray;
                var C_algo = C.algo;
                var SHA256 = C_algo.SHA256;

                /**
                 * SHA-224 hash algorithm.
                 */
                var SHA224 = C_algo.SHA224 = SHA256.extend({
                    _doReset: function () {
                        this._hash = new WordArray.init([
                            0xc1059ed8, 0x367cd507, 0x3070dd17, 0xf70e5939,
                            0xffc00b31, 0x68581511, 0x64f98fa7, 0xbefa4fa4
                        ]);
                    },

                    _doFinalize: function () {
                        var hash = SHA256._doFinalize.call(this);

                        hash.sigBytes -= 4;

                        return hash;
                    }
                });

                /**
                 * Shortcut function to the hasher's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 *
                 * @return {WordArray} The hash.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hash = CryptoJS.SHA224('message');
                 *     var hash = CryptoJS.SHA224(wordArray);
                 */
                C.SHA224 = SHA256._createHelper(SHA224);

                /**
                 * Shortcut function to the HMAC's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 * @param {WordArray|string} key The secret key.
                 *
                 * @return {WordArray} The HMAC.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hmac = CryptoJS.HmacSHA224(message, key);
                 */
                C.HmacSHA224 = SHA256._createHmacHelper(SHA224);
            }());


            (function (undefined) {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var Base = C_lib.Base;
                var X32WordArray = C_lib.WordArray;

                /**
                 * x64 namespace.
                 */
                var C_x64 = C.x64 = {};

                /**
                 * A 64-bit word.
                 */
                var X64Word = C_x64.Word = Base.extend({
                    /**
                     * Initializes a newly created 64-bit word.
                     *
                     * @param {number} high The high 32 bits.
                     * @param {number} low The low 32 bits.
                     *
                     * @example
                     *
                     *     var x64Word = CryptoJS.x64.Word.create(0x00010203, 0x04050607);
                     */
                    init: function (high, low) {
                        this.high = high;
                        this.low = low;
                    }

                    /**
                     * Bitwise NOTs this word.
                     *
                     * @return {X64Word} A new x64-Word object after negating.
                     *
                     * @example
                     *
                     *     var negated = x64Word.not();
                     */
                    // not: function () {
                    // var high = ~this.high;
                    // var low = ~this.low;

                    // return X64Word.create(high, low);
                    // },

                    /**
                     * Bitwise ANDs this word with the passed word.
                     *
                     * @param {X64Word} word The x64-Word to AND with this word.
                     *
                     * @return {X64Word} A new x64-Word object after ANDing.
                     *
                     * @example
                     *
                     *     var anded = x64Word.and(anotherX64Word);
                     */
                    // and: function (word) {
                    // var high = this.high & word.high;
                    // var low = this.low & word.low;

                    // return X64Word.create(high, low);
                    // },

                    /**
                     * Bitwise ORs this word with the passed word.
                     *
                     * @param {X64Word} word The x64-Word to OR with this word.
                     *
                     * @return {X64Word} A new x64-Word object after ORing.
                     *
                     * @example
                     *
                     *     var ored = x64Word.or(anotherX64Word);
                     */
                    // or: function (word) {
                    // var high = this.high | word.high;
                    // var low = this.low | word.low;

                    // return X64Word.create(high, low);
                    // },

                    /**
                     * Bitwise XORs this word with the passed word.
                     *
                     * @param {X64Word} word The x64-Word to XOR with this word.
                     *
                     * @return {X64Word} A new x64-Word object after XORing.
                     *
                     * @example
                     *
                     *     var xored = x64Word.xor(anotherX64Word);
                     */
                    // xor: function (word) {
                    // var high = this.high ^ word.high;
                    // var low = this.low ^ word.low;

                    // return X64Word.create(high, low);
                    // },

                    /**
                     * Shifts this word n bits to the left.
                     *
                     * @param {number} n The number of bits to shift.
                     *
                     * @return {X64Word} A new x64-Word object after shifting.
                     *
                     * @example
                     *
                     *     var shifted = x64Word.shiftL(25);
                     */
                    // shiftL: function (n) {
                    // if (n < 32) {
                    // var high = (this.high << n) | (this.low >>> (32 - n));
                    // var low = this.low << n;
                    // } else {
                    // var high = this.low << (n - 32);
                    // var low = 0;
                    // }

                    // return X64Word.create(high, low);
                    // },

                    /**
                     * Shifts this word n bits to the right.
                     *
                     * @param {number} n The number of bits to shift.
                     *
                     * @return {X64Word} A new x64-Word object after shifting.
                     *
                     * @example
                     *
                     *     var shifted = x64Word.shiftR(7);
                     */
                    // shiftR: function (n) {
                    // if (n < 32) {
                    // var low = (this.low >>> n) | (this.high << (32 - n));
                    // var high = this.high >>> n;
                    // } else {
                    // var low = this.high >>> (n - 32);
                    // var high = 0;
                    // }

                    // return X64Word.create(high, low);
                    // },

                    /**
                     * Rotates this word n bits to the left.
                     *
                     * @param {number} n The number of bits to rotate.
                     *
                     * @return {X64Word} A new x64-Word object after rotating.
                     *
                     * @example
                     *
                     *     var rotated = x64Word.rotL(25);
                     */
                    // rotL: function (n) {
                    // return this.shiftL(n).or(this.shiftR(64 - n));
                    // },

                    /**
                     * Rotates this word n bits to the right.
                     *
                     * @param {number} n The number of bits to rotate.
                     *
                     * @return {X64Word} A new x64-Word object after rotating.
                     *
                     * @example
                     *
                     *     var rotated = x64Word.rotR(7);
                     */
                    // rotR: function (n) {
                    // return this.shiftR(n).or(this.shiftL(64 - n));
                    // },

                    /**
                     * Adds this word with the passed word.
                     *
                     * @param {X64Word} word The x64-Word to add with this word.
                     *
                     * @return {X64Word} A new x64-Word object after adding.
                     *
                     * @example
                     *
                     *     var added = x64Word.add(anotherX64Word);
                     */
                    // add: function (word) {
                    // var low = (this.low + word.low) | 0;
                    // var carry = (low >>> 0) < (this.low >>> 0) ? 1 : 0;
                    // var high = (this.high + word.high + carry) | 0;

                    // return X64Word.create(high, low);
                    // }
                });

                /**
                 * An array of 64-bit words.
                 *
                 * @property {Array} words The array of CryptoJS.x64.Word objects.
                 * @property {number} sigBytes The number of significant bytes in this word array.
                 */
                var X64WordArray = C_x64.WordArray = Base.extend({
                    /**
                     * Initializes a newly created word array.
                     *
                     * @param {Array} words (Optional) An array of CryptoJS.x64.Word objects.
                     * @param {number} sigBytes (Optional) The number of significant bytes in the words.
                     *
                     * @example
                     *
                     *     var wordArray = CryptoJS.x64.WordArray.create();
                     *
                     *     var wordArray = CryptoJS.x64.WordArray.create([
                     *         CryptoJS.x64.Word.create(0x00010203, 0x04050607),
                     *         CryptoJS.x64.Word.create(0x18191a1b, 0x1c1d1e1f)
                     *     ]);
                     *
                     *     var wordArray = CryptoJS.x64.WordArray.create([
                     *         CryptoJS.x64.Word.create(0x00010203, 0x04050607),
                     *         CryptoJS.x64.Word.create(0x18191a1b, 0x1c1d1e1f)
                     *     ], 10);
                     */
                    init: function (words, sigBytes) {
                        words = this.words = words || [];

                        if (sigBytes != undefined) {
                            this.sigBytes = sigBytes;
                        } else {
                            this.sigBytes = words.length * 8;
                        }
                    },

                    /**
                     * Converts this 64-bit word array to a 32-bit word array.
                     *
                     * @return {CryptoJS.lib.WordArray} This word array's data as a 32-bit word array.
                     *
                     * @example
                     *
                     *     var x32WordArray = x64WordArray.toX32();
                     */
                    toX32: function () {
                        // Shortcuts
                        var x64Words = this.words;
                        var x64WordsLength = x64Words.length;

                        // Convert
                        var x32Words = [];
                        for (var i = 0; i < x64WordsLength; i++) {
                            var x64Word = x64Words[i];
                            x32Words.push(x64Word.high);
                            x32Words.push(x64Word.low);
                        }

                        return X32WordArray.create(x32Words, this.sigBytes);
                    },

                    /**
                     * Creates a copy of this word array.
                     *
                     * @return {X64WordArray} The clone.
                     *
                     * @example
                     *
                     *     var clone = x64WordArray.clone();
                     */
                    clone: function () {
                        var clone = Base.clone.call(this);

                        // Clone "words" array
                        var words = clone.words = this.words.slice(0);

                        // Clone each X64Word object
                        var wordsLength = words.length;
                        for (var i = 0; i < wordsLength; i++) {
                            words[i] = words[i].clone();
                        }

                        return clone;
                    }
                });
            }());


            (function (Math) {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var WordArray = C_lib.WordArray;
                var Hasher = C_lib.Hasher;
                var C_x64 = C.x64;
                var X64Word = C_x64.Word;
                var C_algo = C.algo;

                // Constants tables
                var RHO_OFFSETS = [];
                var PI_INDEXES  = [];
                var ROUND_CONSTANTS = [];

                // Compute Constants
                (function () {
                    // Compute rho offset constants
                    var x = 1, y = 0;
                    for (var t = 0; t < 24; t++) {
                        RHO_OFFSETS[x + 5 * y] = ((t + 1) * (t + 2) / 2) % 64;

                        var newX = y % 5;
                        var newY = (2 * x + 3 * y) % 5;
                        x = newX;
                        y = newY;
                    }

                    // Compute pi index constants
                    for (var x = 0; x < 5; x++) {
                        for (var y = 0; y < 5; y++) {
                            PI_INDEXES[x + 5 * y] = y + ((2 * x + 3 * y) % 5) * 5;
                        }
                    }

                    // Compute round constants
                    var LFSR = 0x01;
                    for (var i = 0; i < 24; i++) {
                        var roundConstantMsw = 0;
                        var roundConstantLsw = 0;

                        for (var j = 0; j < 7; j++) {
                            if (LFSR & 0x01) {
                                var bitPosition = (1 << j) - 1;
                                if (bitPosition < 32) {
                                    roundConstantLsw ^= 1 << bitPosition;
                                } else /* if (bitPosition >= 32) */ {
                                    roundConstantMsw ^= 1 << (bitPosition - 32);
                                }
                            }

                            // Compute next LFSR
                            if (LFSR & 0x80) {
                                // Primitive polynomial over GF(2): x^8 + x^6 + x^5 + x^4 + 1
                                LFSR = (LFSR << 1) ^ 0x71;
                            } else {
                                LFSR <<= 1;
                            }
                        }

                        ROUND_CONSTANTS[i] = X64Word.create(roundConstantMsw, roundConstantLsw);
                    }
                }());

                // Reusable objects for temporary values
                var T = [];
                (function () {
                    for (var i = 0; i < 25; i++) {
                        T[i] = X64Word.create();
                    }
                }());

                /**
                 * SHA-3 hash algorithm.
                 */
                var SHA3 = C_algo.SHA3 = Hasher.extend({
                    /**
                     * Configuration options.
                     *
                     * @property {number} outputLength
                     *   The desired number of bits in the output hash.
                     *   Only values permitted are: 224, 256, 384, 512.
                     *   Default: 512
                     */
                    cfg: Hasher.cfg.extend({
                        outputLength: 512
                    }),

                    _doReset: function () {
                        var state = this._state = []
                        for (var i = 0; i < 25; i++) {
                            state[i] = new X64Word.init();
                        }

                        this.blockSize = (1600 - 2 * this.cfg.outputLength) / 32;
                    },

                    _doProcessBlock: function (M, offset) {
                        // Shortcuts
                        var state = this._state;
                        var nBlockSizeLanes = this.blockSize / 2;

                        // Absorb
                        for (var i = 0; i < nBlockSizeLanes; i++) {
                            // Shortcuts
                            var M2i  = M[offset + 2 * i];
                            var M2i1 = M[offset + 2 * i + 1];

                            // Swap endian
                            M2i = (
                                (((M2i << 8)  | (M2i >>> 24)) & 0x00ff00ff) |
                                (((M2i << 24) | (M2i >>> 8))  & 0xff00ff00)
                            );
                            M2i1 = (
                                (((M2i1 << 8)  | (M2i1 >>> 24)) & 0x00ff00ff) |
                                (((M2i1 << 24) | (M2i1 >>> 8))  & 0xff00ff00)
                            );

                            // Absorb message into state
                            var lane = state[i];
                            lane.high ^= M2i1;
                            lane.low  ^= M2i;
                        }

                        // Rounds
                        for (var round = 0; round < 24; round++) {
                            // Theta
                            for (var x = 0; x < 5; x++) {
                                // Mix column lanes
                                var tMsw = 0, tLsw = 0;
                                for (var y = 0; y < 5; y++) {
                                    var lane = state[x + 5 * y];
                                    tMsw ^= lane.high;
                                    tLsw ^= lane.low;
                                }

                                // Temporary values
                                var Tx = T[x];
                                Tx.high = tMsw;
                                Tx.low  = tLsw;
                            }
                            for (var x = 0; x < 5; x++) {
                                // Shortcuts
                                var Tx4 = T[(x + 4) % 5];
                                var Tx1 = T[(x + 1) % 5];
                                var Tx1Msw = Tx1.high;
                                var Tx1Lsw = Tx1.low;

                                // Mix surrounding columns
                                var tMsw = Tx4.high ^ ((Tx1Msw << 1) | (Tx1Lsw >>> 31));
                                var tLsw = Tx4.low  ^ ((Tx1Lsw << 1) | (Tx1Msw >>> 31));
                                for (var y = 0; y < 5; y++) {
                                    var lane = state[x + 5 * y];
                                    lane.high ^= tMsw;
                                    lane.low  ^= tLsw;
                                }
                            }

                            // Rho Pi
                            for (var laneIndex = 1; laneIndex < 25; laneIndex++) {
                                var tMsw;
                                var tLsw;

                                // Shortcuts
                                var lane = state[laneIndex];
                                var laneMsw = lane.high;
                                var laneLsw = lane.low;
                                var rhoOffset = RHO_OFFSETS[laneIndex];

                                // Rotate lanes
                                if (rhoOffset < 32) {
                                    tMsw = (laneMsw << rhoOffset) | (laneLsw >>> (32 - rhoOffset));
                                    tLsw = (laneLsw << rhoOffset) | (laneMsw >>> (32 - rhoOffset));
                                } else /* if (rhoOffset >= 32) */ {
                                    tMsw = (laneLsw << (rhoOffset - 32)) | (laneMsw >>> (64 - rhoOffset));
                                    tLsw = (laneMsw << (rhoOffset - 32)) | (laneLsw >>> (64 - rhoOffset));
                                }

                                // Transpose lanes
                                var TPiLane = T[PI_INDEXES[laneIndex]];
                                TPiLane.high = tMsw;
                                TPiLane.low  = tLsw;
                            }

                            // Rho pi at x = y = 0
                            var T0 = T[0];
                            var state0 = state[0];
                            T0.high = state0.high;
                            T0.low  = state0.low;

                            // Chi
                            for (var x = 0; x < 5; x++) {
                                for (var y = 0; y < 5; y++) {
                                    // Shortcuts
                                    var laneIndex = x + 5 * y;
                                    var lane = state[laneIndex];
                                    var TLane = T[laneIndex];
                                    var Tx1Lane = T[((x + 1) % 5) + 5 * y];
                                    var Tx2Lane = T[((x + 2) % 5) + 5 * y];

                                    // Mix rows
                                    lane.high = TLane.high ^ (~Tx1Lane.high & Tx2Lane.high);
                                    lane.low  = TLane.low  ^ (~Tx1Lane.low  & Tx2Lane.low);
                                }
                            }

                            // Iota
                            var lane = state[0];
                            var roundConstant = ROUND_CONSTANTS[round];
                            lane.high ^= roundConstant.high;
                            lane.low  ^= roundConstant.low;
                        }
                    },

                    _doFinalize: function () {
                        // Shortcuts
                        var data = this._data;
                        var dataWords = data.words;
                        var nBitsTotal = this._nDataBytes * 8;
                        var nBitsLeft = data.sigBytes * 8;
                        var blockSizeBits = this.blockSize * 32;

                        // Add padding
                        dataWords[nBitsLeft >>> 5] |= 0x1 << (24 - nBitsLeft % 32);
                        dataWords[((Math.ceil((nBitsLeft + 1) / blockSizeBits) * blockSizeBits) >>> 5) - 1] |= 0x80;
                        data.sigBytes = dataWords.length * 4;

                        // Hash final blocks
                        this._process();

                        // Shortcuts
                        var state = this._state;
                        var outputLengthBytes = this.cfg.outputLength / 8;
                        var outputLengthLanes = outputLengthBytes / 8;

                        // Squeeze
                        var hashWords = [];
                        for (var i = 0; i < outputLengthLanes; i++) {
                            // Shortcuts
                            var lane = state[i];
                            var laneMsw = lane.high;
                            var laneLsw = lane.low;

                            // Swap endian
                            laneMsw = (
                                (((laneMsw << 8)  | (laneMsw >>> 24)) & 0x00ff00ff) |
                                (((laneMsw << 24) | (laneMsw >>> 8))  & 0xff00ff00)
                            );
                            laneLsw = (
                                (((laneLsw << 8)  | (laneLsw >>> 24)) & 0x00ff00ff) |
                                (((laneLsw << 24) | (laneLsw >>> 8))  & 0xff00ff00)
                            );

                            // Squeeze state to retrieve hash
                            hashWords.push(laneLsw);
                            hashWords.push(laneMsw);
                        }

                        // Return final computed hash
                        return new WordArray.init(hashWords, outputLengthBytes);
                    },

                    clone: function () {
                        var clone = Hasher.clone.call(this);

                        var state = clone._state = this._state.slice(0);
                        for (var i = 0; i < 25; i++) {
                            state[i] = state[i].clone();
                        }

                        return clone;
                    }
                });

                /**
                 * Shortcut function to the hasher's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 *
                 * @return {WordArray} The hash.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hash = CryptoJS.SHA3('message');
                 *     var hash = CryptoJS.SHA3(wordArray);
                 */
                C.SHA3 = Hasher._createHelper(SHA3);

                /**
                 * Shortcut function to the HMAC's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 * @param {WordArray|string} key The secret key.
                 *
                 * @return {WordArray} The HMAC.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hmac = CryptoJS.HmacSHA3(message, key);
                 */
                C.HmacSHA3 = Hasher._createHmacHelper(SHA3);
            }(Math));


            (function () {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var Hasher = C_lib.Hasher;
                var C_x64 = C.x64;
                var X64Word = C_x64.Word;
                var X64WordArray = C_x64.WordArray;
                var C_algo = C.algo;

                function X64Word_create() {
                    return X64Word.create.apply(X64Word, arguments);
                }

                // Constants
                var K = [
                    X64Word_create(0x428a2f98, 0xd728ae22), X64Word_create(0x71374491, 0x23ef65cd),
                    X64Word_create(0xb5c0fbcf, 0xec4d3b2f), X64Word_create(0xe9b5dba5, 0x8189dbbc),
                    X64Word_create(0x3956c25b, 0xf348b538), X64Word_create(0x59f111f1, 0xb605d019),
                    X64Word_create(0x923f82a4, 0xaf194f9b), X64Word_create(0xab1c5ed5, 0xda6d8118),
                    X64Word_create(0xd807aa98, 0xa3030242), X64Word_create(0x12835b01, 0x45706fbe),
                    X64Word_create(0x243185be, 0x4ee4b28c), X64Word_create(0x550c7dc3, 0xd5ffb4e2),
                    X64Word_create(0x72be5d74, 0xf27b896f), X64Word_create(0x80deb1fe, 0x3b1696b1),
                    X64Word_create(0x9bdc06a7, 0x25c71235), X64Word_create(0xc19bf174, 0xcf692694),
                    X64Word_create(0xe49b69c1, 0x9ef14ad2), X64Word_create(0xefbe4786, 0x384f25e3),
                    X64Word_create(0x0fc19dc6, 0x8b8cd5b5), X64Word_create(0x240ca1cc, 0x77ac9c65),
                    X64Word_create(0x2de92c6f, 0x592b0275), X64Word_create(0x4a7484aa, 0x6ea6e483),
                    X64Word_create(0x5cb0a9dc, 0xbd41fbd4), X64Word_create(0x76f988da, 0x831153b5),
                    X64Word_create(0x983e5152, 0xee66dfab), X64Word_create(0xa831c66d, 0x2db43210),
                    X64Word_create(0xb00327c8, 0x98fb213f), X64Word_create(0xbf597fc7, 0xbeef0ee4),
                    X64Word_create(0xc6e00bf3, 0x3da88fc2), X64Word_create(0xd5a79147, 0x930aa725),
                    X64Word_create(0x06ca6351, 0xe003826f), X64Word_create(0x14292967, 0x0a0e6e70),
                    X64Word_create(0x27b70a85, 0x46d22ffc), X64Word_create(0x2e1b2138, 0x5c26c926),
                    X64Word_create(0x4d2c6dfc, 0x5ac42aed), X64Word_create(0x53380d13, 0x9d95b3df),
                    X64Word_create(0x650a7354, 0x8baf63de), X64Word_create(0x766a0abb, 0x3c77b2a8),
                    X64Word_create(0x81c2c92e, 0x47edaee6), X64Word_create(0x92722c85, 0x1482353b),
                    X64Word_create(0xa2bfe8a1, 0x4cf10364), X64Word_create(0xa81a664b, 0xbc423001),
                    X64Word_create(0xc24b8b70, 0xd0f89791), X64Word_create(0xc76c51a3, 0x0654be30),
                    X64Word_create(0xd192e819, 0xd6ef5218), X64Word_create(0xd6990624, 0x5565a910),
                    X64Word_create(0xf40e3585, 0x5771202a), X64Word_create(0x106aa070, 0x32bbd1b8),
                    X64Word_create(0x19a4c116, 0xb8d2d0c8), X64Word_create(0x1e376c08, 0x5141ab53),
                    X64Word_create(0x2748774c, 0xdf8eeb99), X64Word_create(0x34b0bcb5, 0xe19b48a8),
                    X64Word_create(0x391c0cb3, 0xc5c95a63), X64Word_create(0x4ed8aa4a, 0xe3418acb),
                    X64Word_create(0x5b9cca4f, 0x7763e373), X64Word_create(0x682e6ff3, 0xd6b2b8a3),
                    X64Word_create(0x748f82ee, 0x5defb2fc), X64Word_create(0x78a5636f, 0x43172f60),
                    X64Word_create(0x84c87814, 0xa1f0ab72), X64Word_create(0x8cc70208, 0x1a6439ec),
                    X64Word_create(0x90befffa, 0x23631e28), X64Word_create(0xa4506ceb, 0xde82bde9),
                    X64Word_create(0xbef9a3f7, 0xb2c67915), X64Word_create(0xc67178f2, 0xe372532b),
                    X64Word_create(0xca273ece, 0xea26619c), X64Word_create(0xd186b8c7, 0x21c0c207),
                    X64Word_create(0xeada7dd6, 0xcde0eb1e), X64Word_create(0xf57d4f7f, 0xee6ed178),
                    X64Word_create(0x06f067aa, 0x72176fba), X64Word_create(0x0a637dc5, 0xa2c898a6),
                    X64Word_create(0x113f9804, 0xbef90dae), X64Word_create(0x1b710b35, 0x131c471b),
                    X64Word_create(0x28db77f5, 0x23047d84), X64Word_create(0x32caab7b, 0x40c72493),
                    X64Word_create(0x3c9ebe0a, 0x15c9bebc), X64Word_create(0x431d67c4, 0x9c100d4c),
                    X64Word_create(0x4cc5d4be, 0xcb3e42b6), X64Word_create(0x597f299c, 0xfc657e2a),
                    X64Word_create(0x5fcb6fab, 0x3ad6faec), X64Word_create(0x6c44198c, 0x4a475817)
                ];

                // Reusable objects
                var W = [];
                (function () {
                    for (var i = 0; i < 80; i++) {
                        W[i] = X64Word_create();
                    }
                }());

                /**
                 * SHA-512 hash algorithm.
                 */
                var SHA512 = C_algo.SHA512 = Hasher.extend({
                    _doReset: function () {
                        this._hash = new X64WordArray.init([
                            new X64Word.init(0x6a09e667, 0xf3bcc908), new X64Word.init(0xbb67ae85, 0x84caa73b),
                            new X64Word.init(0x3c6ef372, 0xfe94f82b), new X64Word.init(0xa54ff53a, 0x5f1d36f1),
                            new X64Word.init(0x510e527f, 0xade682d1), new X64Word.init(0x9b05688c, 0x2b3e6c1f),
                            new X64Word.init(0x1f83d9ab, 0xfb41bd6b), new X64Word.init(0x5be0cd19, 0x137e2179)
                        ]);
                    },

                    _doProcessBlock: function (M, offset) {
                        // Shortcuts
                        var H = this._hash.words;

                        var H0 = H[0];
                        var H1 = H[1];
                        var H2 = H[2];
                        var H3 = H[3];
                        var H4 = H[4];
                        var H5 = H[5];
                        var H6 = H[6];
                        var H7 = H[7];

                        var H0h = H0.high;
                        var H0l = H0.low;
                        var H1h = H1.high;
                        var H1l = H1.low;
                        var H2h = H2.high;
                        var H2l = H2.low;
                        var H3h = H3.high;
                        var H3l = H3.low;
                        var H4h = H4.high;
                        var H4l = H4.low;
                        var H5h = H5.high;
                        var H5l = H5.low;
                        var H6h = H6.high;
                        var H6l = H6.low;
                        var H7h = H7.high;
                        var H7l = H7.low;

                        // Working variables
                        var ah = H0h;
                        var al = H0l;
                        var bh = H1h;
                        var bl = H1l;
                        var ch = H2h;
                        var cl = H2l;
                        var dh = H3h;
                        var dl = H3l;
                        var eh = H4h;
                        var el = H4l;
                        var fh = H5h;
                        var fl = H5l;
                        var gh = H6h;
                        var gl = H6l;
                        var hh = H7h;
                        var hl = H7l;

                        // Rounds
                        for (var i = 0; i < 80; i++) {
                            var Wil;
                            var Wih;

                            // Shortcut
                            var Wi = W[i];

                            // Extend message
                            if (i < 16) {
                                Wih = Wi.high = M[offset + i * 2]     | 0;
                                Wil = Wi.low  = M[offset + i * 2 + 1] | 0;
                            } else {
                                // Gamma0
                                var gamma0x  = W[i - 15];
                                var gamma0xh = gamma0x.high;
                                var gamma0xl = gamma0x.low;
                                var gamma0h  = ((gamma0xh >>> 1) | (gamma0xl << 31)) ^ ((gamma0xh >>> 8) | (gamma0xl << 24)) ^ (gamma0xh >>> 7);
                                var gamma0l  = ((gamma0xl >>> 1) | (gamma0xh << 31)) ^ ((gamma0xl >>> 8) | (gamma0xh << 24)) ^ ((gamma0xl >>> 7) | (gamma0xh << 25));

                                // Gamma1
                                var gamma1x  = W[i - 2];
                                var gamma1xh = gamma1x.high;
                                var gamma1xl = gamma1x.low;
                                var gamma1h  = ((gamma1xh >>> 19) | (gamma1xl << 13)) ^ ((gamma1xh << 3) | (gamma1xl >>> 29)) ^ (gamma1xh >>> 6);
                                var gamma1l  = ((gamma1xl >>> 19) | (gamma1xh << 13)) ^ ((gamma1xl << 3) | (gamma1xh >>> 29)) ^ ((gamma1xl >>> 6) | (gamma1xh << 26));

                                // W[i] = gamma0 + W[i - 7] + gamma1 + W[i - 16]
                                var Wi7  = W[i - 7];
                                var Wi7h = Wi7.high;
                                var Wi7l = Wi7.low;

                                var Wi16  = W[i - 16];
                                var Wi16h = Wi16.high;
                                var Wi16l = Wi16.low;

                                Wil = gamma0l + Wi7l;
                                Wih = gamma0h + Wi7h + ((Wil >>> 0) < (gamma0l >>> 0) ? 1 : 0);
                                Wil = Wil + gamma1l;
                                Wih = Wih + gamma1h + ((Wil >>> 0) < (gamma1l >>> 0) ? 1 : 0);
                                Wil = Wil + Wi16l;
                                Wih = Wih + Wi16h + ((Wil >>> 0) < (Wi16l >>> 0) ? 1 : 0);

                                Wi.high = Wih;
                                Wi.low  = Wil;
                            }

                            var chh  = (eh & fh) ^ (~eh & gh);
                            var chl  = (el & fl) ^ (~el & gl);
                            var majh = (ah & bh) ^ (ah & ch) ^ (bh & ch);
                            var majl = (al & bl) ^ (al & cl) ^ (bl & cl);

                            var sigma0h = ((ah >>> 28) | (al << 4))  ^ ((ah << 30)  | (al >>> 2)) ^ ((ah << 25) | (al >>> 7));
                            var sigma0l = ((al >>> 28) | (ah << 4))  ^ ((al << 30)  | (ah >>> 2)) ^ ((al << 25) | (ah >>> 7));
                            var sigma1h = ((eh >>> 14) | (el << 18)) ^ ((eh >>> 18) | (el << 14)) ^ ((eh << 23) | (el >>> 9));
                            var sigma1l = ((el >>> 14) | (eh << 18)) ^ ((el >>> 18) | (eh << 14)) ^ ((el << 23) | (eh >>> 9));

                            // t1 = h + sigma1 + ch + K[i] + W[i]
                            var Ki  = K[i];
                            var Kih = Ki.high;
                            var Kil = Ki.low;

                            var t1l = hl + sigma1l;
                            var t1h = hh + sigma1h + ((t1l >>> 0) < (hl >>> 0) ? 1 : 0);
                            var t1l = t1l + chl;
                            var t1h = t1h + chh + ((t1l >>> 0) < (chl >>> 0) ? 1 : 0);
                            var t1l = t1l + Kil;
                            var t1h = t1h + Kih + ((t1l >>> 0) < (Kil >>> 0) ? 1 : 0);
                            var t1l = t1l + Wil;
                            var t1h = t1h + Wih + ((t1l >>> 0) < (Wil >>> 0) ? 1 : 0);

                            // t2 = sigma0 + maj
                            var t2l = sigma0l + majl;
                            var t2h = sigma0h + majh + ((t2l >>> 0) < (sigma0l >>> 0) ? 1 : 0);

                            // Update working variables
                            hh = gh;
                            hl = gl;
                            gh = fh;
                            gl = fl;
                            fh = eh;
                            fl = el;
                            el = (dl + t1l) | 0;
                            eh = (dh + t1h + ((el >>> 0) < (dl >>> 0) ? 1 : 0)) | 0;
                            dh = ch;
                            dl = cl;
                            ch = bh;
                            cl = bl;
                            bh = ah;
                            bl = al;
                            al = (t1l + t2l) | 0;
                            ah = (t1h + t2h + ((al >>> 0) < (t1l >>> 0) ? 1 : 0)) | 0;
                        }

                        // Intermediate hash value
                        H0l = H0.low  = (H0l + al);
                        H0.high = (H0h + ah + ((H0l >>> 0) < (al >>> 0) ? 1 : 0));
                        H1l = H1.low  = (H1l + bl);
                        H1.high = (H1h + bh + ((H1l >>> 0) < (bl >>> 0) ? 1 : 0));
                        H2l = H2.low  = (H2l + cl);
                        H2.high = (H2h + ch + ((H2l >>> 0) < (cl >>> 0) ? 1 : 0));
                        H3l = H3.low  = (H3l + dl);
                        H3.high = (H3h + dh + ((H3l >>> 0) < (dl >>> 0) ? 1 : 0));
                        H4l = H4.low  = (H4l + el);
                        H4.high = (H4h + eh + ((H4l >>> 0) < (el >>> 0) ? 1 : 0));
                        H5l = H5.low  = (H5l + fl);
                        H5.high = (H5h + fh + ((H5l >>> 0) < (fl >>> 0) ? 1 : 0));
                        H6l = H6.low  = (H6l + gl);
                        H6.high = (H6h + gh + ((H6l >>> 0) < (gl >>> 0) ? 1 : 0));
                        H7l = H7.low  = (H7l + hl);
                        H7.high = (H7h + hh + ((H7l >>> 0) < (hl >>> 0) ? 1 : 0));
                    },

                    _doFinalize: function () {
                        // Shortcuts
                        var data = this._data;
                        var dataWords = data.words;

                        var nBitsTotal = this._nDataBytes * 8;
                        var nBitsLeft = data.sigBytes * 8;

                        // Add padding
                        dataWords[nBitsLeft >>> 5] |= 0x80 << (24 - nBitsLeft % 32);
                        dataWords[(((nBitsLeft + 128) >>> 10) << 5) + 30] = Math.floor(nBitsTotal / 0x100000000);
                        dataWords[(((nBitsLeft + 128) >>> 10) << 5) + 31] = nBitsTotal;
                        data.sigBytes = dataWords.length * 4;

                        // Hash final blocks
                        this._process();

                        // Convert hash to 32-bit word array before returning
                        var hash = this._hash.toX32();

                        // Return final computed hash
                        return hash;
                    },

                    clone: function () {
                        var clone = Hasher.clone.call(this);
                        clone._hash = this._hash.clone();

                        return clone;
                    },

                    blockSize: 1024/32
                });

                /**
                 * Shortcut function to the hasher's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 *
                 * @return {WordArray} The hash.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hash = CryptoJS.SHA512('message');
                 *     var hash = CryptoJS.SHA512(wordArray);
                 */
                C.SHA512 = Hasher._createHelper(SHA512);

                /**
                 * Shortcut function to the HMAC's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 * @param {WordArray|string} key The secret key.
                 *
                 * @return {WordArray} The HMAC.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hmac = CryptoJS.HmacSHA512(message, key);
                 */
                C.HmacSHA512 = Hasher._createHmacHelper(SHA512);
            }());


            (function () {
                // Shortcuts
                var C = CryptoJS;
                var C_x64 = C.x64;
                var X64Word = C_x64.Word;
                var X64WordArray = C_x64.WordArray;
                var C_algo = C.algo;
                var SHA512 = C_algo.SHA512;

                /**
                 * SHA-384 hash algorithm.
                 */
                var SHA384 = C_algo.SHA384 = SHA512.extend({
                    _doReset: function () {
                        this._hash = new X64WordArray.init([
                            new X64Word.init(0xcbbb9d5d, 0xc1059ed8), new X64Word.init(0x629a292a, 0x367cd507),
                            new X64Word.init(0x9159015a, 0x3070dd17), new X64Word.init(0x152fecd8, 0xf70e5939),
                            new X64Word.init(0x67332667, 0xffc00b31), new X64Word.init(0x8eb44a87, 0x68581511),
                            new X64Word.init(0xdb0c2e0d, 0x64f98fa7), new X64Word.init(0x47b5481d, 0xbefa4fa4)
                        ]);
                    },

                    _doFinalize: function () {
                        var hash = SHA512._doFinalize.call(this);

                        hash.sigBytes -= 16;

                        return hash;
                    }
                });

                /**
                 * Shortcut function to the hasher's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 *
                 * @return {WordArray} The hash.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hash = CryptoJS.SHA384('message');
                 *     var hash = CryptoJS.SHA384(wordArray);
                 */
                C.SHA384 = SHA512._createHelper(SHA384);

                /**
                 * Shortcut function to the HMAC's object interface.
                 *
                 * @param {WordArray|string} message The message to hash.
                 * @param {WordArray|string} key The secret key.
                 *
                 * @return {WordArray} The HMAC.
                 *
                 * @static
                 *
                 * @example
                 *
                 *     var hmac = CryptoJS.HmacSHA384(message, key);
                 */
                C.HmacSHA384 = SHA512._createHmacHelper(SHA384);
            }());


            /**
             * Cipher core components.
             */
            CryptoJS.lib.Cipher || (function (undefined) {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var Base = C_lib.Base;
                var WordArray = C_lib.WordArray;
                var BufferedBlockAlgorithm = C_lib.BufferedBlockAlgorithm;
                var C_enc = C.enc;
                var Utf8 = C_enc.Utf8;
                var Base64 = C_enc.Base64;
                var C_algo = C.algo;
                var EvpKDF = C_algo.EvpKDF;

                /**
                 * Abstract base cipher template.
                 *
                 * @property {number} keySize This cipher's key size. Default: 4 (128 bits)
                 * @property {number} ivSize This cipher's IV size. Default: 4 (128 bits)
                 * @property {number} _ENC_XFORM_MODE A constant representing encryption mode.
                 * @property {number} _DEC_XFORM_MODE A constant representing decryption mode.
                 */
                var Cipher = C_lib.Cipher = BufferedBlockAlgorithm.extend({
                    /**
                     * Configuration options.
                     *
                     * @property {WordArray} iv The IV to use for this operation.
                     */
                    cfg: Base.extend(),

                    /**
                     * Creates this cipher in encryption mode.
                     *
                     * @param {WordArray} key The key.
                     * @param {Object} cfg (Optional) The configuration options to use for this operation.
                     *
                     * @return {Cipher} A cipher instance.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var cipher = CryptoJS.algo.AES.createEncryptor(keyWordArray, { iv: ivWordArray });
                     */
                    createEncryptor: function (key, cfg) {
                        return this.create(this._ENC_XFORM_MODE, key, cfg);
                    },

                    /**
                     * Creates this cipher in decryption mode.
                     *
                     * @param {WordArray} key The key.
                     * @param {Object} cfg (Optional) The configuration options to use for this operation.
                     *
                     * @return {Cipher} A cipher instance.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var cipher = CryptoJS.algo.AES.createDecryptor(keyWordArray, { iv: ivWordArray });
                     */
                    createDecryptor: function (key, cfg) {
                        return this.create(this._DEC_XFORM_MODE, key, cfg);
                    },

                    /**
                     * Initializes a newly created cipher.
                     *
                     * @param {number} xformMode Either the encryption or decryption transormation mode constant.
                     * @param {WordArray} key The key.
                     * @param {Object} cfg (Optional) The configuration options to use for this operation.
                     *
                     * @example
                     *
                     *     var cipher = CryptoJS.algo.AES.create(CryptoJS.algo.AES._ENC_XFORM_MODE, keyWordArray, { iv: ivWordArray });
                     */
                    init: function (xformMode, key, cfg) {
                        // Apply config defaults
                        this.cfg = this.cfg.extend(cfg);

                        // Store transform mode and key
                        this._xformMode = xformMode;
                        this._key = key;

                        // Set initial values
                        this.reset();
                    },

                    /**
                     * Resets this cipher to its initial state.
                     *
                     * @example
                     *
                     *     cipher.reset();
                     */
                    reset: function () {
                        // Reset data buffer
                        BufferedBlockAlgorithm.reset.call(this);

                        // Perform concrete-cipher logic
                        this._doReset();
                    },

                    /**
                     * Adds data to be encrypted or decrypted.
                     *
                     * @param {WordArray|string} dataUpdate The data to encrypt or decrypt.
                     *
                     * @return {WordArray} The data after processing.
                     *
                     * @example
                     *
                     *     var encrypted = cipher.process('data');
                     *     var encrypted = cipher.process(wordArray);
                     */
                    process: function (dataUpdate) {
                        // Append
                        this._append(dataUpdate);

                        // Process available blocks
                        return this._process();
                    },

                    /**
                     * Finalizes the encryption or decryption process.
                     * Note that the finalize operation is effectively a destructive, read-once operation.
                     *
                     * @param {WordArray|string} dataUpdate The final data to encrypt or decrypt.
                     *
                     * @return {WordArray} The data after final processing.
                     *
                     * @example
                     *
                     *     var encrypted = cipher.finalize();
                     *     var encrypted = cipher.finalize('data');
                     *     var encrypted = cipher.finalize(wordArray);
                     */
                    finalize: function (dataUpdate) {
                        // Final data update
                        if (dataUpdate) {
                            this._append(dataUpdate);
                        }

                        // Perform concrete-cipher logic
                        var finalProcessedData = this._doFinalize();

                        return finalProcessedData;
                    },

                    keySize: 128/32,

                    ivSize: 128/32,

                    _ENC_XFORM_MODE: 1,

                    _DEC_XFORM_MODE: 2,

                    /**
                     * Creates shortcut functions to a cipher's object interface.
                     *
                     * @param {Cipher} cipher The cipher to create a helper for.
                     *
                     * @return {Object} An object with encrypt and decrypt shortcut functions.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var AES = CryptoJS.lib.Cipher._createHelper(CryptoJS.algo.AES);
                     */
                    _createHelper: (function () {
                        function selectCipherStrategy(key) {
                            if (typeof key == 'string') {
                                return PasswordBasedCipher;
                            } else {
                                return SerializableCipher;
                            }
                        }

                        return function (cipher) {
                            return {
                                encrypt: function (message, key, cfg) {
                                    return selectCipherStrategy(key).encrypt(cipher, message, key, cfg);
                                },

                                decrypt: function (ciphertext, key, cfg) {
                                    return selectCipherStrategy(key).decrypt(cipher, ciphertext, key, cfg);
                                }
                            };
                        };
                    }())
                });

                /**
                 * Abstract base stream cipher template.
                 *
                 * @property {number} blockSize The number of 32-bit words this cipher operates on. Default: 1 (32 bits)
                 */
                var StreamCipher = C_lib.StreamCipher = Cipher.extend({
                    _doFinalize: function () {
                        // Process partial blocks
                        var finalProcessedBlocks = this._process(!!'flush');

                        return finalProcessedBlocks;
                    },

                    blockSize: 1
                });

                /**
                 * Mode namespace.
                 */
                var C_mode = C.mode = {};

                /**
                 * Abstract base block cipher mode template.
                 */
                var BlockCipherMode = C_lib.BlockCipherMode = Base.extend({
                    /**
                     * Creates this mode for encryption.
                     *
                     * @param {Cipher} cipher A block cipher instance.
                     * @param {Array} iv The IV words.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var mode = CryptoJS.mode.CBC.createEncryptor(cipher, iv.words);
                     */
                    createEncryptor: function (cipher, iv) {
                        return this.Encryptor.create(cipher, iv);
                    },

                    /**
                     * Creates this mode for decryption.
                     *
                     * @param {Cipher} cipher A block cipher instance.
                     * @param {Array} iv The IV words.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var mode = CryptoJS.mode.CBC.createDecryptor(cipher, iv.words);
                     */
                    createDecryptor: function (cipher, iv) {
                        return this.Decryptor.create(cipher, iv);
                    },

                    /**
                     * Initializes a newly created mode.
                     *
                     * @param {Cipher} cipher A block cipher instance.
                     * @param {Array} iv The IV words.
                     *
                     * @example
                     *
                     *     var mode = CryptoJS.mode.CBC.Encryptor.create(cipher, iv.words);
                     */
                    init: function (cipher, iv) {
                        this._cipher = cipher;
                        this._iv = iv;
                    }
                });

                /**
                 * Cipher Block Chaining mode.
                 */
                var CBC = C_mode.CBC = (function () {
                    /**
                     * Abstract base CBC mode.
                     */
                    var CBC = BlockCipherMode.extend();

                    /**
                     * CBC encryptor.
                     */
                    CBC.Encryptor = CBC.extend({
                        /**
                         * Processes the data block at offset.
                         *
                         * @param {Array} words The data words to operate on.
                         * @param {number} offset The offset where the block starts.
                         *
                         * @example
                         *
                         *     mode.processBlock(data.words, offset);
                         */
                        processBlock: function (words, offset) {
                            // Shortcuts
                            var cipher = this._cipher;
                            var blockSize = cipher.blockSize;

                            // XOR and encrypt
                            xorBlock.call(this, words, offset, blockSize);
                            cipher.encryptBlock(words, offset);

                            // Remember this block to use with next block
                            this._prevBlock = words.slice(offset, offset + blockSize);
                        }
                    });

                    /**
                     * CBC decryptor.
                     */
                    CBC.Decryptor = CBC.extend({
                        /**
                         * Processes the data block at offset.
                         *
                         * @param {Array} words The data words to operate on.
                         * @param {number} offset The offset where the block starts.
                         *
                         * @example
                         *
                         *     mode.processBlock(data.words, offset);
                         */
                        processBlock: function (words, offset) {
                            // Shortcuts
                            var cipher = this._cipher;
                            var blockSize = cipher.blockSize;

                            // Remember this block to use with next block
                            var thisBlock = words.slice(offset, offset + blockSize);

                            // Decrypt and XOR
                            cipher.decryptBlock(words, offset);
                            xorBlock.call(this, words, offset, blockSize);

                            // This block becomes the previous block
                            this._prevBlock = thisBlock;
                        }
                    });

                    function xorBlock(words, offset, blockSize) {
                        var block;

                        // Shortcut
                        var iv = this._iv;

                        // Choose mixing block
                        if (iv) {
                            block = iv;

                            // Remove IV for subsequent blocks
                            this._iv = undefined;
                        } else {
                            block = this._prevBlock;
                        }

                        // XOR blocks
                        for (var i = 0; i < blockSize; i++) {
                            words[offset + i] ^= block[i];
                        }
                    }

                    return CBC;
                }());

                /**
                 * Padding namespace.
                 */
                var C_pad = C.pad = {};

                /**
                 * PKCS #5/7 padding strategy.
                 */
                var Pkcs7 = C_pad.Pkcs7 = {
                    /**
                     * Pads data using the algorithm defined in PKCS #5/7.
                     *
                     * @param {WordArray} data The data to pad.
                     * @param {number} blockSize The multiple that the data should be padded to.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     CryptoJS.pad.Pkcs7.pad(wordArray, 4);
                     */
                    pad: function (data, blockSize) {
                        // Shortcut
                        var blockSizeBytes = blockSize * 4;

                        // Count padding bytes
                        var nPaddingBytes = blockSizeBytes - data.sigBytes % blockSizeBytes;

                        // Create padding word
                        var paddingWord = (nPaddingBytes << 24) | (nPaddingBytes << 16) | (nPaddingBytes << 8) | nPaddingBytes;

                        // Create padding
                        var paddingWords = [];
                        for (var i = 0; i < nPaddingBytes; i += 4) {
                            paddingWords.push(paddingWord);
                        }
                        var padding = WordArray.create(paddingWords, nPaddingBytes);

                        // Add padding
                        data.concat(padding);
                    },

                    /**
                     * Unpads data that had been padded using the algorithm defined in PKCS #5/7.
                     *
                     * @param {WordArray} data The data to unpad.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     CryptoJS.pad.Pkcs7.unpad(wordArray);
                     */
                    unpad: function (data) {
                        // Get number of padding bytes from last byte
                        var nPaddingBytes = data.words[(data.sigBytes - 1) >>> 2] & 0xff;

                        // Remove padding
                        data.sigBytes -= nPaddingBytes;
                    }
                };

                /**
                 * Abstract base block cipher template.
                 *
                 * @property {number} blockSize The number of 32-bit words this cipher operates on. Default: 4 (128 bits)
                 */
                var BlockCipher = C_lib.BlockCipher = Cipher.extend({
                    /**
                     * Configuration options.
                     *
                     * @property {Mode} mode The block mode to use. Default: CBC
                     * @property {Padding} padding The padding strategy to use. Default: Pkcs7
                     */
                    cfg: Cipher.cfg.extend({
                        mode: CBC,
                        padding: Pkcs7
                    }),

                    reset: function () {
                        var modeCreator;

                        // Reset cipher
                        Cipher.reset.call(this);

                        // Shortcuts
                        var cfg = this.cfg;
                        var iv = cfg.iv;
                        var mode = cfg.mode;

                        // Reset block mode
                        if (this._xformMode == this._ENC_XFORM_MODE) {
                            modeCreator = mode.createEncryptor;
                        } else /* if (this._xformMode == this._DEC_XFORM_MODE) */ {
                            modeCreator = mode.createDecryptor;
                            // Keep at least one block in the buffer for unpadding
                            this._minBufferSize = 1;
                        }

                        if (this._mode && this._mode.__creator == modeCreator) {
                            this._mode.init(this, iv && iv.words);
                        } else {
                            this._mode = modeCreator.call(mode, this, iv && iv.words);
                            this._mode.__creator = modeCreator;
                        }
                    },

                    _doProcessBlock: function (words, offset) {
                        this._mode.processBlock(words, offset);
                    },

                    _doFinalize: function () {
                        var finalProcessedBlocks;

                        // Shortcut
                        var padding = this.cfg.padding;

                        // Finalize
                        if (this._xformMode == this._ENC_XFORM_MODE) {
                            // Pad data
                            padding.pad(this._data, this.blockSize);

                            // Process final blocks
                            finalProcessedBlocks = this._process(!!'flush');
                        } else /* if (this._xformMode == this._DEC_XFORM_MODE) */ {
                            // Process final blocks
                            finalProcessedBlocks = this._process(!!'flush');

                            // Unpad data
                            padding.unpad(finalProcessedBlocks);
                        }

                        return finalProcessedBlocks;
                    },

                    blockSize: 128/32
                });

                /**
                 * A collection of cipher parameters.
                 *
                 * @property {WordArray} ciphertext The raw ciphertext.
                 * @property {WordArray} key The key to this ciphertext.
                 * @property {WordArray} iv The IV used in the ciphering operation.
                 * @property {WordArray} salt The salt used with a key derivation function.
                 * @property {Cipher} algorithm The cipher algorithm.
                 * @property {Mode} mode The block mode used in the ciphering operation.
                 * @property {Padding} padding The padding scheme used in the ciphering operation.
                 * @property {number} blockSize The block size of the cipher.
                 * @property {Format} formatter The default formatting strategy to convert this cipher params object to a string.
                 */
                var CipherParams = C_lib.CipherParams = Base.extend({
                    /**
                     * Initializes a newly created cipher params object.
                     *
                     * @param {Object} cipherParams An object with any of the possible cipher parameters.
                     *
                     * @example
                     *
                     *     var cipherParams = CryptoJS.lib.CipherParams.create({
                     *         ciphertext: ciphertextWordArray,
                     *         key: keyWordArray,
                     *         iv: ivWordArray,
                     *         salt: saltWordArray,
                     *         algorithm: CryptoJS.algo.AES,
                     *         mode: CryptoJS.mode.CBC,
                     *         padding: CryptoJS.pad.PKCS7,
                     *         blockSize: 4,
                     *         formatter: CryptoJS.format.OpenSSL
                     *     });
                     */
                    init: function (cipherParams) {
                        this.mixIn(cipherParams);
                    },

                    /**
                     * Converts this cipher params object to a string.
                     *
                     * @param {Format} formatter (Optional) The formatting strategy to use.
                     *
                     * @return {string} The stringified cipher params.
                     *
                     * @throws Error If neither the formatter nor the default formatter is set.
                     *
                     * @example
                     *
                     *     var string = cipherParams + '';
                     *     var string = cipherParams.toString();
                     *     var string = cipherParams.toString(CryptoJS.format.OpenSSL);
                     */
                    toString: function (formatter) {
                        return (formatter || this.formatter).stringify(this);
                    }
                });

                /**
                 * Format namespace.
                 */
                var C_format = C.format = {};

                /**
                 * OpenSSL formatting strategy.
                 */
                var OpenSSLFormatter = C_format.OpenSSL = {
                    /**
                     * Converts a cipher params object to an OpenSSL-compatible string.
                     *
                     * @param {CipherParams} cipherParams The cipher params object.
                     *
                     * @return {string} The OpenSSL-compatible string.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var openSSLString = CryptoJS.format.OpenSSL.stringify(cipherParams);
                     */
                    stringify: function (cipherParams) {
                        var wordArray;

                        // Shortcuts
                        var ciphertext = cipherParams.ciphertext;
                        var salt = cipherParams.salt;

                        // Format
                        if (salt) {
                            wordArray = WordArray.create([0x53616c74, 0x65645f5f]).concat(salt).concat(ciphertext);
                        } else {
                            wordArray = ciphertext;
                        }

                        return wordArray.toString(Base64);
                    },

                    /**
                     * Converts an OpenSSL-compatible string to a cipher params object.
                     *
                     * @param {string} openSSLStr The OpenSSL-compatible string.
                     *
                     * @return {CipherParams} The cipher params object.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var cipherParams = CryptoJS.format.OpenSSL.parse(openSSLString);
                     */
                    parse: function (openSSLStr) {
                        var salt;

                        // Parse base64
                        var ciphertext = Base64.parse(openSSLStr);

                        // Shortcut
                        var ciphertextWords = ciphertext.words;

                        // Test for salt
                        if (ciphertextWords[0] == 0x53616c74 && ciphertextWords[1] == 0x65645f5f) {
                            // Extract salt
                            salt = WordArray.create(ciphertextWords.slice(2, 4));

                            // Remove salt from ciphertext
                            ciphertextWords.splice(0, 4);
                            ciphertext.sigBytes -= 16;
                        }

                        return CipherParams.create({ ciphertext: ciphertext, salt: salt });
                    }
                };

                /**
                 * A cipher wrapper that returns ciphertext as a serializable cipher params object.
                 */
                var SerializableCipher = C_lib.SerializableCipher = Base.extend({
                    /**
                     * Configuration options.
                     *
                     * @property {Formatter} format The formatting strategy to convert cipher param objects to and from a string. Default: OpenSSL
                     */
                    cfg: Base.extend({
                        format: OpenSSLFormatter
                    }),

                    /**
                     * Encrypts a message.
                     *
                     * @param {Cipher} cipher The cipher algorithm to use.
                     * @param {WordArray|string} message The message to encrypt.
                     * @param {WordArray} key The key.
                     * @param {Object} cfg (Optional) The configuration options to use for this operation.
                     *
                     * @return {CipherParams} A cipher params object.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var ciphertextParams = CryptoJS.lib.SerializableCipher.encrypt(CryptoJS.algo.AES, message, key);
                     *     var ciphertextParams = CryptoJS.lib.SerializableCipher.encrypt(CryptoJS.algo.AES, message, key, { iv: iv });
                     *     var ciphertextParams = CryptoJS.lib.SerializableCipher.encrypt(CryptoJS.algo.AES, message, key, { iv: iv, format: CryptoJS.format.OpenSSL });
                     */
                    encrypt: function (cipher, message, key, cfg) {
                        // Apply config defaults
                        cfg = this.cfg.extend(cfg);

                        // Encrypt
                        var encryptor = cipher.createEncryptor(key, cfg);
                        var ciphertext = encryptor.finalize(message);

                        // Shortcut
                        var cipherCfg = encryptor.cfg;

                        // Create and return serializable cipher params
                        return CipherParams.create({
                            ciphertext: ciphertext,
                            key: key,
                            iv: cipherCfg.iv,
                            algorithm: cipher,
                            mode: cipherCfg.mode,
                            padding: cipherCfg.padding,
                            blockSize: cipher.blockSize,
                            formatter: cfg.format
                        });
                    },

                    /**
                     * Decrypts serialized ciphertext.
                     *
                     * @param {Cipher} cipher The cipher algorithm to use.
                     * @param {CipherParams|string} ciphertext The ciphertext to decrypt.
                     * @param {WordArray} key The key.
                     * @param {Object} cfg (Optional) The configuration options to use for this operation.
                     *
                     * @return {WordArray} The plaintext.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var plaintext = CryptoJS.lib.SerializableCipher.decrypt(CryptoJS.algo.AES, formattedCiphertext, key, { iv: iv, format: CryptoJS.format.OpenSSL });
                     *     var plaintext = CryptoJS.lib.SerializableCipher.decrypt(CryptoJS.algo.AES, ciphertextParams, key, { iv: iv, format: CryptoJS.format.OpenSSL });
                     */
                    decrypt: function (cipher, ciphertext, key, cfg) {
                        // Apply config defaults
                        cfg = this.cfg.extend(cfg);

                        // Convert string to CipherParams
                        ciphertext = this._parse(ciphertext, cfg.format);

                        // Decrypt
                        var plaintext = cipher.createDecryptor(key, cfg).finalize(ciphertext.ciphertext);

                        return plaintext;
                    },

                    /**
                     * Converts serialized ciphertext to CipherParams,
                     * else assumed CipherParams already and returns ciphertext unchanged.
                     *
                     * @param {CipherParams|string} ciphertext The ciphertext.
                     * @param {Formatter} format The formatting strategy to use to parse serialized ciphertext.
                     *
                     * @return {CipherParams} The unserialized ciphertext.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var ciphertextParams = CryptoJS.lib.SerializableCipher._parse(ciphertextStringOrParams, format);
                     */
                    _parse: function (ciphertext, format) {
                        if (typeof ciphertext == 'string') {
                            return format.parse(ciphertext, this);
                        } else {
                            return ciphertext;
                        }
                    }
                });

                /**
                 * Key derivation function namespace.
                 */
                var C_kdf = C.kdf = {};

                /**
                 * OpenSSL key derivation function.
                 */
                var OpenSSLKdf = C_kdf.OpenSSL = {
                    /**
                     * Derives a key and IV from a password.
                     *
                     * @param {string} password The password to derive from.
                     * @param {number} keySize The size in words of the key to generate.
                     * @param {number} ivSize The size in words of the IV to generate.
                     * @param {WordArray|string} salt (Optional) A 64-bit salt to use. If omitted, a salt will be generated randomly.
                     *
                     * @return {CipherParams} A cipher params object with the key, IV, and salt.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var derivedParams = CryptoJS.kdf.OpenSSL.execute('Password', 256/32, 128/32);
                     *     var derivedParams = CryptoJS.kdf.OpenSSL.execute('Password', 256/32, 128/32, 'saltsalt');
                     */
                    execute: function (password, keySize, ivSize, salt) {
                        // Generate random salt
                        if (!salt) {
                            salt = WordArray.random(64/8);
                        }

                        // Derive key and IV
                        var key = EvpKDF.create({ keySize: keySize + ivSize }).compute(password, salt);

                        // Separate key and IV
                        var iv = WordArray.create(key.words.slice(keySize), ivSize * 4);
                        key.sigBytes = keySize * 4;

                        // Return params
                        return CipherParams.create({ key: key, iv: iv, salt: salt });
                    }
                };

                /**
                 * A serializable cipher wrapper that derives the key from a password,
                 * and returns ciphertext as a serializable cipher params object.
                 */
                var PasswordBasedCipher = C_lib.PasswordBasedCipher = SerializableCipher.extend({
                    /**
                     * Configuration options.
                     *
                     * @property {KDF} kdf The key derivation function to use to generate a key and IV from a password. Default: OpenSSL
                     */
                    cfg: SerializableCipher.cfg.extend({
                        kdf: OpenSSLKdf
                    }),

                    /**
                     * Encrypts a message using a password.
                     *
                     * @param {Cipher} cipher The cipher algorithm to use.
                     * @param {WordArray|string} message The message to encrypt.
                     * @param {string} password The password.
                     * @param {Object} cfg (Optional) The configuration options to use for this operation.
                     *
                     * @return {CipherParams} A cipher params object.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var ciphertextParams = CryptoJS.lib.PasswordBasedCipher.encrypt(CryptoJS.algo.AES, message, 'password');
                     *     var ciphertextParams = CryptoJS.lib.PasswordBasedCipher.encrypt(CryptoJS.algo.AES, message, 'password', { format: CryptoJS.format.OpenSSL });
                     */
                    encrypt: function (cipher, message, password, cfg) {
                        // Apply config defaults
                        cfg = this.cfg.extend(cfg);

                        // Derive key and other params
                        var derivedParams = cfg.kdf.execute(password, cipher.keySize, cipher.ivSize);

                        // Add IV to config
                        cfg.iv = derivedParams.iv;

                        // Encrypt
                        var ciphertext = SerializableCipher.encrypt.call(this, cipher, message, derivedParams.key, cfg);

                        // Mix in derived params
                        ciphertext.mixIn(derivedParams);

                        return ciphertext;
                    },

                    /**
                     * Decrypts serialized ciphertext using a password.
                     *
                     * @param {Cipher} cipher The cipher algorithm to use.
                     * @param {CipherParams|string} ciphertext The ciphertext to decrypt.
                     * @param {string} password The password.
                     * @param {Object} cfg (Optional) The configuration options to use for this operation.
                     *
                     * @return {WordArray} The plaintext.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var plaintext = CryptoJS.lib.PasswordBasedCipher.decrypt(CryptoJS.algo.AES, formattedCiphertext, 'password', { format: CryptoJS.format.OpenSSL });
                     *     var plaintext = CryptoJS.lib.PasswordBasedCipher.decrypt(CryptoJS.algo.AES, ciphertextParams, 'password', { format: CryptoJS.format.OpenSSL });
                     */
                    decrypt: function (cipher, ciphertext, password, cfg) {
                        // Apply config defaults
                        cfg = this.cfg.extend(cfg);

                        // Convert string to CipherParams
                        ciphertext = this._parse(ciphertext, cfg.format);

                        // Derive key and other params
                        var derivedParams = cfg.kdf.execute(password, cipher.keySize, cipher.ivSize, ciphertext.salt);

                        // Add IV to config
                        cfg.iv = derivedParams.iv;

                        // Decrypt
                        var plaintext = SerializableCipher.decrypt.call(this, cipher, ciphertext, derivedParams.key, cfg);

                        return plaintext;
                    }
                });
            }());


            /**
             * Cipher Feedback block mode.
             */
            CryptoJS.mode.CFB = (function () {
                var CFB = CryptoJS.lib.BlockCipherMode.extend();

                CFB.Encryptor = CFB.extend({
                    processBlock: function (words, offset) {
                        // Shortcuts
                        var cipher = this._cipher;
                        var blockSize = cipher.blockSize;

                        generateKeystreamAndEncrypt.call(this, words, offset, blockSize, cipher);

                        // Remember this block to use with next block
                        this._prevBlock = words.slice(offset, offset + blockSize);
                    }
                });

                CFB.Decryptor = CFB.extend({
                    processBlock: function (words, offset) {
                        // Shortcuts
                        var cipher = this._cipher;
                        var blockSize = cipher.blockSize;

                        // Remember this block to use with next block
                        var thisBlock = words.slice(offset, offset + blockSize);

                        generateKeystreamAndEncrypt.call(this, words, offset, blockSize, cipher);

                        // This block becomes the previous block
                        this._prevBlock = thisBlock;
                    }
                });

                function generateKeystreamAndEncrypt(words, offset, blockSize, cipher) {
                    var keystream;

                    // Shortcut
                    var iv = this._iv;

                    // Generate keystream
                    if (iv) {
                        keystream = iv.slice(0);

                        // Remove IV for subsequent blocks
                        this._iv = undefined;
                    } else {
                        keystream = this._prevBlock;
                    }
                    cipher.encryptBlock(keystream, 0);

                    // Encrypt
                    for (var i = 0; i < blockSize; i++) {
                        words[offset + i] ^= keystream[i];
                    }
                }

                return CFB;
            }());


            /**
             * Electronic Codebook block mode.
             */
            CryptoJS.mode.ECB = (function () {
                var ECB = CryptoJS.lib.BlockCipherMode.extend();

                ECB.Encryptor = ECB.extend({
                    processBlock: function (words, offset) {
                        this._cipher.encryptBlock(words, offset);
                    }
                });

                ECB.Decryptor = ECB.extend({
                    processBlock: function (words, offset) {
                        this._cipher.decryptBlock(words, offset);
                    }
                });

                return ECB;
            }());


            /**
             * ANSI X.923 padding strategy.
             */
            CryptoJS.pad.AnsiX923 = {
                pad: function (data, blockSize) {
                    // Shortcuts
                    var dataSigBytes = data.sigBytes;
                    var blockSizeBytes = blockSize * 4;

                    // Count padding bytes
                    var nPaddingBytes = blockSizeBytes - dataSigBytes % blockSizeBytes;

                    // Compute last byte position
                    var lastBytePos = dataSigBytes + nPaddingBytes - 1;

                    // Pad
                    data.clamp();
                    data.words[lastBytePos >>> 2] |= nPaddingBytes << (24 - (lastBytePos % 4) * 8);
                    data.sigBytes += nPaddingBytes;
                },

                unpad: function (data) {
                    // Get number of padding bytes from last byte
                    var nPaddingBytes = data.words[(data.sigBytes - 1) >>> 2] & 0xff;

                    // Remove padding
                    data.sigBytes -= nPaddingBytes;
                }
            };


            /**
             * ISO 10126 padding strategy.
             */
            CryptoJS.pad.Iso10126 = {
                pad: function (data, blockSize) {
                    // Shortcut
                    var blockSizeBytes = blockSize * 4;

                    // Count padding bytes
                    var nPaddingBytes = blockSizeBytes - data.sigBytes % blockSizeBytes;

                    // Pad
                    data.concat(CryptoJS.lib.WordArray.random(nPaddingBytes - 1)).
                    concat(CryptoJS.lib.WordArray.create([nPaddingBytes << 24], 1));
                },

                unpad: function (data) {
                    // Get number of padding bytes from last byte
                    var nPaddingBytes = data.words[(data.sigBytes - 1) >>> 2] & 0xff;

                    // Remove padding
                    data.sigBytes -= nPaddingBytes;
                }
            };


            /**
             * ISO/IEC 9797-1 Padding Method 2.
             */
            CryptoJS.pad.Iso97971 = {
                pad: function (data, blockSize) {
                    // Add 0x80 byte
                    data.concat(CryptoJS.lib.WordArray.create([0x80000000], 1));

                    // Zero pad the rest
                    CryptoJS.pad.ZeroPadding.pad(data, blockSize);
                },

                unpad: function (data) {
                    // Remove zero padding
                    CryptoJS.pad.ZeroPadding.unpad(data);

                    // Remove one more byte -- the 0x80 byte
                    data.sigBytes--;
                }
            };


            /**
             * Output Feedback block mode.
             */
            CryptoJS.mode.OFB = (function () {
                var OFB = CryptoJS.lib.BlockCipherMode.extend();

                var Encryptor = OFB.Encryptor = OFB.extend({
                    processBlock: function (words, offset) {
                        // Shortcuts
                        var cipher = this._cipher
                        var blockSize = cipher.blockSize;
                        var iv = this._iv;
                        var keystream = this._keystream;

                        // Generate keystream
                        if (iv) {
                            keystream = this._keystream = iv.slice(0);

                            // Remove IV for subsequent blocks
                            this._iv = undefined;
                        }
                        cipher.encryptBlock(keystream, 0);

                        // Encrypt
                        for (var i = 0; i < blockSize; i++) {
                            words[offset + i] ^= keystream[i];
                        }
                    }
                });

                OFB.Decryptor = Encryptor;

                return OFB;
            }());


            /**
             * A noop padding strategy.
             */
            CryptoJS.pad.NoPadding = {
                pad: function () {
                },

                unpad: function () {
                }
            };

            //解密xml下载图片的buffer内容用到
            CryptoJS.enc.u8array = {
                /**
                 * Converts a word array to a Uint8Array.
                 *
                 * @param {WordArray} wordArray The word array.
                 *
                 * @return {Uint8Array} The Uint8Array.
                 *
                 * @static
                 *
                 * @example
                 *
                 * var u8arr = CryptoJS.enc.u8array.stringify(wordArray);
                 */
                stringify: function (wordArray) {
// Shortcuts
                    var words = wordArray.words;
                    var sigBytes = wordArray.sigBytes;
// Convert
                    var u8 = new Uint8Array(sigBytes);
                    for (var i = 0; i < sigBytes; i++) {
                        var byte = (words[i >>> 2] >>> (24 - (i % 4) * 8)) & 0xff;
                        u8[i]=byte;
                    }
                    return u8;
                },
                /**
                 * Converts a Uint8Array to a word array.
                 *
                 * @param {string} u8Str The Uint8Array.
                 *
                 * @return {WordArray} The word array.
                 *
                 * @static
                 *
                 * @example
                 *
                 * var wordArray = CryptoJS.enc.u8array.parse(u8arr);
                 */
                parse: function (u8arr) {
// Shortcut
                    var len = u8arr.length;
// Convert
                    var words = [];
                    for (var i = 0; i < len; i++) {
                        words[i >>> 2] |= (u8arr[i] & 0xff) << (24 - (i % 4) * 8);
                    }
                    return CryptoJS.lib.WordArray.create(words, len);
                }
            };

            (function (undefined) {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var CipherParams = C_lib.CipherParams;
                var C_enc = C.enc;
                var Hex = C_enc.Hex;
                var C_format = C.format;

                var HexFormatter = C_format.Hex = {
                    /**
                     * Converts the ciphertext of a cipher params object to a hexadecimally encoded string.
                     *
                     * @param {CipherParams} cipherParams The cipher params object.
                     *
                     * @return {string} The hexadecimally encoded string.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var hexString = CryptoJS.format.Hex.stringify(cipherParams);
                     */
                    stringify: function (cipherParams) {
                        return cipherParams.ciphertext.toString(Hex);
                    },

                    /**
                     * Converts a hexadecimally encoded ciphertext string to a cipher params object.
                     *
                     * @param {string} input The hexadecimally encoded string.
                     *
                     * @return {CipherParams} The cipher params object.
                     *
                     * @static
                     *
                     * @example
                     *
                     *     var cipherParams = CryptoJS.format.Hex.parse(hexString);
                     */
                    parse: function (input) {
                        var ciphertext = Hex.parse(input);
                        return CipherParams.create({ ciphertext: ciphertext });
                    }
                };
            }());


            (function () {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var BlockCipher = C_lib.BlockCipher;
                var C_algo = C.algo;

                // Lookup tables
                var SBOX = [];
                var INV_SBOX = [];
                var SUB_MIX_0 = [];
                var SUB_MIX_1 = [];
                var SUB_MIX_2 = [];
                var SUB_MIX_3 = [];
                var INV_SUB_MIX_0 = [];
                var INV_SUB_MIX_1 = [];
                var INV_SUB_MIX_2 = [];
                var INV_SUB_MIX_3 = [];

                // Compute lookup tables
                (function () {
                    // Compute double table
                    var d = [];
                    for (var i = 0; i < 256; i++) {
                        if (i < 128) {
                            d[i] = i << 1;
                        } else {
                            d[i] = (i << 1) ^ 0x11b;
                        }
                    }

                    // Walk GF(2^8)
                    var x = 0;
                    var xi = 0;
                    for (var i = 0; i < 256; i++) {
                        // Compute sbox
                        var sx = xi ^ (xi << 1) ^ (xi << 2) ^ (xi << 3) ^ (xi << 4);
                        sx = (sx >>> 8) ^ (sx & 0xff) ^ 0x63;
                        SBOX[x] = sx;
                        INV_SBOX[sx] = x;

                        // Compute multiplication
                        var x2 = d[x];
                        var x4 = d[x2];
                        var x8 = d[x4];

                        // Compute sub bytes, mix columns tables
                        var t = (d[sx] * 0x101) ^ (sx * 0x1010100);
                        SUB_MIX_0[x] = (t << 24) | (t >>> 8);
                        SUB_MIX_1[x] = (t << 16) | (t >>> 16);
                        SUB_MIX_2[x] = (t << 8)  | (t >>> 24);
                        SUB_MIX_3[x] = t;

                        // Compute inv sub bytes, inv mix columns tables
                        var t = (x8 * 0x1010101) ^ (x4 * 0x10001) ^ (x2 * 0x101) ^ (x * 0x1010100);
                        INV_SUB_MIX_0[sx] = (t << 24) | (t >>> 8);
                        INV_SUB_MIX_1[sx] = (t << 16) | (t >>> 16);
                        INV_SUB_MIX_2[sx] = (t << 8)  | (t >>> 24);
                        INV_SUB_MIX_3[sx] = t;

                        // Compute next counter
                        if (!x) {
                            x = xi = 1;
                        } else {
                            x = x2 ^ d[d[d[x8 ^ x2]]];
                            xi ^= d[d[xi]];
                        }
                    }
                }());

                // Precomputed Rcon lookup
                var RCON = [0x00, 0x01, 0x02, 0x04, 0x08, 0x10, 0x20, 0x40, 0x80, 0x1b, 0x36];

                /**
                 * AES block cipher algorithm.
                 */
                var AES = C_algo.AES = BlockCipher.extend({
                    _doReset: function () {
                        var t;

                        // Skip reset of nRounds has been set before and key did not change
                        if (this._nRounds && this._keyPriorReset === this._key) {
                            return;
                        }

                        // Shortcuts
                        var key = this._keyPriorReset = this._key;
                        var keyWords = key.words;
                        var keySize = key.sigBytes / 4;

                        // Compute number of rounds
                        var nRounds = this._nRounds = keySize + 6;

                        // Compute number of key schedule rows
                        var ksRows = (nRounds + 1) * 4;

                        // Compute key schedule
                        var keySchedule = this._keySchedule = [];
                        for (var ksRow = 0; ksRow < ksRows; ksRow++) {
                            if (ksRow < keySize) {
                                keySchedule[ksRow] = keyWords[ksRow];
                            } else {
                                t = keySchedule[ksRow - 1];

                                if (!(ksRow % keySize)) {
                                    // Rot word
                                    t = (t << 8) | (t >>> 24);

                                    // Sub word
                                    t = (SBOX[t >>> 24] << 24) | (SBOX[(t >>> 16) & 0xff] << 16) | (SBOX[(t >>> 8) & 0xff] << 8) | SBOX[t & 0xff];

                                    // Mix Rcon
                                    t ^= RCON[(ksRow / keySize) | 0] << 24;
                                } else if (keySize > 6 && ksRow % keySize == 4) {
                                    // Sub word
                                    t = (SBOX[t >>> 24] << 24) | (SBOX[(t >>> 16) & 0xff] << 16) | (SBOX[(t >>> 8) & 0xff] << 8) | SBOX[t & 0xff];
                                }

                                keySchedule[ksRow] = keySchedule[ksRow - keySize] ^ t;
                            }
                        }

                        // Compute inv key schedule
                        var invKeySchedule = this._invKeySchedule = [];
                        for (var invKsRow = 0; invKsRow < ksRows; invKsRow++) {
                            var ksRow = ksRows - invKsRow;

                            if (invKsRow % 4) {
                                var t = keySchedule[ksRow];
                            } else {
                                var t = keySchedule[ksRow - 4];
                            }

                            if (invKsRow < 4 || ksRow <= 4) {
                                invKeySchedule[invKsRow] = t;
                            } else {
                                invKeySchedule[invKsRow] = INV_SUB_MIX_0[SBOX[t >>> 24]] ^ INV_SUB_MIX_1[SBOX[(t >>> 16) & 0xff]] ^
                                    INV_SUB_MIX_2[SBOX[(t >>> 8) & 0xff]] ^ INV_SUB_MIX_3[SBOX[t & 0xff]];
                            }
                        }
                    },

                    encryptBlock: function (M, offset) {
                        this._doCryptBlock(M, offset, this._keySchedule, SUB_MIX_0, SUB_MIX_1, SUB_MIX_2, SUB_MIX_3, SBOX);
                    },

                    decryptBlock: function (M, offset) {
                        // Swap 2nd and 4th rows
                        var t = M[offset + 1];
                        M[offset + 1] = M[offset + 3];
                        M[offset + 3] = t;

                        this._doCryptBlock(M, offset, this._invKeySchedule, INV_SUB_MIX_0, INV_SUB_MIX_1, INV_SUB_MIX_2, INV_SUB_MIX_3, INV_SBOX);

                        // Inv swap 2nd and 4th rows
                        var t = M[offset + 1];
                        M[offset + 1] = M[offset + 3];
                        M[offset + 3] = t;
                    },

                    _doCryptBlock: function (M, offset, keySchedule, SUB_MIX_0, SUB_MIX_1, SUB_MIX_2, SUB_MIX_3, SBOX) {
                        // Shortcut
                        var nRounds = this._nRounds;

                        // Get input, add round key
                        var s0 = M[offset]     ^ keySchedule[0];
                        var s1 = M[offset + 1] ^ keySchedule[1];
                        var s2 = M[offset + 2] ^ keySchedule[2];
                        var s3 = M[offset + 3] ^ keySchedule[3];

                        // Key schedule row counter
                        var ksRow = 4;

                        // Rounds
                        for (var round = 1; round < nRounds; round++) {
                            // Shift rows, sub bytes, mix columns, add round key
                            var t0 = SUB_MIX_0[s0 >>> 24] ^ SUB_MIX_1[(s1 >>> 16) & 0xff] ^ SUB_MIX_2[(s2 >>> 8) & 0xff] ^ SUB_MIX_3[s3 & 0xff] ^ keySchedule[ksRow++];
                            var t1 = SUB_MIX_0[s1 >>> 24] ^ SUB_MIX_1[(s2 >>> 16) & 0xff] ^ SUB_MIX_2[(s3 >>> 8) & 0xff] ^ SUB_MIX_3[s0 & 0xff] ^ keySchedule[ksRow++];
                            var t2 = SUB_MIX_0[s2 >>> 24] ^ SUB_MIX_1[(s3 >>> 16) & 0xff] ^ SUB_MIX_2[(s0 >>> 8) & 0xff] ^ SUB_MIX_3[s1 & 0xff] ^ keySchedule[ksRow++];
                            var t3 = SUB_MIX_0[s3 >>> 24] ^ SUB_MIX_1[(s0 >>> 16) & 0xff] ^ SUB_MIX_2[(s1 >>> 8) & 0xff] ^ SUB_MIX_3[s2 & 0xff] ^ keySchedule[ksRow++];

                            // Update state
                            s0 = t0;
                            s1 = t1;
                            s2 = t2;
                            s3 = t3;
                        }

                        // Shift rows, sub bytes, add round key
                        var t0 = ((SBOX[s0 >>> 24] << 24) | (SBOX[(s1 >>> 16) & 0xff] << 16) | (SBOX[(s2 >>> 8) & 0xff] << 8) | SBOX[s3 & 0xff]) ^ keySchedule[ksRow++];
                        var t1 = ((SBOX[s1 >>> 24] << 24) | (SBOX[(s2 >>> 16) & 0xff] << 16) | (SBOX[(s3 >>> 8) & 0xff] << 8) | SBOX[s0 & 0xff]) ^ keySchedule[ksRow++];
                        var t2 = ((SBOX[s2 >>> 24] << 24) | (SBOX[(s3 >>> 16) & 0xff] << 16) | (SBOX[(s0 >>> 8) & 0xff] << 8) | SBOX[s1 & 0xff]) ^ keySchedule[ksRow++];
                        var t3 = ((SBOX[s3 >>> 24] << 24) | (SBOX[(s0 >>> 16) & 0xff] << 16) | (SBOX[(s1 >>> 8) & 0xff] << 8) | SBOX[s2 & 0xff]) ^ keySchedule[ksRow++];

                        // Set output
                        M[offset]     = t0;
                        M[offset + 1] = t1;
                        M[offset + 2] = t2;
                        M[offset + 3] = t3;
                    },

                    keySize: 256/32
                });

                /**
                 * Shortcut functions to the cipher's object interface.
                 *
                 * @example
                 *
                 *     var ciphertext = CryptoJS.AES.encrypt(message, key, cfg);
                 *     var plaintext  = CryptoJS.AES.decrypt(ciphertext, key, cfg);
                 */
                C.AES = BlockCipher._createHelper(AES);
            }());


            (function () {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var WordArray = C_lib.WordArray;
                var BlockCipher = C_lib.BlockCipher;
                var C_algo = C.algo;

                // Permuted Choice 1 constants
                var PC1 = [
                    57, 49, 41, 33, 25, 17, 9,  1,
                    58, 50, 42, 34, 26, 18, 10, 2,
                    59, 51, 43, 35, 27, 19, 11, 3,
                    60, 52, 44, 36, 63, 55, 47, 39,
                    31, 23, 15, 7,  62, 54, 46, 38,
                    30, 22, 14, 6,  61, 53, 45, 37,
                    29, 21, 13, 5,  28, 20, 12, 4
                ];

                // Permuted Choice 2 constants
                var PC2 = [
                    14, 17, 11, 24, 1,  5,
                    3,  28, 15, 6,  21, 10,
                    23, 19, 12, 4,  26, 8,
                    16, 7,  27, 20, 13, 2,
                    41, 52, 31, 37, 47, 55,
                    30, 40, 51, 45, 33, 48,
                    44, 49, 39, 56, 34, 53,
                    46, 42, 50, 36, 29, 32
                ];

                // Cumulative bit shift constants
                var BIT_SHIFTS = [1,  2,  4,  6,  8,  10, 12, 14, 15, 17, 19, 21, 23, 25, 27, 28];

                // SBOXes and round permutation constants
                var SBOX_P = [
                    {
                        0x0: 0x808200,
                        0x10000000: 0x8000,
                        0x20000000: 0x808002,
                        0x30000000: 0x2,
                        0x40000000: 0x200,
                        0x50000000: 0x808202,
                        0x60000000: 0x800202,
                        0x70000000: 0x800000,
                        0x80000000: 0x202,
                        0x90000000: 0x800200,
                        0xa0000000: 0x8200,
                        0xb0000000: 0x808000,
                        0xc0000000: 0x8002,
                        0xd0000000: 0x800002,
                        0xe0000000: 0x0,
                        0xf0000000: 0x8202,
                        0x8000000: 0x0,
                        0x18000000: 0x808202,
                        0x28000000: 0x8202,
                        0x38000000: 0x8000,
                        0x48000000: 0x808200,
                        0x58000000: 0x200,
                        0x68000000: 0x808002,
                        0x78000000: 0x2,
                        0x88000000: 0x800200,
                        0x98000000: 0x8200,
                        0xa8000000: 0x808000,
                        0xb8000000: 0x800202,
                        0xc8000000: 0x800002,
                        0xd8000000: 0x8002,
                        0xe8000000: 0x202,
                        0xf8000000: 0x800000,
                        0x1: 0x8000,
                        0x10000001: 0x2,
                        0x20000001: 0x808200,
                        0x30000001: 0x800000,
                        0x40000001: 0x808002,
                        0x50000001: 0x8200,
                        0x60000001: 0x200,
                        0x70000001: 0x800202,
                        0x80000001: 0x808202,
                        0x90000001: 0x808000,
                        0xa0000001: 0x800002,
                        0xb0000001: 0x8202,
                        0xc0000001: 0x202,
                        0xd0000001: 0x800200,
                        0xe0000001: 0x8002,
                        0xf0000001: 0x0,
                        0x8000001: 0x808202,
                        0x18000001: 0x808000,
                        0x28000001: 0x800000,
                        0x38000001: 0x200,
                        0x48000001: 0x8000,
                        0x58000001: 0x800002,
                        0x68000001: 0x2,
                        0x78000001: 0x8202,
                        0x88000001: 0x8002,
                        0x98000001: 0x800202,
                        0xa8000001: 0x202,
                        0xb8000001: 0x808200,
                        0xc8000001: 0x800200,
                        0xd8000001: 0x0,
                        0xe8000001: 0x8200,
                        0xf8000001: 0x808002
                    },
                    {
                        0x0: 0x40084010,
                        0x1000000: 0x4000,
                        0x2000000: 0x80000,
                        0x3000000: 0x40080010,
                        0x4000000: 0x40000010,
                        0x5000000: 0x40084000,
                        0x6000000: 0x40004000,
                        0x7000000: 0x10,
                        0x8000000: 0x84000,
                        0x9000000: 0x40004010,
                        0xa000000: 0x40000000,
                        0xb000000: 0x84010,
                        0xc000000: 0x80010,
                        0xd000000: 0x0,
                        0xe000000: 0x4010,
                        0xf000000: 0x40080000,
                        0x800000: 0x40004000,
                        0x1800000: 0x84010,
                        0x2800000: 0x10,
                        0x3800000: 0x40004010,
                        0x4800000: 0x40084010,
                        0x5800000: 0x40000000,
                        0x6800000: 0x80000,
                        0x7800000: 0x40080010,
                        0x8800000: 0x80010,
                        0x9800000: 0x0,
                        0xa800000: 0x4000,
                        0xb800000: 0x40080000,
                        0xc800000: 0x40000010,
                        0xd800000: 0x84000,
                        0xe800000: 0x40084000,
                        0xf800000: 0x4010,
                        0x10000000: 0x0,
                        0x11000000: 0x40080010,
                        0x12000000: 0x40004010,
                        0x13000000: 0x40084000,
                        0x14000000: 0x40080000,
                        0x15000000: 0x10,
                        0x16000000: 0x84010,
                        0x17000000: 0x4000,
                        0x18000000: 0x4010,
                        0x19000000: 0x80000,
                        0x1a000000: 0x80010,
                        0x1b000000: 0x40000010,
                        0x1c000000: 0x84000,
                        0x1d000000: 0x40004000,
                        0x1e000000: 0x40000000,
                        0x1f000000: 0x40084010,
                        0x10800000: 0x84010,
                        0x11800000: 0x80000,
                        0x12800000: 0x40080000,
                        0x13800000: 0x4000,
                        0x14800000: 0x40004000,
                        0x15800000: 0x40084010,
                        0x16800000: 0x10,
                        0x17800000: 0x40000000,
                        0x18800000: 0x40084000,
                        0x19800000: 0x40000010,
                        0x1a800000: 0x40004010,
                        0x1b800000: 0x80010,
                        0x1c800000: 0x0,
                        0x1d800000: 0x4010,
                        0x1e800000: 0x40080010,
                        0x1f800000: 0x84000
                    },
                    {
                        0x0: 0x104,
                        0x100000: 0x0,
                        0x200000: 0x4000100,
                        0x300000: 0x10104,
                        0x400000: 0x10004,
                        0x500000: 0x4000004,
                        0x600000: 0x4010104,
                        0x700000: 0x4010000,
                        0x800000: 0x4000000,
                        0x900000: 0x4010100,
                        0xa00000: 0x10100,
                        0xb00000: 0x4010004,
                        0xc00000: 0x4000104,
                        0xd00000: 0x10000,
                        0xe00000: 0x4,
                        0xf00000: 0x100,
                        0x80000: 0x4010100,
                        0x180000: 0x4010004,
                        0x280000: 0x0,
                        0x380000: 0x4000100,
                        0x480000: 0x4000004,
                        0x580000: 0x10000,
                        0x680000: 0x10004,
                        0x780000: 0x104,
                        0x880000: 0x4,
                        0x980000: 0x100,
                        0xa80000: 0x4010000,
                        0xb80000: 0x10104,
                        0xc80000: 0x10100,
                        0xd80000: 0x4000104,
                        0xe80000: 0x4010104,
                        0xf80000: 0x4000000,
                        0x1000000: 0x4010100,
                        0x1100000: 0x10004,
                        0x1200000: 0x10000,
                        0x1300000: 0x4000100,
                        0x1400000: 0x100,
                        0x1500000: 0x4010104,
                        0x1600000: 0x4000004,
                        0x1700000: 0x0,
                        0x1800000: 0x4000104,
                        0x1900000: 0x4000000,
                        0x1a00000: 0x4,
                        0x1b00000: 0x10100,
                        0x1c00000: 0x4010000,
                        0x1d00000: 0x104,
                        0x1e00000: 0x10104,
                        0x1f00000: 0x4010004,
                        0x1080000: 0x4000000,
                        0x1180000: 0x104,
                        0x1280000: 0x4010100,
                        0x1380000: 0x0,
                        0x1480000: 0x10004,
                        0x1580000: 0x4000100,
                        0x1680000: 0x100,
                        0x1780000: 0x4010004,
                        0x1880000: 0x10000,
                        0x1980000: 0x4010104,
                        0x1a80000: 0x10104,
                        0x1b80000: 0x4000004,
                        0x1c80000: 0x4000104,
                        0x1d80000: 0x4010000,
                        0x1e80000: 0x4,
                        0x1f80000: 0x10100
                    },
                    {
                        0x0: 0x80401000,
                        0x10000: 0x80001040,
                        0x20000: 0x401040,
                        0x30000: 0x80400000,
                        0x40000: 0x0,
                        0x50000: 0x401000,
                        0x60000: 0x80000040,
                        0x70000: 0x400040,
                        0x80000: 0x80000000,
                        0x90000: 0x400000,
                        0xa0000: 0x40,
                        0xb0000: 0x80001000,
                        0xc0000: 0x80400040,
                        0xd0000: 0x1040,
                        0xe0000: 0x1000,
                        0xf0000: 0x80401040,
                        0x8000: 0x80001040,
                        0x18000: 0x40,
                        0x28000: 0x80400040,
                        0x38000: 0x80001000,
                        0x48000: 0x401000,
                        0x58000: 0x80401040,
                        0x68000: 0x0,
                        0x78000: 0x80400000,
                        0x88000: 0x1000,
                        0x98000: 0x80401000,
                        0xa8000: 0x400000,
                        0xb8000: 0x1040,
                        0xc8000: 0x80000000,
                        0xd8000: 0x400040,
                        0xe8000: 0x401040,
                        0xf8000: 0x80000040,
                        0x100000: 0x400040,
                        0x110000: 0x401000,
                        0x120000: 0x80000040,
                        0x130000: 0x0,
                        0x140000: 0x1040,
                        0x150000: 0x80400040,
                        0x160000: 0x80401000,
                        0x170000: 0x80001040,
                        0x180000: 0x80401040,
                        0x190000: 0x80000000,
                        0x1a0000: 0x80400000,
                        0x1b0000: 0x401040,
                        0x1c0000: 0x80001000,
                        0x1d0000: 0x400000,
                        0x1e0000: 0x40,
                        0x1f0000: 0x1000,
                        0x108000: 0x80400000,
                        0x118000: 0x80401040,
                        0x128000: 0x0,
                        0x138000: 0x401000,
                        0x148000: 0x400040,
                        0x158000: 0x80000000,
                        0x168000: 0x80001040,
                        0x178000: 0x40,
                        0x188000: 0x80000040,
                        0x198000: 0x1000,
                        0x1a8000: 0x80001000,
                        0x1b8000: 0x80400040,
                        0x1c8000: 0x1040,
                        0x1d8000: 0x80401000,
                        0x1e8000: 0x400000,
                        0x1f8000: 0x401040
                    },
                    {
                        0x0: 0x80,
                        0x1000: 0x1040000,
                        0x2000: 0x40000,
                        0x3000: 0x20000000,
                        0x4000: 0x20040080,
                        0x5000: 0x1000080,
                        0x6000: 0x21000080,
                        0x7000: 0x40080,
                        0x8000: 0x1000000,
                        0x9000: 0x20040000,
                        0xa000: 0x20000080,
                        0xb000: 0x21040080,
                        0xc000: 0x21040000,
                        0xd000: 0x0,
                        0xe000: 0x1040080,
                        0xf000: 0x21000000,
                        0x800: 0x1040080,
                        0x1800: 0x21000080,
                        0x2800: 0x80,
                        0x3800: 0x1040000,
                        0x4800: 0x40000,
                        0x5800: 0x20040080,
                        0x6800: 0x21040000,
                        0x7800: 0x20000000,
                        0x8800: 0x20040000,
                        0x9800: 0x0,
                        0xa800: 0x21040080,
                        0xb800: 0x1000080,
                        0xc800: 0x20000080,
                        0xd800: 0x21000000,
                        0xe800: 0x1000000,
                        0xf800: 0x40080,
                        0x10000: 0x40000,
                        0x11000: 0x80,
                        0x12000: 0x20000000,
                        0x13000: 0x21000080,
                        0x14000: 0x1000080,
                        0x15000: 0x21040000,
                        0x16000: 0x20040080,
                        0x17000: 0x1000000,
                        0x18000: 0x21040080,
                        0x19000: 0x21000000,
                        0x1a000: 0x1040000,
                        0x1b000: 0x20040000,
                        0x1c000: 0x40080,
                        0x1d000: 0x20000080,
                        0x1e000: 0x0,
                        0x1f000: 0x1040080,
                        0x10800: 0x21000080,
                        0x11800: 0x1000000,
                        0x12800: 0x1040000,
                        0x13800: 0x20040080,
                        0x14800: 0x20000000,
                        0x15800: 0x1040080,
                        0x16800: 0x80,
                        0x17800: 0x21040000,
                        0x18800: 0x40080,
                        0x19800: 0x21040080,
                        0x1a800: 0x0,
                        0x1b800: 0x21000000,
                        0x1c800: 0x1000080,
                        0x1d800: 0x40000,
                        0x1e800: 0x20040000,
                        0x1f800: 0x20000080
                    },
                    {
                        0x0: 0x10000008,
                        0x100: 0x2000,
                        0x200: 0x10200000,
                        0x300: 0x10202008,
                        0x400: 0x10002000,
                        0x500: 0x200000,
                        0x600: 0x200008,
                        0x700: 0x10000000,
                        0x800: 0x0,
                        0x900: 0x10002008,
                        0xa00: 0x202000,
                        0xb00: 0x8,
                        0xc00: 0x10200008,
                        0xd00: 0x202008,
                        0xe00: 0x2008,
                        0xf00: 0x10202000,
                        0x80: 0x10200000,
                        0x180: 0x10202008,
                        0x280: 0x8,
                        0x380: 0x200000,
                        0x480: 0x202008,
                        0x580: 0x10000008,
                        0x680: 0x10002000,
                        0x780: 0x2008,
                        0x880: 0x200008,
                        0x980: 0x2000,
                        0xa80: 0x10002008,
                        0xb80: 0x10200008,
                        0xc80: 0x0,
                        0xd80: 0x10202000,
                        0xe80: 0x202000,
                        0xf80: 0x10000000,
                        0x1000: 0x10002000,
                        0x1100: 0x10200008,
                        0x1200: 0x10202008,
                        0x1300: 0x2008,
                        0x1400: 0x200000,
                        0x1500: 0x10000000,
                        0x1600: 0x10000008,
                        0x1700: 0x202000,
                        0x1800: 0x202008,
                        0x1900: 0x0,
                        0x1a00: 0x8,
                        0x1b00: 0x10200000,
                        0x1c00: 0x2000,
                        0x1d00: 0x10002008,
                        0x1e00: 0x10202000,
                        0x1f00: 0x200008,
                        0x1080: 0x8,
                        0x1180: 0x202000,
                        0x1280: 0x200000,
                        0x1380: 0x10000008,
                        0x1480: 0x10002000,
                        0x1580: 0x2008,
                        0x1680: 0x10202008,
                        0x1780: 0x10200000,
                        0x1880: 0x10202000,
                        0x1980: 0x10200008,
                        0x1a80: 0x2000,
                        0x1b80: 0x202008,
                        0x1c80: 0x200008,
                        0x1d80: 0x0,
                        0x1e80: 0x10000000,
                        0x1f80: 0x10002008
                    },
                    {
                        0x0: 0x100000,
                        0x10: 0x2000401,
                        0x20: 0x400,
                        0x30: 0x100401,
                        0x40: 0x2100401,
                        0x50: 0x0,
                        0x60: 0x1,
                        0x70: 0x2100001,
                        0x80: 0x2000400,
                        0x90: 0x100001,
                        0xa0: 0x2000001,
                        0xb0: 0x2100400,
                        0xc0: 0x2100000,
                        0xd0: 0x401,
                        0xe0: 0x100400,
                        0xf0: 0x2000000,
                        0x8: 0x2100001,
                        0x18: 0x0,
                        0x28: 0x2000401,
                        0x38: 0x2100400,
                        0x48: 0x100000,
                        0x58: 0x2000001,
                        0x68: 0x2000000,
                        0x78: 0x401,
                        0x88: 0x100401,
                        0x98: 0x2000400,
                        0xa8: 0x2100000,
                        0xb8: 0x100001,
                        0xc8: 0x400,
                        0xd8: 0x2100401,
                        0xe8: 0x1,
                        0xf8: 0x100400,
                        0x100: 0x2000000,
                        0x110: 0x100000,
                        0x120: 0x2000401,
                        0x130: 0x2100001,
                        0x140: 0x100001,
                        0x150: 0x2000400,
                        0x160: 0x2100400,
                        0x170: 0x100401,
                        0x180: 0x401,
                        0x190: 0x2100401,
                        0x1a0: 0x100400,
                        0x1b0: 0x1,
                        0x1c0: 0x0,
                        0x1d0: 0x2100000,
                        0x1e0: 0x2000001,
                        0x1f0: 0x400,
                        0x108: 0x100400,
                        0x118: 0x2000401,
                        0x128: 0x2100001,
                        0x138: 0x1,
                        0x148: 0x2000000,
                        0x158: 0x100000,
                        0x168: 0x401,
                        0x178: 0x2100400,
                        0x188: 0x2000001,
                        0x198: 0x2100000,
                        0x1a8: 0x0,
                        0x1b8: 0x2100401,
                        0x1c8: 0x100401,
                        0x1d8: 0x400,
                        0x1e8: 0x2000400,
                        0x1f8: 0x100001
                    },
                    {
                        0x0: 0x8000820,
                        0x1: 0x20000,
                        0x2: 0x8000000,
                        0x3: 0x20,
                        0x4: 0x20020,
                        0x5: 0x8020820,
                        0x6: 0x8020800,
                        0x7: 0x800,
                        0x8: 0x8020000,
                        0x9: 0x8000800,
                        0xa: 0x20800,
                        0xb: 0x8020020,
                        0xc: 0x820,
                        0xd: 0x0,
                        0xe: 0x8000020,
                        0xf: 0x20820,
                        0x80000000: 0x800,
                        0x80000001: 0x8020820,
                        0x80000002: 0x8000820,
                        0x80000003: 0x8000000,
                        0x80000004: 0x8020000,
                        0x80000005: 0x20800,
                        0x80000006: 0x20820,
                        0x80000007: 0x20,
                        0x80000008: 0x8000020,
                        0x80000009: 0x820,
                        0x8000000a: 0x20020,
                        0x8000000b: 0x8020800,
                        0x8000000c: 0x0,
                        0x8000000d: 0x8020020,
                        0x8000000e: 0x8000800,
                        0x8000000f: 0x20000,
                        0x10: 0x20820,
                        0x11: 0x8020800,
                        0x12: 0x20,
                        0x13: 0x800,
                        0x14: 0x8000800,
                        0x15: 0x8000020,
                        0x16: 0x8020020,
                        0x17: 0x20000,
                        0x18: 0x0,
                        0x19: 0x20020,
                        0x1a: 0x8020000,
                        0x1b: 0x8000820,
                        0x1c: 0x8020820,
                        0x1d: 0x20800,
                        0x1e: 0x820,
                        0x1f: 0x8000000,
                        0x80000010: 0x20000,
                        0x80000011: 0x800,
                        0x80000012: 0x8020020,
                        0x80000013: 0x20820,
                        0x80000014: 0x20,
                        0x80000015: 0x8020000,
                        0x80000016: 0x8000000,
                        0x80000017: 0x8000820,
                        0x80000018: 0x8020820,
                        0x80000019: 0x8000020,
                        0x8000001a: 0x8000800,
                        0x8000001b: 0x0,
                        0x8000001c: 0x20800,
                        0x8000001d: 0x820,
                        0x8000001e: 0x20020,
                        0x8000001f: 0x8020800
                    }
                ];

                // Masks that select the SBOX input
                var SBOX_MASK = [
                    0xf8000001, 0x1f800000, 0x01f80000, 0x001f8000,
                    0x0001f800, 0x00001f80, 0x000001f8, 0x8000001f
                ];

                /**
                 * DES block cipher algorithm.
                 */
                var DES = C_algo.DES = BlockCipher.extend({
                    _doReset: function () {
                        // Shortcuts
                        var key = this._key;
                        var keyWords = key.words;

                        // Select 56 bits according to PC1
                        var keyBits = [];
                        for (var i = 0; i < 56; i++) {
                            var keyBitPos = PC1[i] - 1;
                            keyBits[i] = (keyWords[keyBitPos >>> 5] >>> (31 - keyBitPos % 32)) & 1;
                        }

                        // Assemble 16 subkeys
                        var subKeys = this._subKeys = [];
                        for (var nSubKey = 0; nSubKey < 16; nSubKey++) {
                            // Create subkey
                            var subKey = subKeys[nSubKey] = [];

                            // Shortcut
                            var bitShift = BIT_SHIFTS[nSubKey];

                            // Select 48 bits according to PC2
                            for (var i = 0; i < 24; i++) {
                                // Select from the left 28 key bits
                                subKey[(i / 6) | 0] |= keyBits[((PC2[i] - 1) + bitShift) % 28] << (31 - i % 6);

                                // Select from the right 28 key bits
                                subKey[4 + ((i / 6) | 0)] |= keyBits[28 + (((PC2[i + 24] - 1) + bitShift) % 28)] << (31 - i % 6);
                            }

                            // Since each subkey is applied to an expanded 32-bit input,
                            // the subkey can be broken into 8 values scaled to 32-bits,
                            // which allows the key to be used without expansion
                            subKey[0] = (subKey[0] << 1) | (subKey[0] >>> 31);
                            for (var i = 1; i < 7; i++) {
                                subKey[i] = subKey[i] >>> ((i - 1) * 4 + 3);
                            }
                            subKey[7] = (subKey[7] << 5) | (subKey[7] >>> 27);
                        }

                        // Compute inverse subkeys
                        var invSubKeys = this._invSubKeys = [];
                        for (var i = 0; i < 16; i++) {
                            invSubKeys[i] = subKeys[15 - i];
                        }
                    },

                    encryptBlock: function (M, offset) {
                        this._doCryptBlock(M, offset, this._subKeys);
                    },

                    decryptBlock: function (M, offset) {
                        this._doCryptBlock(M, offset, this._invSubKeys);
                    },

                    _doCryptBlock: function (M, offset, subKeys) {
                        // Get input
                        this._lBlock = M[offset];
                        this._rBlock = M[offset + 1];

                        // Initial permutation
                        exchangeLR.call(this, 4,  0x0f0f0f0f);
                        exchangeLR.call(this, 16, 0x0000ffff);
                        exchangeRL.call(this, 2,  0x33333333);
                        exchangeRL.call(this, 8,  0x00ff00ff);
                        exchangeLR.call(this, 1,  0x55555555);

                        // Rounds
                        for (var round = 0; round < 16; round++) {
                            // Shortcuts
                            var subKey = subKeys[round];
                            var lBlock = this._lBlock;
                            var rBlock = this._rBlock;

                            // Feistel function
                            var f = 0;
                            for (var i = 0; i < 8; i++) {
                                f |= SBOX_P[i][((rBlock ^ subKey[i]) & SBOX_MASK[i]) >>> 0];
                            }
                            this._lBlock = rBlock;
                            this._rBlock = lBlock ^ f;
                        }

                        // Undo swap from last round
                        var t = this._lBlock;
                        this._lBlock = this._rBlock;
                        this._rBlock = t;

                        // Final permutation
                        exchangeLR.call(this, 1,  0x55555555);
                        exchangeRL.call(this, 8,  0x00ff00ff);
                        exchangeRL.call(this, 2,  0x33333333);
                        exchangeLR.call(this, 16, 0x0000ffff);
                        exchangeLR.call(this, 4,  0x0f0f0f0f);

                        // Set output
                        M[offset] = this._lBlock;
                        M[offset + 1] = this._rBlock;
                    },

                    keySize: 64/32,

                    ivSize: 64/32,

                    blockSize: 64/32
                });

                // Swap bits across the left and right words
                function exchangeLR(offset, mask) {
                    var t = ((this._lBlock >>> offset) ^ this._rBlock) & mask;
                    this._rBlock ^= t;
                    this._lBlock ^= t << offset;
                }

                function exchangeRL(offset, mask) {
                    var t = ((this._rBlock >>> offset) ^ this._lBlock) & mask;
                    this._lBlock ^= t;
                    this._rBlock ^= t << offset;
                }

                /**
                 * Shortcut functions to the cipher's object interface.
                 *
                 * @example
                 *
                 *     var ciphertext = CryptoJS.DES.encrypt(message, key, cfg);
                 *     var plaintext  = CryptoJS.DES.decrypt(ciphertext, key, cfg);
                 */
                C.DES = BlockCipher._createHelper(DES);

                /**
                 * Triple-DES block cipher algorithm.
                 */
                var TripleDES = C_algo.TripleDES = BlockCipher.extend({
                    _doReset: function () {
                        // Shortcuts
                        var key = this._key;
                        var keyWords = key.words;
                        // Make sure the key length is valid (64, 128 or >= 192 bit)
                        if (keyWords.length !== 2 && keyWords.length !== 4 && keyWords.length < 6) {
                            throw new Error('Invalid key length - 3DES requires the key length to be 64, 128, 192 or >192.');
                        }

                        // Extend the key according to the keying options defined in 3DES standard
                        var key1 = keyWords.slice(0, 2);
                        var key2 = keyWords.length < 4 ? keyWords.slice(0, 2) : keyWords.slice(2, 4);
                        var key3 = keyWords.length < 6 ? keyWords.slice(0, 2) : keyWords.slice(4, 6);

                        // Create DES instances
                        this._des1 = DES.createEncryptor(WordArray.create(key1));
                        this._des2 = DES.createEncryptor(WordArray.create(key2));
                        this._des3 = DES.createEncryptor(WordArray.create(key3));
                    },

                    encryptBlock: function (M, offset) {
                        this._des1.encryptBlock(M, offset);
                        this._des2.decryptBlock(M, offset);
                        this._des3.encryptBlock(M, offset);
                    },

                    decryptBlock: function (M, offset) {
                        this._des3.decryptBlock(M, offset);
                        this._des2.encryptBlock(M, offset);
                        this._des1.decryptBlock(M, offset);
                    },

                    keySize: 192/32,

                    ivSize: 64/32,

                    blockSize: 64/32
                });

                /**
                 * Shortcut functions to the cipher's object interface.
                 *
                 * @example
                 *
                 *     var ciphertext = CryptoJS.TripleDES.encrypt(message, key, cfg);
                 *     var plaintext  = CryptoJS.TripleDES.decrypt(ciphertext, key, cfg);
                 */
                C.TripleDES = BlockCipher._createHelper(TripleDES);
            }());


            (function () {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var StreamCipher = C_lib.StreamCipher;
                var C_algo = C.algo;

                /**
                 * RC4 stream cipher algorithm.
                 */
                var RC4 = C_algo.RC4 = StreamCipher.extend({
                    _doReset: function () {
                        // Shortcuts
                        var key = this._key;
                        var keyWords = key.words;
                        var keySigBytes = key.sigBytes;

                        // Init sbox
                        var S = this._S = [];
                        for (var i = 0; i < 256; i++) {
                            S[i] = i;
                        }

                        // Key setup
                        for (var i = 0, j = 0; i < 256; i++) {
                            var keyByteIndex = i % keySigBytes;
                            var keyByte = (keyWords[keyByteIndex >>> 2] >>> (24 - (keyByteIndex % 4) * 8)) & 0xff;

                            j = (j + S[i] + keyByte) % 256;

                            // Swap
                            var t = S[i];
                            S[i] = S[j];
                            S[j] = t;
                        }

                        // Counters
                        this._i = this._j = 0;
                    },

                    _doProcessBlock: function (M, offset) {
                        M[offset] ^= generateKeystreamWord.call(this);
                    },

                    keySize: 256/32,

                    ivSize: 0
                });

                function generateKeystreamWord() {
                    // Shortcuts
                    var S = this._S;
                    var i = this._i;
                    var j = this._j;

                    // Generate keystream word
                    var keystreamWord = 0;
                    for (var n = 0; n < 4; n++) {
                        i = (i + 1) % 256;
                        j = (j + S[i]) % 256;

                        // Swap
                        var t = S[i];
                        S[i] = S[j];
                        S[j] = t;

                        keystreamWord |= S[(S[i] + S[j]) % 256] << (24 - n * 8);
                    }

                    // Update counters
                    this._i = i;
                    this._j = j;

                    return keystreamWord;
                }

                /**
                 * Shortcut functions to the cipher's object interface.
                 *
                 * @example
                 *
                 *     var ciphertext = CryptoJS.RC4.encrypt(message, key, cfg);
                 *     var plaintext  = CryptoJS.RC4.decrypt(ciphertext, key, cfg);
                 */
                C.RC4 = StreamCipher._createHelper(RC4);

                /**
                 * Modified RC4 stream cipher algorithm.
                 */
                var RC4Drop = C_algo.RC4Drop = RC4.extend({
                    /**
                     * Configuration options.
                     *
                     * @property {number} drop The number of keystream words to drop. Default 192
                     */
                    cfg: RC4.cfg.extend({
                        drop: 192
                    }),

                    _doReset: function () {
                        RC4._doReset.call(this);

                        // Drop
                        for (var i = this.cfg.drop; i > 0; i--) {
                            generateKeystreamWord.call(this);
                        }
                    }
                });

                /**
                 * Shortcut functions to the cipher's object interface.
                 *
                 * @example
                 *
                 *     var ciphertext = CryptoJS.RC4Drop.encrypt(message, key, cfg);
                 *     var plaintext  = CryptoJS.RC4Drop.decrypt(ciphertext, key, cfg);
                 */
                C.RC4Drop = StreamCipher._createHelper(RC4Drop);
            }());


            /** @preserve
             * Counter block mode compatible with  Dr Brian Gladman fileenc.c
             * derived from CryptoJS.mode.CTR
             * Jan Hruby jhruby.web@gmail.com
             */
            CryptoJS.mode.CTRGladman = (function () {
                var CTRGladman = CryptoJS.lib.BlockCipherMode.extend();

                function incWord(word)
                {
                    if (((word >> 24) & 0xff) === 0xff) { //overflow
                        var b1 = (word >> 16)&0xff;
                        var b2 = (word >> 8)&0xff;
                        var b3 = word & 0xff;

                        if (b1 === 0xff) // overflow b1
                        {
                            b1 = 0;
                            if (b2 === 0xff)
                            {
                                b2 = 0;
                                if (b3 === 0xff)
                                {
                                    b3 = 0;
                                }
                                else
                                {
                                    ++b3;
                                }
                            }
                            else
                            {
                                ++b2;
                            }
                        }
                        else
                        {
                            ++b1;
                        }

                        word = 0;
                        word += (b1 << 16);
                        word += (b2 << 8);
                        word += b3;
                    }
                    else
                    {
                        word += (0x01 << 24);
                    }
                    return word;
                }

                function incCounter(counter)
                {
                    if ((counter[0] = incWord(counter[0])) === 0)
                    {
                        // encr_data in fileenc.c from  Dr Brian Gladman's counts only with DWORD j < 8
                        counter[1] = incWord(counter[1]);
                    }
                    return counter;
                }

                var Encryptor = CTRGladman.Encryptor = CTRGladman.extend({
                    processBlock: function (words, offset) {
                        // Shortcuts
                        var cipher = this._cipher
                        var blockSize = cipher.blockSize;
                        var iv = this._iv;
                        var counter = this._counter;

                        // Generate keystream
                        if (iv) {
                            counter = this._counter = iv.slice(0);

                            // Remove IV for subsequent blocks
                            this._iv = undefined;
                        }

                        incCounter(counter);

                        var keystream = counter.slice(0);
                        cipher.encryptBlock(keystream, 0);

                        // Encrypt
                        for (var i = 0; i < blockSize; i++) {
                            words[offset + i] ^= keystream[i];
                        }
                    }
                });

                CTRGladman.Decryptor = Encryptor;

                return CTRGladman;
            }());




            (function () {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var StreamCipher = C_lib.StreamCipher;
                var C_algo = C.algo;

                // Reusable objects
                var S  = [];
                var C_ = [];
                var G  = [];

                /**
                 * Rabbit stream cipher algorithm
                 */
                var Rabbit = C_algo.Rabbit = StreamCipher.extend({
                    _doReset: function () {
                        // Shortcuts
                        var K = this._key.words;
                        var iv = this.cfg.iv;

                        // Swap endian
                        for (var i = 0; i < 4; i++) {
                            K[i] = (((K[i] << 8)  | (K[i] >>> 24)) & 0x00ff00ff) |
                                (((K[i] << 24) | (K[i] >>> 8))  & 0xff00ff00);
                        }

                        // Generate initial state values
                        var X = this._X = [
                            K[0], (K[3] << 16) | (K[2] >>> 16),
                            K[1], (K[0] << 16) | (K[3] >>> 16),
                            K[2], (K[1] << 16) | (K[0] >>> 16),
                            K[3], (K[2] << 16) | (K[1] >>> 16)
                        ];

                        // Generate initial counter values
                        var C = this._C = [
                            (K[2] << 16) | (K[2] >>> 16), (K[0] & 0xffff0000) | (K[1] & 0x0000ffff),
                            (K[3] << 16) | (K[3] >>> 16), (K[1] & 0xffff0000) | (K[2] & 0x0000ffff),
                            (K[0] << 16) | (K[0] >>> 16), (K[2] & 0xffff0000) | (K[3] & 0x0000ffff),
                            (K[1] << 16) | (K[1] >>> 16), (K[3] & 0xffff0000) | (K[0] & 0x0000ffff)
                        ];

                        // Carry bit
                        this._b = 0;

                        // Iterate the system four times
                        for (var i = 0; i < 4; i++) {
                            nextState.call(this);
                        }

                        // Modify the counters
                        for (var i = 0; i < 8; i++) {
                            C[i] ^= X[(i + 4) & 7];
                        }

                        // IV setup
                        if (iv) {
                            // Shortcuts
                            var IV = iv.words;
                            var IV_0 = IV[0];
                            var IV_1 = IV[1];

                            // Generate four subvectors
                            var i0 = (((IV_0 << 8) | (IV_0 >>> 24)) & 0x00ff00ff) | (((IV_0 << 24) | (IV_0 >>> 8)) & 0xff00ff00);
                            var i2 = (((IV_1 << 8) | (IV_1 >>> 24)) & 0x00ff00ff) | (((IV_1 << 24) | (IV_1 >>> 8)) & 0xff00ff00);
                            var i1 = (i0 >>> 16) | (i2 & 0xffff0000);
                            var i3 = (i2 << 16)  | (i0 & 0x0000ffff);

                            // Modify counter values
                            C[0] ^= i0;
                            C[1] ^= i1;
                            C[2] ^= i2;
                            C[3] ^= i3;
                            C[4] ^= i0;
                            C[5] ^= i1;
                            C[6] ^= i2;
                            C[7] ^= i3;

                            // Iterate the system four times
                            for (var i = 0; i < 4; i++) {
                                nextState.call(this);
                            }
                        }
                    },

                    _doProcessBlock: function (M, offset) {
                        // Shortcut
                        var X = this._X;

                        // Iterate the system
                        nextState.call(this);

                        // Generate four keystream words
                        S[0] = X[0] ^ (X[5] >>> 16) ^ (X[3] << 16);
                        S[1] = X[2] ^ (X[7] >>> 16) ^ (X[5] << 16);
                        S[2] = X[4] ^ (X[1] >>> 16) ^ (X[7] << 16);
                        S[3] = X[6] ^ (X[3] >>> 16) ^ (X[1] << 16);

                        for (var i = 0; i < 4; i++) {
                            // Swap endian
                            S[i] = (((S[i] << 8)  | (S[i] >>> 24)) & 0x00ff00ff) |
                                (((S[i] << 24) | (S[i] >>> 8))  & 0xff00ff00);

                            // Encrypt
                            M[offset + i] ^= S[i];
                        }
                    },

                    blockSize: 128/32,

                    ivSize: 64/32
                });

                function nextState() {
                    // Shortcuts
                    var X = this._X;
                    var C = this._C;

                    // Save old counter values
                    for (var i = 0; i < 8; i++) {
                        C_[i] = C[i];
                    }

                    // Calculate new counter values
                    C[0] = (C[0] + 0x4d34d34d + this._b) | 0;
                    C[1] = (C[1] + 0xd34d34d3 + ((C[0] >>> 0) < (C_[0] >>> 0) ? 1 : 0)) | 0;
                    C[2] = (C[2] + 0x34d34d34 + ((C[1] >>> 0) < (C_[1] >>> 0) ? 1 : 0)) | 0;
                    C[3] = (C[3] + 0x4d34d34d + ((C[2] >>> 0) < (C_[2] >>> 0) ? 1 : 0)) | 0;
                    C[4] = (C[4] + 0xd34d34d3 + ((C[3] >>> 0) < (C_[3] >>> 0) ? 1 : 0)) | 0;
                    C[5] = (C[5] + 0x34d34d34 + ((C[4] >>> 0) < (C_[4] >>> 0) ? 1 : 0)) | 0;
                    C[6] = (C[6] + 0x4d34d34d + ((C[5] >>> 0) < (C_[5] >>> 0) ? 1 : 0)) | 0;
                    C[7] = (C[7] + 0xd34d34d3 + ((C[6] >>> 0) < (C_[6] >>> 0) ? 1 : 0)) | 0;
                    this._b = (C[7] >>> 0) < (C_[7] >>> 0) ? 1 : 0;

                    // Calculate the g-values
                    for (var i = 0; i < 8; i++) {
                        var gx = X[i] + C[i];

                        // Construct high and low argument for squaring
                        var ga = gx & 0xffff;
                        var gb = gx >>> 16;

                        // Calculate high and low result of squaring
                        var gh = ((((ga * ga) >>> 17) + ga * gb) >>> 15) + gb * gb;
                        var gl = (((gx & 0xffff0000) * gx) | 0) + (((gx & 0x0000ffff) * gx) | 0);

                        // High XOR low
                        G[i] = gh ^ gl;
                    }

                    // Calculate new state values
                    X[0] = (G[0] + ((G[7] << 16) | (G[7] >>> 16)) + ((G[6] << 16) | (G[6] >>> 16))) | 0;
                    X[1] = (G[1] + ((G[0] << 8)  | (G[0] >>> 24)) + G[7]) | 0;
                    X[2] = (G[2] + ((G[1] << 16) | (G[1] >>> 16)) + ((G[0] << 16) | (G[0] >>> 16))) | 0;
                    X[3] = (G[3] + ((G[2] << 8)  | (G[2] >>> 24)) + G[1]) | 0;
                    X[4] = (G[4] + ((G[3] << 16) | (G[3] >>> 16)) + ((G[2] << 16) | (G[2] >>> 16))) | 0;
                    X[5] = (G[5] + ((G[4] << 8)  | (G[4] >>> 24)) + G[3]) | 0;
                    X[6] = (G[6] + ((G[5] << 16) | (G[5] >>> 16)) + ((G[4] << 16) | (G[4] >>> 16))) | 0;
                    X[7] = (G[7] + ((G[6] << 8)  | (G[6] >>> 24)) + G[5]) | 0;
                }

                /**
                 * Shortcut functions to the cipher's object interface.
                 *
                 * @example
                 *
                 *     var ciphertext = CryptoJS.Rabbit.encrypt(message, key, cfg);
                 *     var plaintext  = CryptoJS.Rabbit.decrypt(ciphertext, key, cfg);
                 */
                C.Rabbit = StreamCipher._createHelper(Rabbit);
            }());


            /**
             * Counter block mode.
             */
            CryptoJS.mode.CTR = (function () {
                var CTR = CryptoJS.lib.BlockCipherMode.extend();

                var Encryptor = CTR.Encryptor = CTR.extend({
                    processBlock: function (words, offset) {
                        // Shortcuts
                        var cipher = this._cipher
                        var blockSize = cipher.blockSize;
                        var iv = this._iv;
                        var counter = this._counter;

                        // Generate keystream
                        if (iv) {
                            counter = this._counter = iv.slice(0);

                            // Remove IV for subsequent blocks
                            this._iv = undefined;
                        }
                        var keystream = counter.slice(0);
                        cipher.encryptBlock(keystream, 0);

                        // Increment counter
                        counter[blockSize - 1] = (counter[blockSize - 1] + 1) | 0

                        // Encrypt
                        for (var i = 0; i < blockSize; i++) {
                            words[offset + i] ^= keystream[i];
                        }
                    }
                });

                CTR.Decryptor = Encryptor;

                return CTR;
            }());


            (function () {
                // Shortcuts
                var C = CryptoJS;
                var C_lib = C.lib;
                var StreamCipher = C_lib.StreamCipher;
                var C_algo = C.algo;

                // Reusable objects
                var S  = [];
                var C_ = [];
                var G  = [];

                /**
                 * Rabbit stream cipher algorithm.
                 *
                 * This is a legacy version that neglected to convert the key to little-endian.
                 * This error doesn't affect the cipher's security,
                 * but it does affect its compatibility with other implementations.
                 */
                var RabbitLegacy = C_algo.RabbitLegacy = StreamCipher.extend({
                    _doReset: function () {
                        // Shortcuts
                        var K = this._key.words;
                        var iv = this.cfg.iv;

                        // Generate initial state values
                        var X = this._X = [
                            K[0], (K[3] << 16) | (K[2] >>> 16),
                            K[1], (K[0] << 16) | (K[3] >>> 16),
                            K[2], (K[1] << 16) | (K[0] >>> 16),
                            K[3], (K[2] << 16) | (K[1] >>> 16)
                        ];

                        // Generate initial counter values
                        var C = this._C = [
                            (K[2] << 16) | (K[2] >>> 16), (K[0] & 0xffff0000) | (K[1] & 0x0000ffff),
                            (K[3] << 16) | (K[3] >>> 16), (K[1] & 0xffff0000) | (K[2] & 0x0000ffff),
                            (K[0] << 16) | (K[0] >>> 16), (K[2] & 0xffff0000) | (K[3] & 0x0000ffff),
                            (K[1] << 16) | (K[1] >>> 16), (K[3] & 0xffff0000) | (K[0] & 0x0000ffff)
                        ];

                        // Carry bit
                        this._b = 0;

                        // Iterate the system four times
                        for (var i = 0; i < 4; i++) {
                            nextState.call(this);
                        }

                        // Modify the counters
                        for (var i = 0; i < 8; i++) {
                            C[i] ^= X[(i + 4) & 7];
                        }

                        // IV setup
                        if (iv) {
                            // Shortcuts
                            var IV = iv.words;
                            var IV_0 = IV[0];
                            var IV_1 = IV[1];

                            // Generate four subvectors
                            var i0 = (((IV_0 << 8) | (IV_0 >>> 24)) & 0x00ff00ff) | (((IV_0 << 24) | (IV_0 >>> 8)) & 0xff00ff00);
                            var i2 = (((IV_1 << 8) | (IV_1 >>> 24)) & 0x00ff00ff) | (((IV_1 << 24) | (IV_1 >>> 8)) & 0xff00ff00);
                            var i1 = (i0 >>> 16) | (i2 & 0xffff0000);
                            var i3 = (i2 << 16)  | (i0 & 0x0000ffff);

                            // Modify counter values
                            C[0] ^= i0;
                            C[1] ^= i1;
                            C[2] ^= i2;
                            C[3] ^= i3;
                            C[4] ^= i0;
                            C[5] ^= i1;
                            C[6] ^= i2;
                            C[7] ^= i3;

                            // Iterate the system four times
                            for (var i = 0; i < 4; i++) {
                                nextState.call(this);
                            }
                        }
                    },

                    _doProcessBlock: function (M, offset) {
                        // Shortcut
                        var X = this._X;

                        // Iterate the system
                        nextState.call(this);

                        // Generate four keystream words
                        S[0] = X[0] ^ (X[5] >>> 16) ^ (X[3] << 16);
                        S[1] = X[2] ^ (X[7] >>> 16) ^ (X[5] << 16);
                        S[2] = X[4] ^ (X[1] >>> 16) ^ (X[7] << 16);
                        S[3] = X[6] ^ (X[3] >>> 16) ^ (X[1] << 16);

                        for (var i = 0; i < 4; i++) {
                            // Swap endian
                            S[i] = (((S[i] << 8)  | (S[i] >>> 24)) & 0x00ff00ff) |
                                (((S[i] << 24) | (S[i] >>> 8))  & 0xff00ff00);

                            // Encrypt
                            M[offset + i] ^= S[i];
                        }
                    },

                    blockSize: 128/32,

                    ivSize: 64/32
                });

                function nextState() {
                    // Shortcuts
                    var X = this._X;
                    var C = this._C;

                    // Save old counter values
                    for (var i = 0; i < 8; i++) {
                        C_[i] = C[i];
                    }

                    // Calculate new counter values
                    C[0] = (C[0] + 0x4d34d34d + this._b) | 0;
                    C[1] = (C[1] + 0xd34d34d3 + ((C[0] >>> 0) < (C_[0] >>> 0) ? 1 : 0)) | 0;
                    C[2] = (C[2] + 0x34d34d34 + ((C[1] >>> 0) < (C_[1] >>> 0) ? 1 : 0)) | 0;
                    C[3] = (C[3] + 0x4d34d34d + ((C[2] >>> 0) < (C_[2] >>> 0) ? 1 : 0)) | 0;
                    C[4] = (C[4] + 0xd34d34d3 + ((C[3] >>> 0) < (C_[3] >>> 0) ? 1 : 0)) | 0;
                    C[5] = (C[5] + 0x34d34d34 + ((C[4] >>> 0) < (C_[4] >>> 0) ? 1 : 0)) | 0;
                    C[6] = (C[6] + 0x4d34d34d + ((C[5] >>> 0) < (C_[5] >>> 0) ? 1 : 0)) | 0;
                    C[7] = (C[7] + 0xd34d34d3 + ((C[6] >>> 0) < (C_[6] >>> 0) ? 1 : 0)) | 0;
                    this._b = (C[7] >>> 0) < (C_[7] >>> 0) ? 1 : 0;

                    // Calculate the g-values
                    for (var i = 0; i < 8; i++) {
                        var gx = X[i] + C[i];

                        // Construct high and low argument for squaring
                        var ga = gx & 0xffff;
                        var gb = gx >>> 16;

                        // Calculate high and low result of squaring
                        var gh = ((((ga * ga) >>> 17) + ga * gb) >>> 15) + gb * gb;
                        var gl = (((gx & 0xffff0000) * gx) | 0) + (((gx & 0x0000ffff) * gx) | 0);

                        // High XOR low
                        G[i] = gh ^ gl;
                    }

                    // Calculate new state values
                    X[0] = (G[0] + ((G[7] << 16) | (G[7] >>> 16)) + ((G[6] << 16) | (G[6] >>> 16))) | 0;
                    X[1] = (G[1] + ((G[0] << 8)  | (G[0] >>> 24)) + G[7]) | 0;
                    X[2] = (G[2] + ((G[1] << 16) | (G[1] >>> 16)) + ((G[0] << 16) | (G[0] >>> 16))) | 0;
                    X[3] = (G[3] + ((G[2] << 8)  | (G[2] >>> 24)) + G[1]) | 0;
                    X[4] = (G[4] + ((G[3] << 16) | (G[3] >>> 16)) + ((G[2] << 16) | (G[2] >>> 16))) | 0;
                    X[5] = (G[5] + ((G[4] << 8)  | (G[4] >>> 24)) + G[3]) | 0;
                    X[6] = (G[6] + ((G[5] << 16) | (G[5] >>> 16)) + ((G[4] << 16) | (G[4] >>> 16))) | 0;
                    X[7] = (G[7] + ((G[6] << 8)  | (G[6] >>> 24)) + G[5]) | 0;
                }

                /**
                 * Shortcut functions to the cipher's object interface.
                 *
                 * @example
                 *
                 *     var ciphertext = CryptoJS.RabbitLegacy.encrypt(message, key, cfg);
                 *     var plaintext  = CryptoJS.RabbitLegacy.decrypt(ciphertext, key, cfg);
                 */
                C.RabbitLegacy = StreamCipher._createHelper(RabbitLegacy);
            }());


            /**
             * Zero padding strategy.
             */
            CryptoJS.pad.ZeroPadding = {
                pad: function (data, blockSize) {
                    // Shortcut
                    var blockSizeBytes = blockSize * 4;
                    // Pad
                    data.clamp();
                    data.sigBytes += blockSizeBytes - ((data.sigBytes % blockSizeBytes) || blockSizeBytes);
                },

                unpad: function (data) {
                    // Shortcut
                    var dataWords = data.words;

                    // Unpad
                    var i = data.sigBytes - 1;
                    for (var i = data.sigBytes - 1; i >= 0; i--) {
                        if (((dataWords[i >>> 2] >>> (24 - (i % 4) * 8)) & 0xff)) {
                            data.sigBytes = i + 1;
                            break;
                        }
                    }
                }
            };
            return CryptoJS;

        })(),

    //创建字符串处理对象
        strObj: {
            //生成二维码
            qrCode: function (h) {
                var s;

                function u(a) {
                    this.mode = s;
                    this.data = a
                }

                function o(a, c) {
                    this.typeNumber = a;
                    this.errorCorrectLevel = c;
                    this.modules = null;
                    this.moduleCount = 0;
                    this.dataCache = null;
                    this.dataList = []
                }

                function q(a, c) {
                    if (void 0 == a.length) throw Error(a.length + "/" + c);
                    for (var d = 0; d < a.length && 0 == a[d];) d++;
                    this.num = Array(a.length - d + c);
                    for (var b = 0; b < a.length - d; b++) this.num[b] = a[b + d]
                }

                function p(a, c) {
                    this.totalCount = a;
                    this.dataCount = c
                }

                function t() {
                    this.buffer = [];
                    this.length = 0
                }

                u.prototype = {
                    getLength: function () {
                        return this.data.length
                    },
                    write: function (a) {
                        for (var c = 0; c < this.data.length; c++) a.put(this.data.charCodeAt(c), 8)
                    }
                };
                o.prototype = {
                    addData: function (a) {
                        this.dataList.push(new u(a));
                        this.dataCache = null
                    }, isDark: function (a, c) {
                        if (0 > a || this.moduleCount <= a || 0 > c || this.moduleCount <= c) throw Error(a + "," + c);
                        return this.modules[a][c]
                    }, getModuleCount: function () {
                        return this.moduleCount
                    }, make: function () {
                        if (1 > this.typeNumber) {
                            for (var a = 1, a = 1; 40 > a; a++) {
                                for (var c = p.getRSBlocks(a, this.errorCorrectLevel), d = new t, b = 0, e = 0; e < c.length; e++) b += c[e].dataCount;
                                for (e = 0; e < this.dataList.length; e++) c = this.dataList[e], d.put(c.mode, 4), d.put(c.getLength(), j.getLengthInBits(c.mode, a)), c.write(d);
                                if (d.getLengthInBits() <= 8 * b) break
                            }
                            this.typeNumber = a
                        }
                        this.makeImpl(!1, this.getBestMaskPattern())
                    }, makeImpl: function (a, c) {
                        this.moduleCount = 4 * this.typeNumber + 17;
                        this.modules = Array(this.moduleCount);
                        for (var d = 0; d < this.moduleCount; d++) {
                            this.modules[d] = Array(this.moduleCount);
                            for (var b = 0; b < this.moduleCount; b++) this.modules[d][b] = null
                        }
                        this.setupPositionProbePattern(0, 0);
                        this.setupPositionProbePattern(this.moduleCount -
                            7, 0);
                        this.setupPositionProbePattern(0, this.moduleCount - 7);
                        this.setupPositionAdjustPattern();
                        this.setupTimingPattern();
                        this.setupTypeInfo(a, c);
                        7 <= this.typeNumber && this.setupTypeNumber(a);
                        null == this.dataCache && (this.dataCache = o.createData(this.typeNumber, this.errorCorrectLevel, this.dataList));
                        this.mapData(this.dataCache, c)
                    }, setupPositionProbePattern: function (a, c) {
                        for (var d = -1; 7 >= d; d++) if (!(-1 >= a + d || this.moduleCount <= a + d)) for (var b = -1; 7 >= b; b++) -1 >= c + b || this.moduleCount <= c + b || (this.modules[a + d][c + b] =
                            0 <= d && 6 >= d && (0 == b || 6 == b) || 0 <= b && 6 >= b && (0 == d || 6 == d) || 2 <= d && 4 >= d && 2 <= b && 4 >= b ? !0 : !1)
                    }, getBestMaskPattern: function () {
                        for (var a = 0, c = 0, d = 0; 8 > d; d++) {
                            this.makeImpl(!0, d);
                            var b = j.getLostPoint(this);
                            if (0 == d || a > b) a = b, c = d
                        }
                        return c
                    }, createMovieClip: function (a, c, d) {
                        a = a.createEmptyMovieClip(c, d);
                        this.make();
                        for (c = 0; c < this.modules.length; c++) for (var d = 1 * c, b = 0; b < this.modules[c].length; b++) {
                            var e = 1 * b;
                            this.modules[c][b] && (a.beginFill(0, 100), a.moveTo(e, d), a.lineTo(e + 1, d), a.lineTo(e + 1, d + 1), a.lineTo(e, d + 1), a.endFill())
                        }
                        return a
                    },
                    setupTimingPattern: function () {
                        for (var a = 8; a < this.moduleCount - 8; a++) null == this.modules[a][6] && (this.modules[a][6] = 0 == a % 2);
                        for (a = 8; a < this.moduleCount - 8; a++) null == this.modules[6][a] && (this.modules[6][a] = 0 == a % 2)
                    }, setupPositionAdjustPattern: function () {
                        for (var a = j.getPatternPosition(this.typeNumber), c = 0; c < a.length; c++) for (var d = 0; d < a.length; d++) {
                            var b = a[c], e = a[d];
                            if (null == this.modules[b][e]) for (var f = -2; 2 >= f; f++) for (var i = -2; 2 >= i; i++) this.modules[b + f][e + i] = -2 == f || 2 == f || -2 == i || 2 == i || 0 == f && 0 == i ? !0 : !1
                        }
                    }, setupTypeNumber: function (a) {
                        for (var c =
                            j.getBCHTypeNumber(this.typeNumber), d = 0; 18 > d; d++) {
                            var b = !a && 1 == (c >> d & 1);
                            this.modules[Math.floor(d / 3)][d % 3 + this.moduleCount - 8 - 3] = b
                        }
                        for (d = 0; 18 > d; d++) b = !a && 1 == (c >> d & 1), this.modules[d % 3 + this.moduleCount - 8 - 3][Math.floor(d / 3)] = b
                    }, setupTypeInfo: function (a, c) {
                        for (var d = j.getBCHTypeInfo(this.errorCorrectLevel << 3 | c), b = 0; 15 > b; b++) {
                            var e = !a && 1 == (d >> b & 1);
                            6 > b ? this.modules[b][8] = e : 8 > b ? this.modules[b + 1][8] = e : this.modules[this.moduleCount - 15 + b][8] = e
                        }
                        for (b = 0; 15 > b; b++) e = !a && 1 == (d >> b & 1), 8 > b ? this.modules[8][this.moduleCount -
                        b - 1] = e : 9 > b ? this.modules[8][15 - b - 1 + 1] = e : this.modules[8][15 - b - 1] = e;
                        this.modules[this.moduleCount - 8][8] = !a
                    }, mapData: function (a, c) {
                        for (var d = -1, b = this.moduleCount - 1, e = 7, f = 0, i = this.moduleCount - 1; 0 < i; i -= 2) for (6 == i && i--; ;) {
                            for (var g = 0; 2 > g; g++) if (null == this.modules[b][i - g]) {
                                var n = !1;
                                f < a.length && (n = 1 == (a[f] >>> e & 1));
                                j.getMask(c, b, i - g) && (n = !n);
                                this.modules[b][i - g] = n;
                                e--;
                                -1 == e && (f++, e = 7)
                            }
                            b += d;
                            if (0 > b || this.moduleCount <= b) {
                                b -= d;
                                d = -d;
                                break
                            }
                        }
                    }
                };
                o.PAD0 = 236;
                o.PAD1 = 17;
                o.createData = function (a, c, d) {
                    for (var c = p.getRSBlocks(a,
                        c), b = new t, e = 0; e < d.length; e++) {
                        var f = d[e];
                        b.put(f.mode, 4);
                        b.put(f.getLength(), j.getLengthInBits(f.mode, a));
                        f.write(b)
                    }
                    for (e = a = 0; e < c.length; e++) a += c[e].dataCount;
                    if (b.getLengthInBits() > 8 * a) throw Error("code length overflow. (" + b.getLengthInBits() + ">" + 8 * a + ")");
                    for (b.getLengthInBits() + 4 <= 8 * a && b.put(0, 4); 0 != b.getLengthInBits() % 8;) b.putBit(!1);
                    for (; !(b.getLengthInBits() >= 8 * a);) {
                        b.put(o.PAD0, 8);
                        if (b.getLengthInBits() >= 8 * a) break;
                        b.put(o.PAD1, 8)
                    }
                    return o.createBytes(b, c)
                };
                o.createBytes = function (a, c) {
                    for (var d =
                        0, b = 0, e = 0, f = Array(c.length), i = Array(c.length), g = 0; g < c.length; g++) {
                        var n = c[g].dataCount, h = c[g].totalCount - n, b = Math.max(b, n), e = Math.max(e, h);
                        f[g] = Array(n);
                        for (var k = 0; k < f[g].length; k++) f[g][k] = 255 & a.buffer[k + d];
                        d += n;
                        k = j.getErrorCorrectPolynomial(h);
                        n = (new q(f[g], k.getLength() - 1)).mod(k);
                        i[g] = Array(k.getLength() - 1);
                        for (k = 0; k < i[g].length; k++) h = k + n.getLength() - i[g].length, i[g][k] = 0 <= h ? n.get(h) : 0
                    }
                    for (k = g = 0; k < c.length; k++) g += c[k].totalCount;
                    d = Array(g);
                    for (k = n = 0; k < b; k++) for (g = 0; g < c.length; g++) k < f[g].length &&
                    (d[n++] = f[g][k]);
                    for (k = 0; k < e; k++) for (g = 0; g < c.length; g++) k < i[g].length && (d[n++] = i[g][k]);
                    return d
                };
                s = 4;
                for (var j = {
                    PATTERN_POSITION_TABLE: [[], [6, 18], [6, 22], [6, 26], [6, 30], [6, 34], [6, 22, 38], [6, 24, 42], [6, 26, 46], [6, 28, 50], [6, 30, 54], [6, 32, 58], [6, 34, 62], [6, 26, 46, 66], [6, 26, 48, 70], [6, 26, 50, 74], [6, 30, 54, 78], [6, 30, 56, 82], [6, 30, 58, 86], [6, 34, 62, 90], [6, 28, 50, 72, 94], [6, 26, 50, 74, 98], [6, 30, 54, 78, 102], [6, 28, 54, 80, 106], [6, 32, 58, 84, 110], [6, 30, 58, 86, 114], [6, 34, 62, 90, 118], [6, 26, 50, 74, 98, 122], [6, 30, 54, 78, 102, 126], [6, 26, 52,
                        78, 104, 130], [6, 30, 56, 82, 108, 134], [6, 34, 60, 86, 112, 138], [6, 30, 58, 86, 114, 142], [6, 34, 62, 90, 118, 146], [6, 30, 54, 78, 102, 126, 150], [6, 24, 50, 76, 102, 128, 154], [6, 28, 54, 80, 106, 132, 158], [6, 32, 58, 84, 110, 136, 162], [6, 26, 54, 82, 110, 138, 166], [6, 30, 58, 86, 114, 142, 170]],
                    G15: 1335,
                    G18: 7973,
                    G15_MASK: 21522,
                    getBCHTypeInfo: function (a) {
                        for (var c = a << 10; 0 <= j.getBCHDigit(c) - j.getBCHDigit(j.G15);) c ^= j.G15 << j.getBCHDigit(c) - j.getBCHDigit(j.G15);
                        return (a << 10 | c) ^ j.G15_MASK
                    },
                    getBCHTypeNumber: function (a) {
                        for (var c = a << 12; 0 <= j.getBCHDigit(c) -
                        j.getBCHDigit(j.G18);) c ^= j.G18 << j.getBCHDigit(c) - j.getBCHDigit(j.G18);
                        return a << 12 | c
                    },
                    getBCHDigit: function (a) {
                        for (var c = 0; 0 != a;) c++, a >>>= 1;
                        return c
                    },
                    getPatternPosition: function (a) {
                        return j.PATTERN_POSITION_TABLE[a - 1]
                    },
                    getMask: function (a, c, d) {
                        switch (a) {
                            case 0:
                                return 0 == (c + d) % 2;
                            case 1:
                                return 0 == c % 2;
                            case 2:
                                return 0 == d % 3;
                            case 3:
                                return 0 == (c + d) % 3;
                            case 4:
                                return 0 == (Math.floor(c / 2) + Math.floor(d / 3)) % 2;
                            case 5:
                                return 0 == c * d % 2 + c * d % 3;
                            case 6:
                                return 0 == (c * d % 2 + c * d % 3) % 2;
                            case 7:
                                return 0 == (c * d % 3 + (c + d) % 2) % 2;
                            default:
                                throw Error("bad maskPattern:" +
                                    a);
                        }
                    },
                    getErrorCorrectPolynomial: function (a) {
                        for (var c = new q([1], 0), d = 0; d < a; d++) c = c.multiply(new q([1, l.gexp(d)], 0));
                        return c
                    },
                    getLengthInBits: function (a, c) {
                        if (1 <= c && 10 > c) switch (a) {
                            case 1:
                                return 10;
                            case 2:
                                return 9;
                            case s:
                                return 8;
                            case 8:
                                return 8;
                            default:
                                throw Error("mode:" + a);
                        } else if (27 > c) switch (a) {
                            case 1:
                                return 12;
                            case 2:
                                return 11;
                            case s:
                                return 16;
                            case 8:
                                return 10;
                            default:
                                throw Error("mode:" + a);
                        } else if (41 > c) switch (a) {
                            case 1:
                                return 14;
                            case 2:
                                return 13;
                            case s:
                                return 16;
                            case 8:
                                return 12;
                            default:
                                throw Error("mode:" +
                                    a);
                        } else throw Error("type:" + c);
                    },
                    getLostPoint: function (a) {
                        for (var c = a.getModuleCount(), d = 0, b = 0; b < c; b++) for (var e = 0; e < c; e++) {
                            for (var f = 0, i = a.isDark(b, e), g = -1; 1 >= g; g++) if (!(0 > b + g || c <= b + g)) for (var h = -1; 1 >= h; h++) 0 > e + h || c <= e + h || 0 == g && 0 == h || i == a.isDark(b + g, e + h) && f++;
                            5 < f && (d += 3 + f - 5)
                        }
                        for (b = 0; b < c - 1; b++) for (e = 0; e < c - 1; e++) if (f = 0, a.isDark(b, e) && f++, a.isDark(b + 1, e) && f++, a.isDark(b, e + 1) && f++, a.isDark(b + 1, e + 1) && f++, 0 == f || 4 == f) d += 3;
                        for (b = 0; b < c; b++) for (e = 0; e < c - 6; e++) a.isDark(b, e) && !a.isDark(b, e + 1) && a.isDark(b, e +
                            2) && a.isDark(b, e + 3) && a.isDark(b, e + 4) && !a.isDark(b, e + 5) && a.isDark(b, e + 6) && (d += 40);
                        for (e = 0; e < c; e++) for (b = 0; b < c - 6; b++) a.isDark(b, e) && !a.isDark(b + 1, e) && a.isDark(b + 2, e) && a.isDark(b + 3, e) && a.isDark(b + 4, e) && !a.isDark(b + 5, e) && a.isDark(b + 6, e) && (d += 40);
                        for (e = f = 0; e < c; e++) for (b = 0; b < c; b++) a.isDark(b, e) && f++;
                        a = Math.abs(100 * f / c / c - 50) / 5;
                        return d + 10 * a
                    }
                }, l = {
                    glog: function (a) {
                        if (1 > a) throw Error("glog(" + a + ")");
                        return l.LOG_TABLE[a]
                    }, gexp: function (a) {
                        for (; 0 > a;) a += 255;
                        for (; 256 <= a;) a -= 255;
                        return l.EXP_TABLE[a]
                    }, EXP_TABLE: Array(256),
                    LOG_TABLE: Array(256)
                }, m = 0; 8 > m; m++) l.EXP_TABLE[m] = 1 << m;
                for (m = 8; 256 > m; m++) l.EXP_TABLE[m] = l.EXP_TABLE[m - 4] ^ l.EXP_TABLE[m - 5] ^ l.EXP_TABLE[m - 6] ^ l.EXP_TABLE[m - 8];
                for (m = 0; 255 > m; m++) l.LOG_TABLE[l.EXP_TABLE[m]] = m;
                q.prototype = {
                    get: function (a) {
                        return this.num[a]
                    }, getLength: function () {
                        return this.num.length
                    }, multiply: function (a) {
                        for (var c = Array(this.getLength() + a.getLength() - 1), d = 0; d < this.getLength(); d++) for (var b = 0; b < a.getLength(); b++) c[d + b] ^= l.gexp(l.glog(this.get(d)) + l.glog(a.get(b)));
                        return new q(c, 0)
                    }, mod: function (a) {
                        if (0 >
                            this.getLength() - a.getLength()) return this;
                        for (var c = l.glog(this.get(0)) - l.glog(a.get(0)), d = Array(this.getLength()), b = 0; b < this.getLength(); b++) d[b] = this.get(b);
                        for (b = 0; b < a.getLength(); b++) d[b] ^= l.gexp(l.glog(a.get(b)) + c);
                        return (new q(d, 0)).mod(a)
                    }
                };
                p.RS_BLOCK_TABLE = [[1, 26, 19], [1, 26, 16], [1, 26, 13], [1, 26, 9], [1, 44, 34], [1, 44, 28], [1, 44, 22], [1, 44, 16], [1, 70, 55], [1, 70, 44], [2, 35, 17], [2, 35, 13], [1, 100, 80], [2, 50, 32], [2, 50, 24], [4, 25, 9], [1, 134, 108], [2, 67, 43], [2, 33, 15, 2, 34, 16], [2, 33, 11, 2, 34, 12], [2, 86, 68], [4, 43, 27],
                    [4, 43, 19], [4, 43, 15], [2, 98, 78], [4, 49, 31], [2, 32, 14, 4, 33, 15], [4, 39, 13, 1, 40, 14], [2, 121, 97], [2, 60, 38, 2, 61, 39], [4, 40, 18, 2, 41, 19], [4, 40, 14, 2, 41, 15], [2, 146, 116], [3, 58, 36, 2, 59, 37], [4, 36, 16, 4, 37, 17], [4, 36, 12, 4, 37, 13], [2, 86, 68, 2, 87, 69], [4, 69, 43, 1, 70, 44], [6, 43, 19, 2, 44, 20], [6, 43, 15, 2, 44, 16], [4, 101, 81], [1, 80, 50, 4, 81, 51], [4, 50, 22, 4, 51, 23], [3, 36, 12, 8, 37, 13], [2, 116, 92, 2, 117, 93], [6, 58, 36, 2, 59, 37], [4, 46, 20, 6, 47, 21], [7, 42, 14, 4, 43, 15], [4, 133, 107], [8, 59, 37, 1, 60, 38], [8, 44, 20, 4, 45, 21], [12, 33, 11, 4, 34, 12], [3, 145, 115, 1, 146,
                        116], [4, 64, 40, 5, 65, 41], [11, 36, 16, 5, 37, 17], [11, 36, 12, 5, 37, 13], [5, 109, 87, 1, 110, 88], [5, 65, 41, 5, 66, 42], [5, 54, 24, 7, 55, 25], [11, 36, 12], [5, 122, 98, 1, 123, 99], [7, 73, 45, 3, 74, 46], [15, 43, 19, 2, 44, 20], [3, 45, 15, 13, 46, 16], [1, 135, 107, 5, 136, 108], [10, 74, 46, 1, 75, 47], [1, 50, 22, 15, 51, 23], [2, 42, 14, 17, 43, 15], [5, 150, 120, 1, 151, 121], [9, 69, 43, 4, 70, 44], [17, 50, 22, 1, 51, 23], [2, 42, 14, 19, 43, 15], [3, 141, 113, 4, 142, 114], [3, 70, 44, 11, 71, 45], [17, 47, 21, 4, 48, 22], [9, 39, 13, 16, 40, 14], [3, 135, 107, 5, 136, 108], [3, 67, 41, 13, 68, 42], [15, 54, 24, 5, 55, 25], [15,
                        43, 15, 10, 44, 16], [4, 144, 116, 4, 145, 117], [17, 68, 42], [17, 50, 22, 6, 51, 23], [19, 46, 16, 6, 47, 17], [2, 139, 111, 7, 140, 112], [17, 74, 46], [7, 54, 24, 16, 55, 25], [34, 37, 13], [4, 151, 121, 5, 152, 122], [4, 75, 47, 14, 76, 48], [11, 54, 24, 14, 55, 25], [16, 45, 15, 14, 46, 16], [6, 147, 117, 4, 148, 118], [6, 73, 45, 14, 74, 46], [11, 54, 24, 16, 55, 25], [30, 46, 16, 2, 47, 17], [8, 132, 106, 4, 133, 107], [8, 75, 47, 13, 76, 48], [7, 54, 24, 22, 55, 25], [22, 45, 15, 13, 46, 16], [10, 142, 114, 2, 143, 115], [19, 74, 46, 4, 75, 47], [28, 50, 22, 6, 51, 23], [33, 46, 16, 4, 47, 17], [8, 152, 122, 4, 153, 123], [22, 73, 45,
                        3, 74, 46], [8, 53, 23, 26, 54, 24], [12, 45, 15, 28, 46, 16], [3, 147, 117, 10, 148, 118], [3, 73, 45, 23, 74, 46], [4, 54, 24, 31, 55, 25], [11, 45, 15, 31, 46, 16], [7, 146, 116, 7, 147, 117], [21, 73, 45, 7, 74, 46], [1, 53, 23, 37, 54, 24], [19, 45, 15, 26, 46, 16], [5, 145, 115, 10, 146, 116], [19, 75, 47, 10, 76, 48], [15, 54, 24, 25, 55, 25], [23, 45, 15, 25, 46, 16], [13, 145, 115, 3, 146, 116], [2, 74, 46, 29, 75, 47], [42, 54, 24, 1, 55, 25], [23, 45, 15, 28, 46, 16], [17, 145, 115], [10, 74, 46, 23, 75, 47], [10, 54, 24, 35, 55, 25], [19, 45, 15, 35, 46, 16], [17, 145, 115, 1, 146, 116], [14, 74, 46, 21, 75, 47], [29, 54, 24, 19,
                        55, 25], [11, 45, 15, 46, 46, 16], [13, 145, 115, 6, 146, 116], [14, 74, 46, 23, 75, 47], [44, 54, 24, 7, 55, 25], [59, 46, 16, 1, 47, 17], [12, 151, 121, 7, 152, 122], [12, 75, 47, 26, 76, 48], [39, 54, 24, 14, 55, 25], [22, 45, 15, 41, 46, 16], [6, 151, 121, 14, 152, 122], [6, 75, 47, 34, 76, 48], [46, 54, 24, 10, 55, 25], [2, 45, 15, 64, 46, 16], [17, 152, 122, 4, 153, 123], [29, 74, 46, 14, 75, 47], [49, 54, 24, 10, 55, 25], [24, 45, 15, 46, 46, 16], [4, 152, 122, 18, 153, 123], [13, 74, 46, 32, 75, 47], [48, 54, 24, 14, 55, 25], [42, 45, 15, 32, 46, 16], [20, 147, 117, 4, 148, 118], [40, 75, 47, 7, 76, 48], [43, 54, 24, 22, 55, 25], [10,
                        45, 15, 67, 46, 16], [19, 148, 118, 6, 149, 119], [18, 75, 47, 31, 76, 48], [34, 54, 24, 34, 55, 25], [20, 45, 15, 61, 46, 16]];
                p.getRSBlocks = function (a, c) {
                    var d = p.getRsBlockTable(a, c);
                    if (void 0 == d) throw Error("bad rs block @ typeNumber:" + a + "/errorCorrectLevel:" + c);
                    for (var b = d.length / 3, e = [], f = 0; f < b; f++) for (var h = d[3 * f + 0], g = d[3 * f + 1], j = d[3 * f + 2], l = 0; l < h; l++) e.push(new p(g, j));
                    return e
                };
                p.getRsBlockTable = function (a, c) {
                    switch (c) {
                        case 1:
                            return p.RS_BLOCK_TABLE[4 * (a - 1) + 0];
                        case 0:
                            return p.RS_BLOCK_TABLE[4 * (a - 1) + 1];
                        case 3:
                            return p.RS_BLOCK_TABLE[4 *
                            (a - 1) + 2];
                        case 2:
                            return p.RS_BLOCK_TABLE[4 * (a - 1) + 3]
                    }
                };
                t.prototype = {
                    get: function (a) {
                        return 1 == (this.buffer[Math.floor(a / 8)] >>> 7 - a % 8 & 1)
                    }, put: function (a, c) {
                        for (var d = 0; d < c; d++) this.putBit(1 == (a >>> c - d - 1 & 1))
                    }, getLengthInBits: function () {
                        return this.length
                    }, putBit: function (a) {
                        var c = Math.floor(this.length / 8);
                        this.buffer.length <= c && this.buffer.push(0);
                        a && (this.buffer[c] |= 128 >>> this.length % 8);
                        this.length++
                    }
                };
                "string" === typeof h && (h = {text: h});
                h = r.extend({}, {
                    render: "canvas", width: 256, height: 256, typeNumber: -1,
                    correctLevel: 2, background: "#ffffff", foreground: "#000000"
                }, h);
                return this.each(function () {
                    var a;
                    if ("canvas" == h.render) {
                        a = new o(h.typeNumber, h.correctLevel);
                        a.addData(h.text);
                        a.make();
                        var c = document.createElement("canvas");
                        c.width = h.width;
                        c.height = h.height;
                        for (var d = c.getContext("2d"), b = h.width / a.getModuleCount(), e = h.height / a.getModuleCount(), f = 0; f < a.getModuleCount(); f++) for (var i = 0; i < a.getModuleCount(); i++) {
                            d.fillStyle = a.isDark(f, i) ? h.foreground : h.background;
                            var g = Math.ceil((i + 1) * b) - Math.floor(i * b),
                                j = Math.ceil((f + 1) * b) - Math.floor(f * b);
                            d.fillRect(Math.round(i * b), Math.round(f * e), g, j)
                        }
                    } else {
                        a = new o(h.typeNumber, h.correctLevel);
                        a.addData(h.text);
                        a.make();
                        c = r("<table></table>").css("width", h.width + "px").css("height", h.height + "px").css("border", "0px").css("border-collapse", "collapse").css("background-color", h.background);
                        d = h.width / a.getModuleCount();
                        b = h.height / a.getModuleCount();
                        for (e = 0; e < a.getModuleCount(); e++) {
                            f = r("<tr></tr>").css("height", b + "px").appendTo(c);
                            for (i = 0; i < a.getModuleCount(); i++) r("<td></td>").css("width",
                                d + "px").css("background-color", a.isDark(e, i) ? h.foreground : h.background).appendTo(f)
                        }
                    }
                    a = c;
                    jQuery(a).appendTo(this)
                })
            },
            //创建本地的消息任务id
            // 30位
            //用于：服务器未响应则重复发送
            createMsgTaskId : function () {
                return new Date().getTime().toString() + this.makeRadom(17);
            },
            checkHtml : function (htmlStr) {
                var reg = /<[^>]+>/g;
                return reg.test(htmlStr);
            },
            //转义
            // '&': '&amp;', 如果输入内容是网址，里面带有'&'字符，则转义返回的结果会无法被识别为网址
            html2Escape : function (sHtml) {
                if (!backObj.isString(sHtml)) return sHtml;
                if (!sHtml) return '';
                if (!sHtml.replace) return sHtml;
                return sHtml.replace(/[<>"]/g, function (c) {
                    return {'<': '&lt;', '>': '&gt;', '"': '&quot;'}[c];
                });
            },
            //
            escape2Html : function (str) {
                if (!backObj.isString(str)) return str;
                var arrEntities = {'lt': '<', 'gt': '>', 'nbsp': ' ', 'amp': '&', 'quot': '"'};
                return str.replace(/&(lt|gt|nbsp|amp|quot);/ig, function (all, t) {
                    return arrEntities[t];
                });
            },
            keepBr : function (str) {
                if (!backObj.isString(str)) return str; //数字不用替换
                str = str.replace(/\r\n/ig, '<br>');
                str = str.replace(/\n/ig, '<br>');
                return str.replace(/&lt;br&gt;/ig, '<br>');
            },
            removeBr : function (str) {
                if (!backObj.isString(str)) return str; //数字不用替换
                if (backObj.strObj.isNumber(str)) str = str.toString();
                str = str.replace(/<br>/ig, "\r\n");
                str = str.replace(/<br>/ig, "\n");
                str = str.replace(/<\/div>/ig, "");
                str = str.replace(/<div>/ig, "\n");
                return str;
            },

            isJSON : function(str) {
                if (typeof str == 'string') {
                    try {
                        var obj=JSON.parse(str);
                        if(typeof obj == 'object' && obj ){
                            return true;
                        }else{
                            return false;
                        }

                    } catch(e) {
                        console.log('error：'+str+'!!!'+e);
                        return false;
                    }
                }
                console.log('It is not a string!')
            },

            //取信息长度
            getMsgLen : function (str) {
                if (str == null) return 0;
                if (typeof str != "string") {
                    str += "";
                }
                return str.replace(/[^\x00-\xff]/g, "01").length;
            },
            //字符表情转码unicode
            emojiToUnicode : function (str) {
                if (!str) return '';
                var res = [];
                for (var i = 0; i < str.length; i++) {
                    res[i] = ("00" + str.charCodeAt(i).toString(16)).slice(-4);
                }
                return "\\u" + res.join("\\u");
            },
            //unicode转字符表情
            unicodeToEmoji : function (str) {
                str = str.split('\\u');
                var em = '';
                for (var i = 0; i < str.length; i++) {
                    em += String.fromCharCode(parseInt(str[i], 16).toString(10));
                }
                return em;
            },
            //过滤所有标签 只保留[img]
            toEmojiImg : function (str) {
                var regImg = /<img\s([^>]*)data-code=(['"])([^'"]+)(['"])([^>]*)>/gi;
                str = str.replace(regImg, '[$3]'); //替换为：[EM^123]
                var regBr = /<br([^>]*)>/gi;
                str = str.replace(regBr, '[br]'); //[br]
                str = str.replace(regBr, '[br$1]'); //[br]
                var regBrBack = /\[br]/gi;
                str = str.replace(regBrBack, '<br>');
                return str;
            },
            //过滤所有标签
            clearTags : function (str) {
                str = str.replace(/<[^>]*>/gi, ''); //其他标签全部剔除
                return str;
            },
            //过滤所有标签 只保留 im.emoji
            keepAllImEmoji : function (str) {
                var regImg = /<span class="im_emoji em\d+" data-value="(\d+)"([^>]*)><\/span>/gi;
                str = str.replace(regImg, '[em:$1]'); //[img]
                return str;
            },
            //还原 im.emoji
            backAllImEmoji : function (str, showBig) {
                var regImg = /\[em:(\d+)\]/gi;
                var addClass = '';
                if(showBig) {
                    var num = str.match(regImg) ? str.match(regImg).length: 0;
                    if(num ==0) return str;
                    if(num==1) {
                        addClass = ' lg';
                    } else if(num ==2 ) {
                        addClass = ' md';
                    }
                }
                str = str.replace(regImg, '<span class="im_emoji '+addClass+' em$1" data-value="$1" contenteditable="false"><\/span>'); //[img]
                return str;
            },
            //时间戳转文本，并去除秒
            noSecond : function (albTime) {
                var timeStr = new Date(parseInt(albTime) * 1000).toLocaleString().replace(/[^0-9^\^:^\s/]/g, '').replace(/\//g, '-');
                var timeAy = timeStr.split(':');
                timeAy.splice(-1, 1);
                return timeAy.join(':');
            },
            //字符串是否包含字符串
            hasStr : function (str, subStr) {
                var reg = eval("/" + subStr + "/ig");
                return reg.test(str);
            },
            //判断对象为空
            objIsNull : function (obj) {
                return Object.keys(obj).length == 0;
            },
            //下载blob文件 源于webogram-master
            downloadFile :function (blob, mimeType, fileName) {
                var isSafari = 'safari' in window;
                var safariVersion = parseFloat(isSafari && (navigator.userAgent.match(/Version\/(\d+\.\d+).* Safari/) || [])[1]);
                var safariWithDownload = isSafari && safariVersion >= 11.0;
                if (window.navigator && navigator.msSaveBlob !== undefined) {
                    window.navigator.msSaveBlob(blob, fileName);
                    return false
                }
                if (window.navigator && navigator.getDeviceStorage) {
                    var storageName = 'sdcard';
                    var subdir = 'telegram/';
                    switch (mimeType.split('/')[0]) {
                        case 'video':
                            storageName = 'videos';
                            break;
                        case 'audio':
                            storageName = 'music';
                            break;
                        case 'image':
                            storageName = 'pictures';
                            break;
                    }
                    var deviceStorage = navigator.getDeviceStorage(storageName);
                    var request = deviceStorage.addNamed(blob, subdir + fileName);
                    request.onsuccess = function () {
                        console.log('Device storage save result', this.result)
                    };
                    request.onerror = function () {
                    };
                    return;
                }
                var anchor = document.createElementNS('http://www.w3.org/1999/xhtml', 'a');
                anchor.href = blob;
                if (!safariWithDownload) {
                    anchor.target = '_blank';
                }
                anchor.download = fileName;
                if (anchor.dataset) {
                    anchor.dataset.downloadurl = ['video/quicktime', fileName, blob].join(':')
                }
                $(anchor).css({position: 'absolute', top: 1, left: 1}).appendTo('body');
                try {
                    var clickEvent = document.createEvent('MouseEvents');
                    clickEvent.initMouseEvent(
                        'click', true, false, window, 0, 0, 0, 0, 0
                        , false, false, false, false, 0, null
                    );
                    anchor.dispatchEvent(clickEvent);
                } catch (e) {
                    console.error('Download click error', e);
                    try {
                        anchor[0].click();
                    } catch (e) {
                        window.open(blob, '_blank');
                    }
                }
                setTimeout(function () {
                    $(anchor).remove()
                }, 100);
            },

            //base64文件转bloburl
            base64ToBlob : function (base64) {
                var data = backObj.dataURLtoBlob(base64);
                var blob = new Blob([data], {}
                );
                return window.URL.createObjectURL(blob);
            },
            formatSize: function( size, pointLength, units ) {
                var unit;
                units = units || [ 'B', 'K', 'M', 'G', 'TB' ];
                while ( (unit = units.shift()) && size > 1024 ) {
                    size = size / 1024;
                }
                return (unit === 'B' ? size : size.toFixed( pointLength || 2 )) +
                    unit;
            },

            //进度上传请求
            ajaxXhr : function (options) {
                options = options || {};
                var successKey = options['successKey'] || options['success_key'] || null;
                var successVal = options['successVal'] || options['success_value'] || null;
                var successFunc = options['successFunc'] || options['success_func'] || null;
                if(!$.isArray(successVal)) {
                    if(!successVal) successVal = '1';
                    if(backObj.isString(successVal)) {
                        successVal = successVal.split(',');
                    } else {
                        successVal = successVal.toString().split(',');
                    }
                }
                var postUrl = options['post_url'] || options['url'] || '';
                var postData = options['postData'] || options['post_data'] || null;
                var xhr = options['xhr'];
                var errFunc = options['errFunc'] || options['err_func'] ||  options['errorFunc'] ||  options['errorFunc'] || null;
                var formData = new FormData();
                $.each(postData, function (k_, v_) {
                    formData.append(k_, v_);
                });
                return $.ajax(
                    {
                        url: postUrl,
                        type:"post",
                        data: formData,
                        dataType:"json",
                        cache: false,
                        xhr: xhr,
                        contentType:false,
                        processData:false, //设置为true的时候,jquery ajax 提交的时候不会序列化 data，而是直接使用data
                        success:function(data){
                            if(successVal && successKey && (backObj.isUndefined(data[successKey]) || backObj.strObj.strInArray(data[successKey], successVal) == -1)) {
                                if(errFunc) errFunc(data);
                            } else {
                                //可能这里会执行关闭所有（最新）窗口，所以要提前执行，防止将默认的提示语误关。
                                if(successFunc) {
                                    if(backObj.isString(successFunc)) {
                                        eval(successFunc);
                                    } else {
                                        successFunc(data);
                                    }
                                }
                            }

                        },
                        complete:function(XMLHttpRequest,textStatus){
                        },
                        error:function(res){
                            if(res.msg) {
                                lrBox.msg(res.msg);
                            }
                        }
                    }
                ).done(function () {
                }).fail(function () {}) ;
            },
            //获取图片的封面小图
            getMiniSize : function (sourceWidth, sourceHeight, maxWidth, maxHeight) {
                maxWidth = maxWidth || 100;
                maxHeight = maxHeight || 120;
                var miniWidth = 0;
                var miniHeight = 0;
                var bili =  1;
                if (sourceWidth <= sourceHeight) {
                    bili = sourceHeight / sourceWidth;
                    if (sourceHeight > maxHeight) {
                        miniHeight = maxHeight;
                        miniWidth = miniHeight / bili;
                    } else if (sourceWidth > maxWidth) {
                        miniWidth = maxWidth;
                        miniHeight = miniWidth * bili;
                    } else {
                        miniWidth = sourceWidth;
                        miniHeight = sourceHeight;
                    }
                } else {
                    bili = sourceWidth / sourceHeight;
                    if (sourceWidth > maxWidth) {
                        miniWidth = maxWidth;
                        miniHeight = miniWidth / bili;
                    } else if (sourceHeight > maxHeight) {
                        miniHeight = maxHeight;
                        miniWidth = miniHeight * bili;
                    }   else {
                        miniWidth = sourceWidth;
                        miniHeight = sourceHeight;
                    }
                }
                return [parseInt(miniWidth), parseInt(miniHeight)];
            },
            //分片上传base64
            uploadByPiece : function(fileTypeTitle, uploadUrl, fileBobObj, base64Data, diyOpt) {
                var canceled = false;
                var taskId = backObj.strObj.createMsgTaskId(); //给文件的分片分配一个任务id
                diyOpt = diyOpt || null;
                var fileHash = diyOpt.fileHash || '';
                var opt = {
                    successKey: 'state',
                    successVal: '0',
                    successFunc: null,
                    errFunc: null,
                    cancelBtn: null,
                    cancelFunc: null,
                    retryBtn: null,
                    postData: {
                        taskId: taskId,
                    },
                    finishData: {
                        mime: fileBobObj.type,
                        fileHash: fileHash,
                        fileName:  fileBobObj.name || '', //文件名
                    },
                };
                if (diyOpt) {
                    //防止覆盖opt.finishData 先手动提取
                    var finishData = $.extend({}, opt.finishData, diyOpt.finishData);
                    opt = $.extend({}, opt, diyOpt);
                    opt.finishData = finishData;
                }
                var successFunc = opt.successFunc || null;
                var listenObj = $('' +
                    '<div>' +
                    '<div class="progress" style="height: 6px;">' +
                    '<div class="progress-bar" role="progressbar" style="width: 0%;">' +
                    '</div>' +
                    '</div>' +
                    '<div class="text-center">' +
                    '<a href="javascript:void(0);" target="_self" class="btn btn-xs btn-warning" style="margin-left: 10px;">'+ backObj.getLang('cancel') +'</a>' +
                    '</div>' +
                    '</div>');
                var listenWin = lrBox.msgView(fileTypeTitle + backObj.getLang('uploadingPleaseWait'), listenObj, 300, 200);
                var progressObj = listenObj.find('.progress-bar');
                var parentObj = progressObj.parent();
                var cancelBtn = listenObj.find('.btn-warning');
                var pz = {
                    "maxSize": 1024*1024*100, //文件大小限制100M
                    "pieceSize": 1024 * 80, //单个分片大小 单位k
                    "blocks":[], //分片数据集合
                    "retryMaxTime": 2, //异常请求时，重试的最大次数
                    "reset":function(){
                        this.blocks = [];
                    }
                };
                let xhrObj = [];
                var cancelFunc = function () {
                    listenWin.remove();
                    if(xhrObj.length) {
                        $.each(xhrObj, function (index, obj_) {
                            obj_.abort();
                        });
                    }
                    if (opt.cancelFunc) opt.cancelFunc();
                };
                //上传一个分片
                function uploadBlock(index) {
                    if(canceled==true) {
                        cancelFunc();
                        return;
                    }
                    var data_ = {
                        source: pz.blocks[index], //分片的内容
                        index:  index, //分片索引
                        pieceNum:  pz.blocks.length, //分片总数
                    };
                    if(opt.postData) {
                        data_ = $.extend({}, data_, opt.postData);
                    }
                    //完成时想提交的数据
                    if(opt.finishData && index== pz.blocks.length-1 ) {
                        data_ = $.extend({}, data_, opt.finishData);
                    }
                    //监听上传进度
                    var xhrOnProgress = function (fun) {
                        xhrOnProgress.onprogress = fun; //绑定监听
                        //使用闭包实现监听绑
                        return function () {
                            //通过$.ajaxSettings.xhr();获得XMLHttpRequest对象
                            var xhr = $.ajaxSettings.xhr();
                            //判断监听函数是否为函数
                            if (typeof xhrOnProgress.onprogress !== 'function')
                                return xhr;
                            //如果有监听函数并且xhr对象支持绑定时就把监听函数绑定上去
                            if (xhrOnProgress.onprogress && xhr.upload) {
                                xhr.upload.onprogress = xhrOnProgress.onprogress;
                            }
                            return xhr;
                        }
                    };
                    xhrObj.push(this_post({
                        url: uploadUrl,
                        postData: data_,
                        xhr: xhrOnProgress(function (e) {
                            var contentWidth = parentObj.outerWidth();
                            if(contentWidth ==0 ) {
                                console.log('contentWidth没有宽度', parentObj);
                                return;
                            }
                            var oneChuckWidth = contentWidth / pz.blocks.length;
                            //第1份是宽度从0开始增，第2份开始，宽度要从前面2-1份的宽度开始递增
                            var percent = e.loaded / e.total;
                            percent = percent.toFixed(4);
                            var chuckWidth = ((index) * oneChuckWidth) + (percent * oneChuckWidth);
                            progressObj.css('width', chuckWidth);
                        }),
                        successFunc: function (res) {
                            //上传所有分片完成
                            if(res.data.finish == 1 || index>=pz.blocks.length-1) {
                                listenWin.remove();
                                successFunc(res);
                                return;
                            }
                            var newIndex = index + 1;
                            uploadBlock(newIndex);
                        },
                        errFunc: function (res) {
                            listenWin.remove();
                            if(res.msg) {
                                lrBox.msg(res.msg);
                            }
                        }
                    }));
                }

                function uploadBlob() {
                    pz.reset(fileBobObj.name|| 'blob.png');
                    while(base64Data.length >0){
                        pz.blocks.push(base64Data.substr(0, pz.pieceSize));
                        base64Data   = base64Data.substr(pz.pieceSize);
                    }
                    uploadBlock(0);
                }
                uploadBlob();
                //绑定取消上传事件
                if(cancelBtn) {
                    cancelBtn.off('click').click(function () {
                        canceled = true;
                        cancelFunc();
                    })
                }
            },

            //生成随机字符
            makeRadom :function (num) {
                //创建26个字母数组
                var arr = 'abcdefghijklmnopqrstuvwxyz0123456789'.split('');
                var val = '';
                for(var i=0;i<num; i++){
                    val += arr[Math.floor(Math.random() * 36)];
                }
                return val;
            },

            //获取回复内容
            getReplyContent : function (replyObj) {
                let type_ = replyObj.msgtype;
                let url_ = replyObj.url;
                let fileType_ = replyObj.fileType;
                let content_;
                content_ = replyObj.content;
                if (msgRules.contentTypeData.isImg(type_) || msgRules.contentTypeData.isSourceImg(type_)) {
                    return '<div class="image_cover" style="background-image: url(' + backObj.strObj.getImageUrl(url_, true) + ')"></div><span class="icon picture"></span>' +
                        '<span class="bottomText">' + backObj.getLang('image') + '</span>';
                } else if (msgRules.contentTypeData.isVideo(type_)) {
                    if (url_.indexOf('.cover') == -1) url_ += '.cover';
                    return '<div class="video_cover" style="background-image: url(' + backObj.strObj.getFileUrl(url_) + ')"></div>' +
                        '<span class="icon video"></span><span class="bottomText">' + backObj.getLang('video') + '</span>';
                } else if (msgRules.contentTypeData.isFile(type_)) {
                    var fileIconList = ['pdf', 'doc', 'docx', 'word', 'xls', 'xlsx', 'ppt', 'pptx', 'ppsx', 'txt', 'xml', 'jnt', 'zip', 'rar', 'arj', 'z'];
                    var fileIcon = fileType_ || content_.substr(content_.lastIndexOf('.') + 1);
                    if (fileIconList.indexOf(fileIcon) < 0) {
                        fileIcon = 'file';
                    }
                    return '<span class="icon ' + fileIcon + '"></span> ' + content_;
                } else if (msgRules.contentTypeData.isVoice(type_)) {
                    return '<span class="icon voice"></span> ' + content_;
                } else if (msgRules.contentTypeData.isVisiting(type_)) {
                    return '名片';
                } else if (msgRules.contentTypeData.isEmoticon(type_)) {
                    return '<img src="' + backObj.strObj.getFileUrl(url_) + '"  style="max-width: 50px;">';
                } else {
                    //web回复emoji时 web前端显示之前的 webReplyAppendHtml，拉取远程信息时 则是显示content
                    if (replyObj.webReplyAppendHtml) {
                        content_ = replyObj.webReplyAppendHtml;
                        content_ = backObj.strObj.escape2Html(content_);
                    } else {
                        if (content_) {
                            content_ = backObj.strObj.html2Escape(content_);
                            // console.log('content_', content_);
                            content_ = backObj.strObj.removeBr(content_);// css换行好了
                            content_ = Emoji.trans(content_);
                            content_ = strToUrl(content_);//替换链接
                            content_ = chatAreaObj.emojiObj.toEmoji(content_);//转emoji
                            content_ = '<div class="paddR78">' + content_ + '</div>';
                        } else {
                            content_ = '';
                        }
                    }


                    return content_;
                }
            },
            //用户昵称更新对象:单聊群聊
            userNicknameUpdateObj : {
                data: {},
                push: function (uid, dom) {
                    if(typeof this.data[uid] != 'undefined') {
                        this.data[uid].push(dom);
                    } else {
                        this.data[uid] = [dom];
                    }
                },
                update: function (uid, newStr) {
                    var listDom = this.data[uid] || [];
                    if(listDom.length) {
                        $.each(listDom, function (n, v) {
                            v.text(newStr);
                        });
                    }
                }
            },
            //打包表单数据成对象
            packetArrayToObj : function(postData, keyName, val) {
                var keyAll,key1;
                var reg_=/\[([^\]]+)\]/ig;
                var keyAbc,key_;
                keyAbc = keyName.match(reg_);//[a], [b], [c]
                key1 = keyName.split('[')[0];  //content[a][b][c]
                keyAll = [key1].concat(keyAbc);
                function diguiData($data, keys) {
                    key_ = keys.shift();
                    key_ = key_.replace(/(\[|\])/g, '');
                    if(typeof $data[key_] =='undefined') {
                        if(keys.length) {
                            $data[key_] = diguiData({}, keys);
                        } else {
                            $data[key_] = val;
                        }
                        return $data;
                    } else {
                        if(keys.length) {
                            $data[key_] = diguiData($data[key_], keys);
                        } else {
                            $data[key_] = [$data[key_]].concat([val]);
                        }
                        return $data;
                    }
                }

                return diguiData(postData, keyAll);
            },
            //获取多层数据的key
            getKeyFromData : function(data, key_) {
                if(key_.indexOf('.') !=-1) {
                    var array_ = key_.split('.');
                    var data_ = data[array_[0]] || {};
                    return backObj.strObj.getKeyFromData(data_, array_.slice(1).join('.'));
                }
                return data[key_] || '';
            },
            //移除数组成员
            removeArrayMember : function(array_, val) {
                if(!array_.length) return array_;
                var index = backObj.strObj.strInArray(val, array_);
                if (index > -1) {
                    array_.splice(index, 1);
                }
                return array_;
            },
            //时件转换
            timer : {
                toInt: function (valTime) {
                    var timeInt = new Date(valTime).getTime()/1000;
                    if(isNaN(timeInt)) return '';
                    return timeInt;
                },

                //取当前时间
                getTime : function () {
                    return Date.parse(new Date()) / 1000;
                },

                //秒数转时间 视频时长用到
                secondsToTime : function (seconds) {
                    if(seconds > 60) {
                        var mins = parseInt(seconds/60);
                        if(mins<10) {
                            mins = '0'+ mins;
                        }
                        var restSecond = seconds % 60;
                        if(restSecond>9) {
                            return mins + ':'+ (seconds % 60);
                        } else if(restSecond>0) {
                            return mins + ':0'+ (seconds % 60);
                        } else {
                            return mins + ':00';
                        }
                    } else {
                        if(seconds <10) {
                            return '00:0'+ seconds;
                        } else {
                            return '00:'+ seconds;
                        }
                    }
                },
                //取今天
                today : function () {
                    return Date.parse(new Date().toLocaleDateString()) / 1000;
                },
                //转日期
                todayInt : function (time_) {
                    if(time_.toString().length < 11) time_ = time_ * 1000;
                    var dates = new Date(time_);
                    var Y = dates.getFullYear();
                    var M = (dates.getMonth()+1 < 10 ? '0'+ (dates.getMonth()+1) : dates.getMonth()+1);
                    var D = dates.getDate();
                    var date_ = new Date(M +' '+ D +','+ Y);
                    return Date.parse(new Date(date_).toLocaleDateString()) / 1000;
                },
                //判断是否在24小时里
                in24Time : function CompareDate(dateTimeStamp) {
                    var result = true;
                    var oneDaySeconds = 3600 * 24;//一天的秒数
                    var now = this.getTime();
                    var passSeconds = now - dateTimeStamp; //时间差秒
                    if (passSeconds < 0) {
                        return result;
                    }
                    var days = passSeconds / oneDaySeconds;
                    if (days >= 1) {
                        result = false;
                    } else {
                        result = true;
                    }
                    return result;
                },
                //取当天日期时间戳
                // 减去日期subday
                getSubDayNumber: function (subDay) {
                    subDay = subDay || 0;
                    var time_ = this.today();
                    if (subDay > 0) {
                        time_ -= subDay * 24 * 3600;
                    }
                    return time_;
                },
                //加日期
                getAddDayNumber : function (addDay) {
                    addDay = addDay || 0;
                    var time_ = this.today();
                    if (addDay > 0) {
                        time_ += addDay * 24 * 3600;
                    }
                    return time_;
                },
                //计算之前时间的日期或时间 ：当天的只显示时间，昨天以前的显示日期+时间
                getTimeOrLastDate: function (time_) {
                    if(!time_) return '';
                    time_ = time_.toString().length < 11 ? time_ * 1000 : time_;
                    var todayNumber = this.getSubDayNumber();
                    var yester1DayNumber = this.getSubDayNumber(1);
                    var yester2DayNumber = this.getSubDayNumber(2);
                    var day = this.todayInt(parseInt(time_));
                    if (day == todayNumber) return this.getHourSec(time_);//今天的日期 显示时分
                    if (day == yester1DayNumber && lang != 'eng') return backObj.getLang('yesterday');
                    if (day == yester2DayNumber && lang != 'eng') return backObj.getLang('theDayBeforeYesterday');
                    //去年的要加年份
                    var lastYear = new Date(time_).getFullYear();
                    if(lastYear < new Date().getFullYear()) {
                        return lastYear + ' '+ this.checkMD(time_); //之前的显示日期
                    }
                    return this.checkMD(time_) + ' '+ this.getHourSec(time_); //之前的显示日期和时间
                },
                //计算未来时间的日期
                getFutureDate : function (time_) {
                    if(!time_) time_  = '';
                    var todayNumber = this.getAddDayNumber();
                    var tomorrowNumber = this.getAddDayNumber(1);
                    var tomorrow2Number = this.getAddDayNumber(2);
                    var day = this.todayInt(time_);
                    if (day == todayNumber) return backObj.getLang('today');//今天
                    if (day == tomorrowNumber ) return backObj.getLang('tomorrow');
                    if (day == tomorrow2Number) return backObj.getLang('theDayAfterTomorrow');
                    return  this.checkMD(time_ * 1000); //之前的显示日期
                },
                //取月日 (12/29)
                checkMD : function (date) {
                    if (date.toString().length == 10) date = date * 1000;
                    var myDate = date ? new Date(date) : new Date(),
                        d = myDate.getDate(),
                        m = myDate.getMonth() + 1;
                    if (parseInt(d) < 10) {
                        d = '0' + d;
                    }
                    if (parseInt(m) < 10) {
                        m = '0' + m;
                    }

                    var today = lang == 'eng' ? m + '-'+ d : m + '月' + d + '日';
                    return today;
                },
                //取时间 (12:30)
                getHourSec : function (date) {
                    if(!date) return '';
                    if (date.toString().length == 10) date = date * 1000;
                    var myDate = date ? new Date(date) : new Date(), h = myDate.getHours(), m = myDate.getMinutes();
                    if (h < 10) {
                        h = '0' + h;
                    }
                    if (m < 10) {
                        m = '0' + m;
                    }
                    return h + ':' + m;
                },
                //时间戳转文本
                timeToYMD:function (date){
                    var date = new Date(date*1000);//如果date为10位不需要乘1000
                    var Y = date.getFullYear() + '-';
                    var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
                    var D = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate()) + ' ';
                    var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
                    var m = (date.getMinutes() <10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
                    var s = (date.getSeconds() <10 ? '0' + date.getSeconds() : date.getSeconds());
                    return Y+M+D+h+m+s;
                },
                //时间戳转文本，并去除秒
                noSecond:function (albTime) {
                    var timeStr = new Date(parseInt(albTime) * 1000).toLocaleString().replace(/[^0-9^\^:^\s/]/g,'').replace(/\//g,'-');
                    var timeAy = timeStr.split(':');
                    timeAy.splice(-1,1);
                    return timeAy.join(':');
                },
            },
            //检索数组 文本类型：数字和字符都支持 不区分1和'1'
            strInArray : function(str, array_) {
                    var exist_ = -1;
                    $.each(array_, function (n, item_) {
                        if(item_ == str) {
                            exist_ = n;
                            return false; //break
                        }
                    });
                    return exist_;
                },
            //图片加随机尾巴
            urlAddRadom : function (url) {
                if(url.indexOf('?') == -1) {
                    url += '?r_=' + this.makeRadom(12);
                } else {
                    url += '&r_=' + this.makeRadom(12);
                }
                return url;
            },

            //是否纯数字
            isNumber : function( value ) {
                return /^[\+\-0-9.]+$/.test( value );
            }
        },

        tabPanel : function (opt) {
            let appendToTab = opt.appendToTab || null;
            let activeIndex = opt.activeIndex || 0;
            let sonClass = opt.sonClass || null;
            let sonWidthBy = opt.sonWidthBy || null;
            let click_ = opt.click || null;
            let panel = $('<div class="control_tab_line"></div>'); // style="width: 56px; transform: translateX(96px);"
            appendToTab.after(panel);
            let parentPos = appendToTab.parent().offset();
            if(!appendToTab.find('.'+ sonClass).eq(activeIndex)) {
                console.log('flashObj找不到子元素'+ activeIndex);
                return;
            }
            let currentItem = appendToTab.find('.'+ sonClass).eq(activeIndex);
            let tabPos = appendToTab.offset();
            var countTrans = function(index_) {
                let _item = appendToTab.find('.'+ sonClass).eq(index_);
                if(!_item) {
                    console.log('flashObj点击时找不到子元素'+ index_);
                    return;
                }
                let left_ = _item.offset().left - appendToTab.offset().left;
                if(sonWidthBy) {
                    let posLest = _item.outerWidth() - _item.find('.'+ sonWidthBy).outerWidth();
                    if(posLest>0) posLest = posLest/2;
                    if(posLest) left_ += posLest;
                }
                return left_;
            };
            let tabLeftSpace = appendToTab.parent().outerWidth() - appendToTab.outerWidth();
            if(tabLeftSpace >0) tabLeftSpace = tabLeftSpace /2;
            panel.css({
                'left': tabLeftSpace,
                'top': tabPos.top - parentPos.top + appendToTab.outerHeight(true),
                'width': sonWidthBy ? currentItem.find('.'+ sonWidthBy).outerWidth(): currentItem.outerWidth(),
                'transform': 'translateX('+ countTrans(activeIndex) +'px)',
            });
            if(click_) {
                appendToTab.find('.'+ sonClass).click(function (e) {
                    click_(e, $(this));
                    panel.css({
                        'width': sonWidthBy ? $(this).find('.'+ sonWidthBy).outerWidth(): $(this).outerWidth(),
                        'transform': 'translateX('+ countTrans($(this).index()) +'px)',
                    });
                });
            }
        },

        //opt: width/data
        rightMenu : function (obj, opt) {
            if (!opt) return;
            let clickLr = opt['clickBy'] || 'l';//l左键 r右键
            let clickLEven = clickLr =='l' ? 'click contextmenu':'contextmenu';
            obj.on(clickLEven, function (e) {
                e.preventDefault();
                e.stopPropagation();
                let clickKey = clickLr == 'l' ? [0, 2]: [2];
                let justShow = false;
                if (backObj.strObj.strInArray(e.button, clickKey) !=-1) {
                    // 初始化前执行
                    let before_ = opt['before'] || false;//初始化后自动执行的方法
                    if (before_) {
                        var status = before_(obj.menu, obj);
                        if(status === false) return;
                        if(status === 'justShow') {
                            justShow = true;
                        };
                    }
                    let width_ = opt['width'] || 'auto';
                    let zIndex_ = opt['zIndex'] || null;
                    let padding_ = backObj.isUndefined(opt['padding']) ? null : opt['padding'];
                    let under_ = opt['under'] || false;//在点击的父对象底部显示
                    let above_ = opt['above'] || false;//在点击的父对象上面显示
                    let lr = opt['lr'] || ''; //left/right/auto 鼠标在对象的左侧时向左展开，在右侧时向右展开
                    let addLeft = opt['addLeft'] || 0; //add left
                    let subTop = opt['subTop'] || 0;
                    let subLeft = opt['subLeft'] || 0;
                    let menuClass = opt['class'] || null;
                    let showOnMouse = opt['showOnMouse'] || null;//直接在鼠标上显示
                    let left_, top_;
                    let offSet_ = obj.offset();
                    left_ = e.clientX;
                    top_ = e.clientY;
                    let winHeight = $(document).outerHeight(true);
                    var menuObj = null;
                    if(!zIndex_) {
                        if(!window.zIndex) window.zIndex = 1000;
                        zIndex_ = window.zIndex;
                    }
                    //初始化
                    if(!justShow) {
                        let menuData_ =  [];
                        if($.isArray(opt['data'])) {
                            menuData_ = opt['data'];
                        } else if(typeof opt['data'] == 'function') {
                            menuData_ = opt['data']();
                        }
                        menuObj =  $('<div class="diyRightMenu"></div>');
                        $('body').append(menuObj);
                        menuObj.removeClass('hidden');
                        let itemArray_ = [];
                        $.each(menuData_, function (n, v) {
                            let title_ = v['value'] || '未定义value';
                            let click_ = v['click'] || null;
                            let class_ = v['class'] || null;
                            let style_ = v['style'] || null;
                            let href_ = v['href'] || null;
                            let target_ = v['target'] || null;
                            let li_ = '';
                            if(click_) {
                                li_ = $('<a class="item"></a>');
                                li_.append(title_);
                                li_.click(function (e) {
                                    e.stopPropagation();
                                    if(click_) click_(e, $(this), menuObj);
                                });
                            } else {
                                li_ = $('<div></div>');
                                li_.click(function (e) {
                                    e.stopPropagation();
                                });
                                li_.append(title_);
                            }
                            if(class_) li_.addClass(class_);
                            if(style_) li_.attr('style', style_);
                            if(href_) li_.attr('href', href_);
                            if(target_) li_.attr('target', target_);
                            itemArray_.push(
                                li_
                            );
                        });
                        menuObj.html('').append(itemArray_);
                        obj.menu = menuObj;
                    } else {
                        //不删除菜单的 二次展开
                        menuObj = obj.menu ;
                    }
                    let menuHeight = menuObj.outerHeight(true);
                    if (top_ + menuHeight > winHeight) top_ = winHeight - menuHeight - 10;//防止菜单被底部挡住
                    var setCss = {
                        'width': width_,
                        'left': left_,
                        'top': top_,
                        'zIndex': zIndex_,
                    };

                    if(padding_ !== null) {
                        setCss['padding'] = padding_;
                    }
                    menuObj.css(setCss);
                    if(showOnMouse===true) {
                        setTimeout(function () {
                            //右边
                            if (lr == 'auto') {
                                var objMiddel= offSet_.left + obj.outerWidth()/2;
                                if(left_ < objMiddel) {
                                    left_ -= menuObj.outerWidth() ;
                                    menuClass = 'el-dropdown-menu-left';
                                } else {
                                    menuClass = 'el-dropdown-menu-right';
                                }
                                menuObj.addClass(menuClass);
                            } else if (backObj.strObj.strInArray(lr, ['l', 'left'])!=-1 ) {
                                left_ -= menuObj.outerWidth() ;
                            }
                            //上边
                            if (above_) {
                                top_  -= menuObj.outerHeight();
                                left_ = offSet_.left + obj.outerWidth()/2 - menuObj.outerWidth(true)/2;
                            } else if (under_) {
                                left_ = offSet_.left;
                                top_ = offSet_.top + obj.outerHeight();
                            }
                            if(subTop) top_ = parseInt(top_) - parseInt(subTop);
                            if(subLeft) left_ = parseInt(left_) - parseInt(subLeft);
                            if(addLeft) left_ = parseInt(left_) + parseInt(addLeft);
                            menuObj.animate({top: top_, left: left_}, 30);
                        }, 1);

                    } else {
                        //只设置右边
                        if (backObj.strObj.strInArray(lr, ['r', 'right'])!=-1 ) {
                            setTimeout(function () {
                                top_ = offSet_.top;
                                left_ = offSet_.left + obj.outerWidth() ;
                                if(above_) {
                                    top_ -= menuObj.outerHeight();
                                }
                                if(addLeft) left_ = parseInt(left_) + parseInt(addLeft);
                                if(subTop) top_ = parseInt(top_) - parseInt(subTop);
                                menuObj.animate({left: left_, top: top_}, 30);
                            }, 1);
                        } else if (backObj.strObj.strInArray(lr, ['l', 'left'])!=-1 && !above_ && !under_) {
                            //只设置左边
                            setTimeout(function () {
                                // console.log('offSet_.left ', offSet_.left );
                                // console.log('menuObj.outerWidth()', menuObj.outerWidth());
                                top_ = offSet_.top;
                                menuObj.animate({left: offSet_.left -  menuObj.outerWidth(), top: top_}, 30);
                            }, 1);
                        } else if (above_) {
                            setTimeout(function () {
                                top_  = offSet_.top - menuObj.outerHeight();
                                left_ = offSet_.left + obj.outerWidth()/2 - menuObj.outerWidth(true)/2;
                                if(subTop) top_ = parseInt(top_) - parseInt(subTop);
                                if(subLeft) left_ = parseInt(left_) - parseInt(subLeft);
                                if(addLeft) left_ = parseInt(left_) + parseInt(addLeft);
                                menuObj.animate({top: top_, left: left_}, 30);
                            }, 1);
                        } else if (under_) {
                            left_ = offSet_.left;
                            top_ = offSet_.top + obj.outerHeight();
                            menuObj.animate({top: top_, left: left_}, 30);
                        }
                    }

                    if(menuClass) menuObj.addClass(menuClass);
                    if(!justShow) {
                        let onload_ = opt['onload'] || false;//初始化后自动执行的方法
                        if (onload_) {
                            onload_(menuObj, obj);
                        }
                    }
                }
            });
        },

        //渲染语言包
    //xuanranDom必须要临时保存 才能二次渲染
        xuanranLange: function(xuanranDom) {
            if(!window.languages) {
                console.log('未找到语言信息', window.languages);
                return '';
            }

            //渲染对象
            function xuanranObj(dom_, pushData) {
                var findTexts = dom_.xuanranTextObj;
                var findAttrNodes = dom_.xuanranAttrs;
                var findNodes = dom_.xuanranNodes;
                // console.log('findAttrNodes:', findAttrNodes);
                $.each(findNodes, function (n, o_) {
                    var text_ = o_['text'];
                    o_['obj'].childNodes[0].textContent = backObj.strObj.getKeyFromData(pushData, text_);
                    $(o_['obj']).css('display', 'inline');
                });
                $.each(findTexts, function (n, o_) {
                    var replaceText = o_['replaceText'];
                    var findKey = o_['findKey'];
                    var source_ = o_['source'];
                    var reg_ = new RegExp(replaceText, "gm");
                    var newSource = source_.replace(reg_, backObj.strObj.getKeyFromData(pushData, findKey));
                    o_['obj'].textContent = newSource;
                });
                //渲染dom的属性
                $.each(findAttrNodes, function (n, o_) {
                    var node_ = o_['obj'];
                    var attrs = o_['attrs'];
                    var newStr;
                    for (var i = 0; i < attrs.length; i++) {
                        var matches = attrs[i]['matches'];
                        newStr = attrs[i]['source'];
                        matches.map(function (key_, n) {
                            var findKey =  key_.replace('}', '').replace(/\{([a-zA-Z0-9_]+)\:/g, '');
                            var newVal = backObj.strObj.getKeyFromData(pushData, findKey);
                            newStr = newStr.replace(new RegExp(key_, 'g'), newVal);
                        });
                        node_.setAttribute(attrs[i]['name'], newStr);
                    }
                });
            }
            //获取对象里的指定节点
            function getNodes(obj, nodeTag) {
                // console.log('getNodes', obj);
                //渲染dom前置工作：打包花括号
                var findNodes = [];
                var findTexts = [];
                var findAttrNodes = [];
                //判断文本是否包含{}
                function strHasTag(text) {
                    var reg1 = new RegExp('<'+nodeTag+'>\(\[\^\<\]\+\)<\/'+nodeTag+'>', 'g');  //  用于直观显示的语言标签：<lang>abc</div>
                    var reg2 = new RegExp('({'+nodeTag+'\:\([\^\}\]\+\)})', 'g');  //{abc:xxxxxx} //用于属性 title="color:{lang:xxx}" 或纯文本
                    return text.match(reg1) || text.match(reg2);
                }
                function getObjAttr(node) {
                    var attr = node.attributes;
                    // 遍历属性 如果有定义｛｝要存储 下次编译
                    var addElement = false;
                    var tmpObj = {'obj': node, 'attrs': []};
                    for (var i = 0; i < attr.length; i++) {
                        var valueStr = attr[i].nodeValue;
                        var matches = strHasTag(valueStr);
                        if(matches && matches.length) {
                            addElement = true;
                            tmpObj['attrs'].push({
                                'name': attr[i].nodeName,
                                'source': valueStr,
                                'matches': matches,
                            });
                        }
                    }
                    if(tmpObj['attrs'].length) findAttrNodes.push(tmpObj);
                }
                function getObjHtmlNode(node) {
                    node.childNodes.forEach(function (child) {
                        compile(child);
                    });
                    function compile (node) {
                        // 节点类型为元素 div/p/li/ul/ <input> <lang>
                        if (node.nodeType === 1) {
                            //解析属性
                            getObjAttr(node);
                            // 节点类型为 lang
                            if (node.tagName == nodeTag.toUpperCase()) {
                                if (node.childNodes[0]) {
                                    findNodes.push({
                                        'text': node.childNodes[0].textContent,
                                        'obj': node
                                    });
                                }
                            } else {
                                //解析子内容
                                getObjHtmlNode(node);
                            }
                        }else if( node.nodeType == 2 ) {
                        } else if( node.nodeType == 3 ) {
                            // 节点类型为 纯文本 text
                            if (strHasTag(node.nodeValue)) {
                                findTexts.push({
                                    'replaceText': RegExp.$1,
                                    'findKey': RegExp.$2,
                                    'source': node.nodeValue,
                                    'obj': node
                                });
                            }
                        }
                    }
                }
                getObjHtmlNode(obj[0]);
                obj.xuanranNodes = findNodes;
                obj.xuanranTextObj = findTexts;
                obj.xuanranAttrs = findAttrNodes;
            };
            var xr = function (dom_) {
                if(dom_.xuanranNodes || dom_.xuanranTextObj || dom_.xuanranAttrs) {
                    xuanranObj(dom_, window.languages);
                    return;
                }
                getNodes(dom_, 'lang');
                xuanranObj(dom_, window.languages);
            };
            if(Array.isArray(xuanranDom)) {
                $.each(xuanranDom, function (n, v) {
                    xr(v);
                });
            } else {
                xr(xuanranDom);
            }

        },
    //js获取语言
        getLang: function(str) {
            return backObj.strObj.getKeyFromData(window.languages, str)
        },

        //查看对话的图片
        //hasNext: false
        //hasPre: true
        //info:
        // content:
            // cover: "data:image/jpeg;base64,/Z"
            // sourceHeight: 184
            // sourceWidth: 200
            // fileName: "action.jpg"
            // mimeType: "image/jpeg"
            // url: "/piecesFile/3/2021/3_161134_last.png"
        //nextImage 结构= msgInfo
        //prevImage 结构= msgInfo
        viewDialogImgFull: function(info, targetId, getImgInfo) {
        // console.log('viewDialogImgFull', info);
        var hasNext = info.hasNext;
        var hasPre = info.hasPre;
        var viewFullImgObj = null;
        var imgInfo = info.msgInfo.content;
        var prevImageInfo = info.prevImage;
        var nextImageInfo = info.nextImage;
        var base64ImgUrl = imgInfo.cover;
        var fileName = imgInfo.fileName;
        var mimeType = imgInfo.mimeType;
        var fileSaveTime = imgInfo.fileSaveTime;
        var imgResizeWidth = 0;
        var imgResizeHeight = 0;
        var imgLeft = 0;
        var imgTop = 0;
        //初始化图片尺寸：如果提前定义好了宽度和高度
        //img必须带id来实时获取 ,第一次旋转之后img会被 替换为canvas
        var mapObj = $("<div id='preview_img_box'>" +
            "<div class='img-wrap' >" +
            "<img class='rote_img' src='"+ base64ImgUrl +"'/>" +
            '<div class="progress" style="position: absolute; bottom: -4px;left:0;width: 100%;height: 2px;margin: 0;border-radius: 4px;">' +
            '<div class="progress-bar" role="progressbar" style="width: 0%;"></div>' +
            '</div>' +
            "</div>" +
            "</div>");
        var viewImgBox = $("<div id='view_list_img_box'><div class='inner'></div></div>");

        var previewImg = mapObj.find('.rote_img');
        var progressBar = mapObj.find('.progress-bar');
        var progressParent = mapObj.find('.progress');

        //计算图片缩小后的大小
        function __countImgSize(imgWidth, imgHeight) {
            var maxMapWidth = 500;
            var maxMapHeight = 500;
            var bili = imgWidth / imgHeight;
            imgResizeWidth = parseFloat(imgWidth);
            imgResizeHeight = parseFloat(imgHeight);

            // 宽高比>1，宽比高大
            if(bili > 1) {
                if (imgResizeHeight > maxMapHeight) {
                    imgResizeHeight = maxMapHeight;
                    imgResizeWidth = imgResizeHeight * bili;
                }
            } else {
                if (imgResizeWidth > maxMapWidth) {
                    imgResizeWidth = maxMapWidth;
                    imgResizeHeight = imgResizeWidth / bili;
                }
            }
            return [imgResizeWidth, imgResizeHeight];
        }
        //滚动滚轮 设置图片大小
        function _onWheel(e) {
            var e = e || window.event;
            e.preventDefault(); //禁止滚动页面
            var imgPos  = imgWrap.offset();
            var lastTop = imgPos.top;
            var lastLeft = imgPos.left;
            var clientX = e.clientX;
            var clientY = e.clientY;
            var distanceX = clientX - lastLeft;
            var distanceY = clientY - lastTop;
            var deltaY = e.deltaY || e.detail; //火狐底下y就是 detail值
            var lastWidth = imgWrap.outerWidth(true);
            var lastHeight = imgWrap.outerHeight(true);
            var mouseBiliX = distanceX / lastWidth;
            var mouseBiliY = distanceY / lastHeight;
            var changeWidth = 100;
            // 最小宽高
            var minWidth = 10;
            var minHeight = 10;
            // 实际缩放宽高
            var realChangeWidth;
            var realChangeHeight;
            // 新的宽高
            var newWidth;
            var newHeight;
            // 新的定位
            var newTop;
            var newLeft;
            var bili = 0;
            if(!imgWrap.attr('data-bili')) {
                bili = lastWidth/lastHeight;
                imgWrap.attr('data-bili', bili);
            } else {
                bili = imgWrap.attr('data-bili');
            }
            if(deltaY > 0) { //缩小
                newWidth = lastWidth - changeWidth;
                newHeight = newWidth / bili;
                // 宽高比>1，宽比高大
                if (bili > 1) {
                    if (newHeight < minHeight) {
                        newHeight = minHeight;
                        newWidth = newHeight * bili;
                    }
                } else {
                    if (newWidth < minWidth) {
                        newWidth = minWidth;
                        newHeight = newWidth / bili;
                    }
                }
            } else {//放大
                newWidth = lastWidth + changeWidth;
                newHeight = newWidth / bili;
            }
            realChangeWidth = lastWidth - newWidth;
            realChangeHeight = lastHeight - newHeight;
            newTop =  lastTop + (realChangeHeight * mouseBiliY);
            newLeft =  lastLeft + (realChangeWidth * mouseBiliX);
            imgWrap.css({
                'width': newWidth + 'px',
                'height': newHeight + 'px',
                'top': newTop + 'px',
                'left': newLeft + 'px',
            });
        }

        var downLoadRealImg = function (imgInfo) {
            var imgNewCss = [];
            previewImg.addClass('no_transition');
            previewImg.removeAttr('data-rotate');   //清空之前图片的旋转属性
            previewImg.removeAttr('style');   //清空之前图片的旋转属性
            previewImg.src = imgInfo.cover; //显示小图
            var width_ = imgInfo.sourceWidth;
            var height_ = imgInfo.sourceHeight;
            fileSaveTime = imgInfo.fileSaveTime;
            previewImg.width = width_;
            previewImg.height =  height_;
            var imgSizes = __countImgSize(width_, height_);
            imgResizeWidth = imgSizes[0];
            imgResizeHeight = imgSizes[1];
            imgNewCss.push("width:" + imgResizeWidth +"px");
            imgNewCss.push("height:" + imgResizeHeight +"px");
            imgNewCss.push("left:" + (($(window).width() - imgResizeWidth) /2) +"px");
            imgNewCss.push("top:" + (($(window).height() - imgResizeHeight) /2) +"px");
            //重置图片比例
            imgWrap.removeAttr('data-bili').attr('style', imgNewCss.join(';'));
            //下载按钮重置
            downloadObj.attr('data-success', 0);
            var frontFunc = require('frontFunc');
            frontFunc.fileContentDecode(imgInfo.url, fileSaveTime, progressBar, progressParent, imgInfo.sourceWidth,function (url) {
                previewImg.attr('src', url);
                downloadObj.attr('data-success', 1);
            });
        };

        var closeBtn = $("<div class='close_btn'> <img src='/assets/img/lr/close.png' width='100%' alt='close' /></div>");
        closeBtn.click(function () {
            lrBox.removeBoxObj(viewFullImgObj);
        });
        let downloadObj = $("<a class='control_btn' href='javascript: void(0);'> <img class='download_img' src='/assets/img/lr/download.png' width='100%' /></a>");
        let rotateObj = $("<div class='control_btn'> <img class='rotate_img' src='/assets/img/lr/rotate.png' width='100%'  /></div>");
        var imgWrap = mapObj.find('.img-wrap');
        var previewImg = imgWrap.find('.rote_img');
        downloadObj.find('img').on('click', function (e) {
            var url = previewImg.attr('src');
            //执行下载blob文件
            backObj.strObj.downloadFile(url, mimeType, fileName);
        });
        rotateObj.find('img').on('click', function (e) {
            e.preventDefault();
            var tmpImg = previewImg;
            var oldRotate = tmpImg.attr('data-rotate');
            if(backObj.isUndefined(oldRotate)) oldRotate = 0;
            var newRotate = parseInt(oldRotate) +90;
            var newStr = {
                'data-rotate' : newRotate,
                'style' : 'transform:rotate('+ newRotate +'deg);'
            };
            tmpImg.attr(newStr);
        });
        var preObj, nextObj;
        preObj = $("<div class='control_btn'>" +
            "<img  class='pre_img' src='/assets/img/lr/pre.png' width='100%' title='"+ backObj.getLang('viewPrevImg') +"' /></a></div>");
        preObj.find('img').on('click', function (e) {
            e.preventDefault();
            visitImgIndex('prev');
        });
        nextObj = $("<div class='control_btn'><img class='next_img' src='/assets/img/lr/next.png' width='100%'  title='"+ backObj.getLang('viewNextImg') +"' /></div>");
        nextObj.find('img').on('click', function (e) {
            e.preventDefault();
            visitImgIndex('next');
        });
        viewImgBox.find('.inner').append(preObj).append(downloadObj).append(rotateObj).append(nextObj);
        mapObj.append(viewImgBox);

        previewImg.on('load', function () {
            // console.log('on_load');
            //注册拖拽事件
            if(!imgWrap.attr('regdrag')) {
                imgWrap.attr('regdrag', 1);
                imgWrap.Drag(imgWrap, 'relative', {
                    'parent_box': mapObj,
                    'limit_x': false,
                    'limit_y': false
                });
                var element = imgWrap[0];
                if (typeof element.onmousewheel == "object") {
                    element.onmousewheel = function(e) {
                        _onWheel(e);
                    };
                }
                if (typeof element.onmousewheel == "undefined") {
                    element.addEventListener("DOMMouseScroll", _onWheel, false);
                }
            }
            if(imgResizeWidth ==0) {
                setTimeout(function () {
                    var width_ = $(this).outerWidth();
                    var height_ = $(this).outerHeight();
                    if(width_>0 && height_>0 ) {
                        var imgSizes = __countImgSize(width_, height_);
                        imgResizeWidth = imgSizes[0];
                        imgResizeHeight = imgSizes[1];
                        imgLeft = ($(window).width() - imgResizeWidth) /2;
                        imgTop = ($(window).height() - imgResizeHeight) /2;
                        imgWrap.css({
                            left: imgLeft,
                            top: imgTop,
                        })
                    }
                }, 100);
            }
        });
        //点击图片不隐藏图层
        previewImg.click(function (e) {
            e.stopPropagation();
        });
        //点击背景隐藏图层
        mapObj.click(function (e) {
            lrBox.removeBoxObj(viewFullImgObj);
        });
        //点击控制条不隐藏图层
        viewImgBox.click(function (e) {
            e.stopPropagation();
        });
        //计算按钮样式
        function resetPrevNextBtn() {
            //计算  下一张 的按钮样式
            if(!hasNext) {
                nextObj.addClass('no_more');
            } else {
                nextObj.removeClass('no_more');
            }
            //计算上一张 的按钮样式
            if(!hasPre) {
                preObj.addClass('no_more');
            } else {
                preObj.removeClass('no_more');
            }
        }
        //访问第n张图片
        function visitImgIndex(prevNext) {
            var viewInfo = null;
            if(prevNext =='prev') {
                if (!hasPre) {
                    lrBox.msgTisf(backObj.getLang('noMore'));
                    return;
                }
                viewInfo = prevImageInfo;
            } else {
                if (!hasNext) {
                    lrBox.msgTisf(backObj.getLang('noMore'));
                    return;
                }
                viewInfo = nextImageInfo;
            }
            downLoadRealImg(viewInfo.content);
            previewImg.removeAttr('data-rotate');   //清空之前图片的旋转属性
            previewImg.removeAttr('style');   //清空之前图片的旋转属性
            getImgInfo(viewInfo.id, targetId, function (info) {
                hasNext = info.hasNext;
                hasPre = info.hasPre;
                imgInfo = info.msgInfo.content;
                prevImageInfo = info.prevImage;
                nextImageInfo = info.nextImage;
                base64ImgUrl = imgInfo.cover;
                fileName = imgInfo.fileName;
                mimeType = imgInfo.mimeType;
                //修改上下一张 的按钮样式
                resetPrevNextBtn(info);
            });
        }
        var opts = {
            'class': (lrBox.isPc() ? 'msg_box': 'msg_box_wap'),
            bg: 1,//背景遮挡
            text: [mapObj, closeBtn],
            hide: false, //自动隐藏
        };
        opts['x'] = 0;
        opts['y'] = 0;
        opts['width'] = '100%';
        opts['height'] = '100%';
        opts['class'] = 'view_full_img';
        opts['top'] = 0;
        opts['bgOpacity'] = 0.6;
        opts['positionType'] = 'fixed';
        //背景点击：关闭窗口
        opts['bgClick'] = function () {
            lrBox.removeBoxObj(viewFullImgObj);
        };
        /**
         * Perform the keyboard actions
         *
         */
        function _keyboard_action(objEvent) {
            if($('#preview_img_box').length==0) return;
            var keycode, escapeKey;
            // To ie
            if ( objEvent == null ) {
                keycode = event.keyCode;
                escapeKey = 27;
                // To Mozilla
            } else {
                keycode = objEvent.keyCode;
                escapeKey = objEvent.DOM_VK_ESCAPE;
            }
            // Verify the keys to close the ligthBox
            if (keycode == escapeKey) {
                lrBox.hideNewBox();
            }
            // Verify the key to show the previous image
            if (  keycode == 37 ) {
                objEvent.preventDefault();
                // If we are not showing the first image, call the previous
                visitImgIndex('prev');
            }
            // Verify the key to show the next image
            if ( keycode == 39 ) {
                objEvent.preventDefault();
                // If we are not showing the last image, call the next
                visitImgIndex('next');
            }
        }
        $(document).off('keydown', _keyboard_action).on('keydown', _keyboard_action);
        viewFullImgObj = lrBox.makeBox(opts);
        //初始化加载图片
        downLoadRealImg(imgInfo);
        resetPrevNextBtn();
        return viewFullImgObj;
    },

        //压缩生成图片的模糊小图
        getSmallImg: function (base64Data, callFunc, maxWidth, maxHeight) {
            var image = new Image();
            //读取完成触发事件
            image.onload=function(){
                var imgWidth = image.width;
                var imgHeight = image.height;
                maxWidth = maxWidth || 35;
                maxHeight =  maxHeight|| 35;
                var newWidth , newHeight;
                if (imgWidth > 0 && imgHeight > 0) {
                    if (imgWidth / imgHeight >= maxWidth) {
                        if (imgWidth > maxWidth) {
                            newWidth = maxWidth;
                            newHeight = (imgHeight * maxWidth) / imgWidth;
                        } else {
                            newWidth = imgWidth;
                            newHeight = imgHeight;
                        }
                    } else {
                        if (imgHeight > maxHeight) {
                            newHeight = maxHeight;
                            newWidth = (imgWidth * maxHeight) / imgHeight;
                        } else {
                            newWidth = imgWidth;
                            newHeight = imgHeight;
                        }
                    }
                }

                var canvas = document.createElement("canvas");
                var context = canvas.getContext('2d');
                canvas.width = maxWidth;
                canvas.height = maxHeight;
                canvas.height = maxHeight;
                context.clearRect(0, 0, newWidth, newHeight);// 清除画布
                context.drawImage(image, 0, 0, newWidth, newHeight);
                callFunc(canvas.toDataURL("image/jpeg"));
            };
            //读取本地图片
            image.src = base64Data;
        },

        //nanoscroll
        nanoEven: function (dom, settings) {
            var BROWSER_IS_IE7, BROWSER_SCROLLBAR_WIDTH, DOMSCROLL, DOWN, DRAG, ENTER, KEYDOWN, KEYUP, MOUSEDOWN, MOUSEENTER, MOUSEMOVE, MOUSEUP, MOUSEWHEEL, NanoScroll, PANEDOWN, RESIZE, SCROLL, SCROLLBAR, TOUCHMOVE, UP, WHEEL, cAF, defaults, getBrowserScrollbarWidth, hasTransform, isFFWithBuggyScrollbar, rAF, transform, _elementStyle, _prefixStyle, _vendor;
            defaults = {

                /**
                 a classname for the pane element.
                 @property paneClass
                 @type String
                 @default 'nano-pane'
                 */
                paneClass: 'nano-pane',

                /**
                 a classname for the slider element.
                 @property sliderClass
                 @type String
                 @default 'nano-slider'
                 */
                sliderClass: 'nano-slider',

                /**
                 a classname for the content element.
                 @property contentClass
                 @type String
                 @default 'nano-content'
                 */
                contentClass: 'nano-content',

                /**
                 a setting to enable native scrolling in iOS devices.
                 @property iOSNativeScrolling
                 @type Boolean
                 @default false
                 */
                iOSNativeScrolling: false,

                /**
                 a setting to prevent the rest of the page being
                 scrolled when user scrolls the `.content` element.
                 @property preventPageScrolling
                 @type Boolean
                 @default false
                 */
                preventPageScrolling: false,

                /**
                 a setting to disable binding to the resize event.
                 @property disableResize
                 @type Boolean
                 @default false
                 */
                disableResize: false,

                /**
                 a setting to make the scrollbar always visible.
                 @property alwaysVisible
                 @type Boolean
                 @default false
                 */
                alwaysVisible: false,

                /**
                 a default timeout for the `flash()` method.
                 @property flashDelay
                 @type Number
                 @default 1500
                 */
                flashDelay: 1500,

                /**
                 a minimum height for the `.slider` element.
                 @property sliderMinHeight
                 @type Number
                 @default 20
                 */
                sliderMinHeight: 20,

                /**
                 a maximum height for the `.slider` element.
                 @property sliderMaxHeight
                 @type Number
                 @default null
                 */
                sliderMaxHeight: null,

                /**
                 an alternate document context.
                 @property documentContext
                 @type Document
                 @default null
                 */
                documentContext: null,

                /**
                 an alternate window context.
                 @property windowContext
                 @type Window
                 @default null
                 */
                windowContext: null
            };

            /**
             @property SCROLLBAR
             @type String
             @static
             @final
             @private
             */
            SCROLLBAR = 'scrollbar';

            /**
             @property SCROLL
             @type String
             @static
             @final
             @private
             */
            SCROLL = 'scroll';

            /**
             @property MOUSEDOWN
             @type String
             @final
             @private
             */
            MOUSEDOWN = 'mousedown';

            /**
             @property MOUSEENTER
             @type String
             @final
             @private
             */
            MOUSEENTER = 'mouseenter';

            /**
             @property MOUSEMOVE
             @type String
             @static
             @final
             @private
             */
            MOUSEMOVE = 'mousemove';

            /**
             @property MOUSEWHEEL
             @type String
             @final
             @private
             */
            MOUSEWHEEL = 'mousewheel';

            /**
             @property MOUSEUP
             @type String
             @static
             @final
             @private
             */
            MOUSEUP = 'mouseup';

            /**
             @property RESIZE
             @type String
             @final
             @private
             */
            RESIZE = 'resize';

            /**
             @property DRAG
             @type String
             @static
             @final
             @private
             */
            DRAG = 'drag';

            /**
             @property ENTER
             @type String
             @static
             @final
             @private
             */
            ENTER = 'enter';

            /**
             @property UP
             @type String
             @static
             @final
             @private
             */
            UP = 'up';

            /**
             @property PANEDOWN
             @type String
             @static
             @final
             @private
             */
            PANEDOWN = 'panedown';

            /**
             @property DOMSCROLL
             @type String
             @static
             @final
             @private
             */
            DOMSCROLL = 'DOMMouseScroll';

            /**
             @property DOWN
             @type String
             @static
             @final
             @private
             */
            DOWN = 'down';

            /**
             @property WHEEL
             @type String
             @static
             @final
             @private
             */
            WHEEL = 'wheel';

            /**
             @property KEYDOWN
             @type String
             @static
             @final
             @private
             */
            KEYDOWN = 'keydown';

            /**
             @property KEYUP
             @type String
             @static
             @final
             @private
             */
            KEYUP = 'keyup';

            /**
             @property TOUCHMOVE
             @type String
             @static
             @final
             @private
             */
            TOUCHMOVE = 'touchmove';

            /**
             @property BROWSER_IS_IE7
             @type Boolean
             @static
             @final
             @private
             */
            BROWSER_IS_IE7 = window.navigator.appName === 'Microsoft Internet Explorer' && /msie 7./i.test(window.navigator.appVersion) && window.ActiveXObject;

            /**
             @property BROWSER_SCROLLBAR_WIDTH
             @type Number
             @static
             @default null
             @private
             */
            BROWSER_SCROLLBAR_WIDTH = null;
            rAF = window.requestAnimationFrame;
            cAF = window.cancelAnimationFrame;
            _elementStyle = document.createElement('div').style;
            _vendor = (function() {
                var i, transform, vendor, vendors, _i, _len;
                vendors = ['t', 'webkitT', 'MozT', 'msT', 'OT'];
                for (i = _i = 0, _len = vendors.length; _i < _len; i = ++_i) {
                    vendor = vendors[i];
                    transform = vendors[i] + 'ransform';
                    if (transform in _elementStyle) {
                        return vendors[i].substr(0, vendors[i].length - 1);
                    }
                }
                return false;
            })();
            _prefixStyle = function(style) {
                if (_vendor === false) {
                    return false;
                }
                if (_vendor === '') {
                    return style;
                }
                return _vendor + style.charAt(0).toUpperCase() + style.substr(1);
            };
            transform = _prefixStyle('transform');
            hasTransform = transform !== false;

            /**
             Returns browser's native scrollbar width
             @method getBrowserScrollbarWidth
             @return {Number} the scrollbar width in pixels
             @static
             @private
             */
            getBrowserScrollbarWidth = function() {
                var outer, outerStyle, scrollbarWidth;
                outer = document.createElement('div');
                outerStyle = outer.style;
                outerStyle.position = 'absolute';
                outerStyle.width = '100px';
                outerStyle.height = '100px';
                outerStyle.overflow = SCROLL;
                outerStyle.top = '-9999px';
                document.body.appendChild(outer);
                scrollbarWidth = outer.offsetWidth - outer.clientWidth;
                document.body.removeChild(outer);
                return scrollbarWidth;
            };
            isFFWithBuggyScrollbar = function() {
                var isOSXFF, ua, version;
                ua = window.navigator.userAgent;
                isOSXFF = /(?=.+Mac OS X)(?=.+Firefox)/.test(ua);
                if (!isOSXFF) {
                    return false;
                }
                version = /Firefox\/\d{2}\./.exec(ua);
                if (version) {
                    version = version[0].replace(/\D+/g, '');
                }
                return isOSXFF && +version > 23;
            };

            /**
             @class NanoScroll
             @param element {HTMLElement|Node} the main element
             @param options {Object} nanoScroller's options
             @constructor
             */
            NanoScroll = (function() {
                function NanoScroll(el, options) {
                    this.el = el;
                    this.options = options;
                    BROWSER_SCROLLBAR_WIDTH || (BROWSER_SCROLLBAR_WIDTH = getBrowserScrollbarWidth());
                    this.$el = $(this.el);
                    this.doc = $(this.options.documentContext || document);
                    this.win = $(this.options.windowContext || window);
                    this.body = this.doc.find('body');
                    this.$content = this.$el.children("." + options.contentClass);
                    this.$content.attr('tabindex', this.options.tabIndex || 0);
                    this.content = this.$content[0];
                    this.previousPosition = 0;
                    if (this.options.iOSNativeScrolling && (this.el.style.WebkitOverflowScrolling != null || navigator.userAgent.match(/mobi.+Gecko/i))) {
                        this.nativeScrolling();
                    } else {
                        this.generate();
                    }
                    this.createEvents();
                    this.addEvents();
                    this.reset();
                }


                /**
                 Prevents the rest of the page being scrolled
                 when user scrolls the `.nano-content` element.
                 @method preventScrolling
                 @param event {Event}
                 @param direction {String} Scroll direction (up or down)
                 @private
                 */

                NanoScroll.prototype.preventScrolling = function(e, direction) {
                    if (!this.isActive) {
                        return;
                    }
                    if (e.type === DOMSCROLL) {
                        if (direction === DOWN && e.originalEvent.detail > 0 || direction === UP && e.originalEvent.detail < 0) {
                            e.preventDefault();
                        }
                    } else if (e.type === MOUSEWHEEL) {
                        if (!e.originalEvent || !e.originalEvent.wheelDelta) {
                            return;
                        }
                        if (direction === DOWN && e.originalEvent.wheelDelta < 0 || direction === UP && e.originalEvent.wheelDelta > 0) {
                            e.preventDefault();
                        }
                    }
                };


                /**
                 Enable iOS native scrolling
                 @method nativeScrolling
                 @private
                 */

                NanoScroll.prototype.nativeScrolling = function() {
                    this.$content.css({
                        WebkitOverflowScrolling: 'touch'
                    });
                    this.iOSNativeScrolling = true;
                    this.isActive = true;
                };


                /**
                 Updates those nanoScroller properties that
                 are related to current scrollbar position.
                 @method updateScrollValues
                 @private
                 */

                NanoScroll.prototype.updateScrollValues = function() {
                    var content, direction;
                    content = this.content;
                    this.maxScrollTop = content.scrollHeight - content.clientHeight;
                    this.prevScrollTop = this.contentScrollTop || 0;
                    this.contentScrollTop = content.scrollTop;
                    direction = this.contentScrollTop > this.previousPosition ? "down" : this.contentScrollTop < this.previousPosition ? "up" : "same";
                    this.previousPosition = this.contentScrollTop;
                    if (direction !== "same") {
                        this.$el.trigger('update', {
                            position: this.contentScrollTop,
                            maximum: this.maxScrollTop,
                            direction: direction
                        });
                    }
                    if (!this.iOSNativeScrolling) {
                        this.maxSliderTop = this.paneHeight - this.sliderHeight;
                        this.sliderTop = this.maxScrollTop === 0 ? 0 : this.contentScrollTop * this.maxSliderTop / this.maxScrollTop;
                    }
                };


                /**
                 Updates CSS styles for current scroll position.
                 Uses CSS 2d transfroms and `window.requestAnimationFrame` if available.
                 @method setOnScrollStyles
                 @private
                 */

                NanoScroll.prototype.setOnScrollStyles = function() {
                    var cssValue;
                    if (hasTransform) {
                        cssValue = {};
                        cssValue[transform] = "translate(0, " + this.sliderTop + "px)";
                    } else {
                        cssValue = {
                            top: this.sliderTop
                        };
                    }
                    if (rAF) {
                        if (cAF && this.scrollRAF) {
                            cAF(this.scrollRAF);
                        }
                        this.scrollRAF = rAF((function(_this) {
                            return function() {
                                _this.scrollRAF = null;
                                return _this.slider.css(cssValue);
                            };
                        })(this));
                    } else {
                        this.slider.css(cssValue);
                    }
                };


                /**
                 Creates event related methods
                 @method createEvents
                 @private
                 */

                NanoScroll.prototype.createEvents = function() {
                    this.events = {
                        down: (function(_this) {
                            return function(e) {
                                _this.isBeingDragged = true;
                                _this.offsetY = e.pageY - _this.slider.offset().top;
                                if (!_this.slider.is(e.target)) {
                                    _this.offsetY = 0;
                                }
                                _this.pane.addClass('active');
                                _this.doc.bind(MOUSEMOVE, _this.events[DRAG]).bind(MOUSEUP, _this.events[UP]);
                                _this.body.bind(MOUSEENTER, _this.events[ENTER]);
                                return false;
                            };
                        })(this),
                        drag: (function(_this) {
                            return function(e) {
                                _this.sliderY = e.pageY - _this.$el.offset().top - _this.paneTop - (_this.offsetY || _this.sliderHeight * 0.5);
                                _this.scroll();
                                if (_this.contentScrollTop >= _this.maxScrollTop && _this.prevScrollTop !== _this.maxScrollTop) {
                                    _this.$el.trigger('scrollend');
                                } else if (_this.contentScrollTop === 0 && _this.prevScrollTop !== 0) {
                                    _this.$el.trigger('scrolltop');
                                }
                                return false;
                            };
                        })(this),
                        up: (function(_this) {
                            return function(e) {
                                _this.isBeingDragged = false;
                                _this.pane.removeClass('active');
                                _this.doc.unbind(MOUSEMOVE, _this.events[DRAG]).unbind(MOUSEUP, _this.events[UP]);
                                _this.body.unbind(MOUSEENTER, _this.events[ENTER]);
                                return false;
                            };
                        })(this),
                        resize: (function(_this) {
                            return function(e) {
                                _this.reset();
                            };
                        })(this),
                        panedown: (function(_this) {
                            return function(e) {
                                _this.sliderY = (e.offsetY || e.originalEvent.layerY) - (_this.sliderHeight * 0.5);
                                _this.scroll();
                                _this.events.down(e);
                                return false;
                            };
                        })(this),
                        scroll: (function(_this) {
                            return function(e) {
                                _this.updateScrollValues();
                                if (_this.isBeingDragged) {
                                    return;
                                }
                                if (!_this.iOSNativeScrolling) {
                                    _this.sliderY = _this.sliderTop;
                                    _this.setOnScrollStyles();
                                }
                                if (e == null) {
                                    return;
                                }
                                if (_this.contentScrollTop >= _this.maxScrollTop) {
                                    if (_this.options.preventPageScrolling) {
                                        _this.preventScrolling(e, DOWN);
                                    }
                                    if (_this.prevScrollTop !== _this.maxScrollTop) {
                                        _this.$el.trigger('scrollend');
                                    }
                                } else if (_this.contentScrollTop === 0) {
                                    if (_this.options.preventPageScrolling) {
                                        _this.preventScrolling(e, UP);
                                    }
                                    if (_this.prevScrollTop !== 0) {
                                        _this.$el.trigger('scrolltop');
                                    }
                                }
                            };
                        })(this),
                        wheel: (function(_this) {
                            return function(e) {
                                var delta;
                                if (e == null) {
                                    return;
                                }
                                delta = e.delta || e.wheelDelta || (e.originalEvent && e.originalEvent.wheelDelta) || -e.detail || (e.originalEvent && -e.originalEvent.detail);
                                if (delta) {
                                    _this.sliderY += -delta / 3;
                                }
                                _this.scroll();
                                return false;
                            };
                        })(this),
                        enter: (function(_this) {
                            return function(e) {
                                var _ref;
                                if (!_this.isBeingDragged) {
                                    return;
                                }
                                if ((e.buttons || e.which) !== 1) {
                                    return (_ref = _this.events)[UP].apply(_ref, arguments);
                                }
                            };
                        })(this)
                    };
                };


                /**
                 Adds event listeners with jQuery.
                 @method addEvents
                 @private
                 */

                NanoScroll.prototype.addEvents = function() {
                    var events;
                    this.removeEvents();
                    events = this.events;
                    if (!this.options.disableResize) {
                        this.win.bind(RESIZE, events[RESIZE]);
                    }
                    if (!this.iOSNativeScrolling) {
                        this.slider.bind(MOUSEDOWN, events[DOWN]);
                        this.pane.bind(MOUSEDOWN, events[PANEDOWN]).bind("" + MOUSEWHEEL + " " + DOMSCROLL, events[WHEEL]);
                    }
                    this.$content.bind("" + SCROLL + " " + MOUSEWHEEL + " " + DOMSCROLL + " " + TOUCHMOVE, events[SCROLL]);
                };


                /**
                 Removes event listeners with jQuery.
                 @method removeEvents
                 @private
                 */

                NanoScroll.prototype.removeEvents = function() {
                    var events;
                    events = this.events;
                    this.win.unbind(RESIZE, events[RESIZE]);
                    if (!this.iOSNativeScrolling) {
                        this.slider.unbind();
                        this.pane.unbind();
                    }
                    this.$content.unbind("" + SCROLL + " " + MOUSEWHEEL + " " + DOMSCROLL + " " + TOUCHMOVE, events[SCROLL]);
                };


                /**
                 Generates nanoScroller's scrollbar and elements for it.
                 @method generate
                 @chainable
                 @private
                 */

                NanoScroll.prototype.generate = function() {
                    var contentClass, cssRule, currentPadding, options, pane, paneClass, sliderClass;
                    options = this.options;
                    paneClass = options.paneClass, sliderClass = options.sliderClass, contentClass = options.contentClass;
                    if (!(pane = this.$el.children("." + paneClass)).length && !pane.children("." + sliderClass).length) {
                        this.$el.append("<div class=\"" + paneClass + "\"><div class=\"" + sliderClass + "\" /></div>");
                    }
                    this.pane = this.$el.children("." + paneClass);
                    this.slider = this.pane.find("." + sliderClass);
                    if (BROWSER_SCROLLBAR_WIDTH === 0 && isFFWithBuggyScrollbar()) {
                        currentPadding = window.getComputedStyle(this.content, null).getPropertyValue('padding-right').replace(/[^0-9.]+/g, '');
                        cssRule = {
                            right: -14,
                            paddingRight: +currentPadding + 14
                        };
                    } else if (BROWSER_SCROLLBAR_WIDTH) {
                        cssRule = {
                            right: -BROWSER_SCROLLBAR_WIDTH
                        };
                        this.$el.addClass('has-scrollbar');
                    }
                    if (cssRule != null) {
                        this.$content.css(cssRule);
                    }
                    return this;
                };


                /**
                 @method restore
                 @private
                 */

                NanoScroll.prototype.restore = function() {
                    this.stopped = false;
                    if (!this.iOSNativeScrolling) {
                        this.pane.show();
                    }
                    this.addEvents();
                };


                /**
                 Resets nanoScroller's scrollbar.
                 @method reset
                 @chainable
                 @example
                 $(".nano").nanoScroller();
                 */

                NanoScroll.prototype.reset = function() {
                    var content, contentHeight, contentPosition, contentStyle, contentStyleOverflowY, paneBottom, paneHeight, paneOuterHeight, paneTop, parentMaxHeight, right, sliderHeight;
                    if (this.iOSNativeScrolling) {
                        this.contentHeight = this.content.scrollHeight;
                        return;
                    }
                    if (!this.$el.find("." + this.options.paneClass).length) {
                        this.generate().stop();
                    }
                    if (this.stopped) {
                        this.restore();
                    }
                    content = this.content;
                    contentStyle = content.style;
                    contentStyleOverflowY = contentStyle.overflowY;
                    if (BROWSER_IS_IE7) {
                        this.$content.css({
                            height: this.$content.height()
                        });
                    }
                    contentHeight = content.scrollHeight + BROWSER_SCROLLBAR_WIDTH;
                    parentMaxHeight = parseInt(this.$el.css("max-height"), 10);
                    if (parentMaxHeight > 0) {
                        this.$el.height("");
                        this.$el.height(content.scrollHeight > parentMaxHeight ? parentMaxHeight : content.scrollHeight);
                    }
                    paneHeight = this.pane.outerHeight(false);
                    paneTop = parseInt(this.pane.css('top'), 10);
                    paneBottom = parseInt(this.pane.css('bottom'), 10);
                    paneOuterHeight = paneHeight + paneTop + paneBottom;
                    sliderHeight = Math.round(paneOuterHeight / contentHeight * paneOuterHeight);
                    if (sliderHeight < this.options.sliderMinHeight) {
                        sliderHeight = this.options.sliderMinHeight;
                    } else if ((this.options.sliderMaxHeight != null) && sliderHeight > this.options.sliderMaxHeight) {
                        sliderHeight = this.options.sliderMaxHeight;
                    }
                    if (contentStyleOverflowY === SCROLL && contentStyle.overflowX !== SCROLL) {
                        sliderHeight += BROWSER_SCROLLBAR_WIDTH;
                    }
                    this.maxSliderTop = paneOuterHeight - sliderHeight;
                    this.contentHeight = contentHeight;
                    this.paneHeight = paneHeight;
                    this.paneOuterHeight = paneOuterHeight;
                    this.sliderHeight = sliderHeight;
                    this.paneTop = paneTop;
                    this.slider.height(sliderHeight);
                    this.events.scroll();
                    this.pane.show();
                    this.isActive = true;
                    if ((content.scrollHeight === content.clientHeight) || (this.pane.outerHeight(true) >= content.scrollHeight && contentStyleOverflowY !== SCROLL)) {
                        this.pane.hide();
                        this.isActive = false;
                    } else if (this.el.clientHeight === content.scrollHeight && contentStyleOverflowY === SCROLL) {
                        this.slider.hide();
                    } else {
                        this.slider.show();
                    }
                    this.$el.toggleClass('active-scrollbar', this.isActive);
                    this.pane.css({
                        opacity: (this.options.alwaysVisible ? 1 : ''),
                        visibility: (this.options.alwaysVisible ? 'visible' : '')
                    });
                    contentPosition = this.$content.css('position');
                    if (contentPosition === 'static' || contentPosition === 'relative') {
                        right = parseInt(this.$content.css('right'), 10);
                        if (right) {
                            this.$content.css({
                                right: '',
                                marginRight: right
                            });
                        }
                    }
                    return this;
                };


                /**
                 @method scroll
                 @private
                 @example
                 $(".nano").nanoScroller({ scroll: 'top' });
                 */

                NanoScroll.prototype.scroll = function() {
                    if (!this.isActive) {
                        return;
                    }
                    this.sliderY = Math.max(0, this.sliderY);
                    this.sliderY = Math.min(this.maxSliderTop, this.sliderY);
                    this.$content.scrollTop(this.maxScrollTop * this.sliderY / this.maxSliderTop);
                    if (!this.iOSNativeScrolling) {
                        this.updateScrollValues();
                        this.setOnScrollStyles();
                    }
                    return this;
                };


                /**
                 Scroll at the bottom with an offset value
                 @method scrollBottom
                 @param offsetY {Number}
                 @chainable
                 @example
                 $(".nano").nanoScroller({ scrollBottom: value });
                 */

                NanoScroll.prototype.scrollBottom = function(offsetY) {
                    if (!this.isActive) {
                        return;
                    }
                    this.$content.scrollTop(this.contentHeight - this.$content.height() - offsetY).trigger(MOUSEWHEEL);
                    this.stop().restore();
                    return this;
                };


                /**
                 Scroll at the top with an offset value
                 @method scrollTop
                 @param offsetY {Number}
                 @chainable
                 @example
                 $(".nano").nanoScroller({ scrollTop: value });
                 */

                NanoScroll.prototype.scrollTop = function(offsetY) {
                    if (!this.isActive) {
                        return;
                    }
                    this.$content.scrollTop(+offsetY).trigger(MOUSEWHEEL);
                    this.stop().restore();
                    return this;
                };


                /**
                 Scroll to an element
                 @method scrollTo
                 @param node {Node} A node to scroll to.
                 @chainable
                 @example
                 $(".nano").nanoScroller({ scrollTo: $('#a_node') });
                 */

                NanoScroll.prototype.scrollTo = function(node) {
                    if (!this.isActive) {
                        return;
                    }
                    this.scrollTop(this.$el.find(node).get(0).offsetTop);
                    return this;
                };


                /**
                 To stop the operation.
                 This option will tell the plugin to disable all event bindings and hide the gadget scrollbar from the UI.
                 @method stop
                 @chainable
                 @example
                 $(".nano").nanoScroller({ stop: true });
                 */

                NanoScroll.prototype.stop = function() {
                    if (cAF && this.scrollRAF) {
                        cAF(this.scrollRAF);
                        this.scrollRAF = null;
                    }
                    this.stopped = true;
                    this.removeEvents();
                    if (!this.iOSNativeScrolling) {
                        this.pane.hide();
                    }
                    return this;
                };


                /**
                 Destroys nanoScroller and restores browser's native scrollbar.
                 @method destroy
                 @chainable
                 @example
                 $(".nano").nanoScroller({ destroy: true });
                 */

                NanoScroll.prototype.destroy = function() {
                    if (!this.stopped) {
                        this.stop();
                    }
                    if (!this.iOSNativeScrolling && this.pane.length) {
                        this.pane.remove();
                    }
                    if (BROWSER_IS_IE7) {
                        this.$content.height('');
                    }
                    this.$content.removeAttr('tabindex');
                    if (this.$el.hasClass('has-scrollbar')) {
                        this.$el.removeClass('has-scrollbar');
                        this.$content.css({
                            right: ''
                        });
                    }
                    return this;
                };


                /**
                 To flash the scrollbar gadget for an amount of time defined in plugin settings (defaults to 1,5s).
                 Useful if you want to show the user (e.g. on pageload) that there is more content waiting for him.
                 @method flash
                 @chainable
                 @example
                 $(".nano").nanoScroller({ flash: true });
                 */

                NanoScroll.prototype.flash = function() {
                    if (this.iOSNativeScrolling) {
                        return;
                    }
                    if (!this.isActive) {
                        return;
                    }
                    this.reset();
                    this.pane.addClass('flashed');
                    setTimeout((function(_this) {
                        return function() {
                            _this.pane.removeClass('flashed');
                        };
                    })(this), this.options.flashDelay);
                    return this;
                };

                return NanoScroll;

            })();

            return dom.each(function() {
                var options, scrollbar;
                if (!(scrollbar = this.nanoscroller)) {
                    options = $.extend({}, defaults, settings);
                    this.nanoscroller = scrollbar = new NanoScroll(this, options);
                }
                if (settings && typeof settings === "object") {
                    $.extend(scrollbar.options, settings);
                    if (settings.scrollBottom != null) {
                        return scrollbar.scrollBottom(settings.scrollBottom);
                    }
                    if (settings.scrollTop != null) {
                        return scrollbar.scrollTop(settings.scrollTop);
                    }
                    if (settings.scrollTo) {
                        return scrollbar.scrollTo(settings.scrollTo);
                    }
                    if (settings.scroll === 'bottom') {
                        return scrollbar.scrollBottom(0);
                    }
                    if (settings.scroll === 'top') {
                        return scrollbar.scrollTop(0);
                    }
                    if (settings.scroll && settings.scroll instanceof $) {
                        return scrollbar.scrollTo(settings.scroll);
                    }
                    if (settings.stop) {
                        return scrollbar.stop();
                    }
                    if (settings.destroy) {
                        return scrollbar.destroy();
                    }
                    if (settings.flash) {
                        return scrollbar.flash();
                    }
                }
                return scrollbar.reset();
            });
        },

        //删除属性
        delProperty : function (obj, propertys) {
            if(!Array.isArray(propertys)) propertys = [propertys];
            propertys.map(function (v, n) {
                Reflect.deleteProperty(obj, v);
            });
        }
    };
    return backObj;
});

