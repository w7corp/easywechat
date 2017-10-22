<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\Kernel;

use EasyWeChat\Kernel\Support;
use EasyWeChat\Kernel\Traits\HasHttpRequests;
use EasyWeChat\Payment\Application;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\ResponseInterface;

/**
 * Class BaseClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class BaseClient
{
    use HasHttpRequests { request as performRequest; }

    /**
     * @var \EasyWeChat\Payment\Application
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param \EasyWeChat\Payment\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        if ($this->app->inSandbox()) {
            $config = $this->app['http_client']->getConfig();
            $config['base_uri'] = new Uri($this->app['config']->get('http.base_uri').'/sandboxnew/');

            $client = new Client($config);
        }

        $this->setHttpClient($client ?? $this->app['http_client']);
    }

    /**
     * Extra request params.
     *
     * @return array
     */
    protected function prepends()
    {
        return [];
    }

    /**
     * Make a API request.
     *
     * @param string $endpoint
     * @param array  $params
     * @param string $method
     * @param array  $options
     * @param bool   $returnResponse
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    protected function request(string $endpoint, array $params = [], $method = 'post', array $options = [], $returnResponse = false)
    {
        $base = [
            'appid' => $this->app['config']['app_id'],
            'mch_id' => $this->app['config']['mch_id'],
            'nonce_str' => uniqid(),
        ];

        $params = array_filter(array_merge($base, $this->prepends(), $params));

        if ($this->app->inSandbox() && !$this->app['sandbox']->except($endpoint)) {
            $key = $this->app['sandbox']->key();
        }

        $params['sign'] = Support\generate_sign($params, $key ?? $this->app['config']->key);

        $options = array_merge([
            'body' => Support\XML::build($params),
        ], $options);

        $response = $this->performRequest($endpoint, $method, $options);

        return $returnResponse ? $response : $this->resolveResponse($response, $this->app->config->get('response_type'));
    }

    /**
     * Make a request and return raw response.
     *
     * @param string $endpoint
     * @param array  $params
     * @param string $method
     * @param array  $options
     *
     * @return array|Support\Collection|object|ResponseInterface|string
     */
    protected function requestRaw($endpoint, array $params = [], $method = 'post', array $options = [])
    {
        return $this->request($endpoint, $params, $method, $options, true);
    }

    /**
     * Request with SSL.
     *
     * @param string $endpoint
     * @param array  $params
     * @param string $method
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    protected function safeRequest($endpoint, array $params, $method = 'post')
    {
        $options = [
            'cert' => $this->app['config']->get('cert_path'),
            'ssl_key' => $this->app['config']->get('key_path'),
        ];

        return $this->request($endpoint, $params, $method, $options);
    }
}
