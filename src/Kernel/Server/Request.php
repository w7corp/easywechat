<?php

namespace EasyWeChat\Kernel\Server;

/**
 * Class Request
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class Request
{
    /**
     * Request constructor.
     *
     * @param \EasyWeChat\Kernel\Server\BaseServer $server
     */
    public function __construct(
        public BaseServer $server
    ) {}

    /**
     * @param \EasyWeChat\Kernel\Server\BaseServer $server
     *
     * @return \EasyWeChat\Kernel\Server\Request
     */
    public static function create(BaseServer $server): Request
    {
        return new self($server);
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
        return $this->server->app['request']->get($key, $default);
    }

    /**
     * @param bool $asResource
     *
     * @return mixed
     */
    public function getContent(bool $asResource = false): mixed
    {
        return $this->server->app['request']->getContent($asResource);
    }
}
