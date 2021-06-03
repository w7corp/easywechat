<?php

namespace EasyWeChat\Kernel\Server;

use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Support\XML;
use function EasyWeChat\Kernel\throw_if;

class Message implements \ArrayAccess
{
    /**
     * @var \EasyWeChat\Kernel\Server\BaseServer
     */
    public BaseServer $server;

    /**
     * @var array
     */
    protected array $attributes = [];

    /**
     * @var string|null
     */
    public ?string $originContent = null;

    /**
     * Message constructor.
     *
     * @param \EasyWeChat\Kernel\Server\Request $request
     *
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \Throwable
     */
    public function __construct(
        public Request $request,
    ) {
        $this->server = $request->server;

        $this->init();
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        return $this->request->validate();
    }

    /**
     * @return bool
     */
    public function isValidation(): bool
    {
        return !is_null($this->request->get('echostr'));
    }

    /**
     * @return string|null
     */
    public function getOriginalContents(): ?string
    {
        return $this->originContent;
    }

    /**
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \Throwable
     */
    public function init(): array
    {
        $this->originContent = $this->request->getContent();

        $message = self::parse($this->originContent);

        throw_if(
            !$message,
            BadRequestException::class,
            'No message received.'
        );

        if (
            $this->request->isSafeMode()
            &&
            $encrypt = $message['Encrypt'] ?? null
        ) {
            $message = self::parse($this->server->decrypt($encrypt));
        }

        $this->attributes = $message ?: [];

        return $message;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $content
     *
     * @return array|null
     *
     * @throws \Throwable
     */
    protected static function parse(string $content): ?array
    {
        if (0 === stripos($content, '<')) {
            return XML::parse($content);
        }

        // Handle JSON format.
        $dataSet = json_decode($content, true);

        if (
            JSON_ERROR_NONE === json_last_error()
            &&
            $content
        ) {
            $content = $dataSet;
        }

        return $content ?? null;
    }

    /**
     * @param $attribute
     * @param $value
     */
    public function __set($attribute, $value){
        $this->attributes[$attribute] = $value;
    }

    /**
     * @param $attribute
     *
     * @return mixed
     */
    public function __get($attribute){
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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getOriginalContents() ?: '';
    }
}
