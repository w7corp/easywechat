<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

/**
 * @property string $title
 * @property string $description
 * @property string $url
 */
class TextCard extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'textcard';

    /**
     * Properties.
     *
     * @var array
     */
    protected array $properties = [
        'title',
        'description',
        'url',
    ];
}
