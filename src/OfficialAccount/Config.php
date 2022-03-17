<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

class Config extends \EasyWeChat\Kernel\Config
{
    /**
     * @var array<string>
     */
    protected array $requiredKeys = [
        'app_id',
    ];
}
