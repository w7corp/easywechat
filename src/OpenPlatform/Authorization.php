<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Contracts\Arrayable;
use EasyWeChat\Kernel\Contracts\Jsonable;
use EasyWeChat\Kernel\Traits\HasAttributes;
use JetBrains\PhpStorm\Pure;

class Authorization implements \ArrayAccess, Jsonable, Arrayable
{
    use HasAttributes;

    public function getAppId(): ?string
    {
        return $this->attributes['authorizer_appid'] ?? null;
    }

    #[Pure]
    public function getAccessToken(): AuthorizerAccessToken
    {
        return new AuthorizerAccessToken($this->getAppId(), $this->attributes['authorizer_access_token'] ?? '');
    }

    public function getRefreshToken(): ?string
    {
        return $this->attributes['authorizer_refresh_token'] ?? null;
    }
}
