<?php
namespace app\admin\addon\juzi\controller\api;
use app\common\controller\Api;
use app\admin\addon\juzi\model\Juzi as JuziModel;
use app\admin\addon\juzi\model\Juzi_author as JuziAuthorModel;

//句子作者
class Juziauthor extends Api
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new JuziAuthorModel();
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
            if(JuziAuthorModel::where(['title'=>$postData['title'], 'cuid'=> $myUid])->find()) $this->error('来源名字已存在');
            $res = JuziAuthorModel::addAuthor($postData['title'], $myUid);
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
        if(!JuziAuthorModel::where($where)->find()) $this->error('数据不存在');
        if($this->request->isPost()){
            $postData = input()['row'];
            if(empty($postData['title'])) $this->error('名字不能为空');
            $typeOldTitle = JuziAuthorModel::getfieldbyid($id, 'title');
            if(!$typeOldTitle) $this->error('来源不存在');
            if($typeOldTitle == JuziAuthorModel::$defaultTypeName) $this->error(JuziAuthorModel::$defaultTypeName.'不允许编辑');
            if(JuziAuthorModel::where(['title'=>$postData['title'],'id'=>['neq', $id]])->find()) $this->error('来源名字已存在');
            $re = JuziAuthorModel::where('id', $id)->update($postData);
            if($re){
                $this->success('编辑成功！');
            }else{
                $this->error('编辑失败！');
            }

        }
        $find = JuziAuthorModel::where('id',$id)->find();
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
                $result = JuziAuthorModel::field('id,title')->where($where)->select();
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
            $typeinfo = JuziAuthorModel::where($where)->find();
            if(!$typeinfo) $this->error('数据不存在');
            $this->success('获取成功', $typeinfo);
        }
    }

    //删除来源
    public function del($id = "")
    {
        $typeOldTitle = JuziAuthorModel::getfieldbyid($id, 'title');
        $myUid = $this->auth->id;
        $where = [];
        $where['id'] = $id;
        $where['cuid'] = $myUid;
        if(!JuziAuthorModel::where($where)->find()) $this->error('数据不存在');
        if(!$typeOldTitle) $this->error('来源不存在');
        if($typeOldTitle == JuziAuthorModel::$defaultTypeName) $this->error(JuziAuthorModel::$defaultTypeName.'不允许编辑');
        $re = JuziAuthorModel::destroy($id);
        if($re){
            $defaultTid = juziTypeModel::getUserDefaultType($myUid);
            JuziModel::releaseJuziType($myUid, $id, $defaultTid);
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }
    }


}