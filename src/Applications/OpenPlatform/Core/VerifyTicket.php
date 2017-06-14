<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OpenPlatform\Core;

use EasyWeChat\Exceptions\RuntimeException;
use EasyWeChat\Support\InteractsWithCache;

class VerifyTicket
{
    use InteractsWithCache;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $cacheKey;

    /**
     * VerifyTicket constructor.
     *
     * @param string $clientId
     */
    public function __construct(string $clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * Set component verify ticket.
     *
     * @param string $ticket
     *
     * @return bool
     */
    public function setTicket(string $ticket)
    {
        return $this->getCache()->set($this->getCacheKey(), $ticket);
    }

    /**
     * Get component verify ticket.
     *
     * @return string
     *
     * @throws \EasyWeChat\Exceptions\RuntimeException
     */
    public function getTicket()
    {
        if ($cached = $this->getCache()->get($this->getCacheKey())) {
            return $cached;
        }

        throw new RuntimeException('Component verify ticket does not exists.');
    }

    /**
     * Set component verify ticket cache key.
     *
     * @param string $cacheKey
     *
     * @return $this
     */
    public function setCacheKey(string $cacheKey)
    {
        $this->cacheKey = $cacheKey;

        return $this;
    }

    /**
     * Get component verify ticket cache key.
     *
     * @return string
     */
    public function getCacheKey(): string
    {
        if (is_null($this->cacheKey)) {
            return 'easywechat.open_platform.component_verify_ticket.'.$this->clientId;
        }

        return $this->cacheKey;
    }
}
