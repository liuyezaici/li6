<?php
namespace app\admin\addon\juzi\controller;

use app\admin\addon\juzi\model\Juzi_author;
use app\common\model\Users;
use app\common\controller\Backend;

use think\Db;
use \fast\Str;
use \fast\File;
use app\admin\addon\juzi\model\Juzi_from as juziFromModel;
use app\admin\addon\juzi\model\Juzi_fromarticle as juziFromArticleModel;
use app\admin\addon\juzi\model\JuziCaijidianjirule as caijiModel;
use app\admin\addon\juzi\model\Juzi_author as juziAuthModel;
/**
 * 采集典籍
 * @internal
 */
class Caijidianji extends Backend
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
            $author = $postData['author'];
            if(caijiModel::hasCaiji($url)) {
                $this->error('采集已经被发布过了');
            }
            if($author && !is_numeric($author)) {
                $postData['authorid'] = juziAuthModel::addAuthor($author, $this->auth->id);
                unset($postData['author']);
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
   public function begincaijiXs($id = NULL){
       set_time_limit(0);
       $myUid = $this->auth->id;
       $row = caijiModel::get(['id' => $id]);
       if (!$row) $this->error(__('No Results were found'));

       $insertJuziFrom = function ($title='', $authorId, $contentNew) use($myUid)  {
           Db::startTrans();
           try {
               $postData = [];
               $postData['ctime'] = time();
               $postData['title'] = $title;
               $postData['authorid'] = $authorId;
               $postData['fromtype'] = juziFromModel::$fromtypeXiaoshuo;
               $postData['yearid'] = 0;
               $postData['content'] = $contentNew;
               $postData['cuid'] = $myUid;
               $postData['uri'] = \fast\Str::getRadomTime(20);
               $newSid = juziFromModel::insertGetId($postData);
               Db::commit();
           } catch (\Exception $e) {
               Db::rollback();
               $this->error($e->getMessage());
           }
           return $newSid;
       };

       if($this->request->isPost()){
            $url = $row['url_reg'];
            $title = $row['title'];
            $authorId = $row['authorid'];
            $fromurl = $row['fromurl'];
            $usehttps = $row['usehttps'];
            $from_str = $row['from_str'];
            $end_str = $row['end_str'];
            $topage = $row['topage'];
            $remove_str1 = $row['remove_str1'];
            $remove_str2 = $row['remove_str2'];
            $link_str1 = $row['link_str1'];
            $link_str2 = $row['link_str2'];
            $son_tit1 = $row['son_tit1'];
            $son_tit2 = $row['son_tit2'];
            $son_textstr1 = $row['son_textstr1'];
            $son_textstr2 = $row['son_textstr2'];
            $son_morelink_str1 = $row['son_morelink_str1'];
            $son_morelink_str2 = $row['son_morelink_str2'];
            if (!$url) $this->error('未配置 url');
            if (!$from_str) $this->error('未配置 from_str');
            if (!$end_str) $this->error('未配置 end_str');
//            if (!$remove_str1) $this->error('未配置 remove_str1');
//          if (!$remove_str2) $this->error('未配置 remove_str2');
            $remove_str2 = str_replace('/','\\/', $remove_str2);
            if(!$fromId = juziFromModel::where(['title' => $title, 'authorid'=> $authorId])->value('id')) {
               $fromId = $insertJuziFrom($title, $authorId, '');
            }
            $caijiSon = function($titleStr, $sonPage) use($id, $usehttps, $fromurl, $fromId, $authorId, $son_tit1, $son_tit2, $son_textstr1, $son_textstr2,$myUid, $son_morelink_str1, $son_morelink_str2) {
//                print_r($sonPage);
//                exit;
                $response = $usehttps ? File::get_https($sonPage, $fromurl) : File::get_nr($sonPage, $fromurl);
                $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');//使用该函数对结果进行转码
//                print_r($son_textstr1);
//                print_r($response);
//                exit;
               //取文章 内容
                $sonMain = Str::sp_($son_textstr1, $son_textstr2, $response);
//                print_r($sonMain);
//                exit;
               $sonMain = strip_tags($sonMain, '<br>');
               $sonMain = trim($sonMain);
               $sonMain = preg_replace("/^(\s|　)+/",'', $sonMain); //去掉前面的空格
               $sonMain = preg_replace("/^(<br>|<BR>)+/",'', $sonMain); //去掉前面的 <br>
                $sonMain = preg_replace("/^(\s|　)+/",'', $sonMain); //去掉前面的空格
               $sonMain = preg_replace("/^(<br>|<BR>)+/",'', $sonMain); //去掉前面的 <br>
                $sonMain = preg_replace("/^(\s|　)+/",'', $sonMain); //去掉前面的空格
               $sonMain = preg_replace("/^(<br>|<BR>)+/",'', $sonMain); //去掉前面的 <br>
               $sonMain = preg_replace("/^(<br>|<BR>)+/",'', $sonMain); //去掉前面的 <br>
                $sonMain = preg_replace("/^(\s|　)+/",'', $sonMain); //去掉前面的空格
                $sonMain = preg_replace("/^(<br>|<BR>)+/",'', $sonMain); //去掉前面的 <br>
//                print_r($sonMain);
//                exit;
               juziFromArticleModel::addArticle($titleStr, $authorId, $fromId, $sonMain, $myUid);
           };
            $response = $usehttps ? File::get_https($url, $fromurl) : File::get_nr($url, $fromurl);
            $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');//使用该函数对结果进行转码
//            print_r($response);
//            exit;
            if (!$response) $this->error('获取不到任何内容');
            $middleStr = Str::sp_($from_str, $end_str, $response);
            if (!$middleStr) $this->error('截取不到任何内容');
            if($remove_str1 && $remove_str2) {
                $middleStr = preg_replace("/$remove_str1(.+)$remove_str2/", '', $middleStr);
            }
            $middleStr = str_replace("\n", '',$middleStr);
            $middleStr = trim($middleStr);
//          print_r($middleStr);
//          exit;
            $pageStr =  strtolower($middleStr);
            $pageStr = strip_tags($pageStr, '<a>');
//          print_r($pageStr);
//          exit;
            $array_ = explode($link_str1, $pageStr);
            if(!$topage) {
               $topage = 0;
            }
            foreach ($array_ as $n=>$v) {
                if(!strstr($v, '<a')) unset($array_[$n]);
            }
//          print_r($array_);
//          exit;
            if(!isset($array_[$topage])) $this->error('采集以及完成');
            $pageItem = $array_[$topage];
//            print_r($pageItem);
//            exit;
            $pageItem = strtolower($pageItem);

            $articleTitle = explode('>', $pageItem)[1];
            $articleTitle = trim($articleTitle);
//            print_r($articleTitle);
//            exit;
            if(!$articleTitle) {
                $topage ++;
                $pageItem = $array_[$topage];
                $pageItem = strtolower($pageItem);
                $articleTitle = explode('>', $pageItem)[1];
                $articleTitle = trim($articleTitle);
                if(!$articleTitle) {
                    $topage ++;
                    $pageItem = $array_[$topage];
                    $pageItem = strtolower($pageItem);
                    $articleTitle = explode('>', $pageItem)[1];
                    $articleTitle = trim($articleTitle);
                }
            }
//            print_r($articleTitle);
//            exit;
            if(strstr($pageItem, 'href="')) {
                $pageLink = Str::sp_('href="', '"', $pageItem);
            } elseif(strstr($pageItem, "href='")) {
                $pageLink = Str::sp_("href='", "'", $pageItem);
            } else {
                $topage ++;
                $pageItem = $array_[$topage];
                $pageItem = strtolower($pageItem);
                $articleTitle = strip_tags($pageItem);
                $articleTitle = trim($articleTitle);
                if(strstr($pageItem, 'href="')) {
                    $pageLink = Str::sp_('href="', '"', $pageItem);
                } elseif(strstr($pageItem, "href='")) {
                    $pageLink = Str::sp_("href='", "'", $pageItem);
                } else {
                    $this->error('采集以及完成');
                }
            }
            $pageLink = $fromurl . $pageLink;
            $caijiSon($articleTitle, $pageLink);
            caijiModel::where(['id' => $id])->update(['topage'=> $topage+1]);
//           print_r($url);
//           exit;
            $this->success('采集完成！');
        }
   }
   public function begincaiji($id = NULL){
       set_time_limit(0);
       $myUid = $this->auth->id;
       $row = caijiModel::get(['id' => $id]);
       if (!$row) $this->error(__('No Results were found'));

       $insertJuziFrom = function ($title='', $authorId, $contentNew) use($myUid)  {
           Db::startTrans();
           try {
               $postData = [];
               $postData['ctime'] = time();
               $postData['title'] = $title;
               $postData['authorid'] = $authorId;
               $postData['fromtype'] = juziFromModel::$fromtypeTonghua;
               $postData['yearid'] = 0;
               $postData['content'] = $contentNew;
               $postData['cuid'] = $myUid;
               $postData['uri'] = \fast\Str::getRadomTime(20);
               $newSid = juziFromModel::insertGetId($postData);
               Db::commit();
           } catch (\Exception $e) {
               Db::rollback();
               $this->error($e->getMessage());
           }
           return $newSid;
       };

       if($this->request->isPost()){
            $url = $row['url_reg'];
            $title = $row['title'];
            $authorId = $row['authorid'];
            $fromurl = $row['fromurl'];
            $usehttps = $row['usehttps'];
            $from_str = $row['from_str'];
            $end_str = $row['end_str'];
            $topage = $row['topage'];
            $remove_str1 = $row['remove_str1'];
            $remove_str2 = $row['remove_str2'];
            $link_str1 = $row['link_str1'];
            $link_str2 = $row['link_str2'];
            $son_tit1 = $row['son_tit1'];
            $son_tit2 = $row['son_tit2'];
            $son_textstr1 = $row['son_textstr1'];
            $son_textstr2 = $row['son_textstr2'];
            $son_morelink_str1 = $row['son_morelink_str1'];
            $son_morelink_str2 = $row['son_morelink_str2'];
            if (!$url) $this->error('未配置 url');
            if (!$from_str) $this->error('未配置 from_str');
            if (!$end_str) $this->error('未配置 end_str');
//            if (!$remove_str1) $this->error('未配置 remove_str1');
//          if (!$remove_str2) $this->error('未配置 remove_str2');
            $remove_str2 = str_replace('/','\\/', $remove_str2);
            if(!$fromId = juziFromModel::where(['title' => $title, 'authorid'=> $authorId])->value('id')) {
               $fromId = $insertJuziFrom($title, $authorId, '');
            }
            $caijiSon = function($titleStr, $sonPage) use($id, $usehttps, $fromurl, $fromId, $authorId, $son_tit1, $son_tit2, $son_textstr1, $son_textstr2,$myUid, $son_morelink_str1, $son_morelink_str2) {
//                print_r($sonPage);
//                exit;
                $response = $usehttps ? File::get_https($sonPage, $fromurl) : File::get_nr($sonPage, $fromurl);
                $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');//使用该函数对结果进行转码
//                print_r($response);
//                exit;
               //取文章 内容
                $sonMain = Str::sp_($son_textstr1, $son_textstr2, $response);
               $sonMain = strip_tags($sonMain, '<br>');
               $sonMain = trim($sonMain);
               $sonMain = preg_replace("/^(\s|　)+/",'', $sonMain); //去掉前面的空格
               $sonMain = preg_replace("/^<br>+/i",'', $sonMain); //去掉前面的 <br>
                $sonMain = preg_replace("/^(\s|　)+/",'', $sonMain); //去掉前面的空格
               $sonMain = preg_replace("/^<br>+/i",'', $sonMain); //去掉前面的 <br>
                $sonMain = preg_replace("/^(\s|　)+/",'', $sonMain); //去掉前面的空格
               $sonMain = preg_replace("/^<br>+/i",'', $sonMain); //去掉前面的 <br>
               $sonMain = preg_replace("/^<br>+/i",'', $sonMain); //去掉前面的 <br>
                $sonMain = preg_replace("/^(\s|　)+/",'', $sonMain); //去掉前面的空格
//                print_r($sonMain);
//                exit;
               juziFromArticleModel::addArticle($titleStr, $authorId, $fromId, $sonMain, $myUid);
           };
            $response = $usehttps ? File::get_https($url, $fromurl) : File::get_nr($url, $fromurl);
            $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');//使用该函数对结果进行转码
//            print_r($response);
//            exit;
            if (!$response) $this->error('获取不到任何内容');
            $middleStr = Str::sp_($from_str, $end_str, $response);
            if (!$middleStr) $this->error('截取不到任何内容');
            if($remove_str1 && $remove_str2) {
                $middleStr = preg_replace("/$remove_str1(.+)$remove_str2/", '', $middleStr);
            }
           $middleStr = str_replace("\n", '',$middleStr);
           $middleStr = trim($middleStr);
//          print_r($middleStr);
//          exit;
           $pageStr =  $middleStr;
           $pageStr = strtolower($pageStr);
           $array_ = explode($link_str1, $pageStr);
           if(!$topage) {
               $topage = 0;
           }
//            print_r($array_);
//            exit;
           if(!isset($array_[$topage])) $this->error('采集以及完成');
           $pageItem = $array_[$topage];
           if(!$pageItem) {
               $topage ++ ;
               $pageItem = $array_[$topage];
           }
           if(strstr($pageItem, '<a')) {
               $pageItem = '<a'.explode('<a', $pageItem)[1];
           }
//           print_r($pageItem);
//           exit;
           $articleTitle = strip_tags($pageItem);
           $articleTitle = trim($articleTitle);
//            print_r($articleTitle);
//            exit;
            if(strstr($pageItem, 'href="')) {
                $pageLink = Str::sp_('href="', '"', $pageItem);
            } elseif(strstr($pageItem, "href='")) {
                $pageLink = Str::sp_("href='", "'", $pageItem);
            } else {
                $this->error('采集以及完成');
            }
            $pageLink = $fromurl . $pageLink;
            $caijiSon($articleTitle, $pageLink);
            caijiModel::where(['id' => $id])->update(['topage'=> $topage+1]);
//           print_r($url);
//           exit;
            $this->success('采集完成！');
        }
   }
    //开始执行采集散文
   public function begincaijiFrom($id = NULL){
       set_time_limit(0);
       $myUid = $this->auth->id;
       $row = caijiModel::get(['id' => $id]);
       if (!$row) $this->error(__('No Results were found'));

       $insertJuziFrom = function ($title='', $authorId, $contentNew) use($myUid)  {
           if($contentNew && $fromId = juziFromModel::where(['contentHash' =>  MD5($contentNew), 'authorid'=> $authorId])->value('id')) {
               return $fromId;
           }
           Db::startTrans();
           try {
               $postData = [];
               $postData['ctime'] = time();
               $postData['title'] = $title;
               $postData['authorid'] = $authorId;
               $postData['fromtype'] = juziFromModel::$fromtypeSanwen;
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

       if($this->request->isPost()){
            $url = $row['url_reg'];
            $title = $row['title'];
            $authorId = $row['authorid'];
            $fromurl = $row['fromurl'];
            $usehttps = $row['usehttps'];
            $from_str = $row['from_str'];
            $end_str = $row['end_str'];
            $topage = $row['topage'];
            $remove_str1 = $row['remove_str1'];
            $remove_str2 = $row['remove_str2'];
            $link_str1 = $row['link_str1'];
            $link_str2 = $row['link_str2'];
            $son_tit1 = $row['son_tit1'];
            $son_tit2 = $row['son_tit2'];
            $son_textstr1 = $row['son_textstr1'];
            $son_textstr2 = $row['son_textstr2'];
            $son_morelink_str1 = $row['son_morelink_str1'];
            $son_morelink_str2 = $row['son_morelink_str2'];
            if (!$url) $this->error('未配置 url');
            if (!$from_str) $this->error('未配置 from_str');
            if (!$end_str) $this->error('未配置 end_str');
//            if (!$remove_str1) $this->error('未配置 remove_str1');
//          if (!$remove_str2) $this->error('未配置 remove_str2');
            $remove_str2 = str_replace('/','\\/', $remove_str2);
            $caijiSon = function($titleStr, $sonPage) use($id, $usehttps, $fromurl, $authorId, $son_tit1, $son_tit2, $son_textstr1, $son_textstr2,$myUid, $son_morelink_str1, $son_morelink_str2,$insertJuziFrom) {
    //          print_r($sonPage);
    //          exit;
                $response = $usehttps ? File::get_https($sonPage, $fromurl) : File::get_nr($sonPage, $fromurl);
                $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');//使用该函数对结果进行转码
    //          print_r($response);
    //          exit;
                //取文章 内容
                $sonMain = Str::sp_($son_textstr1, $son_textstr2, $response);
                $sonMain = strip_tags($sonMain, '<br>');
                $sonMain = trim($sonMain);
                $sonMain = preg_replace("/^(\s|　)+/",'', $sonMain); //去掉前面的空格
                $sonMain = preg_replace("/^<br>+/",'', $sonMain); //去掉前面的 <br>
                $sonMain = preg_replace("/^(\s|　)+/",'', $sonMain); //去掉前面的空格
                $sonMain = preg_replace("/^<br>+/",'', $sonMain); //去掉前面的 <br>
                $sonMain = preg_replace("/^(\s|　)+/",'', $sonMain); //去掉前面的空格
                $sonMain = preg_replace("/^<br>+/",'', $sonMain); //去掉前面的 <br>
                $sonMain = preg_replace("/^<br>+/",'', $sonMain); //去掉前面的 <br>
                $sonMain = preg_replace("/^(\s|　)+/",'', $sonMain); //去掉前面的空格
                $sonMain = str_replace("一鸣扫描，雪儿校对",'', $sonMain); //去掉版权
    //          print_r($sonMain);
    //          exit;
                $fromId = $insertJuziFrom($titleStr, $authorId, $sonMain);
            };
            $response = $usehttps ? File::get_https($url, $fromurl) : File::get_nr($url, $fromurl);
            $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');//使用该函数对结果进行转码
//          print_r($response);
//          exit;
            if (!$response) $this->error('获取不到任何内容');
            $middleStr = Str::sp_($from_str, $end_str, $response);
            if (!$middleStr) $this->error('截取不到任何内容');
            if($remove_str1 && $remove_str2) {
                $middleStr = preg_replace("/$remove_str1(.+)$remove_str2/", '', $middleStr);
            }
            $middleStr = str_replace("\n", '',$middleStr);
            $middleStr = trim($middleStr);
//          print_r($middleStr);
//          exit;
            $pageStr =  $middleStr;
            $pageStr = strtolower($pageStr);
            $array_ = explode($link_str1, $pageStr);
            if(!$topage) {
               $topage = 0;
            }
//            print_r($array_);
//            exit;
            if(!isset($array_[$topage])) $this->error('采集以及完成');
            $pageItem = $array_[$topage];
            if(strstr($pageItem, '<a')) {
                $pageItem = '<a'.explode('<a', $pageItem)[1];
            }
//           print_r($pageItem);
//           exit;
            $articleTitle = strip_tags($pageItem);
            $articleTitle = trim($articleTitle);
//            print_r($articleTitle);
//            exit;
            if(strstr($pageItem, 'href="')) {
                $pageLink = Str::sp_('href="', '"', $pageItem);
            } elseif(strstr($pageItem, "href='")) {
                $pageLink = Str::sp_("href='", "'", $pageItem);
            } else {
                $this->error('采集以及完成');
            }
            $pageLink = $fromurl . $pageLink;
            $caijiSon($articleTitle, $pageLink);
            caijiModel::where(['id' => $id])->update(['topage'=> $topage+1]);
//          print_r($url);
//          exit;
            $this->success('采集完成！');
        }
   }
   //获取
    public function get($id=null) {
        $row = caijiModel::get(['id' => $id]);
        if(!$row) $this->error('数据不存在');
        $authorid = $row['authorid'];
        if($authorid && is_numeric($authorid)) {
            $row['author'] = juziAuthModel::getfieldbyid($authorid, 'title');
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
            $author = $postData['author'];
            Db::startTrans();
            try {
                if($author && !is_numeric($author)) {
                    $postData['authorid'] = juziAuthModel::addAuthor($author, $this->auth->id);
                    unset($postData['author']);
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


