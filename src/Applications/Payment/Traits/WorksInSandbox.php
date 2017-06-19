<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\Payment\Traits;

use EasyWeChat\Exceptions\InvalidArgumentException;
use EasyWeChat\Support\InteractsWithCache;
use EasyWeChat\Support\XML;

trait WorksInSandbox
{
    use InteractsWithCache;

    /**
     * Sandbox box mode.
     *
     * @var bool
     */
    protected $sandboxEnabled = false;

    /**
     * Sandbox sign key.
     *
     * @var string
     */
    protected $sandboxSignKey;

    /**
     * @var string
     */
    protected $sandboxSignKeyEndpoint = 'https://api.mch.weixin.qq.com/sandboxnew/pay/getsignkey';

    /**
     * Set sandbox mode.
     *
     * @param bool $enabled
     *
     * @return $this
     */
    public function sandboxMode(bool $enabled = false)
    {
        $this->sandboxEnabled = $enabled;

        return $this;
    }

    /**
     * Wrap API.
     *
     * @param string $resource
     *
     * @return string
     */
    protected function wrapApi($resource)
    {
        return 'https://api.mch.weixin.qq.com/'.($this->sandboxEnabled ? 'sandboxnew/' : '').$resource;
    }

    /**
     * Return key to sign.
     *
     * @param string $api
     *
     * @return string
     */
    protected function getSignKey($api)
    {
        return $this->sandboxEnabled && $api !== $this->sandboxSignKeyEndpoint ? $this->getSandboxSignKey() : $this->app['merchant']->key;
    }

    /**
     * Get sandbox sign key.
     *
     * @return string
     */
    protected function getSandboxSignKey()
    {
        if ($this->sandboxSignKey) {
            return $this->sandboxSignKey;
        }

        if (!$this->sandboxSignKey = $this->getCache()->get($this->getCacheKey())) {
            $response = $this->request($this->sandboxSignKeyEndpoint, [], 'post', [], true);

            $result = (array) XML::parse($response->getBody());

            $this->sandboxSignKey = $this->getSignKeyFromResponse($result);
        }

        return $this->sandboxSignKey;
    }

    /**
     * @param array $result
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    private function getSignKeyFromResponse(array $result)
    {
        if ($result['return_code'] === 'SUCCESS') {
            $this->getCache()->set($this->getCacheKey(), $sandboxSignKey = $result['sandbox_signkey'], 24 * 3600);

            return $this->sandboxSignKey = $sandboxSignKey;
        }

        throw new InvalidArgumentException($result['return_msg']);
    }

    /**
     * @return string
     */
    private function getCacheKey()
    {
        return 'easywechat.payment.sandbox.'.$this->app['merchant']->merchant_id;
    }
}
