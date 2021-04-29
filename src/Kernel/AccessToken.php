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

    /**
     * @var string
     */
    protected string $endpointToGetToken;

    /**
     * @var string
     */
    protected string $queryName;

    /**
     * @var array
     */
    protected array $token;

    /**
     * @var string
     */
    protected string $requestMethod = 'GET';

    /**
     * @var string
     */
    protected string $tokenKey = 'access_token';

    /**
     * @var string
     */
    protected string $cachePrefix = 'easywechat.kernel.access_token.';

    /**
     * AccessToken constructor.
     *
     * @param \EasyWeChat\Kernel\ServiceContainer $app
     */
    public function __construct(
        protected ServiceContainer $app
    ) {
    }

    /**
     * @return array
     */
    abstract protected function getCredentials(): array;

    /**
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */
    public function getRefreshedToken(): array
    {
        return $this->getToken(true);
    }

    /**
     * @param bool $refresh
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */
    public function getToken(bool $refresh = false): array
    {
        $cacheKey = $this->getCacheKey();
        $cache = $this->getCache();

        if (!$refresh && $result = $cache->get($cacheKey)) {
            return $result;
        }

        /** @var array $token */
        $token = $this->requestToken($this->getCredentials(), true);

        $this->setToken($token[$this->tokenKey], $token['expires_in'] ?? 7200);

        $this->app->events->dispatch(new Events\AccessTokenRefreshed($this));

        return $token;
    }

    /**
     * @param string $token
     * @param int    $lifetime
     *
     * @return $this
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */
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

        throw_if(
            !$this->getCache()->has($this->getCacheKey()),
            RuntimeException::class,
            'Failed to cache access token.'
        );

        return $this;
    }

    /**
     * @return $this
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */
    public function refresh(): static
    {
        $this->getToken(true);

        return $this;
    }

    /**
     * @param array $credentials
     * @param false $toArray
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Throwable
     */
    public function requestToken(array $credentials, $toArray = false): mixed
    {
        $response = $this->sendRequest($credentials);

        $result = json_decode($response->getBody()->getContents(), true);

        throw_if(
            !($result[$this->tokenKey] ?? null),
            HttpException::class,
            'Request access_token fail: '.json_encode($result, JSON_UNESCAPED_UNICODE),
            $response
        );

        if ($toArray) {
            return $result;
        }

        return $this->castResponseToType($response, $this->app['config']->get('response_type'));
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array                              $requestOptions
     *
     * @return \Psr\Http\Message\RequestInterface
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */
    public function applyToRequest(
        RequestInterface $request,
        array $requestOptions = []
    ): RequestInterface {
        parse_str($request->getUri()->getQuery(), $query);

        $query = http_build_query(
            array_merge($this->getQuery(), $query)
        );

        return $request->withUri($request->getUri()->withQuery($query));
    }

    /**
     * @param array $credentials
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \Throwable
     */
    protected function sendRequest(array $credentials): ResponseInterface
    {
        $options = [
            ('GET' === $this->requestMethod) ? 'query' : 'json' => $credentials,
        ];

        return $this->setHttpClient(
            $this->app['http_client'])->request($this->getEndpoint(),
            $this->requestMethod,
            $options
        );
    }

    /**
     * @return string
     */
    protected function getCacheKey(): string
    {
        return $this->cachePrefix.md5(json_encode($this->getCredentials()));
    }

    /**
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */
    protected function getQuery(): array
    {
        return [
            $this->queryName ?? $this->tokenKey => $this->getToken()[$this->tokenKey]
        ];
    }

    /**
     * @return string
     *
     * @throws \Throwable
     */
    public function getEndpoint(): string
    {
        throw_if(
            !($this->endpointToGetToken ?? null),
            InvalidArgumentException::class,
            'No endpoint for access token request.'
        );

        return $this->endpointToGetToken;
    }

    /**
     * @return string
     */
    public function getTokenKey(): string
    {
        return $this->tokenKey;
    }
}
