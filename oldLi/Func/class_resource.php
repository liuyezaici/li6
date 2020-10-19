<?php
/**
 * 
 * 内部

 * @version 1.0.0
 */

class resource
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
        $picData = $db->getAll("s_resource_pic", 'p_imgurl,p_link', "p_id in(". $picIds .") ORDER by p_index DESC ");
        return $picData;
    }

    //获取资源分类
    public function getResourceType($ids='' )
    {
        $wh_ = '1';
        $db = mysql::getInstance();
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
    //根据信息id删除资源
    public function delResource( $id )
    {
        $db = mysql::getInstance();
        return DbBase::deleteBy('s_resource', $id ,'r_id');
    }
}
