<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\Transfer;

use EasyWeChat\Payment\BaseClient;

/**
 * Class Client.
 *
 * @author AC <alexever@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Query MerchantPay.
     *
     * @param string $mchBillNo
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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

        return $this->safeRequest('mmpaymkttransfers/gettransferinfo', $params);
    }

    /**
     * Send MerchantPay.
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function send(array $params)
    {
        $params['mchid'] = $this->app['merchant']->merchant_id;
        $params['mch_appid'] = $this->app['merchant']->app_id;

        return $this->safeRequest('mmpaymkttransfers/promotion/transfers', $params);
    }
}
