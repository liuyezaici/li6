<?php
/*
 * addTime:2017-4-4
 * */
class tag
{

	public function __construct( )
	{

	}

    //获取tags的关键词 返回数字或 1,2,4
    public static function getTagsIds($tags='') {
        $db = mysql::getInstance();
        if(!$tags) return '';
        if(strstr($tags, ',')) {
            $tagIdArray = [];
            foreach (explode(',', $tags) as $tmpTag) {
                $tagInfo = DbBase::getRowBy('c_tags', 't_id', "t_title='{$tmpTag}'");
                if(!$tagInfo) {
                    DbBase::insertRows('c_tags', array('t_title'=> $tmpTag));
                    $tagIdArray[] = DbBase::lastInsertId();;
                } else {
                    $tagIdArray[] = $tagInfo['t_id'];
                }
            }
            return join(',', $tagIdArray);
        } else {
            $tagInfo = DbBase::getRowBy('c_tags', 't_id', "t_title='{$tags}'");
            if(!$tagInfo) {
                DbBase::insertRows('c_tags', array('t_title'=> $tags));
                return DbBase::lastInsertId();;
            } else {
                return $tagInfo['t_id'];
            }
        }
    }
    //获取tags的名字
    public static function getTagsNames($tagIds='', $pieces=',') {
        $db = mysql::getInstance();
        if(!$tagIds) return '-';
        if(strstr($tagIds, ',')) {
            $tagInfo = $db->getAll('c_tags', 't_title', "t_id IN({$tagIds})");
            $tagNameArray = [];
            foreach ($tagInfo as $tmpTag) {
                if($tmpTag['t_title']) $tagNameArray[] = $tmpTag['t_title'];
            }
            return join($pieces, $tagNameArray);
        } else {
            $tagIndexInfo = DbBase::getRowBy('c_tags', 't_title', "t_id ={$tagIds}");
            return $tagIndexInfo ? $tagIndexInfo['t_title'] : '-';
        }
    }
    //获取tags的名字
    public static function getTagsLinks($link='', $tagIds='', $pieces=',') {
        $db = mysql::getInstance();
        if(!$tagIds) return '-';
        if(strstr($tagIds, ',')) {
            $tagInfo = $db->getAll('c_tags', 't_id,t_title', "t_id IN({$tagIds})");
            $tagNameArray = [];
            foreach ($tagInfo as $tmpTag) {
                if($tmpTag['t_title']) $tagNameArray[] = "<a href=\"/{$link}/{$tmpTag['t_id']}/1\">".$tmpTag['t_title'] ."</a>";
            }
            return join($pieces, $tagNameArray);
        } else {
            $tagIndexInfo = DbBase::getRowBy('c_tags', 't_title', "t_id ={$tagIds}");
            return $tagIndexInfo ? "<a href=\"/{$link}/{$tagIds}/1\">".$tagIndexInfo['t_title'] ."</a>" : '';
        }
    }
}