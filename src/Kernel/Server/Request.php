<?php

namespace EasyWeChat\Kernel\Server;

use EasyWeChat\Kernel\ServiceContainer;

class Request
{
    /**
     * Request constructor.
     *
     * @param \EasyWeChat\Kernel\ServiceContainer $app
     */
    public function __construct(
        public ServiceContainer $app
    ) {}

    /**
     * @param \EasyWeChat\Kernel\ServiceContainer $app
     *
     * @return \EasyWeChat\Kernel\Server\Request
     */
    public static function create(ServiceContainer $app): Request
    {
        return new self($app);
    }

    /**
     * @return \EasyWeChat\Kernel\Server\Message
     *
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \Throwable
     */
    public function getMessage(): Message
    {
        return new Message($this);
    }

    /**
     * @param string $encrypt
     *
     * @return string
     */
    public function decrypt(string $encrypt): string
    {
        return
            $this->app['encryptor']->decrypt(
                $encrypt,
                $this->get('msg_signature'),
                $this->get('nonce'),
                $this->get('timestamp')
            );
    }

    /**
     * @return bool
     */
    public function isSafeMode(): bool
    {
        return
            $this->get('signature')
            &&
            'aes' === $this->get('encrypt_type');
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null): mixed
    {
        return $this->app['request']->get($key, $default);
    }

    /**
     * @param bool $asResource
     *
     * @return mixed
     */
    public function getContent(bool $asResource = false): mixed
    {
        return $this->app['request']->getContent($asResource);
    }
}
