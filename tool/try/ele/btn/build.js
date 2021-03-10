({
    // 需要打包的插件所在的绝对路径
    baseUrl: "C:\\web\\li6\\assets\\libs\\lr\\ele",
    paths: {
        jquery: 'empty:',
        lrBox: 'empty:',
    },
    name: 'C:/web/li6/tool/try/ele/btn', //当前demo执行的html访问的入口js文件名 无.js结尾
    // 打包输出到的路径
    out: "C:\\web\\li6\\assets\\libs\\lr\\lrEle.mini.js",
    allowSourceOverwrites: true,
    optimize: 'uglify'
});