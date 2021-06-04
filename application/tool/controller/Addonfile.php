<?php

namespace app\tool\controller;

use app\common\controller\Frontend;
use fast\Date;
use fast\File;
use fast\Str;
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

    //上传附件
    public function upload() {
        $sidVal = input('post.sid', 0, 'trim');
        if(!$sidVal) $this->error('no sid');
        $addonKey = input('addonKey', '', 'trim');
        $addonVal = input('addonVal', '', 'trim');
        $sidKey = input('sidKey', '', 'trim');
        $table = input('table', '', 'trim');
        if(!$table) {
            $this->error('enter table');
        }
        $filename = input('post.filename', 0, 'trim');
        if(!$filename) $this->error('no filename');
        $fileInput = isset($_FILES['fileInput']) ? $_FILES['fileInput'] : [];
        if(!$fileInput) {
            $this->error('no file select');
        }
        $type = isset($fileInput['type']) ? $fileInput['type'] : '';
        $size = isset($fileInput['size']) ? $fileInput['size'] : '';
        $tmp_name = isset($fileInput['tmp_name']) ? $fileInput['tmp_name'] : '';
        if(!$size) $this->error('no file size');
        if(!$type) $this->error('no file type');
        if(!$tmp_name) $this->error('no file tmp_name');
        $savePath = ROOT_PATH . "\public\upload\post_files\\{$sidVal}";
        $visitUrl = "/upload/post_files/{$sidVal}/{$filename}";
        File::creatdir($savePath);
        $saveUrl = $savePath . "\\{$filename}";
        rename($tmp_name, $saveUrl);
        $array_ = explode('.', $filename);
        $geshi = end($array_);
        $newData = [
        'addtime' => Date::toYMDS(time()),
        'filename' => $filename,
        'fileurl' => $visitUrl,
        'filesize' => $size,
        'geshi' => $geshi,
        ];
        if($addonKey && $addonVal) $newData[$addonKey] = $addonVal;
        if($sidKey && $sidVal) $newData[$sidKey] = $sidVal;
        Db::table($table)->insert($newData);
        $this->success('success');
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
        if($url && file_exists(ROOT_PATH .'public'. $url)) {
            unlink(ROOT_PATH .'public'. $url);
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
        $orderby = input('order', 'id.desc', 'trim');
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
        if(!strstr($orderby, '.')) {
            $this->error('order be: id.desc');
        }
        $array_ = explode('.', $orderby);
        $orderKey = $array_[0];
        $orderDesc = $array_[1];
        // id,typeid,title,cuid,ctime,rq,fileids,content,status
        $result = Db::table($table)->where($where_)->order($orderKey, $orderDesc)
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
            'sid' => $sidVal,
            'addonKey' => $addonKey,
            'addonVal' => $addonVal,
            'sidKey' => $sidKey,
            'total' => $total,
            'table' => $table,
            'list' => $list,
            'menu' => $menu,
        ]));
    }
}
