<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Server\Handlers;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Decorators\FinallyResult;
use EasyWeChat\Kernel\Server\BaseServer;

class ServerValidationHandler implements EventHandlerInterface
{
    /**
     * ServerValidationHandler constructor.
     *
     * @param \EasyWeChat\Kernel\Server\BaseServer $server
     */
    public function __construct(
        public BaseServer $server
    ) {}

    /**
     * @param mixed $payload
     *
     * @return FinallyResult|bool
     */
    public function handle($payload = null): FinallyResult|bool
    {
        if (
            $decrypted = $this->server->request->get('echostr')
        ) {
            $str = $this->server->app['encryptor']->decrypt(
                $decrypted,
                $this->server->request->get('msg_signature'),
                $this->server->request->get('nonce'),
                $this->server->request->get('timestamp')
            );

            return new FinallyResult($str);
        }

        return true;
    }
}
