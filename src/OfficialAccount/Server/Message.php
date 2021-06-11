<?php

namespace EasyWeChat\OfficialAccount\Server;

use EasyWeChat\OfficialAccount\Contracts\Message as MessageContract;

class Message implements MessageContract
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

    public function __toString()
    {
        return $this->getOriginalContents();
    }
}
