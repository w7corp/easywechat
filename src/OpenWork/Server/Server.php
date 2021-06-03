<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\Server;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Server\BaseServer;
use EasyWeChat\Kernel\Server\Handlers\MessageValidationHandler;
use EasyWeChat\Kernel\ServiceContainer;

class Server extends BaseServer
{
    /**
     * Server constructor.
     *
     * @param \EasyWeChat\Kernel\ServiceContainer $app
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function __construct(ServiceContainer $app)
    {
        parent::__construct($app);

        $this->withoutHandler(MessageValidationHandler::class);
    }

    /**
     * @param string                            $encrypt
     * @param \EasyWeChat\Kernel\Encryptor|null $encryptor
     *
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function decrypt(string $encrypt, Encryptor $encryptor = null): string
    {
        if (!$encryptor) {
            $encryptor = new Encryptor(
                $this->message->ToUserName,
                $this->app['config']->get('token'),
                $this->app['config']->get('aes_key')
            );
        }

        return
            $encryptor->decrypt(
                $encrypt,
                $this->request->get('msg_signature'),
                $this->request->get('nonce'),
                $this->request->get('timestamp')
            );
    }

    /**
     * @return bool
     */
    protected function shouldReturnRawResponse(): bool
    {
        return !is_null($this->request->get('echostr'));
    }

    /**
     * @return bool
     */
    public function isSafeMode(): bool
    {
        return true;
    }
}
