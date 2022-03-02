<?php

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\Support\Str;

class Utils
{
    public function __construct(protected Application $app)
    {
    }

    /**
     * @param  string  $url
     * @param  array<string>  $jsApiList
     * @param  array<string>  $openTagList
     * @param  bool  $debug
     *
     * @return array<string, mixed>
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function buildJsSdkConfig(string $url, array $jsApiList = [], array $openTagList = [], bool $debug = false): array
    {
        return array_merge(
            compact('jsApiList', 'openTagList', 'debug'),
            $this->app->getTicket()->configSignature($url, Str::random(), \time())
        );
    }
}
