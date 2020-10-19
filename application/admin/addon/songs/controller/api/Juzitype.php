<?php
namespace app\admin\addon\songs\controller\api;
use app\common\controller\Api;
use app\admin\addon\songs\model\Songs as SongsModel;
use app\admin\addon\songs\model\Songs_type as SongsTypeModel;

//歌曲分类
class Songstype extends Api
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new SongsTypeModel();
    }
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = [''];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];
    //分类添加
    public function add()
    {
        if ($this->request->isPost()){
            $postData = input()['row'];
            if(empty($postData['title'])) $this->error('名字不能为空');
            $myUid = $this->auth->id;
            $postData['ctime'] = time();
            $postData['cuid'] = $myUid;
            $postData['opened'] = isset($postData['opened']) ? intval($postData['opened']) : 1;
            if(SongsTypeModel::where(['title'=>$postData['title'], 'cuid'=> $myUid])->find()) $this->error('分类名字已存在');
            $res = SongsTypeModel::insert($postData);
            $res ? $this->success('添加成功') : $this->error('添加失败');
            $this->success();
        }
        return parent::add();
    }

    //分类编辑
    public function edit($id = NULL)
    {
        $myUid = $this->auth->id;
        $where = [];
        $where['id'] = $id;
        $where['cuid'] = $myUid;
        if(!SongsTypeModel::where($where)->find()) $this->error('数据不存在');
        if($this->request->isPost()){
            $postData = input()['row'];
            if(empty($postData['title'])) $this->error('名字不能为空');
            $postData['opened'] = isset($postData['opened']) ? intval($postData['opened']) : 0;
            $typeOldTitle = SongsTypeModel::getfieldbyid($id, 'title');
            if(!$typeOldTitle) $this->error('分类不存在');
            if($typeOldTitle == SongsTypeModel::$defaultTypeName) $this->error(SongsTypeModel::$defaultTypeName.'不允许编辑');
            if(SongsTypeModel::where(['title'=>$postData['title'],'id'=>['neq', $id]])->find()) $this->error('分类名字已存在');
            $re = SongsTypeModel::where('id', $id)->update($postData);
            if($re){
                $this->success('编辑成功！');
            }else{
                $this->error('编辑失败！');
            }

        }
        $find = SongsTypeModel::where('id',$id)->find();
        $this->assign('row', $find);
        return $this->fetch();
    }


    //获取分类
    public function get($id = NULL)
    {
        $myUid = $this->auth->id;
        if($this->request->isPost()){
            $where = [];
            $where['id'] = $id;
            $where['cuid'] = $myUid;
            $typeinfo = SongsTypeModel::where($where)->find();
            if(!$typeinfo) $this->error('数据不存在');
            $this->success('获取成功', $typeinfo);
        }
    }

    //删除分类
    public function del($id = "")
    {
        $typeOldTitle = SongsTypeModel::getfieldbyid($id, 'title');
        $myUid = $this->auth->id;
        $where = [];
        $where['id'] = $id;
        $where['cuid'] = $myUid;
        if(!SongsTypeModel::where($where)->find()) $this->error('数据不存在');
        if(!$typeOldTitle) $this->error('分类不存在');
        if($typeOldTitle == SongsTypeModel::$defaultTypeName) $this->error(SongsTypeModel::$defaultTypeName.'不允许编辑');
        $re = SongsTypeModel::destroy($id);
        if($re){
            $defaultTid = songsTypeModel::getUserDefaultType($myUid);
            SongsModel::releaseSongsType($myUid, $id, $defaultTid);
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }
    }

    //获取我的歌曲分类 全部
    public function myalltypes(){
        $myUid = $this->auth->id;
        $songsTypeArray = songsTypeModel::field('id,title')->where('cuid', $myUid)->select();
        if(!$songsTypeArray) {
            $typeId = songsTypeModel::getUserDefaultType($myUid);
            $songsTypeArray= [
                [
                    'id' => $typeId,
                    'title' => songsTypeModel::$defaultTypeName,
                ]
            ];
        }
        $this->success('获取成功', $songsTypeArray);
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
            if($keyword) $where['title'] = ['like', "%{$keyword}%"];
            $total = SongsTypeModel::where($where)->count();
            $list = SongsTypeModel::where($where)
                ->order('id', 'desc')
                ->page($page, $pageSize)
                ->select();
            return json_output($total, $list, $pageSize, $page);
        }
        print_r($this->view->fetch());
    }
}