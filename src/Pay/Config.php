<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

class Config extends \EasyWeChat\Kernel\Config
{
    protected array $requiredKeys = [
        'mch_id',
        'secret_key',
        'private_key',
        'certificate',
        'certificate_serial_no',
    ];
}
