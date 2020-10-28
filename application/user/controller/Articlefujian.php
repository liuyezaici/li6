<?php
//文章附件
namespace app\user\controller;


use app\admin\addon\fujian\model\Fujian;
use app\common\controller\Backend;
use fast\File;
use \app\admin\addon\article\model\Article;
use \app\admin\addon\article\model\ArticleFujian as fileModel;

class Articlefujian extends Backend
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';
    protected $layout = '';
    protected $keyword = '';
    protected $allTypes = [];

    public function _initialize()
    {
        parent::_initialize();
    }

    //移除附件文件
    public function remove_article_fujians() {
        $userId = $this->auth->id;
        $fid = input('fid');
        if(!$fid) $this->error('noid');
        $fileInfo = fileModel::getPostFile($fid, "id,sid,adduid,status,fileurl");
        if(!$fileInfo) return $this->error('文件不存在');
        $id = $fileInfo['id'];
        $sid = $fileInfo['sid'];
        $fileurl = $fileInfo['fileurl'];
        if($fileInfo['status'] !=0 ) $this->error('文件已删除，请刷新!');
        if($fileInfo['adduid'] != $userId) $this->error('文件不属于您，请刷新!');
        fileModel::deleteFile($id);
        //更新文章的附件
        if(file_exists(ROOT_PATH . $fileurl)) unlink(ROOT_PATH. $fileurl);
        Article::refreshArticleFujians($sid);
        if(!File::isLocalFile($fileurl)) File::delHttpFile($fileurl);
        $this->success('删除成功');
    }

    //修改文件的排序
    final function edit_file_order() {
        $userId = $this->auth->id;
        $fid = input('fid');
        $ord_id = input('ord_id', 0, 'int');
        if(!$fid) {
            $this->error('noid');
        }
        $fileInfo = fileModel::field('id,adduid,status')->where('id', $fid)->find();
        if(!$fileInfo) {
            return $this->error('文件不存在');
        }
        $id = $fileInfo['id'];
        if($fileInfo['status'] !=0 ) {
            return $this->error('文件已删除，请刷新!');
        }
        if($fileInfo['adduid'] != $userId) {
            return $this->error('文件不属于您，请刷新!');
        }
        DbBase::updateByData('s_article_fujian', $id, array('order'=> $ord_id), 'id');
        $this->success('修改成功');;
    }

    //修改文件名字
    public function edit_article_fujian_title() {
        $userId = $this->auth->id;
        $fid = input('fid');
        $title = input('title');
        if(!$fid) {
            $this->error('noid');
        }
        if(!$title) {
            return $this->error('文件名不能为空');
        }
        $fileInfo = fileModel::getPostFile($fid, "id,adduid,status");
        if(!$fileInfo) {
            return $this->error('文件不存在');
        }
        $id = $fileInfo['id'];
        if($fileInfo['status'] !=0 ) {
            return $this->error('文件已删除，请刷新!');
        }
        if($fileInfo['adduid'] != $userId) {
            return $this->error('文件不属于您，请刷新!');
        }
        if(!DbBase::updateByData('s_article_fujian', array('filename'=> $title), 'id='.$id)) {
            return  Message::getMsgJson('0044');
        }
        $this->success('修改成功');;
    }
    //加载当前分享的附件

    //移动附件文件
    public function move_post_fujian() {
        $id = input('id');
        $direction = input('direction'); //l r
        if(!$id) $this->error('no id');
        if(!in_array($direction, ['l', 'r'])) {
            $this->error('error direction');
        }
        $fileInfo = Article::getFileById($id);
        if(!$fileInfo) {
            $this->error('文件不存在');
        }
        $pid = $fileInfo['sid'];
        $order = $fileInfo['order'];
        if($fileInfo['status'] !=0 ) {
            $this->error('文件已删除，请刷新!');
        }
        if($direction == 'l') {
            $leftFileInfo = fileModel::getPostFileLeft($pid, $order, "id,order");
            if(!$leftFileInfo) $this->error('最左边了');
            fileModel::editFile($id, ['order'=> $leftFileInfo['order']]);
            fileModel::editFile($leftFileInfo['id'], ['order'=> $order]);
        } else {
            $rightFileInfo = fileModel::getPostFileRight($pid, $order, "id,order");
            if(!$rightFileInfo) $this->error('最右边了');
            fileModel::editFile($id, ['order'=> $rightFileInfo['order']]);
            fileModel::editFile($rightFileInfo['id'], ['order'=> $order]);
        }
        $this->success('修改成功');
    }
    //加载当前分享的附件
    public function load_article_fujians() {
        $page = input('page', 1, 'intval');
        $sid = input('sid', 0, 'intval');
        if(!$sid) {
            $this->error('缺少sid');
        }
        $fileids = Article::getfieldbyid($sid, 'fileids');
        $fileDatas = [];
        $pageInfo = [];
        if($fileids) {
            $fileids = trim($fileids, ',');
            $where = [
                'id' => ['in', $fileids],
                'status' => 0,
            ];
            $pagesize = 10;
            $result = Db('articleFujian')->where($where)->order('order', 'Desc')->paginate($pagesize, false,
                [
                    'page' => $page,
                ]
            );
            $fileDatas = json_decode(json_encode($result), true)['data'];
            $pageInfo = [
                'pagenow' => $page,
                'total' => $result->total(),
                'pagesize' => $pagesize,
            ];
            foreach ($fileDatas as $n => &$fileVal) {
                $fileVal['filesize'] = File::formatBytes($fileVal['filesize']);
                $fileurl = $fileVal['fileurl'];
//              $fileurl = func::ossUrlEncode($fileurl);
                $fileVal['fileurl'] = $fileurl;
                $fileVal['downUrl'] = $fileurl;
                $fileVal['is_img'] = File::isImg($fileVal['geshi']);
            }
        }
        $this->success('获取成功', '', ['fileDatas' => $fileDatas, 'pageInfo' => $pageInfo]);
    }
    //上传文件
    final function upload_files() {
        $userId = $this->auth->id;
        $a_id = input('a_id');
        $savePath = input('save_path');
        $pathSafeHash = input('path_safe_hash');//路径安全码
        if(!$savePath) die('no set save_path');
        $mytime = time();
        //flash插件决定 $_FILES['Filedata']的文件名 Filedata
        if (isset($_FILES['file'])) {
            $fileData = $_FILES['file'];
        } elseif(isset($_FILES['Filedata'])) {
            $fileData = $_FILES['Filedata'];
        } elseif (isset($_FILES['fileList'])) {
            $fileData = $_FILES['fileList'];
        }
        if(!$a_id) die('no a_id');
        if(!$userId) die('no login');
        $rightHash = \Func\Func::makeSafeUploadCode($savePath, $userId);
        if($pathSafeHash != $rightHash ) {
            die('文件上传目录被手动篡改了! '. $pathSafeHash . '|'. $rightHash);
        }
        //获取分享信息
        $articleInfo = Article::getArticle($a_id, 'a_adduid');
        if(!$articleInfo) {
            echo('文章不存在');
            exit;
        }
        $s_adduid = $articleInfo['a_adduid'];
        if($s_adduid != $userId) {
            echo('身份已经切换.请刷新');
            exit;
        }
        if (!empty($fileData)) {
            //得到上传的临时文件流
            $tempFile = $fileData['tmp_name'];
            $fileSize = $fileData['size'];
            $geshi = File::geshi($fileData['name']);
            //允许的文件后缀
            //得到文件原名
            $fileName = Str::getRandChar(16). '.'. $geshi;
            $parthUrl = $savePath .'/'. $fileName;
            $saveRoot = RootPath. trim($savePath, "/");
            File::creatdir($saveRoot);
            $targetUrl = $saveRoot. '/'. $fileName;
            if (move_uploaded_file($tempFile, $targetUrl)){
                Article::addArticleFujian($a_id, $userId, $fileData['name'], $parthUrl, $fileSize, $geshi, $mytime); //更新文件索引
                return Message::getMsgJson('0388', $targetUrl);
            }else{
                echo $fileName."上传失败！";
            }
        } else {
            return $this->error('没有文件');
        }
    }

    //下载文件
    final function down_article_fujian() {
        $userId = $this->auth->id;
        $fid = input('id');
        if(!$fid) {
            $this->error('noid');
        }
        $fileInfo = fileModel::getPostFile($fid, "sid,adduid,status,fileurl");
        if(!$fileInfo) {
            return $this->error('文件不存在');
        }
        $adduid = $fileInfo['adduid'];
        $fileurl = $fileInfo['fileurl'];

        $downUrl = \Func\Func::ossUrlEncode($fileurl);
        print_r("<a href='{$downUrl}' class='btn' target='_blank'>点击下载，或 右键 目标另存为</a>");
        exit;
    }


}
