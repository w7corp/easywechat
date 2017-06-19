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
     * Parse Response XML to array.
     *
     * @param \Psr\Http\Message\ResponseInterface|string $response
     *
     * @return \EasyWeChat\Support\Collection
     */
    protected function parseResponse($response)
    {
        if ($response instanceof ResponseInterface) {
            $response = $response->getBody();
        }

        return new Collection((array) XML::parse($response));
    }
}
