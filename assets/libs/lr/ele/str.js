//字符串方法
define(['require'], function (require) {
    var global = {};
    //生成随机数字
    global.makeRandomInt = function(len) {
        var core = require('core');
        if(core.isObj(len)) len = len[0];
        len = len || 10;
        return (Math.random()*1000000000).toString().substr(0, len).replace(/\./g, '');
    };

    //生成随机字符
    global.makeRandomStr = function(num) {
        var arr = 'abcdefghijklmnopqrstuvwxyz0123456789'.split('');
        var val = '';
        for(var i=0;i<num; i++){
            val += arr[Math.floor(Math.random() * 36)];
        }
        return val;
    };

    //四舍五入 保留2位小数
    global.formatFloat =  function (src, pos) {
        var core = require('core');
        pos = core.isUndefined(pos) ? 2 : pos;
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
    };
    return global;
});

