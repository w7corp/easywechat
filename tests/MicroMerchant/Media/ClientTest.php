<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MicroMerchant\Media;

use EasyWeChat\MicroMerchant\Application;
use EasyWeChat\MicroMerchant\Media\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function getApp()
    {
        return new Application([
            'mch_id' => 'mch_id',
            'key' => '88888888888888888888888888888888',
            'apiv3_key' => 'apiv3_key',
            'cert_path' => 'cert_path',
            'key_path' => 'key_path',
        ]);
    }

    public function testUpload()
    {
        $client = $this->mockApiClient(Client::class, ['upload'], $this->getApp())->makePartial();
        $client->expects()->upload(STUBS_ROOT.'/files/image.jpg')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->upload(STUBS_ROOT.'/files/image.jpg'));
    }
}
