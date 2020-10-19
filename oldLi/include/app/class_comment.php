<?php
/* ClassName: comment
 * Memo:News class
 * Version:1.0.0
 * EditTime:2014-04-26

 * */
class comment
{

	private $creatdate = NULL;
	private $pubdate = NULL;

	public function __construct( )
	{
		$this->creatdate = Timer::now();
		$this->pubdate = $this->creatdate;
	}
    public static $all_type = array(
        'article' => 1,
    );
    //获取分类
    public static function getTypeId($type_='mp3') {
        $all_type = self::$all_type;
        foreach($all_type as $tmp_type=> $typeid) {
            if($tmp_type == $type_) return $typeid;
        }
        return 0;
    }
	//添加评论
	public function add($vartab)
	{
        $db = mysql::getInstance();
		return DbBase::insertRows('c_comment',$vartab);
	}
	
	//编辑评论
	public function edit( $id, $vartab, $flag = 'c_id' )
	{
        $db = mysql::getInstance();
		return DbBase::updateByData('c_comment', $id, $vartab, $flag);
	}

	//根据信息id删除信息
	public function del( $id )
	{
		$db = mysql::getInstance();
		return $db->DeleteRecord('c_comment', $id ,'c_id');
	}

    //获取评论原链接
    public function getCommentLink($fid, $sid) {
        if($fid == 1) { //'促销产品';
            $url = '/action-view-'.$sid.'.html';
        } else if ($fid == 2) { //试用商品
            $url = '/free-view-'.$sid.'.html';
        } else if ($fid == 3) { //试用报告
            $url = '/report-view-'.$sid.'.html';
        } else { //促销产品
            $url = '/action-view-'.$sid.'.html';
        }
        return $url;
    }

	//获取评论分类
	public function getTypeNameById($fid) {
       if($fid == 1) {
           $typename = '促销产品';
       } else if ($fid == 2) {
           $typename = '试用商品';
       } else if ($fid == 3) {
           $typename = '试用报告';
       } else {
           $typename = '促销产品';
       }
		return $typename;
	}
    //获取评论的数据标题
    public  function getCommentTitle($fid, $sid) {
        $db = mysql::getInstance();
        if($fid == 1) {//促销产品
            $sql = "SELECT a_name as title FROM a_action WHERE a_id = '".$sid."'";
        } else if ($fid == 2) {
            $sql = "SELECT a_goodsname as title FROM a_saction WHERE a_id = '".$sid."'";
        } else if ($fid == 3) {
            $sql = "SELECT r_guocheng as title FROM a_report WHERE r_id = '".$sid."'";
        } else { //促销产品
            $sql = "SELECT a_name as title FROM a_action WHERE a_id = '".$sid."'";
        }
        $db->Query( $sql );
        $title = $db->getCurRecode(\PDO::FETCH_ASSOC);
        return $title['title'];
    }
    //评论数+1
    public function updateCount($fid, $sid) {
        $db = mysql::getInstance();
        if($fid == 1) {//促销产品
            $sql = "update a_action set a_reply = a_reply+1 WHERE a_id = '".$sid."'";
        } else if ($fid == 2) { //免费活动
            $sql = "update a_saction set a_reply = a_reply+1 WHERE a_id = '".$sid."'";
        } else if ($fid == 3) {//试用报告
            $sql = "update a_report set r_num = r_num+1 WHERE r_id = '".$sid."'";
        } else { //促销产品
            $sql = "update a_action set a_reply = a_reply+1 WHERE a_id = '".$sid."'";
        }
        $db->Query( $sql );
        return true;
    }
}