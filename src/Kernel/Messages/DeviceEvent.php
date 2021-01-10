<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

/**
 * @property string $media_id
 */
class DeviceEvent extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'device_event';

    /**
     * Properties.
     *
     * @var array
     */
    protected array $properties = [
        'device_type',
        'device_id',
        'content',
        'session_id',
        'open_id',
    ];
}
