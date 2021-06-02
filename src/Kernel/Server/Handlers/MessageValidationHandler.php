<?php

namespace EasyWeChat\Kernel\Server\Handlers;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Server\BaseServer;

class MessageValidationHandler implements EventHandlerInterface
{
    /**
     * MessageValidationHandler constructor.
     *
     * @param \EasyWeChat\Kernel\Server\BaseServer $server
     */
    public function __construct(
        public BaseServer $server,
    ) {}

    /**
     * @param mixed|null $payload
     *
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     */
    public function handle(mixed $payload = null)
    {
        if (
            !$this->server->isSafeMode()
        ) {
            return null;
        }

        $signature = $this->server->request->get('signature');

        if (
            $signature !== BaseServer::signature(
                [
                    $this->server->getToken(),
                    $this->server->request->get('timestamp'),
                    $this->server->request->get('nonce'),
                ]
            )
        ) {
            throw new BadRequestException('Invalid request signature.', 400);
        }

        return null;
    }
}
