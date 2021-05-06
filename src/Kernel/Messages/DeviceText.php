<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

/**
 * @property string $content
 */
class DeviceText extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected string $type = 'device_text';

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

    /**
     * @return array
     */
    public function toXmlArray(): array
    {
        return [
            'DeviceType' => $this->get('device_type'),
            'DeviceID' => $this->get('device_id'),
            'SessionID' => $this->get('session_id'),
            'Content' => base64_encode($this->get('content')),
        ];
    }
}
