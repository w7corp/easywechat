<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\RefreshableAccessToken;
use EasyWeChat\Kernel\Exceptions\HttpException;
use JetBrains\PhpStorm\ArrayShape;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Stringable;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AuthorizerAccessToken implements RefreshableAccessToken, Stringable
{
    protected HttpClientInterface $httpClient;

    protected CacheInterface $cache;

    public function __construct(
        protected string $corpId,
        protected string $permanentCodeOrAccessToken,
        protected ?AccessTokenInterface $suiteAccessToken = null,
        protected ?string $key = null,
        ?CacheInterface $cache = null,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClient::create(['base_uri' => 'https://qyapi.weixin.qq.com/']);
        $this->cache = $cache ?? new Psr16Cache(new FilesystemAdapter(namespace: 'easywechat', defaultLifetime: 1500));
    }

    public function getCorpId(): string
    {
        return $this->corpId;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws HttpException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getToken(): string
    {
        if (! isset($this->suiteAccessToken)) {
            return $this->permanentCodeOrAccessToken;
        }

        $token = $this->cache->get($this->getKey());

        if ($token && is_string($token)) {
            return $token;
        }

        return $this->refresh();
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws HttpException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function __toString()
    {
        return $this->getToken();
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws HttpException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    #[ArrayShape(['access_token' => 'string'])]
    public function toQuery(): array
    {
        return ['access_token' => $this->getToken()];
    }

    public function getKey(): string
    {
        return $this->key ?? $this->key = sprintf('open_work.authorizer.access_token.%s.%s', $this->corpId, $this->permanentCodeOrAccessToken);
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @throws HttpException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     */
    public function refresh(): string
    {
        if (! isset($this->suiteAccessToken)) {
            return '';
        }

        $response = $this->httpClient->request('POST', 'cgi-bin/service/get_corp_token', [
            'query' => [
                'suite_access_token' => $this->suiteAccessToken->getToken(),
            ],
            'json' => [
                'auth_corpid' => $this->corpId,
                'permanent_code' => $this->permanentCodeOrAccessToken,
            ],
        ])->toArray(false);

        if (empty($response['access_token'])) {
            throw new HttpException('Failed to get access_token: '.json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        $this->cache->set($this->getKey(), $response['access_token'], intval($response['expires_in']));

        return $response['access_token'];
    }
}
