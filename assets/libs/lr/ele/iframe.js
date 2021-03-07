define(['require'], function (require) {
    var global = {};
    var objBindAttrsName = 'bind_attrs';
    var objAttrHasKh = 'obj_opt_has_kuohao';//obj的属性包含有{} 则可能绑定全局变量
    var objValIsNode = 'obj_val_is_node';//obj的val是否允许以字符串的形式重新再写入
    //创建嵌入的窗口
    global.makeIframe = function(sourceOptions) {
        var core = require('core');
        var options = cloneData(sourceOptions);
        if(!isUndefined(options['url']) && isUndefined(options['src'])) {
            options['src'] = options['url'];
        }
        if(!isUndefined(options['width']) ) {
            var width = options['width'];
            if(width.toString().substr(-1, 1) != '%' &&  width.toString().substr(-2, 2) != 'px') {
                width += 'px';
                options['width'] = width;
            }
        }
        if(!isUndefined(options['height']) ) {
            var height = options['height'];
            if(height.toString().substr(-1, 1) != '%' && height.toString().substr(-2, 2) != 'px') {
                height += 'px';
                options['height'] = height;
            }
        }
        if(!isUndefined(options['border']) && isUndefined(options['frameborder']) ) {
            var hasBorder = options['border'] ? 1:0; //frameborder: 1/0
            options['frameborder'] = hasBorder;
        }
        if(isUndefined(options['frameborder'])) {
            options['frameborder'] = 0;
        }
        if(!isUndefined(options['scroll']) && !isUndefined(options['scrolling']) ) {
            options['scrolling'] = options['scroll'];
        }
        var obj = makeDom({
            'tag': 'iframe',
            'options': options
        });
        if(!isUndefined(options['resize'])) {
            var resizeFunc = options['resize'];
            $(window).resize(function (e) {
                //console.log('resize');
                resizeFunc(obj, e);
            });

        }
        return obj;
    };
    return global;
});

