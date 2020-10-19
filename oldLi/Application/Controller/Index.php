<?php
//前台模版
use Func\Api;
use Func\DbBase;
use Func\Users;
use Func\Divpage;
use App\Model\Article;

class Index extends Api
{
    public function __construct()
    {
        parent::__construct();
    }


    final function main() {
        $users = new Users();
        $userId = $users->getUserAttrib('userId');
        $page = $this->getOption('page', 1, 'int');
        $typeId = $this->getOption('type', 0, 'int');
        $keyword = $this->getOption('keyword', '', 'trim');
        $where_ = 'a_status = 0';//=0表示删除的
        $model = '/?page={%u}';
        $to_title = '最新文章';
        if($keyword) {
            $where_ .= " AND a_title LIKE '%{$keyword}%'";
            $to_title = '搜索:'.$keyword;
            $model = '/?keyword='. urlencode($keyword) .'&page={%u}';
        }
        if($typeId) {
            $where_ .= " AND a_typeid ={$typeId}";
            $model = '/?keyword='. urlencode($keyword) .'&$type='. $typeId .'&page={%u}';
            $to_title = Article::getTypeName($typeId);
        }
//        $sql = 'SELECT a_id,a_title,a_adduid,a_addtime,a_typeid,a_hit,a_hit FROM `s_articles` '. $where_ .' ORDER BY a_id DESC';
        $pag = new Divpage('s_articles' , $model, $fields = 'a_id,a_title,a_adduid,a_addtime,a_typeid,a_hit,a_hit', $page , $pagesize = 20,
            $menustyle = 'index', '', 'a_id', 'desc', $where_);
        $pag->getDivPage();
        $newArticles = $pag->getPage();
        $articlePages = $pag->getMenu();

        foreach($newArticles as &$newV) {
            // u nick
            $userInfo = Users::getUserInfo($newV['a_adduid'],'u_name,u_nick,u_logo');
            if( !$userInfo) {
                $userInfo['u_name'] = '-';
                $userInfo['u_nick'] = '-';
            }
            $newV['u_name'] = $userInfo['u_name'];
            $newV['typeName'] = Article::getTypeName($newV['a_typeid']);
            $newV['u_logo'] = !$userInfo['u_logo'] ? \Config::get('cfg_default_face') : $userInfo['u_logo'];
        }
        $arr['newArticles'] = $newArticles;
        $arr['articlePages'] = $articlePages;

        //所有分类
        $allTypes = DbBase::getRows('SELECT t_id,t_title FROM `s_articles_types` WHERE t_status=0');
        $arr['typeId'] = $typeId;
        $arr['keyword'] = $keyword;
        $arr['to_title'] = $to_title;
        $arr['allTypes'] = $allTypes;
        $arr['header'] = $this -> readTemp('../header.php', $arr);
        $arr['footer'] = $this -> readTemp('../footer.php', $arr);
        return $this->readTemp('', $arr);//设置模板
    }

}