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

use EasyWeChat\Payment\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author AC <alexever@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Query MerchantPay to balance.
     *
     * @param string $partnetTradeNo
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function queryBalanceOrder(string $partnetTradeNo)
    {
        $params = [
            'appid' => $this->app['config']->app_id,
            'mch_id' => $this->app['config']->mch_id,
            'partner_trade_no' => $partnetTradeNo,
        ];

        return $this->safeRequest('mmpaymkttransfers/gettransferinfo', $params);
    }

    /**
     * Send MerchantPay to balance.
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function toBalance(array $params)
    {
        $base = [
            'appid' => null,
            'mch_id' => null,
            'mchid' => $this->app['config']->mch_id,
            'mch_appid' => $this->app['config']->app_id,
        ];

        return $this->safeRequest('mmpaymkttransfers/promotion/transfers', array_merge($base, $params));
    }

    /**
     * Query MerchantPay order to BankCard.
     *
     * @param string $partnetTradeNo
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function queryBankCardOrder(string $partnetTradeNo)
    {
        $params = [
            'mch_id' => $this->app['config']->mch_id,
            'partner_trade_no' => $partnetTradeNo,
        ];

        return $this->safeRequest('mmpaysptrans/query_bank', $params);
    }

    /**
     * Send MerchantPay to BankCard.
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function toBankCard(array $params)
    {
        $base = [
            'mchid' => $this->app['config']->mch_id,
        ];

        // TODO: RSA 加密 enc_bank_no, enc_true_name

        return $this->safeRequest('mmpaysptrans/pay_bank', array_merge($base, $params));
    }
}
