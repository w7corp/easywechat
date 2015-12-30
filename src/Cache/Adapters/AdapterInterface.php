<?php

/*
 * This file is part of the EasyWeChat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * AdapterInterface.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Cache\Adapters;

/**
 * Interface AdapterInterface.
 */
interface AdapterInterface
{
    /**
     * Set app id.
     *
     * @param string $appId
     *
     * @return AdapterInterface
     */
    public function setAppId($appId);

    /**
     * Get cache content.
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return string
     */
    public function get($key, $default = null);

    /**
     * Set cache content.
     *
     * @param string $key
     * @param string $value
     * @param int    $lifetime
     *
     * @return int
     */
    public function set($key, $value, $lifetime = 7200);
}
