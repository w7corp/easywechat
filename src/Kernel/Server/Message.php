<?php

namespace EasyWeChat\Kernel\Server;

use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Kernel\Support\XML;
use function EasyWeChat\Kernel\throw_if;

class Message implements \ArrayAccess
{
    /**
     * @var \EasyWeChat\Kernel\ServiceContainer
     */
    public ServiceContainer $app;

    /**
     * @var array
     */
    protected array $attributes = [];

    /**
     * @var string|null
     */
    public ?string $originContent = null;

    // 消息类型
    public const TEXT = 'text';
    public const IMAGE = 'image';
    public const VOICE = 'voice';
    public const VIDEO = 'video';
    public const SHORT_VIDEO = 'shortvideo';
    public const LOCATION = 'location';
    public const LINK = 'link';
    public const DEVICE_EVENT = 'device_event';
    public const DEVICE_TEXT = 'device_text';
    public const EVENT = 'event';
    public const DEVICE_FILE = 'file';
    public const DEVICE_MINIPROGRAM_PAGE = 'miniprogrampage';

    // 事件类型
    public const SUBSCRIBE_EVENT = 'subscribe';
    public const UNSUBSCRIBE_EVENT = 'unsubscribe';
    public const SCAN_EVENT = 'SCAN';
    public const LOCATION_EVENT = 'LOCATION';
    public const CLICK_EVENT = 'CLICK';
    public const VIEW_EVENT = 'VIEW';

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
        $this->app = $request->app;

        $this->create();
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        if (
            !$this->request->isSafeMode()
        ) {
            return true;
        }

        $signature = $this->request->get('signature');

        if (
            $signature !== Server::signature(
                [
                    Server::getToken($this->app),
                    $this->request->get('timestamp'),
                    $this->request->get('nonce'),
                ]
            )
        ) {
            return false;
        }

        return true;
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
    public function create(): array
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
            $message = self::parse($this->request->decrypt($encrypt));
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
