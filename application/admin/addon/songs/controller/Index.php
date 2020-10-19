<?php
namespace app\admin\addon\songs\controller;

use app\common\model\Users;
use app\common\controller\Backend;

use think\Db;
use fast\Addon;
use app\admin\addon\songs\model\Songs as songsModel;
/**
 * 歌曲
 * @internal
 */
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new songsModel();
    }
    //修改歌曲
   public function edit($id = NULL){
       $row = songsModel::get(['id' => $id]);
       if (!$row)
           $this->error(__('No Results were found'));
        if($this->request->isPost()){
            $postData = input()['row'];
            $where['id'] = $id;
            if(empty($postData['title'])) $this->error('内容不能为空');
            $re = songsModel::where($where)->update($postData);
            $titleOld = $postData['title'];
            $titleOld = songsModel::changeQuanjiaoCode($titleOld);
            $titleNew = songsModel::removeAllCode($titleOld);
            Db::startTrans();
            try {
                $postData = [];
                $postData['title'] = $titleNew;
                $postData['titlehash'] = MD5($titleNew);
                songsModel::where('id', $id)->update($postData);
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if($re){
                $this->success('编辑成功！');
            }else{
                $this->error('编辑失败！');
            }
        }
       return $this->fetch();
   }


    //歌曲列表
    public function index()
    {
        if ($this->request->isPost()){
            list($whereMore, $sort, $order) = $this->buildparams();
            $where  = [];
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');
            if($id = input('id/d'))  $where['id'] = $id;
            if($title = input('title/s'))  $where['title'] = ['like', '%'. trim($title) .'%'];
//            print_r(json_encode($where));exit;
            if($whereMore) $where = array_merge($where, $whereMore);

            $total = songsModel::where($where)->count();
            $list = songsModel::where($where)
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
                unset($v);
            }
            return json_output($total, $list);
        }
       print_r($this->view->fetch());
    }

}


