<?php

/*
 * 创建3个stream
 * 每个stream有1个分组
 * 每个分组有1个消费者
 *
 * */

namespace Pub;

class RedisMQ

{

    //Redis连接的句柄

    static $redis;
    public static $mqCfg = [
        'groupName'=> 'groupStream', // mq群子表消息缓存名字,
        'redisMqGroup' => [
            // key与上面groupName的值相同
            'groupStream' => [
                'groupName' => '_Group',
            ],
        ]
    ];
    protected static $RdsSetting = [
        'type'  => 'redis',//指定类型
        'prefix' => '',
        'address'      => '127.0.0.1',
        'port' => 6379,
        'password'=>'Ygmlxomlg6',
    ];

    //key名

    static $keyName;

    //定义3个分群 每个分组有自己的stream和自己的消费者
    private static $groupTypes = [];

    //获取stream名
    private static function __getStreamName($groupName)
    {
        return 'MMR' . $groupName;
    }

    //创建stream群组
    public static function createStream() {
        try {
            return self::_createGroups(); //连接stream
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *    初始化
     */

    public static function init()

    {
        try {
            self::$groupTypes = self::$mqCfg['redisMqGroup'];
//            print_r("所有群组:\n");
//            print_r(self::$groupTypes);
            //连接Redis
            self::_connectRds();
            self::_createGroups();
        } catch (\RedisException $e) {
            echo "RedisEx:" . $e->getMessage();
        } catch (Exception $e) {
            echo "Ex:" . $e->getMessage();
        }
    }

    /**
     * 创建一个组，需要在启用服务前操作
     * @param string $groupName
     * @return mixed
     * @throws Exception
     */
    private static function _createGroups()
    {
        //创建单个群
        $createOneGroup = function ($gName) use (&$createOneGroup) {
            $streamName = self::__getStreamName($gName);
            try {
                if (is_null(self::$redis)) self::_connectRds();
//                print_r("创建组：{$streamName}\n");
                return self::$redis->xgroup('create', $streamName, $gName, 0, true);
            } catch (\RedisException $e) {
                if (false === stripos($e->getMessage(), 'read error on connection')) {
                    throw new Exception($e->getMessage());
                }
                self::_close();
                return $createOneGroup($gName);
            }
        };
        foreach (self::$groupTypes as $streamKey => $datum) {
            $groupName = $datum['groupName'];
            $createOneGroup($groupName);
        }
    }

    private static function _connectRds()
    {
        self::$redis = new \Redis;
        $setting = self::$RdsSetting;
        self::$redis->connect($setting['address'], $setting['port']);
        if ($setting['password']) {
            self::$redis->auth($setting['password']);
        }
    }

    protected static function _close()
    {
        self::$redis = null;

    }


    /**
     * 确认一个消息
     * @param int $type
     * @param array $msgId
     * @return mixed
     * @throws Exception
     */
    public static function ack($streamIndex = 'groupStream', $msgId = [])
    {
        $cfg = self::$groupTypes[$streamIndex];
        $groupName = $cfg['groupName'];
        $streamName = self::__getStreamName($groupName);
        try {
            if (is_null(self::$redis)) self::_connectRds();
            if (!is_array($msgId)) $msgId = [$msgId];
            self::$redis->XDEL($streamName, $msgId);
            return self::$redis->xack($streamName, $groupName, $msgId);
        } catch (\RedisException $e) {
            if (false === stripos($e->getMessage(), 'read error on connection')) {
                throw new Exception($e->getMessage());
            }
            self::_close();
            return self::ack($streamIndex, $msgId);
        }
    }


    /**
     * 添加一个消息到尾部
     * @param int $type
     * @param $fieldName
     * @param $value
     * @return mixed
     * @throws Exception
     */

    public static function add($streamIndex = 'systemStream', $fieldName, $value)
    {
        if (!isset(self::$groupTypes[$streamIndex])) {
            print_r("不存在的组名:{$streamIndex}\n");
        }
        $cfg = self::$groupTypes[$streamIndex];
        $groupName = $cfg['groupName'];
        $streamName = self::__getStreamName($groupName);
        try {
            if (is_null(self::$redis)) self::_connectRds();
            if (is_array($value)) $value = json_encode($value);
            return self::$redis->xadd($streamName, '*', [$fieldName, $value]);
        } catch (\RedisException $e) {
            if (false === stripos($e->getMessage(), 'read error on connection')) {
                throw new Exception($e->getMessage());
            }
            self::_close();
            return self::add($streamIndex, $fieldName, $value);
        }
    }


    /**
     * 获得最前一个消息
     * @param int $type
     * @param null $block
     * @return
     * @throws Exception
     */
    public static function getMqData($type = 'systemStream', $block = null, $customer='c1')
    {
        $cfg = self::$groupTypes[$type];
        $groupName = $cfg['groupName'];
        $streamName = self::__getStreamName($groupName);
//        print_r("cfg:\n");
//        print_r($cfg);
//        print_r("streamName:{$streamName}\n");
        try {
            //若断链则重新连接
            if (is_null(self::$redis)) self::_connectRds();
            //以组读取
            $data = self::$redis->xreadgroup(
                $groupName,
                $customer,
                [$streamName => '>'],    //查询方式
                1, //读取1条
                ($block !== null ? (int)$block * 1000 : $block)//是否阻塞读取，不设置该值则为永远阻塞，若设置请设置值为正整型，单位是秒
            );
            $cfg = self::$groupTypes[$type];
            $streamName = self::__getStreamName($cfg['groupName']);
            return isset($data[$streamName]) ? $data[$streamName] : [];
        } catch (\RedisException $e) {
            $eMsg = $e->getMessage();
            if (false === stripos($eMsg, 'read error on connection')) {
                throw new \RedisException($eMsg);
            }
            self::_close();
            return self::getMqData($type, $block, $customer);
        }

    }


    /**
     * 获得一条未应答的消息 pending状态的消息
     * @param int $type
     * @return mixed
     * @throws Exception
     */

    public static function getUnAck($type = 'systemStream', $customer='c1/c2/c3')

    {
        try {
            //若断链则重新连接
            if (is_null(self::$redis)) self::_connectRds();
            //以组读取
            $cfg = self::$groupTypes[$type];
            $groupName = $cfg['groupName'];
            $streamName = self::__getStreamName($groupName);
//            $data = self::$redis->XPENDING(
//                $streamName,
//                $groupName,
//                '-',
//                '+',
//                1  //读取1条
//            );
////            print_r("pending:\n");
////            print_r($data);
//            return $data;
//
//            print_r("streamName:{$streamName}\n");
//            print_r("groupName:{$groupName}\n");
            //0是读取未消费过的数据
            $data = self::$redis->xreadgroup(
                $groupName,  //查询类型
                $customer,                          //消费者
                [$streamName => '0'],    //查询方式
                1  //读取1条
            );
            $cfg = self::$groupTypes[$type];
            $streamName = self::__getStreamName($cfg['groupName']);
            return isset($data[$streamName]) ? $data[$streamName] : [];

        } catch (\RedisException $e) {
            if (false === stripos($e->getMessage(), 'read error on connection')) {
                throw new Exception($e->getMessage());
            }
            self::_close();
            return self::getUnAck($type, $customer);
        }

    }


    /**
     * 裁剪，舍弃老旧的队列
     * @param int $type
     * @param int $max
     * @return mixed
     * @throws Exception
     */

    public static function trimList($streamIndex = 'systemStream', int $max)
    {
        $cfg = self::$groupTypes[$streamIndex];
        $groupName = $cfg['groupName'];
        $streamName = self::__getStreamName($groupName);
        try {
            if (is_null(self::$redis)) self::_connectRds();
            return self::$redis->xtrim($streamName, $max, true);
        } catch (\RedisException $e) {
            if (false === stripos($e->getMessage(), 'read error on connection')) {
                throw new Exception($e->getMessage());
            }
            self::_close();
            return self::trimList($streamIndex, $max);
        }

    }


    public static function ackGroup($msgId) {
        return self::ack('groupStream', $msgId);
    }
}