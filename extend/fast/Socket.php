<?php

namespace fast;

use Workerman\Connection\AsyncTcpConnection;

/**
 *    连接远程socket服务
 * @author LR <rui6ye@163.com>
 */
class Socket
{
    protected $socket = null;
    protected $socketUrl = 'ws://socket.li6.cc/wss';
    protected function _connectSocketAndSend($data)
    {
        if(is_array($data)){
            $data = json_encode($data);
        }
        require_once ROOT_PATH . 'vendor/websocket/vendor/autoload.php';
        $client = new \WebSocket\Client($this->socketUrl); //实例化
        $client->send($data); //发送数据
        $result=$client->receive(); //接收数据
        $client->close();//关闭连接
    }
    public function sendImgUrls($urls){
        if(is_array($urls)){
            $urls = json_encode($urls);
        }
        $pcUserKey = 'pc';
        $this->_connectSocketAndSend([
            'hisUserKey'     => $pcUserKey,
            'cmd' => 'notify',
            'data'  => $urls,
        ]);
    }
}
