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
 * Class Image.
 *
 * @property string $media_id
 */
class File extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'file';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'media_id',
    ];

    /**
     * Set media_id.
     *
     * @param string $mediaId
     *
     * @return \EasyWeChat\Kernel\Messages\File
     */
    public function file($mediaId)
    {
        $this->setAttribute('media_id', $mediaId);

        return $this;
    }
}
