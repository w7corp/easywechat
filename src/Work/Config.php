<?php

declare(strict_types=1);

namespace EasyWeChat\Work;

class Config extends \EasyWeChat\Kernel\Config
{
    /**
     * @var array<string>
     */
    protected array $requiredKeys = [
        'corp_id',
        'secret',
        'token',
        'aes_key',
    ];
}
