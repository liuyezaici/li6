({
    // 程序的根路径
    appDir: "C:\\web\\li6\\tool\\try\\ele\\btn",
    // 脚本的根路径.相对于当前程序的根路径
    baseUrl: "C:\\web\\li6\\tool\\try\\ele\\btn\\js",
    paths: {
        jquery: 'C:\\web\\li6/resource/pub/js/jq/jquery-3.2.1',
    },
    // 打包输出到的路径
    dir: "C:\\web\\li6/assets/libs/lr/ele/dist",
    allowSourceOverwrites: true,
    optimize: 'none',
    modules: [
        {
            name: 'core'
        }
    ]

    // 通过正则以文件名排除文件/文件夹
   //  // 比如当前的正则表示排除 .svn、.git 这类的隐藏文件
   //  fileExclusionRegExp: /^\.css|^\.git/
})