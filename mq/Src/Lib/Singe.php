<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/16
 * Time: 16:14
 * 处理个人聊天信息
 */

namespace Pub;
use \RedisMQ;

class Singe
{

    protected static $streamName;

    public function __construct($customer='c1')
    {
        self::$streamName = \CommonCfg::get('mqRules.soleName');
        self::_dealMqData($customer);
    }


    //处理单人信息
    private static function _dealMqData($customer='c1') {
        while (true) {
            //读取pending的消息
            $pendingData = \RedisMQ::getUnAck(self::$streamName, $customer);
            if(!$pendingData) {
                break;
            }
//            if(isset($pendingData['MMR_Single'])) break;
            //读取未处理的消息 每次只有1条 遍历才可以拿 msgid
            foreach ($pendingData as $msgId=> $datum) {
                self::_insertData($msgId, $datum[1], $customer);
            }
        }

        //读取未处理的消息
        while (true) {
            $newData = \RedisMQ::getMqData(self::$streamName, 0, $customer);
            //读取未处理的消息 每次只有1条 遍历才可以拿 msgid
            foreach ($newData as $msgId=> $datum) {
//                print_r($datum);
                if(!$datum[1]) {
                    print_r("没有datum[1] \n");
                    print_r($datum[1]);
                    return;
                }
                self::_insertData($msgId, $datum[1], $customer);
            }
        }
    }
  

    /**
     * 插入数据
     */
    private static function _insertData($msgId, $datumString, $customer='')
    {
        try {
            $status = \MsgTask::dealSingleMsg($datumString);
            if($status !== true) {
                throw new \Exception("insertData_error:".$status);
            }
            //执行完成 删除stream的msgid
            \RedisMQ::ack(self::$streamName, $msgId);
            \MsgCount::addMsgNum(0);
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }
        //进程内存写入缓存 监控 posix_getpid()
//        print_r("消费者:{$customer} ackSingle:{$msgId}\n");
        Released::saveRam(memory_get_usage());
    }

    //删除方法
    //XTRIM MMR_Single MAXLEN ~ 10
}
