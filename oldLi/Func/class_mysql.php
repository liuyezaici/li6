<?php
    class mysql
    {
        private $linkID = NULL;
        private $dsn = NULL;
        private $dbms = NULL;
        private $dbHost = NULL;
        private $dbPort = '3306';
        private $dbUser = NULL;
        private $dbPwd = NULL;
        private $dbName = NULL;
        private $dbWorkMode = 'run';
        private $dbPrefix = '';
        private $result = NULL;
        private $queryString = NULL;
        private $hasActiveTransaction = FALSE;
        private $pconnect = FALSE;//持久化连接 默认：关闭
        private static $instance = null;//静态变量保存全局实例
        ////声明私有构造方法为了防止外部代码使用new来创建对象。 外部声明 mysql::getInstance()
        private function __construct()
        {
            //初始化数据源
            //$this->SetSource ( $cfg_dbms, $cfg_dbhost, $cfg_dbport, $cfg_dbuser, $cfg_dbpwd, $cfg_dbname, $cfg_dbprefix, $cfg_dbworkmode );
            $this->setSource( $GLOBALS[ 'cfg_dbtype' ] , //修改了全局变量名
                $GLOBALS[ 'cfg_dbhost' ] , $GLOBALS[ 'cfg_dbport' ] , $GLOBALS[ 'cfg_dbuser' ] , $GLOBALS[ 'cfg_dbpwd' ] , $GLOBALS[ 'cfg_dbname' ] , $GLOBALS[ 'cfg_dbprefix' ] , $GLOBALS[ 'cfg_dbworkmode' ] );
            if ( !$this->linkID ){
                $this->Open();
            }
        }
        //声明一个getInstance()静态方法，给外部声明 mysql::getInstance() 但每次会检测是否存在实例才去新建。
        static public function getInstance(){
            if(!self::$instance) self::$instance = new self();
            return self::$instance;
        }
        //注销
        public function __destruct()
        {
            $this->linkID = NULL;
        }

        /****************************************连接数据库*************************************/
        //设置数据源
        public function setSource( $dbms , $host , $dbPort = '3306' , $username , $pwd , $dbname , $dbprefix = '' , $workmode )
        {
            $this->dbms        = $dbms;
            $this->dbHost      = $host;
            $this->dbPort      = $dbPort;
            $this->dbUser      = $username;
            $this->dbPwd       = $pwd;
            $this->dbName      = $dbname;
            $this->dbPrefix    = $dbprefix;
            $this->dbWorkMode  = $workmode;
            $this->dsn         = $this->dbms . ":host=" . $this->dbHost . ";port=" . $this->dbPort . ";dbname=" . $this->dbName;
            $this->result      = NULL;
            $this->linkID      = NULL;
            $this->queryString = '';
        }


        //打开数据连接
        public function Open()
        {
            try {
                $this->linkID = new PDO ( $this->dsn , $this->dbUser , $this->dbPwd , array( \PDO::ATTR_PERSISTENT => $this->pconnect ) );
                $this->linkID->query( "SET NAMES '" . $GLOBALS[ 'cfg_dbcharset' ] . "'" );
            } catch ( PDOException $e ) {
                return $this->DisplayError( $e->getMessage() );
            }
        }

        //关闭链接
        public function Close()
        {
            $this->linkID = NULL;
        }

        /******************************记录集相关*******************************/
        //执行不返回记录集，只返回影响的记录数
        public function Execute( $sql = '' )
        {
           /* //禁止运行危险的查询
            $limitWords = array('ALTER','DROP','TRUNCATE');
            foreach($limitWords as $n => $word_) {
                if(strstr($sql, $word_) || strstr($sql, strtolower($word_))) {
                    message::Show('limit sql :'.$word_);
                    exit;
                }
            }*/
            if ( $sql != '' ){
                $this->setQuery( $sql );
            }
            return $this->linkID->exec( $this->queryString );
        }

        //查询单条数据
        public function getOne( $table_ = '' , $fields = "*" , $where_ = "1", $debug=false)
        {
            if ( !$table_ ) return '';
            $sql = "SELECT " . $fields . " FROM `" . $table_ . "` WHERE  " . $where_ ;
            if($debug) return $sql;
            $this->Query($sql); //此处不能加limit 0,1 因为如果有sum或count函数 则会统计出错
            return $this->getCurRecode( \PDO::FETCH_ASSOC );
        }
        //查询多条数据
        public function getAll( $table_ = '', $fields = "*", $where_ = "1", $debug=false)
        {
            if ( !$table_ ) return '';
            $sql = "SELECT " . $fields . " FROM `" . $table_ . "` WHERE  " . $where_ ;
            if($debug) return $sql;
            $this->Query($sql); //此处不能加limit 0,1 因为如果有sum或count函数 则会统计出错
            return $this->getAllRecodes( \PDO::FETCH_ASSOC );
        }
        //查询所有的索引合并为字符串 1,2,3,4 group_concat 有最大内容限制:1024
        public function getAllIds($table_ = '' , $field = "*" , $where_ = "1", $orderBy='') {
            //WHERE 里的order by 对 group_concat查询无效，必须转移到 group_concat里面
            $sql = "SELECT group_concat({$field} {$orderBy}) as result_ FROM `" . $table_ . "` WHERE  " . $where_ ;
            $this->Query($sql); //此处不能加limit 0,1 因为如果有sum或count函数 则会统计出错
            $result = $this->getCurRecode( \PDO::FETCH_ASSOC );
            return isset($result['result_']) ? $result['result_'] : '';
        }


        //de:start:2016.10.9
        //sql语句获取信息
        public function getQuery($sql='') {
            if ( !$sql ) return '';
            $this->Query($sql);
            return $this->getAllRecodesEx( \PDO::FETCH_ASSOC );

        }
        //de:end:2016.10.9


        //查询单条锁表数据
        public function getOneLock( $table_ = '' , $fields = "*" , $where_ = "1" )
        {
            if ( !$table_ ) return '';
            $sql = "SELECT " . $fields . " FROM `" . $table_ . "` WHERE  " . $where_ ." LOCK IN SHARE MODE";
            //$sql = "SELECT " . $fields . " FROM `" . $table_ . "` WHERE  " . $where_ ." FOR UPDATE";
            $this->Query($sql); //此处不能加limit 0,1 因为如果有sum或count函数 则会统计出错
            return $this->getCurRecode( \PDO::FETCH_ASSOC );
        }
        //求和 可能是资金 会返还小数
        public function getSum( $table_ = '' , $fields = "*" , $where_ = "1" )
        {
            if ( !$table_ ) return '';
            $fields = str_replace(',', '+', $fields);//多字段直接用+来求和
            $sql = "SELECT Sum(" . $fields . ") as total FROM `" . $table_ . "` WHERE  " . $where_ ;
            $this->Query($sql); //此处不能加limit 0,1 因为如果有sum或count函数 则会统计出错
            $result = $this->getCurRecode( \PDO::FETCH_ASSOC );
            return isset($result['total']) ? floatval($result['total']) : 0;
        }

        //返回查询记录集
        public function Query( $sql = '' )
        {
            //禁止运行危险的查询
            $limitWords = array('ALTER','DROP','TRUNCATE');
            foreach($limitWords as $n => $word_) {
                if(strstr($sql, $word_) || strstr($sql, strtolower($word_))) {
                    message::Show('limit sql :'.$word_);
                    exit;
                }
            }
            if ( $sql != '' ){
                $this->setQuery( $sql );
            }
            $this->result = $this->linkID->query( $this->queryString );
            if ( !$this->result ){
                $this->getError();
            }
            return $this;
        }

        public function fecth()
        { //单行
            return $this->getCurRecode( \PDO::FETCH_ASSOC );
        }

        public function fecthAll()
        { //多行
            return $this->getAllRecodesEx( \PDO::FETCH_ASSOC );
        }

        //返回一列一行内容
        public function getResultCol()
        {
            if ( $this->getTotalRow() == 0 ){
                return NULL;
            }
            return $this->result->fetchColumn();
        }

        //返回当前的一条记录并把游标移向下一记录
        public function getCurRecode( $mode = \PDO::FETCH_NUM )
        {
            if ( $this->getTotalRow() == 0 ){
                return array();
            }
            $this->result->setFetchMode( $mode );
            return $this->result->fetch();
        }

        //返回整个记录集
        public function getAllRecodes( $mode = \PDO::FETCH_NUM )
        {
            if ( $this->getTotalRow() == 0 ){
                return array();
            }
            $this->result->setFetchMode( $mode );
            return $this->result->fetchAll();
        }

        //返回整个记录集2
        public function getAllRecodesEx( $mode = \PDO::FETCH_NUM )
        { //\PDO::FETCH_BOTH，返回的记录集同时用两钟做索引
            //$this->result->nextRowset();//\PDO::FETCH_NUM，返回的记录集用数字作索引
            $this->result->setFetchMode( $mode ); //\PDO::FETCH_ASSOC，返回的记录集用字段名做索引
            return $this->result->fetchAll();
        }

        //记录集行数
        public function getTotalRow()
        {
            if ( is_object( $this->result ) ){
                $c = $this->linkID->query( "SELECT found_rows()" );
                return $c->fetchColumn();
            } else {
                return 0;
            }
        }

        //最后ID
        public function getLastID()
        {
            return $this->linkID->lastInsertId();
        }

        //SQL安全过滤
        public function setQuery( $sql )
        {
            $prefix    = "#@_@__";
            $sql       = trim( $sql );
            $inQuote   = FALSE;
            $escaped   = FALSE;
            $quoteChar = "";
            $n         = strlen( $sql );
            $np        = strlen( $prefix );
            $restr     = "";
            $j         = 0;
            for ( ; $j < $n ; ++$j ) {
                $c    = $sql [ $j ];
                $test = substr( $sql , $j , $np );
                if ( !$inQuote ){
                    if ( $c == "\"" || $c == "'" ){
                        $inQuote   = TRUE;
                        $escaped   = FALSE;
                        $quoteChar = $c;
                    }
                } else if ( $c == $quoteChar && !$escaped ){
                    $inQuote = FALSE;
                } else if ( $c == "\\" && !$escaped ){
                    $escaped = TRUE;
                } else {
                    $escaped = FALSE;
                }
                if ( $test == $prefix && !$inQuote ){
                    $restr .= $this->dbPrefix;
                    $j += $np - 1;
                } else {
                    $restr .= $c;
                }
            }
            $this->queryString = $restr;
        }

        //释放记录集
        public function FreeResultAll()
        {
            $this->result = NULL;
        }

        //返回记录集对象
        public function getResult()
        {
            return $this->result;
        }

        /*********************************事务************************************/
        //事务处理
        public function BeginTRAN()
        {
            if ( $this->hasActiveTransaction ){
                return FALSE;
            } else {
                $this->hasActiveTransaction = $this->linkID->beginTransaction();
            }
            return $this->hasActiveTransaction;
        }

        public function CommitTRAN()
        {
            $this->linkID->commit();
            $this->hasActiveTransaction = FALSE;
        }

        public function RollBackTRAN()
        {
            $this->linkID->rollback();
            $this->hasActiveTransaction = FALSE;
        }

        /******************************存储过程与函数*******************************/
        //执行存储过程
        public function ExecStoredProcedure( $pname , $vartab = '' , $mode = \PDO::FETCH_NUM )
        {
            if ( !is_array( $vartab ) ){
                return -1;
            }
            $var = '';
            foreach ( $vartab as $v ) {
                $var .= "'{$v}'" . ",";
            }
            $var = trim( $var , ',' );
            $sql = "CALL {$pname}({$var});";
            $this->Query( $sql );
            return $this->getAllRecodesEx( $mode );
        }

        //执行函数
        public function ExecStoredFunction( $fname , $vartab = '' )
        {
            if ( !is_array( $vartab ) ){
                return -1;
            }
            $var = '';
            foreach ( $vartab as $v ) {
                $var .= "'{$v}'" . ",";
            }
            $var = trim( $var , ',' );
            $sql = "SELECT {$fname}({$var})";
            $this->Query( $sql );
            return $this->getResultCol();
        }

        /************************操作数据库（插入、删除、修改）*******************/

        //删除指定数据 不轻易执行！
        public function delete( $table , $where_ = '', $debug = 0 )
        {
            if(!$table || !$where_) return;
            $sql = "DELETE FROM {$table} WHERE ".$where_ ;
            if ( $debug == 1 ) return $sql;
            return $this->Execute($sql);
        }
        //插入
        public function InsertRecord( $table , $vartab, $debug=0)
        {
            if ( !is_array( $vartab ) ){
                return -1;
            }
            $field = "";
            $var   = "";
            foreach ( $vartab as $key => $value ) {
                $field .= $key . ",";
                $var .= "'" . $value . "',";
            }
            $field = trim( $field , ',' );
            $var   = trim( $var , ',' );
            $sql   = "INSERT INTO " . $table . " (" . $field . ") VALUES (" . $var . ")";
            if($debug == 1) return $sql;
            return $this->Execute( $sql );
        }

        //修改
        // $field_add 如果不为空，那么此字段为 递加字段
        // 例 count = count+1
        public function UpdateRecord( $table , $ids , $vartab , $flag = 'id' , $where_ = "" , $field_add = '' , $debug = 0 )
        {
            if ( $ids == '' || !is_array( $vartab ) ){
                return -1;
            }
            if ( strstr( $ids , "," ) ){
                $wh2 = "$flag in (" . self::quo( $ids ) . ")";
            } else {
                if(!is_int($ids)) {
                    $ids = self::quo( $ids );
                }
                $wh2 = "$flag = " . $ids . "";
            }
            if ( $where_ ){
                $wh2 .= " AND " . $where_;
            }
            return $this->update($table, $vartab, $wh2, $debug);
        }
        //带条件的数据修改
        public function update($table ,$vartab , $where_ = "",$debug = 0 )
        {
            $updateStrArray = [];
            foreach ( $vartab as $key => $value ) {
                $updateStrArray[] = "{$key}='{$value}'";
            }
            $updateStr = join(',', $updateStrArray);
            if(!$where_) return -1;//必须带条件 防止意外的全局修改
            $where_  = " WHERE " . $where_;
            $sql = "UPDATE {$table} SET " . $updateStr . $where_;
            if ( $debug == 1 ) return $sql;
            return $this->Execute($sql);
        }

        /********************************数据库相关*****************************************/
        //数据库中是否存在表
        public function isTable( $tbname )
        {
            $this->Query( "SHOW TABLES" );
            $row = $this->getAllRecodes( \PDO::FETCH_NUM );
            foreach ( $row as $v ) {
                if ( $v[ 0 ] == $tbname ){
                    return TRUE;
                }
            }
            return FALSE;
        }

        //数据库版本
        public function getMySqlVersion()
        {
            return $this->ExecStoredFunction( "VERSION" );
        }

        //表字段
        public function getTableFields( $tbname )
        {
            $this->Query( "DESCRIBE {$tbname}" );
            return $this->getAllRecodes();
        }

        /**********************************错误处理***************************************/
        //显示错误信息
        public function DisplayError( $msg )
        {
            $msg = $msg . "<br/> - Execute Query False! <br/><font style='color:red;size:10px'>" . $this->queryString . "</font>";
            echo "<html>\r\n";
            echo "<head>\r\n";
            echo "<meta http-equiv='Content-Type' content='text/html;charset=UTF-8'>\r\n";//  utf-8 or gbk
            echo "<title>Error Track</title>\r\n";
            echo "</head>\r\n";
            echo "<body>\r\n<p style='line-helght:150%;font-size:10pt'>\r\n";
            echo $msg;
            echo "<br/><br/>";
            echo "</p>\r\n</body>\r\n";
            echo "</html>";
            exit ();
        }

        //获得错误信息
        public function getError()
        {
            //'00000'表没有错误
            if ( $this->dbWorkMode == "debug" && $this->linkID->errorCode() != "00000" ){
                $error = $this->linkID->errorInfo();
                return $this->DisplayError( "[code:{$error[0]}]-{$error[2]}" );
            }
        }

        //统计查询的数量
        public function count( $tab , $where_ )
        {
            $sql = "select count(*) as num from " . $tab . " where " . $where_;
            //echo $sql;exit;
            $this->Query($sql);
            $a = $this->getCurRecode();
            if ( isset( $a[ 0 ] ) ){
                $a = $a[ 0 ];
            } else {
                $a = 0;
            }
            return $a;
        }

        //唯一索引判断数量
        public function ifExist($tab , $where_ , $field = "1", $debug=false)
        {
            $sql = "select {$field} from {$tab} where {$where_} ";
            if(!strstr(strtolower($where_), 'limit')) $sql .= " limit 1";
            if($debug) return $sql;
            $this->Query($sql);
            $data = $this->getCurRecode( \PDO::FETCH_ASSOC );
            if ( count( $data ) == 0 ){
                return false;
            }
            return true;
        }
        //判断数据是否存在
        public function ifExistSql($sql_)
        {
            $this->Query($sql_);
            $data = $this->getCurRecode( \PDO::FETCH_ASSOC );
            if ( count( $data ) == 0 ){
                return 0;
            }
            return 1;
        }

        //统计查询的数量 $fields 可以用多字段 ，例 money_a+money_b
        public function sumResult( $tab , $fields , $where_ )
        {
            $this->Query( "select sum($fields) as num from " . $tab . " where " . $where_ );
            $a = $this->getCurRecode();
            if ( isset( $a[ 0 ] ) ){
                $a = $a[ 0 ];
            } else {
                $a = 0;
            }
            return $a;
        }

        /*
         * 请尽量使用手写的统计条数sql语句 例：$count = $db->getRows('select count(1) from {table} {where}',1);
         *
         * 如果需要匹配的sql查询统计条数,注意sql的语法。
         * $sql 为 sql语句
         * $t  默认为false,是要把sql截取成两段，根据 from
         * 如果 $t==true,那么$sql语句最好了select count(1) from {table} {where}
         *
         * 返回  string(包括0，尽量用 === )
         */
        public function getRows( $sql , $t = FALSE )
        {
            if ( !$t ){
                //$sql = preg_split('/from/i','from',$sql); /* 替换，把大小写的替换成小写 */
                //$sql = preg_split('/order by/i','order by',$sql); /* 替换，把大小写的替换成小写 */
                $from_array = preg_split( "/[\s]from[\s]/mi" , $sql , 2 ); // 分割第一个from
                $count_sql  = "SELECT COUNT(1)" . " from " . $from_array[ 1 ]; //取第一个from右边的所有语句
                $order_by   = preg_split( "/[\s]order[\s]/mi" , $count_sql , 2 ); //取order by左边的所有数据
                $count_sql  = $order_by[ 0 ];
                /*查询条数*/
                $this->Query( $count_sql );
            } else {
                /*直接查询sql语句，此时的sql需要直接写 select count(1) from {table} {where} */
                $this->Query( $sql );
            }
            //SELECT found_rows();
            $r = $this->getCurRecode();

            return isset($r[0]) ? ($r[0]) : 0;
        }

        //统计数据的点击是否重复
        public static function ifHit($sessionName = 'action', $sid=1) {
            @session_start();
            $oldSession = isset($_SESSION[$sessionName]) ? trim($_SESSION[$sessionName]) : '';
            $oldSession = trim($oldSession,",");
            $exist = false;
            if($oldSession && strlen($oldSession) > 0) {
                if(strstr(",".$oldSession.",", ",". $sid .",")) {
                    $exist = true;
                }
            }
            if(!$exist) {
                $newSession = $oldSession.",". $sid;
                $newSession = trim($newSession,",");
                $_SESSION[$sessionName] = $newSession;
            }
            return $exist;
        }


        //将id用逗号分隔开，用于mysql in 查询语句
        public static function quo($ids) {
            $ids = str_replace('|',',',$ids);
            $ids = str_replace("'","",$ids); //先去掉单引号,防止多加引号
            $ids = trim($ids,",");
            $ids = trim($ids,",");
            $ids = str_replace(",","','",$ids);
            $ids = trim($ids,"'");
            $ids = trim($ids,"'");
            $ids = "'". $ids ."'";
            return $ids;
        }

        //ids计算分页
        public static function getPageIds($businessIDs, $pagesize= 10, $page=1) {
            $idsArray = explode(",", $businessIDs);
            $idsArray = array_reverse($idsArray);
            $total = count($idsArray);
            $fromId = ($page-1) * $pagesize;
            $endId = ($page) * $pagesize;
            if($endId > $total) $endId = $total;
            $index = 0;
            $newIdArray = array();
            for($i = $fromId; $i < $endId; $i ++) {
                $newIdArray[$index] = $idsArray[$i];
                $index ++;
            }
            $newIDs = join(",", $newIdArray);
            $newIDs = trim($newIDs, ",");
            return $newIDs;
        }

        //写入报错日志 便于跟踪
        public static function addErrLog($mytime=NULL, $memo='') {
            $db = mysql::getInstance();
            if(is_null($mytime)) $mytime = Timer::now();
            DbBase::insertRows('s_err_log', array('l_addtime'=> $mytime, 'l_memo'=> $memo ));
        }
    }