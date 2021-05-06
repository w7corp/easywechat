<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

class MiniProgramPage extends Message
{
    /**
     * @var string
     */
    protected string $type = 'miniprogrampage';

    /**
     * @var array
     */
    protected array $properties = [
        'title',
        'appid',
        'pagepath',
        'thumb_media_id',
    ];

    /**
     * @var array
     */
    protected array $required = [
        'thumb_media_id', 'appid', 'pagepath',
    ];
}
