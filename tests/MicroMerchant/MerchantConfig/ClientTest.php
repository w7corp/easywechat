<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MicroMerchant\MerchantConfig;

use EasyWeChat\MicroMerchant\Application;
use EasyWeChat\MicroMerchant\MerchantConfig\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function getApp()
    {
        return new Application([
            'mch_id' => 'mock-mch_id',
            'key' => 'mock-key123456789101234567891011',
        ]);
    }

    public function testAttention()
    {
        $client = $this->mockApiClient(Client::class, ['setFollowConfig'], $this->getApp())->makePartial();
        $sub_appid = '121n3kjn2j3nnjknj';
        $subscribe_appid = '1222323dssfsd';
        $client->expects()->setFollowConfig($sub_appid, $subscribe_appid)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->setFollowConfig($sub_appid, $subscribe_appid));
    }

    public function testAddPayAccreditDirectory()
    {
        $client = $this->mockApiClient(Client::class, ['safeRequest'], $this->getApp())->makePartial();
        $jsapi_path = 'https://www.wannanbigpig.com';
        $appid = 'sdasdfasdf23232';
        $sub_mch_id = '232423423';
        $client->expects()->safeRequest('secapi/mch/addsubdevconfig', [
            'appid' => $appid,
            'sub_mch_id' => $sub_mch_id,
            'jsapi_path' => $jsapi_path,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->addPath($jsapi_path, $appid, $sub_mch_id));
    }

    public function testBindAppid()
    {
        $client = $this->mockApiClient(Client::class, ['safeRequest'], $this->getApp())->makePartial();
        $sub_appid = 'fasdfa343443r43';
        $appid = 'sdasdfasdf23232';
        $sub_mch_id = '232423423';
        $client->expects()->safeRequest('secapi/mch/addsubdevconfig', [
            'appid' => $appid,
            'sub_mch_id' => $sub_mch_id,
            'sub_appid' => $sub_appid,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->bindAppid($sub_appid, $appid, $sub_mch_id));
    }

    public function testQueryConfig()
    {
        $client = $this->mockApiClient(Client::class, ['safeRequest'], $this->getApp())->makePartial();
        $sub_mch_id = '232423423';
        $appid = '232423423';
        $client->expects()->safeRequest('secapi/mch/querysubdevconfig', ['sub_mch_id' => $sub_mch_id, 'appid' => $appid])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getConfig($sub_mch_id, $appid));
    }
}
