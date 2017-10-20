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
     * @return \Psr\Http\Message\StreamInterface
     */
    public function download($date, $type = 'ALL')
    {
        $params = [
            'bill_date' => $date,
            'bill_type' => $type,
        ];

        $response = $this->requestRaw('pay/downloadbill', $params);

        if (strpos($response->getHeaderLine('Content-Type'), 'text') === false) {
            return StreamResponse::buildFromPsrResponse($response);
        }

        return $this->resolveResponse($response, $this->app['config']->get('response_type', 'array'));
    }

    /**
     * @return array
     */
    protected function prepends()
    {
        return [
            'appid' => $this->app['config']['app_id'],
            'mch_id' => $this->app['config']['mch_id'],
        ];
    }
}
