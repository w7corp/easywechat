<?php

/**
 * Video.php.
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

use EasyWeChat\Message\Video as BaseVideo;

/**
 * Class Video.
 *
 * @property string $video
 * @property string $title
 * @property string $media_id
 * @property string $description
 * @property string $thumb_media_id
 */
class Video extends BaseVideo implements MessageInterface
{
    /**
     * 生成主动消息数组.
     *
     * @return array
     */
    public function toStaff()
    {
        return [
                'video' => [
                            'title' => $this->title,
                            'media_id' => $this->media_id,
                            'description' => $this->description,
                            'thumb_media_id' => $this->thumb_media_id,
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
        $response = [
                     'Video' => [
                                 'MediaId' => $this->media_id,
                                 'Title' => $this->title,
                                 'Description' => $this->description,
                                ],
                    ];

        return $response;
    }
}//end class
