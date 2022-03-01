<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

class Config extends \EasyWeChat\Kernel\Config
{
    /**
     * @var array<string>
     */
    protected array $requiredKeys = [
        'app_id',
        'secret',
        'aes_key',
    ];
}
