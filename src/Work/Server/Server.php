<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Server;

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
     * @return bool
     */
    protected function shouldReturnRawResponse(): bool
    {
        return !is_null($this->app['request']->get('echostr'));
    }

    /**
     * @return bool
     */
    public function isSafeMode(): bool
    {
        return true;
    }
}
