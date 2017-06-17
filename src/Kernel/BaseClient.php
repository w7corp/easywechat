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

use EasyWeChat\Contracts\AccessToken;
use EasyWeChat\Support\Collection;
use EasyWeChat\Support\HasHttpRequests;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\Promise;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class BaseClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class BaseClient
{
    use HasHttpRequests { request as doRequest; }

    /**
     * @var \Pimple\Container
     */
    protected $app;

    /**
     * @var \EasyWeChat\Contracts\AccessToken
     */
    protected $accessToken;

    /**
     * BaseClient constructor.
     *
     * @param \Pimple\Container                      $app
     * @param \EasyWeChat\Contracts\AccessToken|null $accessToken
     */
    public function __construct(Container $app, AccessToken $accessToken = null)
    {
        $this->app = $app;
        $this->accessToken = $accessToken;
        $this->registerHttpMiddlewares();
    }

    /**
     * GET request.
     *
     * @param string $url
     * @param array  $query
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get(string $url, array $query = [])
    {
        return $this->request($url, 'GET', ['query' => $query]);
    }

    /**
     * POST request.
     *
     * @param string       $url
     * @param array|string $data
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post(string $url, array $data = [])
    {
        $key = is_array($data) ? 'form_params' : 'body';

        return $this->request($url, 'POST', [$key => $data]);
    }

    /**
     * JSON request.
     *
     * @param string       $url
     * @param string|array $data
     * @param array        $query
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function postJson(string $url, array $data = [], array $query = [])
    {
        return $this->request($url, 'POST', ['query' => $query, 'json' => $data]);
    }

    /**
     * Upload file.
     *
     * @param string $url
     * @param array  $files
     * @param array  $form
     * @param array  $query
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function upload(string $url, array $files = [], array $form = [], array $query = [])
    {
        $multipart = [];

        foreach ($files as $name => $path) {
            $multipart[] = [
                'name' => $name,
                'contents' => fopen($path, 'r'),
            ];
        }

        foreach ($form as $name => $contents) {
            $multipart[] = compact('name', 'contents');
        }

        return $this->request($url, 'POST', ['query' => $query, 'multipart' => $multipart]);
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param \EasyWeChat\Contracts\AccessToken $accessToken
     *
     * @return $this
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $options
     * @param bool   $returnRaw
     *
     * @return \EasyWeChat\Support\Collection|array|object|string
     */
    public function request(string $url, string $method = 'GET', array $options = [], $returnRaw = false)
    {
        $response = call_user_func_array([$this, 'doRequest'], func_get_args());

        return $returnRaw ? $response : $this->resolveResponse($response);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \EasyWeChat\Support\Collection|array|object|string
     */
    protected function resolveResponse(ResponseInterface $response)
    {
        $body = $response->getBody()->getContents();

        switch ($type = $this->app->config->get('response_type', 'array')) {
            case 'collection':
                return new Collection(json_decode($body, true));
            case 'array':
                return json_decode($body, true);
            case 'object':
                return json_decode($body);
            case 'raw':
            default:
                if (class_exists($type)) {
                    return new $type($response);
                }
                return $body;
        }
    }

    /**
     * Register Guzzle middlewares.
     */
    protected function registerHttpMiddlewares()
    {
        // log
        $this->pushMiddleware($this->logMiddleware(), 'log');
        // retry
        $this->pushMiddleware($this->retryMiddleware(), 'retry');
        // access token
        $this->pushMiddleware($this->accessTokenMiddleware(), 'access_token');
    }

    /**
     * Attache access token to request query.
     *
     * @return \Closure
     */
    protected function accessTokenMiddleware()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if ($this->accessToken) {
                    $request = $request->withUri($uri = $request->getUri()->withQuery($this->accessToken->getQuery()));
                }

                return $handler($request, $options);
            };
        };
    }

    /**
     * Log the request.
     *
     * @return \Closure
     */
    protected function logMiddleware()
    {
        return Middleware::tap(function (RequestInterface $request, $options) {
            Log::debug('Request', [
                    'method' => $request->getMethod(),
                    'headers' => $request->getHeaders(),
                    'uri' => $request->getUri(),
                    'options' => $options,
                ]);
        }, function (RequestInterface $request, array $options, Promise $responsePromise) {
            $responsePromise->then(function (ResponseInterface $response) use ($request, $options) {
                Log::debug('Response', [
                    'method' => $request->getMethod(),
                    'uri' => $request->getUri(),
                    'status' => $response->getStatusCode(),
                    'reason_phrase' => $response->getReasonPhrase(),
                    'headers' => $response->getHeaders(),
                    'body' => $response->getBody(),
                ]);
            });
        });
    }

    /**
     * Return retry middleware.
     *
     * @return \Closure
     */
    protected function retryMiddleware()
    {
        return Middleware::retry(function (
            $retries,
            RequestInterface $request,
            ?ResponseInterface $response = null
        ) {
            // Limit the number of retries to 2
            if ($retries < $this->app->config->get('http_retries', 1) && $response && $body = $response->getBody()) {
                // Retry on server errors
                $response = json_decode($body, true);
                if (!empty($response['errcode']) && in_array($response['errcode'], ['40001', '42001'])) {
                    $this->accessToken->refresh(true);

                    $request = $request->withUri($uri = $request->getUri()->withQuery($this->accessToken->getQuery()));

                    Log::debug('Retrying', compact('token', 'uri'));

                    return true;
                }
            }

            return false;
        }, function ($retries) {
            return $retries * abs($this->app->config->get('http_retry_delay', 0));
        });
    }
}
