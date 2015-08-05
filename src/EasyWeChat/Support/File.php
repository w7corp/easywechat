<?php
/**
 * File.php
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
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
        '255216'   => 'jpg',
        '13780'    => 'png',
        '7173'     => 'gif',
        '6677'     => 'bmp',
        '7368'     => 'mp3',
        '3533'     => 'amr',
        '8273'     => 'wav',
        '4838'     => 'wma',
    ];

    /**
     * Return steam extension.
     *
     * @param  string $stream
     *
     * @return string
     */
    public static function getStreamExt($stream)
    {
        $stream = unpack("C2chars", substr($stream, 0,2));

        $code = intval($stream['chars1'].$stream['chars2']);

        return isset(self::$extensions[$code]) ? self::$extensions[$code] : null;
    }
}
