<?php

namespace app\juzi\controller;

use app\common\controller\Frontend;
use app\common\model\Users;
use fast\Date;
use fast\File;
use fast\Str;
use think\Config;
use fast\Addon;
use \think\Db;
use app\admin\addon\juzi\model\Musictimemodel;
use app\admin\addon\juzi\model\UserMusicArticle;

class Musictime extends Frontend
{
    protected $noNeedLogin = ['index', 'lilist'];
    protected $noNeedRight = '*';
    protected static $webTitle = '';
    protected static $webdesc = '';
    protected static $webLogo = '';
    protected static $tongjiCode = '';
    protected static $footContent = '';

    public function _initialize()
    {
        parent::_initialize();
        //实例化配置组件
        $settingModel = Addon::getModel('setting');
        if(!$settingModel) {
            self::$webTitle = '未安装setting组件';
            self::$webdesc = '未安装setting组件';
            self::$webLogo = Config::get('default_img');
            self::$tongjiCode = '';
            self::$footContent = '';
        } else {
            self::$webTitle = $settingModel->getSetting('web_title');//站点名字设置
            self::$webdesc = $settingModel->getSetting('web_desc');//站点描述
            self::$webLogo = $settingModel->getSetting('web_logo');//站点logo
            self::$tongjiCode = $settingModel->getSetting('tongji_code');//统计代码
            self::$footContent = $settingModel->getSetting('foot_content');//页脚内容
        }
        $this->view->assign('webLogo', self::$webLogo);//站点名
        $this->view->assign('front_header', $this->view->fetch('common/header'));
        $this->view->assign('webTitle', self::$webTitle);//站点名
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('tongjiCode', self::$tongjiCode);//统计代码
        $this->view->assign('footContent', self::$footContent);//页脚内容
    }

    //获取歌曲信息 - 播放
    public function getMusicToPlay() {
        $myUid = $this->auth->id;
        $musicId = input('musicId', 0, 'int');
        $musicInfo = Musictimemodel::field('cuid,songTitle,singer,songGeci,songGeshi,songPathUrl,songSize,songTime')->where('id', $musicId)->find();
        if(!$musicInfo)  return $this->error('记录不存在');
        if($musicInfo['cuid'] != $myUid) return $this->error('身份已经切换');
        return $this->success('success', '', $musicInfo);
    }
    //删除心情
    public function removeXinqing() {
        $id = input('id', 0, 'int');
        if(!$id) return $this->error('id不能为空');
        $myUid = $this->auth->id;
        Db::startTrans();
        try {
            $liInfo = UserMusicArticle::where('id', $id)->find();
            if(!$liInfo) throw new \Exception('记录不存在');
            if($liInfo['cuid'] != $myUid) throw new \Exception('身份已经切换');
            $musicId = $liInfo['musicId'];
            UserMusicArticle::where('id', $id)->delete();
            UserMusicArticle::recountArticles($musicId);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $this->error($e->getMessage());
        }
        return $this->success('删除成功');
    }
    //发布 编辑我的歌曲
    public function post() {
        $id = input('id', 0, 'int');
        $myUid = $this->auth->id;
        if ($this->request->isPost()){
            $rows = input()['row'];
            $id = input('row.id', 0, 'int');
            unset($rows['id']);
            Db::startTrans();
            try {
                if($id) {
                    $musicInfo = Musictimemodel::where('id', $id)->find();
                    if(!$musicInfo) throw new \Exception('记录不存在');
                    if($musicInfo['cuid'] != $myUid) throw new \Exception('身份已经切换');
                    Musictimemodel::where('id', $id)->update($rows);
                } else {
                    $rows['cuid'] = $myUid;
                    $rows['ctime'] = time();
                    Musictimemodel::insert($rows);
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                return $this->error($e->getMessage());
            }
            if($id) {
                return $this->success('保存成功');
            } else {
                return $this->success('发布成功');
            }
        }
        if($id) {
            $musicInfo = Musictimemodel::where('id', $id)->find();
            if(!$musicInfo) return $this->error('记录不存在');
            if($musicInfo['cuid'] != $myUid) return $this->error('身份已经切换');
        } else {
            $musicInfo = [
                'id'=> 0,
                'songTitle'=> '',
                'singer'=> '',
                'songPathUrl'=> '',
                'songSize'=> '',
                'songGeshi'=> '',
                'songGeci'=> '',
                'opened'=>1,
            ];
        }
        if($id) {
            $topTitle ='编辑歌曲';
            $modify ='edit';
            $submitBtnText ='保存';
        } else {
            $modify ='add';
            $submitBtnText ='发布';
            $topTitle ='发布歌曲';
        }
        $this->view->assign('musicInfo', urlencode(json_encode($musicInfo)));
        $this->view->assign('webTitle', $topTitle .  self::$webTitle.']');
        $this->view->assign('topTitle', $topTitle);
        $this->view->assign('modify', $modify);
        $this->view->assign('submitBtnText', $submitBtnText);
        print_r($this->view->fetch());
    }
    //查看心情
    public function liDetails($id=NULL) {
        $myUid = $this->auth->id;
        $id = input('id', 0, 'int');
        if(!$id) {
            return $this->error('id不能为空');
        }
        $liInfo = UserMusicArticle::where('id', $id)->find();
        if(!$liInfo) return $this->error('心情记录不存在');
        if($liInfo['opened']==0) {
            return $this->error('心情已经设为私有.');
        }
        $musicInfo = Musictimemodel::where('id', $liInfo['musicId'])->find();
        if(!$musicInfo) return $this->error('歌曲不存在');
        if($musicInfo['opened']==0) {
            return $this->error('改歌曲下的所有心情已经设为私有.');
        }
        //markdown
        vendor('markdown.Markdown');
        vendor('markdown.MarkdownExtra');
        $liInfo['content'] = \MarkdownExtra::defaultTransform($liInfo['content']);

        $this->view->assign('webTitle', '关于歌曲'. $musicInfo['songTitle']. '-'. $musicInfo['singer']. '的心情'. $id .'.['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('musicInfo', $musicInfo);
        $this->view->assign('liInfo', $liInfo);
        $this->view->assign('id', $id);
        print_r($this->view->fetch());
    }

    //写心情 / 编辑心情
    public function write() {
        $myUid = $this->auth->id;
        if ($this->request->isPost()){
            $rows = input()['row'];
            $musicId = input('row.musicId', 0, 'int');
            $id = input('row.id', 0, 'int');
            $content = input('row.content', '', 'trim');
            if(!$content) {
                return $this->error('内容不能为空');
            }
            unset($rows['id']);
            Db::startTrans();
            try {
                if($id) {
                    $liInfo = UserMusicArticle::where('id', $id)->find();
                    if(!$liInfo) throw new \Exception('记录不存在');
                    if($liInfo['cuid'] != $myUid) throw new \Exception('身份已经切换');
                    UserMusicArticle::where('id',$id)->update($rows);
                } else {
                    $rows['musicId'] = $musicId;
                    $rows['cuid'] = $myUid;
                    $rows['ctime'] = time();
                    UserMusicArticle::insert($rows);
                    Musictimemodel::where('id', $musicId)->setInc('articles', 1);
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                return $this->error($e->getMessage());
            }
            if($id) {
                return $this->success('保存成功');
            } else {
                return $this->success('发布成功');
            }
        }
        $id = input('id', 0, 'int');
        $liId = input('liId', 0, 'int');
        if($liId) {
            $liInfo = UserMusicArticle::where('id', $liId)->find();
            if(!$liInfo) return $this->error('文章不存在');
            if($liInfo['cuid'] != $myUid) return $this->error('身份已经切换');
            $musicId = $liInfo['musicId'];
            $musicInfo = Musictimemodel::where('id', $musicId)->find();
            if(!$musicInfo) return $this->error('记录不存在');
        } else {
            if(!$id) {
                return $this->error('id不能为空');
            }
            $musicId = $id;
            $musicInfo = Musictimemodel::where('id', $id)->find();
            if(!$musicInfo) return $this->error('记录不存在');
            if($musicInfo['cuid'] != $myUid) return $this->error('身份已经切换');
            $liInfo = [
                'opened'=> 1,
                'content'=> '',
            ];
        }
        if($liId) {
            $topTitle ='编辑心情:'.$liId;
            $modify ='edit';
            $submitBtnText ='保存';
        } else {
            $modify ='add';
            $submitBtnText ='发布';
            $topTitle ='写心情';
        }

        $this->view->assign('webTitle', '写心情.['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('musicId', $musicId);
        $this->view->assign('liId', $liId);
        $this->view->assign('topTitle', $topTitle);
        $this->view->assign('musicInfo', $musicInfo);
        $this->view->assign('modify', $modify);
        $this->view->assign('submitBtnText', $submitBtnText);
        $this->view->assign('liInfo', $liInfo);
        print_r($this->view->fetch());
    }

    // 句子心情 首页
    public function liList($id=NULL) {
        $myUid = $this->auth->id;
        $musicId = intval($id);
        $page = input('page', 1, 'int');
        $page = (int)$page;
        $musicInfo = Musictimemodel::where('id', $musicId)->find();
        $musicInfo['songSize'] = File::formatBytes($musicInfo['songSize']);
        $musicInfo['songTime'] = Date::get_second_m_s($musicInfo['songTime']);
        $musicInfo['songGeci'] = str_replace(chr(13), '<br />', $musicInfo['songGeci']);
        $isMine = $myUid == $musicInfo['cuid'];
        if(!$musicInfo) return $this->error('记录不存在');
        $pageSize = 10;
        $path = "/juzi/musicTime/liList/id/{$musicId}";
        $where_ = [
            'musicId' => $musicId,
        ];
        if(!$isMine) $where_['opened'] = 1;
        $list = UserMusicArticle::where($where_)->order('id', 'desc')->paginate($pageSize, false,
            [
                'page'=> $page,
                'path'=> $path,
                'query'=> [
                    'id' => $musicId,
                ],
            ]
        );
        foreach ($list as &$v) {
            $v['authorLink'] = "/juzi/musicTime/author/id/{$v['cuid']}/1";
            $v['userName'] = Users::getfieldbyid($v['cuid'], 'nickname');
            $v['ctime'] = Date::toYMDHI($v['ctime']);
            $v['content'] = mb_strlen($v['content']) > 20 ? mb_substr($v['content'], 0 ,20).'...': $v['content'];
            $v['opened'] = $v['opened']==1 ? '是': '否';
        }
//        print_r(json_encode($list));exit;
        $nullStr = '';
        if(count($list)==0) {
            $nullStr = '听过此歌曲是什么心情? <a href="/juzi/Musictime/write/?id='. $musicId .'" class="btn btn-xs btn-success">写第一篇</a>';
        }
        // 获取分页显示
        $pageMenu = $list->render();
        $this->view->assign('webTitle', ''. $musicInfo['songTitle'] .'-'. $musicInfo['singer'] .'的歌曲心情.['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('musicInfo', $musicInfo);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        $this->view->assign('musicId', $musicId);
        $this->view->assign('isMine', $isMine);
        print_r($this->view->fetch());
    }
    //   句子心情 首页
    public function myMusic() {
        $myUid = $this->auth->id;
        $page = input('page', 1, 'int');
        $page = (int)$page;
        $keyword = input('keyword', '', 'trim');
        $pageSize = 10;
        $path = "/juzi/musicTime/myMusic/";
        $where_ = [
            'cuid' => $myUid
        ];
        $whereOr = [];
        if($keyword) {
            $whereOr['songTitle'] = ['like', "%{$keyword}%"];
        }
        $list = Musictimemodel::field('id,cuid,songTitle,singer,ctime,songSize,songGeshi,opened,articles')
            ->where($where_)->whereOr($whereOr)
            ->order('id', 'desc')
            ->paginate($pageSize, false,
            [
                'page'=> $page,
                'path'=> $path,
                'query'=> [
                    'keyword' => $keyword,
                ],
            ]
        );
        foreach ($list as &$v) {
            $v['authorLink'] = "/juzi/musicTime/author/id/{$v['cuid']}/1";
            $v['userName'] = Users::getfieldbyid($v['cuid'], 'nickname');
            $v['ctime'] = Date::toYMDHI($v['ctime']);
            $v['opened'] = $v['opened'] ? '是': '否';
            $v['songFile'] = $v['songSize'] >0 ? File::formatBytes($v['songSize']) .'<i class="playBtn" title="播放"></i><br />'. $v['songGeshi'] .'': '未上传';
        }
        $nullStr = '';
        if(count($list)==0) {
            if($keyword) {
                $nullStr = '没有搜索结果';
            } else {
                $nullStr = '你难道没有喜欢的歌曲吗? <a href="/juzi/Musictime/post" class="btn btn-xs btn-success">发布一首</a>';
            }
        }
        // 获取分页显示
        $pageMenu = $list->render();
        $this->view->assign('webTitle', '我的歌曲.['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('list', $list);
        $this->view->assign('uid', $myUid);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }

    //   句子心情 首页
    public function index() {
        $myUid = $this->auth->id;
        if ($this->request->isPost()){
            print_r('forbid_post');
            exit;
        }
        $page = input('page', 1, 'int');
        $page = (int)$page;
        $keyword = input('keyword', '', 'trim');
        $pageSize = 10;
        $path = "/juzi/musicTime/index/";
        $where_ = [
            'opened' => 1
        ];
        if($keyword) {
            $where_['songTitle'] = ['like', "%{$keyword}%"];
        }
        $list = Musictimemodel::field('id,cuid,songTitle,singer,ctime')->where($where_)
            ->order('id', 'desc')->paginate($pageSize, false,
            [
                'page'=> $page,
                'path'=> $path,
                'query'=> [
                    'keyword' => $keyword,
                ],
            ]
        );
        foreach ($list as &$v) {
            $v['authorLink'] = "/juzi/musicTime/author/id/{$v['cuid']}/1";
            $v['userName'] = Users::getfieldbyid($v['cuid'], 'nickname');
            $v['ctime'] = Date::toYMDHI($v['ctime']);
        }
        $nullStr = '';
        if(count($list)==0) {
            if($keyword) {
                $nullStr = '没有搜索结果';
            } else {
                $nullStr = '没有记录';
            }
        }
        // 获取分页显示
        $pageMenu = $list->render();
        $this->view->assign('webTitle', '歌曲心情,记录一个人成长的心里历程.['.  self::$webTitle.']');
        $this->view->assign('webdesc', self::$webdesc);//站点名
        $this->view->assign('nullStr', $nullStr);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('list', $list);
        $this->view->assign('pageMenu', $pageMenu);
        print_r($this->view->fetch());
    }


}
