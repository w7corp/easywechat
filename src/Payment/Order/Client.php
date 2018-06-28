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
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function unify(array $params)
    {
        if (empty($params['spbill_create_ip'])) {
            $params['spbill_create_ip'] = ('NATIVE' === $params['trade_type']) ? Support\get_server_ip() : Support\get_client_ip();
        }

        $params['appid'] = $this->app['config']->app_id;
        $params['notify_url'] = $params['notify_url'] ?? $this->app['config']['notify_url'];

        return $this->request($this->wrap('pay/unifiedorder'), $params);
    }

    /**
     * Query order by out trade number.
     *
     * @param string $number
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
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
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
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
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    protected function query(array $params)
    {
        $params['appid'] = $this->app['config']->app_id;

        return $this->request($this->wrap('pay/orderquery'), $params);
    }

    /**
     * Close order by out_trade_no.
     *
     * @param string $tradeNo
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function close(string $tradeNo)
    {
        $params = [
            'appid' => $this->app['config']->app_id,
            'out_trade_no' => $tradeNo,
        ];

        return $this->request($this->wrap('pay/closeorder'), $params);
    }
}
