<?php

declare(strict_types=1);

namespace EasyWeChat\Work;

use EasyWeChat\Kernel\Exceptions\HttpException;
use JetBrains\PhpStorm\ArrayShape;

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

        if ($ticket = $this->cache->get($key)) {
            return $ticket;
        }

        $response = $this->httpClient->request('GET', '/cgi-bin/get_jsapi_ticket')
                                     ->toArray();

        if (empty($response['ticket'])) {
            throw new HttpException('Failed to get jssdk ticket.');
        }

        $this->cache->set($key, $response['ticket'], \intval($response['expires_in']));

        return $response['ticket'];
    }


    public function getAgentTicket(): string
    {
        $key = $this->getKey();

        if ($ticket = $this->cache->get($key)) {
            return $ticket;
        }

        $response = $this->httpClient->request('GET', '/cgi-bin/ticket/get', ['query' => ['type' => 'agent_config']])
            ->toArray();

        if (empty($response['ticket'])) {
            throw new HttpException('Failed to get jssdk agentTicket.');
        }

        $this->cache->set($key, $response['ticket'], \intval($response['expires_in']));

        return $response['ticket'];
    }

    #[ArrayShape([
        'url' => "string",
        'nonceStr' => "string",
        'timestamp' => "int",
        'appId' => "string",
        'signature' => "string"
    ])]
    public function configSignature(string $url, string $nonce, int $timestamp): array
    {
        return [
            'url' => $url,
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'appId' => $this->corpId,
            'signature' => sha1(sprintf('jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s', $this->getTicket(), $nonce, $timestamp, $url)),
        ];
    }

    public function getKey(): string
    {
        return $this->key ?? $this->key = \sprintf('work.jsapi_ticket.%s', $this->corpId);
    }

    public function getAgentKey(): string
    {
        return $this->key ?? $this->key = \sprintf('work.jsapi_ticket.%s.%s', $this->corpId,$this->agentId);
    }



}
