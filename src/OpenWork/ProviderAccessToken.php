<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use const JSON_UNESCAPED_UNICODE;

use EasyWeChat\Kernel\Contracts\RefreshableAccessToken as RefreshableAccessTokenInterface;
use EasyWeChat\Kernel\Exceptions\HttpException;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function intval;

class ProviderAccessToken implements RefreshableAccessTokenInterface
{
    protected HttpClientInterface $httpClient;

    protected CacheInterface $cache;

    public function __construct(
        protected string $corpId,
        protected string $providerSecret,
        protected ?string $key = null,
        ?CacheInterface $cache = null,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClient::create(['base_uri' => 'https://qyapi.weixin.qq.com/']);
        $this->cache = $cache ?? new Psr16Cache(new FilesystemAdapter(namespace: 'easywechat', defaultLifetime: 1500));
    }

    public function getKey(): string
    {
        return $this->key ?? $this->key = \sprintf('open_work.access_token.%s.%s', $this->corpId, $this->providerSecret);
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function getToken(): string
    {
        $token = $this->cache->get($this->getKey());

        if ($token && \is_string($token)) {
            return $token;
        }

        return $this->refresh();
    }

    /**
     * @return array<string, string>
     */
    #[\JetBrains\PhpStorm\ArrayShape(['provider_access_token' => 'string'])]
    public function toQuery(): array
    {
        return ['provider_access_token' => $this->getToken()];
    }

    /**
     * @throws HttpException
     */
    public function refresh(): string
    {
        $response = $this->httpClient->request('POST', 'cgi-bin/service/get_provider_token', [
            'json' => [
                'corpid' => $this->corpId,
                'provider_secret' => $this->providerSecret,
            ],
        ])->toArray(false);

        if (empty($response['provider_access_token'])) {
            throw new HttpException('Failed to get provider_access_token: '.\json_encode(
                $response,
                JSON_UNESCAPED_UNICODE
            ));
        }

        $this->cache->set($this->getKey(), $response['provider_access_token'], intval($response['expires_in']));

        return $response['provider_access_token'];
    }
}
