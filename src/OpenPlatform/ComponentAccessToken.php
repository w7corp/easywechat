<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\OpenPlatform\Contracts\VerifyTicket as VerifyTicketInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ComponentAccessToken implements AccessTokenInterface
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
        $this->httpClient = $httpClient ?? new HttpClient();
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

        $response = $this->httpClient->request(
            'POST',
            'cgi-bin/component/api_component_token',
            [
                'query' => [
                    'component_appid' => $this->appId,
                    'component_appsecret' => $this->secret,
                    'component_verify_ticket' => $this->verifyTicket->getTicket(),
                ],
            ]
        )->toArray();

        if (empty($response['component_access_token'])) {
            throw new HttpException('Failed to get component_access_token.');
        }

        $this->cache->set($key, $response['component_access_token'], \abs(\intval($response['expires_in']) - 100));

        return $response['component_access_token'];
    }
}
