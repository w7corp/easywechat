<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\Traits\HasAttributes;
use EasyWeChat\Kernel\Traits\HasHttpRequests;
use EasyWeChat\Kernel\Traits\InteractsWithCache;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Class AuthorizerAccessToken.
 *
 * @author overtrue <i@overtrue.me>
 */
abstract class AccessToken implements AccessTokenInterface
{
    use HasHttpRequests, HasAttributes, InteractsWithCache { getCache as getCacheInstance; }

    /**
     * @var string
     */
    protected $requestMethod = 'GET';

    /**
     * @var \Pimple\Container
     */
    protected $app;

    /**
     * @var array
     */
    protected $token;

    /**
     * @var int
     */
    protected $safeSeconds = 500;

    /**
     * @var string
     */
    protected $tokenKey = 'access_token';

    /**
     * @var string
     */
    protected $cachePrefix = 'easywechat.access_token.';

    /**
     * AuthorizerAccessToken constructor.
     *
     * @param \Pimple\Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @return \Psr\SimpleCache\CacheInterface
     */
    public function getCache()
    {
        if (!$this->cache && isset($this->app['cache']) && $this->app['cache'] instanceof CacheInterface) {
            $this->setCache($this->app['cache']);
        }

        return $this->getCacheInstance();
    }

    /**
     * @param bool $refresh
     *
     * @return array
     */
    public function getToken(bool $refresh = false): array
    {
        $cacheKey = $this->getCacheKey();
        $cache = $this->getCache();

        if (!$refresh && $cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $token = $this->requestToken($this->getCredentials());

        $this->setToken($token[$this->tokenKey], $token['expires_in'] ?? 7200);

        return $token;
    }

    /**
     * @param string $token
     * @param int    $lifetime
     *
     * @return \EasyWeChat\Kernel\AccessToken
     */
    public function setToken(string $token, int $lifetime = 7200): AccessToken
    {
        $this->getCache()->set($this->getCacheKey(), [
            $this->tokenKey => $token,
            'expires_in' => $lifetime,
        ], $lifetime - $this->safeSeconds);

        return $this;
    }

    /**
     * @return \EasyWeChat\Kernel\Contracts\AccessToken
     */
    public function refresh(): AccessTokenInterface
    {
        $this->getToken(true);

        return $this;
    }

    /**
     * @param array $credentials
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     */
    public function requestToken(array $credentials): array
    {
        $result = json_decode($this->sendRequest($credentials)->getBody()->getContents(), true);

        if (empty($result[$this->tokenKey])) {
            throw new HttpException('Request AuthorizerAccessToken fail: '.json_encode($result, JSON_UNESCAPED_UNICODE));
        }

        return $result;
    }

    /**
     * Send http request.
     *
     * @param array $credentials
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function sendRequest(array $credentials)
    {
        $options = [
            ($this->requestMethod === 'GET') ? 'query' : 'json' => $credentials,
        ];
        if (method_exists($this, 'appendQuery')) {
            $options['query'] = $this->appendQuery();
        }

        return $this->setHttpClient($this->app['http_client'])->request($this->endpointToGetToken, $this->requestMethod, $options);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array                              $requestOptions
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function applyToRequest(RequestInterface $request, array $requestOptions = []): RequestInterface
    {
        parse_str($request->getUri()->getQuery(), $query);
        $query = http_build_query(array_merge($this->getQuery(), $query));

        return $request->withUri($request->getUri()->withQuery($query));
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return $this->cachePrefix.md5(json_encode($this->getCredentials()));
    }

    /**
     * The request query will be used to add to the request.
     *
     * @return array
     */
    protected function getQuery(): array
    {
        return [$this->queryName ?? $this->tokenKey => $this->getToken()[$this->tokenKey]];
    }

    /**
     * Credential for get token.
     *
     * @return array
     */
    abstract protected function getCredentials(): array;
}
