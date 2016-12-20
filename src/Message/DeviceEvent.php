<?php

namespace EasyWeChat\Message;

/**
 * Class DeviceEvent.
 *
 * @property string $media_id
 */
class DeviceEvent extends AbstractMessage
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'device_event';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'device_type',
        'device_id',
        'content',
        'session_id',
        'open_id',
    ];
}
