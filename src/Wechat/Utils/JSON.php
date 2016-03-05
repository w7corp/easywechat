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
 * JSON.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    James ZHANG <james.cau@gmail.com>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://thenorthmemory.github.io
 */

namespace Overtrue\Wechat\Utils;

// for PHP5.3, prevent PHP Notice msg assumed
defined('JSON_UNESCAPED_UNICODE') || define('JSON_UNESCAPED_UNICODE', 256);

/**
 * Unicode2multi characters supported for the wechat server.
 */
class JSON
{
    /**
     * To prevent new operation, under static usage only.
     */
    protected function __construct()
    {
    }

    /**
     * PHP >= 5.3 JSON_UNESCAPED_UNICODE constant supported.
     *
     * @param mixed $value   The value (except a resource) being encoded.
     * @param int   $options Bitmask consisting of blah...
     * @param int   $depth   Set the maximum depth. Must be greater than zero.
     *
     * @see http://php.net/manual/en/function.json-encode.php
     *
     * @return mixed Returns a string containing the JSON representation of data
     */
    public static function encode($value, $options = 0, $depth = 512)
    {
        // multi-characters supported by default
        $options |= JSON_UNESCAPED_UNICODE;

        $data = version_compare(PHP_VERSION, '5.5.0', '>=')
            ? json_encode($value, $options, $depth)
            : json_encode($value, $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return $data;
        }

        return version_compare(PHP_VERSION, '5.4.0', '>=')
            ? $data
            : preg_replace_callback('/\\\\u([0-9a-f]{2})([0-9a-f]{2})/iu', function ($pipe) {
                return iconv(
                    strncasecmp(PHP_OS, 'WIN', 3) ? 'UCS-2BE' : 'UCS-2',
                    'UTF-8',
                    chr(hexdec($pipe[1])).chr(hexdec($pipe[2]))
                );
            }, $data);
    }

    /**
     * PHP >= 5.3 options supported (TODO).
     *
     * @param string $json    The json string being decoded.
     * @param bool   $assoc   When TRUE, returned objects will be converted into associative arrays.
     * @param int    $depth   User specified recursion depth.
     * @param int    $options Bitmask of JSON decode options blah...
     *
     * @see http://php.net/manual/en/function.json-decode.php
     *
     * @return mixed Returns the value encoded in json in appropriate PHP type.
     */
    public static function decode($json, $assoc = false, $depth = 512, $options = 0)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return json_decode($json, $assoc, $depth, $options);
        }

        return json_decode($json, $assoc, $depth);
    }
}
