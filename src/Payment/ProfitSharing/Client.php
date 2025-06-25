<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\ProfitSharing;

use EasyWeChat\Payment\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author ClouderSky <clouder.flow@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * {@inheritdoc}.
     */
    protected function prepends()
    {
        return [
            'sign_type' => 'HMAC-SHA256',
        ];
    }

    /**
     * Add profit sharing receiver.
     * 服务商代子商户发起添加分账接收方请求.
     * 后续可通过发起分账请求将结算后的钱分到该分账接收方.
     *
     * @param array $receiver 分账接收方对象，json格式
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addReceiver(array $receiver)
    {
        $params = [
            'appid' => $this->app['config']->app_id,
            'receiver' => json_encode(
                $receiver,
                JSON_UNESCAPED_UNICODE
            ),
        ];

        return $this->request(
            'pay/profitsharingaddreceiver',
            $params
        );
    }

    /**
     * Delete profit sharing receiver.
     * 服务商代子商户发起删除分账接收方请求.
     * 删除后不支持将结算后的钱分到该分账接收方.
     *
     * @param array $receiver 分账接收方对象，json格式
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteReceiver(array $receiver)
    {
        $params = [
            'appid' => $this->app['config']->app_id,
            'receiver' => json_encode(
                $receiver,
                JSON_UNESCAPED_UNICODE
            ),
        ];

        return $this->request(
            'pay/profitsharingremovereceiver',
            $params
        );
    }

    /**
     * Single profit sharing.
     * 请求单次分账.
     *
     * @param string $transactionId 微信支付订单号
     * @param string $outOrderNo    商户系统内部的分账单号
     * @param array  $receivers     分账接收方列表
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function share(
        string $transactionId,
        string $outOrderNo,
        array $receivers
    ) {
        $params = [
            'appid' => $this->app['config']->app_id,
            'transaction_id' => $transactionId,
            'out_order_no' => $outOrderNo,
            'receivers' => json_encode(
                $receivers,
                JSON_UNESCAPED_UNICODE
            ),
        ];

        return $this->safeRequest(
            'secapi/pay/profitsharing',
            $params
        );
    }

    /**
     * Multi profit sharing.
     * 请求多次分账.
     *
     * @param string $transactionId 微信支付订单号
     * @param string $outOrderNo    商户系统内部的分账单号
     * @param array  $receivers     分账接收方列表
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function multiShare(
        string $transactionId,
        string $outOrderNo,
        array $receivers
    ) {
        $params = [
            'appid' => $this->app['config']->app_id,
            'transaction_id' => $transactionId,
            'out_order_no' => $outOrderNo,
            'receivers' => json_encode(
                $receivers,
                JSON_UNESCAPED_UNICODE
            ),
        ];

        return $this->safeRequest(
            'secapi/pay/multiprofitsharing',
            $params
        );
    }

    /**
     * Finish profit sharing.
     * 完结分账.
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function markOrderAsFinished(array $params)
    {
        $params['appid'] = $this->app['config']->app_id;
        $params['sub_appid'] = null;

        return $this->safeRequest(
            'secapi/pay/profitsharingfinish',
            $params
        );
    }

    /**
     * Query profit sharing result.
     * 查询分账结果.
     *
     * @param string $transactionId 微信支付订单号
     * @param string $outOrderNo    商户系统内部的分账单号
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function query(
        string $transactionId,
        string $outOrderNo
    ) {
        $params = [
            'sub_appid' => null,
            'transaction_id' => $transactionId,
            'out_order_no' => $outOrderNo,
        ];

        return $this->request(
            'pay/profitsharingquery',
            $params
        );
    }

    /**
     * Profit sharing return.
     * 分账回退.
     *
     * @param string $outOrderNo    商户系统内部的分账单号
     * @param string $outReturnNo   商户系统内部分账回退单号
     * @param int    $returnAmount  回退金额
     * @param string $returnAccount 回退方账号
     * @param string $description   回退描述
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function returnShare(
        string $outOrderNo,
        string $outReturnNo,
        int $returnAmount,
        string $returnAccount,
        string $description
    ) {
        $params = [
            'appid' => $this->app['config']->app_id,
            'out_order_no' => $outOrderNo,
            'out_return_no' => $outReturnNo,
            'return_account_type' => 'MERCHANT_ID',
            'return_account' => $returnAccount,
            'return_amount' => $returnAmount,
            'description' => $description,
        ];

        return $this->request(
            'secapi/pay/profitsharingreturn',
            $params
        );
    }


    /**
     * Profit sharing Order AmountQuery.
     * 查询订单待分账金额.
     *
     * @param string $transactionID   微信支付订单号
     * @return array 查询结果数组
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */

    public function profitSharingOrderAmountQuery(
        string $transactionID
    ) {
        $params = [
            'transaction_id' => $transactionID,
        ];
        return $this->safeRequest(
            'pay/profitsharingorderamountquery',
            $params
        );
    }


    /**
     * Profit sharing return.
     * 查询最大分账比例.
     *
     * @param string $sub_mch_id   微信支付分配的子商户号，即分账的出资商户号。普通分账传入子商户号时，查询子商户号的设置的最大分账比例。
     * @return array 查询结果数组
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */

    public function profitSharingMerchantRatioQuery(
        string $sub_mch_id
    ) {
        $params = [
            'sub_mch_id' => $sub_mch_id
        ];
        return $this->safeRequest(
            'pay/profitsharingmerchantratioquery',
            $params
        );
    }


    /**
     * 查询分账回退结果
     *
     * @param string $outOrderNo 商户订单号
     * @param string $outReturnNo 分账回退单号
     *
     * @return array 查询结果数组
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function returnShareQuery(
        string $outOrderNo,
        string $outReturnNo
    ) {
        $params = [
            'appid' => $this->app['config']->app_id,
            'out_order_no' => $outOrderNo,
            'out_return_no' => $outReturnNo,
        ];

        return $this->request(
            'pay/profitsharingreturnquery',
            $params
        );
    }

    /**
     * 发起自定义请求。
     *
     * @param string $url 请求的URL地址。
     * @param array $params 请求参数数组。
     * @return mixed 请求的结果。
     */

    public function requestCustom(
        string $url,
        array $params = [],
        string $method = 'post',
        array $options = [],
        bool $returnResponse = false
    ) {
        return $this->request(
            $url, 
            $params,
            $method,
            $options,
            $returnResponse
        );
    }
    /**
     * 安全请求自定义接口
     *
     * @param string $url 请求的URL地址
     * @param array $params 请求参数数组
     * @return mixed 返回请求的结果
     *
     * 该方法封装了一个安全的HTTP请求，用于向指定的URL发送请求并返回响应。
     * 它内部调用了safeRequest方法，确保请求的安全性。
     */
    public function safeRequestCustom(
        string $url,
        array $params,
        string $method = 'post',
        array $options = []
    ) {
        return $this->safeRequest(
            $url, 
            $params,
            $method,
            $options
        );
    }
}
