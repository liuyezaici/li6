<?php
/* ClassName: News
 * Memo:News class
 * Version:1.0.0
 * EditTime:2012-05-02

 * */
class help
{

	private $id = NULL;
	private $typeid = NULL;
	private $title = NULL;
	private $body = NULL;
	private $source = "";
	private $writer = NULL;
	private $creatdate = NULL;
	private $countnum = 0;
	private $istop = 0;
	private $keyword = NULL;
	private $pubdate = NULL;

	public function __construct( )
	{
		$this->creatdate = Func::ntime();
		$this->pubdate = $this->creatdate;
	}

	//添加信息
	public function addHelp( $vartab )
	{
		$db = mysql::getInstance();
		return DbBase::insertRows('c_help',$vartab);
	}
	
	//编辑信息
	public function edit( $id, $vartab, $flag = 'n_id' )
	{
        $db = mysql::getInstance();
		return DbBase::updateByData('c_help', $id, $vartab, $flag);
	}

	//根据信息id删除信息
	public function delHelp( $id )
	{
        $db = mysql::getInstance();
		return $db->Delete('c_help', $id ,'n_id');
	}

	//获取id对应的文章
	public function getHelpById( $id = "")
	{
        $db = mysql::getInstance();
		$sql = "SELECT * FROM c_help WHERE n_id = '{$id}'";
		$db->Query( $sql );
		return $db->getCurRecode( \PDO::FETCH_ASSOC );
	}
	
	//得到新闻类型的名称
	//增加了$an参数，参数 为条件，例如root<>0，用于筛选root的数据。
	
	public function getTypeNameByTypeId($id, $an='')
	{
        $db = mysql::getInstance();
		if ($id =='')
		{
			$an = ($an == '')?'':" where $an ";
			$sql = 'SELECT t_id,t_typename FROM c_helptype '.$an;
		}
		else
		{
			$an = ($an == '')?$an:" and $an";
			$sql = "SELECT t_id,t_typename FROM c_helptype WHERE t_id = '{$id}'".$an;
		}
		$db->Query($sql);
		return $db->getAllRecodes( \PDO::FETCH_ASSOC );
	}

	//获取所有分类
	public function getAllTypeNames()
	{
        $db = mysql::getInstance();
		$sql = "SELECT t_id,t_typename FROM c_helptype limit 100";
		$db->Query($sql);
		return $db->getAllRecodes();
	}
	
	//通过rootid获取所有该类型的 类型名称
	public function getTypeNameById($id)
	{
        $db = mysql::getInstance();
		$sql = "SELECT t_id,t_typename FROM c_helptype WHERE t_id = '{$id}'";
		$db->Query($sql);
		return $db->getAllRecodes();
	}

    //通过一串类型id，返回一串类型名称
    public function getTypeNamesByIds($ids)
    {
        $ids = mysql::quo($ids);
        $db = mysql::getInstance();
        $ids = str_replace("'","",$ids);
        $sql = "SELECT t_id,t_typename FROM c_helptype WHERE t_id in ({$ids}) ";
        $db->Query($sql);
        return $db->getAllRecodes( \PDO::FETCH_ASSOC );
    }
    //添加分类
    public function addClass($vartab)
    {
        $db = mysql::getInstance();
        return DbBase::insertRows('c_helptype',$vartab);
    }
    //修改分类信息
    public function editClass($id, $vartab , $flag = 't_id' , $dbname = 'c_helptype')
    {
        $db = mysql::getInstance();
        return DbBase::updateByData( $dbname , $id , $vartab , $flag );
    }

}