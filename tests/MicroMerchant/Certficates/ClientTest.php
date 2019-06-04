<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MicroMerchant\Certficates;

use EasyWeChat\MicroMerchant\Certficates\Client;
use EasyWeChat\MicroMerchant\Application;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testGetCertficates()
    {
        $app = new Application(['mch_id' => 'mock-mch_id']);
        $client = $this->mockApiClient(Client::class, ['getCertficates'], $app);

        $client->expects()->getCertficates()->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getCertficates());
    }

    public function testRefreshCertificate()
    {
        $app = new Application(['mch_id' => 'mock-mch_id']);
        $client = $this->mockApiClient(Client::class, ['refreshCertificate'], $app);

        $client->expects()->refreshCertificate()->andReturn('mock-result');

        $this->assertSame('mock-result', $client->refreshCertificate());
    }

}
