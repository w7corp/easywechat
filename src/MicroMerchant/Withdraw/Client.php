<?php

declare(strict_types=1);

namespace EasyWeChat\MicroMerchant\Withdraw;

use EasyWeChat\MicroMerchant\Kernel\BaseClient;

/**
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
