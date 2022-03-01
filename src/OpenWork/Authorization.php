<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\Contracts\Arrayable;
use EasyWeChat\Kernel\Contracts\Jsonable;
use EasyWeChat\Kernel\Traits\HasAttributes;

/**
 * @implements \ArrayAccess<string, mixed>
 */
class Authorization implements \ArrayAccess, Jsonable, Arrayable
{
    use HasAttributes;

    public function getAppId(): string
    {
        return $this->attributes['auth_corp_info']['corpid'];
    }
}
