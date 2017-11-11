<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\Auth;

use EasyWeChat\OpenPlatform\Auth\VerifyTicket;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;

class VerifyTicketTest extends TestCase
{
    public function testConstruct()
    {
        $mock = $this->getMockBuilder(VerifyTicket::class)->disableOriginalConstructor()->setMethods(['setTicket'])->getMockForAbstractClass();

        $mock->expects($this->once())->method('setTicket')->with($this->equalTo('ticket@123456'));

        (new \ReflectionClass(VerifyTicket::class))->getConstructor()->invoke($mock, 'app-id', 'ticket@123456');
    }

    public function testSetTicket()
    {
        $client = \Mockery::mock(VerifyTicket::class.'[getCache]', ['app-id'], function ($mock) {
            $cache = \Mockery::mock(CacheInterface::class, function ($mock) {
                $mock->expects()->set('easywechat.open_platform.verify_ticket.app-id', 'ticket@654321', 3600)->once();
            });
            $mock->allows()->getCache()->andReturn($cache);
        });

        $this->assertInstanceOf(VerifyTicket::class, $client->setTicket('ticket@654321'));
    }

    public function testGetTicket()
    {
        $client = \Mockery::mock(VerifyTicket::class.'[getCache]', ['app-id'], function ($mock) {
            $cache = \Mockery::mock(CacheInterface::class, function ($mock) {
                $mock->expects()->get('easywechat.open_platform.verify_ticket.app-id')->andReturn('ticket@123456')->once();
            });
            $mock->allows()->getCache()->andReturn($cache);
        });

        $this->assertSame('ticket@123456', $client->getTicket());
    }

    public function testGetTicketAndThrowException()
    {
        $client = \Mockery::mock(VerifyTicket::class.'[getCache]', ['app-id'], function ($mock) {
            $cache = \Mockery::mock(CacheInterface::class, function ($mock) {
                $mock->expects()->get('easywechat.open_platform.verify_ticket.app-id')->andReturn(null)->once();
            });
            $mock->allows()->getCache()->andReturn($cache);
        });

        $this->expectException('EasyWeChat\Kernel\Exceptions\RuntimeException');
        $this->expectExceptionMessage('Credential "component_verify_ticket" does not exist in cache.');
        $client->getTicket();
    }
}
