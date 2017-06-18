<?php


namespace EasyWeChat\Kernel;

use EasyWeChat\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Exceptions\HttpException;
use EasyWeChat\Support\HasHttpRequests;
use EasyWeChat\Support\InteractsWithCache;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;


/**
 * Class AccessToken
 *
 * @author overtrue <i@overtrue.me>
 */
abstract class AccessToken implements AccessTokenInterface
{
    use HasHttpRequests, InteractsWithCache;

    /**
     * @var \EasyWeChat\Applications\WeWork\Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $token;

    /**
     * @var string
     */
    protected $cachePrefix = 'easywechat.access_token.';

    /**
     * AccessToken constructor.
     *
     * @param \Pimple\Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @param bool $refresh
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function getToken($refresh = false): array
    {
        $cacheKey = $this->getCacheKey();
        $cache = $this->getCache();

        if (!$refresh && $cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $token = $this->requestToken($this->getCredential());

        $this->setToken($token['access_token'], $token['expires_in'] ?? 7200);

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
            'access_token' => $token,
            'expires_in' => $lifetime
        ], $lifetime - 500);

        return $this;
    }

    /**
     * @return \EasyWeChat\Contracts\AccessToken
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
     * @throws \EasyWeChat\Exceptions\HttpException
     */
    public function requestToken(array $credentials): array
    {
        $this->setHttpClient($this->app['http_client']);

        $token = $this->request($this->endpointToGetToken, 'GET', [
            'query' => $credentials,
        ]);

        $token = json_decode($token->getBody()->getContents(), true);

        if (empty($token['access_token'])) {
            throw new HttpException('Request AccessToken fail: '. json_encode($token, JSON_UNESCAPED_UNICODE));
        }

        return $token;
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
        return $this->cachePrefix.md5(json_encode($this->getCredential()));
    }

    /**
     * The request query will be used to add to the request.
     *
     * @return array
     */
    protected function getQuery(): array
    {
        return ['access_token' => $this->getToken()['access_token']];
    }

    /**
     * Credential for get token.
     *
     * @return array
     */
    abstract protected function getCredential(): array;
}