<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/16
 * Time: 16:14
 * 处理个人聊天信息
 */

namespace Pub;

class Group
{

    protected static $streamName;

    public function __construct($customer='c1', $streamName)
    {
        self::$streamName = $streamName;
        self::_dealMqData($customer);
    }

    //处理群聊信息
    private static function _dealMqData($customer) {
        while (true) {
            //读取pending的消息
            $pendingData = RedisMQ::getUnAck(self::$streamName, $customer);
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
            $newData = RedisMQ::getMqData(self::$streamName, 0, $customer);
            //读取未处理的消息 每次只有1条 遍历才可以拿 msgid
            foreach ($newData as $msgId=> $datum) {
                print_r("消费者:{$customer} 写入group.Data:{$msgId}\n");
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
        //开启事务 只要有一个群成员消息未写入 则回滚当前人
        DbBase::beginTransaction();
        try {
            $tmpData = json_decode($datumString, true);
            if($tmpData) {
                $status = MsgTask::dealSingleMsg($datumString);
                //保证其他人正常 先屏蔽错误
                if($status !== true) {
                    throw new \Exception("insertGroupData_error:".$status);
                }
            }
            DbBase::commit();
        } catch (\Exception $e) {
            DbBase::rollBack();
            echo $e->getMessage() . "\n";
        }
         RedisMQ::ack(self::$streamName, $msgId);
    }

    //删除方法
    //XTRIM MMR_Single MAXLEN ~ 10
}
