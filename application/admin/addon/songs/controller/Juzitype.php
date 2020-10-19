<?php
namespace app\admin\addon\songs\controller;
use app\common\controller\Backend;

use fast\Addon;
use think\Db;
use app\admin\addon\songs\model\Songs_type as SongsTypeModel;

//歌曲分类
class Songstype extends Backend
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
            if(SongsTypeModel::where(['title'=>$postData['title'], 'cuid'=> $myUid])->find()) $this->error('分类名字已存在');
            $res = SongsTypeModel::insert($postData);
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
            $typeOldTitle = SongsTypeModel::getfieldbyid($id, 'title');
            if(!$typeOldTitle) $this->error('分类不存在');
            if($typeOldTitle == SongsTypeModel::$defaultTypeName) $this->error(SongsTypeModel::$defaultTypeName.'不允许编辑');
            if(SongsTypeModel::where(['title'=>$postData['title'],'id'=>['neq', $id]])->find()) $this->error('分类名字已存在');
            $re = SongsTypeModel::where($where)->update($postData);
            if($re){
                $this->success('编辑成功！');
            }else{
                $this->error('编辑失败！');
            }

        }
        $find = SongsTypeModel::where('id',$id)->find();
        $this->assign('row', $find);
        return $this->fetch();
    }

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new SongsTypeModel();
    }
    //删除分类
    public function del($id = "")
    {
        $typeOldTitle = SongsTypeModel::getfieldbyid($id, 'title');
        if(!$typeOldTitle) $this->error('分类不存在');
        if($typeOldTitle == SongsTypeModel::$defaultTypeName) $this->error(SongsTypeModel::$defaultTypeName.'不允许编辑');
        $re = SongsTypeModel::destroy($id); //不能用静态方法删除
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
            $total = SongsTypeModel::where($where)->count();
            $list = SongsTypeModel::where($where)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();
            return json_output($total, $list);
        }
       print_r($this->view->fetch());
    }

}