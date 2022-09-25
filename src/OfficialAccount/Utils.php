<?php

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use function time;

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
     * @return array<string, mixed>
     *
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
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
