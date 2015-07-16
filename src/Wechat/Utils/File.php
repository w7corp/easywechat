<?php
/**
 * File.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat\Utils;

class File
{
    /**
     * 对应文件类型
     *
     * @var array
     */
    protected static $extensionMaps = [
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
     * 根据文件流获取
     *
     * @param  string $content 文件流
     *
     * @return string 文件类型
     */
    public static function getStreamExt($content)
    {
        $stream = @unpack("C2chars", substr($content, 0,2));

        $code = intval($stream['chars1'].$stream['chars2']);

        return isset(self::$extensionMaps[$code]) ? self::$extensionMaps[$code] : null;
    }
}
