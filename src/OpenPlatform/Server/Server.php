<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Server;

use EasyWeChat\Kernel\Server\BaseServer;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenPlatform\Server\Handlers\Authorized;
use EasyWeChat\OpenPlatform\Server\Handlers\Unauthorized;
use EasyWeChat\OpenPlatform\Server\Handlers\UpdateAuthorized;
use EasyWeChat\OpenPlatform\Server\Handlers\VerifyTicketRefreshed;
use Symfony\Component\HttpFoundation\Response;
use EasyWeChat\Kernel\Server\Response as ServerResponse;

class Server extends BaseServer
{
    public const EVENT_AUTHORIZED = 'authorized';
    public const EVENT_UNAUTHORIZED = 'unauthorized';
    public const EVENT_UPDATE_AUTHORIZED = 'updateauthorized';
    public const EVENT_COMPONENT_VERIFY_TICKET = 'component_verify_ticket';
    public const EVENT_THIRD_FAST_REGISTERED = 'notify_third_fasteregister';

    public function __construct(ServiceContainer $app)
    {
        parent::__construct($app);

        $this->registerHandlers();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process(): Response
    {
        $this->handle($this->message->toArray());

        return ServerResponse::success();
    }

    /**
     * @return \EasyWeChat\OpenPlatform\Server\Server|void
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    protected function registerHandlers()
    {
        if (!$this->message->InfoType) {
            return;
        }

        $handlers = [];

        match ($this->message->InfoType) {
            self::EVENT_AUTHORIZED => \array_push($handlers, Authorized::class),
            self::EVENT_UNAUTHORIZED => \array_push($handlers, Unauthorized::class),
            self::EVENT_UPDATE_AUTHORIZED => \array_push($handlers, UpdateAuthorized::class),
            self::EVENT_COMPONENT_VERIFY_TICKET => \array_push($handlers, VerifyTicketRefreshed::class),
        };

        return $this->withHandlers($handlers);
    }
}
