<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\Refund;

use EasyWeChat\Payment\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Refund by out trade number.
     *
     * @param string $number
     * @param string $refundNumber
     * @param int    $totalFee
     * @param int    $refundFee
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function byOutTradeNumber(string $number, string $refundNumber, int $totalFee, int $refundFee, array $options = [])
    {
        return $this->refund($refundNumber, $totalFee, $refundFee, array_merge($options, ['out_trade_no' => $number]));
    }

    /**
     * Refund by transaction id.
     *
     * @param string $transactionId
     * @param string $refundNumber
     * @param int    $totalFee
     * @param int    $refundFee
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function byTransactionId(string $transactionId, string $refundNumber, int $totalFee, int $refundFee, array $options = [])
    {
        return $this->refund($refundNumber, $totalFee, $refundFee, array_merge($options, ['transaction_id' => $transactionId]));
    }

    /**
     * Query refund by transaction id.
     *
     * @param string $transactionId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function queryByTransactionId(string $transactionId)
    {
        return $this->query($transactionId, 'transaction_id');
    }

    /**
     * Query refund by out trade number.
     *
     * @param string $outTradeNumber
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function queryByOutTradeNumber(string $outTradeNumber)
    {
        return $this->query($outTradeNumber, 'out_trade_no');
    }

    /**
     * Query refund by out refund number.
     *
     * @param string $outRefundNumber
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function queryByOutRefundNumber(string $outRefundNumber)
    {
        return $this->query($outRefundNumber, 'out_refund_no');
    }

    /**
     * Query refund by refund id.
     *
     * @param string $refundId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function queryByRefundId(string $refundId)
    {
        return $this->query($refundId, 'refund_id');
    }

    /**
     * Refund.
     *
     * @param string $refundNumber
     * @param int    $totalFee
     * @param int    $refundFee
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    protected function refund(string $refundNumber, int $totalFee, int $refundFee, $options = [])
    {
        $params = array_merge([
            'out_refund_no' => $refundNumber,
            'total_fee' => $totalFee,
            'refund_fee' => $refundFee,
        ], $options);

        return $this->safeRequest('secapi/pay/refund', $params);
    }

    /**
     * Query refund.
     *
     * @param string $number
     * @param string $type
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    protected function query(string $number, string $type)
    {
        $params = [
            $type => $number,
        ];

        return $this->request('pay/refundquery', $params);
    }
}
