<?php

namespace EasyWeChat\Message;

/**
 * Class DeviceText.
 *
 * @property string $content
 */
class DeviceText extends AbstractMessage
{
    /**
     * Message type.
     *
     * @var string
     */
    protected $type = 'device_text';

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
