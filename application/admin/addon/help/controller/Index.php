<?php
namespace app\admin\addon\help\controller;

use app\common\model\Users;
use app\common\controller\Backend;

use think\Db;
use fast\Addon;
use app\admin\addon\help\model\Help as HelpModel;
use app\admin\addon\help\model\HelpType as HelpTypeModel;
/**
 * 帮助系统
 * @internal
 */
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new HelpModel();
    }

    //添加帮助文章
    public function add() {
        if ($this->request->isPost()){
            $postData = input()['row'];
            if(empty($postData['typeid'])) $this->error('标题不能为空');
            if(empty($postData['content'])) $this->error('内容不能为空');
            if($postData['keyname'] && HelpModel::where(['keyname'=>$postData['keyname']])->find()) $this->error('索引名 已存在');
            $cover_url = $postData['cover_url'];
            $postData['ctime'] = time();
            $postData['cuid'] = $this->auth->id;
            HelpModel::insert($postData);

            $newSid = HelpModel::getLastInsID();
            //附件更新sid
            if($cover_url) {
                $fujianModel = Addon::getModel('fujian');
                if(!$fujianModel) $this->error('未安装fujian组件');
                $fujianModel->updateSid($newSid, [$cover_url]);
            }

            $this->success();
        }
        return parent::add();
    }

    //修改帮助文章
   public function edit($id = NULL){
       $row = HelpModel::get(['id' => $id]);
       if (!$row)
           $this->error(__('No Results were found'));
        if($this->request->isPost()){
            $postData = input()['row'];
            $where['id'] = $id;
            if(empty($postData['title'])) $this->error('标题不能为空');
            if(empty($postData['typeid'])) $this->error('分类不能为空');
            $cover_url = $postData['cover_url'];
            if($postData['keyname']&&HelpModel::where(['keyname'=>$postData['keyname'],'id'=>['neq', $id]])->find()) $this->error('索引名 已存在');
            $re = HelpModel::where($where)->update($postData);
            if($re){

                //附件更新sid
                if($cover_url) {
                    $fujianModel = Addon::getModel('fujian');
                    if(!$fujianModel) $this->error('未安装fujian组件');
                    $fujianModel->updateSid($id, [$cover_url]);
                }

                $this->success('编辑成功！');
            }else{
                $this->error('编辑失败！');
            }
        }
        $find = HelpModel::where('id',$id)->find();
        $this->assign('row',$find);
       return $this->fetch();
   }


    //文章列表
    public function index()
    {
        if ($this->request->isPost()){
            list($whereMore, $sort, $order) = $this->buildparams();
            $where  = [];
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');
            if($id = input('id/d'))  $where['id'] = $id;
            if($title = input('title/s'))  $where['title'] = ['like', '%'. trim($title) .'%'];
            if($typeid = input('typeid/s'))  $where['typeid'] = $typeid;
            if($keyname = input('keyname/s'))  $where['keyname'] = trim($keyname);
//            print_r(json_encode($where));exit;
            if($whereMore) $where = array_merge($where, $whereMore);

            $total = HelpModel::where($where)->count();
            $list = HelpModel::where($where)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();

            foreach ($list as $n =>&$v) {
                $uname = '-';
                $unickname = '-';
                $uInfo = Users::field('username,nickname')->where('id', $v['cuid'])->find();
                if($uInfo) {
                    $uname = $uInfo['username'];
                    $unickname = $uInfo['nickname'];
                }
                $v['username'] =  $uname;
                $v['nickname'] = $unickname;
                $v['typenames'] =  HelpTypeModel::getTitles($v['typeid'], '<br />');
                unset($v);
            }

            return json_output($total, $list);

        }
       print_r($this->view->fetch());
    }

}


