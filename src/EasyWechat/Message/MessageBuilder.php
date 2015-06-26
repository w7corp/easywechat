<?php

/**
 * MessageBuilder.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Message;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;

/**
 * Class MessageBuilder.
 */
class MessageBuilder
{
    /**
     * 消息类型.
     */
    const TEXT = 'text';
    const IMAGE = 'image';
    const VOICE = 'voice';
    const VIDEO = 'video';
    const MUSIC = 'music';
    const NEWS = 'news';
    const TRANSFER = 'transfer';
    const NEWS_ITEM = 'news_item';

    /**
     * Return message instance.
     *
     * @param string $type
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public static function make($type = self::TEXT)
    {
        if (!defined(__CLASS__.'::'.strtoupper($type))) {
            throw new InvalidArgumentException("Error Message Type '{$type}'");
        }

        $message = 'EasyWeChat\\Server\\Messages\\'
                    .str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $type)));

        return new $message();
    }

    /**
     * Magic access.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        return call_user_func_array('self::make', [$method, $args]);
    }
}//end class

