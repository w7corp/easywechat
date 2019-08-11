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

use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Kernel\Exceptions\SandboxException;
use EasyWeChat\Payment\Sandbox\Client;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;

class ClientTest extends TestCase
{
    public function testKey()
    {
        $client = $this->mockApiClient(Client::class, ['getCache'], new Application(['app_id' => 'mock-123']))->makePartial();
        $cache = \Mockery::mock(CacheInterface::class);

        // without cache...
        $response = [
            'return_code' => 'SUCCESS',
            'sandbox_signkey' => 'sandbox-key',
        ];
        $client->expects()->getCache()->times(2)->andReturn($cache);
        $cache->expects()->get('easywechat.payment.sandbox.d76cffbeb98b8c8214acd523f7f889c3')->andReturn(false);
        $cache->expects()->set('easywechat.payment.sandbox.d76cffbeb98b8c8214acd523f7f889c3', 'sandbox-key', 86400)->andReturn(true);
        $client->expects()->requestArray('sandboxnew/pay/getsignkey')->andReturn($response);

        $this->assertSame('sandbox-key', $client->getKey());

        // has cache...
        $client->expects()->getCache()->andReturn($cache);
        $cache->expects()->get('easywechat.payment.sandbox.d76cffbeb98b8c8214acd523f7f889c3')->andReturn('sandbox-key-in-cache');
        $this->assertSame('sandbox-key-in-cache', $client->getKey());

        // return code != SUCCESS
        $response = [
            'return_code' => 'FAIL',
            'return_msg' => 'fail-reason',
        ];
        $client->expects()->getCache()->andReturn($cache);
        $cache->expects()->get('easywechat.payment.sandbox.d76cffbeb98b8c8214acd523f7f889c3')->andReturn(false);
        $client->expects()->requestArray('sandboxnew/pay/getsignkey')->andReturn($response);

        $this->expectException(SandboxException::class);
        $client->getKey();
    }

    public function testGetCacheKey()
    {
        $client = $this->mockApiClient(Client::class, ['getCacheKey'], new Application(['app_id' => 'mock-123']))->shouldAllowMockingProtectedMethods();
        $client->expects()->getCacheKey()->passthru();
        $this->assertSame('easywechat.payment.sandbox.d76cffbeb98b8c8214acd523f7f889c3', $client->getCacheKey());
    }
}
