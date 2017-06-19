<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\Payment\Transfer;

use EasyWeChat\Applications\Payment\BaseClient;
use EasyWeChat\Support;

/**
 * Class Client.
 *
 * @author AC <alexever@gmail.com>
 */
class Client extends BaseClient
{
    use Support\HasHttpRequests {
        request as httpRequest;
    }

    // api
    const API_SEND = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
    const API_QUERY = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo';

    /**
     * Query MerchantPay.
     *
     * @param string $mchBillNo
     *
     * @return \EasyWeChat\Support\Collection
     *
     * @notice mch_id when query, but mchid when send
     */
    public function query($mchBillNo)
    {
        $params = [
            'appid' => $this->app['merchant']->app_id,
            'mch_id' => $this->app['merchant']->merchant_id,
            'partner_trade_no' => $mchBillNo,
        ];

        return $this->request(self::API_QUERY, $params);
    }

    /**
     * Send MerchantPay.
     *
     * @param array $params
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function send(array $params)
    {
        $params['mchid'] = $this->app['merchant']->merchant_id;
        $params['mch_appid'] = $this->app['merchant']->app_id;

        return $this->request(self::API_SEND, $params);
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
        $params = array_filter($params);
        $params['nonce_str'] = uniqid();
        $params['sign'] = Support\generate_sign($params, $this->app['merchant']->key, 'md5');

        $options = [
            'body' => Support\XML::build($params),
            'cert' => $this->app['merchant']->get('cert_path'),
            'ssl_key' => $this->app['merchant']->get('key_path'),
        ];

        return $this->parseResponse($this->httpRequest($api, $method, $options));
    }
}
