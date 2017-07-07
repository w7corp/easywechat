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
 * Class Music.
 *
 * @property string $url
 * @property string $hq_url
 * @property string $title
 * @property string $description
 * @property string $thumb_media_id
 * @property string $format
 */
class Music extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'music';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'title',
        'description',
        'url',
        'hq_url',
        'thumb_media_id',
        'format',
    ];

    /**
     * 设置音乐封面.
     *
     * @param string $mediaId
     *
     * @return $this
     */
    public function thumb($mediaId)
    {
        $this->setAttribute('thumb_media_id', $mediaId);

        return $this;
    }
}
