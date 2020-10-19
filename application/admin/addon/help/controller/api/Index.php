<?php

namespace app\admin\addon\help\controller\api;

use app\common\controller\Api;
use fast\Random;
use think\Db;
use fast\Addon;

/**
 * 商品接口
 * @internal
 */
class Index extends Api
{
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $this->addonName = 'help';
        $this->model = Addon::getModel($this->addonName);
    }

        public function index(){
		$this->success(__('success'), []);
	}


    /**
     * 通过KeyName索引获取文章(关于我们、服务协议、押金说明...)
     * @author limingqiang
     * @Date:2018/5/22 19:23
     */
    public function getArticleByKeyName()
    {
        $keyname = input('keyname', '', 'trim') ?: $this->error('keyname不能为空');
        $row = $this->model->where('keyname', $keyname)->field('id,title,content,video_url,ctime,pic')->find();
        if (!$row) $this->error('keyname不存在:'.$keyname);
        $row['pic'] = long_url($row['pic']);
        $row['content'] = html_long_url($row['content']);
        $this->success('获取成功', $row);
    }

    //通过分类的keyname获取文章 (例如常见问题分类 => 常见问题1 、常见问题2)
    public function getArticleByTypeKeyName()
    {

        $page = input('page') ?: 1;
        $pagesize = input('pagesize') ?: 20;
        $helpTypeModel = Addon::getModel('help', 'HelpType');
        $keyname = input('keyname') ?: $this->error('keyname不能为空');
        $helpTypeId = $helpTypeModel->where('keyname', $keyname)->value('id');
        if (!$helpTypeId) $this->error('分类的keyname不存在');

        $list_page = list_page($this->model, ['helptype' => $helpTypeId], $page, $pagesize, 'id desc');
        //图片补充全路径
        foreach ($list_page['list'] as $n => &$v) {
            $v['pic'] = long_url($v['pic']);
        }
        unset($v);
        $this->success('获取成功', $list_page);
    }
    //获取所有文章分类
    public function getAllArticleType(){
        $page = input('page') ?: 1;
        $pagesize = input('pagesize') ?: 20;
        $keyname = input('keyname');
        $helpTypeModel = Addon::getModel('help', 'HelpType');
        $where = [];
        if($keyname){
            $where = ['keyname' => $keyname];
        }
        $list_page = list_page($helpTypeModel, $where, $page, $pagesize, 'id desc');
        $this->success('获取成功', $list_page);
    }
    //通过id获取文章
    public function getArticleById(){
        $id = input('id')  ?: $this->error('id不能为空');
        $articleInfo = $this->model->where(['id'=>$id])->field('id,pic,title,content')->find();
        if(!$articleInfo) $this->error('文章不存在');
        $articleInfo['pic'] = long_url($articleInfo['pic']);
        $articleInfo['content'] = html_long_url($articleInfo['content']);
        $this->success('获取成功',$articleInfo ?:[]);
    }

    //检测是否阅读谋篇文章 未读则返回文章信息
    public function checkReadArticle() {
        $keyname = input('keyname', '', 'trim') ?: $this->error('keyname不能为空');
        $sessionId = \think\Session::session_id();
        $cacheName = $sessionId."_".$keyname;
        $read = \think\cache::get($cacheName);
        $article_info = [];
        if(!$read) {
            $article_info = $this->model->getbykeyname($keyname);
            $article_info['pic'] = long_url($article_info['pic']);
            if(!$article_info) $this->error($keyname.'文章不存在');
        }
        $this->success('获取成功', ['read'=> ($read ? true: false), 'article_info'=> $article_info ?: (object)array()]);
    }

    //文章设为已读
    public function setArticleRead() {
        $keyname = input('keyname', '', 'trim') ?: $this->error('keyname不能为空');
        $sessionId = \think\Session::session_id();
        $cacheName = $sessionId."_".$keyname;
        \think\cache::set($cacheName, 1);
        $this->success('成功');
    }
}
