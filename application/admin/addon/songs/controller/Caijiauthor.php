<?php
namespace app\admin\addon\songs\controller;

use app\common\model\Users;
use app\common\controller\Backend;

use think\Db;
use \fast\Str;
use \fast\File;
use app\admin\addon\songs\model\Songs as songsModel;
use app\admin\addon\songs\model\SongsCaijiauthorrule as caijiModel;
use app\admin\addon\songs\model\Songs_author as SongsAuthorModel;
use app\admin\addon\songs\model\Songs_from as SongsFromModel;
/**
 * 采集作者
 * @internal
 */
class Caijiauthor extends Backend
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new caijiModel();
    }
    //添加采集规则
   public function add(){
        if($this->request->isPost()){
            $postData = input()['row'];
            $url = $postData['url_reg'];
            if(caijiModel::hasCaiji($url)) {
                $this->error('采集已经被发布过了');
            }
            Db::startTrans();
            try {
                $re = $this->model->save($postData);
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
   //拷贝一个采集规则
    public function copycaiji($id = NULL) {
        if($this->request->isPost()){
            $row = caijiModel::get(['id' => $id]);
            if (!$row) $this->error('数据不存在');
            Db::startTrans();
            try {
                unset($row['id']);
                $row['topage'] = 0;
                $row = json_decode(json_encode($row), true);
                $re = $this->model->insert($row);
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if($re){
                $this->success('拷贝成功！');
            }else{
                $this->error('拷贝失败！');
            }
        }
    }

    //开始执行采集
   public function begincaiji($id = NULL){
       $myUid = $this->auth->id;
       $row = caijiModel::get(['id' => $id]);
       if (!$row) $this->error(__('No Results were found'));
       if($this->request->isPost()){
            $url = $row['url_reg'];
            $fromurl = $row['fromurl'];
            $usehttps = $row['usehttps'];
            $from_str = $row['from_str'];
            $end_str = $row['end_str'];
            $topage = $row['topage'];
            $repeat_explode = $row['repeat_explode'];
            $item_author_explode1 = $row['item_author_explode1'];
            $item_author_explode2 = $row['item_author_explode2'];
            if (!$url) $this->error('未配置 url');
            if (!$from_str) $this->error('未配置 from_str');
            if (!$end_str) $this->error('未配置 end_str');
            if (!$repeat_explode) $this->error('未配置 repeat_explode');
            if(!$topage) $topage = 1;
            $url = str_replace('(page)', $topage, $url);
            $response = $usehttps ? File::get_https($url, $fromurl) : File::get_nr($url, $fromurl);
            if (!$response) $this->error('获取不到任何内容');
//           print_r($url);
//           exit;
            $middleStr = Str::sp_($from_str, $end_str, $response);
            if (!$middleStr) $this->error('截取不到任何内容');
            $itemArray = explode($repeat_explode, $middleStr);
            $successNum = 0;
//            print_r($itemArray);
//            exit;
            foreach ($itemArray as $item_) {
                if(!$item_ || strlen($item_) < 3) continue;
                $authorStr = Str::sp_($item_author_explode1, $item_author_explode2, $item_);
                $authorStr = strip_tags($authorStr);
                $authorStr = trim($authorStr);
                $authorId = 0;
                if($authorStr) {
                  $authorId = SongsAuthorModel::addAuthor($authorStr, $myUid);
                }
//                print_r($authorStr ."\n");
                $successNum++;
            }
//            exit;
            caijiModel::where(['id' => $id])->inc('num', $successNum)->update(['topage'=> $topage+1]);
            $this->success('采集完成！');
        }
   }
    //修改采集规则
   public function edit($id = NULL){
       $row = caijiModel::get(['id' => $id]);
       if (!$row)
           $this->error(__('No Results were found'));
        if($this->request->isPost()){
            $postData = input()['row'];
            Db::startTrans();
            try {
                $where['id'] = $id;
                $re = caijiModel::where($where)->update($postData);
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


    //采集规则列表
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

            $total = caijiModel::where($where)->count();
            $list = caijiModel::where($where)
                ->order($sort, $order)
                ->page($page, $pageSize)
                ->select();

            return json_output($total, $list);

        }
       print_r($this->view->fetch());
    }

}


