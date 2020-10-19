<?php

namespace Pub;
#Msg消息单用户写入任务

class MsgTask
{

    //jsdecode 递归解析json字符串
    protected static function __jsonDecode($str='') {
        if(is_string($str)) {//"{\"uid\":\"138\", json_decode后解析为 "{"uid":"138",
            $str = json_decode($str, true);
            return self::__jsonDecode($str);
        }
        return $str;
    }

    //从mq任务请求过来的 写入单聊数据
    public static function dealSingleMsg($datumString) {
        $msgData = json_decode($datumString, true);
        if($msgData) {
            if(!isset($msgData['toIsReTry']) || !$msgData['toIsReTry']) $msgData['toIsReTry'] = 0;
            if(!isset($msgData['fromIsReTry']) || !$msgData['fromIsReTry']) $msgData['fromIsReTry'] = 0;
            if(!isset($msgData['both']) || !$msgData['both']) $msgData['both'] = 'both';
            $replyObj = isset($msgData['replyObj']) ? $msgData['replyObj'] : '';
            // 是否阅后即焚消息
            $isMsgDar = isset($msgData['isMsgDar']) ? $msgData['isMsgDar'] : false;
            $msgType = \PubMsg::getMsgTypeFromMsg($msgData);
            //回复对象
            $replyUniqueid = '';
            if($replyObj) {
                $replyObj = json_decode($replyObj, true);
                $replyUniqueid = isset($replyObj['id']) ? $replyObj['id'] : '';
                $replyObj = json_encode($replyObj);
            }
            if(!$replyObj || $replyObj =='[]') $replyObj = '';//数据库要清空
//            print_r("single_replyObj:\n");
//            print_r($replyObj);
            try {
                $insData = [
                    'fromUid'          => $msgData['fromUid'],
                    'content'          => $msgData['content'],
                    'groupId'          => 0,
                    'sendbackstate'    => $msgData['sendbackstate'],
                    'receiveState'     => $msgData['receiveState'],
                    'fromTime'         => $msgData['fromTime'],
                    'type'             => $msgType,
                    'uniqueId'         => $msgData['uniqueId'],
                    'noticeSwitch'     => $msgData['noticeSwitch'],
                    'isReTry'          => $msgData['fromIsReTry'],
                    'targetUid'        => $msgData['targetUid'],
                    'delStatus'        => isset($msgData['delStatus']) ? (int)$msgData['delStatus'] : 0,
                    'readed'           => isset($msgData['readed']) ? (int)$msgData['readed'] : 0,
                    'reply'            => $replyObj,
                    'replyUniqueid'    => $replyUniqueid,
                ];
                // 写入发送者消息表
                if($msgData['both'] == 'both' || $msgData['both']=='me') {
                    MsgPartition::writeFriend($msgData['fromUid'], $insData);
                    if (!$msgData['sendbackstate'] && \PubMsg::isUnloadType($msgType)) {
                        //2020.4.21 离线消息：不能自己写给自己
                        if($msgData['fromUid'] != $msgData['toUid']) {
                            \PubMsg::userUnloadOneMsg('add', $msgData['fromUid'], $msgData['toUid'], false, [$msgData['uniqueId']]);
                        } else {
                            \PubMsg::userUnloadOneMsg('add', $msgData['fromUid'], $msgData['targetUid'], false, [$msgData['uniqueId']]);
                        }
                    }
                    // 插入消息阅后即焚表
                    if ($isMsgDar) {
                        MsgDar::insertRow($msgData['fromUid'], $msgData['uniqueId'], $msgData['fromTime']);
                    }
                }
//                print_r('$msgData'."\n");
//                print_r($msgData);
                if ($msgData['toUid'] && $msgData['fromUid'] != $msgData['toUid']) {
                    //写入接受者消息表
                    if($msgData['both'] == 'both' || $msgData['both']=='he') {
                        // 写入信息分区表
                        // 数组+法运算相同下标不覆盖
                        MsgPartition::writeFriend($msgData['toUid'], [
                                'targetUid' => $msgData['targetUid'] !== PubMsg::$systemFromUid ? $msgData['fromUid'] : $msgData['targetUid'],
                                'isReTry'   => $msgData['toIsReTry'],
                            ] + $insData);
                        // 强制写入离线消息 因为离线状态可能是自动断网
                        if($msgData['targetUid'] !== PubMsg::$systemFromUid) {
                            PubMsg::userUnloadOneMsg('add', $msgData['toUid'], $msgData['fromUid'], false, [$msgData['uniqueId']]);
                        } else {
                            PubMsg::userUnloadOneMsg('add', $msgData['toUid'], $msgData['targetUid'], false, [$msgData['uniqueId']]);
                        }
                        // 插入消息阅后即焚表
                        if ($isMsgDar) {
                            MsgDar::insertRow($msgData['toUid'], $msgData['uniqueId'], $msgData['fromTime']);
                        }
                    }
                    // 添加到最近联系人
                    if(in_array($msgType, PubMsg::needUpdateRecenterType())) {
                        //mq执行
                        if($msgData['targetUid'] !== PubMsg::$systemFromUid) {
                            $taskData = [
                                $msgData['fromUid'],
                                $msgData['toUid'],
                                false,
                                $msgData['uniqueId'],
                                $msgData['fromTime']
                            ];
                            RedisMQ::add(\CommonCfg::get('mqRules.recenterName'), 'data', json_encode($taskData));
                            $taskData = [
                                $msgData['toUid'],
                                $msgData['fromUid'],
                                false,
                                $msgData['uniqueId'],
                                $msgData['fromTime']
                            ];
                            RedisMQ::add(\CommonCfg::get('mqRules.recenterName'), 'data', json_encode($taskData));
                        }
                    }
                } else { //异常 touid==fromuid 信息 直接作为success处理
                    return true;
                }
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
        return true;
    }


}
