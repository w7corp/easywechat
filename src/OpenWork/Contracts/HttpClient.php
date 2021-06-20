<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\Contracts;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

interface HttpClient extends HttpClientInterface
{
    public function withAccessToken(AccessTokenInterface $accessToken): static;
}
