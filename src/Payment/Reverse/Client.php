<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\Reverse;

use EasyWeChat\Payment\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Reverse order by out trade number.
     *
     * @param string $outTradeNumber
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\ArrayException
     * @throws \Safe\Exceptions\JsonException
     * @throws \Safe\Exceptions\PcreException
     * @throws \Safe\Exceptions\SimplexmlException
     * @throws \Safe\Exceptions\StringsException
     */
    public function byOutTradeNumber(string $outTradeNumber)
    {
        return $this->reverse($outTradeNumber, 'out_trade_no');
    }

    /**
     * Reverse order by transaction_id.
     *
     * @param string $transactionId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\ArrayException
     * @throws \Safe\Exceptions\JsonException
     * @throws \Safe\Exceptions\PcreException
     * @throws \Safe\Exceptions\SimplexmlException
     * @throws \Safe\Exceptions\StringsException
     */
    public function byTransactionId(string $transactionId)
    {
        return $this->reverse($transactionId, 'transaction_id');
    }

    /**
     * Reverse order.
     *
     * @param string $number
     * @param string $type
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\ArrayException
     * @throws \Safe\Exceptions\JsonException
     * @throws \Safe\Exceptions\PcreException
     * @throws \Safe\Exceptions\SimplexmlException
     * @throws \Safe\Exceptions\StringsException
     */
    protected function reverse(string $number, string $type)
    {
        $params = [
            'appid' => $this->app['config']->app_id,
            $type => $number,
        ];

        return $this->safeRequest($this->wrap('secapi/pay/reverse'), $params);
    }
}
