<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\Exceptions\HttpException;
use JetBrains\PhpStorm\ArrayShape;
use function sprintf;

class JsApiTicket extends AccessToken
{
    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function getTicket(): string
    {
        $key = $this->getKey();
        $ticket = $this->cache->get($key);

        if ((bool) $ticket && \is_string($ticket)) {
            return $ticket;
        }

        $response = $this->httpClient->request('GET', '/cgi-bin/ticket/getticket', ['query' => ['type' => 'jsapi']])
            ->toArray(false);

        if (empty($response['ticket'])) {
            throw new HttpException('Failed to get jssdk ticket: '.\json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        $this->cache->set($key, $response['ticket'], \intval($response['expires_in']));

        return $response['ticket'];
    }

    /**
     * @return array<string,mixed>
     *
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    #[ArrayShape([
        'url' => 'string',
        'nonceStr' => 'string',
        'timestamp' => 'int',
        'appId' => 'string',
        'signature' => 'string',
    ])]
    public function configSignature(string $url, string $nonce, int $timestamp): array
    {
        return [
            'url' => $url,
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'appId' => $this->appId,
            'signature' => sha1(sprintf(
                'jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s',
                $this->getTicket(),
                $nonce,
                $timestamp,
                $url
            )),
        ];
    }

    public function getKey(): string
    {
        return $this->key ?? $this->key = sprintf('official_account.jsapi_ticket.%s', $this->appId);
    }
}
