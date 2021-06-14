<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Server\Handlers;

use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\OfficialAccount\Application;
use EasyWeChat\OfficialAccount\Contracts\Message;

class MessageValidationHandler
{
    public function __construct(
        public Application $application
    ) {
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     */
    public function __invoke(Message $message, \Closure $next): bool
    {
        $request = $this->application->getRequest();

        if (!$request->isSafeMode()) {
            return $next($message);
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

        return $next($message);
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
