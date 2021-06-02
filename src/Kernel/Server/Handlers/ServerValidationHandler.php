<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Server\Handlers;

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
     * @param null $payload
     *
     * @return \EasyWeChat\Kernel\Decorators\FinallyResult
     */
    public function handle($payload = null)
    {
        if (
            $str = $this->server->request->get('echostr')
        ) {
            return new FinallyResult($str);
        }

        return null;
    }
}
