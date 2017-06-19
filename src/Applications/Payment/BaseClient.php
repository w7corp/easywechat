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

use EasyWeChat\Support\Collection;
use EasyWeChat\Support\XML;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;

class BaseClient
{
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
                return new Collection((array) XML::parse($response->getBody()));
            case 'array':
                return (array) XML::parse($response->getBody());
            case 'object':
                return (object) XML::parse($response->getBody());
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
