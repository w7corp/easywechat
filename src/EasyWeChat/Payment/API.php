<?php
/**
 * API.php
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Payment;

use EasyWeChat\Core\Http;
use EasyWeChat\Support\XML;

/**
 * Class API.
 */
class API
{
    /**
     * Http Client.
     *
     * @var Http
     */
    protected $http;

    /**
     * Merchant instance.
     *
     * @var Merchant
     */
    protected $merchant;

    // api
    const API_PREPARE_ORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    const API_QUERY = 'https://api.mch.weixin.qq.com/pay/orderquery';
    const API_CLOSE = 'https://api.mch.weixin.qq.com/pay/closeorder';
    const API_REVERSE = 'https://api.mch.weixin.qq.com/secapi/pay/reverse';
    const API_REFUND = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    const API_QUERY_REFUND = 'https://api.mch.weixin.qq.com/pay/refundquery';
    const API_DOWNLOAD_BILL = 'https://api.mch.weixin.qq.com/pay/downloadbill';
    const API_REPORT = 'https://api.mch.weixin.qq.com/payitil/report';
    const API_URL_SHORTEN = 'https://api.mch.weixin.qq.com/tools/shorturl';

    // order id types.
    const TRANSCATION_ID = 'transcation_id';
    const OUT_TRADE_NO = 'out_trade_no';
    const OUT_REFUND_NO = 'out_refund_no';
    const REFUND_ID     = 'refund_id';

    // bill types.
    const BILL_TYPE_ALL = 'ALL';
    const BILL_TYPE_SUCCESS = 'SUCCESS';
    const BILL_TYPE_REFUND = 'REFUND';
    const BILL_TYPE_REVOKED = 'REVOKED';

    /**
     * Constructor.
     *
     * @param Merchant $merchant
     * @param Http     $http
     */
    public function __construct(Merchant $merchant, Http $http)
    {
        $this->merchant = $merchant;
        $this->http = $http->setExpectedException(PaymentHttpException::class);
    }

    /**
     * Prepare order to pay.
     *
     * @param Order $order
     *
     * @return array
     */
    public function prepare(Order $order)
    {
        return $this->request(self::API_PREPARE_ORDER, $order->all());
    }

    /**
     * Query order.
     *
     * @param string $orderNo
     * @param string $type
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
     * @return array
     */
    public function queryByTranscationId($transcationId)
    {
        return $this->query($transcationId, self::TRANSCATION_ID);
    }

    /**
     * Close order by out_trade_no.
     *
     * @param $tradeNo
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
     */
    public function reverse($orderNo, $type = self::OUT_TRADE_NO)
    {
        $params = [
            $type => $orderNo,
        ];

        return $this->request(self::API_REVERSE, $params);
    }

    /**
     * Make a refund request.
     *
     * @param string $orderNo
     * @param string $type
     *
     * @return array
     */
    public function refund(
        $orderNo,
        $totalFee,
        $refundFee = null,
        $refundNo = null,
        $type = self::OUT_TRADE_NO,
        $opUserId = null
        )
    {
        $params = [
            $type             => $orderNo,
            'total_fee'       => $totalFee,
            'refund_fee'      => $refundFee ?: $totalFee,
            'refund_no'       => $refundNo ?: $orderNo,
            'refund_fee_type' => $this->merchant->fee_type,
            'op_user_id'      => $opUserId ?: $this->merchant->merchant_id,
        ];

        return $this->request(self::API_REFUND, $params);
    }

    /**
     * Query refund status.
     *
     * @param string $orderNo
     * @param string $type
     */
    public function queryRefund($orderNo, $type = self::OUT_TRADE_NO)
    {
        $params = [
            $type => $orderNo,
        ];

        return $this->request(self::API_QUERY_REFUND, $params);
    }

    /**
     * Download bill history as a table file.
     *
     * @param string $date
     * @param string $type
     *
     * @return stream
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
     * @return array
     */
    public function urlShorten($url)
    {
        return $this->request(self::API_URL_SHORTEN, ['long_url' => $url]);
    }

    // /**
    //  * Report API status to WeChat.
    //  *
    //  * @param string $api
    //  * @param int    $timeConsuming
    //  * @param string $resultCode
    //  * @param string $returnCode
    //  * @param string $returnMessage
    //  * @param string $errorCode
    //  * @param string $errorMessage
    //  * @param string $tradeNo
    //  * @param string $ip
    //  *
    //  * @return array
    //  */
    // public function report(
    //     $api,
    //     $timeConsuming,
    //     $resultCode,
    //     $returnCode,
    //     $returnMessage = null,
    //     $errorCode = null,
    //     $errorMessage = null,
    //     $tradeNo = null,
    //     $ip = null
    //     )
    // {
    //     $params = [
    //         'interface_url' => $api,
    //         'execute_time_' => $timeConsuming,
    //         'return_code'   => $returnCode,
    //         'return_msg'    => $returnMessage,
    //         'result_code'   => $resultCode,
    //         'err_code'      => $errorCode,
    //         'err_code_des'  => $errorMessage,
    //         'out_trade_no'  => $tradeNo,
    //         'user_ip'       => $ip ?: $_SERVER['SERVER_ADDR'],
    //         'time'          => time(),
    //     ];

    //     return $this->request(self::API_DOWNLOAD_BILL, $params);
    // }

    /**
     * Merchant setter.
     *
     * @param Merchant $merchant
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
     * @return array
     */
    protected function request($api, array $params, $method = 'post')
    {
        $params['appid']       = $this->merchant->app_id;
        $params['mch_id']      = $this->merchant->merchant_id;
        $params['device_info'] = $this->merchant->device_info;
        $params['time_stamp']  = time();
        $params['nonce_str']   = uniqid();
        $params['sign']        = (new SignGenerator($this->merchant->key, 'md5'))
                                    ->generate($params);

        return $this->parseResponse($this->http->{$method}($api, XML::build($params)));
    }

    /**
     * Parse Response XML to array.
     *
     * @param string $response
     *
     * @return array
     */
    protected function parseResponse($response)
    {
        return XML::parse($response);
    }
}