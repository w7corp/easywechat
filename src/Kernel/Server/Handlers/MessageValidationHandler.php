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
     * @return bool
     *
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     */
    public function handle(mixed $payload = null): bool
    {
        if (
            !$this->server->request->validate()
        ) {
            throw new BadRequestException('Invalid request signature.', 400);
        }

        return true;
    }
}
