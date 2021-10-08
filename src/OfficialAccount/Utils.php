<?php

namespace EasyWeChat\OfficialAccount;

class Utils
{
    public function __construct(protected Application $app)
    {
    }

    public function buildJsSdkConfig(string $url, array $jsApiList = [], array $openTagList = [], $debug = false)
    {
        $nonceStr = uniqid();
        $timestamp = time();

        return array_merge(
            compact('jsApiList', 'openTagList', 'debug'),
            $this->app->getTicket()->configSignature($url, $nonceStr, $timestamp)
        );
    }
}
