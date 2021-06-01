<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Server;

use EasyWeChat\Kernel\Server\Server;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OfficialAccount\Server\Handlers\ServerValidationHandler;

class Guard extends Server
{
    public function __construct(ServiceContainer $app)
    {
        parent::__construct($app);

        $this->withHandler(ServerValidationHandler::class);
    }

    /**
     * @return bool
     */
    protected function shouldReturnRawResponse(): bool
    {
        return !is_null($this->app['request']->get('echostr'));
    }
}
