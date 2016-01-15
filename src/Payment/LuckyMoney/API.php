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
 * @author    tianyong90 <412039588@qq.com>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Payment\LuckyMoney;

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Payment\Merchant;
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
     * API constructor.
     *
     * @param \EasyWeChat\Payment\Merchant $merchant
     */
    public function __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * Prepare luckymoney.
     *
     *
     * @return Collection
     */
    public function prepare(array $params)
    {
        $params['wxappid'] = $this->merchant->app_id;

        //XXX: PLEASE DON'T CHANGE THE FOLLOWING LINES.
        $params['auth_mchid'] = '1000052601';
        $params['auth_appid'] = 'wxbf42bd79c4391863';

        $params['amt_type'] = 'ALL_RAND';

        return $this->request(self::API_PREPARE, $params);
    }

    /**
     * Query luckymoney.
     *
     * @param string $mchBillNo
     */
    public function query($orderNo)
    {
        $params = [
            'appid' => $this->merchant->app_id,
            'mch_billno' => $orderNo,
            'bill_type' => 'MCHT',
        ];

        return $this->request(self::API_QUERY, $params);
    }

    /**
     * Send Luckymoney.
     *
     * @param array  $params
     * @param string $type
     */
    public function send(array $params, $type = self::TYPE_NORMAL)
    {
        if ($type === self::TYPE_NORMAL) {
            $api = self::API_SEND;
        } else {
            $api = self::API_SEND_GROUP;
        }

        $params['wxappid'] = $this->merchant->app_id;

        return $this->request($api, $params);
    }

    /**
     * Send normal lucnymoney.
     *
     * @param array $params
     *
     * @return Collection
     */
    public function sendNormal($params)
    {
        $params['total_num'] = 1;
        $params['client_ip'] = $params['client_ip'] ?: $_SERVER['HTTP_CLIENT_IP'];

        return $this->send($params, self::TYPE_NORMAL);
    }

    /**
     * Send group luckymoney.
     *
     * @param array $params
     *
     * @return Collection
     */
    public function sendGroup($params)
    {
        $params['amt_type'] = 'ALL_RAND';
        $params['client_ip'] = $params['client_ip'] ?: $_SERVER['HTTP_CLIENT_IP'];

        return $this->send($params, self::TYPE_GROUP);
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
     * @return Collection
     */
    protected function request($api, array $params, $method = 'post')
    {
        $params['mch_id'] = $this->merchant->merchant_id;
        $params['nonce_str'] = uniqid();
        $params['sign'] = \EasyWeChat\Payment\generate_sign($params, $this->merchant->key, 'md5');

        $options['body'] = XML::build($params);
        $options['cert'] = $this->merchant->get('cert_path');
        $options['ssl_key'] = $this->merchant->get('key_path');

        return $this->parseResponse($this->getHttp()->request($api, $method, $options));
    }

    /**
     * Parse Response XML to array.
     *
     * @param string $response
     *
     * @return Collection
     */
    protected function parseResponse($response)
    {
        if ($response instanceof ResponseInterface) {
            $response = $response->getBody();
        }

        return new Collection((array) XML::parse($response));
    }
}
