<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\Order;

use EasyWeChat\Kernel\Support;
use EasyWeChat\Payment\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Unify order.
     *
     * @param array $attributes
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function unify(array $attributes)
    {
        if (empty($attributes['spbill_create_ip'])) {
            $attributes['spbill_create_ip'] = ('NATIVE' === $attributes['trade_type']) ? Support\get_server_ip() : Support\get_client_ip();
        }

        $attributes['notify_url'] = $attributes['notify_url'] ?? $this->app['config']['notify_url'];

        return $this->request($this->wrap('pay/unifiedorder'), $attributes);
    }

    /**
     * Query order by out trade number.
     *
     * @param string $number
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function queryByOutTradeNumber(string $number)
    {
        return $this->query([
            'out_trade_no' => $number,
        ]);
    }

    /**
     * Query order by transaction id.
     *
     * @param string $transactionId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function queryByTransactionId(string $transactionId)
    {
        return $this->query([
            'transaction_id' => $transactionId,
        ]);
    }

    /**
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    protected function query(array $params)
    {
        return $this->request($this->wrap('pay/orderquery'), $params);
    }

    /**
     * Close order by out_trade_no.
     *
     * @param string $tradeNo
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function close(string $tradeNo)
    {
        $params = [
            'out_trade_no' => $tradeNo,
        ];

        return $this->request($this->wrap('pay/closeorder'), $params);
    }
}
