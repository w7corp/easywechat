<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel;

use ArrayAccess;
use EasyWeChat\Kernel\Contracts\Jsonable;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\Kernel\Traits\HasAttributes;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @property string $FromUserName
 * @property string $ToUserName
 * @property string $Encrypt
 *
 * @implements ArrayAccess<array-key, mixed>
 */
abstract class Message implements \JsonSerializable, ArrayAccess, Jsonable
{
    use HasAttributes;

    /**
     * @param  array<string,string>  $attributes
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
        $attributes = self::format($originContent = strval($request->getBody()));

        return new static($attributes, $originContent);
    }

    /**
     * @return array<string,string>
     *
     * @throws BadRequestException
     */
    public static function format(string $originContent): array
    {
        if (stripos($originContent, '<') === 0) {
            $attributes = Xml::parse($originContent);
        }

        // Handle JSON format.
        $dataSet = json_decode($originContent, true);

        if (json_last_error() === JSON_ERROR_NONE && $originContent) {
            $attributes = $dataSet;
        }

        if (empty($attributes) || ! is_array($attributes)) {
            throw new BadRequestException('Failed to decode request contents.');
        }

        return $attributes;
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
