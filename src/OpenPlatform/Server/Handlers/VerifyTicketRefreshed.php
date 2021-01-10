<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Server\Handlers;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Traits\ResponseCastable;
use EasyWeChat\OpenPlatform\Application;

use function EasyWeChat\Kernel\data_get;

class VerifyTicketRefreshed implements EventHandlerInterface
{
    use ResponseCastable;

    /**
     * @var \EasyWeChat\OpenPlatform\Application
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param \EasyWeChat\OpenPlatform\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }


    public function handle($payload = null)
    {
        $ticket = data_get($payload, 'ComponentVerifyTicket');

        if (!empty($ticket)) {
            $this->app['verify_ticket']->setTicket($ticket);
        }
    }
}
