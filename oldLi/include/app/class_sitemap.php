<?php
/**

 * @version 1.0.0
 */

class sitemap
{	
	public function __construct( ){}	
	
	//返回 i_place 与 i_ids的值名对
	public function getMapById( $smids = '' , $fild = '*' )
	{
		
		$wh = ( $smids == '' )?'':"WHERE sm_id in ($smids)";
		$sql = "SELECT $fild FROM a_sitemap $wh";
        $db = mysql::getInstance();
		$db->Query($sql);
		return $db->getAllRecodes(\PDO::FETCH_ASSOC);
	}
	
	//edit修改多条数据
	public function supedit($arr)
	{
		
		$sql = "UPDATE a_sitemap SET sm_ids = CASE sm_id WHEN 1 THEN '{$arr[0]}' WHEN 2 THEN '{$arr[1]}' WHEN 3 THEN '{$arr[2]}' WHEN 4 THEN '{$arr[3]}' WHEN 5 THEN '{$arr[4]}' WHEN 6 THEN '{$arr[5]}' WHEN 7 THEN '{$arr[6]}' WHEN 8 THEN '{$arr[7]}' WHEN 9 THEN '{$arr[8]}' END WHERE sm_id IN (1,2,3,4,5,6,7,8,9)";

        $db = mysql::getInstance();
		$db->Query($sql);
		return $db->getAllRecodes(\PDO::FETCH_ASSOC);
	}
	
	
	public function getArrByPlace( $place )
	{
        $db = mysql::getInstance();
		//找到所有的板块的
		$sql = "select * from a_sitemap where sm_place = '$place'";
		$db->Query($sql);
		$row = $db->getAllRecodes(\PDO::FETCH_ASSOC);
		foreach ( $row as $key => $val){
			$arr[$key] = $row[$key]['sm_ids'];
		}
		return $arr;
	}
	
}