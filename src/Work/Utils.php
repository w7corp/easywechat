<?php

namespace EasyWeChat\Work;

use EasyWeChat\Kernel\Support\Str;

use function time;

class Utils
{
    public function __construct(protected Application $app)
    {
    }

    /**
     * @param  array<string>  $jsApiList
     * @param  array<string>  $openTagList
     * @return array<string, mixed>
     */
    public function buildJsSdkConfig(
        string $url,
        array $jsApiList,
        array $openTagList = [],
        bool $debug = false,
        bool $beta = true,
    ): array {
        return array_merge(
            compact('jsApiList', 'openTagList', 'debug', 'beta'),
            $this->app->getTicket()->createConfigSignature($url, Str::random(), time())
        );
    }

    /**
     * @param  array<string>  $jsApiList
     * @param  array<string>  $openTagList
     * @return array<string, mixed>
     */
    public function buildJsSdkAgentConfig(
        int $agentId,
        string $url,
        array $jsApiList,
        array $openTagList = [],
        bool $debug = false
    ): array {
        return array_merge(
            compact('jsApiList', 'openTagList', 'debug'),
            $this->app->getTicket()->createAgentConfigSignature($agentId, $url, Str::random(), time())
        );
    }
}
