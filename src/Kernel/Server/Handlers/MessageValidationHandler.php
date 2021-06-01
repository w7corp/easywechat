<?php

namespace EasyWeChat\Kernel\Server\Handlers;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Server\Request as ServerRequest;
use EasyWeChat\Kernel\Server\Server;
use EasyWeChat\Kernel\ServiceContainer;

class MessageValidationHandler implements EventHandlerInterface
{
    /**
     * SignatureValidationHandler constructor.
     *
     * @param \EasyWeChat\Kernel\ServiceContainer $app
     */
    public function __construct(
        public ServiceContainer $app,
    ) {}

    /**
     * @param mixed|null $payload
     *
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     */
    public function handle(mixed $payload = null)
    {
        $request = ServerRequest::create($this->app);

        if (
            !$request->isSafeMode()
        ) {
            return null;
        }

        $signature = $request->get('signature');

        if (
            $signature !== Server::signature(
                [
                    Server::getToken($this->app),
                    $request->get('timestamp'),
                    $request->get('nonce'),
                ]
            )
        ) {
            throw new BadRequestException('Invalid request signature.', 400);
        }

        return null;
    }
}
