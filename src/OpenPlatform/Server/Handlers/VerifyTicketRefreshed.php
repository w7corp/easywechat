<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Server\Handlers;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Server\Server;

use function EasyWeChat\Kernel\data_get;

class VerifyTicketRefreshed implements EventHandlerInterface
{
    /**
     * VerifyTicketRefreshed constructor.
     *
     * @param \EasyWeChat\Kernel\Server\Server $server
     */
    public function __construct(
        public Server $server
    ) {
    }

    /**
     * @param null $payload
     *
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function handle($payload = null)
    {
        $ticket = data_get($payload, 'ComponentVerifyTicket');

        $ticket && $this->server->app['verify_ticket']->setTicket($ticket);
    }
}
