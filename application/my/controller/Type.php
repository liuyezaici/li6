<?php
namespace app\my\controller;

use app\common\controller\Backend;
use app\my\model\types;
use think\Db;
use fast\Aes;
use think\Exception;
use think\Validate;

class Type extends Backend
{
    protected $layout = true;
    protected $noNeedLogin = [''];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
    }



    //获取子分类
    public function getSonTypes() {
        $pid = input('post.pid', 0, 'intval');
        if(!$pid) {
            $this->error('no pid');
        }
        $list = types::getUserTypes($this->auth->id, $pid);
        $this->success('获取成功', '', ['list' => $list]);
    }


    //添加分类
    public function addApi() {
        $title = input('post.title', '', 'trim');
        if(!$title) {
            $this->error('请输入分类名');
        }
        if(types::hasTitle($title)) {
            $this->error('分类名已经存在');
        }
        types::addType($title);
        $this->success('添加成功');
    }



    //添加分类
    public function add() {
        $allTypes = types::getUserRootTypes($this->auth->id);
        print_r($this->fetch('', ['allTypes' => $allTypes]));
    }

    //编辑分类-api
    public function editapi($id=NULL) {
        $info = types::get($id);
        if(!$info) $this->error('分类不存在');
        if($info['uid'] != $this->auth->id) $this->error('身份已经切换');
        $pid = input('post.pid', 0, 'intval');
        $title = input('post.title', '', 'trim');
        if(!$title) {
            $this->error('请输入分类名');
        }
        if($pid && types::hasSon($id)) {
            $this->error('当前分类已包含子分类，不能设为二级分类');
        }

        if(types::hasTitle($title, $id)) {
            $this->error('分类名已经存在');
        }
        types::editType($id, $title);
        $this->success('添加成功');
    }
    //修改分类
    public function edit($id=null) {
        $info = types::get($id);
        if(!$info) $this->error('分类不存在');
        if($info['uid'] != $this->auth->id) $this->error('身份已经切换');
        $allTypes = types::getUserRootTypes($this->auth->id);
        $needAns = false;
        foreach ($allTypes as $n =>&$v) {
            if($v['id']==$info['pid']) {
                $v['selected'] = 'selected';
            } else {
                $v['selected'] = '';
            }
        }
        unset($v);
        print_r($this->fetch('', ['allTypes' => $allTypes, 'info' => $info, 'needAns' => $needAns]));
    }


    //删除分类
    public function del($id=null) {
        Db::startTrans();
        try {
            types::remove($this->auth->id, $id);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('删除成功');

    }
    //添加分类
    public function getmytypes() {
        $id1 = input('id1', 0, 'intval');
        $allTypes = types::getUserRootTypes();
        foreach ($allTypes as $n => &$v) {
            if($id1 && $v['id'] == $id1) {
                $v['selected'] = 'selected';
            } else {
                $v['selected'] = '';
            }
        }
        unset($v);
        print_r($this->fetch('', ['allTypes' => $allTypes, 'id1' => $id1]));
    }

    //分类列表
    public function index() {
        $keyword = input('keyword', '', 'trim');
        if($keyword) {
            //过滤掉非英文 针
            preg_match_all("/[\x{4e00}-\x{9fa5}a-zA-Z0-9]/ui", $keyword,$result);
            if($result) {
                $keyword = join('', $result[0]);
            } else {
                $keyword = '';
            }
//            print_r($keyword);
//            exit;
        }
        if($keyword) {
            $allTypes = types::searchUserRootTypes($keyword);
        } else {
            $allTypes = types::getUserRootTypes();
        }
        unset($v);
        print_r($this->fetch('', ['list' => $allTypes, 'keyword' => $keyword]));
    }


}
