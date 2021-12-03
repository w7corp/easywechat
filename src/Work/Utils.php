<?php

namespace EasyWeChat\Work;

class Utils
{
    public function __construct(protected Application $app)
    {
    }

    public function buildJsSdkConfig(string $url, array $jsApiList, array $openTagList = [], bool $debug = false, bool $beta = false, )
    {
        return array_merge(
            compact('jsApiList', 'openTagList', 'debug', 'beta'),
            $this->app->getTicket()->configSignature($url, \uniqid(), \time())
        );
    }
    public function buildJsSdkAgentConfig(string $url, array $jsApiList, array $openTagList = [], bool $debug = false, bool $beta = false, )
    {
        return array_merge(
            compact('jsApiList', 'openTagList', 'debug', 'beta'),
            $this->app->getTicket()->agentConfigSignature($url, \uniqid(), \time())
        );
    }
}
