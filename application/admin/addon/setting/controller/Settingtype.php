<?php
namespace app\admin\addon\setting\controller;
use app\common\controller\Backend;

use app\admin\addon\setting\model\SettingType as SettingtypeModel;

//配置的分类
class Settingtype extends Backend
{

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new SettingtypeModel();
    }

    //删除分类
    public function del($id = "")
    {
        $re = SettingtypeModel::destroy($id); //不能用静态方法删除
        if($re){
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }
    }

    //分类添加
    public function add()
    {
        if ($this->request->isPost()){
            $postData = input()['row'];
            if(empty($postData['title'])) $this->error('名字不能为空');
            $postData['ctime'] = time();
            $postData['cuid'] = $this->auth->id;
            if(SettingtypeModel::where(['title'=>$postData['title']])->find()) $this->error('分类名字已存在');
            $res = SettingtypeModel::insert($postData);
            $res ? $this->success('添加成功') : $this->error('添加失败');
            $this->success();
        }
        return parent::add();
    }

    //分类编辑
    public function edit($id = NULL)
    {
        if($this->request->isPost()){
            $postData = input()['row'];
            $where['id'] = $id;
            if(empty($postData['title'])) $this->error('名字不能为空');
            if(SettingtypeModel::where(['title'=>$postData['title'],'id'=>['neq', $id]])->find()) $this->error('分类名字已存在');
            $re = SettingtypeModel::where($where)->update($postData);
            if($re){
                $this->success('编辑成功！');
            }else{
                $this->error('编辑失败！');
            }

        }
        $find = SettingtypeModel::where('id',$id)->find();
        $this->assign('row', $find);
        return $this->fetch();
    }

    /**
     * 分类列表
     */
    public function index()
    {
        if ($this->request->isPost()){
            list($whereMore, $sort, $order) = $this->buildparams();
            $where  = [];
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');

            if($whereMore) $where = array_merge($where, $whereMore);
            if($title = input('title/s')) $where['title'] = ['like', '%'. $title .'%'];
            $total = SettingtypeModel::where($where)->count();
            $list = SettingtypeModel::where($where)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();
            foreach ($list as $n =>&$v) {
                if($v['pid']) {
                    $v['p_title'] =  SettingtypeModel::getfieldbyid($v['pid'], 'title');
                } else {
                    $v['p_title'] =  '-';
                }
            }
            return json_output($total, $list);
        }
       print_r($this->view->fetch());
    }

}