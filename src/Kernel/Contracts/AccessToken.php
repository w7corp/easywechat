<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

interface AccessToken
{
    public function getToken(): string;
    public function toQuery(): array;
}
