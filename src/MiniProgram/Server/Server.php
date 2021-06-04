<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\Server;

use EasyWeChat\Kernel\Server\Server;
use EasyWeChat\Kernel\Server\Handlers\ServerValidationHandler;
use EasyWeChat\Kernel\ServiceContainer;

class Server extends Server
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

        $this->withHandler(ServerValidationHandler::class);
    }
}
