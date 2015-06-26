<?php

/**
 * MessageInterface.php.
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

namespace EasyWeChat\Server\Messages;

/**
 * Class MessageInterface.
 */
interface MessageInterface
{
    /**
     * Build message to server.
     *
     * @return array
     */
    public function toReply();
}//end class
