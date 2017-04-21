<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\EventHandlers;

use Doctrine\Common\Cache\ArrayCache;
use EasyWeChat\OpenPlatform\EventHandlers\ComponentVerifyTicket;
use EasyWeChat\OpenPlatform\VerifyTicket;
use EasyWeChat\Support\Collection;
use EasyWeChat\Tests\TestCase;

class ComponentVerifyTicketTest extends TestCase
{
    public function testHandle()
    {
        $verifyTicket = new VerifyTicket('appid', new ArrayCache());
        $handler = new ComponentVerifyTicket($verifyTicket);
        $message = new Collection([
            'AppId' => 'appid',
            'CreateTime' => '1413192605',
            'InfoType' => 'component_verify_ticket',
            'ComponentVerifyTicket' => 'ticket@123123',
        ]);
        $handler->handle($message);

        $this->assertEquals('ticket@123123', $verifyTicket->getTicket());
    }
}
