<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OpenPlatform\Auth;

use EasyWeChat\Exceptions\RuntimeException;
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
     * @var string
     */
    protected $cacheKey;

    /**
     * Constructor.
     *
     * @param string $appId
     */
    public function __construct(string $appId)
    {
        $this->appId = $appId;
        $this->cacheKey = 'easywechat.open_platform.component_verify_ticket.'.$this->appId;
    }

    /**
     * Put the credential `component_verify_ticket` in cache.
     *
     * @param string $ticket
     *
     * @return bool
     */
    public function setTicket(string $ticket)
    {
        return $this->getCache()->set($this->cacheKey, $ticket);
    }

    /**
     * Get the credential `component_verify_ticket` from cache.
     *
     * @return string
     *
     * @throws \EasyWeChat\Exceptions\RuntimeException
     */
    public function getTicket()
    {
        if ($cached = $this->getCache()->get($this->cacheKey)) {
            return $cached;
        }

        throw new RuntimeException('Credential "component_verify_ticket" does not exist in cache.');
    }
}
