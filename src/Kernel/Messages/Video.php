<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Messages;

/**
 * Class Video.
 *
 * @property string $video
 * @property string $title
 * @property string $media_id
 * @property string $description
 * @property string $thumb_media_id
 */
class Video extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'video';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'title',
        'description',
        'media_id',
        'thumb_media_id',
    ];

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
}
