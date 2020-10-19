<?php
/* ClassName: sign
 * Memo:sign class
 * 签到类，提供签到的方法
 * */
class sign
{

	private $id = NULL;//记录id
	private $typeid = NULL;//用户id
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
	public function add($vartab){
		$db = mysql::getInstance();
		return DbBase::insertRows('a_sign',$vartab);
	}

	//根据信息类型选择信息（可指定返回数量和标题长度）
	public function getSignInfoByUserId( $uid, $fildes = '*', $len = 1) {
        $db = mysql::getInstance();
		$sql = "SELECT {$fildes} FROM a_sign WHERE sg_uid = '". $uid ."' ORDER BY sg_id DESC LIMIT 0,". $len ."";
		$db->Query( $sql );
		$row = $db->getAllRecodes( \PDO::FETCH_ASSOC );
		return $row;
	}

    //获取我的最新10条签到记录
    public function getUserSignList( $uid, $page, $pagesize = 10, $fildes = '*')
    {
        $model = '';//网址参数
        $sql = "SELECT {$fildes} FROM a_sign WHERE sg_uid = '{$uid}' ORDER BY sg_ctime DESC LIMIT 0,".$pagesize;
        $ac = new Divpage( $sql, $model, $fildes , $page, $pagesize, $menustyle = 7 );
        $ac->getDivPage();
        return $ac->getPage();
    }

    //获取最后的签到记录
    public function getLastSign($uid, $fildes = '*')
    {$db = mysql::getInstance();
        $sql = "SELECT {$fildes} FROM a_sign WHERE sg_uid = '{$uid}' ORDER BY sg_id DESC LIMIT 0,1";
		$db->Query( $sql );
		$row = $db->getCurRecode( \PDO::FETCH_ASSOC );
		return $row;
    }

}