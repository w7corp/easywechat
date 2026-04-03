<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use ArrayAccess;
use EasyWeChat\Kernel\Contracts\Arrayable;
use EasyWeChat\Kernel\Contracts\Jsonable;
use EasyWeChat\Kernel\Support\Arr;
use EasyWeChat\Kernel\Traits\HasAttributes;

use function is_string;

/**
 * @implements ArrayAccess<string, mixed>
 */
class Authorization implements Arrayable, ArrayAccess, Jsonable
{
    use HasAttributes;

    public function getAppId(): string
    {
        return $this->getCorpId();
    }

    public function getCorpId(): string
    {
        $corpId = Arr::get($this->attributes, 'auth_corp_info.corpid');

        return is_string($corpId) ? $corpId : '';
    }
}
