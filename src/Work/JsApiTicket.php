<?php

declare(strict_types=1);

namespace EasyWeChat\Work;

use EasyWeChat\Kernel\Exceptions\HttpException;
use function intval;
use function is_string;
use JetBrains\PhpStorm\ArrayShape;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use function sprintf;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JsApiTicket
{
    protected HttpClientInterface $httpClient;

    protected CacheInterface $cache;

    public function __construct(
        protected string $corpId,
        protected ?string $key = null,
        ?CacheInterface $cache = null,
        ?HttpClientInterface $httpClient = null
    ) {
        $this->httpClient = $httpClient ?? HttpClient::create(['base_uri' => 'https://qyapi.weixin.qq.com/']);
        $this->cache = $cache ?? new Psr16Cache(new FilesystemAdapter(namespace: 'easywechat', defaultLifetime: 1500));
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws HttpException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    #[ArrayShape([
        'url' => 'string',
        'nonceStr' => 'string',
        'timestamp' => 'int',
        'appId' => 'string',
        'signature' => 'string',
    ])]
    public function createConfigSignature(string $url, string $nonce, int $timestamp): array
    {
        return [
            'appId' => $this->corpId,
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'url' => $url,
            'signature' => $this->getTicketSignature($this->getTicket(), $nonce, $timestamp, $url),
        ];
    }

    public function getTicketSignature(string $ticket, string $nonce, int $timestamp, string $url): string
    {
        return sha1(sprintf('jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s', $ticket, $nonce, $timestamp, $url));
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws TransportExceptionInterface
     * @throws HttpException
     * @throws ServerExceptionInterface
     */
    public function getTicket(): string
    {
        $key = $this->getKey();
        $ticket = $this->cache->get($key);

        if ((bool) $ticket && is_string($ticket)) {
            return $ticket;
        }

        $response = $this->httpClient->request('GET', '/cgi-bin/get_jsapi_ticket')->toArray(false);

        if (empty($response['ticket'])) {
            throw new HttpException('Failed to get jssdk ticket: '.json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        $this->cache->set($key, $response['ticket'], intval($response['expires_in']));

        return $response['ticket'];
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function getKey(): string
    {
        return $this->key ?? $this->key = sprintf('work.jsapi_ticket.%s', $this->corpId);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws TransportExceptionInterface
     * @throws HttpException
     * @throws ServerExceptionInterface
     */
    #[ArrayShape([
        'corpid' => 'string',
        'agentid' => 'int',
        'nonceStr' => 'string',
        'timestamp' => 'int',
        'url' => 'string',
        'signature' => 'string',
    ])]
    public function createAgentConfigSignature(int $agentId, string $url, string $nonce, int $timestamp): array
    {
        return [
            'corpid' => $this->corpId,
            'agentid' => $agentId,
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'url' => $url,
            'signature' => $this->getTicketSignature($this->getAgentTicket($agentId), $nonce, $timestamp, $url),
        ];
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
    public function getAgentTicket(int $agentId): string
    {
        $key = $this->getAgentKey($agentId);
        $ticket = $this->cache->get($key);

        if ((bool) $ticket && is_string($ticket)) {
            return $ticket;
        }

        $response = $this->httpClient->request('GET', '/cgi-bin/ticket/get', ['query' => ['type' => 'agent_config']])
            ->toArray(false);

        if (empty($response['ticket'])) {
            throw new HttpException('Failed to get jssdk agentTicket: '.json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        $this->cache->set($key, $response['ticket'], intval($response['expires_in']));

        return $response['ticket'];
    }

    public function getAgentKey(int $agentId): string
    {
        return sprintf('%s.%s', $this->getKey(), $agentId);
    }
}
