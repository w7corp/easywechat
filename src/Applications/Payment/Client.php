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

use EasyWeChat\Applications\Payment\Traits\HandleNotify;
use EasyWeChat\Applications\Payment\Traits\JssdkHelpers;
use EasyWeChat\Exceptions\Exception;
use EasyWeChat\Support;
use EasyWeChat\Support\HasHttpRequests;
use EasyWeChat\Support\InteractsWithCache;
use EasyWeChat\Support\XML;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    use JssdkHelpers, HandleNotify;
    use InteractsWithCache;
    use HasHttpRequests {
        request as httpRequest;
    }

    /**
     * Sandbox box mode.
     *
     * @var bool
     */
    protected $sandboxEnabled = false;

    /**
     * Sandbox sign key.
     *
     * @var string
     */
    protected $sandboxSignKey;

    const API_HOST = 'https://api.mch.weixin.qq.com';

    // api
    const API_PAY_ORDER = '/pay/micropay';
    const API_PREPARE_ORDER = '/pay/unifiedorder';
    const API_QUERY = '/pay/orderquery';
    const API_CLOSE = '/pay/closeorder';
    const API_REVERSE = '/secapi/pay/reverse';
    const API_REFUND = '/secapi/pay/refund';
    const API_QUERY_REFUND = '/pay/refundquery';
    const API_DOWNLOAD_BILL = '/pay/downloadbill';
    const API_REPORT = '/payitil/report';

    const API_URL_SHORTEN = 'https://api.mch.weixin.qq.com/tools/shorturl';
    const API_AUTH_CODE_TO_OPENID = 'https://api.mch.weixin.qq.com/tools/authcodetoopenid';
    const API_SANDBOX_SIGN_KEY = 'https://api.mch.weixin.qq.com/sandboxnew/pay/getsignkey';

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
     * Set sandbox mode.
     *
     * @param bool $enabled
     *
     * @return $this
     */
    public function sandboxMode(bool $enabled = false)
    {
        $this->sandboxEnabled = $enabled;

        return $this;
    }

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
     * @return \EasyWeChat\Support\Collection
     */
    public function pay(Order $order)
    {
        return $this->request($this->wrapApi(self::API_PAY_ORDER), $order->all());
    }

    /**
     * Prepare order to pay.
     *
     * @param Order $order
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function prepare(Order $order)
    {
        $order->notify_url = $order->get('notify_url', $this->app['merchant']->notify_url);
        if (is_null($order->spbill_create_ip)) {
            $order->spbill_create_ip = ($order->trade_type === Order::NATIVE) ? Support\get_server_ip() : Support\get_client_ip();
        }

        return $this->request($this->wrapApi(self::API_PREPARE_ORDER), $order->all());
    }

    /**
     * Query order.
     *
     * @param string $orderNo
     * @param string $type
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function query($orderNo, $type = self::OUT_TRADE_NO)
    {
        $params = [
            $type => $orderNo,
        ];

        return $this->request($this->wrapApi(self::API_QUERY), $params);
    }

    /**
     * Query order by transaction_id.
     *
     * @param string $transactionId
     *
     * @return \EasyWeChat\Support\Collection
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
     * @return \EasyWeChat\Support\Collection
     */
    public function close($tradeNo)
    {
        $params = [
            'out_trade_no' => $tradeNo,
        ];

        return $this->request($this->wrapApi(self::API_CLOSE), $params);
    }

    /**
     * Reverse order.
     *
     * @param string $orderNo
     * @param string $type
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function reverse($orderNo, $type = self::OUT_TRADE_NO)
    {
        $params = [
            $type => $orderNo,
        ];

        return $this->safeRequest($this->wrapApi(self::API_REVERSE), $params);
    }

    /**
     * Reverse order by transaction_id.
     *
     * @param int $transactionId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function reverseByTransactionId($transactionId)
    {
        return $this->reverse($transactionId, self::TRANSACTION_ID);
    }

    /**
     * Make a refund request.
     *
     * @param string $orderNo
     * @param $refundNo
     * @param float  $totalFee
     * @param float  $refundFee
     * @param string $opUserId
     * @param string $type
     * @param string $refundAccount
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function refund(
        $orderNo,
        $refundNo,
        $totalFee,
        $refundFee = null,
        $opUserId = null,
        $type = self::OUT_TRADE_NO,
        $refundAccount = 'REFUND_SOURCE_UNSETTLED_FUNDS'
        ) {
        $params = [
            $type => $orderNo,
            'out_refund_no' => $refundNo,
            'total_fee' => $totalFee,
            'refund_fee' => $refundFee ?: $totalFee,
            'refund_fee_type' => $this->app['merchant']->fee_type,
            'refund_account' => $refundAccount,
            'op_user_id' => $opUserId ?: $this->app['merchant']->merchant_id,
        ];

        return $this->safeRequest($this->wrapApi(self::API_REFUND), $params);
    }

    /**
     * Refund by transaction id.
     *
     * @param string $orderNo
     * @param $refundNo
     * @param float  $totalFee
     * @param float  $refundFee
     * @param string $opUserId
     * @param string $refundAccount
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function refundByTransactionId(
        $orderNo,
        $refundNo,
        $totalFee,
        $refundFee = null,
        $opUserId = null,
        $refundAccount = 'REFUND_SOURCE_UNSETTLED_FUNDS'
        ) {
        return $this->refund($orderNo, $refundNo, $totalFee, $refundFee, $opUserId, self::TRANSACTION_ID, $refundAccount);
    }

    /**
     * Query refund status.
     *
     * @param string $orderNo
     * @param string $type
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function queryRefund($orderNo, $type = self::OUT_TRADE_NO)
    {
        $params = [
            $type => $orderNo,
        ];

        return $this->request($this->wrapApi(self::API_QUERY_REFUND), $params);
    }

    /**
     * Query refund status by out_refund_no.
     *
     * @param string $refundNo
     *
     * @return \EasyWeChat\Support\Collection
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
     * @return \EasyWeChat\Support\Collection
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
     * @return \EasyWeChat\Support\Collection
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

        return $this->request($this->wrapApi(self::API_DOWNLOAD_BILL), $params, 'post', [\GuzzleHttp\RequestOptions::STREAM => true], true)->getBody();
    }

    /**
     * Convert long url to short url.
     *
     * @param string $url
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function urlShorten($url)
    {
        return $this->request(self::API_URL_SHORTEN, ['long_url' => $url]);
    }

    /**
     * Report API status to WeChat.
     *
     * @param string $api
     * @param int    $timeConsuming
     * @param string $resultCode
     * @param string $returnCode
     * @param array  $other         ex: err_code,err_code_des,out_trade_no,user_ip...
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function report($api, $timeConsuming, $resultCode, $returnCode, array $other = [])
    {
        $params = array_merge([
            'interface_url' => $api,
            'execute_time_' => $timeConsuming,
            'return_code' => $returnCode,
            'return_msg' => null,
            'result_code' => $resultCode,
            'user_ip' => Support\get_client_ip(),
            'time' => time(),
        ], $other);

        return $this->request($this->wrapApi(self::API_REPORT), $params);
    }

    /**
     * Get openid by auth code.
     *
     * @param string $authCode
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function authCodeToOpenId($authCode)
    {
        return $this->request(self::API_AUTH_CODE_TO_OPENID, ['auth_code' => $authCode]);
    }

    /**
     * Make a API request.
     *
     * @param string $api
     * @param array  $params
     * @param string $method
     * @param array  $options
     * @param bool   $returnResponse
     *
     * @return \EasyWeChat\Support\Collection|\Psr\Http\Message\ResponseInterface
     */
    protected function request($api, array $params, $method = 'post', array $options = [], $returnResponse = false)
    {
        $params = array_merge($params, $this->app['merchant']->only(['sub_appid', 'sub_mch_id']));

        $params['appid'] = $this->app['merchant']->app_id;
        $params['mch_id'] = $this->app['merchant']->merchant_id;
        $params['device_info'] = $this->app['merchant']->device_info;
        $params['nonce_str'] = uniqid();
        $params = array_filter($params);
        $params['sign'] = Support\generate_sign($params, $this->getSignKey($api), 'md5');

        $options = array_merge([
            'body' => XML::build($params),
        ], $options);

        $response = $this->httpRequest($api, $method, $options);

        return $returnResponse ? $response : $this->resolveResponse($response);
    }

    /**
     * Return key to sign.
     *
     * @param string $api
     *
     * @return string
     */
    protected function getSignKey($api)
    {
        return $this->sandboxEnabled && $api !== self::API_SANDBOX_SIGN_KEY ? $this->getSandboxSignKey() : $this->app['merchant']->key;
    }

    /**
     * Request with SSL.
     *
     * @param string $api
     * @param array  $params
     * @param string $method
     *
     * @return \EasyWeChat\Support\Collection
     */
    protected function safeRequest($api, array $params, $method = 'post')
    {
        $options = [
            'cert' => $this->app['merchant']->get('cert_path'),
            'ssl_key' => $this->app['merchant']->get('key_path'),
        ];

        return $this->request($api, $params, $method, $options);
    }

    /**
     * Wrap API.
     *
     * @param string $resource
     *
     * @return string
     */
    protected function wrapApi($resource)
    {
        return self::API_HOST.($this->sandboxEnabled ? '/sandboxnew' : '').$resource;
    }

    /**
     * Get sandbox sign key.
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getSandboxSignKey()
    {
        if ($this->sandboxSignKey) {
            return $this->sandboxSignKey;
        }

        $cache = $this->getCache();
        $cacheKey = 'easywechat.payment.sandbox.'.$this->app['merchant']->merchant_id;

        if (!$this->sandboxSignKey = $cache->get($cacheKey)) {
            $result = $this->request(self::API_SANDBOX_SIGN_KEY, []);

            if ($result->return_code === 'SUCCESS') {
                $cache->set($cacheKey, $key = $result->sandbox_signkey, 24 * 3600);

                return $this->sandboxSignKey = $key;
            }

            throw new Exception($result->return_msg);
        }

        return $this->sandboxSignKey;
    }
}
