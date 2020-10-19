<?php


require_once realpath(dirname(__DIR__)) . '/Model/LiveChannelListInfo.php';

class ListLiveChannelResult extends Result
{
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $channelList = new LiveChannelListInfo();
        $channelList->parseFromXml($content);
        return $channelList;
    }
}
