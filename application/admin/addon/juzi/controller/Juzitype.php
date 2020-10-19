<?php
namespace app\admin\addon\juzi\controller;
use app\common\controller\Backend;

use fast\Addon;
use think\Db;
use app\admin\addon\juzi\model\Juzi_type as JuziTypeModel;

//句子分类
class Juzitype extends Backend
{


    //分类添加
    public function add()
    {
        if ($this->request->isPost()){
            $postData = input()['row'];
            if(empty($postData['title'])) $this->error('名字不能为空');
            $myUid = $this->auth->id;
            $postData['ctime'] = time();
            $postData['cuid'] = $myUid;
            if(JuziTypeModel::where(['title'=>$postData['title'], 'cuid'=> $myUid])->find()) $this->error('分类名字已存在');
            $res = JuziTypeModel::insert($postData);
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
            $typeOldTitle = JuziTypeModel::getfieldbyid($id, 'title');
            if(!$typeOldTitle) $this->error('分类不存在');
            if($typeOldTitle == JuziTypeModel::$defaultTypeName) $this->error(JuziTypeModel::$defaultTypeName.'不允许编辑');
            if(JuziTypeModel::where(['title'=>$postData['title'],'id'=>['neq', $id]])->find()) $this->error('分类名字已存在');
            $re = JuziTypeModel::where($where)->update($postData);
            if($re){
                $this->success('编辑成功！');
            }else{
                $this->error('编辑失败！');
            }

        }
        $find = JuziTypeModel::where('id',$id)->find();
        $this->assign('row', $find);
        return $this->fetch();
    }

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new JuziTypeModel();
    }
    //删除分类
    public function del($id = "")
    {
        $typeOldTitle = JuziTypeModel::getfieldbyid($id, 'title');
        if(!$typeOldTitle) $this->error('分类不存在');
        if($typeOldTitle == JuziTypeModel::$defaultTypeName) $this->error(JuziTypeModel::$defaultTypeName.'不允许编辑');
        $re = JuziTypeModel::destroy($id); //不能用静态方法删除
        if($re){
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }
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
            $total = JuziTypeModel::where($where)->count();
            $list = JuziTypeModel::where($where)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();
            return json_output($total, $list);
        }
       print_r($this->view->fetch());
    }

}