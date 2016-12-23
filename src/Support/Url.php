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
 * Url.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Support;

/**
 * Class Url.
 */
class Url
{
    /**
     * Get current url.
     *
     * @return string
     */
    public static function current()
    {
        $protocol = (!empty($_SERVER['HTTPS'])
                        && $_SERVER['HTTPS'] !== 'off'
                        || (int) $_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://';

        return $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }

    /**
     * URL-encodes string.
     *
     * @param $url
     *
     * @return string
     */
    public static function encode($url)
    {
        return urlencode($url);
    }
}
