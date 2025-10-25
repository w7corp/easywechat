<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\Contracts\RefreshableAccessToken as RefreshableAccessTokenInterface;
use EasyWeChat\Kernel\Exceptions\HttpException;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function intval;
use function is_string;
use function json_encode;
use function sprintf;

class AccessToken implements RefreshableAccessTokenInterface
{
    protected HttpClientInterface $httpClient;

    protected CacheInterface $cache;

    const CACHE_KEY_PREFIX = 'official_account';

    public function __construct(
        protected string $appId,
        protected string $secret,
        protected ?string $key = null,
        ?CacheInterface $cache = null,
        ?HttpClientInterface $httpClient = null,
        protected ?bool $stable = false
    ) {
        $this->httpClient = $httpClient ?? HttpClient::create(['base_uri' => 'https://api.weixin.qq.com/']);
        $this->cache = $cache ?? new Psr16Cache(new FilesystemAdapter(namespace: 'easywechat', defaultLifetime: 1500));
    }

    public function getKey(): string
    {
        return $this->key ?? $this->key = sprintf('%s.access_token.%s.%s.%s', static::CACHE_KEY_PREFIX, $this->appId, $this->secret, (int) $this->stable);
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function getToken(): string
    {
        $token = $this->cache->get($this->getKey());

        if ($token && is_string($token)) {
            return $token;
        }

        return $this->refresh();
    }

    /**
     * @return array{access_token:string}
     */
    #[\JetBrains\PhpStorm\ArrayShape(['access_token' => 'string'])]
    public function toQuery(): array
    {
        return ['access_token' => $this->getToken()];
    }

    public function refresh(): string
    {
        return $this->stable ? $this->getStableAccessToken() : $this->getAccessToken();
    }

    /**
     * @throws HttpException
     */
    public function getStableAccessToken(bool $force_refresh = false): string
    {
        $response = $this->httpClient->request(
            'POST',
            'https://api.weixin.qq.com/cgi-bin/stable_token',
            [
                'json' => [
                    'grant_type' => 'client_credential',
                    'appid' => $this->appId,
                    'secret' => $this->secret,
                    'force_refresh' => $force_refresh,
                ],
            ]
        )->toArray(false);

        if (empty($response['access_token'])) {
            throw new HttpException('Failed to get stable access_token: '.json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        $this->cache->set($this->getKey(), $response['access_token'], intval($response['expires_in']));

        return $response['access_token'];
    }

    /**
     * @throws HttpException
     */
    public function getAccessToken(): string
    {
        $response = $this->httpClient->request(
            'GET',
            'cgi-bin/token',
            [
                'query' => [
                    'grant_type' => 'client_credential',
                    'appid' => $this->appId,
                    'secret' => $this->secret,
                ],
            ]
        )->toArray(false);

        if (empty($response['access_token'])) {
            throw new HttpException('Failed to get access_token: '.json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        $this->cache->set($this->getKey(), $response['access_token'], intval($response['expires_in']));

        return $response['access_token'];
    }
}
