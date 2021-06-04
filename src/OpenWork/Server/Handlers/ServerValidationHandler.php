<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\Server\Handlers;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Decorators\FinallyResult;
use EasyWeChat\Kernel\Server\Server;

class ServerValidationHandler implements EventHandlerInterface
{
    /**
     * ServerValidationHandler constructor.
     *
     * @param \EasyWeChat\Kernel\Server\Server $server
     */
    public function __construct(
        public Server $server
    ) {
    }

    /**
     * @param mixed $payload
     *
     * @return FinallyResult|null
     */
    public function handle($payload = null)
    {
        if (
            $decrypted = $this->server->request->get('echostr')
        ) {
            $str = $this->server->app['encryptor_corp']->decrypt(
                $decrypted,
                $this->server->request->get('msg_signature'),
                $this->server->request->get('nonce'),
                (int) $this->server->request->get('timestamp')
            );

            return new FinallyResult($str);
        }

        //把SuiteTicket缓存起来
        if ($payload['SuiteTicket'] ?? null) {
            $this->server->app['suite_ticket']->setTicket($payload['SuiteTicket']);
        }

        return null;
    }
}
