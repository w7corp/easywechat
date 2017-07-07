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

use EasyWeChat\OpenPlatform\Auth\VerifyTicket;

/**
 * Class ComponentVerifyTicket.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class ComponentVerifyTicket extends EventHandler
{
    /**
     * VerifyTicket.
     *
     * @var \EasyWeChat\OpenPlatform\Auth\VerifyTicket
     */
    protected $verifyTicket;

    /**
     * Constructor.
     *
     * @param \EasyWeChat\OpenPlatform\Auth\VerifyTicket $verifyTicket
     */
    public function __construct(VerifyTicket $verifyTicket)
    {
        $this->verifyTicket = $verifyTicket;
    }

    /**
     * {@inheritdoc}.
     */
    public function handle($message)
    {
        $this->verifyTicket->setTicket($message->get('ComponentVerifyTicket'));
    }
}
