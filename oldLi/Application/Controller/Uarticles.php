<?php
//文章管理
use  Func\Api;
use  Func\Str;
use  Func\Timer;
use  Func\DbBase;
use  Func\Message;
use Func\Divpage;
use  App\Model\Article;

class Uarticles extends Api
{
    protected static function makeArticleCoverUrl($a_id) {
        return $GLOBALS['cfg_photo_aticle_imagefiles'].'/pic_'. $a_id .'_'. Str::getRam(12) .'.jpg';
    }
    protected $myTime = '';
    protected $userId = 0;

    function __construct()
    {
        parent::__construct();
        $this->myTime = Timer::now();
        if(!$this->userId) return Message::getMessage('获取不到uid');
    }

//每次发布 自动添加临时文章
    final function make_tmp_article() {
        $a_title = $this->getOption('a_title');
        if(!$a_title) {
            return $this->error('请输入标题');
        }
        $newData['a_title'] = $a_title;
        $tmpInfo = DbBase::getRowBy('s_articles', 'a_id', 'a_adduid='. $this->userId .' AND a_tmp=1');
        if($tmpInfo) {
            return Message::getMsgJson('0113', $tmpInfo['a_id']);
        }
        $newData['a_adduid'] = $this->userId;
        $newData['a_addtime'] = $this->myTime;
        $newData['a_tmp'] = 1;
        if(DbBase::insertRows('s_articles', $newData) != 1) {
            return Message::getMsgJson('0114');
        }
        $newId = DbBase::lastInsertId();;
        return Message::getMsgJson('0113', $newId);
    }
    //获取最新分类
    final function get_all_types() {
        $rootTypeData = Article::getAllTypes();
        $rootTypeData = Str::makeSelectData($rootTypeData, 't_id', 't_title');
        return json_encode(['id'=>'0038','info'=> $rootTypeData]);
    }
    //删除文章分类
    final function del_type() {
        $tid = $this->getOption('tid', 0, 'int');
        if( !$tid ){
            return Message::getMsgJson('0065');//缺少数据id
        }
        if(DbBase::ifExist("s_articles", "a_typeid={$tid} AND a_status !=-1")) {
            return $this->error('分类下含有文章,不能删除');
        }
        if(DbBase::deleteBy("s_articles_types", 't_id='.$tid) != 1){
            return (Message::getMsgJson('0040'));//删除失败
        }
        return (Message::getMsgJson('0039')); //删除成功
    }
    //添加 修改文章分类
    final function modify_type() {
        $t_id = $this->getOption('t_id', 0, 'trim'); //分类ID
        $modify = $this->getOption('modify'); //编辑方式
        $t_title = $this->getOption('t_title', 0, 'trim'); //新分类名字
        if(!$t_title) {
            return Message::getMsgJson('0065');//缺少数据id
        }
        if($modify == 'edit' && !$t_id) {
            return Message::getMsgJson('0065');//缺少数据id
        }
        $t_title = urldecode($t_title);
        $newData = array(
            't_title' => $t_title
        );
        if($modify == 'edit') {
            if(DbBase::updateByData("s_articles_types", $t_id, $newData, 't_id')) {
                return  Message::getMsgJson('0043');//返回‘修改成功’
            } else {
                return  Message::getMsgJson('0044');//返回‘修改失败’
            }
        } else if ($modify == 'add') {
            $newData['t_adduid'] = $this->userId;
            $newData['t_addtime'] = $this->myTime;
            if(DbBase::insertRows('s_articles_types',$newData)) {
                return  Message::getMsgJson('0113');//返回‘添加成功’
            } else {
                return  Message::getMsgJson('0114');//返回‘添加失败’
            }
        }
    }
    //删除文章
    final function del_article() {
        $a_id = $this->getOption('a_id', 0, 'int');
        if(!$a_id) {
            return Message::getMsgJson('0065');
        }
        $articleInfo = DbBase::getRowBy('s_articles', 'a_adduid', "a_id=". $a_id ."");
        if(!$articleInfo) {
            return $this->error('文章不存在');
        }

        //移除文章
        DbBase::deleteBy('s_articles', 'a_id='.$a_id);
        return Message::getMsgJson('0039');
    }
    //修改文章信息
    final function submitEditArticle() {
        set_time_limit(0);
        $a_id = $this->getOption('a_id');
        $a_title = $this->getOption('a_title');
        $a_typeid = $this->getOption('a_typeid');
        $modify = $this->getOption('modify');
        $a_content = $this->getOption('a_content');

        if(!$a_id) {
            return Message::getMsgJson('0065');
        }

        //过滤正文内容外链图片
//                $a_content = Str::tohtml($a_content);
//                $savePath = "content_img/{$this->userId}/article/{$a_id}";
//                $a_content = Str::filterImages($a_content, $this->userId, $savePath, $a_id, 'article');
        $articleInfo = DbBase::getRowBy('s_articles', 'a_adduid,a_status,a_tmp', "a_id={$a_id}");
        if(!$articleInfo) {
            return $this->error('分享不存在');
        }
        $oldStatus = $articleInfo['a_status'];
        $a_tmp = $articleInfo['a_tmp'];

        if($oldStatus == -1) {
            return $this->error('文章已经删除，请刷新');
        }
        //获取智能分词系统 分割的关键词
        $newData['a_title'] = $a_title;
        $newData['a_typeid'] = $a_typeid;
        $newData['a_content'] = $a_content;
        $newData['a_tmp'] = 0;//每次编辑 添加 要将 临时变为正规
        if($modify =='add') {
            if($a_tmp !=1) {//如果发布时使用的数据不再是临时的（被其他窗口同时执行发布了），必须新增数据，防止覆盖其他数据。
                $newData['a_adduid'] = $this->userId;
                $newData['a_addtime'] = $this->myTime;
                if(DbBase::insertRows('s_articles', $newData) != 1) {
                    return Message::getMsgJson('0114');
                }
            } else { //发布时 其实是将临时的数据改为正规的数据。
                if(DbBase::updateByData('s_articles', $newData, 'a_id='. $a_id) ==-1) {
                    return Message::getMsgJson('0044');
                }
            }
        } else {
//                    print_r($newData);
//                    exit;
            $articleInfo = DbBase::getRowBy('s_articles', 'a_adduid', "a_id={$a_id}");
            if(!$articleInfo) {
                return $this->error('文章不存在');
            }

            DbBase::updateByData('s_articles', $newData, 'a_id='.$a_id);
        }
        if($modify =='add') {
            return Message::getMsgJson('0113');
        }else {
            return Message::getMsgJson('0043');
        }
    }
    //所有文章分类
    final function all_types() {
        //获取分类树形菜单
        $rootTypeData = Article::getAllTypes($this->userId);
        $arr['class_list'] = json_encode($rootTypeData);
        $htmlname = 'manage/article/all_types.php';
        return $this->readTemp('', $arr);//设置模板
    }
    //添加分类
    final function add_type() {
        $arr = array(
            't_id' => 0,
            't_title' => '',
            'modify' => 'add'
        );
        $htmlname = 'manage/article/f_modify_type.php';
        return $this->readTemp($htmlname, $arr);//设置模板
    }

    //编辑文章分类
    final function edit_type() {
        $t_id = $this->getOption('t_id', 0, 'int');
        //id参数是否为空
        if( !$t_id ){
            print_r(Message::getMessage('0065'));//缺少数据id
            exit;
        }
        $classInfo = DbBase::getRowBy("s_articles_types", "t_id,t_title", "t_id=".$t_id);
        $arr = $classInfo;
        $arr['modify'] = 'edit';
        $htmlname = 'manage/article/f_modify_type.php';
        return $this->readTemp($htmlname, $arr);//设置模板

    }

    //添加文章
    final function add_article() {
        $allTypes = Article::getAllTypes($this->userId);
        $allTypes = Str::makeSelectData($allTypes, 't_id', 't_title');
        $arr = array(
            'a_id' => 0,
            'a_title' => '',
            'allTypes' => json_encode($allTypes),
            'a_typeid' => 0,
            'a_status' => 0,
            'a_content' => '',
            'modify' => 'add',
        );
        $arr['savePath'] = 'upload/post_files/';
        $arr['userId'] = $this->userId;
        $arr['editData'] = json_encode([]);
        $arr['uhash'] = $this->userClass->getUserAttrib('safe_hash');
        $htmlname = 'edit_article.php';
        return $this->readTemp($htmlname, $arr);//设置模板
    }

    //修改文章
    final function edit_article() {
        $uhash = $this->userClass->getUserAttrib('safe_hash');
        $a_id = $this->getOption('a_id', 0, 'int');
        if(!$a_id) {
            Message::Show(Message::getMessage('0065'));//缺少数据id
            exit;
        }
        if(!$this->userId) {
            Message::Show('no uid');//
            exit;
        }
        if(!$uhash) {
            Message::Show('no uid');//
            exit;
        }
        $articleInfo = Article::getArticle($a_id, 'a_id,a_title,a_typeid,a_adduid,a_hit,a_status,a_content');
        if(!$articleInfo) {
            Message::Show('文章不存在');
            exit;
        }

        //markdown代码
        $articleInfo['a_content'] = Str::tohtml($articleInfo['a_content']);
//                        $articleInfo['a_content'] = Str::keepCode($articleInfo['a_content']);
        $allTypes = Article::getAllTypes($this->userId);
        $allTypes = Str::makeSelectData($allTypes, 't_id', 't_title');
        $arr = $articleInfo;
        $arr2 = array(
            'modify' => 'edit',
            'allTypes' => json_encode($allTypes),
        );
        $arr = array_merge($arr, $arr2);
        $arr['savePath'] = 'upload/post_files/';
        $arr['uhash'] = $this->userClass->getUserAttrib('safe_hash');
        $arr['userId'] = $this->userId;
        $arr['editData'] = json_encode($articleInfo);
        return $this->readTemp('', $arr);//设置模板
    }

    final function main() {
        $searchkey = $this->getOption('searchkey');
        $page = $this->getOption('page', 1, 'int');
        $wh_ = '1';
        if( $searchkey ){
            $searchkey = urldecode($searchkey);
            $wh_ .= " AND a_title LIKE '%". $searchkey ."%'";
        }
        $fields = 'a_id,a_title,a_hit,a_addtime,a_status';
        $div = new Divpage('s_articles', "", $fields, $page, 10, '', '', 'a_id', 'desc', $wh_);
        $div -> getDivPage();
        $listResult = $div->getPage();
        $pageInfo = $div->getPageInfo();
        $arr = array(
            'listResult' => json_encode($listResult),
            'pageInfo' => $pageInfo,
            'page' => $page,
            'uhash' => $this->userClass->getUserAttrib('safe_hash'),
            'searchkey' => $searchkey,
        );
        return $this->readTemp('', $arr);//设置模板
    }

}
