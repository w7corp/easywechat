<?php

/**
 * Text.php.
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

/**
 * Class Text.
 *
 * @property string $content
 */
class Text extends AbstractMessage implements MessageInterface
{
    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = ['content'];

    /**
     * 生成主动消息数组.
     *
     * @return array
     */
    public function toStaff()
    {
        return [
                'text' => [
                           'content' => $this->content,
                          ],
               ];
    }

    /**
     * 生成回复消息数组.
     *
     * @return array
     */
    public function toReply()
    {
        return [
                'Content' => $this->content,
               ];
    }
}
