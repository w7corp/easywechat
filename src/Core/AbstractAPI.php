<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * AbstractAPI.php.
 *
 * This file is part of the wechat-components.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace EasyWeChat\Core;

use EasyWeChat\Core\Exceptions\HttpException;
use EasyWeChat\Support\Collection;
use EasyWeChat\Support\Log;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AbstractAPI.
 */
abstract class AbstractAPI
{
    /**
     * Http instance.
     *
     * @var \EasyWeChat\Core\Http
     */
    protected $http;

    /**
     * The reqeust token.
     *
     * @var \EasyWeChat\Core\AccessToken
     */
    protected $accessToken;

    const GET = 'get';
    const POST = 'post';
    const JSON = 'json';

    /**
     * Constructor.
     *
     * @param \EasyWeChat\Core\AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        $this->setAccessToken($accessToken);

        $this->registerHttpMiddleware();
    }

    /**
     * Return the http instance.
     *
     * @return \EasyWeChat\Core\Http
     */
    public function getHttp()
    {
        return $this->http ?: $this->http = new Http();
    }

    /**
     * Set the http instance.
     *
     * @param \EasyWeChat\Core\Http $http
     *
     * @return $this
     */
    public function setHttp(Http $http)
    {
        $this->http = $http;

        return $this;
    }

    /**
     * Return the current accessToken.
     *
     * @return \EasyWeChat\Core\AccessToken
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set the request token.
     *
     * @param \EasyWeChat\Core\AccessToken $accessToken
     *
     * @return $this
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Return API url, and add some parameters.
     *
     * @param string $url
     * @param array  $quires
     *
     * @return string
     */
    public function getAPI($url, array $quires = [])
    {
        return $url;
    }

    /**
     * Parse JSON from response and check error.
     *
     * @param string $method
     * @param array  $args
     * @param bool   $throws
     *
     * @return \EasyWeChat\Support\Collection
     *
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function parseJSON($method, $args, $throws = true)
    {
        $http = $this->getHttp();

        $args = (array) $args;

        // Add access token
        $args['0'] = $this->getAPI($args[0]);

        $contents = $http->parseJSON(call_user_func_array([$http, $method], $args));

        $this->checkAndThrow($contents);

        return new Collection($contents);
    }

    /**
     * Set request access_token query.
     */
    protected function registerHttpMiddleware()
    {
        // access token
        $this->getHttp()->addMiddleware($this->accessTokenMiddleware());
        // log
        $this->getHttp()->addMiddleware($this->logMiddleware());
        // retry
        $this->getHttp()->addMiddleware($this->retryMiddleware());
    }

    /**
     * Attache access token to request query.
     *
     * @return Closure
     */
    public function accessTokenMiddleware()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if (!$this->accessToken) {
                    return $handler($request, $options);
                }

                $field = $this->accessToken->getQueryName();
                $token = $this->accessToken->getToken();

                $request = $request->withUri(Uri::withQueryValue($request->getUri(), $field, $token));

                return $handler($request, $options);
            };
        };
    }

    /**
     * Log the request.
     *
     * @return \GuzzleHttp\Middleware
     */
    public function logMiddleware()
    {
        return Middleware::tap(function (RequestInterface $request) {
            Log::debug("Request: {$request->getMethod()} {$request->getUri()}");
            Log::debug("Request Body: {$request->getBody()}");
        });
    }

    /**
     * Return retry middleware.
     *
     * @return \GuzzleHttp\RetryMiddleware
     */
    protected function retryMiddleware()
    {
        return Middleware::retry(function (
                                          $retries,
                                          RequestInterface $request,
                                          ResponseInterface $response = null,
                                          RequestException $exception = null
                                       ) {
            // Limit the number of retries to 2
            if ($retries <= 2 && $response && $body = $response->getBody()) {
                // Retry on server errors
                if (stripos($body, 'errcode') && (stripos($body, '40001') || stripos($body, '42001'))) {
                    $field = $this->accessToken->getQueryName();
                    $token = $this->accessToken->getToken();

                    $request = $request->withUri($newUri = Uri::withQueryValue($request->getUri(), $field, $token));

                    Log::debug("Retry with Request Token: {$token}");
                    Log::debug("Retry with Request Uri: {$newUri}");

                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Check the array data erros, and Throw expcetion when the contents cotnains error.
     *
     * @param array $contents
     *
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    protected function checkAndThrow(array $contents)
    {
        if (isset($contents['errcode']) && 0 !== $contents['errcode']) {
            if (empty($contents['errmsg'])) {
                $contents['errmsg'] = 'Unknown';
            }

            throw new HttpException($contents['errmsg'], $contents['errcode']);
        }
    }
}
