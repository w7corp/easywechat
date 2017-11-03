<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\Bill;

use EasyWeChat\Kernel\Http\StreamResponse;
use EasyWeChat\Payment\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Download bill history as a table file.
     *
     * @param string $date
     * @param string $type
     *
     * @return \EasyWeChat\Kernel\Http\StreamResponse|\Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function get(string $date, string $type = 'ALL', array $optional = [])
    {
        $params = [
            'bill_date' => $date,
            'bill_type' => $type,
        ] + $optional;

        $response = $this->requestRaw($this->wrap('pay/downloadbill'), $params);

        if (strpos($response->getBody()->getContents(), '<xml>') === 0) {
            return $this->resolveResponse($response, $this->app['config']->get('response_type', 'array'));
        }

        return StreamResponse::buildFromPsrResponse($response);
    }
}
