<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

/**
 * @property string $media_id
 */
class Image extends Media
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'image';
}
