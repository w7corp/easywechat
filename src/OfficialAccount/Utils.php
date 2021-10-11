<?php

namespace EasyWeChat\OfficialAccount;

class Utils
{
    public function __construct(protected Application $app)
    {
    }

    public function buildJsSdkConfig(string $url, array $jsApiList = [], array $openTagList = [], $debug = false): array
    {
        return array_merge(
            compact('jsApiList', 'openTagList', 'debug'),
            $this->app->getTicket()->configSignature($url, \uniqid(), \time())
        );
    }
}
