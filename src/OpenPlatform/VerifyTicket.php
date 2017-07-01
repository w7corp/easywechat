<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * VerifyTicket.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @author    lixiao <leonlx126@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform;

use Doctrine\Common\Cache\Cache;
use EasyWeChat\Core\Exceptions\RuntimeException;

class VerifyTicket
{
    /**
     * Cache manager.
     *
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * App Id.
     *
     * @var string
     */
    protected $appId;

    /**
     * Cache Key.
     *
     * @var string
     */
    private $cacheKey;

    /**
     * Cache key prefix.
     *
     * @var string
     */
    protected $prefix = 'easywechat.open_platform.component_verify_ticket.';

    /**
     * VerifyTicket constructor.
     *
     * @param string                       $appId
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function __construct($appId, Cache $cache)
    {
        $this->appId = $appId;
        $this->cache = $cache;
    }

    /**
     * Set component verify ticket to the cache.
     *
     * @param string $ticket
     *
     * @return bool
     */
    public function setTicket($ticket)
    {
        return $this->cache->save($this->getCacheKey(), $ticket);
    }

    /**
     * Get component verify ticket.
     *
     * @return string
     *
     * @throws \EasyWeChat\Core\Exceptions\RuntimeException
     */
    public function getTicket()
    {
        if ($cached = $this->cache->fetch($this->getCacheKey())) {
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
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;

        return $this;
    }

    /**
     * Get component verify ticket cache key.
     *
     * @return string $this->cacheKey
     */
    public function getCacheKey()
    {
        if (is_null($this->cacheKey)) {
            return $this->prefix.$this->appId;
        }

        return $this->cacheKey;
    }
}
