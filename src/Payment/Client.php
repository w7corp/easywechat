<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment;

use EasyWeChat\Kernel\Support;
use EasyWeChat\Payment\Traits\HandleNotify;
use EasyWeChat\Payment\Traits\JssdkHelpers;
use EasyWeChat\Payment\Traits\WorksInSandbox;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    use WorksInSandbox, JssdkHelpers, HandleNotify;

    // order id types.
    const TRANSACTION_ID = 'transaction_id';
    const OUT_TRADE_NO = 'out_trade_no';
    const OUT_REFUND_NO = 'out_refund_no';
    const REFUND_ID = 'refund_id';

    // bill types.
    const BILL_TYPE_ALL = 'ALL';
    const BILL_TYPE_SUCCESS = 'SUCCESS';
    const BILL_TYPE_REFUND = 'REFUND';
    const BILL_TYPE_REVOKED = 'REVOKED';

    /**
     * Build payment scheme for product.
     *
     * @param string $productId
     *
     * @return string
     */
    public function scheme($productId)
    {
        $params = [
            'appid' => $this->app['merchant']->app_id,
            'mch_id' => $this->app['merchant']->merchant_id,
            'time_stamp' => time(),
            'nonce_str' => uniqid(),
            'product_id' => $productId,
        ];

        $params['sign'] = Support\generate_sign($params, $this->app['merchant']->key, 'md5');

        return 'weixin://wxpay/bizpayurl?'.http_build_query($params);
    }

    /**
     * Pay the order.
     *
     * @param Order $order
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function pay(Order $order)
    {
        return $this->request($this->wrapApi('pay/micropay'), $order->all());
    }

    /**
     * Prepare order to pay.
     *
     * @param Order $order
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function prepare(Order $order)
    {
        $order->notify_url = $order->get('notify_url', $this->app['merchant']->notify_url);

        if (is_null($order->spbill_create_ip)) {
            $order->spbill_create_ip = ($order->trade_type === Order::NATIVE) ? Support\get_server_ip() : Support\get_client_ip();
        }

        return $this->request($this->wrapApi('pay/unifiedorder'), $order->all());
    }

    /**
     * Query order.
     *
     * @param string $orderNo
     * @param string $type
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function query($orderNo, $type = self::OUT_TRADE_NO)
    {
        $params = [
            $type => $orderNo,
        ];

        return $this->request($this->wrapApi('pay/orderquery'), $params);
    }

    /**
     * Query order by transaction_id.
     *
     * @param string $transactionId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function queryByTransactionId($transactionId)
    {
        return $this->query($transactionId, self::TRANSACTION_ID);
    }

    /**
     * Close order by out_trade_no.
     *
     * @param $tradeNo
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function close($tradeNo)
    {
        $params = [
            'out_trade_no' => $tradeNo,
        ];

        return $this->request($this->wrapApi('pay/closeorder'), $params);
    }

    /**
     * Reverse order.
     *
     * @param string $orderNo
     * @param string $type
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function reverse($orderNo, $type = self::OUT_TRADE_NO)
    {
        $params = [
            $type => $orderNo,
        ];

        return $this->safeRequest($this->wrapApi('secapi/pay/reverse'), $params);
    }

    /**
     * Reverse order by transaction_id.
     *
     * @param int $transactionId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function reverseByTransactionId($transactionId)
    {
        return $this->reverse($transactionId, self::TRANSACTION_ID);
    }

    /**
     * Make a refund request.
     *
     * @param string $orderNo
     * @param string $refundNo
     * @param float  $totalFee
     * @param array  $optional
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function refund($orderNo, $refundNo, $totalFee, $optional = [])
    {
        $params = array_merge([
            self::TRANSACTION_ID => $orderNo,
            'out_refund_no' => $refundNo,
            'total_fee' => $totalFee,
            'refund_fee' => $totalFee,
            'refund_fee_type' => $this->app['merchant']->fee_type,
            'refund_account' => 'REFUND_SOURCE_UNSETTLED_FUNDS',
            'op_user_id' => $this->app['merchant']->merchant_id,
        ], $optional);

        return $this->safeRequest($this->wrapApi('secapi/pay/refund'), $params);
    }

    /**
     * Refund by transaction id.
     *
     * @param string $orderNo
     * @param string $refundNo
     * @param float  $totalFee
     * @param array  $optional
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function refundByTransactionId($orderNo, $refundNo, $totalFee, $optional = [])
    {
        return $this->refund($orderNo, $refundNo, $totalFee, $optional);
    }

    /**
     * Query refund status.
     *
     * @param string $orderNo
     * @param string $type
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function queryRefund($orderNo, $type = self::OUT_TRADE_NO)
    {
        $params = [
            $type => $orderNo,
        ];

        return $this->request($this->wrapApi('pay/refundquery'), $params);
    }

    /**
     * Query refund status by out_refund_no.
     *
     * @param string $refundNo
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function queryRefundByRefundNo($refundNo)
    {
        return $this->queryRefund($refundNo, self::OUT_REFUND_NO);
    }

    /**
     * Query refund status by transaction_id.
     *
     * @param string $transactionId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function queryRefundByTransactionId($transactionId)
    {
        return $this->queryRefund($transactionId, self::TRANSACTION_ID);
    }

    /**
     * Query refund status by refund_id.
     *
     * @param string $refundId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function queryRefundByRefundId($refundId)
    {
        return $this->queryRefund($refundId, self::REFUND_ID);
    }

    /**
     * Download bill history as a table file.
     *
     * @param string $date
     * @param string $type
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function downloadBill($date, $type = self::BILL_TYPE_ALL)
    {
        $params = [
            'bill_date' => $date,
            'bill_type' => $type,
        ];

        return $this->request($this->wrapApi('pay/downloadbill'), $params, 'post', [\GuzzleHttp\RequestOptions::STREAM => true], true)->getBody();
    }

    /**
     * Report API status to WeChat.
     *
     * @param string $api
     * @param int    $timeConsuming
     * @param string $resultCode
     * @param string $returnCode
     * @param array  $optional      ex: err_code, err_code_des, out_trade_no, user_ip...
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function report($api, $timeConsuming, $resultCode, $returnCode, array $optional = [])
    {
        $params = array_merge([
            'interface_url' => $api,
            'execute_time_' => $timeConsuming,
            'return_code' => $returnCode,
            'return_msg' => null,
            'result_code' => $resultCode,
            'user_ip' => Support\get_client_ip(),
            'time' => time(),
        ], $optional);

        return $this->request($this->wrapApi('payitil/report'), $params);
    }

    /**
     * Get openid by auth code.
     *
     * @param string $authCode
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function authCodeToOpenId($authCode)
    {
        return $this->request('https://api.mch.weixin.qq.com/tools/authcodetoopenid', ['auth_code' => $authCode]);
    }

    /**
     * {@inheritdoc}.
     */
    protected function prepends(): array
    {
        return array_merge($this->app['merchant']->only(['sub_appid', 'sub_mch_id']),
            [
                'appid' => $this->app['merchant']->app_id,
                'mch_id' => $this->app['merchant']->merchant_id,
                'device_info' => $this->app['merchant']->device_info,
            ]
        );
    }
}
