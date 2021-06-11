<?php

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\OfficialAccount\Contracts\Account as AccountInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AccessToken implements \EasyWeChat\OfficialAccount\Contracts\AccessToken
{
    public function __construct(
        protected AccountInterface $account,
        protected HttpClientInterface $client,
        protected CacheInterface $cache,
        protected ?string $key = null,
    ) {
    }

    public function getKey(): string
    {
        return $this->key ?? $this->key = \sprintf('official_account.access_token.%s', $this->account->getAppId());
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken(): string
    {
        $key = $this->getKey();

        if ($token = $this->cache->get($key)) {
            return $token;
        }

        $response = $this->client->request(
            'GET',
            '/cgi-bin/token',
            [
                'query' => [
                    'grant_type' => 'client_credential',
                    'appid' => $this->account->getAppId(),
                    'secret' => $this->account->getSecret(),
                ],
            ]
        )->toArray();

        if (empty($response['access_token'])) {
            throw new HttpException('Failed to get access_token.');
        }

        $this->cache->set($key, $response['access_token'], \abs($response['expires_in'] - 100));

        return $response['access_token'];
    }
}
