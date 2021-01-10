<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

/**
 * @property string $media_id
 */
class File extends Media
{
    /**
     * @var string
     */
    protected string  $type = 'file';
}
