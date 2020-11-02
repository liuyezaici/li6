<?php

namespace app\index\controller;

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

    protected $noNeedLogin = ['details', 'index'];
    protected $noNeedRight = '*';
    protected $layout = '';
    protected $keyword = '';
    protected $allTypes = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->allTypes = ArticleTypesModel::select();
        $this->keyword = input('keyword', '', 'trim');
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
        ArticleModel::updateRq($id);
        $info['typeName'] = $info['typeid'] ? ArticleTypesModel::getFieldById($info['typeid'], 'title') : '';
        //markdown
        include_once(ROOT_PATH . 'assets/libs/markdown/Markdown.php');
        include_once(ROOT_PATH . 'assets/libs/markdown/MarkdownExtra.php');
        $info['content'] = \MarkdownExtra::defaultTransform( $info['content']);
        $info['content'] = preg_replace("/<img(.+)src=\"([^\"]+)\"(.+)>/", '<img class="lazy"$1data-original=\'$2\' src=\'/assets/img/loading2.gif\'$3>',$info['content']);
        //上一篇 下一篇
        $prevArticle = ArticleModel::getPrevNextArticle($info['typeid'], $id, '<');
        $nextArticle = ArticleModel::getPrevNextArticle($info['typeid'], $id, '>');

        $myUid = $this->auth->id;
        $showEdit = false;
        if($myUid == $info['cuid']) {
            $showEdit = true;
        }
        $info['author'] = Users::getfieldbyid($info['cuid'], 'nickname');
        $rightHtml = $this->view->fetch('', [
            'webTitle' =>  $info['title'],
            'info' =>  $info,
            'showEdit' =>  $showEdit,
            'prevArticle' =>  $prevArticle,
            'nextArticle' =>  $nextArticle,
        ]);
        print_r($rightHtml);
    }

}
