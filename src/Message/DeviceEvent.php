<?php
<<<<<<< HEAD
=======

>>>>>>> 35062f10cb8d3a6a94e06e14d0d81f7af335a56a
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
