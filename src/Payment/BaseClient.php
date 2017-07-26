<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment;

use EasyWeChat\Kernel\Support;
use EasyWeChat\Kernel\Traits\HasHttpRequests;
use Pimple\Container;
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
     * @var \Pimple\Container
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param \Pimple\Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;

        $this->setHttpClient($this->app['http_client']);
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
     * @param string $api
     * @param array  $params
     * @param string $method
     * @param array  $options
     * @param bool   $returnResponse
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    protected function request($api, array $params, $method = 'post', array $options = [], $returnResponse = false)
    {
        $params = array_merge($this->prepends(), $params);
        $params['nonce_str'] = uniqid();
        $params = array_filter($params);

        $key = method_exists($this, 'getSignKey') ? $this->getSignKey($api) : $this->app['merchant']->key;
        $params['sign'] = Support\generate_sign($params, $key, 'md5');

        $options = array_merge([
            'body' => Support\XML::build($params),
        ], $options);

        $response = $this->performRequest($api, $method, $options);

        return $returnResponse ? $response : $this->resolveResponse($response, $this->app->config->get('response_type', 'array'));
    }

    /**
     * Make a request and return raw response.
     *
     * @param string $api
     * @param array  $params
     * @param string $method
     * @param array  $options
     *
     * @return array|Support\Collection|object|ResponseInterface|string
     */
    protected function requestRaw($api, array $params, $method = 'post', array $options = [])
    {
        return $this->request($api, $params, $method, $options, true);
    }

    /**
     * Request with SSL.
     *
     * @param string $api
     * @param array  $params
     * @param string $method
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    protected function safeRequest($api, array $params, $method = 'post')
    {
        $options = [
            'cert' => $this->app['merchant']->get('cert_path'),
            'ssl_key' => $this->app['merchant']->get('key_path'),
        ];

        return $this->request($api, $params, $method, $options);
    }
}
