<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use function abs;
use EasyWeChat\Kernel\Contracts\RefreshableAccessToken as RefreshableAccessTokenInterface;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\OpenWork\Contracts\SuiteTicket as SuiteTicketInterface;
use function intval;
use JetBrains\PhpStorm\ArrayShape;
use function json_encode;
use const JSON_UNESCAPED_UNICODE;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SuiteAccessToken implements RefreshableAccessTokenInterface
{
    protected HttpClientInterface $httpClient;

    protected CacheInterface $cache;

    public function __construct(
        protected string $suiteId,
        protected string $suiteSecret,
        protected ?SuiteTicketInterface $suiteTicket = null,
        protected ?string $key = null,
        ?CacheInterface $cache = null,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClient::create(['base_uri' => 'https://qyapi.weixin.qq.com/']);
        $this->cache = $cache ?? new Psr16Cache(new FilesystemAdapter(namespace: 'easywechat', defaultLifetime: 1500));
        $this->suiteTicket ??= new SuiteTicket($this->suiteId, $this->cache);
    }

    public function getKey(): string
    {
        return $this->key ?? $this->key = \sprintf('open_work.suite_access_token.%s.%s', $this->suiteId, $this->suiteSecret);
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
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface|\Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken(): string
    {
        $token = $this->cache->get($this->getKey());

        if ((bool) $token && \is_string($token)) {
            return $token;
        }

        return $this->refresh();
    }

    /**
     * @return array<string, string>
     *
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    #[ArrayShape(['suite_access_token' => 'string'])]
    public function toQuery(): array
    {
        return ['suite_access_token' => $this->getToken()];
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function refresh(): string
    {
        $response = $this->httpClient->request('POST', 'cgi-bin/service/get_suite_token', [
            'json' => [
                'suite_id' => $this->suiteId,
                'suite_secret' => $this->suiteSecret,
                'suite_ticket' => $this->suiteTicket?->getTicket(),
            ],
        ])->toArray(false);

        if (empty($response['suite_access_token'])) {
            throw new HttpException('Failed to get suite_access_token: '.json_encode(
                $response,
                JSON_UNESCAPED_UNICODE
            ));
        }

        $this->cache->set(
            $this->getKey(),
            $response['suite_access_token'],
            abs(intval($response['expires_in']) - 100)
        );

        return $response['suite_access_token'];
    }
}
