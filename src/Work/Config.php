<?php

declare(strict_types=1);

namespace EasyWeChat\Work;

class Config extends \EasyWeChat\Kernel\Config
{
    protected array $requiredKeys = [
        'corp_id',
        'secret',
        'aes_key',
    ];
}
