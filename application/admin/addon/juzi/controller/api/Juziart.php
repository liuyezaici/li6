<?php
namespace app\admin\addon\juzi\controller\api;
use app\common\controller\Api;
use app\admin\addon\juzi\model\Juzi_fromarticle as ArtModel;
use app\admin\addon\juzi\model\Juzi_from as JuziFromModel;
use app\admin\addon\juzi\model\Juzi_author as JuziAuthorModel;

//文集
class Juziart extends Api
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new JuziFromModel();
    }
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = [''];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];
    //添加文集
    public function add()
    {
        if ($this->request->isPost()){
            $postData = input()['row'];
            if(empty($postData['title'])) $this->error('名字不能为空');
            $authorStr = isset($postData['author']) ? trim($postData['author']) : '';
            $fromstr = isset($postData['fromstr']) ? trim($postData['fromstr']) : '';
            $title = isset($postData['title']) ? trim($postData['title']) : '';
            $content = isset($postData['content']) ? trim($postData['content']) : '';
            $fromtype = isset($postData['fromtype']) ? intval($postData['fromtype']) : '';
            if(!$fromtype) $this->error('类型不能为空');
            if(!$authorStr) $this->error('作者不能为空');
            $myUid = $this->auth->id;
            $authorId = JuziAuthorModel::addAuthor($authorStr, $myUid);
            $fromId = 0;
            if($fromstr) {
                $fromId = JuziFromModel::addFrom($fromstr, $authorId, $myUid, $fromtype);
            }
            $newData = [];
            $newData['title'] = $title;
            $newData['cuid'] = $myUid;
            $newData['ctime'] = time();
            $newData['authorid'] = $authorId;
            $newData['fromid'] = $fromId;
            $newData['content'] = $content;
            if(ArtModel::where(['title'=> $title, 'fromid'=> $fromId])->find()) $this->error('文集已存在');
            $res = ArtModel::insert($newData);
            $res ? $this->success('添加成功') : $this->error('添加失败');
            $this->success();
        }
        return parent::add();
    }

    //来源编辑
    public function edit($id = NULL)
    {
        if($this->request->isPost()){
            $myUid = $this->auth->id;
            $where = [];
            $where['id'] = $id;
            $where['cuid'] = $myUid;
            if(!ArtModel::where($where)->find()) $this->error('数据不存在');
            $postData = input()['row'];
            $authorStr = isset($postData['author']) ? trim($postData['author']) : '';
            $fromstr = isset($postData['fromstr']) ? trim($postData['fromstr']) : '';
            $title = isset($postData['title']) ? trim($postData['title']) : '';
            $content = isset($postData['content']) ? trim($postData['content']) : '';
            $fromtype = isset($postData['fromtype']) ? intval($postData['fromtype']) : '';
            if(!$fromtype) $this->error('类型不能为空');
            if(!$authorStr) $this->error('作者不能为空');
            $myUid = $this->auth->id;
            $authorId = JuziAuthorModel::addAuthor($authorStr, $myUid);
            $fromId = 0;
            if($fromstr) {
                $fromId = JuziFromModel::addFrom($fromstr, $authorId, $myUid, $fromtype);
            }
            if(ArtModel::where(['title'=>$title, 'fromid'=> $fromId,'id'=>['neq', $id]])->find()) $this->error('来源名字已存在');
            $newData = [];
            $newData['title'] = $title;
            $newData['authorid'] = $authorId;
            $newData['fromid'] = $fromId;
            $newData['content'] = $content;
            $re = ArtModel::where('id', $id)->update($newData);
            if($re){
                $this->success('编辑成功！');
            }else{
                $this->error('编辑失败！');
            }
        }
    }

    //搜索来源
    public function search()
    {
        if($this->request->isPost()){
            $fromKey = input('fromStr', '', 'trim');
            if(!$fromKey) {
                $result = [];
            } else {
                $where = ['title' => ['like', "%{$fromKey}%"]];
                $result = JuziFromModel::field('id,title')->where($where)->select();
            }
            $this->success('获取成功', $result);
        }
    }
    //获取来源
    public function get($id = NULL)
    {
        $myUid = $this->auth->id;
        if($this->request->isPost()){
            $where = [];
            $where['id'] = $id;
            $where['cuid'] = $myUid;
            $info = ArtModel::where($where)->find();
            if(!$info) $this->error('数据不存在');
            $authorStr = '';
            if($info['authorid']) $authorStr = JuziAuthorModel::getfieldbyid($info['authorid'], 'title');
            $info['authorstr'] = $authorStr;
            $fromStr = '';
            if($info['fromid']) $fromStr = JuziFromModel::getfieldbyid($info['fromid'], 'title');
            $info['fromstr'] = $fromStr;
            unset($info['authorid']);
            unset($info['fromid']);
            $this->success('获取成功', $info);
        }
    }
    //删除
    public function del($id = "")
    {
        $typeOldTitle = JuziFromModel::getfieldbyid($id, 'title');
        $myUid = $this->auth->id;
        $where = [];
        $where['id'] = $id;
        $where['cuid'] = $myUid;
        if(!ArtModel::where($where)->find()) $this->error('数据不存在');
        if(!$typeOldTitle) $this->error('来源不存在');
        $re = ArtModel::destroy($id);
        if($re){
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }
    }

}