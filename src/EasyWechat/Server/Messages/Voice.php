<?php

/**
 * Voice.php.
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

use EasyWeChat\Message\Voice as BaseVoice;

/**
 * Class Voice.
 *
 * @property string $media_id
 */
class Voice extends BaseVoice implements MessageInterface
{
    /**
     * 生成主动消息数组.
     *
     * @return array
     */
    public function toStaff()
    {
        return [
                'voice' => [
                            'media_id' => $this->media_id,
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
                'Voice' => [
                            'MediaId' => $this->media_id,
                           ],
               ];
    }
}//end class
