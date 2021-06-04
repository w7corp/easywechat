<?php

namespace EasyWeChat\OfficialAccount\Server;

class Message implements \EasyWeChat\OfficialAccount\Contracts\Message
{
    public function __construct(protected array $attributes = [], protected ?string $originContents = '')
    {
    }

    public function getOriginalContents(): ?string
    {
        return $this->originContents;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function __set(string $attribute, mixed $value)
    {
        $this->attributes[$attribute] = $value;
    }

    public function __get(string $attribute)
    {
        return $this->attributes[$attribute] ?? null;
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->attributes);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->attributes[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->attributes[] = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    public function __toString()
    {
        return $this->getOriginalContents() ?: '';
    }
}
