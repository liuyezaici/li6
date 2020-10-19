<?php
/**
 * Created by LR.
 * User: Administrator
 * Date: 2019/10/15
 * Time: 14:35
 * 处理最近联系人
 */

namespace Pub;
use \RedisMQ;
use \MsgTask;

class SetExpire
{
    protected static $streamName;

    public function __construct($customer='c1')
    {
        self::$streamName = \CommonCfg::get('mqRules.setExpireName');
        self::_dealMqData($customer);
    }

    //处理信息
    private static function _dealMqData($customer='c1') {
        while (true) {
            //读取pending的消息
            $pendingData = \RedisMQ::getUnAck(self::$streamName, $customer);
            if(!$pendingData) {
                break;
            }
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
//                print_r("写入SetExpire.Data:{$msgId}\n");
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
            $time1 = microtime(true);
            $tmpData = json_decode($datumString, true);
            if($tmpData) {
                $status = MsgTask::dealSetExpire($datumString);
                //保证其他人正常 先屏蔽错误
                if($status !== true) {
                    throw new \Exception("SetExpireData_error:".$status);
                }
            }
            //执行完成 删除stream的msgid
            $time2 = microtime(true) - $time1;
            \RedisMQ::ack(self::$streamName, $msgId);
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }
//        print_r("消费者:{$customer},ackRecenter:{$msgId},time use:{$time2}\n");
        Released::saveRam(memory_get_usage());
    }

    //删除方法
    //XTRIM MMR_Single MAXLEN ~ 10
}
