<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Contracts\Arrayable;
use EasyWeChat\Kernel\Contracts\Jsonable;
use EasyWeChat\Kernel\Traits\HasAttributes;
use JetBrains\PhpStorm\Pure;

/**
 * @implements \ArrayAccess<string, mixed>
 */
class Authorization implements \ArrayAccess, Jsonable, Arrayable
{
    use HasAttributes;

    public function getAppId(): string
    {
        return $this->attributes['authorization_info']['authorizer_appid'];
    }

    #[Pure]
    public function getAccessToken(): AuthorizerAccessToken
    {
        return new AuthorizerAccessToken(
            $this->attributes['authorization_info']['authorizer_appid'],
            $this->attributes['authorization_info']['authorizer_access_token'] ?? ''
        );
    }

    public function getRefreshToken(): string
    {
        return $this->attributes['authorization_info']['authorizer_refresh_token'];
    }
}
