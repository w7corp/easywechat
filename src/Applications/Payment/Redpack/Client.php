<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\Payment\Redpack;

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
    const API_SEND = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
    const API_SEND_GROUP = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendgroupredpack';
    const API_QUERY = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo';
    const API_PREPARE = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/hbpreorder';

    // LuckyMoney type
    const TYPE_NORMAL = 'NORMAL';
    const TYPE_GROUP = 'GROUP';

    // Risk control type.
    const RISK_NORMAL = 'NORMAL';
    const RISK_IGN_FREQ_LMT = 'IGN_FREQ_LMT';
    const RISK_IGN_DAY_LMT = 'IGN_DAY_LMT';
    const RISK_IGN_FREQ_DAY_LMT = 'IGN_FREQ_DAY_LMT';

    /**
     * Prepare shake-around redpack.
     *
     * @param array $params
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function prepare(array $params)
    {
        $params['wxappid'] = $this->app['merchant']->app_id;

        // XXX: PLEASE DON'T CHANGE THE FOLLOWING LINES.
        $params['auth_mchid'] = '1000052601';
        $params['auth_appid'] = 'wxbf42bd79c4391863';

        $params['amt_type'] = 'ALL_RAND';

        return $this->request(self::API_PREPARE, $params);
    }

    /**
     * Query redpack.
     *
     * @param string $mchBillNo
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function query($mchBillNo)
    {
        $params = [
            'appid' => $this->app['merchant']->app_id,
            'mch_billno' => $mchBillNo,
            'bill_type' => 'MCHT',
        ];

        return $this->request(self::API_QUERY, $params);
    }

    /**
     * Send redpack.
     *
     * @param array  $params
     * @param string $type
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function send(array $params, $type = self::TYPE_NORMAL)
    {
        $api = ($type === self::TYPE_NORMAL) ? self::API_SEND : self::API_SEND_GROUP;

        $params['wxappid'] = $this->app['merchant']->app_id;
        //如果类型为分裂红则去掉client_ip参数,否则签名会出错
        if ($type === self::TYPE_GROUP) {
            unset($params['client_ip']);
        }

        return $this->request($api, $params);
    }

    /**
     * Send normal redpack.
     *
     * @param array $params
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function sendNormal($params)
    {
        $params['total_num'] = 1;
        $params['client_ip'] = $params['client_ip'] ?? Support\get_server_ip();

        return $this->send($params, self::TYPE_NORMAL);
    }

    /**
     * Send group redpack.
     *
     * @param array $params
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function sendGroup($params)
    {
        $params['amt_type'] = 'ALL_RAND';

        return $this->send($params, self::TYPE_GROUP);
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
        $params['nonce_str'] = uniqid();
        $params['sign'] = Support\generate_sign($params, $this->app['merchant']->key, 'md5');

        $options = [
            'body' => Support\XML::build($params),
            'cert' => $this->app['merchant']->get('cert_path'),
            'ssl_key' => $this->app['merchant']->get('key_path'),
        ];

        return $this->resolveResponse($this->httpRequest($api, $method, $options));
    }
}
