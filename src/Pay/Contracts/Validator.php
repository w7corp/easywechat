<?php

declare(strict_types=1);

namespace EasyWeChat\Pay\Contracts;

use Psr\Http\Message\MessageInterface;

interface Validator
{
    public function validate(MessageInterface $message): void;
}
