<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Server\Handlers;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Decorators\FinallyResult;
use EasyWeChat\Kernel\ServiceContainer;

class ServerValidationHandler implements EventHandlerInterface
{
    /**
     * @param ServiceContainer $app
     */
    public function __construct(
        public ServiceContainer $app
    ) {}

    /**
     * @param null $payload
     *
     * @return \EasyWeChat\Kernel\Decorators\FinallyResult
     */
    public function handle($payload = null)
    {
        if ($str = $this->app['request']->get('echostr')) {
            return new FinallyResult($str);
        }

        return null;
    }
}
