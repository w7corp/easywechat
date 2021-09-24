<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Kf;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Kf\ServicerClient;

/**
 * Class ServicerClientTest.
 *
 * @package EasyWeChat\Tests\Work\Kf
 *
 * @author 读心印 <aa24615@qq.com>
 */
class ServicerClientTest extends TestCase
{
    public function testAdd()
    {
        $client = $this->mockApiClient(ServicerClient::class);
        $client->expects()->httpPostJson('cgi-bin/kf/servicer/add', [
            'open_kfid' => 'kfxxxxxxxxxxxxxx',
            'userid_list' => ["zhangsan", "lisi"]
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->add('kfxxxxxxxxxxxxxx', ["zhangsan", "lisi"]));
    }

    public function testDel()
    {
        $client = $this->mockApiClient(ServicerClient::class);
        $client->expects()->httpPostJson('cgi-bin/kf/servicer/del', [
            'open_kfid' => 'kfxxxxxxxxxxxxxx',
            'userid_list' => ["zhangsan", "lisi"]
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->del('kfxxxxxxxxxxxxxx', ["zhangsan", "lisi"]));
    }

    public function testList()
    {
        $client = $this->mockApiClient(ServicerClient::class);
        $client->expects()->httpGet('cgi-bin/kf/servicer/list', [
            'open_kfid' => 'kfid_123'
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list('kfid_123'));
    }
}
