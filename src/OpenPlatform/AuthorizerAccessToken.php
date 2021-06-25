<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use Stringable;
use EasyWeChat\Kernel\Contracts\AccessToken;

class AuthorizerAccessToken implements AccessToken, Stringable
{
    public function __construct(protected string $appId, protected string $accessToken)
    {
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function getToken(): string
    {
        return $this->accessToken;
    }

    public function __toString()
    {
        return $this->accessToken;
    }
}
