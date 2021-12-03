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
        $key = $this->getAgentKey();

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
            'appId' => $this->corpId,
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'url' => $url,
            'signature' => $this->getTicketSignature($this->getTicket(), $nonce, $timestamp, $url),
        ];
    }


    public function agentConfigSignature(string $url = null, string $nonce = null, $timestamp = null): array
    {
        return [
            'appId' => $this->corpId,
            'agentid' => $this->agentId,
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'url' => $url,
            'signature' => $this->getTicketSignature($this->getAgentTicket(), $nonce, $timestamp, $url),
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


    /**
     * Sign the params.
     *
     * @param string $ticket
     * @param string $nonce
     * @param int    $timestamp
     * @param string $url
     */
    public function getTicketSignature($ticket, $nonce, $timestamp, $url): string
    {
        return sha1(sprintf('jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s', $ticket, $nonce, $timestamp, $url));
    }


}
