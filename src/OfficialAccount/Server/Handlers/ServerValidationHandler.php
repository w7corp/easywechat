<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Server\Handlers;

use EasyWeChat\Kernel\Decorators\FinallyResult;
use EasyWeChat\OfficialAccount\Application;
use EasyWeChat\OfficialAccount\Contracts\Handler;
use EasyWeChat\OfficialAccount\Contracts\Message;

class ServerValidationHandler implements Handler
{
    public function __construct(
        public Application $application
    ) {
    }

    public function handle(Message $message): FinallyResult | bool
    {
        $request = $this->application->getRequest();

        if (
            $request->isValidation()
        ) {
            return new FinallyResult($request->get('echostr'));
        }

        return true;
    }
}
