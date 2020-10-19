<?php
/**
 * 
 * 内部广告类
 * @version 1.0.0
 */

class Ad
{

	public function __construct( )
	{
		
	}
    //查找资源信息
    public function getResourceById( $sourceId , $fildes = '*' , $and_ = '1=1' ) {
        $db = mysql::getInstance();
        $listResult = DbBase::getRowBy("s_resource", $fildes, "r_id='". $sourceId ."' AND $and_ ");
        return $listResult;
    }
    //获取广告文本
    public function getResourceText( $sourceId ) {
        $db = mysql::getInstance();
        $listResult = DbBase::getRowBy("s_resource", 'r_text', "r_id='". $sourceId ."'");
        if(!$listResult) return '';
        return $listResult['r_text'];
    }
    //获取广告图片
    public function getResourcePicData( $sourceId ) {
        $db = mysql::getInstance();
        $resourceData = DbBase::getRowBy("s_resource", 'r_picids', "r_id='". $sourceId ."'");
        if(!$resourceData) return array();
        $picIds = $resourceData['r_picids'];
        $picIds = trim($picIds, ",");
        if(!$picIds) return array();
        $picData = $db->getAll("s_resource_pic", 'p_imgurl,p_link,p_text', "p_id in(". $picIds .") ORDER by p_index DESC ");
        return $picData;
    }

    //获取资源分类
    public function getResourceType($ids='' )
    {
        $db = mysql::getInstance();
        $wh_ = '1';
        if($ids) {
            $ids = $db::quo($ids);
            $wh_ = "where t_id in (". $ids .")";
        }
        $typeInfo = $db->getAll("s_resource_type", "t_id,t_typename", $wh_);
        return $typeInfo;
    }
    //获取资源分类
    public function getTypename($id_='' )
    {
        $db = mysql::getInstance();
        $typeInfo = DbBase::getRowBy("s_resource_type", "t_typename", "t_id='". $id_ ."'");
        return $typeInfo['t_typename'];
    }
}
