<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

class MiniProgramPage extends Message
{
    protected $type = 'miniprogrampage';

    protected $properties = [
        'title',
        'appid',
        'pagepath',
        'thumb_media_id',
    ];

    protected $required = [
        'thumb_media_id', 'appid', 'pagepath',
    ];
}
