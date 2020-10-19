<?php
namespace app\admin\addon\juzi\controller\api;
use app\common\controller\Api;
use app\admin\addon\juzi\model\Juzi as JuziModel;
use app\admin\addon\juzi\model\Juzi_type as JuziTypeModel;

//句子分类
class Juzitype extends Api
{
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new JuziTypeModel();
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
            if(JuziTypeModel::where(['title'=>$postData['title'], 'cuid'=> $myUid])->find()) $this->error('分类名字已存在');
            $res = JuziTypeModel::insert($postData);
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
        if(!JuziTypeModel::where($where)->find()) $this->error('数据不存在');
        if($this->request->isPost()){
            $postData = input()['row'];
            if(empty($postData['title'])) $this->error('名字不能为空');
            $postData['opened'] = isset($postData['opened']) ? intval($postData['opened']) : 0;
            $typeOldTitle = JuziTypeModel::getfieldbyid($id, 'title');
            if(!$typeOldTitle) $this->error('分类不存在');
            if($typeOldTitle == JuziTypeModel::$defaultTypeName) $this->error(JuziTypeModel::$defaultTypeName.'不允许编辑');
            if(JuziTypeModel::where(['title'=>$postData['title'],'id'=>['neq', $id]])->find()) $this->error('分类名字已存在');
            $re = JuziTypeModel::where('id', $id)->update($postData);
            if($re){
                $this->success('编辑成功！');
            }else{
                $this->error('编辑失败！');
            }

        }
        $find = JuziTypeModel::where('id',$id)->find();
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
            $typeinfo = JuziTypeModel::where($where)->find();
            if(!$typeinfo) $this->error('数据不存在');
            $this->success('获取成功', $typeinfo);
        }
    }

    //删除分类
    public function del($id = "")
    {
        $typeOldTitle = JuziTypeModel::getfieldbyid($id, 'title');
        $myUid = $this->auth->id;
        $where = [];
        $where['id'] = $id;
        $where['cuid'] = $myUid;
        if(!JuziTypeModel::where($where)->find()) $this->error('数据不存在');
        if(!$typeOldTitle) $this->error('分类不存在');
        if($typeOldTitle == JuziTypeModel::$defaultTypeName) $this->error(JuziTypeModel::$defaultTypeName.'不允许编辑');
        $re = JuziTypeModel::destroy($id);
        if($re){
            $defaultTid = juziTypeModel::getUserDefaultType($myUid);
            JuziModel::releaseJuziType($myUid, $id, $defaultTid);
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }
    }

    //获取我的句子分类 全部
    public function myalltypes(){
        $myUid = $this->auth->id;
        $juziTypeArray = juziTypeModel::field('id,title')->where('cuid', $myUid)->select();
        if(!$juziTypeArray) {
            $typeId = juziTypeModel::getUserDefaultType($myUid);
            $juziTypeArray= [
                [
                    'id' => $typeId,
                    'title' => juziTypeModel::$defaultTypeName,
                ]
            ];
        }
        $this->success('获取成功', $juziTypeArray);
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
            if($keyword) $where['title'] = ['like', "%{$keyword}%"];
            $total = JuziTypeModel::where($where)->count();
            $list = JuziTypeModel::where($where)
                ->order('id', 'desc')
                ->page($page, $pageSize)
                ->select();
            return json_output($total, $list, $pageSize, $page);
        }
        print_r($this->view->fetch());
    }
}