<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\Payment;

use EasyWeChat\Support;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;

abstract class BaseClient
{
    use Support\HasHttpRequests { request as httpRequest; }

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
    abstract protected function extra(): array;

    /**
     * Make a API request.
     *
     * @param string $api
     * @param array  $params
     * @param string $method
     * @param array  $options
     * @param bool   $returnResponse
     *
     * @return \EasyWeChat\Support\Collection|\Psr\Http\Message\ResponseInterface
     */
    protected function request($api, array $params, $method = 'post', array $options = [], $returnResponse = false)
    {
        $params = array_merge($this->extra(), $params);
        $params['nonce_str'] = uniqid();
        $params = array_filter($params);

        $key = method_exists($this, 'getSignKey') ? $this->getSignKey($api) : $this->app['merchant']->key;
        $params['sign'] = Support\generate_sign($params, $key, 'md5');

        $options = array_merge([
            'body' => Support\XML::build($params),
        ], $options);

        $response = $this->httpRequest($api, $method, $options);

        return $returnResponse ? $response : $this->resolveResponse($response);
    }

    /**
     * Request with SSL.
     *
     * @param string $api
     * @param array  $params
     * @param string $method
     *
     * @return \EasyWeChat\Support\Collection
     */
    protected function safeRequest($api, array $params, $method = 'post')
    {
        $options = [
            'cert' => $this->app['merchant']->get('cert_path'),
            'ssl_key' => $this->app['merchant']->get('key_path'),
        ];

        return $this->request($api, $params, $method, $options);
    }

    /**
     * Resolve Response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return mixed
     */
    protected function resolveResponse(ResponseInterface $response)
    {
        switch ($type = $this->app['config']->get('response_type', 'array')) {
            case 'collection':
                return new Support\Collection(
                    (array) Support\XML::parse($response->getBody())
                );
            case 'array':
                return (array) Support\XML::parse($response->getBody());
            case 'object':
                return (object) Support\XML::parse($response->getBody());
            case 'raw':
            default:
                $response->getBody()->rewind();
                if (class_exists($type)) {
                    return new $type($response);
                }

                return (string) $response->getBody();
        }
    }
}
