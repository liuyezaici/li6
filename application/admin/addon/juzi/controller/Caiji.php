<?php
namespace app\admin\addon\juzi\controller;

use app\common\model\Users;
use app\common\controller\Backend;

use think\Db;
use \fast\Str;
use \fast\File;
use app\admin\addon\juzi\model\Juzi as juziModel;
use app\admin\addon\juzi\model\Juzi_tag as juziTagModel;
use app\admin\addon\juzi\model\JuziCaijirule as caijiModel;
use app\admin\addon\juzi\model\Juzi_author as JuziAuthorModel;
use app\admin\addon\juzi\model\Juzi_from as JuziFromModel;
/**
 * 采集
 * @internal
 */
class Caiji extends Backend
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

    //采集百度诗歌
   public function caijibaidu(){
       set_time_limit(0);
        $myUid = $this->auth->id;
        $lastCaijiAuthor = Db('juziAuthorbaidu')->where(['finished'=>0])->order('id', 'asc')->find();
        if(!$lastCaijiAuthor) {
            $author = '鲁迅';
            $page = 1;
        } else {
            $author = $lastCaijiAuthor['title'];
            $page = $lastCaijiAuthor['toPage']+1;
        }
       $caijiUrl = "https://hanyu.baidu.com/hanyu/ajax/search_list?wd={$author}&from=poem&pn={$page}";
       $response = File::get_https($caijiUrl, 'https://hanyu.baidu.com', '', 0);
       $response = json_decode($response, true);
       $responseMain = ($response['ret_array']);
       if(!isset($responseMain[0]['poems'])) {
           print_r('没有poems');
           print_r($responseMain[0]);
           exit;
       }
       $poems = ($responseMain[0]['poems']['ret_array']);
       $totalPage = ($responseMain[0]['poems']['extra']['total-page']);
       $responseAuthors = ($response['recommend_authors']);
       $successNum = 0;
       foreach ($poems as $item_) {
           $contentOld = trim($item_['body'][0]);
           $fromStr = trim($item_['display_name'][0]);
           $contentOld = juziModel::changeQuanjiaoCode($contentOld);
           $contentNew = juziModel::removeAllCode($contentOld);
           $contentNew = trim($contentNew);
           if(!$contentNew) continue;
           if(juziModel::hasJuzi($contentNew))  {
               continue;
           }
           $authorId = JuziAuthorModel::addAuthor($author, $myUid);
           JuziAuthorModel::addBaiduAuthor($author, $myUid);
           $fromId = JuziFromModel::addFrom($fromStr, $authorId, $myUid);
           Db::startTrans();
           try {
               $postData = [];
               $postData['createtime'] = time();
               $postData['author'] = $authorId;
               $postData['fromid'] = $fromId;
               $postData['content'] = $contentNew;
               $postData['contenthash'] = MD5($contentNew);
               $postData['cuid'] = $myUid;
               $postData['uri'] = \fast\Str::getRadomTime(20);
               $newSid = juziModel::insertGetId($postData);
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
               $tagIdArray = juziTagModel::saveJuziTagsIndex($newSid, $tagArray);
               juziModel::saveJuziTagIds($newSid, $tagIdArray);
               Db::commit();
           } catch (\Exception $e) {
               Db::rollback();
               $this->error($e->getMessage());
           }
           $successNum++;
       }
       if($totalPage == $page) {
           Db('juziAuthorbaidu')->where(['title'=> $author])->update(['finished'=>1,'toPage'=> $page]);
       } else {
           Db('juziAuthorbaidu')->where(['title'=> $author])->update(['toPage'=> $page]);
       }
       foreach ($responseAuthors as $item_) {
           $author = trim($item_['name']);
           $authorId = JuziAuthorModel::addAuthor($author, $myUid);
           JuziAuthorModel::addBaiduAuthor($author, $myUid);
       }
        $this->success('采集完成！');
   }
    //采集text
   public function caijitext(){
       $myUid = $this->auth->id;
       if($this->request->isPost()){
            $postData = input()['row'];
            $author = isset($postData['author']) ? trim($postData['author']): 0;
            $text = isset($postData['text']) ? trim($postData['text']): '';
            $from_str = isset($postData['from_str']) ? trim($postData['from_str']): '';
            $repeat_explode = isset($postData['repeat_explode']) ? trim($postData['repeat_explode']): '';
            if (!$author) $this->error('未填写作者');
            if (!$text) $this->error('未填写文本');
            if (!$from_str) $this->error('未配置 from_str');
            if (!$repeat_explode) $this->error('未配置 repeat_explode');
           if($repeat_explode == '\\n') {
               $repeat_explode = "\n";
           }
            $itemArray = explode($repeat_explode, $text);
            $successNum = 0;
            foreach ($itemArray as $item_) {
                if(!$item_ || strlen($item_) < 3) continue;
                $item_ = strip_tags($item_);
                $item_ = trim($item_);
                $item_ = juziModel::changeQuanjiaoCode($item_);
                $item_ = juziModel::removeAllCode($item_);
                $contentNew = trim($item_);
                if(!$contentNew) continue;
                $contentNew = preg_replace("/^([0-9]+){$from_str}/", '', $contentNew);
                $contentNew = preg_replace("/^([0-9]+)./", '', $contentNew);
                $contentNew = preg_replace("/^([0-9]+)、/", '', $contentNew);
//                print_r($item_);exit;
                $authorId = 0;
                if($author) {
                    $authorId = JuziAuthorModel::addAuthor($author, $myUid);
                }
                if(juziModel::hasJuzi($contentNew))  {
                    juziModel::where(['contenthash' => MD5($contentNew)])->update(['author'=>$authorId]);
//                    echo($contentNew.'内容已经存在:'. MD5($contentNew))."\n";
                    continue;
                }
                $fromId = 0;
                Db::startTrans();
                try {
                    $postData = [];
                    $postData['createtime'] = time();
                    $postData['author'] = $authorId;
                    $postData['fromid'] = $fromId;
                    $postData['content'] = $contentNew;
                    $postData['contenthash'] = MD5($contentNew);
                    $postData['cuid'] = $myUid;
                    $postData['uri'] = \fast\Str::getRadomTime(20);
                    $newSid = juziModel::insertGetId($postData);
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
                    $tagIdArray = juziTagModel::saveJuziTagsIndex($newSid, $tagArray);
                    juziModel::saveJuziTagIds($newSid, $tagIdArray);
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                $successNum++;
            }
            $this->success('采集完成！');
        }
   }
    //采集text 歌词
   public function caijiGeci(){
       $myUid = $this->auth->id;
       if($this->request->isPost()){
            $postData = input()['row'];
            $author = isset($postData['author']) ? trim($postData['author']): 0;
            $text = isset($postData['text']) ? trim($postData['text']): '';
            $repeat_explode = '[';
            if (!$author) $this->error('未填写作者');
            if (!$text) $this->error('未填写文本');
           if($repeat_explode == '\\n') {
               $repeat_explode = "\n";
           }

           $authorId = JuziAuthorModel::addAuthor($author, $myUid);
           $insertJuziFrom = function ($title='', $contentNew) use($authorId, $myUid)  {
               if($contentNew && $fromId = juziFromModel::where(['contentHash' =>  MD5($contentNew), 'authorid'=> $authorId])->value('id')) {
                   return $fromId;
               }
               Db::startTrans();
               try {
                   $postData = [];
                   $postData['ctime'] = time();
                   $postData['title'] = $title;
                   $postData['authorid'] = $authorId;
                   $postData['fromtype'] = juziFromModel::$fromtypeGeci;
                   $postData['yearid'] = 0;
                   $postData['content'] = $contentNew;
                   $postData['cuid'] = $myUid;
                   $postData['uri'] = \fast\Str::getRadomTime(20);
                   if($contentNew) {
                       $postData['contentHash'] = MD5($contentNew);
                   }
                   $newSid = juziFromModel::insertGetId($postData);
                   Db::commit();
               } catch (\Exception $e) {
                   Db::rollback();
                   $this->error($e->getMessage());
               }
               return $newSid;
           };
            $itemArray = explode($repeat_explode, $text);
            $successNum = 0;
            foreach ($itemArray as $item_) {
                if(!$item_ || strlen($item_) < 3) continue;
                $item_ = strip_tags($item_);
                $item_ = trim($item_);
//                $item_ = juziModel::changeQuanjiaoCode($item_);
//                $item_ = juziModel::removeAllCode($item_);
                if(!$item_) continue;
                $array_ = explode(']',$item_);
                $title = trim($array_[0]);
                $contentNew = trim($array_[1]);

                if(JuziFromModel::hasFrom($title, $authorId))  {
//                    juziModel::where(['contenthash' => MD5($contentNew)])->update(['author'=>$authorId]);
//                    echo($contentNew.'内容已经存在:'. MD5($contentNew))."\n";
                    continue;
                }
                $insertJuziFrom($title, $contentNew);
                $successNum++;
            }
            $this->success('采集完成！');
        }
   }

    //开始执行采集 短文学
   public function begincaijiDuanwenxue($id = NULL){
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
            $repeat_explode = $row['repeat_explode'];
            $item_content_explode = $row['item_content_explode'];
            $item_author_explode = $row['item_author_explode'];
            $getson = $row['getson'];
            $son_beginstr = $row['son_beginstr'];
            $son_endstr = $row['son_endstr'];
            $son_textstr = $row['son_textstr'];
            if (!$url) $this->error('未配置 url');
            if (!$from_str) $this->error('未配置 from_str');
            if (!$end_str) $this->error('未配置 end_str');
            if (!$repeat_explode) $this->error('未配置 repeat_explode');
            if(!$topage) $topage = 1;
            $url = str_replace('(page)', $topage, $url);
//            print_r($url);
//            exit;
            $response = $usehttps ? File::get_https($url, $fromurl) : File::get_nr($url, $fromurl);
            $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');//使用该函数对结果进行转码
            if (!$response) $this->error('获取不到任何内容');

            $middleStr = Str::sp_($from_str, $end_str, $response);
            if (!$middleStr) {
                $this->error('截取不到任何内容:'.$url);
            }
            $itemArray = explode($repeat_explode, $middleStr);
            $successNum = 0;
//            print_r($itemArray);
//            exit;
            $insertJuzi = function ($myUid, $authorStr, $fromStr, $contentNew) {
                $authorId = 0;
                $authorStr = str_replace("\n", '', $authorStr);
                $authorStr = trim($authorStr);
                if($authorStr) {
                    $authorId = JuziAuthorModel::addAuthor($authorStr, $myUid);
                }
                $fromId = 0;
                if($fromStr) {
                    $fromId = JuziFromModel::addFrom($fromStr, $authorId, $myUid);
                }
                Db::startTrans();
                try {
                    $postData = [];
                    $postData['createtime'] = time();
                    $postData['author'] = $authorId;
                    $postData['fromid'] = $fromId;
                    $postData['content'] = $contentNew;
                    $postData['contenthash'] = MD5($contentNew);
                    $postData['cuid'] = $myUid;
                    $postData['uri'] = \fast\Str::getRadomTime(20);
                    $newSid = juziModel::insertGetId($postData);
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
                    $tagIdArray = juziTagModel::saveJuziTagsIndex($newSid, $tagArray);
                    juziModel::saveJuziTagIds($newSid, $tagIdArray);
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
            };
            foreach ($itemArray as $item_) {
                if(!$item_ || strlen($item_) < 3) continue;
                if($getson) {
                    $item_ = strip_tags($item_, '<a>');
                    $sonLink = $fromurl . Str::sp_('href="', '"', $item_);
                    $contentOld = Str::sp_('target="_blank">', '</a>', $item_);
                    $contentOld = juziModel::changeQuanjiaoCode($contentOld);
                    $contentNew = juziModel::removeAllCode($contentOld);
                    $contentNew = trim($contentNew);
                    if(!$contentNew) continue;
                    if(juziModel::hasJuzi($contentNew))  {
//                    echo($contentNew.'内容已经存在:'. MD5($contentNew))."\n";
                        continue;
                    }
                    $sonResponse = $usehttps ? File::get_https($sonLink, $fromurl) : File::get_nr($sonLink, $fromurl);
                    $sonMain = Str::sp_($son_beginstr, $son_endstr, $sonResponse);
                    $sonTextMain = explode($son_textstr, $sonMain)[0];
                    //截取来源
                    $fromArray = explode('《', $sonTextMain);
                    $fromStr = end($fromArray);
                    $fromArray = explode('》', $fromStr);
                    $fromStr = current($fromArray);
                    $fromStr = strip_tags($fromStr);
                    $fromStr = str_replace("\n", '', $fromStr);
                    $fromStr = "《{$fromStr}》";
                    //截取作者
                    $authorStr = explode('</a>', $sonTextMain)[0];
                    $authorArray = explode('>', $authorStr);
                    $authorStr = end($authorArray);
//                    print_r($fromStr.'|');
//                    print_r($sonMain);
//                    exit;
                    $insertJuzi($myUid, $authorStr, $fromStr, $contentNew);
                    //截取原文
//                    if(!isset(explode($son_textstr, $sonMain)[1])) {
////                        print_r($sonMain);
////                        exit;
//                        continue;
//                    }
//                    $sonSourceStr= explode($son_textstr, $sonMain)[1];
//                    $sonSourceStr = strip_tags($sonSourceStr);
//                    $sonSourceStr = str_replace('...查看全文', '', $sonSourceStr);
//                    $contentOld = juziModel::changeQuanjiaoCode($sonSourceStr);
//                    $contentNew = juziModel::removeAllCode($contentOld);
//                    $contentNew = trim($contentNew);
//                    $contentNew = str_replace("\n", '', $contentNew);
//                    if(!$contentNew) continue;
//                    if(juziModel::hasJuzi($contentNew))  {
////                    echo($contentNew.'内容已经存在:'. MD5($contentNew))."\n";
//                        continue;
//                    }
//                    $insertJuzi($myUid, $authorStr, $fromStr, $contentNew);
//                    print_r($sonSourceStr);
//                    exit;
                } else {
//                    $item_ = strip_tags($item_);
                    $item_ = trim($item_);
                    if($item_content_explode == '\\n') {
                        $item_content_explode = "\n";
                    }
//                    print_r($item_);
//                    exit;
                    $contentOld = Str::sp_('.html">', '</a>', $item_);
//                    print_r($contentOld.'|');
//                    exit;
//                    $authorAy = explode($item_author_explode, $str2);
//                    $authorStr = $authorAy[0];
//                    $fromStr = $authorAy[1];
                    $authorStr ='佚名';
                    $fromStr ='';
                    $contentOld = juziModel::changeQuanjiaoCode($contentOld);
                    $contentNew = juziModel::removeAllCode($contentOld);
                    $contentNew = trim($contentNew);
                    if(!$contentNew) continue;
                    if(juziModel::hasJuzi($contentNew))  {
//                    echo($contentNew.'内容已经存在:'. MD5($contentNew))."\n";
                        continue;
                    }
                    $insertJuzi($myUid, $authorStr, $fromStr, $contentNew);
                }
                $successNum++;
            }
            caijiModel::where(['id' => $id])->inc('num', $successNum)->update(['topage'=> $topage+1]);
            $this->success('采集完成！');
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
            $repeat_explode = $row['repeat_explode'];
            $item_content_explode = $row['item_content_explode'];
            $item_author_explode = $row['item_author_explode'];
            $getson = $row['getson'];
            $son_beginstr = $row['son_beginstr'];
            $son_endstr = $row['son_endstr'];
            $son_textstr = $row['son_textstr'];
            if (!$url) $this->error('未配置 url');
            if (!$from_str) $this->error('未配置 from_str');
            if (!$end_str) $this->error('未配置 end_str');
            if (!$repeat_explode) $this->error('未配置 repeat_explode');
            if(!$topage) $topage = 1;
            $url = str_replace('(page)', $topage, $url);
//            print_r($url);
//            exit;
            $response = $usehttps ? File::get_https($url, $fromurl) : File::get_nr($url, $fromurl);
            $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');//使用该函数对结果进行转码
            if (!$response) $this->error('获取不到任何内容:'. $url);

            $middleStr = Str::sp_($from_str, $end_str, $response);
            if (!$middleStr) {
                $this->error('截取不到任何内容:'.$url);
            }
            $itemArray = explode($repeat_explode, $middleStr);
            $successNum = 0;
//            print_r($itemArray);
//            exit;
            $insertJuzi = function ($myUid, $authorStr, $fromStr, $contentNew) {
                $authorId = 0;
                $authorStr = str_replace("\n", '', $authorStr);
                $authorStr = trim($authorStr);
                if($authorStr) {
                    $authorId = JuziAuthorModel::addAuthor($authorStr, $myUid);
                }
                $fromId = 0;
                if($fromStr) {
                    $fromId = JuziFromModel::addFrom($fromStr, $authorId, $myUid);
                }
                Db::startTrans();
                try {
                    $postData = [];
                    $postData['createtime'] = time();
                    $postData['author'] = $authorId;
                    $postData['fromid'] = $fromId;
                    $postData['content'] = $contentNew;
                    $postData['contenthash'] = MD5($contentNew);
                    $postData['cuid'] = $myUid;
                    $postData['uri'] = \fast\Str::getRadomTime(20);
                    $newSid = juziModel::insertGetId($postData);
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
                    $tagIdArray = juziTagModel::saveJuziTagsIndex($newSid, $tagArray);
                    juziModel::saveJuziTagIds($newSid, $tagIdArray);
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
            };
            foreach ($itemArray as $item_) {
                if(!$item_ || strlen($item_) < 3) continue;

//                    $item_ = strip_tags($item_);
                    $item_ = trim($item_);
//                print_r($item_.'|'.$item_content_explode.'|'.$item_author_explode);
//                exit;
                    $contentOld = Str::sp_($item_content_explode, $item_author_explode, $item_);
//                    $authorAy = explode($item_author_explode, $str2);
//                    $authorStr = $authorAy[0];
//                    $fromStr = $authorAy[1];
                    $authorStr ='佚名';
                    $fromStr ='';
                    $contentOld = juziModel::changeQuanjiaoCode($contentOld);
                    $contentNew = juziModel::removeAllCode($contentOld);
                    $contentNew = str_replace(' ​', '', $contentNew);
                    $contentNew = trim($contentNew);
//                print_r($contentNew.'|');
//                exit;
                    if(!$contentNew) continue;
                    if(juziModel::hasJuzi($contentNew))  {
//                    echo($contentNew.'内容已经存在:'. MD5($contentNew))."\n";
                        continue;
                    }
                    $insertJuzi($myUid, $authorStr, $fromStr, $contentNew);
                }
                $successNum++;
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


