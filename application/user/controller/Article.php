<?php

namespace app\user\controller;

use app\admin\addon\fujian\model\Fujian;
use app\common\controller\Backend;
use app\common\model\Users;
use fast\File;
use fast\Date;
use fast\Addon;
use \app\admin\addon\article\model\Article as ArticleModel;
use \app\admin\addon\article\model\ArticleTypes as ArticleTypesModel;
use think\Db;

class Article extends Backend
{

    protected $noNeedLogin = [];
    protected $noNeedRight = '*';
    protected $layout = '';
    protected $keyword = '';
    protected $allTypes = [];

    public function _initialize()
    {
        parent::_initialize();
    }


//每次发布 自动添加临时文章
    final function make_tmp_article() {
        $title = input('title');
        if(!$title) {
            $this->error('请输入标题');
        }
        $newData['title'] = $title;
        $tmpInfo = ArticleModel::where( ['cuid', $this->auth->id, 'tmp'=>1])->find();
        if($tmpInfo) {
            $this->success('生成成功', '', ['id'=> $tmpInfo['id']]);
        }
        $newData['cuid'] = $this->auth->id;
        $newData['addtime'] = time();
        $newData['tmp'] = 1;
        if(!$newId = ArticleModel::insertGetId($newData)) {
            $this->error('生成失败');
        }
        $this->success('生成成功', '', ['id'=> $newId]);
    }

    //修改文章信息
    final function submitEditArticle() {
        set_time_limit(0);
        $id = input('id');
        $title = input('title');
        $typeid = input('typeid');
        $modify = input('modify');
        $content = input('content');
        if(!$id) {
            $this->error('noid');
        }
        //过滤正文内容外链图片
//                $content = Str::tohtml($content);
//                $savePath = "content_img/{$this->auth->id}/article/{$id}";
//                $content = Str::filterImages($content, $this->auth->id, $savePath, $id, 'article');
        $articleInfo = ArticleModel::where('id', $id)->field('cuid,status,tmp')->find();
        if(!$articleInfo) {
            $this->error('数据不存在');
        }
        $oldStatus = $articleInfo['status'];
        $tmp = $articleInfo['tmp'];

        if($oldStatus == -1) {
            $this->error('文章已经删除，请刷新');
        }
        //获取智能分词系统 分割的关键词
        $newData['title'] = $title;
        $newData['typeid'] = $typeid;
        $newData['content'] = $content;
        $newData['tmp'] = 0;//每次编辑 添加 要将 临时变为正规
        if($modify =='add') {
            if($tmp !=1) {//如果发布时使用的数据不再是临时的（被其他窗口同时执行发布了），必须新增数据，防止覆盖其他数据。
                $newData['cuid'] = $this->auth->id;
                $newData['addtime'] = $this->myTime;
                if(ArticleModel::insertGetId($newData)) {
                    $this->error('写入失败');
                }
            } else { //发布时 其实是将临时的数据改为正规的数据。
                if(ArticleModel::where('id', $id)->update($newData)) {
                    $this->error('更新失败');
                }
            }
        } else {
            $articleInfo = ArticleModel::where('id', $id)->find();
            if(!$articleInfo) {
                $this->error('文章不存在');
            }
            ArticleModel::where('id', $id)->update($newData);
        }
        if($modify=='add') $this->success('发布成功');
        $this->success('编辑成功');
    }

    //写一篇
    public function add_article() {
        $myUid = $this->auth->id;
        if($this->request->isPost()) {
            $rows = input('post.row/a');
            if(!$rows) {
                $this->error('no rows');
            }
            $title = isset($rows['title']) ? trim($rows['title']) : '';
            $content = isset($rows['content']) ? trim($rows['content']) : '';
            $typeId = isset($rows['typeId']) ? intval($rows['typeId']) : 0;
            if(!$typeId) $this->error('请选择分类');
            if(!$title) $this->error('请输入标题');
            if(!$content) $this->error('请输入内容');
            //过滤内容的附件
            $rows['uid'] = $myUid;
            $rows['ctime'] = time();
            $sid = ArticleModel::insertGetId($rows);

            //     /uploads/article/2020-09-21/uid_1/
             preg_match_all('/\/uploads\/article\/([^\/]+)\/uid_([\d]+)\/([^\)]+)/i', $content, $matches);
             //Array
            //(
            //    [0] => Array
            //        (
            //            [0] => /uploads/article/2020-09-21/uid_1/20200921003851drWNdd.png)
            //            [1] => /uploads/article/2020-09-21/uid_1/202009210052nW6opJoi.png)
            //        )
            //
            //    [1] => Array
            //        (
            //            [0] => 2020-09-21
            //            [1] => 2020-09-21
            //        )
            //
            //    [2] => Array
            //        (
            //            [0] => 1
            //            [1] => 1
            //        )
            //
            //    [3] => Array
            //        (
            //            [0] => 20200921003851drWNdd.png
            //            [1] => 202009210052nW6opJoi.png
            //        )
            //
            //)
            $fullUrls = $matches[0];
            $fullDates = $matches[1];
            $fullFileNames = $matches[3];
            foreach ($fullUrls as $index=>$url_) {
                if(file_exists(ROOT_PATH . ltrim($url_, '/'))) {
                     $newFilePath = '/uploads/article/'. $myUid .'/' . $sid . '/'. $fullFileNames[$index];
                     File::creatdir(dirname(ROOT_PATH . ltrim($newFilePath, '/')));
                     @copy(ROOT_PATH . ltrim($url_, '/'), ROOT_PATH . ltrim($newFilePath, '/'));
                     @unlink(ROOT_PATH . ltrim($url_, '/'));
                     $content = str_replace($url_, $newFilePath , $content);
                     Fujian::editFilePath('article', MD5($url_), $newFilePath, $sid);
                } else {
//                    print_r(ROOT_PATH . ltrim($url_, '/'));exit;
                }
            }
            ArticleModel::where('id', $sid)->update([
                'content' => $content
            ]);
            $this->success('发布成功');
        }
        $articleHeader = $this->view->fetch('top', [
            'allTypes' => $this->allTypes,
            'tab' => '',
            'keyword' => ''
        ]);
        $allTypeOption = ArticleTypesModel::select();
        $rightHtml = $this->view->fetch('writeDetails', [
            'articleHeader' =>  $articleHeader,
            'allTypeOption' =>  $allTypeOption,
            'id' =>  0,
            'uid' =>  $myUid,
        ]);
        $this->view->assign('webTitle',   '写文章');
        $this->view->assign('right',   $this->view->fetch('common/right', ['rightHtml' =>  $rightHtml]));
        print_r($this->view->fetch());
    }

    //移动附件文件
    final function move_post_fujian() {
        $userId = $this->userClass->getUserAttrib('userId');
        $id = input('id');
        $direction = input('direction'); //l r
        if(!$id) $this->error('no id');
        if(!in_array($direction, ['l', 'r'])) {
            $this->error('error direction');
        }
        $fileInfo = ArticleModel::getFileById($id);
        if(!$fileInfo) {
            $this->error('文件不存在');
        }
        $pid = $fileInfo['sid'];
        $order = $fileInfo['order'];
        if($fileInfo['status'] !=0 ) {
            $this->error('文件已删除，请刷新!');
        }
        if($direction == 'l') {
            $leftFileInfo = ArticleModel::getPostFileLeft($pid, $order, "id,order");
            if(!$leftFileInfo) $this->error('最左边了');
            ArticleModel::editFile($id, ['order'=> $leftFileInfo['order']]);
            ArticleModel::editFile($leftFileInfo['id'], ['order'=> $order]);
        } else {
            $rightFileInfo = ArticleModel::getPostFileRight($pid, $order, "id,order");
            if(!$rightFileInfo) $this->error('最右边了');
            ArticleModel::editFile($id, ['order'=> $rightFileInfo['order']]);
            ArticleModel::editFile($rightFileInfo['id'], ['order'=> $order]);
        }
        return  $this->success('修改成功');
    }

    //加载当前分享的附件
    public function load_article_fujians() {
        $page = input('page', 1, 'intval');
        $sid = input('sid', 0, 'intval');
        if(!$sid) {
            $this->error('缺少sid');
        }
        $fileids = ArticleModel::getfieldbyid($sid, 'fileids');
        $fileDatas = [];
        $pageInfo = [];
        if($fileids) {
            $fileids = trim($fileids, ',');
            $where = [
                'id' => ['in', $fileids],
                'status' => 0,
            ];
            $pagesize = 10;
            $result = Db('articleFujian')->where($where)->order('order', 'Desc')->paginate($pagesize, false,
                [
                    'page' => $page,
                ]
            );
            $fileDatas = json_decode(json_encode($result), true)['data'];
            $pageInfo = [
                'pagenow' => $page,
                'total' => $result->total(),
                'pagesize' => $pagesize,
            ];
            foreach ($fileDatas as $n => &$fileVal) {
                $fileVal['filesize'] = File::formatBytes($fileVal['filesize']);
                $fileurl = $fileVal['fileurl'];
//              $fileurl = func::ossUrlEncode($fileurl);
                $fileVal['fileurl'] = $fileurl;
                $fileVal['downUrl'] = $fileurl;
                $fileVal['is_img'] = File::isImg($fileVal['geshi']);
            }
        }
        $this->success('获取成功', '', ['fileDatas' => $fileDatas, 'pageInfo' => $pageInfo]);
    }

    //删除
    public function del($id=NULL){
        $id = intval($id);
        if(!$id) {
            $this->error('缺少参数id');
        }
        $info = ArticleModel::get($id);
        if(!$info) {
            $this->error('数据不存在');
        }
        $uid = ArticleModel::getfieldbyid($id, 'uid');
        $myUid = $this->auth->id;
        if($myUid != $uid) $this->error('身份已经切换');
        //删除附件
        Fujian::removeAddonFile('article', $id);
        ArticleModel::where('id', $id)->delete();
        $this->success('删除成功');
    }
    //添加分类
    final function add_type() {
        $myUid = $this->auth->id;
        if($this->request->isPost()) {
            $title = input('title', '', 'trim');
            if(!$title) $this->error('no title');
            if(ArticleTypesModel::where(['title' => $title])->find()) $this->error('分类已经存在');
            $newData = [
                'title' => $title,
                'cuid' => $myUid,
                'addtime' => time(),
            ];
            ArticleTypesModel::insert($newData);
            $this->success('添加成功');
        }
        $arr = array(
            'id' => 0,
            'title' => '',
            'modify' => 'add'
        );
        $mainHtml = $this->view->fetch('', $arr);
        print_r($mainHtml);
    }

    //编辑文章分类
    final function edit_type() {
        $id = input('id', 0, 'int');
        //id参数是否为空
        if( !$id ){
            $this->error('no id');
        }
        $classInfo = ArticleTypesModel::where('id', $id)->find();
        if(!$classInfo) $this->error('分类不存在');
        if($this->request->isPost()) {
            $title = input('title', '', 'trim');
            if(!$title) $this->error('no title');
            if(ArticleTypesModel::where(['title' => $title, 'id'=>['<>', $id]])->find()) $this->error('分类已经存在');
            $editData = [
                'title' => $title,
            ];
            ArticleTypesModel::where('id', $id)->update($editData);
            $this->success('添加成功');
        }
        $mainHtml = $this->view->fetch('', json_decode(json_encode($classInfo), true));
        print_r($mainHtml);
    }

    //编辑文章分类
    final function del_type() {
        $id = input('id', 0, 'int');
        //id参数是否为空
        if( !$id ){
            $this->error('no id');
        }
        ArticleTypesModel::where('id', $id)->delete();
        $this->error('删除成功');
    }

    //管理所有分类
    public function manageAllTypes(){
        $list = ArticleTypesModel::field('id,title')->order('id', 'desc')->select();
        //获取分类树形菜单
        $mainHtml = $this->view->fetch('', [
          'class_list' => json_encode($list)
        ]);
        print_r($mainHtml);
    }
    //获取所有分类
    public function getAllTypes($id=NULL){
        $list = ArticleTypesModel::field('id,title')->select();
        $this->success('获取成功', '', ['list' => $list]);
    }
    //获取详情
    public function get($id=NULL) {
        $info = ArticleModel::getbyid($id);
        $this->success('获取成功', '', $info);
    }

    //编辑详情
    public function edit($id=NULL){
        $info = ArticleModel::getbyid($id);
        $myUid = $this->auth->id;
        if($myUid != $info['cuid']) $this->error('身份已经切换:'.$myUid . '!='. $info['cuid']);
        if($this->request->isPost()) {
            $rows = input('post.row/a');
            if(!$rows) {
                $this->error('no rows');
            }
            $id = intval($rows['id']);
            $title = isset($rows['title']) ? trim($rows['title']) : '';
            $content = isset($rows['content']) ? trim($rows['content']) : '';
            $typeId = isset($rows['typeId']) ? intval($rows['typeId']) : 0;
            if(!$typeId) $this->error('请选择分类');
            if(!$title) $this->error('请输入标题');
            if(!$content) $this->error('请输入内容');
            ArticleModel::where('id', $id)->update($rows);
            $this->success('编辑成功');
        }
        $mainHtml = $this->view->fetch('', [
            'modify' =>  'edit',
            'id' =>  $id,
            'savePath' =>  'upload/post_files/',
            'upload_safe_code' =>  \fast\Str::makeSafeUploadCode('upload/post_files/', $myUid), //生成安全码 防止上传路径被手动篡改
        ]);
        print_r($mainHtml);
    }


    //我的文章
    public function index(){
        $typeid = input('typeid', 0, 'int');
        $page = input('page', 1, 'int');
        $keyword = input('keyword', '', 'trim');
        $topTitle = '我的文章';
        $noResultText = '还没有文章';
        $pagesize = 10;
        $where = [];
        $myUid = $this->auth->id;
//        $where['uid'] = $myUid;
        if($typeid) {
            $where['typeid'] = $typeid;
            $topTitle = '分类:'. ArticleTypesModel::getFieldById($typeid, 'title') ;
            $noResultText = '分类没有文章';
        }
        if($keyword) {
            $where['title'] = ['like', "%{$keyword}%"];
            $topTitle = '搜索文章:'. $keyword;
            $noResultText = '没有搜索结果';
        }
        $path = "/user/article/?keyword={$keyword}&typeid={$typeid}&page=[PAGE]";
        $result = ArticleModel::field('id,title,rq,typeid')->where($where)->order('id', 'Desc')->paginate($pagesize, false,
            [
                'page' => $page,
                'path' => $path,
            ]
        );
        $articleList = json_decode(json_encode($result), true)['data'];
        foreach ($articleList as &$v) {
            $v['typeName'] = $v['typeid'] ? ArticleTypesModel::getFieldById($v['typeid'], 'title') : '';
        }
        unset($v);
        $pageInfo = [
            'pagenow' => $page,
            'total' => $result->total(),
            'pagesize' => $pagesize,
        ];
//        print_r(json_encode($pageInfo));exit;
        $mainHtml = $this->view->fetch('', [
            'webTitle' =>  '我的文章',
            'allTypes' => $this->allTypes,
            'typeid' => $typeid,
            'keyword' => $keyword,
            'topTitle' =>  $topTitle,
            'articleList' =>  json_encode($articleList),
            'noResultText' =>  $noResultText,
            'page' =>  $page,
            'pageInfo' =>  $pageInfo,
        ]);
        print_r($mainHtml);
    }

}
