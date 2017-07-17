<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Auth;

use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Traits\InteractsWithCache;

/**
 * Class VerifyTicket.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class VerifyTicket
{
    use InteractsWithCache;

    /**
     * @var string
     */
    protected $appId;

    /**
     * Constructor.
     *
     * @param string      $appId
     * @param string|null $ticket
     */
    public function __construct(string $appId, string $ticket = null)
    {
        $this->appId = $appId;
        $this->setTicket($ticket);
    }

    /**
     * Put the credential `component_verify_ticket` in cache.
     *
     * @param string $ticket
     *
     * @return $this
     */
    public function setTicket(string $ticket)
    {
        $this->getCache()->set($this->getCacheKey(), $ticket);

        return $this;
    }

    /**
     * Get the credential `component_verify_ticket` from cache.
     *
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function getTicket()
    {
        if ($cached = $this->getCache()->get($this->getCacheKey())) {
            return $cached;
        }

        throw new RuntimeException('Credential "component_verify_ticket" does not exist in cache.');
    }

    /**
     * Get cache key.
     *
     * @return string
     */
    protected function getCacheKey()
    {
        return 'easywechat.open_platform.component_verify_ticket.'.$this->appId;
    }
}
