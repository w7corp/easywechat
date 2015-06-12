<?php
/**
 * Text.php
 *
 * Part of MasApi\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace MasApi\Wechat\BroadcastMessages;

/**
 * 文本消息
 *
 * @property string $content
 */
class Text extends BaseMessage
{

    /**
     * 属性
     *
     * @var array
     */
    protected $properties = array('content');


    /**
     * 生成按用户组群发数组
     */
    public function toBroadcast()
    {
        $text = array(
            'text' => array(
                'content' => $this->content,
                )
            );

        return $text;
    }
}
