<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\Sandbox;

use EasyWeChat\Kernel\Traits\InteractsWithCache;
use EasyWeChat\Payment\Kernel\BaseClient;
use EasyWeChat\Payment\Kernel\Exceptions\SandboxException;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    use InteractsWithCache;

    const ENDPOINT = 'pay/getsignkey';

    /**
     * @return string
     *
     * @throws \EasyWeChat\Payment\Kernel\Exceptions\SandboxException
     */
    public function key(): string
    {
        if ($cache = $this->getCache()->get($this->getCacheKey())) {
            return $cache;
        }

        $response = $this->resolveResponse($this->requestRaw(self::ENDPOINT), 'array');

        if ($response['return_code'] === 'SUCCESS') {
            $this->getCache()->set($this->getCacheKey(), $key = $response['sandbox_signkey'], 24 * 3600);

            return $key;
        }

        throw new SandboxException($response['return_msg']);
    }

    /**
     * @param string $endpoint
     *
     * @return bool
     */
    public function except(string $endpoint): bool
    {
        $excepts = [
            self::ENDPOINT,
        ];

        return in_array($endpoint, $excepts, true);
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return 'easywechat.payment.sandbox.'.$this->app['config']->app_id;
    }
}
