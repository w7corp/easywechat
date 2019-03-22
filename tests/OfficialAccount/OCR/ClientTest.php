<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\OCR;

use EasyWeChat\OfficialAccount\OCR\Client;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testIdCard()
    {
        $client = $this->mockApiClient(Client::class);

        $path = '/foo/bar.jpg';
        $client->expects()->httpGet('cv/ocr/idcard', [
            'type' => 'photo',
            'img_url' => $path,
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->idCard('photo', $path));

        try {
            $client->idCard('image', $path);
            $this->fail('No expected exception thrown.');
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Unsupported type: \'image\'', $e->getMessage());
        }
    }

    public function testBankCard()
    {
        $client = $this->mockApiClient(Client::class);

        $path = '/foo/bar.jpg';
        $client->expects()->httpGet('cv/ocr/bankcard', [
            'img_url' => $path,
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->bankCard($path));
    }

    public function testVehicleLicense()
    {
        $client = $this->mockApiClient(Client::class);

        $path = '/foo/bar.jpg';
        $client->expects()->httpGet('cv/ocr/driving', [
            'img_url' => $path,
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->vehicleLicense($path));
    }
}
