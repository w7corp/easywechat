<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

use Psr\Http\Message\RequestInterface;

interface AccessTokenInterface
{
    public function getToken(): array;
    public function refresh(): static;
    public function applyToRequest(RequestInterface $request, array $requestOptions = []): RequestInterface;
}
