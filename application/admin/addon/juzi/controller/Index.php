<?php
namespace app\admin\addon\juzi\controller;

use app\common\model\Users;
use app\common\controller\Backend;

use think\Db;
use fast\Addon;
use app\admin\addon\juzi\model\Juzi as juziModel;
use app\admin\addon\juzi\model\Juzi_tag as juziTagModel;
/**
 * 句子
 * @internal
 */
class Index extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new juziModel();
    }
    //修改句子
   public function edit($id = NULL){
       $row = juziModel::get(['id' => $id]);
       if (!$row)
           $this->error(__('No Results were found'));
        if($this->request->isPost()){
            $postData = input()['row'];
            $where['id'] = $id;
            if(empty($postData['content'])) $this->error('内容不能为空');
            $re = juziModel::where($where)->update($postData);
            $contentOld = $postData['content'];
            $contentOld = juziModel::changeQuanjiaoCode($contentOld);
            $contentNew = juziModel::removeAllCode($contentOld);
            Db::startTrans();
            try {
                $postData = [];
                $postData['content'] = $contentNew;
                $postData['contenthash'] = MD5($contentNew);
                juziModel::where('id', $id)->update($postData);
                //生成tagid
                vendor('scws.Pscws4');
                $pscws = new \Pscws4();
                $pscws->set_ignore(true);
                $pscws->send_text($contentNew);
                $tagData = $pscws->get_tops();
                $tagArray = [];
                foreach ($tagData as $tmpData) {
                    $tagArray[] = $tmpData['word'];
                }
                $tagIdArray = juziTagModel::saveJuziTagsIndex($id, $tagArray);
                juziModel::saveJuziTagIds($id, $tagIdArray);
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


    //句子列表
    public function index()
    {
        if ($this->request->isPost()){
            list($whereMore, $sort, $order) = $this->buildparams();
            $where  = [];
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');
            if($id = input('id/d'))  $where['id'] = $id;
            if($title = input('content/s'))  $where['content'] = ['like', '%'. trim($title) .'%'];
//            print_r(json_encode($where));exit;
            if($whereMore) $where = array_merge($where, $whereMore);

            $total = juziModel::where($where)->count();
            $list = juziModel::where($where)
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


