<?php

declare(strict_types=1);

namespace EasyWeChat\Pay\Contracts;

use Psr\Http\Message\MessageInterface;

interface Validator
{
    /**
     * @throws \EasyWeChat\Pay\Exceptions\InvalidSignatureException if signature validate failed.
     */
    public function validate(MessageInterface $message): void;
}
