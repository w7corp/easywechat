<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use ArrayAccess;
use EasyWeChat\Kernel\Contracts\Arrayable;
use EasyWeChat\Kernel\Contracts\Jsonable;
use EasyWeChat\Kernel\Support\Arr;
use EasyWeChat\Kernel\Traits\HasAttributes;
use JetBrains\PhpStorm\Pure;

use function is_string;

/**
 * @implements ArrayAccess<string, mixed>
 */
class Authorization implements Arrayable, ArrayAccess, Jsonable
{
    use HasAttributes;

    public function getAppId(): string
    {
        $appId = Arr::get($this->attributes, 'authorization_info.authorizer_appid');

        return is_string($appId) ? $appId : '';
    }

    #[Pure]
    public function getAccessToken(): AuthorizerAccessToken
    {
        $appId = Arr::get($this->attributes, 'authorization_info.authorizer_appid');
        $accessToken = Arr::get($this->attributes, 'authorization_info.authorizer_access_token');

        return new AuthorizerAccessToken(
            is_string($appId) ? $appId : '',
            is_string($accessToken) ? $accessToken : ''
        );
    }

    public function getRefreshToken(): string
    {
        $refreshToken = Arr::get($this->attributes, 'authorization_info.authorizer_refresh_token');

        return is_string($refreshToken) ? $refreshToken : '';
    }
}
