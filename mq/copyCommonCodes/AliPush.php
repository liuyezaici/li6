<?php

/**
 * 阿里云推送
 */
class AliPush
{
    const PUSH_TYPE_MESSAGE = 'MESSAGE';

    const PUSH_TYPE_NOTICE = 'NOTICE';

    /**
     * 发送批量推送给批量安卓设备
     * @param array $noticeList
     * @example $noticeList = [
     *     [
     *         'deviceIdList' => ['123456789', '987654321', ...],
     *         'title'        => 'title',
     *         'body'         => 'body',
     *         'extParams'    => [],
     *     ],
     *     ...
     * ];
     * @return bool
     */
    private static function notify($pushType, $noticeList = [])
    {
        $pushData = [];
        // 每次发送的最多设备数量
        $maxSendDeviceNumAtATime = CommonCfg::get('alipushRules.maxSendDeviceNumAtATime') ?? 1000;
        foreach ($noticeList as $li) {
            $deviceIdList = $li['deviceIdList'] ?? [];
            $title        = $li['title'] ?? '';
            $body         = $li['body'] ?? '';
            $extParams    = $li['extParams'] ?? [];
            if (!$deviceIdList || !$title || !$body) {
                continue;
            }
            $deviceIdListGroup = Str::splitArrayBynum($li['deviceIdList'], $maxSendDeviceNumAtATime);
            foreach ($deviceIdListGroup as $deviceIdList) {
                $pushData[] = [
                    'pushType'        => $pushType,
                    'deviceIdListStr' => implode(',', $deviceIdList),
                    'title'           => $title,
                    'body'            => $body,
                    'extParams'       => $extParams,
                ];
            }
        }
        if (!$pushData) {
            return false;
        }
        RedisMQ::add(CommonCfg::get('mqRules.aliPushName'), 'data', json_encode($pushData));
        return true;
    }

    public static function sendMassMessageToMassAndroidDevice($noticeList = [])
    {
        return self::notify(self::PUSH_TYPE_MESSAGE, $noticeList);
    }

    public static function sendMassNoticeToMassAndroidDevice($noticeList = [])
    {
        return self::notify(self::PUSH_TYPE_NOTICE, $noticeList);
    }
}