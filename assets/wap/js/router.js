//定义所有路由
wapRouter.routers = {
    'path': '/assets/wap/js/', //默认路径
    'login': {
        'path': '/assets/wap/js/login.js'
    },
    'mapSearch': {
        'needLogin': true,
    },
    'reg': {
        'path': '/assets/wap/js/reg.js'
    },
    'ucenter': {
        'path': '/assets/wap/js/user/index.js'
    },
    'user_order': {
        'path': '/assets/wap/js/user/order.js'
    },
    'user_coupon': {
        'path': '/assets/wap/js/user/coupon.js'
    },
    'setting': {
        'path': '/assets/wap/js/user/setting.js'
    },
    'buhuo': {
        'path': '/assets/wap/js/seller/index.js'
    },
    'buhuoStation': {
        'path': '/assets/wap/js/seller/buhuoStation.js'
    },
    'buhuoCupboards': {
        'path': '/assets/wap/js/seller/buhuoCupboards.js'
    },
    'buhuoCupboard': {
        'path': '/assets/wap/js/seller/buhuoCupboard.js'
    },
    'buyerscanner': {
        'path': '/assets/wap/js/buyerscanner.js',
        'needLogin': true,
    }
};
