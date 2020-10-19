<?php
namespace app\admin\addon\juzi\controller;

use app\common\model\Users;
use app\common\controller\Backend;

use think\Db;
use \fast\Str;
use \fast\File;
use app\admin\addon\juzi\model\Juzi_from as juziFromModel;
use app\admin\addon\juzi\model\Juzi_tag as juziTagModel;
use app\admin\addon\juzi\model\JuziCaijigushirule as caijiModel;
use app\admin\addon\juzi\model\Juzi_author as JuziAuthorModel;
/**
 * 采集古诗
 * @internal
 */
class Caijigushi extends Backend
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
            $yearid = $postData['yearid'];
            if(caijiModel::hasCaiji($url)) {
                $this->error('采集已经被发布过了');
            }
            if($yearid && !is_numeric($yearid)) {
                $postData['yearid'] = caijiModel::addYear($yearid, $this->auth->id);
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
       set_time_limit(0);
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
            $yearid = $row['yearid'];
            $repeat_explode1 = $row['repeat_explode1'];
            $repeat_explode2 = $row['repeat_explode2'];
            $item_explode = $row['item_explode'];
            $son_textstr1 = $row['son_textstr1'];
            $son_textstr2 = $row['son_textstr2'];
            if (!$yearid) $this->error('未配置 yearid');
            if (!$url) $this->error('未配置 url');
            if (!$from_str) $this->error('未配置 from_str');
            if (!$end_str) $this->error('未配置 end_str');
            if (!$repeat_explode1) $this->error('未配置 repeat_explode1');
            if (!$repeat_explode2) $this->error('未配置 repeat_explode2');
            if(!$topage) $topage = 0;
            $url = str_replace('(page)', $topage, $url);
            $response = $usehttps ? File::get_https($url, $fromurl) : File::get_nr($url, $fromurl);
            if (!$response) $this->error('获取不到任何内容');
//           print_r($url);
//           exit;
            $middleStr = Str::sp_($from_str, $end_str, $response);
//           print_r($url);
//           exit;
            if (!$middleStr) $this->error('截取不到任何内容');
            $itemArray = explode($repeat_explode2, $middleStr);
            $successNum = 0;
//            print_r($itemArray);
//            exit;

           $insertJuziFrom = function ($myUid, $title='', $authorId, $contentNew) use($yearid) {
               Db::startTrans();
               try {
                   $postData = [];
                   $postData['ctime'] = time();
                   $postData['title'] = $title;
                   $postData['authorid'] = $authorId;
                   $postData['fromtype'] = juziFromModel::$fromtypeGushi;
                   $postData['yearid'] = $yearid;
                   $postData['content'] = $contentNew;
                   $postData['cuid'] = $myUid;
                   $newSid = juziFromModel::insertGetId($postData);
                   Db::commit();
               } catch (\Exception $e) {
                   Db::rollback();
                   $this->error($e->getMessage());
               }
           };
            foreach ($itemArray as $item_) {
                if(!$item_ || strlen($item_) < 3) continue;
                if(!isset(explode($repeat_explode1, $item_)[1])) {
                    continue;
                }
                $item_ = explode($repeat_explode1, $item_)[1];
                $itemAy = explode($item_explode, $item_);
                //截取标题
                $fromStr = $itemAy[0];
                $fromStr = strip_tags($fromStr);
                $fromStr = str_replace("\n", '',$fromStr);
                $fromStr = trim($fromStr);
                if(!strstr($fromStr, '》')) {
                    $fromStr = "《{$fromStr}》";
                }
                //截取作者
                $authorStr = $itemAy[1];
                $authorStr = strip_tags($authorStr);
                $authorStr = str_replace("\n", '',$authorStr);
                $authorStr = trim($authorStr);
                $authorStr = str_replace("作者：", '',$authorStr);
                //截取链接
                $linkStr = $itemAy[2];
                $linkStr = Str::sp_('href="', '"', $linkStr);
                $linkStr = $fromurl . trim($linkStr);
                $authorId = 0;
                if($authorStr) {
                  $authorId = JuziAuthorModel::addAuthor($authorStr, $myUid);
                }
                $sonResponse = $usehttps ? File::get_https($linkStr, $fromurl) : File::get_nr($linkStr, $fromurl);
                $sonMain = Str::sp_($son_textstr1, $son_textstr2, $sonResponse);
                if(strstr($sonMain, '</a></span>')) {
                    $sonMain = explode('</a></span>', $sonMain)[1];
                }
                if(preg_match( "/<p>\s+<\/p>/", $sonMain)) {
                    $sonMain = preg_replace("/<p>\s+<\/p>/", '[___]',$sonMain);
                    $sonMainAy = explode('[___]', $sonMain);
                    if(isset($sonMainAy[1]) && strlen($sonMainAy[1])>5) {
                        $sonMain = $sonMainAy[1];
                    }
                    $sonMain = trim($sonMain);
                }
                if(preg_match( "/<p>\s+<p>/", $sonMain)) {
                    $sonMain = preg_replace("/<p>\s+<p>/", '[___]',$sonMain);
                    $sonMainAy = explode('[___]', $sonMain);
                    if(isset($sonMainAy[1]) && strlen($sonMainAy[1])>5) {
                        $sonMain = $sonMainAy[1];
                    }
                    $sonMain = trim($sonMain);
                }
                $sonMain = explode('<p style="text-align: center">', $sonMain)[0];
                $sonMain = trim($sonMain);
                $contentNew = strip_tags($sonMain, '<br>');
                $contentNew = preg_replace("/^(\s|　)+/",'', $contentNew); //去掉前面的空格
//                if($fromStr=='《金缕曲二首》') {
//                    print_r($contentNew);
//                    exit;
//                }
                if($lastInfo=juziFromModel::hasFrom($fromStr, $authorId))  {
//                    print_r('in'.$fromStr);exit;
                    $Id = $lastInfo['id'];
                    $editData = [
                        'content' => $contentNew,
                        'fromtype'=> juziFromModel::$fromtypeGushi,
                        'yearid'=> $yearid,
                    ];
                    juziFromModel::where('id', $Id)->update($editData);
                } else {
                    $insertJuziFrom($myUid, $fromStr, $authorId, $contentNew);
                }
//                print_r($authorStr ."\n");
                $successNum++;
            }
//            exit;
            caijiModel::where(['id' => $id])->inc('num', $successNum)->update(['topage'=> $topage+1]);
            $this->success('采集完成！');
        }
   }
   //获取
    public function get($id=null) {
        $row = caijiModel::get(['id' => $id]);
        if(!$row) $this->error('数据不存在');
        $yearid = $row['yearid'];
        if($yearid && is_numeric($yearid)) {
            $row['yearid'] = caijiModel::getYearTitle($yearid);
        }
        $this->result($row, 1);
    }
    //修改采集规则
   public function edit($id = NULL){
       $row = caijiModel::get(['id' => $id]);
       if (!$row)
           $this->error(__('No Results were found'));
        if($this->request->isPost()){
            $postData = input()['row'];
            $yearid = $postData['yearid'];
            Db::startTrans();
            try {
                if($yearid && !is_numeric($yearid)) {
                    $postData['yearid'] = caijiModel::addYear($yearid, $this->auth->id);
                }
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

   //获取年份下拉菜单
    public function getyearselect() {
        $this->success('success', '', Db('gushiyear')->field('id,title')->select());
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


