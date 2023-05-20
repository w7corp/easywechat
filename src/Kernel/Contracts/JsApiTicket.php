<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

interface JsApiTicket
{
    public function getTicket(): string;

    /**
     * @return array<string,mixed>
     */
    public function configSignature(string $url, string $nonce, int $timestamp): array;
}
