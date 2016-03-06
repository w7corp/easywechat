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
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Payment;

use EasyWeChat\Core\AbstractAPI;
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

    // api
    const API_PAY_ORDER = 'https://api.mch.weixin.qq.com/pay/micropay';
    const API_PREPARE_ORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    const API_QUERY = 'https://api.mch.weixin.qq.com/pay/orderquery';
    const API_CLOSE = 'https://api.mch.weixin.qq.com/pay/closeorder';
    const API_REVERSE = 'https://api.mch.weixin.qq.com/secapi/pay/reverse';
    const API_REFUND = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    const API_QUERY_REFUND = 'https://api.mch.weixin.qq.com/pay/refundquery';
    const API_DOWNLOAD_BILL = 'https://api.mch.weixin.qq.com/pay/downloadbill';
    const API_REPORT = 'https://api.mch.weixin.qq.com/payitil/report';
    const API_URL_SHORTEN = 'https://api.mch.weixin.qq.com/tools/shorturl';
    const API_AUTH_CODE_TO_OPENID = 'https://api.mch.weixin.qq.com/tools/authcodetoopenid';

    // order id types.
    const TRANSCATION_ID = 'transcation_id';
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
     * @param \EasyWeChat\Payment\Merchant $merchant
     */
    public function __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;
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
        $order['notify_url'] = $order['notify_url']? : $this->merchant->notify_url;
        
        return $this->request(self::API_PAY_ORDER, $order->all());
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
        $order['notify_url'] = $order['notify_url']? : $this->merchant->notify_url;
        
        return $this->request(self::API_PREPARE_ORDER, $order->all());
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

        return $this->request(self::API_QUERY, $params);
    }

    /**
     * Query order by transcation_id.
     *
     * @param string $transcationId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function queryByTranscationId($transcationId)
    {
        return $this->query($transcationId, self::TRANSCATION_ID);
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

        return $this->request(self::API_CLOSE, $params);
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

        return $this->request(self::API_REVERSE, $params);
    }

    /**
     * Reverse order by transcation_id.
     *
     * @param int $transcationId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function reverseByTranscationId($transcationId)
    {
        return $this->reverse($transcationId, self::TRANSCATION_ID);
    }

    /**
     * Make a refund request.
     *
     * @param string $orderNo
     * @param float  $totalFee
     * @param float  $refundFee
     * @param string $opUserId
     * @param string $type
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function refund(
        $orderNo,
        $totalFee,
        $refundFee = null,
        $opUserId = null,
        $type = self::OUT_TRADE_NO
        ) {
        $params = [
            $type => $orderNo,
            'total_fee' => $totalFee,
            'refund_fee' => $refundFee ?: $totalFee,
            'refund_fee_type' => $this->merchant->fee_type,
            'op_user_id' => $opUserId ?: $this->merchant->merchant_id,
        ];

        return $this->request(self::API_REFUND, $params);
    }

    /**
     * Refund by transcation id.
     *
     * @param string $orderNo
     * @param float  $totalFee
     * @param float  $refundFee
     * @param string $opUserId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function refundByTranscationId(
        $orderNo,
        $totalFee,
        $refundFee = null,
        $opUserId = null
        ) {
        return $this->refund($orderNo, $totalFee, $refundFee, $opUserId, self::TRANSCATION_ID);
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

        return $this->request(self::API_QUERY_REFUND, $params);
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
     * @param string $transcationId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function queryRefundByTranscationId($transcationId)
    {
        return $this->queryRefund($transcationId, self::TRANSCATION_ID);
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
     * @return \EasyWeChat\Support\Collection
     */
    public function downloadBill($date, $type = self::BILL_TYPE_ALL)
    {
        $params = [
            'bill_date' => $date,
            'bill_type' => $type,
        ];

        return $this->request(self::API_DOWNLOAD_BILL, $params);
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
            'user_ip' => $_SERVER['SERVER_ADDR'],
            'time' => time(),
        ], $other);

        return $this->request(self::API_REPORT, $params);
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
     * Make a API request.
     *
     * @param string $api
     * @param array  $params
     * @param string $method
     *
     * @return \EasyWeChat\Support\Collection
     */
    protected function request($api, array $params, $method = 'post')
    {
        $params['appid'] = $this->merchant->app_id;
        $params['mch_id'] = $this->merchant->merchant_id;
        $params['device_info'] = $this->merchant->device_info;
        $params['time_stamp'] = time();
        $params['nonce_str'] = uniqid();
        $params['sign'] = generate_sign($params, $this->merchant->key, 'md5');

        return $this->parseResponse($this->getHttp()->{$method}($api, XML::build($params)));
    }

    /**
     * Parse Response XML to array.
     *
     * @param string $response
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
}
