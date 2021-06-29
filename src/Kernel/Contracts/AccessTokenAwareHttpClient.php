<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

interface AccessTokenAwareHttpClient extends HttpClientInterface
{
    public function withAccessToken(AccessTokenInterface $accessToken): static;
}
