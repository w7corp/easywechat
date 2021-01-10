<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

class Link extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'link';

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
