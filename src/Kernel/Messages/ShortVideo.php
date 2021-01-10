<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

/**
 * @property string $title
 * @property string $media_id
 * @property string $description
 * @property string $thumb_media_id
 */
class ShortVideo extends Video
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'shortvideo';
}
