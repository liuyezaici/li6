<?php

namespace app\user\controller;

use app\admin\addon\fujian\model\Fujian;
use app\common\controller\Frontend;
use app\common\model\Users;
use fast\File;
use fast\Date;
use fast\Addon;
use \app\admin\addon\article\model\Article as ArticleModel;
use \app\admin\addon\article\model\ArticleTypes as ArticleTypesModel;

class Article extends Frontend
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
    //写一篇
    public function write() {
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

    //文章详情
    public function edit($id=NULL){
        $info = ArticleModel::getbyid($id);
        $myUid = $this->auth->id;
        if(!$myUid) $this->error('请先登录');
        if($myUid != $info['uid']) $this->error('身份已经切换');
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
        $articleHeader = $this->view->fetch('top', [
            'allTypes' => $this->allTypes,
            'tab' => '',
            'keyword' => ''
        ]);
        $allTypeOption = ArticleTypesModel::select();
        $rightHtml = $this->view->fetch('editDetails', [
            'articleHeader' =>  $articleHeader,
            'allTypeOption' =>  $allTypeOption,
            'id' =>  $id,
            'info' =>  $info,
        ]);
        $this->view->assign('webTitle',   '编辑文章');
        $this->view->assign('right',   $this->view->fetch('common/right', ['rightHtml' =>  $rightHtml]));
        print_r($this->view->fetch());
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
        $result = ArticleModel::where($where)->order('id', 'Desc')->paginate($pagesize, false,
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
