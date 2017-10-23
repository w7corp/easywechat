<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Sandbox;

use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Kernel\Exceptions\SandboxException;
use EasyWeChat\Payment\Sandbox\Client;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;

class ClientTest extends TestCase
{
    public function testKey()
    {
        $client = $this->mockApiClient(Client::class, ['requestRaw', 'getCache'], new Application(['app_id' => 'mock-123']))->makePartial();
        $cache = \Mockery::mock(CacheInterface::class);

        // without cache...
        $response = new Response(200, [], '<xml><return_code>SUCCESS</return_code><sandbox_signkey>sandbox-key</sandbox_signkey></xml>');
        $client->expects()->getCache()->times(2)->andReturn($cache);
        $cache->expects()->get('easywechat.payment.sandbox.mock-123')->andReturn(false);
        $cache->expects()->set('easywechat.payment.sandbox.mock-123', 'sandbox-key', 86400)->andReturn(true);
        $client->expects()->requestRaw('/sandboxnew/pay/getsignkey')->andReturn($response);

        $this->assertSame('sandbox-key', $client->key());

        // has cache...
        $client->expects()->getCache()->andReturn($cache);
        $cache->expects()->get('easywechat.payment.sandbox.mock-123')->andReturn('sandbox-key-in-cache');
        $this->assertSame('sandbox-key-in-cache', $client->key());

        // return code != SUCCESS
        $response = new Response(200, [], '<xml><return_code>FAIL</return_code><return_msg>fail-reason</return_msg></xml>');
        $client->expects()->getCache()->andReturn($cache);
        $cache->expects()->get('easywechat.payment.sandbox.mock-123')->andReturn(false);
        $client->expects()->requestRaw('/sandboxnew/pay/getsignkey')->andReturn($response);

        $this->expectException(SandboxException::class);
        $client->key();
    }

    public function testExcept()
    {
        $client = $this->mockApiClient(Client::class, [], new Application(['app_id' => 'mock-123']));
        $this->assertTrue($client->except('/sandboxnew/pay/getsignkey'));
        $this->assertFalse($client->except('other'));
    }

    public function testGetCacheKey()
    {
        $client = $this->mockApiClient(Client::class, ['getCacheKey'], new Application(['app_id' => 'mock-123']))->shouldAllowMockingProtectedMethods();
        $client->expects()->getCacheKey()->passthru();
        $this->assertSame('easywechat.payment.sandbox.mock-123', $client->getCacheKey());
    }
}
