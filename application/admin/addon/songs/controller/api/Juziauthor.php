<?php
namespace app\admin\addon\songs\controller\api;
use app\common\controller\Api;
use app\admin\addon\songs\model\Songs as SongsModel;
use app\admin\addon\songs\model\Songs_author as SongsAuthorModel;

//歌曲作者
class Songsauthor extends Api
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new SongsAuthorModel();
    }
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = [''];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];
    //作者添加
    public function add()
    {
        if ($this->request->isPost()){
            $postData = input()['row'];
            if(empty($postData['title'])) $this->error('名字不能为空');
            $myUid = $this->auth->id;
            if(SongsAuthorModel::where(['title'=>$postData['title'], 'cuid'=> $myUid])->find()) $this->error('来源名字已存在');
            $res = SongsAuthorModel::addAuthor($postData['title'], $myUid);
            $res ? $this->success('添加成功') : $this->error('添加失败');
            $this->success();
        }
        return parent::add();
    }

    //来源编辑
    public function edit($id = NULL)
    {
        $myUid = $this->auth->id;
        $where = [];
        $where['id'] = $id;
        $where['cuid'] = $myUid;
        if(!SongsAuthorModel::where($where)->find()) $this->error('数据不存在');
        if($this->request->isPost()){
            $postData = input()['row'];
            if(empty($postData['title'])) $this->error('名字不能为空');
            $typeOldTitle = SongsAuthorModel::getfieldbyid($id, 'title');
            if(!$typeOldTitle) $this->error('来源不存在');
            if($typeOldTitle == SongsAuthorModel::$defaultTypeName) $this->error(SongsAuthorModel::$defaultTypeName.'不允许编辑');
            if(SongsAuthorModel::where(['title'=>$postData['title'],'id'=>['neq', $id]])->find()) $this->error('来源名字已存在');
            $re = SongsAuthorModel::where('id', $id)->update($postData);
            if($re){
                $this->success('编辑成功！');
            }else{
                $this->error('编辑失败！');
            }

        }
        $find = SongsAuthorModel::where('id',$id)->find();
        $this->assign('row', $find);
        return $this->fetch();
    }


    //搜索
    public function search()
    {
        if($this->request->isPost()){
            $authorStr = input('authorStr', '', 'trim');
            if(!$authorStr) {
                $result = [];
            } else {
                $where = ['title' => ['like', "%{$authorStr}%"]];
                $result = SongsAuthorModel::field('id,title')->where($where)->select();
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
            $typeinfo = SongsAuthorModel::where($where)->find();
            if(!$typeinfo) $this->error('数据不存在');
            $this->success('获取成功', $typeinfo);
        }
    }

    //删除来源
    public function del($id = "")
    {
        $typeOldTitle = SongsAuthorModel::getfieldbyid($id, 'title');
        $myUid = $this->auth->id;
        $where = [];
        $where['id'] = $id;
        $where['cuid'] = $myUid;
        if(!SongsAuthorModel::where($where)->find()) $this->error('数据不存在');
        if(!$typeOldTitle) $this->error('来源不存在');
        if($typeOldTitle == SongsAuthorModel::$defaultTypeName) $this->error(SongsAuthorModel::$defaultTypeName.'不允许编辑');
        $re = SongsAuthorModel::destroy($id);
        if($re){
            $defaultTid = songsTypeModel::getUserDefaultType($myUid);
            SongsModel::releaseSongsType($myUid, $id, $defaultTid);
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }
    }


}