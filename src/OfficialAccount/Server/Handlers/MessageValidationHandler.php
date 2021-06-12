<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Server\Handlers;

use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\OfficialAccount\Application;
use EasyWeChat\OfficialAccount\Contracts\Handler;
use EasyWeChat\OfficialAccount\Contracts\Message;

class MessageValidationHandler implements Handler
{
    public function __construct(
        public Application $application
    ) {
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     */
    public function handle(Message $message): bool
    {
        $request = $this->application->getRequest();

        if (!$request->isSafeMode()) {
            return true;
        }

        $signature = $request->get('signature');

        if (
            $signature !== $this->signature(
                [
                    $this->application->getAccount()->getToken(),
                    $request->get('timestamp'),
                    $request->get('nonce'),
                ]
            )
        ) {
            throw new BadRequestException('Invalid request signature.', 400);
        }

        return true;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    protected function signature(array $params): string
    {
        sort($params, SORT_STRING);

        return sha1(implode($params));
    }
}
