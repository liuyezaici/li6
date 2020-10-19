<?php
namespace app\admin\addon\juzi\controller\api;
use app\admin\addon\juzi\model\Juzi_author;
use app\common\controller\Api;
use app\admin\addon\juzi\model\Juzi as JuziModel;
use app\admin\addon\juzi\model\Juzi_from as JuziFromModel;
use app\admin\addon\juzi\model\Juzi_author as JuziAuthorModel;

//句子来源
class Juzifrom extends Api
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
    //来源添加
    public function add()
    {
        if ($this->request->isPost()){
            $postData = input()['row'];
            $title = isset($postData['title']) ? trim($postData['title']) : '';
            if(!$title) $this->error('标题不能为空:'.$title);
            $authorStr = isset($postData['author']) ? trim($postData['author']) : '';
            $content = isset($postData['content']) ? trim($postData['content']) : '';
            $fromtype = isset($postData['fromtype']) ? intval($postData['fromtype']) : '';
            if(!$fromtype) $this->error('类型不能为空');
            if(JuziFromModel::allowFromType($fromtype)) $this->error('类型不支持');
            if(!$authorStr) $this->error('作者不能为空');
            $myUid = $this->auth->id;
            $authorId = JuziAuthorModel::addAuthor($authorStr, $myUid);
            $newData = [];
            $newData['uri'] = \fast\Str::getRadomTime(20);
            $newData['title'] = $title;
            $newData['cuid'] = $myUid;
            $newData['ctime'] = time();
            $newData['authorid'] = $authorId;
            $newData['fromtype'] = $fromtype;
            $newData['content'] = $content;
            $newData['contentHash'] = MD5($content);
            if(JuziFromModel::where(['contentHash'=> MD5($content)])->find()) $this->error('文集已存在');
            $res = JuziFromModel::insert($newData);
            $res ? $this->success('添加成功') : $this->error('添加失败');
            $this->success();
        }
    }

    //来源编辑
    public function edit($id = NULL)
    {
        if($this->request->isPost()){
            $myUid = $this->auth->id;
            $where = [];
            $where['id'] = $id;
            $where['cuid'] = $myUid;
            if(!JuziFromModel::where($where)->find()) $this->error('数据不存在');
            $postData = input()['row'];
            $title = isset($postData['title']) ? trim($postData['title']) : '';
            if(!$title) $this->error('标题不能为空');
            $authorStr = isset($postData['author']) ? trim($postData['author']) : '';
            $content = isset($postData['content']) ? trim($postData['content']) : '';
            $fromtype = isset($postData['fromtype']) ? intval($postData['fromtype']) : '';
            if(!$fromtype) $this->error('类型不能为空');
            if(JuziFromModel::allowFromType($fromtype)) $this->error('类型不支持');
            if(!$authorStr) $this->error('作者不能为空');
            $authorId = JuziAuthorModel::addAuthor($authorStr, $myUid);
            $newData = [];
            $newData['title'] = $title;
            $newData['authorid'] = $authorId;
            $newData['fromtype'] = $fromtype;
            $newData['content'] = $content;
            $newData['contentHash'] = MD5($content);
            if(JuziFromModel::where(['contentHash'=> MD5($content),'id'=>['neq', $id]])->find()) $this->error('文集已存在');
            $re = JuziFromModel::where('id', $id)->update($newData);
            if($re){
                $this->success('编辑成功！');
            }else{
                $this->error('编辑失败！');
            }

        }
        $find = JuziFromModel::where('id',$id)->find();
        $this->assign('row', $find);
        return $this->fetch();
    }


    //获取来源
    public function getAllFromType() {
        $allType = JuziFromModel::getAllFromType();
        $outPutData = [];
        foreach ($allType as $id_=>$title_) {
            $outPutData[] = [
                'id' => $id_,
                'title'=> $title_,
            ];
        }
        $this->success('获取成功', $outPutData);
    }

    //搜索来源
    public function search()
    {
        if($this->request->isPost()){
            $fromKey = input('fromStr', '', 'trim');
            $author = input('author', '', 'trim');
            if(!$fromKey) {
                $result = [];
            } else {
                $where = ['title' => ['like', "%{$fromKey}%"]];
                if($author) {
                    $authorId = Juzi_author::getfieldbytitle($author, 'id');
                    $where['authorid'] = $authorId;
                }
                $result = JuziFromModel::field('id,authorid,title')->where($where)->select();
            }
            foreach ($result as &$v) {
                $v['author'] = '';
                if($v['authorid']) {
                    $v['author'] = Juzi_author::getfieldbyid($v['authorid'], 'title');
                    $v['title']  = "(". $v['author'] .")".$v['title'];
                }

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
            $typeinfo = JuziFromModel::where($where)->find();
            if(!$typeinfo) $this->error('数据不存在');
            $this->success('获取成功', $typeinfo);
        }
    }

    //删除来源
    public function del($id = "")
    {
        $typeOldTitle = JuziFromModel::getfieldbyid($id, 'title');
        $myUid = $this->auth->id;
        $where = [];
        $where['id'] = $id;
        $where['cuid'] = $myUid;
        if(!JuziFromModel::where($where)->find()) $this->error('数据不存在');
        if(!$typeOldTitle) $this->error('来源不存在');
        if($typeOldTitle == JuziFromModel::$defaultTypeName) $this->error(JuziFromModel::$defaultTypeName.'不允许编辑');
        $re = JuziFromModel::destroy($id);
        if($re){
            $defaultTid = juziTypeModel::getUserDefaultType($myUid);
            JuziModel::releaseJuziType($myUid, $id, $defaultTid);
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }
    }


}