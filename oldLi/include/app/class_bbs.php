<?php
/**
 * 论坛基类
 * 论坛帖子/回复 的  添加 删除 修改 查询 等等
 * @author lr
 * @version 1.0.0
 */

class bbs
{

	private $t_id = NULL;//任务id
	private $t_title = NULL;//任务的标题
	private $t_type;//任务类型

	
	public function __construct( )
	{
		//获取最近操作的时间
		$this->a_ctime = Func::ntime();
		$this->pubdate = $this->a_ctime;
	}
	
	//添加帖子
	public function addPost( $vartab )
	{
        $db = mysql::getInstance();
		return DbBase::insertRows('c_bbspost',$vartab);
	}

    //添加回复
    public function addReply( $vartab )
    {
        $db = mysql::getInstance();
        return DbBase::insertRows('c_bbsreply',$vartab);
    }
    //添加版块
    public function addBk( $vartab )
    {
        $db = mysql::getInstance();
        return DbBase::insertRows('c_bbsbk',$vartab);
    }
    //添加版块帖子分类
    public function addBkClass( $vartab )
    {
        $db = mysql::getInstance();
        return DbBase::insertRows('c_bbsbkclass',$vartab);
    }

    //修改帖子
	public function editPost( $id, $vartab , $flag = 'p_id' , $dbname = 'c_bbspost')
	{
        $db = mysql::getInstance();
		return DbBase::updateByData( $dbname , $id , $vartab , $flag );
	}

    //修改回复
    public function editReply( $id, $vartab , $flag = 'r_id' , $dbname = 'c_bbsreply')
    {
        $db = mysql::getInstance();
        return DbBase::updateByData( $dbname , $id , $vartab , $flag );
    }
    //修改版块信息
    public function editBk( $id, $vartab , $flag = 'b_id' , $dbname = 'c_bbsbk')
    {
        $db = mysql::getInstance();
        return DbBase::updateByData( $dbname , $id , $vartab , $flag );
    }
    //修改版块帖子分类
    public function editBkClass( $id, $vartab , $flag = 'c_id' , $dbname = 'c_bbsbkclass')
    {
        $db = mysql::getInstance();
        return DbBase::updateByData( $dbname , $id , $vartab , $flag );
    }

	//删除帖子
	public function delPost( $id,  $flag = 'p_id' , $and='1=1' , $dbname = 'c_bbspost' )
	{
	   $db = mysql::getInstance();
		return $db->Delete( $dbname, $id , $flag , $and );
	}
    //删除帖子分类
    public function delBkClass( $id,  $flag = 'c_id' , $and='1=1' , $dbname = 'c_bbsbkclass' )
    {
        $db = mysql::getInstance();
        return $db->Delete( $dbname, $id , $flag , $and );
    }

    //删除回复
    public function delReply( $id,  $flag = 'r_id' , $and , $dbname = 'c_bbsreply' )
    {
        $db = mysql::getInstance();
        return $db->Delete( $dbname, $id , $flag , $and );
    }

	//根据tid获取帖子信息(竖线分割)
    public function getPostById( $ids , $fildes = '*' , $and_ = '1=1' )
    {
        $db = mysql::getInstance();
        $ids = mysql::quo($ids);
        $sql = "SELECT {$fildes} FROM v_bbspost WHERE p_id in ($ids) AND $and_ ORDER BY p_id DESC";
        $db->Query($sql);
        return $db->getAllRecodes( \PDO::FETCH_ASSOC );
    }
    
    //根据会员uid获取帖子
	public function getPostByUid( $uid , $fildes = '*' , $and_ = '1=1' )
	{
        $db = mysql::getInstance();
		$sql = "SELECT {$fildes} FROM v_bbspost WHERE p_uid ='{$uid}' AND $and_ ORDER BY p_id DESC";
		$db->Query($sql);
		return $db->getAllRecodes( \PDO::FETCH_ASSOC );
	}
    
     //根据回复ID获取回复信息(竖线分割)
	public function getReplyByRid( $ids , $fildes = '*' , $and_ = '1=1' )
	{
        $db = mysql::getInstance();
        $ids = mysql::quo($ids);
		$sql = "SELECT {$fildes} FROM v_bbsreply WHERE r_id in ({$ids}) AND $and_ ORDER BY r_id DESC";
		$db->Query($sql);
		return $db->getAllRecodes( \PDO::FETCH_ASSOC );
	}
    //根据版块ID获取版块信息
    public function getBkByBid($bids)
    {
        $db = mysql::getInstance();
        $bids = mysql::quo($bids);
        $sql = "SELECT b_id, b_title, b_adminunick, b_posts, b_replys, b_visitunick, b_postunick, b_replyunick, b_guize  FROM c_bbsbk WHERE b_id in ({$bids})  ORDER BY b_id DESC";
        $db->Query($sql);
        return $db->getAllRecodes( \PDO::FETCH_ASSOC );
    }
    //根据ID获取头衔
    public function getNkByBid($bids)
    {
        $db = mysql::getInstance();
        $bids = mysql::quo($bids);
        $sql = "SELECT  n_id,n_uid,n_bbsnick,n_nickcss,n_addtime,n_desc  FROM c_bbsnick WHERE n_id in ({$bids})  ORDER BY n_id DESC";
        $db->Query($sql);
        return $db->getAllRecodes( \PDO::FETCH_ASSOC );
    }
    //根据UID获取头衔
    public function getNkByUid($uid)
    {
        $db = mysql::getInstance();
        $sql = "SELECT n_id,n_uid,n_bbsnick,n_nickcss,n_addtime,n_desc  FROM c_bbsnick WHERE n_uid = '". $uid ."' ORDER BY n_id DESC";
        $db->Query($sql);
        return $db->getCurRecode( \PDO::FETCH_ASSOC );
    }
	//获取所有的论坛版块
    public function getAllBk()
    {
        $db = mysql::getInstance();
        $sql = "SELECT b_id, b_title, b_adminunick, b_posts, b_replys, b_visitunick, b_postunick, b_replyunick, b_guize  FROM c_bbsbk ORDER BY b_id DESC";
        $db->Query($sql);
        return $db->getAllRecodes( \PDO::FETCH_ASSOC );
    }
    //获取论坛版块所有的帖子分类
    public function getBkAllClassByBid($bid)
    {
        $db = mysql::getInstance();
        $sql = "SELECT * FROM c_bbsbkclass where c_bkid = '{$bid}' ORDER BY c_id DESC";
        $db->Query($sql);
        return $db->getAllRecodes( \PDO::FETCH_ASSOC );
    }

    //获取论坛版块单个的帖子分类
    public function getBkClassByCid($cid)
    {
        $db = mysql::getInstance();
        $sql = "SELECT * FROM v_bbsbkclass where c_id = '{$cid}' ORDER BY c_id DESC";
        $db->Query($sql);
        return $db->getAllRecodes( \PDO::FETCH_ASSOC );
    }
    //获取单个论坛版块分类信息
    public function getBkClass($cid, $fildes = '*' )
    {
        $db = mysql::getInstance();
        $sql = "SELECT ". $fildes ." FROM c_bbsbkclass where c_id = '{$cid}' limit 1";
        $db->Query($sql);
        return $db->getCurRecode( \PDO::FETCH_ASSOC );
    }

    //检测会员是否有权限
    public function CheckBanzhu($unick='', $bkpowerstr='')
    {
        if(!$unick) return false;
        if(!$bkpowerstr) {
            return true;
        }
        $bkpowerstr = trim($bkpowerstr);
        $bkpowerstr = trim($bkpowerstr, ',');
        $bkpowerstr = ','. $bkpowerstr . ',';
        if(strstr($bkpowerstr, ','. $unick .',') ) {
            return true;
        } else {
            return false;
        }
    }
    //根据uid获取会员帖子统计、最后发帖时间等信息
    public function getCountByUid( $uids , $fildes = '*')
    {
        $db = mysql::getInstance();
        $uids = mysql::quo($uids);
        $sql = "SELECT {$fildes} FROM c_bbsusercount WHERE c_uid in ({$uids})";
        $db->Query($sql);
        return $db->getAllRecodes( \PDO::FETCH_ASSOC );
    }
    //添加一条会员统计信息
    public function addUserCount( $vartab )
    {
        $db = mysql::getInstance();
        return DbBase::insertRows('c_bbsusercount',$vartab);
    }
    //生成首页的单个版块html
    public function indexItem($v)
    {
        return "<a href=\"/bbs-forum-". $v['b_id'] ."-1.html\" class=\"ico ico".$v['b_id']."\"></a>
                <div class=\"forum\">
                    <div class=\"title\">
                    <a href=\"/bbs-forum-". $v['b_id'] ."-1.html\">". $v['b_title'] ."</a>
                    <span class='count'>主题: ". $v['b_posts'] .", 帖数: ". $v['b_replys'] ."</span></div>
                    <div class=\"desc\">". Func::tohtml($v['b_guize']) ."</div>
                </div>";
    }
    //重新统计版块帖子个数
    public  function reCountPost($bkid) {
        //统计总贴
        $sql = "select count(*) as total FROM c_bbspost where p_bkid='". $bkid ."'";
        $db->Query($sql); //执行sql语句
        $total =  $db->getCurRecode(\PDO::FETCH_ASSOC);
        $total = $total['total'];
        //统计回复数量
        $sql = "select count(*) as total2 FROM v_bbsreply where p_bkid='". $bkid ."'";
        $db->Query($sql); //执行sql语句
        $total2 =  $db->getCurRecode(\PDO::FETCH_ASSOC);
        $total2 = $total2['total2'];
        $newDate = array(
            'b_posts' => $total,
            'b_replys' => $total2,
        );
        $this -> editBk($bkid, $newDate, 'b_id' , 'c_bbsbk' );
    }
    //获取所有的论坛头衔
    public function getAllNick()
    {
        $db = mysql::getInstance();
        $sql = "SELECT n_id,n_uid,n_bbsnick,n_nickcss,n_addtime,n_desc  FROM c_bbsnick ORDER BY n_id DESC";
        $db->Query($sql);
        return $db->getAllRecodes( \PDO::FETCH_ASSOC );
    }

    //添加版块
    public function addNk( $vartab )
    {
        $db = mysql::getInstance();
        return DbBase::insertRows('c_bbsnick',$vartab);
    }
    //修改版块信息
    public function editNk( $id, $vartab , $flag = 'n_id' , $dbname = 'c_bbsnick' )
    {
        $db = mysql::getInstance();
        return DbBase::updateByData( $dbname , $id , $vartab , $flag );
    }

    public  static  function getBKListByID($BKids,$fields='p_id,p_title,p_uid',$limit=8,$where='  ',$order_by){
        //$fildes = 'p_id, p_title, p_titlecss, p_posttime, p_bkid, p_flid, p_uid, p_hit, p_orderid, p_replys, p_content, p_last_user, p_last_time,p_jing';
        // order by p_orderid desc,p_id desc
        $ids = mysql::quo($BKids);
        if(strstr($ids, ",")) {
            $wh_ = "where p_bkid in (". $ids .")";
        } else {
            $wh_ = "where p_bkid = ". $ids ."";
        }

        $sql = "select {$fields} from c_bbspost  ". $wh_ . $where ." ".$order_by.' limit  '.$limit;
        $db->Query($sql);
        return $db->getAllRecodes( \PDO::FETCH_ASSOC );
    }
}