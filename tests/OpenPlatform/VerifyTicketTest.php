<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Doctrine\Common\Cache\ArrayCache;
use EasyWeChat\OpenPlatform\VerifyTicket;
use EasyWeChat\Support\Collection;

class VerifyTicketTest extends TestCase
{
    /**
     * Tests that the verify ticket is properly cached.
     */
    public function testCache()
    {
        $appId = 'foobar';
        $cache = new ArrayCache();
        $verifyTicket = new VerifyTicket($appId, $cache);

        $ticket = 'ticket@foobar';
        $message = new Collection(['ComponentVerifyTicket' => $ticket]);

        $ok = $verifyTicket->cache($message);
        $this->assertTrue($ok);

        $cached = $verifyTicket->getTicket();
        $this->assertEquals($ticket, $cached);
    }
}
