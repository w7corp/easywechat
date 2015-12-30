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
 * File.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Support;

/**
 * Class File.
 */
class File
{
    /**
     * Extensions.
     *
     * @var array
     */
    protected static $extensions = [
        '255216' => 'jpg',
        '13780' => 'png',
        '7173' => 'gif',
        '6677' => 'bmp',
        '7368' => 'mp3',
        '3533' => 'amr',
        '8273' => 'wav',
        '4838' => 'wma',
    ];

    /**
     * Return steam extension.
     *
     * @param string $stream
     *
     * @return string
     */
    public static function getStreamExt($stream)
    {
        $stream = unpack('C2chars', substr($stream, 0, 2));

        $code = intval($stream['chars1'].$stream['chars2']);

        return isset(self::$extensions[$code]) ? self::$extensions[$code] : null;
    }
}
