<?php

namespace app\tool\controller;

use app\common\controller\Frontend;
use fast\File;
use think\Config;
use think\Db;
use fast\Addon;
use app\admin\library\Auth;
use app\common\model\Users;
use app\admin\addon\usercenter\model\Third;
use OSS\OssClient;
class Addonfile extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
    }

    //附件列表
    public function index(){
        $table = input('table', '', 'trim');
        $addonKey = input('addonKey', '', 'trim');
        $addonVal = input('addonVal', '', 'trim');
        $sidKey = input('sidKey', '', 'trim');
        $sidVal = input('sidVal', '', 'trim');
        $page = input('page', '', 'intval');
        $pageSize = input('page_size', 20, 'int');
        $where_ = [];
        if(!$table) {
            $this->error('enter table');
        }
        $pathArray = [
            'page' => $page,
            'page_size' => $pageSize,
            'table' => $table,
        ];
        if($addonKey && $addonVal) {
            $where_[$addonKey] = $addonVal;
            $pathArray['addonKey'] = $addonKey;
            $pathArray['addonVal'] = $addonVal;
        }
        if($sidKey && $sidVal) {
            $where_[$sidKey] = $sidVal;
            $pathArray['sidKey'] = $sidKey;
            $pathArray['sidVal'] = $sidVal;
        }
        // id,typeid,title,cuid,ctime,rq,fileids,content,status
        $result = Db::table($table)->where($where_)
            ->paginate($pageSize,false, [
                'page' => $page,
                'query' => $pathArray,
            ]);
        $total = $result->total();
        $menu = $result->render();
        $list = $result->items();
        foreach ($list as &$v) {
            $v['filesize'] = File::formatBytes($v['filesize']);
        }
        unset($v);
        $this->view->engine->layout(false);
        print_r($this->fetch(APP_PATH .'tool/view/addonfile/index.php', [
            'total' => $total,
            'list' => $list,
            'menu' => $menu,
        ]));
    }
}
