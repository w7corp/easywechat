<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Contracts;

use EasyWeChat\Kernel\Encryptor;

interface Request
{
    public function isValidation(): bool;

    public function isSafeMode(): bool;

    public function getMessage(Encryptor $encryptor = null): Message;
}
