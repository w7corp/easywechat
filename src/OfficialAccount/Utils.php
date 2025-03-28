<?php

namespace EasyWeChat\OfficialAccount;

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
        array $jsApiList = [],
        array $openTagList = [],
        bool $debug = false
    ): array {
        return array_merge(
            compact('jsApiList', 'openTagList', 'debug'),
            $this->app->getTicket()->configSignature($url, Str::random(), time())
        );
    }
}
