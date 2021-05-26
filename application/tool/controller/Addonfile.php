<?php

namespace app\tool\controller;

use app\common\controller\Frontend;
use fast\File;
use think\Config;
use think\Db;
use fast\Addon;
use app\admin\library\Auth;
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

    //删除附件
    public function del($id=null) {
        $id=intval($id);
        if(!$id) {
            $this->error('no id');
        }
        $table = input('table', '', 'trim');
        if(!$table) {
            $this->error('enter table');
        }
        $info =  Db::table($table)->where('id', $id)->find();
        if(!$info) {
            $this->error('file No find');
        }
        $url = $info['fileurl'];
        if($url && file_exists(ROOT_PATH.$url)) {
            unlink(ROOT_PATH.$url);
        }
        Db::table($table)->where('id', $id)->delete();
        $this->success('success');
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
                'type'      => 'page\Pagetyle1',
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
            'table' => $table,
            'list' => $list,
            'menu' => $menu,
        ]));
    }
}
