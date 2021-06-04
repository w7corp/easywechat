<?php

namespace EasyWeChat\OfficialAccount\Server\Handlers;

use EasyWeChat\OfficialAccount\Server\Server;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MessageValidationHandler implements MiddlewareInterface
{
    /**
     * @param \EasyWeChat\OfficialAccount\Server\Server $server
     */
    public function __construct(
        public Server $server,
    ) {
    }

    protected function signature(array $params): string
    {
        sort($params, SORT_STRING);

        return sha1(implode($params));
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // TODO: Implement process() method.
    }
}
