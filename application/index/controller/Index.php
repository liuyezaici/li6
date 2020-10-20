<?php

namespace app\index\controller;

use app\admin\addon\fujian\model\Fujian;
use app\common\controller\Frontend;
use fast\File;
use think\Config;
use think\Db;
use fast\Addon;
use app\admin\library\Auth;
use app\common\model\Users;
use \app\admin\addon\article\model\Article as ArticleModel;
use \app\admin\addon\article\model\ArticleTypes as ArticleTypesModel;

class Index extends Frontend
{

    protected $noNeedLogin = ['details', 'index'];
    protected $noNeedRight = '*';
    protected $layout = '';
    protected $allTypes = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->allTypes = ArticleTypesModel::select();
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

    //文章详情
    public function details($id=NULL){
        $id = intval($id);
        if(!$id) {
            $this->error('缺少参数id');
        }
        $info = ArticleModel::get($id);
        if(!$info) {
            $this->error('数据不存在');
        }
        $myUid = $this->auth->id;
        ArticleModel::updateRq($id);
        $addonName = AppAddon::$addonSourceTypeArticle;
        $commentClass = Addon::getModel('comment');
        $comments = $commentClass->countComment($addonName, $id);
        if(!$comments) $comments = '';
        $zanClass = Addon::getModel('zan');
        $zans = $zanClass->countZan($addonName, $id);
        $zaned = $myUid ? $zanClass->hasZan($addonName, $id, $myUid) : 0;
        if(!$zans) $zans = '';
        $collectClass = Addon::getModel('collect');
        $collects = $collectClass->countCollect($addonName, $id);
        $collected = $myUid ? $collectClass->hasCollected($addonName, $id, $myUid) : 0;
        if(!$collects) $collects = '';

        $articleHeader = $this->view->fetch('top', [
            'allTypes' =>  $this->allTypes,
            'tab' =>  '',
            'keyword' =>  '',
        ]);
        $info['ctime'] = \fast\Date::toYMD($info['ctime']);
        if($info['thatDate']) $info['thatDate'] = \fast\Date::toYMD($info['thatDate']);
        $info['typeName'] = $info['typeId'] ? ArticleTypesModel::getFieldById($info['typeId'], 'title') : '';
        //markdown
        include_once(ROOT_PATH . 'assets/libs/markdown/Markdown.php');
        include_once(ROOT_PATH . 'assets/libs/markdown/MarkdownExtra.php');
        $info['content'] = \MarkdownExtra::defaultTransform( $info['content']);
        $info['content'] = preg_replace("/<img(.+)src=\"([^\"]+)\"(.+)>/", '<img class="lazy"$1data-original=\'$2\' src=\'/assets/img/loading2.gif\'$3>',$info['content']);
        //上一篇 下一篇
        $prevArticle = ArticleModel::getPrevNextArticle($info['typeId'], $id, '<');
        $nextArticle = ArticleModel::getPrevNextArticle($info['typeId'], $id, '>');

        $myUid = $this->auth->id;
        $showEdit = false;
        if($myUid == $info['uid']) {
            $showEdit = true;
        }
        $rightHtml = $this->view->fetch('detailsRight', [
            'articleHeader' =>  $articleHeader,
            'info' =>  $info,
            'showEdit' =>  $showEdit,
            'prevArticle' =>  $prevArticle,
            'nextArticle' =>  $nextArticle,
            'zaned' =>  $zaned,
            'zans' =>  $zans,
            'comments' =>  $comments,
            'collected' =>  $collected,
            'collects' =>  $collects,
            'addonName' =>  $addonName,
        ]);
        $this->view->assign('webTitle',   $info['title'].',beyond资料、歌迷文章、周边讯息');
        $this->view->assign('right',   $this->view->fetch('common/right', ['rightHtml' =>  $rightHtml]));
        print_r($this->view->fetch());
    }

    //我的文章
    public function my(){
        $tab = input('tab', 0, 'int');
        $page = input('page', 1, 'int');
        $keyword = input('keyword', '', 'trim');
        $topTitle = '最新文章';
        $noResultText = '还没有文章';
        $where = [];
        $myUid = $this->auth->id;
        $where['uid'] = $myUid;
        if($tab) {
            $where['typeId'] = $tab;
            $topTitle = '分类:'. ArticleTypesModel::getFieldById($tab, 'title') ;
            $noResultText = '分类没有文章';

        }

        $articleHeader = $this->view->fetch('topMy', [
            'allTypes' => $this->allTypes,
            'tab' => $tab,
            'keyword' => $keyword
        ]);
        if($keyword) {
            $where['title'] = ['like', "%{$keyword}%"];
            $topTitle = '搜索文章:'. $keyword;
            $noResultText = '没有搜索结果';
        }
        $path = "/uc/article/my/?keyword={$keyword}&tab={$tab}&page=[PAGE]";
        $result = ArticleModel::where($where)->order('id', 'Desc')->paginate(10, false,
            [
                'page' => $page,
                'path' => $path,
            ]
        );
        $articleList = json_decode(json_encode($result), true)['data'];
        foreach ($articleList as &$v) {
            $v['thatDate'] = \fast\Date::toYMD($v['thatDate']);
            $v['typeName'] = $v['typeId'] ? ArticleTypesModel::getFieldById($v['typeId'], 'title') : '';
        }
        unset($v);
        $pageMenu = $result->render();
        $rightHtml = $this->view->fetch('myRight', [
            'articleHeader' =>  $articleHeader,
            'topTitle' =>  $topTitle,
            'articleList' =>  $articleList,
            'pageMenu' =>  $pageMenu,
            'noResultText' =>  $noResultText,
        ]);
        $this->view->assign('webTitle',   'beyond资料、歌迷文章、周边讯息');
        $this->view->assign('right',   $this->view->fetch('common/right', ['rightHtml' =>  $rightHtml]));
        print_r($this->view->fetch());
    }

    //文章首页
    public function index(){
        $tab = input('tab', 0, 'int');
        $page = input('page', 1, 'int');
        $keyword = input('keyword', '', 'trim');
        $topTitle = '最新文章';
        $noResultText = '还没有文章';
        $where = [];
        $where['status'] = 1;
        if($tab) {
            $where['typeId'] = $tab;
            $topTitle = '分类:'. ArticleTypesModel::getFieldById($tab, 'title') ;
            $noResultText = '分类没有文章';

        }

        $articleHeader = $this->view->fetch('top', [
            'allTypes' => $this->allTypes,
            'tab' => $tab,
            'keyword' => $keyword
        ]);
        if($keyword) {
            $where['title'] = ['like', "%{$keyword}%"];
            $topTitle = '搜索文章:'. $keyword;
            $noResultText = '没有搜索结果';
        }
        $path = "/uc/article/index/?keyword={$keyword}&tab={$tab}&page=[PAGE]";
        $result = ArticleModel::where($where)->order('id', 'Desc')->paginate(10, false,
            [
                'page' => $page,
                'path' => $path,
            ]
        );
        $articleList = json_decode(json_encode($result), true)['data'];
        foreach ($articleList as &$v) {
            $v['thatDate'] = \fast\Date::toYMD($v['thatDate']);
            $v['typeName'] = $v['typeId'] ? ArticleTypesModel::getFieldById($v['typeId'], 'title') : '';
        }
        unset($v);
        $pageMenu = $result->render();
        $rightHtml = $this->view->fetch('indexRight', [
            'articleHeader' =>  $articleHeader,
            'topTitle' =>  $topTitle,
            'articleList' =>  $articleList,
            'pageMenu' =>  $pageMenu,
            'noResultText' =>  $noResultText,
        ]);
        $this->view->assign('webTitle',   'beyond资料、歌迷文章、周边讯息');
        $this->view->assign('right',   $this->view->fetch('common/right', ['rightHtml' =>  $rightHtml]));
        print_r($this->view->fetch());
    }
}
