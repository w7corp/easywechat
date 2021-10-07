<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\Exceptions\HttpException;

class JSApiTicket extends AccessToken
{
    public function getTicket(): string
    {
        $key = $this->getKey();

        if ($ticket = $this->cache->get($key)) {
            return $ticket;
        }

        $response = $this->httpClient->request('GET', '/cgi-bin/ticket/getticket', ['query' => ['type' => 'jsapi']])
                                     ->toArray();

        if (empty($response['ticket'])) {
            throw new HttpException('Failed to get jssdk ticket.');
        }

        $this->cache->set($key, $response['ticket'], \intval($response['expires_in']));

        return $response['ticket'];
    }

    public function configSignature(string $url, string $nonce, int $timestamp): array
    {
        return [
            'url'       => $url,
            'nonceStr'  => $nonce,
            'timestamp' => $timestamp,
            'appId'     => $this->appId,
            'signature' => sha1(sprintf('jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s', $this->getTicket(), $nonce, $timestamp, $url)),
        ];
    }

    public function getKey(): string
    {
        return $this->key ?? $this->key = \sprintf('official_account.jsapi_ticket.%s', $this->appId);
    }
}
