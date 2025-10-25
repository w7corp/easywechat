<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Contracts\RefreshableAccessToken as RefreshableAccessTokenInterface;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\OpenPlatform\Contracts\VerifyTicket as VerifyTicketInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function abs;
use function intval;
use function json_encode;

class ComponentAccessToken implements RefreshableAccessTokenInterface
{
    protected HttpClientInterface $httpClient;

    protected CacheInterface $cache;

    public function __construct(
        protected string $appId,
        protected string $secret,
        protected VerifyTicketInterface $verifyTicket,
        protected ?string $key = null,
        ?CacheInterface $cache = null,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClient::create(['base_uri' => 'https://api.weixin.qq.com/']);
        $this->cache = $cache ?? new Psr16Cache(new FilesystemAdapter(namespace: 'easywechat', defaultLifetime: 1500));
    }

    public function getKey(): string
    {
        return $this->key ?? $this->key = \sprintf('open_platform.component_access_token.%s', $this->appId);
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

    #[\JetBrains\PhpStorm\ArrayShape(['component_access_token' => 'string'])]
    public function toQuery(): array
    {
        return ['component_access_token' => $this->getToken()];
    }

    /**
     * @throws HttpException
     */
    public function refresh(): string
    {
        $response = $this->httpClient->request(
            'POST',
            'cgi-bin/component/api_component_token',
            [
                'json' => [
                    'component_appid' => $this->appId,
                    'component_appsecret' => $this->secret,
                    'component_verify_ticket' => $this->verifyTicket->getTicket(),
                ],
            ]
        )->toArray(false);

        if (empty($response['component_access_token'])) {
            throw new HttpException('Failed to get component_access_token: '.json_encode(
                $response,
                JSON_UNESCAPED_UNICODE
            ));
        }

        $this->cache->set(
            $this->getKey(),
            $response['component_access_token'],
            abs(intval($response['expires_in']) - 100)
        );

        return $response['component_access_token'];
    }
}
