<?php
//文章附件

use  Func\Api;
use  Func\Str;
use  Func\File;
use  Func\Timer;
use  Func\DbBase;
use  Func\Message;
use Func\Divpage;
use  App\Model\Article;

class Uarticlefujian extends Api
{
    function __construct()
    {
        parent::__construct();
    }

    //移除附件文件
    final function remove_article_fujians() {
        $userId = $this->userClass->getUserAttrib('userId');
        $fid = $this->getOption('fid');
        if(!$fid) return Message::getMsgJson('0065');
        $fileInfo = Article::getPostFile($fid, "f_id,f_sid,f_adduid,f_status,f_fileurl");
        if(!$fileInfo) return $this->error('文件不存在');
        $f_id = $fileInfo['f_id'];
        $f_sid = $fileInfo['f_sid'];
        $fileurl = $fileInfo['f_fileurl'];
        if($fileInfo['f_status'] !=0 ) return $this->error('文件已删除，请刷新!');
        if($fileInfo['f_adduid'] != $userId) return $this->error('文件不属于您，请刷新!');
        if(!Article::updateFile($f_id, array('f_status'=> -1))) return  Message::getMsgJson('0044');
        //更新文章的附件
        if(file_exists(ROOT_PATH . $fileurl)) unlink(ROOT_PATH. $fileurl);
        Article::refreshArticleFujians($f_sid);
        if(!File::isLocalFile($fileurl)) File::delHttpFile($fileurl);
        return  Message::getMsgJson('0039');
    }

    //修改文件的排序
    final function edit_file_order() {
        $userId = $this->userClass->getUserAttrib('userId');
        $fid = $this->getOption('fid');
        $ord_id = $this->getOption('ord_id', 0, 'int');
        if(!$fid) {
            return Message::getMsgJson('0065');
        }
        $fileInfo = Article::getPostFile($fid, "f_id,f_adduid,f_status");
        if(!$fileInfo) {
            return $this->error('文件不存在');
        }
        $f_id = $fileInfo['f_id'];
        if($fileInfo['f_status'] !=0 ) {
            return $this->error('文件已删除，请刷新!');
        }
        if($fileInfo['f_adduid'] != $userId) {
            return $this->error('文件不属于您，请刷新!');
        }
        DbBase::updateByData('s_article_fujian', $f_id, array('f_order'=> $ord_id), 'f_id');
        return  Message::getMsgJson('0043');
    }
    //移动附件文件
    final function move_post_fujian() {
        $userId = $this->userClass->getUserAttrib('userId');
        $fid = $this->getOption('f_id');
        $direction = $this->getOption('direction'); //l r
        if(!$fid) return Message::getMsgJson('0065');
        if(!in_array($direction, ['l', 'r'])) {
            return Message::getMsgJson('0065');
        }
        $fileInfo = Article::getPostFile($fid, "f_id,f_sid,f_adduid,f_order,f_status");
        if(!$fileInfo) {
            return $this->error('文件不存在');
        }
        $f_id = $fileInfo['f_id'];
        $f_pid = $fileInfo['f_sid'];
        $f_order = $fileInfo['f_order'];
        if($fileInfo['f_status'] !=0 ) {
            return $this->error('文件已删除，请刷新!');
        }
        if($fileInfo['f_adduid'] != $userId) {
            return $this->error('文件不属于您，请刷新!');
        }
        if($direction == 'l') {
            $leftFileInfo = Article::getPostFileLeft($f_pid, $f_order, "f_id,f_order");
            if(!$leftFileInfo) return $this->error('最左边了');
            Article::updateFile($f_id, ['f_order'=> $leftFileInfo['f_order']]);
            Article::updateFile($leftFileInfo['f_id'], ['f_order'=> $f_order]);
        } else {
            $rightFileInfo = Article::getPostFileRight($f_pid, $f_order, "f_id,f_order");
            if(!$rightFileInfo) return $this->error('最右边了');
            Article::updateFile($f_id, ['f_order'=> $rightFileInfo['f_order']]);
            Article::updateFile($rightFileInfo['f_id'], ['f_order'=> $f_order]);
        }
        return  Message::getMsgJson('0043');
    }
    //修改文件名字
    final function edit_article_fujian_title() {
        $userId = $this->userClass->getUserAttrib('userId');
        $fid = $this->getOption('fid');
        $title = $this->getOption('title');
        if(!$fid) {
            return Message::getMsgJson('0065');
        }
        if(!$title) {
            return $this->error('文件名不能为空');
        }
        $fileInfo = Article::getPostFile($fid, "f_id,f_adduid,f_status");
        if(!$fileInfo) {
            return $this->error('文件不存在');
        }
        $f_id = $fileInfo['f_id'];
        if($fileInfo['f_status'] !=0 ) {
            return $this->error('文件已删除，请刷新!');
        }
        if($fileInfo['f_adduid'] != $userId) {
            return $this->error('文件不属于您，请刷新!');
        }
        if(!DbBase::updateByData('s_article_fujian', array('f_filename'=> $title), 'f_id='.$f_id)) {
            return  Message::getMsgJson('0044');
        }
        return  Message::getMsgJson('0043');
    }
    //加载当前分享的附件
    final function load_article_fujians() {
        $page = $this->getOption('page', 1, 'int');
        $a_id = $this->getOption('a_id');
        if(!$a_id) {
            return $this->error('缺少a_id');
        }
        $sInfo = DbBase::getRowBy('s_articles', 'a_fileids', "a_id='{$a_id}'");
        if(!$sInfo) {
            return $this->error('文章不存在');
        }
        $fileDatas = [];
        $pageInfo = [];
        $fileids = $sInfo['a_fileids'];
        if($fileids) {
            $fileids = trim($fileids, ',');
            $fields ='f_id,f_filename,f_filesize,f_geshi,f_order,f_fileurl';
            $whereSql = "f_id IN({$fileids}) AND f_status = 0";
            $div = new Divpage('s_article_fujian', "", $fields, $page, 10, 9, 'articleFujianGotoPage', 'f_order', 'desc', $whereSql);
            $div -> getDivPage();
            $fileDatas = $div->getPage();
            $pageInfo = $div->getMenu();
            foreach ($fileDatas as $n => &$fileVal) {
                $fileVal['filesize'] = File::formatBytes($fileVal['f_filesize']);
                $fileurl = $fileVal['f_fileurl'];
//              $fileurl = func::ossUrlEncode($fileurl);
                $fileVal['f_fileurl'] = $fileurl;
                $fileVal['downUrl'] = $fileurl;
                $fileVal['is_img'] = File::isImg($fileVal['f_geshi']);
            }
        }
        return Message::getMsgJson('0038', ['fileDatas' => $fileDatas, 'pageInfo' => $pageInfo]);
    }
    //上传文件
    final function upload_files() {
        $userId = $this->userClass->getUserAttrib('userId');
        $a_id = $this->getOption('a_id');
        $savePath = $this->getOption('save_path');
        $pathSafeHash = $this->getOption('path_safe_hash');//路径安全码
        if(!$savePath) die('no set save_path');
        $mytime = Timer::now();
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
            $saveRoot = ROOT_PATH. trim($savePath, "/");
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
        $userId = $this->userClass->getUserAttrib('userId');
        $fid = $this->getOption('f_id');
        if(!$fid) {
            return Message::getMsgJson('0065');
        }
        $fileInfo = Article::getPostFile($fid, "f_sid,f_adduid,f_status,f_fileurl");
        if(!$fileInfo) {
            return $this->error('文件不存在');
        }
        $f_adduid = $fileInfo['f_adduid'];
        $fileurl = $fileInfo['f_fileurl'];

        $downUrl = \Func\Func::ossUrlEncode($fileurl);
        print_r("<a href='{$downUrl}' class='btn' target='_blank'>点击下载，或 右键 目标另存为</a>");
        exit;
    }


}
