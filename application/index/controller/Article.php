<?php
namespace app\index\controller;

use app\admin\controller\addons\article\model\Article as ArticleModel;
use app\admin\controller\addons\article\model\Types;
use app\common\controller\Frontend;

class Article extends Frontend
{
    protected $layout = true;

    public function _initialize()
    {
        parent::_initialize();
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
    }


    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';
    protected $keyword = '';



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
        $info['typeName'] = $info['typeid'] ? Types::getFieldById($info['typeid'], 'title') : '';
        //markdown
        include_once(ROOT_PATH . 'assets/libs/markdown/Markdown.php');
        include_once(ROOT_PATH . 'assets/libs/markdown/MarkdownExtra.php');
        $info['content'] = \MarkdownExtra::defaultTransform( $info['content']);
//        $info['content'] = preg_replace("/<img(.+)src=\"([^\"]+)\"(.+)>/", '<img class="lazy"$1data-original=\'$2\' src=\'/assets/img/loading2.gif\'$3>',$info['content']);
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
