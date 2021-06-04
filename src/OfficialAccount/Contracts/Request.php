<?php

namespace EasyWeChat\OfficialAccount\Contracts;

use Psr\Http\Message\RequestInterface;

interface Request extends RequestInterface
{
    public function isValidation(): bool;
    public function isSafeMode(): bool;
    public function getMessage(): Message;
}
