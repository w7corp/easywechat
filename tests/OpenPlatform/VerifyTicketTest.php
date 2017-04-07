<?php

/**
 * Test VerifyTicketTest.php.
 *
 * @author lixiao <leonlx126@gmail.com>
 */
namespace EasyWeChat\Tests\OpenPlatform;

use Doctrine\Common\Cache\ArrayCache;
use EasyWeChat\OpenPlatform\VerifyTicket;
use EasyWeChat\Support\Collection;
use EasyWeChat\Tests\TestCase;

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
