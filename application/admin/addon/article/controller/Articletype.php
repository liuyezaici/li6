<?php

namespace app\admin\addon\article\controller;

use app\admin\addon\article\model\ArticleTypes as ArticleTypesModel;
use app\common\controller\Backend;
use think\Config;
use think\Hook;
use think\Validate;
use fast\Addon;

/**
 * 分类
 * @internal
 */
class Articletype extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new ArticleTypesModel();
    }


    //添加
    public function edit($id=NULL) {
        if ($this->request->isPost()){
            $postData = input()['row'];
            $where['id'] = $id;
            if(empty($postData['title'])) $this->error('名字不能为空');
            if(ArticleTypesModel::where(['title'=>$postData['title'],'id'=>['neq', $id]])->find()) $this->error('分类名字已存在');
            $re = ArticleTypesModel::where($where)->update($postData);
            if($re){
                $this->success('编辑成功！');
            }else{
                $this->error('编辑失败！');
            }
        }
    }
    //添加
    public function add() {
        if ($this->request->isPost()){
            $postData = input()['row'];
            if(empty($postData['title'])) $this->error('名字不能为空');
            if(ArticleTypesModel::where(['title'=>$postData['title']])->find()) $this->error('名字已存在');
            $res = ArticleTypesModel::insert($postData);
            $res ? $this->success('添加成功') : $this->error('添加失败');
            $this->success();
        }
    }

    //查看详情
    public function get($id='') {
        $row = ArticleTypesModel::get($id);
        if (!$row) $this->error(__('No Results were found'));
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
            if($title = input('title/s'))  $where['title'] = ['like', "%{$title}%"];
            if($whereMore) $where = array_merge($where, $whereMore);

            $total = ArticleTypesModel::where($where)->count();
            $list = ArticleTypesModel::where($where)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();
            unset($v);
            return json_output($total, $list);
        }
        return parent::index();
    }
}
