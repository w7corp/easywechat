<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * API.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Payment;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\Exception;
use EasyWeChat\Support\Collection;
use EasyWeChat\Support\XML;
use Psr\Http\Message\ResponseInterface;

/**
 * Class API.
 */
class API extends AbstractAPI
{
    /**
     * Merchant instance.
     *
     * @var Merchant
     */
    protected $merchant;

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

    /**
     * Cache.
     *
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

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
     * API constructor.
     *
     * @param \EasyWeChat\Payment\Merchant      $merchant
     * @param \Doctrine\Common\Cache\Cache|null $cache
     */
    public function __construct(Merchant $merchant, Cache $cache = null)
    {
        $this->merchant = $merchant;
        $this->cache = $cache;
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
        $order->notify_url = $order->get('notify_url', $this->merchant->notify_url);
        if (is_null($order->spbill_create_ip)) {
            $order->spbill_create_ip = ($order->trade_type === Order::NATIVE) ? get_server_ip() : get_client_ip();
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
     * @param string $refundNo
     * @param float  $totalFee
     * @param float  $refundFee
     * @param string $opUserId
     * @param string $type
     * @param string $refundAccount
     * @param string $refundReason
     *
     * @return Collection
     */
    public function refund(
        $orderNo,
        $refundNo,
        $totalFee,
        $refundFee = null,
        $opUserId = null,
        $type = self::OUT_TRADE_NO,
        $refundAccount = 'REFUND_SOURCE_UNSETTLED_FUNDS',
        $refundReason = ''
        ) {
        $params = [
            $type => $orderNo,
            'out_refund_no' => $refundNo,
            'total_fee' => $totalFee,
            'refund_fee' => $refundFee ?: $totalFee,
            'refund_fee_type' => $this->merchant->fee_type,
            'refund_account' => $refundAccount,
            'refund_desc' => $refundReason,
            'op_user_id' => $opUserId ?: $this->merchant->merchant_id,
        ];

        return $this->safeRequest($this->wrapApi(self::API_REFUND), $params);
    }

    /**
     * Refund by transaction id.
     *
     * @param string $orderNo
     * @param string $refundNo
     * @param float  $totalFee
     * @param float  $refundFee
     * @param string $opUserId
     * @param string $refundAccount
     * @param string $refundReason
     *
     * @return Collection
     */
    public function refundByTransactionId(
        $orderNo,
        $refundNo,
        $totalFee,
        $refundFee = null,
        $opUserId = null,
        $refundAccount = 'REFUND_SOURCE_UNSETTLED_FUNDS',
        $refundReason = ''
        ) {
        return $this->refund($orderNo, $refundNo, $totalFee, $refundFee, $opUserId, self::TRANSACTION_ID, $refundAccount, $refundReason);
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
            'user_ip' => get_client_ip(),
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
     * Merchant setter.
     *
     * @param Merchant $merchant
     *
     * @return $this
     */
    public function setMerchant(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * Merchant getter.
     *
     * @return Merchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * Set sandbox mode.
     *
     * @param bool $enabled
     *
     * @return $this
     */
    public function sandboxMode($enabled = false)
    {
        $this->sandboxEnabled = $enabled;

        return $this;
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
        $params = array_merge($params, $this->merchant->only(['sub_appid', 'sub_mch_id']));

        $params['appid'] = $this->merchant->app_id;
        $params['mch_id'] = $this->merchant->merchant_id;
        $params['device_info'] = $this->merchant->device_info;
        $params['nonce_str'] = uniqid();
        $params = array_filter($params);

        $params['sign'] = generate_sign($params, $this->getSignkey($api), 'md5');

        $options = array_merge([
            'body' => XML::build($params),
        ], $options);

        $response = $this->getHttp()->request($api, $method, $options);

        return $returnResponse ? $response : $this->parseResponse($response);
    }

    /**
     * Return key to sign.
     *
     * @param string $api
     *
     * @return string
     */
    protected function getSignkey($api)
    {
        return $this->sandboxEnabled && $api !== self::API_SANDBOX_SIGN_KEY ? $this->getSandboxSignKey() : $this->merchant->key;
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
            'cert' => $this->merchant->get('cert_path'),
            'ssl_key' => $this->merchant->get('key_path'),
        ];

        return $this->request($api, $params, $method, $options);
    }

    /**
     * Parse Response XML to array.
     *
     * @param ResponseInterface $response
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
     */
    protected function getSandboxSignKey()
    {
        if ($this->sandboxSignKey) {
            return $this->sandboxSignKey;
        }

        // Try to get sandbox_signkey from cache
        $cacheKey = 'sandbox_signkey.'.$this->merchant->merchant_id.$this->merchant->sub_merchant_id;

        /** @var \Doctrine\Common\Cache\Cache $cache */
        $cache = $this->getCache();

        $this->sandboxSignKey = $cache->fetch($cacheKey);

        if (!$this->sandboxSignKey) {
            // Try to acquire a new sandbox_signkey from WeChat
            $result = $this->request(self::API_SANDBOX_SIGN_KEY, []);

            if ($result->return_code === 'SUCCESS') {
                $cache->save($cacheKey, $result->sandbox_signkey, 24 * 3600);

                return $this->sandboxSignKey = $result->sandbox_signkey;
            }

            throw new Exception($result->return_msg);
        }

        return $this->sandboxSignKey;
    }

    /**
     * Return the cache manager.
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }
}
