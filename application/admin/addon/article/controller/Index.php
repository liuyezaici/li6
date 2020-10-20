<?php

namespace app\admin\addon\article\controller;

use app\admin\addon\song\model\Album;
use app\common\controller\Backend;
use think\Config;
use think\Hook;
use think\Validate;
use think\Db;
use fast\Addon;
use app\admin\addon\article\model\Article as ArticleModel;
use app\admin\addon\article\model\ArticleTypes as articleTypes;

/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new ArticleModel();
    }


    public function modify($id=NULL) {
        if ($this->request->isPost()){
            $id = trim($id);
            $data = input('row/a');
            $data['thatDate'] = \fast\Date::toInt($data['thatDate']);
            if(!$id) {
                //add
                $this->model->insert($data);
                $this->success('发布成功 ');
            } else {
                $this->model->where('id',$id)->update($data);
                $this->success('编辑成功 ');
            }
        }
        if($id) {
            $info = ArticleModel::get($id);
            if(!$info) $this->error('文章不存在');
            $info['thatDate'] = \fast\Date::toYMD($info['thatDate']);
            print_r($this->view->fetch('',['info' =>  json_encode($info), 'modify'=> 'edit', 'id'=> $id, 'modifyUrl'=> '/admin/addon/article/index/modify/id/'.$id]));
        } else {
            $info = (object)[];
            print_r($this->view->fetch('',['info' =>  json_encode($info), 'modify'=> 'add',  'id'=> 0,'modifyUrl'=> '/admin/addon/article/index/modify/']));
        }
    }

    //审核
    public function audit($id=NULL) {
        if ($this->request->isPost()){
            $id = intval($id);
            if(!$id) $this->error('id不能留空');
            $pass = input('post.pass', 0, 'intval');
            $this->model->where('id',$id)->update([
                'status' => $pass
            ]);
            $this->success('操作成功 ');
        }
    }

    public function del($id=NULL) {
        if ($this->request->isPost()){
            $ids = trim($id);
            if(!$ids) $this->error('id不能留空');
            foreach (explode(',', $ids) as $id_) {
                $this->model->where('id',$id_)->delete();
            }
            $this->success('删除成功 ');
        }
    }

    //查看详情
    public function searchSongForAlbum() {
        $title = input('title', '', 'trim');
        if(!$title)  $this->success('success', '', []);
        $row = ArticleModel::field('id,title,zjid')->where(['title' => ['like', "%{$title}%"]])->limit(10)->select();
        foreach ($row as &$v) {
            $v['album'] = $v['zjid'] ? Album::getFieldbyid($v['zjid'], 'title') : '';
        }
        unset($v);
        $this->success('success', '', $row);
    }

    //查看详情
    public function get($id='') {
        $row = ArticleModel::get($id);
        if (!$row) $this->error(__('No Results were found'));
        $row['album'] = $row['zjid'] ? Album::getfieldbyid($row['zjid'], 'title') : '';
        $this->result($row, 1);
    }

    //修改状态
    public function setStatus($id=0) {
        if ($id)
        {
            $newStatus = $this->request->post('newStatus');
            $count = $this->model->where('id', $id)->update([
                'status' => $newStatus
            ]);
            if ($count)
            {
                $this->success();
            } else {
                $this->error('no_count');
            }
        }
        $this->error('no_id');
    }

    public function index(){
        if ($this->request->isPost()){
            list($whereMore, $sort, $order) = $this->buildparams();
            $where  = [];
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');
            if($albumid = input('albumid')) {
                if(is_string($albumid)) {
                    $albumInfo = Album::field('id')->where(['title'=> ['like', "%{$albumid}%"]])->find();
                    if(!$albumInfo) {
                        $albumid = 0;
                    } else {
                        $albumid = $albumInfo['id'];
                    }
                    if($albumid)  $where['zjid'] = $albumid;
                }

            }
            if($title = input('title/s'))  $where['title'] = ['like', "%{$title}%"];
//            print_r(json_encode($where));exit;
            if($whereMore) $where = array_merge($where, $whereMore);
            $total = $this->model->where($where)->count();
            $list = $this->model->where($where)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();

            foreach ($list as $n =>&$v) {
                $v['typeName'] = $v['typeId'] ? articleTypes::getfieldbyid($v['typeId'], 'title') : '';
                unset($v);
            }
            return json_output($total, $list);
        }
        return parent::index();
    }
}
