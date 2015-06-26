<?php

/**
 * MessageFactory.php.
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

use EasyWeChat\Core\Application;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Support\Str;

/**
 * Class MessageBuilder.
 */
class MessageFactory
{
    const TEXT = 'text';
    const IMAGE = 'image';
    const VOICE = 'voice';
    const VIDEO = 'video';
    const MUSIC = 'music';
    const ARTICLES = 'articles';
    const TRANSFER = 'transfer';
    const NEWS_ITEM = 'news_item';

    /**
     * Application instance.
     *
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Return message instance.
     *
     * @param string $type
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function make($type = self::TEXT)
    {
        if (!defined(__CLASS__.'::'.strtoupper($type))) {
            throw new InvalidArgumentException("Error Message Type '{$type}'");
        }

        return $this->app->get("message.{$type}");
    }
}//end class
