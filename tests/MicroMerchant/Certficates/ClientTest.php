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

use EasyWeChat\MicroMerchant\Application;
use EasyWeChat\MicroMerchant\Certficates\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testGetCertficates()
    {
        $app = new Application(['mch_id' => 'mock-mch_id']);
        $client = $this->mockApiClient(Client::class, ['get'], $app);
        $result = [
            'serial_no' => '121*******************************3835AA',
            'effective_time' => '2018-08-21 16:38:49',
            'expire_time' => '2023-08-20 16:38:49',
            'certificates' => 'WMewjjefD8qWylTgZZbmm22pFMOods0CxNwzVJ1mdaKWUINtTKts...',
        ];
        $client->expects()->get()->andReturn($result);

        $this->assertSame($result, $client->get());
    }

    public function testDecrypt()
    {
        $app = new Application(['mch_id' => 'mock-mch_id']);
        $client = $this->mockApiClient(Client::class, ['decrypt'], $app);
        $encryptCertificate = [
            'algorithm' => 'AEAD_AES_256_GCM',
            'nonce' => 'ac5c32e8eb1b',
            'associated_data' => 'certificate',
            'ciphertext' => 'WMewjjefD8qWylTgZZbmm22pFMOods0CxNwzVJ1mdaKWUINtTKts...',
        ];
        $client->expects()->decrypt($encryptCertificate)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->decrypt($encryptCertificate));
    }
}
