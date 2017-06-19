<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\Payment\Coupon;

use EasyWeChat\Applications\Payment\BaseClient;
use EasyWeChat\Support;

/**
 * Class Client.
 *
 * @author tianyong90 <412039588@qq.com>
 */
class Client extends BaseClient
{
    use Support\HasHttpRequests {
        request as httpRequest;
    }

    // api
    const API_SEND = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/send_coupon';
    const API_QUERY_STOCK = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/query_coupon_stock';
    const API_QUERY = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/querycouponsinfo';

    /**
     * send a cash coupon.
     *
     * @param array $params
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function send(array $params)
    {
        $params['openid_count'] = 1;

        return $this->request(self::API_SEND, $params);
    }

    /**
     * query a coupon stock.
     *
     * @param array $params
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function queryStock(array $params)
    {
        return $this->request(self::API_QUERY_STOCK, $params);
    }

    /**
     * query a info of coupon.
     *
     * @param array $params
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function query(array $params)
    {
        return $this->request(self::API_QUERY, $params);
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
        $params['mch_id'] = $this->app['merchant']->merchant_id;
        $params['appid'] = $this->app['merchant']->app_id;
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
