<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OpenWork;

use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\OpenWork\SuiteTicket;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;

class SuiteTicketTest extends TestCase
{
    public function test_get_key_and_set_key()
    {
        $suiteTicket = new SuiteTicket('mock-suite-id');

        $this->assertSame('open_work.suite_ticket.mock-suite-id', $suiteTicket->getKey());

        $suiteTicket->setKey('mock-suite-ticket-key');

        $this->assertSame('mock-suite-ticket-key', $suiteTicket->getKey());
    }

    public function test_set_and_get_ticket()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->set('open_work.suite_ticket.mock-suite-id', 'mock-suite-ticket', 6000)->andReturn(true);
        $cache->expects()->get('open_work.suite_ticket.mock-suite-id')->andReturn('mock-suite-ticket');

        $suiteTicket = new SuiteTicket('mock-suite-id', $cache);

        $this->assertSame($suiteTicket, $suiteTicket->setTicket('mock-suite-ticket'));
        $this->assertSame('mock-suite-ticket', $suiteTicket->getTicket());
    }

    public function test_get_ticket_throws_exception_when_missing()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get('open_work.suite_ticket.mock-suite-id')->andReturn(null);

        $suiteTicket = new SuiteTicket('mock-suite-id', $cache);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No suite_ticket found.');

        $suiteTicket->getTicket();
    }
}
