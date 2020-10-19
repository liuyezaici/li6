<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/5
 * Time: 16:11
 */

NameSpace Func;

class DbBase
{
    //主连接
    public static $master_ins;
    //从连接
    public static $slave_ins;
    //连接信息
    public static $setting;
    public static $lastSql;

    /**
     * 按要求连接数据
     * @author LR
     * @Date:date
     * @param bool $mod
     */
    public static function init($mod=false)
    {
        try {
            //读取配置文件
            self::$setting = \Config::get('db.base');
//            print_r('setting:');
//            print_r(self::$setting);
            if(!self::$setting) {
                print_r("找不到setting\n");
                exit;
            }
            if ($mod) {
                if($mod == 'master')
                    self::$master_ins = self::connection(self::$setting['master']);
                else
                    self::$slave_ins = self::connection(self::$setting['slave']);
            } else {
                //默认情况下主从都连
                self::$master_ins = self::connection(self::$setting['master']);
                self::$slave_ins = self::connection(self::$setting['slave']);
            }
        }catch (\PDOException $e){
            echo "Mysql Connection Error!";
            return;
        }
    }

    /**
     * 连接数据库
     * @author LR
     * @Date:date
     * @param $setting
     * @return PDO
     */
    public static function connection($setting)
    {
        if(!isset($setting['dsn'])) {
            print_r("db.base.setting找不到dsn[\n");
            print_r($setting);
            print_r("]\n");
            exit;
        }
        $pdo = new \PDO($setting['dsn'],$setting['user'],$setting['password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE,  \PDO::ERRMODE_EXCEPTION); #开启异常模式
        $pdo->query("SET NAMES 'utf8mb4'");
        return $pdo;
    }

    /**
     * 关闭连接
     * @author LR
     * @Date:date
     * @param bool $mod
     */
    public static function close($mod=false)
    {
        if($mod){
            self::$master_ins = null;
        }else{
            self::$slave_ins = null;
        }
    }

    /**
     * 仅执行
     * @param string $sql
     * @param null $params
     * @param int $cache
     * @return int
     * @throws \PDOException
     */
    public static function update($sql,$params=null)
    {
        try {
            if(is_null(self::$master_ins)){
                //未连接的连接
                self::$master_ins = self::connection(self::$setting['master']);
            }
            $stmt = self::$master_ins->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            $stmt->execute($params);
            return $stmt->rowCount();
        }catch (\PDOException $e){
            // 服务端断开时重连一次
            if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
                //手动清空连接
                self::close(true);
                //递归一次执行
                return self::update($sql,$params);
            }else{
                throw new \PDOException($e->getMessage());
            }
        }
    }

    /**
     * 单纯的修改数据
     */
    public static function updateByData($table, $editData=[], $whereSql='')
    {
        $params = [];
        $sqlArray = [];
        foreach ($editData as $key=>$v) {
            $params[] = $v;
            $sqlArray[] = "`{$key}`=?";
        }
        return self::update("UPDATE `{$table}` SET ". join(',', $sqlArray). " WHERE {$whereSql}", $params);
    }

    /**
     * 单纯的修改数据
     */
    public static function deleteBy($table, $whereSql='')
    {
        return self::update("DELETE FROM `{$table}` WHERE {$whereSql}");
    }

    /**
     * 查询多行数据
     * @param string $sql
     * @param null $params
     * @param int $cache
     * @return array
     * @throws \Exception
     */
    public static function getRows($sql, $params=null)
    {
        try {
            //特殊查询，比如按日期条件查的，可记到缓存
            if(is_null(self::$slave_ins)){
                //未连接的连接
                self::$slave_ins = self::connection(self::$setting['slave']);
            }
            $stmt = self::$slave_ins->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            $stmt->execute($params);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $return = $stmt->fetchAll();
            $stmt = null;

            return $return;
        }catch (\PDOException $e){
            // 服务端断开时重连一次
            if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
                //手动清空连接
                self::close(false);
                //递归一次执行
                return self::getRows($sql,$params);
            }else{
                throw new \PDOException($e->getMessage());
            }
        }
    }

    /**
     * 查询单行数据
     * @param string $sql
     * @param null $params
     * @param int $cache
     * @return array|mixed
     * @throws \Exception
     */
    public static function getRow($sql,$params=null)
    {
        try {
            //特殊查询，比如按日期条件查的，可记到缓存
            if(is_null(self::$slave_ins)){
                //未连接的连接
                self::$slave_ins = self::connection(self::$setting['slave']);
            }
            $stmt = self::$slave_ins->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            if(is_string($params)) {
                print_r($params);exit;
            }
            $stmt->execute($params);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $return = $stmt->fetch();
            $stmt = null;
            return $return;
        }catch (\PDOException $e){
            // 服务端断开时重连一次
            if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
                //手动清空连接
                self::close(false);
                //递归一次执行
                return self::getRow($sql,$params);
            }else{
                throw new \PDOException($e->getMessage());
            }
        }
    }

    /**
     * 查询单行单个列
     * @param string $sql
     * @param null $params
     * @param int $cache
     * @return mixed
     * @throws \Exception
     */
    public static function getValue($sql,$params=null)
    {
        try {
            //特殊查询，比如按日期条件查的，可记到缓存
            if(is_null(self::$slave_ins)){
                //未连接的连接
                self::$slave_ins = self::connection(self::$setting['slave']);
            }
            $stmt = self::$slave_ins->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
            $stmt->execute($params);
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
            $return = $stmt->fetchColumn();
            $stmt = null;
            return $return;
        }catch (\PDOException $e){
            // 服务端断开时重连一次
            if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
                //手动清空连接
                self::close(false);
                //递归一次执行
                return self::getValue($sql,$params);
            }else{
                throw new \PDOException($e->getMessage());
            }
        }
    }

    /**
     * 查询多行并归类返回
     * @param string $sql
     * @param string $field
     * @param null $params
     * @param int $cache
     * @return array
     * @throws \PDOException
     */
    public static function getObject($sql, $field, $params=null, $cache=0)
    {
        if(!$field) throw new \PDOException("缺少参数$field");
        $return = self::getRows($sql,$params,$cache);
        if($return){
            $rs = [];
            foreach ($return as $var){
                if(!isset($var[$field])) throw new \PDOException('数据中没有['.$field.']单元');
                $rs[$var[$field]] = $var;
            }
            return $rs;
        }
        return [];
    }

    /**
     * 插入一行或多行数据
     * @param string $table
     * @param array $data
     * @return bool|void
     * @throws \PDOException
     */
    public static function insertRows($table, array $data)
    {
        if(!$data || !is_array($data)){
            throw new \PDOException('参数data必需数组');
        }
        $sql = "INSERT INTO `{$table}`";
        $field = "";
        $variable = "";
        $params = [];
        #判断是否二维数组，若否则将其设为二维数组
        if(count($data) == count($data, 1))$data = [$data];
        $key = 0;
        foreach($data as $item){
            #获得字段名,若已设置则无需重复
            if(!$field){
                $field = join(',', array_keys($item));
            }
            $variablePre = [];
            foreach($item as $name => $var){
                $variablePre[] = ":{$key}_{$name}";
                $params[$key."_".$name] = $var;
            }
            $variable .= "(".join(',', $variablePre)."),";
            $key++;
        }
        if(!$field || !$variable){
            throw new \PDOException('无法整理字段名');
        }
        $sql .= "(".$field.")VALUES".trim($variable,',');
        return self::update($sql,$params);
    }

    public static function lastInsertId(){
        return self::$master_ins->lastInsertId();
    }

    /**
     * @return bool
     * @throws \PDOException
     */
    public static function beginTransaction(){
        try {
            if (is_null(self::$master_ins)) {
                self::$master_ins = self::connection(self::$setting['master']);
            }
            if(!self::$master_ins->inTransaction()){
                return self::$master_ins->beginTransaction();
            }else{
                return null;
            }
        } catch (\PDOException $e) {
            // 服务端断开时重连一次
            if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
                self::close(true);
                return self::beginTransaction();
            } else {
                throw $e;
            }
        }
    }

    public static function commit(){
        if(self::$master_ins->inTransaction())self::$master_ins->commit();
    }

    public static function rollBack(){
        if(self::$master_ins->inTransaction())self::$master_ins->rollBack();
    }

    //无条件的分页查询
    //$whereSqlArray = ['a>1', 'b<2'];
    public static function getPageData($field='', $table='', $page=1, $size=10, $indexField='id', $orderBy='desc', $whereSqlArray='') {
        $whereSqlStr = '';
        $andSqlStr = '';
        if(!is_array($whereSqlArray)) $whereSqlArray= [$whereSqlArray];
        if($whereSqlArray) {
            $sqlStr = join(' AND ', $whereSqlArray);
            $whereSqlStr = ' WHERE '. $sqlStr;
            $andSqlStr = ' AND  '. $sqlStr;
        }
        if($orderBy == 'desc') {
            $sql = "SELECT {$field} FROM `{$table}` WHERE {$indexField} <=(SELECT {$indexField} FROM `{$table}` {$whereSqlStr} ORDER BY {$indexField} desc LIMIT ". (($page-1)* $size) .",1) {$andSqlStr} ORDER BY {$indexField} desc LIMIT ". ($size);
//            echo $sql;
            return self::getRows($sql);
        } else {
            $sql = "SELECT {$field} FROM `{$table}` WHERE {$indexField} >=(SELECT {$indexField} FROM `{$table}` {$whereSqlStr} ORDER BY {$indexField} ASC LIMIT ". (($page-1)* $size) .",1) {$andSqlStr} ORDER BY {$indexField} ASC LIMIT ". ($size);
//                        echo $sql;
            return self::getRows($sql);
        }
    }

    //统计总数
    public static function countPageData($table='', $whereSqlArray) {
        $whereSqlStr = '';
        if(!is_array($whereSqlArray)) $whereSqlArray= [$whereSqlArray];
        if($whereSqlArray) {
            $sqlStr = join(' AND ', $whereSqlArray);
            $whereSqlStr = ' WHERE '. $sqlStr;
        }
        $sql = "SELECT COUNT(*) FROM `{$table}` {$whereSqlStr}";
//        echo $sql;
        return self::getValue($sql);
    }

    //统计总数
    public static function getRowBy( $table_ = '' , $fields = "*" , $where_ = "1", $debug=false)
    {
        if ( !$table_ ) return '';
        $sql = "SELECT " . $fields . " FROM `" . $table_ . "` WHERE  " . $where_ ;
        if($debug) return $sql;
        return self::getRow($sql); //此处不能加limit 0,1 因为如果有sum或count函数 则会统计出错
    }

    //统计总数
    public static function getRowsBy( $table_ = '' , $fields = "*" , $where_ = "1", $debug=false)
    {
        if ( !$table_ ) return '';
        $sql = "SELECT " . $fields . " FROM `" . $table_ . "` WHERE  " . $where_ ;
        if($debug) return $sql;
        return self::getRows($sql); //此处不能加limit 0,1 因为如果有sum或count函数 则会统计出错
    }

    //是否存在
    public static function ifExist( $table_ = '' , $where_ = "1", $debug=false)
    {
        if ( !$table_ ) return '';
        $sql = "SELECT count(*) FROM `" . $table_ . "` WHERE  " . $where_ ;
        if($debug) return $sql;
        return self::getValue($sql); //此处不能加limit 0,1 因为如果有sum或count函数 则会统计出错
    }

}