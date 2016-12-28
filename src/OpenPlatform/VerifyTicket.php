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
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use EasyWeChat\Core\Exceptions\RuntimeException;
use EasyWeChat\Support\Collection;

class VerifyTicket
{
    /**
     * Config.
     *
     * @var array
     */
    protected $config;

    /**
     * Cache.
     *
     * @var Cache
     */
    private $cache;

    /**
     * Cache Key.
     *
     * @var string
     */
    private $cacheKey;

    /**
     * Component verify ticket xml structure name.
     *
     * @var string
     */
    protected $ticketXmlName = 'ComponentVerifyTicket';

    /**
     * Cache key prefix.
     *
     * @var string
     */
    protected $prefix = 'easywechat.common.component_verify_ticket.';

    /**
     * VerifyTicket constructor.
     *
     * @param array                        $config
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function __construct($config, Cache $cache = null)
    {
        $this->config = $config;
        $this->cache = $cache;
    }

    /**
     * Save component verify ticket.
     *
     * @param Collection $message
     *
     * @return bool
     */
    public function cache(Collection $message)
    {
        return $this->getCache()->save(
            $this->getCacheKey(),
            $message->get($this->ticketXmlName)
        );
    }

    /**
     * Get component verify ticket.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function getTicket()
    {
        $cached = $this->getCache()->fetch($this->getCacheKey());

        if (empty($cached)) {
            throw new RuntimeException('Component verify ticket does not exists.');
        }

        return $cached;
    }

    /**
     * Set cache.
     *
     * @param \Doctrine\Common\Cache\Cache $cache
     *
     * @return VerifyTicket
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Return the cache manager.
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
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
            return $this->prefix.$this->config['app_id'];
        }

        return $this->cacheKey;
    }
}
