<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OpenPlatform\Server;

use EasyWeChat\Applications\OfficialAccount\Server\Guard as BaseGuard;
use EasyWeChat\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Guard.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Guard extends BaseGuard
{
    const EVENT_AUTHORIZED = 'authorized';
    const EVENT_UNAUTHORIZED = 'unauthorized';
    const EVENT_UPDATE_AUTHORIZED = 'updateauthorized';
    const EVENT_COMPONENT_VERIFY_TICKET = 'component_verify_ticket';

    /**
     * Event handlers.
     *
     * @var \EasyWeChat\Support\Collection
     */
    protected $handlers;

    /**
     * Set handlers.
     *
     * @param array $handlers
     *
     * @return $this
     */
    public function setHandlers(array $handlers)
    {
        $this->handlers = new Collection($handlers);

        return $this;
    }

    /**
     * {@inheritdoc}.
     */
    protected function resolve()
    {
        $message = new Collection($this->getMessage());

        if ($handler = $this->handlers->get($message->get('InfoType'))) {
            $handler->handle($message);
        }

        if ($customHandler = $this->getMessageHandler()) {
            call_user_func_array($customHandler, [$message]);
        }

        return new Response(self::SUCCESS_EMPTY_RESPONSE);
    }
}
