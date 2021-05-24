<?php

namespace fast;
/**
 *	Redis 操作类
 */


use think\Config;
class RDS
{

    static $redis;

    /**
     * socket 底下不需要单例模式 默认只实例化一次
     */
    public function __construct()
    {
        if (!extension_loaded('redis')) {
            throw new \BadFunctionCallException('not support: redis');      //判断是否有扩展
        }

        try {
            self::_connectRds();
        } catch (Exception $e) {
            echo"rds无法连接  ======";
            echo $e->getMessage();
            return;
        }
    }
    //连接redis
    private static function _connectRds()
    {
        self::$redis = new \Redis;
        $setting = Config::get('redis.single');
        self::$redis->connect($setting['address'], $setting['port']);
        if ($setting['password']) {
            self::$redis->auth($setting['password']);
        }
    }

      /**
      * 禁止外部克隆
      */
    public function __clone()
      {
             trigger_error('Clone is not allow!',E_USER_ERROR);
      }


    #监听key
    static function watch($tabName)
    {
        return self::$redis->watch($tabName);
    }
    #开启事务
    static function multi()
    {
        return self::$redis->multi();
    }
    #提交事务
    static function exec()
    {
        return self::$redis->exec();
    }

    #获取所有HASH的键值
    static function hkeys($tabName)
    {
        return self::$redis->hkeys($tabName);
    }
    //切换数据库
    static function select($index)
    {
        return self::$redis->select($index);
    }

    #设置HASH
    static function hset($tabName, $key, $value)
    {

       return self::$redis->hset($tabName, $key, $value);
    }

    static function hMSet($key, $hashKey) {
        self::$redis->hMSet($key, $hashKey);
    }

    //SET if Not eXists  之前已设置返回0 之前未设置 返回1
    static function setNx($key, $val) {
        return self::$redis->setNx($key, $val);
    }
    #获取指定HASH的键值
    static function hget($tabName, $key)
    {
        return self::$redis->hget($tabName, $key);
    }

    static function hMGet($key, $hashKeyList) {
        return self::$redis->hMGet($key, $hashKeyList);
    }

    #设置HASH的值
    static function set($tabName, $value)
    {
        return self::$redis->set($tabName, $value);
    }

    // $valueArr = [
    //     $tableName => $value,
    //     $tableName => $value,
    // ]
    static function mSet($valueArr = [])
    {
        return self::$redis->mSet($valueArr);
    }

    # 设置SET的值，同时设置超时时间 second
    static function setEx($key, $liveTime, $value) {
          return self::$redis->setex($key, $liveTime, $value);
    }
    #获取SET的值
    static function get($tabName)
    {
        return self::$redis->get($tabName);
    }

    static function mGet($tabName = [])
    {
        return self::$redis->mGet($tabName);
    }

    static function keys($keys)
    {
        return self::$redis->keys($keys);
    }

    # 删除SET的值
    static function del($key) {
          return self::$redis->del($key);
    }

    #删除指定HASH
    static function hdel($tabName,$key)
    {
        return self::$redis->hdel($tabName,$key);
    }

    // 删除某个table的多个key
    static function hMDel($tabName, $keyList = [])
    {
        return call_user_func_array([self::$redis, 'hDel'], array_merge([$tabName], $keyList));
    }

    #获取HASH的数量
    static function hlen($tabName)
    {
        return self::$redis->hlen($tabName);
    }

    #插入数据 左部
    static function lpush($tabName,$value)
    {
        self::$redis->lpush($tabName,$value);
    }
    #插入数据 右部
    static function rpush($tabName,$value)
    {
        self::$redis->rpush($tabName,$value);
    }
    #删除指定值的所有数组  与命令行的不同 命令行模式是count在前面，$value在后面
    static function lrem($tabName,$value){
        return self::$redis->lRem($tabName, $value, 0);
    }
    #查看HASH表指定字段是否存在
    static function hexists($tabName,$value)
    {
        return self::$redis->hexists($tabName,$value);
    }
    //存在key
    static function existsKey($key)
    {
        return self::$redis->exists($key);
    }

    #命令用于通过索引获取列表中的元素
    static function lindex($tabName,$value)
    {
        return self::$redis->lindex($tabName,$value);
    }

    #移除并返回列表的最后一个元素
    public static function rpop($tabName)
    {
        return self::$redis->rpop($tabName);
    }


    #分页返回列表 $page从0开始
    public static function lrange($cacheName, $page=0, $pagesize=9)
    {
        $fromIndex = $page * $pagesize;
        $toIndex = ($page + 1) * $pagesize;
        return self::$redis->lrange($cacheName, $fromIndex, $toIndex);
    }
    #分页返回列表 $page从0开始
    public static function getIndex($cacheName, $fromIndex=0, $toIndex=9)
    {
        return self::$redis->lrange($cacheName, $fromIndex, $toIndex);
    }
    #删除元素
    public static function ltrim($cacheName, $fromIndex=0, $toIndex=9)
    {
        return self::$redis->ltrim($cacheName, $fromIndex, $toIndex);
    }
    #查询数量
    public static function llen($cacheName)
    {
        return self::$redis->llen($cacheName);
    }

    # key 所储存的字符串值，获取指定偏移量上的位(bit)
    static function getBit($key,$index)
    {
        return self::$redis->getBit($key,$index);
    }

    //无序集合
    //加入集合
    static function sAdd($key, $val)
    {
        return self::$redis->sAdd($key, $val);
    }

    // 一次加入多个成员到集合
    static function sMAdd($key, $valList = [])
    {
        return call_user_func_array([self::$redis, 'sAdd'], array_merge([$key], $valList));
    }

    // 检查是否是无序集合的成员
    static function sIsMember($key, $val)
    {
        return self::$redis->sIsMember($key, $val);
    }

    // 从无序集合中移除某个成员
    static function sRem($key, $val)
    {
        return self::$redis->sRem($key, $val);
    }

    //获取集合元素的数量
    static function scard($key)
    {
        return self::$redis->sCard($key);
    }
    #返回 无序集合 中的所有的成员。 不存在的集合 key 被视为空集合
    static function sMembers($key)
    {
        return self::$redis->sMembers($key);
    }

    #用于对 key 所储存的字符串值，设置或清除指定偏移量上的位(bit)
    static function setBit($key,$index,$value){
        self::$redis->setBit($key,$index,$value);
    }

    #批量移除元素,Set类型
    static function sSubArray($tabName,array $list)
    {
        if(is_string($list)) $list = explode(',', $list);
        return call_user_func_array([self::$redis, 'sRem'], array_merge([$tabName], $list));
    }

    # 设置超时时间
    static function expire(string $key, int $sec) {
        self::$redis->expire($key, $sec);
    }

    //缓存所有信息
    protected static $msgCacheName = 'all_msg_cache';
    //uniqueid: [{
    //   from_uid: 12,
    //   received: 0,
    //   to_uid: 66,
    //   send_callback: 0,
    //   content: 'xxxx',
    //   time: '20180000',
    //}]
    //保存uniqueid缓存
    public static function setUniqueidMsg($uniqueid='', $data=[]) {
        self::hset(self::$msgCacheName,  $uniqueid, $data);
    }
    //获取uniqueid缓存
    public static function getUniqueidMsg($uniqueid='') {
        return self::hget(self::$msgCacheName,  $uniqueid);
    }

   //有序集合 批量添加
    //$elems  [
    //{'score'=> $val}, {'score'=> $val}
    //]
    public static function zAdd($cacheName, $score=null, $member) {
//        print_r("zdd写入:{$cacheName} {$score} {$objName}\n");
       if(!$score) $score = microtime(true);
        return self::$redis->zAdd($cacheName, $score, $member);
    }
   //有序集合 移除
    public static function zRem($cacheName, $objName) {
        return self::$redis->zRem($cacheName, $objName);
    }
   //有序集合 计算总数
    public static function zSize($cacheName) {
        return self::$redis->zCard($cacheName);
    }
    //有序集合 升序排列
    public static function zRange($cacheName, $widthScore=true) {
        return self::$redis->zRange($cacheName, 0, -1, $widthScore);
    }
    //有序集合 判断成员的分数排名
    public static function zScore($cacheName, $member) {
        return self::$redis->zScore($cacheName, $member);
    }
   //有序集合 倒序排列
    public static function zRevRange($cacheName) {
        return self::$redis->zReverseRange($cacheName, 0, -1, true);
    }
   //有序集合 查询分数小于等于多少的
    public static function zFindScoreSmaller($cacheName, $score, $limit =10) {
        return self::$redis->ZRANGEBYSCORE($cacheName, '-inf', $score, array('withscores' => TRUE, 'limit'=> [0, $limit]));
    }
}