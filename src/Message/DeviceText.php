<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
