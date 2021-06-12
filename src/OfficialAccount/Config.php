<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

class Config extends \EasyWeChat\Kernel\Config
{
    protected array $requiredKeys = [
        'app_id',
        'secret',
        'aes_key',
    ];
}
