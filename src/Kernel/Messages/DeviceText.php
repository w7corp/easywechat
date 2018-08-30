<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Messages;

/**
 * Class DeviceText.
 *
 * @property string $content
 */
class DeviceText extends Message
{
    /**
     * Messages type.
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

    public function toXmlArray()
    {
        return [
            'DeviceType' => $this->get('device_type'),
            'DeviceID' => $this->get('device_id'),
            'SessionID' => $this->get('session_id'),
            'Content' => base64_encode($this->get('content')),
        ];
    }
}
