<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

interface MessageInterface
{
    public function getType(): string;
    public function transformForJsonRequest(): array;
    public function transformToXml(): string;
}
