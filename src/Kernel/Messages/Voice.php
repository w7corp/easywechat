<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

/**
 * @property string $media_id
 */
class Voice extends Media
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'voice';

    /**
     * Properties.
     *
     * @var array
     */
    protected array $properties = [
        'media_id',
        'recognition',
    ];
}
