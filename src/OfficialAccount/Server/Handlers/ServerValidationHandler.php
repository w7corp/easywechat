<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Server\Handlers;

use EasyWeChat\OfficialAccount\Application;
use EasyWeChat\OfficialAccount\Contracts\Message;

class ServerValidationHandler
{
    public function __construct(
        public Application $application
    ) {
    }

    public function __invoke(Message $message, \Closure $next)
    {
        $request = $this->application->getRequest();

        if ($request->isValidation()) {
            return $request->get('echostr');
        }

        return $next($message);
    }
}
