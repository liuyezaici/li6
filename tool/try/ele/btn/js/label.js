define(['core'], function (core) {
    var global = {};
    global.makeLabel = function(options) {
        return core.makeDom({tag: 'label', 'options':options});
    };
    return global;
});
