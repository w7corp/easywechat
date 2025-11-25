<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel;

use ArrayAccess;
use EasyWeChat\Kernel\Contracts\Jsonable;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Support\MessageParser;
use EasyWeChat\Kernel\Traits\HasAttributes;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @property string $FromUserName
 * @property string $ToUserName
 * @property string $Encrypt
 * @property string $encrypt
 *
 * @implements ArrayAccess<array-key, mixed>
 */
abstract class Message implements \JsonSerializable, ArrayAccess, Jsonable
{
    use HasAttributes;

    /**
     * @param  array<string, mixed>  $attributes
     */
    final public function __construct(array $attributes = [], protected ?string $originContent = '')
    {
        $this->attributes = $attributes;
    }

    /**
     * @throws BadRequestException
     */
    public static function createFromRequest(ServerRequestInterface $request): Message
    {
        return static::createFromStringContent(strval($request->getBody()));
    }

    /**
     * @throws BadRequestException
     */
    public static function createFromStringContent(string $originContent): Message
    {
        $attributes = MessageParser::parse($originContent);

        return new static($attributes, $originContent);
    }

    public function getOriginalContents(): string
    {
        return $this->originContent ?? '';
    }

    public function __toString()
    {
        return $this->toJson() ?: '';
    }
}
