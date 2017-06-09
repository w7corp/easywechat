<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OpenPlatform\Server\Handlers;

use EasyWeChat\Applications\OpenPlatform\Core\VerifyTicket;

class ComponentVerifyTicket extends EventHandler
{
    /**
     * VerifyTicket.
     *
     * @var \EasyWeChat\Applications\OpenPlatform\Core\VerifyTicket
     */
    protected $verifyTicket;

    /**
     * Constructor.
     *
     * @param \EasyWeChat\Applications\OpenPlatform\Core\VerifyTicket $verifyTicket
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
