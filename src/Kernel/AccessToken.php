<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Contracts\AccessTokenInterface;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Traits\HasHttpRequests;
use EasyWeChat\Kernel\Traits\InteractsWithCache;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AccessToken implements AccessTokenInterface
{
    use HasHttpRequests;
    use InteractsWithCache;

    protected string $requestMethod = 'GET';
    protected string $endpointToGetToken;
    protected string $queryName;
    protected array $token;
    protected string $tokenKey = 'access_token';
    protected string $cachePrefix = 'easywechat.kernel.access_token.';

    public function __construct(
        protected ServiceContainer $app
    ) {
    }

    public function getRefreshedToken(): array
    {
        return $this->getToken(true);
    }

    public function getToken(bool $refresh = false): array
    {
        $cacheKey = $this->getCacheKey();
        $cache = $this->getCache();

        if (!$refresh && $cache->has($cacheKey) && $result = $cache->get($cacheKey)) {
            return $result;
        }

        /** @var array $token */
        $token = $this->requestToken($this->getCredentials(), true);

        $this->setToken($token[$this->tokenKey], $token['expires_in'] ?? 7200);

        $this->app->events->dispatch(new Events\AccessTokenRefreshed($this));

        return $token;
    }

    public function setToken(string $token, int $lifetime = 7200): static
    {
        $this->getCache()->set(
            $this->getCacheKey(),
            [
                $this->tokenKey => $token,
                'expires_in' => $lifetime,
            ],
            $lifetime
        );

        if (!$this->getCache()->has($this->getCacheKey())) {
            throw new RuntimeException('Failed to cache access token.');
        }

        return $this;
    }

    public function refresh(): static
    {
        $this->getToken(true);

        return $this;
    }

    public function requestToken(array $credentials, $toArray = false)
    {
        $response = $this->sendRequest($credentials);
        $result = json_decode($response->getBody()->getContents(), true);
        $formatted = $this->castResponseToType($response, $this->app['config']->get('response_type'));

        if (empty($result[$this->tokenKey])) {
            throw new HttpException('Request access_token fail: '.json_encode($result, JSON_UNESCAPED_UNICODE), $response);
        }

        return $toArray ? $result : $formatted;
    }

    public function applyToRequest(RequestInterface $request, array $requestOptions = []): RequestInterface
    {
        parse_str($request->getUri()->getQuery(), $query);

        $query = http_build_query(array_merge($this->getQuery(), $query));

        return $request->withUri($request->getUri()->withQuery($query));
    }

    protected function sendRequest(array $credentials): ResponseInterface
    {
        $options = [
            ('GET' === $this->requestMethod) ? 'query' : 'json' => $credentials,
        ];

        return $this->setHttpClient($this->app['http_client'])->request($this->getEndpoint(), $this->requestMethod, $options);
    }

    protected function getCacheKey(): string
    {
        return $this->cachePrefix.md5(json_encode($this->getCredentials()));
    }

    protected function getQuery(): array
    {
        return [$this->queryName ?? $this->tokenKey => $this->getToken()[$this->tokenKey]];
    }

    public function getEndpoint(): string
    {
        if (empty($this->endpointToGetToken)) {
            throw new InvalidArgumentException('No endpoint for access token request.');
        }

        return $this->endpointToGetToken;
    }

    public function getTokenKey(): string
    {
        return $this->tokenKey;
    }

    abstract protected function getCredentials(): array;
}
