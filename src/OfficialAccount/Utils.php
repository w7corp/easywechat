<?php

namespace EasyWeChat\OfficialAccount;

class Utils
{
    public function __construct(protected JSApiTicket $ticket)
    {
    }

    public function buildJsSdkConfig(string $url, array $jsApiList = [], array $openTagList = [], $debug = false, $json = false)
    {
        $nonceStr = uniqid();
        $timestamp = time();

        $config = array_merge(
            compact('jsApiList', 'openTagList', 'debug'),
            $this->ticket->configSignature($url, $nonceStr, $timestamp)
        );

        return $json ? json_encode($config) : $config;
    }
}
