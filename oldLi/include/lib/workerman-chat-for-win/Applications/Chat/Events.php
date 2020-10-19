<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose
 */
use \GatewayWorker\Lib\Gateway;

class Events
{
    //实例化数据库链接
    public static function getMysqlUrl() {
        //我的client要录入数据库
        $allPath = realpath(dirname(__FILE__));
        $filePathArray = explode('\\', $allPath);
        array_pop($filePathArray);
        array_pop($filePathArray);
        $filePath = join('\\', $filePathArray);
        return  $filePath.'\\mysql_webchat.php';
    }
    //通知我 对方上线了
    public static function tell_me_his_online($to_uid) {
        Gateway::sendToCurrentClient(json_encode(array('type'=>'his_online', 'his_uid'=>$to_uid)));
    }
    //通知对方 我上线了
    public static function tell_him_im_onlne($toClientId='', $my_uid=0) {
        if(!$toClientId) return;
        Gateway::sendToClient($toClientId, json_encode(array('type'=>'his_online', 'his_uid'=>$my_uid)));
    }
    //获取uid的所有的clientid和聊天对象【多页面聊天时 会出现很多不一样的client的现象 聊天对象也可能重复多样 】
    public static function getUserClientInfo($room_id=0, $uid=0) {
        if(!$room_id) return 0;
        if(!$uid) return 0;
        $chatSessionData = Gateway::getClientSessionsByGroup($room_id);
        $clientData = array();
        //通知对方我的clienct 和 通知本地他的client
        foreach($chatSessionData as $temp_clientid => $item_) {
            $tmp_uid = isset($item_['my_uid']) ? $item_['my_uid'] : 0;
            $tmp_talk_to_uid = isset($item_['talking_to_uid']) ? $item_['talking_to_uid'] : 0;
            if($tmp_uid == $uid) {
                $clientData[] = array($temp_clientid, $tmp_talk_to_uid);
            }
        }
        return $clientData;
    }
    //找到和我聊天的人的clientid和我的uid (离线通知时要输出我的uid)
    public static function getClientIdWhoTalkToMe($room_id=0, $myUid=0) {
        if(!$room_id) return 0;
        if(!$myUid)  return 0;
        $hisClientData = array();
        $chatSessionData = Gateway::getClientSessionsByGroup($room_id);
        //通知对方我的clienct 和 通知本地他的client
        foreach($chatSessionData as $temp_clientid => $item_) {
            $tmp_talk_to_uid = isset($item_['talking_to_uid']) ? $item_['talking_to_uid'] : 0;
            if($tmp_talk_to_uid == $myUid) {
                $hisClientData[] = $temp_clientid;
            }
        }
        return $hisClientData;
    }
    /**
     * 有消息时
     * @param int $client_id
     * @param mixed $message
     *
     * $chatSessionData = Gateway::getClientSessionsByGroup($room_id); 中包含的数据
        [ '$client_id' => {session, 'roomid': '123' }, ... ]
     * 其中session 有自定义的：
     * my_uid 为 client_id 对应的会员uid
     * talking_to_uid 为 client_id 聊天对象的会员uid
     */
    public static function onMessage($client_id, $message)
    {
        // 客户端传递的是json数据
        $message_data = json_decode($message, true);
        if(!$message_data)
        {
            return ;
        }

        // 根据类型执行不同的业务
        switch($message_data['type'])
        {
            //把自己加入聊天室
            case 'join_chat':
                $fromUid = isset($message_data['my_uid']) ? intval($message_data['my_uid']) : 0;
                $room_id = isset($message_data['room_id']) ? trim($message_data['room_id']) : '';
                if($room_id) {
                    if(!isset($_SESSION['room_id'])) $_SESSION['room_id'] = $room_id;
                    echo "client id: $client_id join in ".$room_id ." \n";
                    $_SESSION['my_uid'] = $fromUid;
                    Gateway::joinGroup($client_id, $room_id);//把客户加入聊天室
                    //找到已经和我建立聊天的人 告诉他 大爷我已经上线。
                    $uClientInfo = self::getClientIdWhoTalkToMe($room_id, $fromUid);
                    foreach($uClientInfo as $uClientInId) {
                        //通知他 我上线了
                        self::tell_him_im_onlne($uClientInId, $fromUid);
                    }
                    //我的client要录入数据库
                    $dbUrl = self::getMysqlUrl();
                    include_once($dbUrl);
                    $db = mysql_webchat::getInstance();
                    //废除之前使用此client_id的会员记录
                    DbBase::updateByData('webchat_user', $client_id,array('l_client_id' => ''), 'l_client_id');
                    //更新我的client_id
                    DbBase::updateByData('webchat_user', $fromUid, array('l_client_id' => $client_id), 'l_uid');
                    $db->Close();
                }
                break;
            //客户正在输入
            case 'writing':
                $from_uid = isset($message_data['from_uid']) ? intval($message_data['from_uid']) : '';//动作的来源uid
                $to_uid = isset($message_data['to_u_id']) ? $message_data['to_u_id'] : '';
                $room_id = isset($message_data['room_id']) ? trim($message_data['room_id']) : '';
                if($room_id && $to_uid) {
                    //找到他所有的clientid
                    $toUClientInfo = self::getUserClientInfo($room_id, $to_uid);
                    if(!$toUClientInfo) return;
                    foreach($toUClientInfo as $n=> $item_) {
                        $toUClientId = $item_[0];
                        $hisTalkingTo = $item_[1];
                        if($hisTalkingTo == $from_uid) { //其的聊天对象必须是来源对象，才显示状态
                            //如果对方正在这当前人聊天 则显示输入状态
                            $new_message = array(
                                'type'=>'on_write',
                                'time'=> date('Y-m-d H:i:s',time()),
                            );
                            //发给对方
                            return Gateway::sendToClient($toUClientId, json_encode($new_message));
                        }
                    }
                }
            break;
            //通知回答方 更新他的未读的信息
            case 'notify_him_refresh_no_read_msg':
                $to_uid = isset($message_data['to_u_id']) ? $message_data['to_u_id'] : '';
                $room_id = isset($message_data['room_id']) ? trim($message_data['room_id']) : '';
                if($room_id && $to_uid) {
                    //找到他所有的clientid
                    $toUClientInfo = self::getUserClientInfo($room_id, $to_uid);
                    if(!$toUClientInfo) return;
                    foreach($toUClientInfo as $n=> $item_) {
                        $toUClientId = $item_[0];
                        $hisTalkingTo = $item_[1];
                            //如果对方正在这当前人聊天 则显示输入状态
                        $new_message = array(
                            'type'=>'cmd_refresh_his_no_read_num',
                            'to_uid'=> $to_uid,
                            'time'=> date('Y-m-d H:i:s',time()),
                        );
                        //发给对方
                        return Gateway::sendToClient($toUClientId, json_encode($new_message));
                    }
                }
            break;
            // 客户端回应服务端的心跳
            case 'pong':
                return;
            break;
            // 客户端发起聊天请求
            case 'talk_to_user':
                // 判断是否有房间号
                if(!isset($message_data['room_id']))
                {
                    throw new \Exception("\$message_data['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']} \$message:$message");
                }
                // 判断是否有提交uid
                if(!isset($message_data['my_uid']))
                {
                    throw new \Exception("\$message_data['my_uid'] not set. client_ip:{$_SERVER['REMOTE_ADDR']} \$message:$message");
                }
                // 判断是否有提交to_uid
                if(!isset($message_data['to_uid']))
                {
                    throw new \Exception("\$message_data['to_uid'] not set. client_ip:{$_SERVER['REMOTE_ADDR']} \$message:$message");
                }
                //会员uid的聊天对象数据包
                //格式：
                /*talking_to_uid = array(
                    [7] => array(
                    '1' : 1 client_id
                    )
                );*/
                $my_uid = isset($message_data['my_uid']) ? intval($message_data['my_uid']) : 0;
                $to_uid = isset($message_data['to_uid']) ? intval($message_data['to_uid']) : 0;

                //echo "my_uid: $my_uid  client_id: $client_id   talk_to: $to_uid   \n";
                $room_id = $message_data['room_id'];
                //通知我的聊天对象 我上线了
                $whoTalkToMe = self::getClientIdWhoTalkToMe($room_id, $my_uid);
                foreach($whoTalkToMe as $uClientInId) {
                    //通知他 我上线了
                    self::tell_him_im_onlne($uClientInId, $my_uid);
                }
                //判断我的聊天对象是否在线
                $toUClientInfo = self::getUserClientInfo($room_id, $to_uid);
                if($toUClientInfo) {
                    self::tell_me_his_online($to_uid);
                }
                //获取我的聊天对象包 更新
                $_SESSION['talking_to_uid'] = $to_uid;
                $_SESSION['my_uid'] = $my_uid;
                $_SESSION['room_id'] = $room_id;
                return Gateway::joinGroup($client_id, $room_id);//把客户加入聊天室
            break;
            case 'say':
                // 非法请求
                if(!isset($_SESSION['room_id']))
                {
                    echo ("\$_SESSION['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']}");
                    exit;
                }
                $room_id = $_SESSION['room_id'];
                $my_uid = isset($message_data['my_uid']) ? intval($message_data['my_uid']): 0;
                $to_uid = isset($message_data['to_uid']) ? intval($message_data['to_uid']): 0;
                $words = urldecode($message_data['content']);
                //找到对方uid的所有会话页面，并且在其中找到正在和我对话的clientid 才发起内容 可能多个clientid(页面)都在和我聊天，所以找到其中一个clientid后不能break停止。
                $toUClientInfo = self::getUserClientInfo($room_id, $to_uid);
                //echo "to_client_info ". json_encode($to_client_info) ." \n";
                foreach($toUClientInfo as $n=> $item_) {
                    $his_client_id = $item_[0];
                    $his_talk_to_uid = $item_[1];
                    if(strlen($his_client_id) > 4 ) {
                        //通知对方信息
                        $new_message = array(
                            'type'=> 'send_message',
                            'from_uid'=> $my_uid,
                            'room_id'=> $room_id,
                            'content'=> nl2br(htmlspecialchars($words)),
                            'time'=>  date('Y-m-d H:i:s',time()),
                        );
                        Gateway::sendToClient($his_client_id, json_encode($new_message));
                    }
                }

                //发给自己
                $new_message = array(
                    'type'=>'i_say',
                    'room_id'=>$room_id,
                    'content'=> nl2br(htmlspecialchars($words)),
                    'time'=> date('Y-m-d H:i:s',time()),
                );
                //echo "isay ". json_encode($new_message) ." \n";
                Gateway::sendToCurrentClient(json_encode($new_message));

                //当本地可能打开多个聊天页面时，需要发给所有自己
                //1.找到my_uid都是我的clientid(要排除当前这个，因为上面已经发过一次通知)
                $myUClientInfo = self::getUserClientInfo($room_id, $my_uid);
                foreach($myUClientInfo as $n=> $item_) {
                    $tmp_my_client_id = $item_[0];
                    $tmp_my_talk_to_uid = $item_[1];
                    //(要排除当前这个)
                    if($tmp_my_client_id == $client_id) continue;
                    if(strlen($tmp_my_client_id) > 4 && $tmp_my_talk_to_uid == $to_uid ) {
                        $new_message = array(
                            'type'=>'i_say',
                            'room_id'=>$room_id,
                            'content'=> nl2br(htmlspecialchars($words)),
                            'time'=> date('Y-m-d H:i:s',time()),
                        );
                        Gateway::sendToClient($tmp_my_client_id, json_encode($new_message));
                    }
                    //通知我的列表(在列表时也通知)
                    $new_message = array(
                        'type'=>'list_user',
                        'room_id'=>$room_id,
                        'content'=> nl2br(htmlspecialchars($words)),
                        'time'=> date('Y-m-d H:i:s',time()),
                    );
                    Gateway::sendToClient($tmp_my_client_id, json_encode($new_message));
                }
                /* $new_message = array(
                    'type'=>'say',
                    'from_client_name' =>$client_name,
                    'content'=>nl2br(htmlspecialchars($message_data['content'])),
                    'time'=> date('Y-m-d H:i:s',time()),
                );
                return Gateway::sendToGroup($room_id ,json_encode($new_message));*/
        }
    }

    /**
     * 当客户端断开连接时 聊天室中不再有其uclient信息
     * @param integer $client_id 客户端id
     */
    public static function onClose($client_id)
    {
        $room_id = isset($_SESSION['room_id']) ? $_SESSION['room_id'] : 0;
        if($room_id) {
            //我的client要录入数据库
            $dbUrl = self::getMysqlUrl();
            include_once($dbUrl);
            $db = mysql_webchat::getInstance();
            //判断聊天对象是否有注册
            $myUinfo = DbBase::getRowBy('webchat_user', 'l_uid,l_talking_dialogs', "l_client_id='". $client_id ."'");
            if($myUinfo) {
                DbBase::updateByData('webchat_user', $client_id, array('l_client_id'=>''), 'l_client_id');
                $myUid = $myUinfo['l_uid'];
                $myTalkingToIds = $myUinfo['l_talking_dialogs'];
                if($myTalkingToIds) {
                    $talkingTargets = $db->getAll('webchat_user_obj', 'l_from_uid,l_to_uid', "l_id IN(". trim($myTalkingToIds, ',') .")");
                    foreach($talkingTargets as $n => $v) {
                        //获取聊天对象uid
                        $talkToUid = $v['l_from_uid'] == $myUid ? $v['l_to_uid'] : $v['l_from_uid'];
                        if($talkToUid) {
                            $talkToClientInfo = DbBase::getRowBy('webchat_user', 'l_client_id', "l_uid=". $talkToUid ."");
                            if($talkToClientInfo && $talkToClientInfo['l_client_id']) {
                                //通知聊天对象 我离线了
                                $new_message = array(
                                    'type'=>'some_one_logout',
                                    'room_id'=> $room_id,
                                    'his_uid'=> $myUid,
                                    'time'=> date('Y-m-d H:i:s',time()),
                                );
                                Gateway::sendToClient($talkToClientInfo['l_client_id'], json_encode($new_message));
                            }
                        }
                    }
                }
            }
            $db->Close();

        }
    }

}
