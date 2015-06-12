<?php
/**
 * Video.php
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

use MasApi\Wechat\Media;

/**
 * 视频消息
 *
 * @property string $video
 * @property string $title
 * @property string $media_id
 * @property string $description
 * @property string $thumb_media_id
 */
class Video extends BaseMessage
{

    /**
     * 属性
     *
     * @var array
     */
    protected $properties = array(
                             'title',
                             'description',
                             'media_id',
                            );


    /**
     * 生成视频群发
     */
    public function toBroadcast()
    {
        $video = array(
            'voice' => array(
                'media_id' => $this->media_id,
                'title' => $this->title,
                'description' => $this->description,
                )
            );

        return $video;
    }
}
