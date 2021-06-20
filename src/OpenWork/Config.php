<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

class Config extends \EasyWeChat\Kernel\Config
{
    protected array $requiredKeys = [
        'corp_id',
        'provider_secret',
        'token',
        'aes_key',
    ];
}
