<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Server\Handlers;

use EasyWeChat\OpenPlatform\Application;
use EasyWeChat\OpenPlatform\Auth\VerifyTicket;

/**
 * Class VerifyTicket.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class VerifyTicketRefreshed extends EventHandler
{
    /**
     * @var \EasyWeChat\OpenPlatform\Auth\VerifyTicket
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

    /**
     * {@inheritdoc}.
     */
    public function handle($message)
    {
        $this->app['verify_ticket']->setTicket($message->get('ComponentVerifyTicket'));
    }
}
