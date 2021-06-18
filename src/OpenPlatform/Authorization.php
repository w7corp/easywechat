<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Contracts\Arrayable;
use EasyWeChat\Kernel\Contracts\Jsonable;
use EasyWeChat\Kernel\Traits\HasAttributes;

class Authorization implements \ArrayAccess, Jsonable, Arrayable
{
    use HasAttributes;

    public function getAppId(): ?string
    {
        return $this->attributes['authorizer_appid'] ?? null;
    }

    public function getAccessToken(): ?string
    {
        return $this->attributes['authorizer_access_token'] ?? null;
    }

    public function getRefreshToken(): ?string
    {
        return $this->attributes['authorizer_refresh_token'] ?? null;
    }
}
