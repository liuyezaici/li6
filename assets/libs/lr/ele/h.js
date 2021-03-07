define(['require'], function (require) {
    var global = {};
    global.makeH1 = function(options) {
        var core = require('core');
        return core.makeDom({tag: 'h1', 'options':options});
    };
    global.makeH2= function(options) {
        var core = require('core');
        return core.makeDom({tag: 'h2', 'options':options});
    };
    global.makeH3 = function(options) {
        var core = require('core');
        return core.makeDom({tag: 'h3', 'options':options});
    };
    global.makeH4 = function(options) {
        var core = require('core');
        return core.makeDom({tag: 'h4', 'options':options});
    };
    global.makeH5 = function(options) {
        var core = require('core');
        return core.makeDom({tag: 'h5', 'options':options});
    };
    global.makeH6 = function(options) {
        var core = require('core');
        return core.makeDom({tag: 'h6', 'options':options});
    };
    return global;
});
