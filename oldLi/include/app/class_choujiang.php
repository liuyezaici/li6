<?php
/* ClassName: sign
 * 抽奖类
 * */
class choujiang
{

	private $id = NULL;//记录id
	private $typeid = NULL;//用户id

	public function __construct( )
	{
	}

	//添加抽奖活动
	public function addAction($vartab){
		$db = mysql::getInstance();
		return DbBase::insertRows('c_choujiangaction',$vartab);
	}

	//根据活动信息
	public function getAction( $aid, $fildes = '*') {
        $db = mysql::getInstance();
		$sql = "SELECT {$fildes} FROM c_choujiangaction WHERE a_id = '". $aid ."' ";
		$db->Query( $sql );
		$row = $db->getCurRecode( \PDO::FETCH_ASSOC );
		return $row;
	}

    //修改活动信息
    public function editAction($id, $vartab )
    {
        $db = mysql::getInstance();
        return DbBase::updateByData('c_choujiangaction' , $id , $vartab , 'a_id' );
    }
    //插入参加记录
    public function joinAction($vartab )
    {
        $db = mysql::getInstance();
        return DbBase::insertRows('c_choujiangusers',$vartab);
    }
    //修改参加记录状态
    public function editJoin($id, $vartab )
    {
        $db = mysql::getInstance();
        return DbBase::updateByData( 'c_choujiangusers' , $id , $vartab , 'j_id' );
    }

}