<?php

namespace EasyWeChat\Support;

/**
 * Static methods autoloading.
 */
class Utils
{
    public static function current()
    {
        return 'http://current.org';
    }

    public static function encode($url)
    {
        return urlencode($url);
    }
}
