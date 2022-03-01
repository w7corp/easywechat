<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

class Config extends \EasyWeChat\Kernel\Config
{
    /**
     * @var array<string>
     */
    protected array $requiredKeys = [
        'corp_id',
        'suite_id',
        'provider_secret',
        'suite_secret',
        'token',
        'aes_key',
    ];
}
