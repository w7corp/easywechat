<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

class Location extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'location';

    /**
     * Properties.
     *
     * @var array
     */
    protected array $properties = [
        'latitude',
        'longitude',
        'scale',
        'label',
        'precision',
    ];
}
