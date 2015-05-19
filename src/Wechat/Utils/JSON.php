<?php

namespace Overtrue\Wechat\Utils;

/**
 * unicode2multi characters supported for the wechat server
 * 
 *  @author  James ZHANG
 *  @link    https://thenorthmemory.github.io
 *  @version 1.0
 */
class JSON
{
    /**
     * PHP >= 5.3 JSON_UNESCAPED_UNICODE constant supported
     * @see http://php.net/manual/en/function.json-encode.php
     *
     * @param mixed $value   The value (except a resource) being encoded.
     * @param int   $options Bitmask consisting of blah...
     * @param int   $depth   Set the maximum depth. Must be greater than zero.
     *
     * @return mixed Returns a string containing the JSON representation of data 
     */
    public static function encode($value, $options = 0, $depth = 512)
    {
        $depth_supported = version_compare(PHP_VERSION, '5.4.0', '>=');

        $data = $depth_supported ? json_encode($value, $options, $depth) : json_encode($value, $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return $data;
        }

        return $depth_supported ? $data : preg_replace_callback("/\\\u([\w]{2})([\w]{2})/iu", function ($pipe) {
                return iconv(
                    strncasecmp(PHP_OS, 'WIN', 3) ? 'UCS-2BE' : 'UCS-2', 
                    'UTF-8', 
                    chr(hexdec($pipe[1])) . chr(hexdec($pipe[2]))
                );
            }, $data);
    }

    /**
     * PHP >= 5.3 options supported (TODO)
     * @see http://php.net/manual/en/function.json-decode.php
     *
     * @param string $json    The json string being decoded.
     * @param bool   $assoc   When TRUE, returned objects will be converted into associative arrays.
     * @param int    $depth   User specified recursion depth.
     * @param int    $options Bitmask of JSON decode options blah...
     *
     * @return mixed Returns the value encoded in json in appropriate PHP type.
     */
    public static function decode($json, $assoc = false, $depth = 512, $options = 0)
    {
        return version_compare(PHP_VERSION, '5.4.0', '>=') ? json_decode($json, $assoc, $depth, $options)
            : json_decode($json, $assoc, $depth);
    }
}

/*EOF*/