<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\OfficialAccount\Contracts\Message as MessageInterface;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ServerRequestInterface;

class Message implements MessageInterface
{
    public function __construct(
        protected array $attributes = [],
        protected ?string $originContent = ''
    ) {
    }

    public function getOriginalContents(): ?string
    {
        return $this->originContent;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function __set(string $attribute, mixed $value)
    {
        $this->attributes[$attribute] = $value;
    }

    public function __get(string $attribute): mixed
    {
        return $this->attributes[$attribute] ?? null;
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->attributes);
    }

    public function offsetGet($offset): mixed
    {
        return $this->attributes[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->attributes[] = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    #[Pure]
    public function __toString()
    {
        return $this->getOriginalContents();
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException|\EasyWeChat\Kernel\Exceptions\RuntimeException|\EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public static function createFromRequest(ServerRequestInterface $request, ?Encryptor $encryptor = null): static
    {
        $originContent = $request->getBody()->getContents();

        if (0 === stripos($originContent, '<')) {
            $attributes = XML::parse($originContent);
        }

        // Handle JSON format.
        $dataSet = json_decode($originContent, true);

        if (JSON_ERROR_NONE === json_last_error() && $originContent) {
            $attributes = $dataSet;
        }

        if (empty($attributes)) {
            throw new BadRequestException('Failed to decode request contents.');
        }

        $query = $request->getQueryParams();

        if (isset($query['signature']) && 'aes' === ($query['encrypt_type'] ?? '') && $ciphertext = $attributes['Encrypt'] ?? null) {
            if (!$encryptor) {
                throw new InvalidArgumentException('$encryptor could not be empty in safety mode.');
            }
            $attributes = XML::parse($encryptor->decrypt($ciphertext, $query['msg_signature'], $query['nonce'], $query['timestamp']));
        }

        return new static($attributes, $originContent);
    }
}
