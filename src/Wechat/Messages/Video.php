<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Video.php.
 *
 * Part of Overtrue\Wechat.
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

namespace Overtrue\Wechat\Messages;

/**
 * 视频消息.
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
     * 属性.
     *
     * @var array
     */
    protected $properties = array(
                             'title',
                             'description',
                             'media_id',
                             'thumb_media_id',
                            );

    /**
     * 设置视频消息.
     *
     * @param string $mediaId
     *
     * @return Video
     */
    public function media($mediaId)
    {
        $this->setAttribute('media_id', $mediaId);

        return $this;
    }

    /**
     * 设置视频封面.
     *
     * @param string $mediaId
     *
     * @return Video
     */
    public function thumb($mediaId)
    {
        $this->setAttribute('thumb_media_id', $mediaId);

        return $this;
    }

    /**
     * 生成主动消息数组.
     *
     * @return array
     */
    public function toStaff()
    {
        return array(
                'video' => array(
                            'title' => $this->title,
                            'media_id' => $this->media_id,
                            'description' => $this->description,
                            'thumb_media_id' => $this->thumb_media_id,
                           ),
               );
    }

    /**
     * 生成回复消息数组.
     *
     * @return array
     */
    public function toReply()
    {
        $response = array(
                     'Video' => array(
                                 'MediaId' => $this->media_id,
                                 'Title' => $this->title,
                                 'Description' => $this->description,
                                ),
                    );

        return $response;
    }
}
