<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Messages;

class MiniprogramNotice extends Message
{
    /**
     * @var string
     */
    protected string $type = 'miniprogram_notice';

    /**
     * @var array
     */
    protected array $properties = [
        'appid',
        'title',
    ];
}
