<?php
namespace app\index\controller;

use app\common\controller\Frontend;
use app\my\model\types;
use \app\index\model\Article;
use fast\Date;

class Index extends Frontend
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



    //首页
    public function index() {
        $typeId = input('get.typeId', 0, 'intval');
        $keyword = input('get.keyword', '', 'trim');
        $page = input('page', 1, 'int');
        $pageSize = 15;
        //过滤掉非英文 针
        preg_match_all("/[\x{4e00}-\x{9fa5}a-zA-Z0-9,，\|\d]/ui", $keyword,$result);
        if($result) {
            $keyword = join('', $result[0]);
        } else {
            $keyword = '';
        }
        $cateList = types::select();
        $where  = [
            'status' => 0
        ];
        if($typeId && is_numeric($typeId)) {
            $where['typeid'] = $typeId;
        }
        if($keyword) {
            $where['title'] = ['like', "{$keyword}%"];
        }
        $pathArray = [
            'page' => $page,
            'typeId' => $typeId,
            'keyword' => $keyword,
        ];
        $result = Article::field('id,title,typeid,ctime,rq')->where($where)
            ->order('id', 'desc')
            ->paginate($pageSize, false, [
                'page' => $page,
                'type'      => 'page\Pagetyle2',
                'query' => $pathArray,
            ]);
        $articleList = $result->getCollection();
        $pageMenu = $result->render();
        foreach ($articleList as $n =>&$v) {
            $v['typeName'] =  Types::getfieldbyid($v['typeid'], 'title');
        }
        unset($v);
        print_r($this->fetch('',
            [
                'typeId' => $typeId,
                'articleList' => $articleList,
                'pageMenu' => $pageMenu,
                'cateList' => $cateList,
            ]
        ));
    }
}
