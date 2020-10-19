<?php

/**
*	Martin Tse 2018 06 27
* 	舍弃使用Workerman内部提供的Client管理，自定义管理
*/


namespace Workerman\Lib;

use \GatewayWorker\Lib\Gateway;
use \Workerman\Lib\Timer;
use \Workerman\Lib\DB;

class Clienter{

	static $clients;
	static $clients2Uid;
	static $client;

	static function init()
	{
		self::$clients = [];
		self::$client = [];
	}

	/**
	*	绑定客户端
	*/
	static function bindUid($uid, $clientId)
	{
		self::$clients[$uid] = $clientId;
	}

	static function bind($client_id){
		self::$client[$client_id] = 1;
	}

	/**
	*	根据UID获得客户端
	*/
	static function getClientIdByUid($uid)
	{
		if(self::$clients && isset(self::$clients[$uid]))
			return self::$clients[$uid];
		else
			return;
	}

	/**
	* 	根据ClientId获得Uid
	*/

	/**
	* 解绑客户端
	*/
	static function unBindUid($uid, $clientId)
	{
		unset(self::$clients[$uid]);
	}

	static function unBind($client_id){
		unset(self::$client[$client_id]);
	}

	/**
	*	是否在线
	*/
	static function isUidOnline($uid){
		return isset(self::$clients[$uid]) ? true : false;
	}

	static function isOnline($client_id){
		return isset(self::$client[$client_id]) ? true : false;
	}

	/**
	*	发送信息到指定客户端
	* 	@param uid 接收方的UID
	* 	@param msg 信息内容
	* 	@param uniqId   唯一id
	* 	@param time 时间戳
	* 	@param type 发送类型
	* 	@param tid 消息类型
	* 	@param fastIndex 可选
	*/
	static function sendToUid($uid, $msg, $uniqId, $time, $type, $tid, $fastIndex=''){
		if($cls = Gateway::getClientIdByUid($uid)){
			#获得真正Client
			$trueClient = end($cls);
			#记录到消息发送检测
			self::$msgPostCheck[$uniqId] = ['msg'=>$msg, 'time'=>$time, 'type'=>$type, 'toUid'=>$uid, 'tid'=>$tid, 'fastIndex'=>$fastIndex];
			echo"\nstu -> {$type}\n";
			$msg['postCheckUniqId'] = $uniqId;
			Gateway::sendToClient($trueClient, json_encode($msg, true));
		}
	}

	/**
	*	前台反馈已收到
	*/
	static function receiveConfirmOk($uniqId){
		#print_r(self::$msgPostCheck);
		unset(self::$msgPostCheck[$uniqId]);
		#print_r(self::$msgPostCheck);
	}

}