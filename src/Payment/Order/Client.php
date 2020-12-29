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

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Support;
use EasyWeChat\Kernel\Support\Collection;
use EasyWeChat\Payment\Kernel\BaseClient;
use Psr\Http\Message\ResponseInterface;

class Client extends BaseClient
{
    /**
     * Unify order.
     *
     * @param bool $isContract
     *
     * @return ResponseInterface|Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unify(array $params, $isContract = false)
    {
        if (empty($params['spbill_create_ip'])) {
            $params['spbill_create_ip'] = ('NATIVE' === $params['trade_type']) ? Support\get_server_ip() : Support\get_client_ip();
        }

        $params['appid'] = $this->app['config']->app_id;
        $params['notify_url'] = $params['notify_url'] ?? $this->app['config']['notify_url'];

        if ($isContract) {
            $params['contract_appid'] = $this->app['config']['app_id'];
            $params['contract_mchid'] = $this->app['config']['mch_id'];
            $params['request_serial'] = $params['request_serial'] ?? time();
            $params['contract_notify_url'] = $params['contract_notify_url'] ?? $this->app['config']['contract_notify_url'];

            return $this->request($this->wrap('pay/contractorder'), $params);
        }

        return $this->request($this->wrap('pay/unifiedorder'), $params);
    }

    /**
     * Query order by out trade number.
     *
     * @return ResponseInterface|Collection|array|object|string
     *
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
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
     * @return ResponseInterface|Collection|array|object|string
     *
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public function queryByTransactionId(string $transactionId)
    {
        return $this->query([
            'transaction_id' => $transactionId,
        ]);
    }

    /**
     * @return ResponseInterface|Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function query(array $params)
    {
        $params['appid'] = $this->app['config']->app_id;

        return $this->request($this->wrap('pay/orderquery'), $params);
    }

    /**
     * Close order by out_trade_no.
     *
     * @return ResponseInterface|Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
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
