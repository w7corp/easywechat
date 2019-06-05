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
        $app    = new Application(['mch_id' => 'mock-mch_id']);
        $client = $this->mockApiClient(Client::class, ['getCertficates'], $app);
        $result = [
            'serial_no'      => '121*******************************3835AA',
            'effective_time' => '2018-08-21 16:38:49',
            'expire_time'    => '2023-08-20 16:38:49',
            'certificates'    => 'WMewjjefD8qWylTgZZbmm22pFMOods0CxNwzVJ1mdaKWUINtTKts',
        ];
        $client->expects()->getCertficates()->andReturn($result);

        $this->assertSame($result, $client->getCertficates());
    }

    public function testRefreshCertificate()
    {
        $app    = new Application(['mch_id' => 'mock-mch_id']);
        $client = $this->mockApiClient(Client::class, ['refreshCertificate'], $app);

        $result = [
            'serial_no'      => '121*******************************3835AA',
            'effective_time' => '2018-08-21 16:38:49',
            'expire_time'    => '2023-08-20 16:38:49',
            'certificates'    => 'WMewjjefD8qWylTgZZbmm22pFMOods0CxNwzVJ1mdaKWUINtTKts',
        ];
        $client->expects()->refreshCertificate()->andReturn($result);
        $this->assertSame($result, $client->refreshCertificate());
    }

    public function setCache()
    {
        $app    = new Application(['mch_id' => 'mock-mch_id']);
        $client = $this->mockApiClient(Client::class, [], $app);
        $certificates = [
            'serial_no'      => '121*******************************3835AA',
            'effective_time' => '2018-08-21 16:38:49',
            'expire_time'    => '2023-08-20 16:38:49',
            'certificates'    => 'WMewjjefD8qWylTgZZbmm22pFMOods0CxNwzVJ1mdaKWUINtTKts',
        ];
        $client->getCache()->set('mock-mch_id_micro_certificates', $certificates);

        $this->assertSame($certificates, $client->getCertficates());
    }

    public function clearCache()
    {
        $app    = new Application(['mch_id' => 'mock-mch_id']);
        $client = $this->mockApiClient(Client::class, [], $app);
        $client->getCache()->delete('mock-mch_id_micro_certificates');

        $this->assertSame(null,  $client->getCache()->get('mock-mch_id_micro_certificates'));
    }
}
