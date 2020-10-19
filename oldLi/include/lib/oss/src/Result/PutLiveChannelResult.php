<?php
require_once realpath(dirname(__DIR__)) . '/Result//Result.php';
require_once realpath(dirname(__DIR__)) . '/Model/LiveChannelInfo.php';


class PutLiveChannelResult extends Result
{
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $channel = new LiveChannelInfo();
        $channel->parseFromXml($content);
        return $channel;
    }
}
