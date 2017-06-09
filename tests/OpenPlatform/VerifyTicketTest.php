<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform;

use Doctrine\Common\Cache\ArrayCache;
use EasyWeChat\Applications\OpenPlatform\Core\VerifyTicket;
use EasyWeChat\Tests\TestCase;

class VerifyTicketTest extends TestCase
{
    /**
     * Get VerifyTicket instance.
     */
    public function getVerifyTicket($appId)
    {
        return new VerifyTicket($appId, new ArrayCache());
    }

    /**
     * Tests that the verify ticket is properly cached.
     */
    public function testTicket()
    {
        $verifyTicket = $this->getVerifyTicket('foobar');

        $this->assertTrue($verifyTicket->setTicket('ticket@foobar'));
        $this->assertSame('ticket@foobar', $verifyTicket->getTicket());
    }

    /**
     * Test cache key.
     */
    public function testCacheKey()
    {
        $verifyTicket = $this->getVerifyTicket('app-id');

        $this->assertSame('easywechat.open_platform.component_verify_ticket.app-id', $verifyTicket->getCacheKey());

        $verifyTicket->setCacheKey('cache-key.app-id');

        $this->assertSame('cache-key.app-id', $verifyTicket->getCacheKey());
    }
}
