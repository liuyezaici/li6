<?php
/*
 * EditTime:2017-04-02
use Func\DbBase;
 * */
namespace App\Model;

use Func\DbBase;
use Func\Timer;

class Article {

	public function __construct( )
	{
	}
    //获取所有分类
    public static function getAllTypes() {
        return DbBase::getRowsBy('s_articles_types', "t_id,t_title", "1 order by t_id desc");
    }

    //通过rootid获取所有该类型的 类型名称
    public static function getTypeName($t_id)
    {
        $typeInfo = DbBase::getRowBy('s_articles_types', "t_title", " t_id = '{$t_id}'" );
        $typeInfo['t_title'] = !empty($typeInfo['t_title']) ? $typeInfo['t_title'] : '';
        return $typeInfo['t_title'];
    }
    
    public static $articleDotypeAdd = 0;//文章动作 添加
    public static $articleDotypeBegin = 1;//文章动作 启动
    public static $articleDotypeEditInfo = 2;//文章动作 修改信息
    public static $articleDotypePause = -1;//文章动作 暂停
    public static $articleDotypeDelete = -2;//文章动作 删除

    public static $articleDotypeUploadFujian = 7;//文章动作 上传附件
    public static $articleDotypeDeleteFujian = -8;//文章动作 删除附件

    //更新分享包含的附件
    public static function refreshArticleFujians($articleid=0)
    { 
        $fileDatas = DbBase::getRowsBy('s_article_fujian', 'f_id', 'f_sid='. $articleid .' AND f_status=0');
        $fileNum = 0;
        $fileIdArray = [];
        foreach ($fileDatas as $v) {
            $fileNum ++;
            $fileIdArray[] = $v['f_id'];
        }
        $editData = [
            'a_fileids' => join(',', $fileIdArray)
        ];
        return DbBase::updateByData('s_articles', $editData, 'a_id='. $articleid);
    }

    //上传分享的附件
    public static function addArticleFujian($aid=0, $uid=0, $fileNameRight='', $fileUrl='', $filesize=0, $geshi=0, $mytime=NULL) {
        
        $mytime = is_null($mytime)? Timer::now():$mytime;
        //获取最大的排序 递增1
        $lastInfo = DbBase::getRowBy('s_article_fujian', 'f_order', "f_sid=". $aid ." AND f_status=0 ORDER BY f_order DESC");
        if($lastInfo) {
            $lastOrder = $lastInfo['f_order'] + 1;
        } else {
            $lastOrder = 1;
        }

        $newInsertData = array(
            'f_sid' => $aid,
            'f_adduid' => $uid,
            'f_addtime' => $mytime,
            'f_filename' => $fileNameRight,
            'f_fileurl' => $fileUrl,
            'f_filesize' => $filesize,
            'f_geshi' => $geshi,
            'f_order' => $lastOrder,
        );
        //file_put_contents('text_size.txt', $filesize);
        DbBase::insertRows('s_article_fujian', $newInsertData);
        self::refreshArticleFujians($aid); //更新文件索引
    }
    //更新文章和tag的分类索引
    public static function refreshArticleTagsIndex($sid=0, $tagIds='', $type_id1=0) {
        
        if(!$sid || !$type_id1 ) return ;
        $type_id1 = intval($type_id1);
        $indexArray = [];
        if($tagIds) {
            foreach (explode(',', $tagIds) as $tmpTagId) {
                $indexInfo = DbBase::getRowBy('s_article_tags_index', 'i_id', "i_sid={$sid} AND i_tagid={$tmpTagId} AND i_typeid1={$type_id1}");
                if($indexInfo) {
                    $indexArray[] = $indexInfo['i_id'];
                } else {
                    DbBase::insertRows('s_article_tags_index', array('i_sid'=> $sid, 'i_tagid'=> $tmpTagId, 'i_typeid1'=> $type_id1));
                    $indexArray[] = DbBase::lastInsertId();
                }
            }
        }
        $indexIds = join(',', $indexArray);
        DbBase::updateByData('s_articles', $sid, array('s_tags'=> $indexIds), 's_id');
        DbBase::deleteBy('s_article_tags_index', 'i_sid='. $sid .' AND i_id NOT IN('. $indexIds .')');
    }
    //获取文章信息
    public static function getArticle($a_id=0, $fields='*') {
        
        return DbBase::getRowBy('s_articles', $fields, 'a_id='. $a_id);
    }
    //获取附件信息
    public static function getPostFile($fid=0, $fields='*') {
        
        return DbBase::getRowBy('s_article_fujian', $fields, "f_id={$fid}");
    }
    //获取靠前的附件信息
    public static function getPostFileLeft($postId='', $orderId=0, $fields='*') {
        
        return DbBase::getRowBy('s_article_fujian', $fields, "f_sid={$postId} AND f_order <{$orderId} AND f_status =0 ORDER BY f_order DESC LIMIT 1");
    }
    //获取靠后的附件信息
    public static function getPostFileRight($postId='', $orderId=0, $fields='*') {
        
        return DbBase::getRowBy('s_article_fujian', $fields, "f_sid={$postId} AND f_order >{$orderId} AND f_status =0 ORDER BY f_order ASC LIMIT 1");
    }
    //修改文章信息
    public static function updateArticle($id=0, $editData=[]) {
        return DbBase::updateByData('s_articles', $editData, "a_id={$id}");
    }
    //修改附件信息
    public static function updateFile($fid=0, $editData=[]) {

        if(!$fid) return false;
        return DbBase::updateByData('s_article_fujian', $editData, "f_id={$fid}");
    }
}