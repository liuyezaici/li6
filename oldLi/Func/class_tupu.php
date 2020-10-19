<?php
class tupu {
    //获取所有父分类
    public static function getTypes($sid=0)
    {
        $db = mysql::getInstance();
        return $db->getAll("s_tupu_item_types", "t_id,t_title", "t_tupu_id ={$sid}");
    }
    //获取id对应的分类
    public static function getTypeInfo($tid = "", $fields="*")
    {
        $db = mysql::getInstance();
        return DbBase::getRowBy("s_tupu_item_types", $fields, "t_id = {$tid}");
    }
    //获取id对应的分类
    public static function getTypeName($tid = "", $fields="*")
    {
        $db = mysql::getInstance();
        $typeInfo = self::getTypeInfo($tid, 't_title');
        return $typeInfo ? $typeInfo['t_title'] : '-';
    }
    //获取id对应的图谱 s_id s_title s_typeid s_uid s_addtime s_item_ids t_from_url s_desc
    public static function getTupuInfo($sid = "", $fields="*")
    {
        $db = mysql::getInstance();
        return DbBase::getRowBy("s_tupu", $fields, "s_id = {$sid}");
    }
    //获取id对应的单元文章 a_id a_item_id a_typeid a_title a_uid a_addtime a_content a_order
    public static function getTupuArticleInfo($aid = "", $fields="*")
    {
        $db = mysql::getInstance();
        return DbBase::getRowBy("s_tupu_item_articles", $fields, "a_id = {$aid}");
    }
    //添加图谱
    public static function addTupu($editData = []) {
        $db = mysql::getInstance();
        return DbBase::insertRows("s_tupu", $editData);
    }
    //修改图谱
    public static function editTupu($s_id=0, $editData = []) {
        $db = mysql::getInstance();
        if(!$s_id) return false;
        return DbBase::updateByData("s_tupu", $editData , "s_id={$s_id}");
    }
    //修改图谱的单元
    public static function editTupuItem($t_id=0, $editData = [], $debug=false) {
        $db = mysql::getInstance();
        if(!$t_id) return false;
        return DbBase::updateByData("s_tupu_item", $editData , "t_id={$t_id}", $debug);
    }
    //获取图谱的所有单元
    public static function getTupuItem($itemIds='')
    {
        $db = mysql::getInstance();
        if(!$itemIds) return [];
        return $db->getAll("s_tupu_item", "t_id,t_tupu_id,t_title,t_uid,t_addtime,t_articles,t_pos_x,t_pos_y,t_pre_itemid", "t_id IN({$itemIds})");
    }
    //获取图谱的单元的信息
    public static function getTupuItemInfo($itemId=0, $fileds='*')
    {
        $db = mysql::getInstance();
        if(!$itemId) return [];
        return DbBase::getRowBy("s_tupu_item", $fileds, "t_id ={$itemId}");
    }
    //更新图谱的单元ids
    public static function refreshTupuItems($tupuId=0) {
        $db = mysql::getInstance();
        $itemIds = $db->getAllIds('s_tupu_item', 't_id', "t_tupu_id ={$tupuId}");
        self::editTupu($tupuId, ['s_item_ids' => $itemIds]);
    }
    //添加图谱默认单元
    public static function addTupuItem($uid =0, $tupuId=0, $editData = []) {
        $db = mysql::getInstance();
        if(!$editData) {
            $editData=  [
                't_tupu_id' => $tupuId,
                't_title' => '默认单元',
                't_uid' => $uid,
                't_addtime' => Timer::now(),
            ];
        }
        $status = DbBase::insertRows("s_tupu_item", $editData);
        if(!$status) return false;
        $newItemId = DbBase::lastInsertId();;
        self::refreshTupuItems($tupuId);
        return $newItemId;
    }
}