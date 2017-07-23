<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\Redpack;

use EasyWeChat\Kernel\Support;
use EasyWeChat\Payment\BaseClient;

/**
 * Class Client.
 *
 * @author tianyong90 <412039588@qq.com>
 */
class Client extends BaseClient
{
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function prepare(array $params)
    {
        $params['wxappid'] = $this->app['merchant']->app_id;

        // XXX: PLEASE DON'T CHANGE THE FOLLOWING LINES.
        $params['auth_mchid'] = '1000052601';
        $params['auth_appid'] = 'wxbf42bd79c4391863';

        $params['amt_type'] = 'ALL_RAND';

        return $this->safeRequest('mmpaymkttransfers/hbpreorder', $params);
    }

    /**
     * Query redpack.
     *
     * @param string $mchBillNo
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function query($mchBillNo)
    {
        $params = [
            'appid' => $this->app['merchant']->app_id,
            'mch_billno' => $mchBillNo,
            'bill_type' => 'MCHT',
        ];

        return $this->safeRequest('mmpaymkttransfers/gethbinfo', $params);
    }

    /**
     * Send redpack.
     *
     * @param array  $params
     * @param string $type
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function send(array $params, $type = self::TYPE_NORMAL)
    {
        $params['wxappid'] = $this->app['merchant']->app_id;
        //如果类型为分裂红则去掉client_ip参数,否则签名会出错
        if ($type === self::TYPE_GROUP) {
            unset($params['client_ip']);
        }

        return $this->safeRequest(
            ($type === self::TYPE_NORMAL) ? 'mmpaymkttransfers/sendredpack' : 'mmpaymkttransfers/sendgroupredpack',
            $params
        );
    }

    /**
     * Send normal redpack.
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function sendGroup($params)
    {
        $params['amt_type'] = 'ALL_RAND';

        return $this->send($params, self::TYPE_GROUP);
    }

    /**
     * {@inheritdoc}.
     */
    protected function prepends(): array
    {
        return [
            'mch_id' => $this->app['merchant']->merchant_id,
        ];
    }
}
