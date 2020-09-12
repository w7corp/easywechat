<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Server;

use EasyWeChat\Kernel\ServerGuard;
use EasyWeChat\OpenPlatform\Server\Handlers\Authorized;
use EasyWeChat\OpenPlatform\Server\Handlers\Unauthorized;
use EasyWeChat\OpenPlatform\Server\Handlers\UpdateAuthorized;
use EasyWeChat\OpenPlatform\Server\Handlers\VerifyTicketRefreshed;
use Symfony\Component\HttpFoundation\Response;
use function EasyWeChat\Kernel\data_get;

/**
 * Class Guard.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Guard extends ServerGuard
{
    public const EVENT_AUTHORIZED = 'authorized';
    public const EVENT_UNAUTHORIZED = 'unauthorized';
    public const EVENT_UPDATE_AUTHORIZED = 'updateauthorized';
    public const EVENT_COMPONENT_VERIFY_TICKET = 'component_verify_ticket';
    public const EVENT_THIRD_FAST_REGISTERED = 'notify_third_fasteregister';

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    protected function resolve(): Response
    {
        $this->registerHandlers();

        $message = $this->getMessage();

        if ($infoType = data_get($message, 'InfoType')) {
            $this->dispatch($infoType, $message);
        }

        return new Response(static::SUCCESS_EMPTY_RESPONSE);
    }

    /**
     * Register event handlers.
     */
    protected function registerHandlers()
    {
        $this->on(self::EVENT_AUTHORIZED, Authorized::class);
        $this->on(self::EVENT_UNAUTHORIZED, Unauthorized::class);
        $this->on(self::EVENT_UPDATE_AUTHORIZED, UpdateAuthorized::class);
        $this->on(self::EVENT_COMPONENT_VERIFY_TICKET, VerifyTicketRefreshed::class);
    }
}
