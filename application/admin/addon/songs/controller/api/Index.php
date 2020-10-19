<?php

namespace app\admin\addon\songs\controller\api;

use app\admin\addon\songs\model\Songs;
use app\admin\addon\songs\model\SongsSinger;
use app\common\controller\Api;
use fast\Random;
use think\Db;
use fast\Addon;
use fast\Str;
use app\common\model\Users;
use app\admin\addon\songs\model\Songs as songsModel;
use app\admin\addon\songs\model\SongsSinger as SingerModel;

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
        $this->addonName = 'songs';
        $this->model = Addon::getModel($this->addonName);
    }


    //我的歌曲分类页面
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
            $info = SongsModel::where($where)->find();
            if(!$info) $this->error('数据不存在');
            if($info['author']) {
                $info['authorStr']= SingerModel::getfieldbyid($info['author'], 'title');
            } else {
                $info['authorStr']= '';
            }
            $this->success('获取成功', $info);
        }
    }

    //编辑歌曲
    public function edit() {
        if ($this->request->isPost()){
            $id = input('id', '', 'int');
            $typeid = input('typeid', '', 'int');
            $contentOld = input('content', '', 'trim');
            $fromStr = input('fromStr', '', 'trim');
            $authorStr = input('authorStr', '', 'trim');
            $contentOld = songsModel::changeQuanjiaoCode($contentOld);
            $contentNew = songsModel::removeAllCode($contentOld);
            if($contentOld != $contentNew) {
                $oldArray = Str::splitStr($contentOld);
                $newArray = Str::splitStr($contentNew);
                $diffArray = [];
                foreach ($oldArray as $tmpWord) {
                    if(!in_array($tmpWord, $newArray) && count($diffArray) < 5) {
                        $diffArray[] = $tmpWord;
                        $diffArray = array_unique($diffArray);
                    }
                }
                $this->error('不能输入特殊符号:'. join('', $diffArray));
            }
            $myUid = $this->auth->id;
            if(!$contentNew) $this->error('内容不能为空');
            if(mb_strlen($contentNew)<8) $this->error('歌曲至少8个字');
            if(songsModel::hasSongs($contentNew, $id)) {
                $this->error('歌曲已经存在');
            }
            $authorId = 0;
            if($authorStr) {
                $authorId = SingerModel::addAuthor($authorStr, $myUid);
            }

            Db::startTrans();
            try {
                $postData = [];
//                $postData['typeid'] = $typeid;
                $postData['author'] = $authorId;
                $postData['content'] = $contentNew;
                $postData['contenthash'] = MD5($contentNew);
                songsModel::where('id', $id)->update($postData);
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
                $tagIdArray = songsTagModel::saveSongsTagsIndex($id, $tagArray);
                songsModel::saveSongsTagIds($id, $tagIdArray);
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            $this->success('保存成功');
        }
    }

    /**
     * 前端搜索
     */
    public function ajax_search()
    {
        if ($this->request->isPost()){
            $keyword = input('keyword', '', 'trim');
            $type = input('t', '', 'int');
            if(!$keyword) return $this->success('获取成功', []);
            $likeSql = "%{$keyword}%";
            if(mb_strlen($keyword) > 10) $keyword = mb_substr($keyword, 0, 10);
            //本站歌曲
            $listOutPut = [];
            if($type==1) {
                $list = SongsModel::field('uri,title,singer')->where('title', 'like', $likeSql)->limit(10)
                    ->select();
                foreach ($list as &$v) {
                    $listOutPut[]['text_li'] = SingerModel::getSingerLink($v['singer'], '/') . ':'. "<a href='/juzi/songs/uri/{$v['uri']}' target='_blank'>{$v['title']}</a>";
                }
            } elseif($type==2) { //全网歌曲
                $data = Songs::searchCloudMusic($keyword, 1, false);
                $list = $data['list'];
                foreach ($list as &$v) {
                    $listOutPut[]['text_li'] = SingerModel::getSingerLink($v['singer'], '/') . ':'. "<a href='/juzi/songs/uri/{$v['uri']}' target='_blank'>{$v['title']}</a>";
                }
            } elseif($type==3) { //本站歌手
                $list = SingerModel::field('uri,title')->where('title', 'like', $likeSql)->limit(10)
                    ->select();
                foreach ($list as &$v) {
                    $listOutPut[]['text_li'] = "<a href='/juzi/singer/uri/{$v['uri']}' target='_blank'>{$v['title']}</a>";
                }
            } elseif($type==4) { //全网歌手
                $list = SongsModel::field('uri,title,singer')->where('title', 'like', $likeSql)->limit(10)
                    ->select();
                foreach ($list as &$v) {
                    $listOutPut[]['text_li'] = SingerModel::getSingerLink($v['singer'], '/');
                }
            }
            $this->success('获取成功', $listOutPut);
        }
    }
    /**
     * 歌曲分页列表
     */
    public function index()
    {
        if ($this->request->isPost()){
            $page = input('page', 1, 'int');
            $pageSize = input('page_size', 10, 'int');
            $keyword = input('keyword', '', 'trim');
            $where['cuid'] = $this->auth->id;
            if($keyword) $where['content'] = ['like', "%{$keyword}%"];
            $total = SongsModel::where($where)->count();
            $list = SongsModel::where($where)
                ->order('id', 'desc')
                ->page($page, $pageSize)
                ->select();
            foreach ($list as &$v) {
                $v['typename'] = SongsTypeModel::getfieldbyid($v['typeid'], 'title');
                $v['content'] = mb_substr($v['content'], 0, 20);
                $v['opened'] = SongsTypeModel::getfieldbyid($v['typeid'], 'opened');
            }
            return json_output($total, $list, $pageSize, $page);
        }
        print_r($this->view->fetch());
    }
}
