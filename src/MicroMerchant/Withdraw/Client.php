<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MicroMerchant\Withdraw;

use EasyWeChat\MicroMerchant\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author   liuml  <liumenglei0211@163.com>
 * @DateTime 2019-05-30  14:19
 */
class Client extends BaseClient
{
    /**
     * Query withdrawal status.
     *
     * @param string $date
     * @param string $subMchId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\ArrayException
     * @throws \Safe\Exceptions\JsonException
     * @throws \Safe\Exceptions\PcreException
     * @throws \Safe\Exceptions\SimplexmlException
     * @throws \Safe\Exceptions\StringsException
     */
    public function queryWithdrawalStatus($date, $subMchId = '')
    {
        return $this->safeRequest('fund/queryautowithdrawbydate', [
            'date' => $date,
            'sign_type' => 'HMAC-SHA256',
            'nonce_str' => uniqid('micro'),
            'sub_mch_id' => $subMchId ?: $this->app['config']->sub_mch_id,
        ]);
    }

    /**
     * Re-initiation of withdrawal.
     *
     * @param string $date
     * @param string $subMchId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\ArrayException
     * @throws \Safe\Exceptions\JsonException
     * @throws \Safe\Exceptions\PcreException
     * @throws \Safe\Exceptions\SimplexmlException
     * @throws \Safe\Exceptions\StringsException
     */
    public function requestWithdraw($date, $subMchId = '')
    {
        return $this->safeRequest('fund/reautowithdrawbydate', [
            'date' => $date,
            'sign_type' => 'HMAC-SHA256',
            'nonce_str' => uniqid('micro'),
            'sub_mch_id' => $subMchId ?: $this->app['config']->sub_mch_id,
        ]);
    }
}
