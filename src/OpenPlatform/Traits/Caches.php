<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Traits;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;

trait Caches
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * Sets cache store.
     *
     * @param Cache $cache
     *
     * @return $this
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Gets cache store. Defaults to file system in system temp folder.
     *
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }

    /**
     * Gets the cached data.
     *
     * @param string $key
     * @param mixed  $default a default value or a callable to return the value
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($cached = $this->getCache()->fetch($key)) {
            return $cached;
        }

        if (is_callable($default)) {
            return $default();
        }

        return $default;
    }

    /**
     * Sets the cached data.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $life  cache life time in seconds
     *
     * @return bool
     */
    public function set($key, $value, $life = 0)
    {
        return $this->getCache()->save($key, $value, $life);
    }

    /**
     * Removes the cached data.
     *
     * @param string $key
     *
     * @return bool
     */
    public function remove($key)
    {
        return $this->getCache()->delete($key);
    }
}
