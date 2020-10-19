<?php

namespace app\admin\addon\juzi\controller\api;

use app\common\controller\Api;
use fast\Random;
use think\Db;
use fast\Addon;
use fast\Str;
use app\common\model\Users;
use app\admin\addon\juzi\model\Juzi as juziModel;
use app\admin\addon\juzi\model\Juzi_type as juziTypeModel;
use app\admin\addon\juzi\model\Juzi_tag as juziTagModel;
use app\admin\addon\juzi\model\Juzi_author as JuziAuthorModel;
use app\admin\addon\juzi\model\Juzi_from as JuziFromModel;

/**
 * 商品接口
 * @internal
 */
class Index extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['ajax_search'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $this->addonName = 'juzi';
        $this->model = Addon::getModel($this->addonName);
    }


    //我的句子分类页面
    public function mytypesPage(){
        return $this->view->fetch();
    }

    //获取
    public function get($id = NULL)
    {
        $myUid = $this->auth->id;
        if($this->request->isPost()){
            $where = [];
            $where['id'] = $id;
            $where['cuid'] = $myUid;
            $info = JuziModel::where($where)->find();
            if(!$info) $this->error('数据不存在');
            if($info['fromid']) {
                $info['fromStr']= JuziFromModel::getfieldbyid($info['fromid'], 'title');
            } else {
                $info['fromStr']= '';
            }
            if($info['author']) {
                $info['authorStr']= JuziAuthorModel::getfieldbyid($info['author'], 'title');
            } else {
                $info['authorStr']= '';
            }
            $this->success('获取成功', $info);
        }
    }
    //添加句子
    public function add() {
        if ($this->request->isPost()){
//            $typeid = input('typeid', '', 'int');
            $contentOld = input('content', '', 'trim');
            $fromStr = input('fromStr', '', 'trim');
            $authorStr = input('authorStr', '', 'trim');
            $contentNew = juziModel::changeQuanjiaoCode($contentOld);
//            $contentNew = juziModel::removeAllCode($contentOld);
//            if($contentOld != $contentNew) {
//                $oldArray = Str::splitStr($contentOld);
//                $newArray = Str::splitStr($contentNew);
//                $diffArray = [];
//                foreach ($oldArray as $tmpWord) {
//                    if(!in_array($tmpWord, $newArray) && count($diffArray) < 5) {
//                        $diffArray[] = $tmpWord;
//                        $diffArray = array_unique($diffArray);
//                    }
//                }
//                $this->error('不能输入特殊符号:'. join('', $diffArray));
//            }
            $myUid = $this->auth->id;
//            if(empty($typeid)) {
//                $typeid = juziTypeModel::getUserDefaultType($myUid);
//            }
            if(!$contentNew) $this->error('内容不能为空');
            if(mb_strlen($contentNew)<4) $this->error('句子至少4个字');
            if(juziModel::hasJuzi($contentNew)) {
                $this->error('句子已经被发布过了');
            }
            $authorId = 0;
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
//                $postData['typeid'] = $typeid;
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
            if(!$newSid) $this->error('未生成句子id');

            $this->success('发布成功');
        }
    }

    //编辑句子
    public function edit() {
        if ($this->request->isPost()){
            $id = input('id', '', 'int');
            $typeid = input('typeid', '', 'int');
            $contentOld = input('content', '', 'trim');
            $fromStr = input('fromStr', '', 'trim');
            $authorStr = input('authorStr', '', 'trim');
            $contentNew = juziModel::changeQuanjiaoCode($contentOld);
//            $contentNew = juziModel::removeAllCode($contentOld);
//            if($contentOld != $contentNew) {
//                $oldArray = Str::splitStr($contentOld);
//                $newArray = Str::splitStr($contentNew);
//                $diffArray = [];
//                foreach ($oldArray as $tmpWord) {
//                    if(!in_array($tmpWord, $newArray) && count($diffArray) < 5) {
//                        $diffArray[] = $tmpWord;
//                        $diffArray = array_unique($diffArray);
//                    }
//                }
//                $this->error('不能输入特殊符号:'. join('', $diffArray));
//            }
            $myUid = $this->auth->id;
            if(!$contentNew) $this->error('内容不能为空');
            if(mb_strlen($contentNew)<4) $this->error('句子至少4个字');
            if(juziModel::hasJuzi($contentNew, $id)) {
                $this->error('句子已经存在');
            }
            $authorId = 0;
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
//                $postData['typeid'] = $typeid;
                $postData['author'] = $authorId;
                $postData['fromid'] = $fromId;
                $postData['content'] = $contentNew;
                $postData['contenthash'] = MD5($contentNew);
                juziModel::where('id', $id)->update($postData);
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
                $tagIdArray = juziTagModel::saveJuziTagsIndex($id, $tagArray);
                juziModel::saveJuziTagIds($id, $tagIdArray);
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            $this->success('保存成功');
        }
    }
    //del
    public function del($id)
    {
        $myUid = $this->auth->id;
        if(!JuziModel::where([
            'id' => $id,
            'cuid' => $myUid,
        ])->find()) {
            $this->error('数据不存在');
        }
        JuziModel::where('id', $id)->delete();
        $this->success('删除成功');
    }
    /**
     * 前端搜索
     */
    public function ajax_search()
    {
        if ($this->request->isPost()){
            $keyword = input('keyword', '', 'trim');
            $t_ = input('t', '', 'trim');
            if(!$keyword) return $this->success('获取成功', []);
            if(mb_strlen($keyword) > 10) $keyword = mb_substr($keyword, 0, 10);
            if(!in_array($t_, ['lr', 'l', 'r'])) $t_ = 'lr';
            if($t_ == 'lr') {
                $likeSql = "%{$keyword}%";
            } elseif($t_ == 'l') {
                $likeSql = "{$keyword}%";
            } elseif($t_ == 'r') {
                $likeSql = "%{$keyword}";
            }
            $list = JuziModel::field('uri,content,cuid,author')->where('content', 'like', $likeSql)->limit(10)
                ->select();
            foreach ($list as &$v) {
                $v['authorName'] = $v['author'] ? juziAuthorModel::getfieldbyid($v['author'], 'title') : Users::getfieldbyid($v['cuid'], 'nickname');
            }
            $this->success('获取成功', $list);
        }
    }
    /**
     * 句子分页列表
     */
    public function index()
    {
        if ($this->request->isPost()){
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');
            $keyword = input('keyword', '', 'trim');
            $where['cuid'] = $this->auth->id;
            if($keyword) $where['content'] = ['like', "%{$keyword}%"];
            $total = JuziModel::where($where)->count();
            $list = JuziModel::where($where)
                ->order('id', 'desc')
                ->page($page, $pageSize)
                ->select();
            foreach ($list as &$v) {
                $v['typename'] = JuziTypeModel::getfieldbyid($v['typeid'], 'title');
                $v['content'] = mb_substr($v['content'], 0, 20);
                $v['opened'] = JuziTypeModel::getfieldbyid($v['typeid'], 'opened');
            }
            return json_output($total, $list, $pageSize, $page);
        }
        print_r($this->view->fetch());
    }
}
